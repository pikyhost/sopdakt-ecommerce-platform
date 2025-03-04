<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Services\JtExpressService;
use App\Http\Requests\LandingPage\{OrderLandingRequest, OrderLandingPageBundleRequest};
use App\Models\{Governorate, ShippingType, Region, LandingPage, WebsiteSetting, LandingPageNavbarItems, LandingPageOrder};

class LandingPageController extends Controller
{
    public function show($slug)
    {
        $landingPage = LandingPage::where('slug', $slug)
            ->with(['media', 'features', 'aboutItems', 'featuresItems', 'dealOfTheWeekItems', 'productsItems', 'whyChooseUsItems',
                'feedbacksItems', 'comparesItems', 'faqsItems', 'varieties', 'colors', 'sizes', 'orders', 'topBars',
                'LandingPageShippingZones', 'LandingPageShippingTypes', 'LandingPageGovernorates', 'LandingPageRegions', 'shippingTypes',
                'shippingGovernorates', 'bundles'])
            ->firstOrFail();

        $totalPrice = $landingPage->after_discount_price ? $landingPage->after_discount_price : $landingPage->price;

        return view('landing-pages.landing-page', [
            'landingPage'         => $landingPage,
            'settings'            => WebsiteSetting::query()->latest()->first(),
            'governorates'        => Governorate::all(),
            'landingPageNavItems' => LandingPageNavbarItems::all(),
            'media'               => $landingPage->media,
            'totalPrice'          => $totalPrice,
            'productFeatures'     => $landingPage->features
        ]);
    }

    public function saveBundleData(Request $request, $id)
    {
        $request->validate([
            'bundle_landing_page_id' => 'required|exists:bundles,id',
            'quantity'               => 'required|integer|min:1',
            'varieties'              => 'required|array',
            'varieties.*.color_id'   => 'required|exists:colors,id',
            'varieties.*.size_id'    => 'required|exists:sizes,id',
        ]);

        $landingPage = LandingPage::with('bundles')->find($id);
        $bundle = $landingPage->bundles->where('id', request('bundle_landing_page_id'))->first();

        if (!$bundle) return response()->json(['error' => 'Bundle not found'], 404);

        $quantity = $request->quantity;
        $varieties = $request->varieties;
        $totalPrice = $bundle->price * $quantity;

        $landingPagesOrders = [
            'bundle_landing_page_id' => $request->bundle_landing_page_id,
            'quantity'               => $quantity,
            'total_price'            => $totalPrice,
            'varieties'              => $varieties,
            'landing_page_id'        => $id,
        ];

        $request->session()->put('landing_pages_orders', [$id => $landingPagesOrders]);

        return response()->json([
            'message'   => 'Bundle data saved successfully',
            'price'     => $totalPrice,
            'success'   => true
        ]);
    }

    public function showPurchaseForm(Request $request, $slug)
    {
        if (!$request->session()->has('landing_pages_orders')) {
            return redirect()->route('landing-page.purchase-form.show', $slug)->with('error', 'Please select a bundle first');
        }

        $landingPage = LandingPage::where('slug', $slug)->firstOrFail();
        $landingPageOrder = (object) $request->session()->get('landing_pages_orders')[$landingPage->id];

        if (!$landingPageOrder) return back()->with('error', 'Please select a bundle first');

        return view('landing-pages.landing-page-purhcase-form', [
            'landingPage'         => $landingPage,
            'governorates'        => Governorate::all(),
            'settings'            => WebsiteSetting::latest()->first(),
            'bundle'              => $landingPage->bundles->where('id', $landingPageOrder->bundle_landing_page_id)->first(),
            'totalPrice'          => $landingPageOrder->total_price,
            'varieties'           => $landingPageOrder->varieties,
            'quantity'            => $landingPageOrder->quantity,
        ]);
    }

    public function getCombinationPrice(Request $request, $id)
    {
        $landingPage = LandingPage::find($id);
        $combination = $landingPage->varieties()->where('size_id', $request->get('size_id'))->where('color_id', $request->get('color_id'))->first();

        return response()->json($combination);
    }

