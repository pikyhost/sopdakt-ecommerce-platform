<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Region;
use Illuminate\Support\Arr;
use App\Models\ShippingType;
use Illuminate\Http\Request;
use App\Services\JtExpressService;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\LandingPage\OrderLandingRequest;
use App\Http\Requests\LandingPage\OrderLandingPageBundleRequest;
use App\Models\{Governorate, LandingPage, WebsiteSetting, LandingPageSetting, LandingPageNavbarItems, LandingPageOrder};

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
            'landingPage'                   => $landingPage,
            'settings'                      => WebsiteSetting::query()->latest()->first(),
            'landingPageSettings'           => LandingPageSetting::latest()->first(),
            'governorates'                  => Governorate::all(),
            'landingPageNavItems'           => LandingPageNavbarItems::all(),
            'media'                         => $landingPage->media,
            'totalPrice'                    => $totalPrice,
            'productFeatures'               => $landingPage->features
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
            return redirect()->route('landing-pages.show', $slug)->with('error', 'Please select a bundle first');
        }

        $landingPage = LandingPage::where('slug', $slug)->firstOrFail();
        $landingPageOrder = (object) $request->session()->get('landing_pages_orders')[$landingPage->id];

        if (!$landingPageOrder) return back()->with('error', 'Please select a bundle first');

        return view('landing-pages.landing-page-purhcase-form', [
            'landingPage'           => $landingPage,
            'landingPageSettings'   => LandingPageSetting::latest()->first(),
            'governorates'          => Governorate::all(),
            'settings'              => WebsiteSetting::latest()->first(),
            'bundle'                => $landingPage->bundles->where('id', $landingPageOrder->bundle_landing_page_id)->first(),
            'totalPrice'            => $landingPageOrder->total_price,
            'varieties'             => $landingPageOrder->varieties,
            'quantity'              => $landingPageOrder->quantity,
        ]);
    }

    public function getCombinationPrice(Request $request, $id)
    {
        $size = $request->get('size_id');
        $color = $request->get('color_id');

        $landingPage = LandingPage::find($id);
        $combination = $landingPage->varieties()->where('size_id', $size)->where('color_id', $color)->first();

        return response()->json($combination);
    }

    public function order(OrderLandingPageBundleRequest $request, $id)
    {
        try {
            $slug = LandingPage::select('slug')->find($id)->value('slug');
            $this->orderBundlePost($request, $id);

            $request->session()->forget('landing_pages_orders');
            return redirect()->route('landing-pages.thanks', $slug)->with('success', 'Order has been placed successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function orderBundlePost(OrderLandingPageBundleRequest $request, $id): LandingPageOrder
    {
        $data = (object) $request->validated();
        $landingPage = LandingPage::find($id);

        if ($landingPage) {
            $bundle = $landingPage->bundles->where('id', $data->bundle_landing_page_id)->first();

            if (!$bundle) throw new Exception('Bundle not found');

            $region = Region::find($request->region_id);
            $shippingType = ShippingType::find($request->shipping_type_id);
            $shippingCost = $landingPage->shippingCost($region, $shippingType);
            $subtotal = $bundle->price * $data->quantity;

            try {
                DB::beginTransaction();

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
                            'size_id' => $variety['size_id'],
                            'color_id' => $variety['color_id'],
                        ]);

                    } else {
                        $size = $landingPage->sizes()->find($variety['size_id']);
                        $color = $landingPage->colors()->find($variety['color_id']);

                        throw new Exception('Variety of size ' . $size?->name ?? $variety['size_id'] . ' and color ' . $color?->name ?? $variety['color_id'] . ' not found');
                    }
                }

                $JtExpressOrderData =  $this->prepareJtExpressOrderData($order);
                $jtExpressResponse = app(JtExpressService::class)->createOrder($JtExpressOrderData);
                $this->updateJtExpressLandingPageOrder($order, 'pending', $JtExpressOrderData,  $jtExpressResponse);

                DB::commit();
                return $order;
            } catch (Exception $e) {
                dd($e->getMessage());
                DB::rollBack();
                throw $e;
            }
        }

        throw new Exception('LandingPage not found');
    }

    public function saveOrder(OrderLandingRequest $request, $id)
    {
        try {
            $slug = LandingPage::select('slug')->find($id)->value('slug');

            $this->orderPost($request, $id);

            $request->session()->forget('landing_pages_orders');
            return redirect()->route('landing-pages.thanks', $slug)->with('success', 'Order has been placed successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function orderPost(OrderLandingRequest $request, $id): LandingPageOrder
    {
        $data = $request->validated();

        $landingPage = LandingPage::find($id);

        $landingPageVariant = $landingPage->varieties()
                                ->where('size_id', $data['size_id'])
                                ->where('color_id', $data['color_id'])
                                ->first();

        if ($landingPage && $landingPageVariant) {

            if ($landingPageVariant->quantity < $data['quantity']) {
                throw new Exception('Quantity not available');
            }

            $region = Region::find($request->region_id);

            $shippingType = ShippingType::find($request->shipping_type_id);

            $shippingCost = $landingPage->shippingCost($region, $shippingType);

            $combination = $landingPage->varieties()->where('size_id', $data['size_id'])->where('color_id', $data['color_id'])->first();

            $subtotal = $combination->price * $request->quantity;

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

                return $order;
            }
        }

        throw new Exception('LandingPage not found');
    }

    private function prepareJtExpressOrderData($order): array
    {
        return [
            'tracking_number'           => 'EGY' . time() . rand(1000, 9999),
            'weight'                    => 1.0,
            'quantity'                  => $order->quantity,
            'remark'                    => $order->notes ?? '',
            'sender_name'               => 'Your Company Name',
            'sender_company'            => 'Your Company',
            'sender_province'           => 'Your Province',
            'sender_city'               => 'Your City',
            'sender_address'            => 'Your Full Address',
            'sender_mobile'             => 'Your Contact Number',
            'receiver_name'             => $order->name,
            'receiver_province'         => $order->region->governorate->name ?? '',
            'receiver_city'             => $order->region->name ?? '',
            'receiver_address'          => $order->address,
            'receiver_mobile'           => $order->phone,
            'receiver_alternate_phone'  => $order->another_phone ?? '',
            'item_name'                 => $order->landingPage->name ?? 'Product Order',
            'item_quantity'             => $order->quantity,
            'item_value'                => $order->total,
            'item_currency'             => 'EGP',
            'item_description'          => $order->landingPage->description ?? '',
        ];
    }

    private function updateJtExpressLandingPageOrder(LandingPageOrder $order, string $shipping_status, $JtExpressOrderData,  $jtExpressResponse)
    {
        if (isset($jtExpressResponse['code']) && $jtExpressResponse['code'] == 1) {
            $order->update([
                'tracking_number'   => $JtExpressOrderData['tracking_number'],
                'shipping_status'   => $shipping_status,
                'shipping_response' => json_encode($jtExpressResponse)
            ]);
        }
    }

    public function thanks($slug)
    {
        return view('landing-pages.landing-page-thanks', [
            'landingPage'          => LandingPage::where('slug', $slug)->firstOrFail(),
            'settings'             => WebsiteSetting::query()->latest()->first(),
            'landingPageSettings'  => LandingPageSetting::latest()->first(),
        ]);
    }
}