    public function order(OrderLandingPageBundleRequest $request, $id)
    {
        try {
            $data = (object) $request->validated();
            $landingPage = LandingPage::find($id);

            if ($landingPage) {
                $bundle = $landingPage->bundles->where('id', $data->bundle_landing_page_id)->first();

                if (!$bundle) throw new Exception('Bundle not found');

                $region = Region::find($request->region_id);
                $shippingType = ShippingType::find($request->shipping_type_id);
                $shippingCost = $landingPage->shippingCost($region, $shippingType);
                $subtotal = $bundle->price * $data->quantity;

                $order = $landingPage->orders()->create([
                    'name'                   => $data->name,
                    'phone'                  => $data->phone,
                    'another_phone'          => $data->another_phone,
                    'address'                => $data->address,
                    'governorate_id'         => $data->governorate_id,
                    'region_id'              => $data->region_id,
                    'quantity'               => $data->quantity,
                    'notes'                  => $data->notes,
                    'shipping_type_id'       => $data->shipping_type_id ?? null,
                    'shipping_cost'          => $shippingCost,
                    'total'                  => $subtotal + $shippingCost,
                    'subtotal'               => $subtotal,
                    'bundle_landing_page_id' => $data->bundle_landing_page_id ?? null,
                ]);

                foreach ($request->varieties as $variety) {
                    $landingPageVariant = $landingPage->varieties()->where('size_id', $variety['size_id'])->where('color_id', $variety['color_id'])->first();

                    if ($landingPageVariant) {

                        if ($landingPageVariant->quantity < $data->quantity) {
                            throw new Exception('Quantity not available for size ' . $landingPageVariant->size?->name . ' and color ' . $landingPageVariant->color?->name);
                        }

                        $landingPageVariant->quantity -= $data->quantity;
                        $landingPageVariant->save();

                        $order->varieties()->create([
                            'size_id'  => $variety['size_id'],
                            'color_id' => $variety['color_id'],
                        ]);
                    }
                }

                $JtExpressOrderData =  $this->prepareJtExpressOrderData($order);
                $jtExpressResponse = app(JtExpressService::class)->createOrder($JtExpressOrderData);
                $this->updateJtExpressLandingPageOrder($order, 'pending', $JtExpressOrderData,  $jtExpressResponse);
            }

            $request->session()->forget('landing_pages_orders');
            return redirect()->route('landing-page.show-by-slug', $landingPage->slug)->with('success', 'Order has been placed successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function saveOrder(OrderLandingRequest $request, $id)
    {
        try {
            $data = $request->validated();

            $landingPage = LandingPage::find($id);

            $landingPageVariant = $landingPage->varieties()->where('size_id', $data['size_id'])->where('color_id', $data['color_id'])->first();

            if ($landingPage && $landingPageVariant) {

                if ($landingPageVariant->quantity < $data['quantity']) {
                    throw new Exception('Quantity not available');
                }

                $region = Region::find($request->region_id);

                $shippingType = ShippingType::find($request->shipping_type_id);

                $shippingCost = $landingPage->shippingCost($region, $shippingType);

                $subtotal = $landingPageVariant->price * $request->quantity;

                $total = $subtotal + $shippingCost;

                $data['subtotal'] = $subtotal;

                $data['shipping_cost'] = $shippingCost;

                $data['total'] = $total;

                $order = $landingPage->orders()->create(
                    Arr::except($data, ['color_id', 'size_id'])
                );

                if ($order) {
                    $order->varieties()->create([
                        'size_id' => $request['size_id'],
                        'color_id' => $request['color_id'],
                    ]);

                    $landingPageVariant->quantity -= $data['quantity'];
                    $landingPageVariant->save();

                    $JtExpressOrderData =  $this->prepareJtExpressOrderData($order);
                    $jtExpressResponse = app(JtExpressService::class)->createOrder($JtExpressOrderData);
                    $this->updateJtExpressLandingPageOrder($order, 'pending', $JtExpressOrderData,  $jtExpressResponse);
                }
            }

            $request->session()->forget('landing_pages_orders');
            return redirect()->back()->with('success', 'Order has been placed successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    private function prepareJtExpressOrderData($order): array
    {
        $data = [
            'tracking_number'           => 'EGY' . time() . rand(1000, 9999),
            'weight'                    => 1.0,
            'quantity'                  => 1, // $order->quantity,
            'remark'                    => $order->notes ?? '',
            'item_name'                 => $order->landingPage->name ?? 'Product Order',
            'item_quantity'             => $order->quantity,
            'item_value'                => $order->total,
            'item_currency'             => 'EGP',
            'item_description'          => $order->landingPage->description ?? '',
        ];

        $data['sender'] = [
            'name'                   => 'Your Company Name',
            'company'                => 'Your Company',
            'city'                   => 'Your City',
            'address'                => 'Your Full Address',
            'mobile'                 => 'Your Contact Number',
            'countryCode'            => 'Your Country Code',
            'prov'                   => 'Your Prov',
            'area'                   => 'Your Area',
            'town'                   => 'Your Town',
            'street'                 => 'Your Street',
            'addressBak'             => 'Your Address Bak',
            'postCode'               => 'Your Post Code',
            'phone'                  => 'Your Phone',
            'mailBox'                => 'Your Mail Box',
            'areaCode'               => 'Your Area Code',
            'building'               => 'Your Building',
            'floor'                  => 'Your Floor',
            'flats'                  => 'Your Flats',
            'alternateSenderPhoneNo' => 'Your Alternate Sender Phone No',
        ];

        $data['receiver'] = [
            'name'                      => 'test', // $order->name,
            'prov'                      => 'أسيوط', // $order->region->governorate->name,
            'city'                      => 'القوصية', // $order->region->name,
            'address'                   => 'sdfsacdscdscdsa', // $order->address,
            'mobile'                    => '1441234567', // $order->phone,
            'company'                   => 'guangdongshengshenzhe',
            'countryCode'               => 'EGY',
            'area'                      => 'الصبحه',
            'town'                      => 'town',
            'addressBak'                => 'receivercdsfsafdsaf lkhdlksjlkfjkndskjfnhskjlkafdslkjdshflksjal',
            'street'                    => 'street',
            'postCode'                  => '54830',
            'phone'                     => '23423423423',
            'mailBox'                   => 'ant_li123@qq.com',
            'areaCode'                  => '2342343',
            'building'                  => '13',
            'floor'                     => '25',
            'flats'                     => '47',
            'alternateReceiverPhoneNo'  => $order->another_phone ?? '1231321322',
        ];

        return $data;
    }

    private function updateJtExpressLandingPageOrder(LandingPageOrder $order, string $shipping_status, $JtExpressOrderData, $jtExpressResponse)
    {
        if (isset($jtExpressResponse['code']) && $jtExpressResponse['code'] == 1) {
            $order->update([
                'tracking_number'   => $JtExpressOrderData['tracking_number'],
                'shipping_status'   => $shipping_status,
                'shipping_response' => json_encode($jtExpressResponse)
            ]);
        }
    }
}
