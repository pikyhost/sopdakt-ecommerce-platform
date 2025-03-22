<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\LandingPageSetting;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\LandingPage\{OrderLandingRequest, OrderLandingPageBundleRequest};
use App\Models\{City, Governorate, ShippingType, LandingPage, WebsiteSetting, LandingPageNavbarItems};

class LandingPageController extends Controller
{
    public function show($slug)
    {
        $landingPage = LandingPage::where('slug', $slug)
            ->with(['media', 'features', 'aboutItems', 'featuresItems', 'dealOfTheWeekItems', 'productsItems', 'whyChooseUsItems',
                'feedbacksItems', 'comparesItems', 'faqsItems', 'varieties', 'colors', 'sizes', 'orders', 'topBars',
                'LandingPageShippingZones', 'LandingPageShippingTypes', 'LandingPageGovernorates', 'shippingTypes',
                'shippingGovernorates', 'bundles'])
            ->firstOrFail();

        $totalPrice = $landingPage->after_discount_price ? $landingPage->after_discount_price : $landingPage->price;

        return view('landing-pages.landing-page', [
            'landingPage'          => $landingPage,
            'landingPageSettings'  => LandingPageSetting::latest()->first(),
            'settings'             => WebsiteSetting::query()->latest()->first(),
            'governorates'         => Governorate::all(),
            'landingPageNavItems'  => LandingPageNavbarItems::all(),
            'media'                => $landingPage->media,
            'totalPrice'           => $totalPrice,
            'productFeatures'      => $landingPage->features,
            'cities' => City::all()
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
            'landingPage'          => $landingPage,
            'landingPageSettings'  => LandingPageSetting::latest()->first(),
            'governorates'         => Governorate::all(),
            'bundle'               => $landingPage->bundles->where('id', $landingPageOrder->bundle_landing_page_id)->first(),
            'totalPrice'           => $landingPageOrder->total_price,
            'varieties'            => $landingPageOrder->varieties,
            'quantity'             => $landingPageOrder->quantity,
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

                $city = City::find($request->city_id);
                $shippingType = ShippingType::find($request->shipping_type_id);
                $shippingCost = $landingPage->shippingCost($city, $shippingType);
                $subtotal = $bundle->price * $data->quantity;

                $order = $landingPage->orders()->create([
                    'name'                   => $data->name,
                    'phone'                  => $data->phone,
                    'another_phone'          => $data->another_phone,
                    'address'                => $data->address,
                    'governorate_id'         => $data->governorate_id,
                    'city_id'              => $data->city_id,
                    'quantity'               => $data->quantity,
                    'notes'                  => $data->notes,
                    'status'                 => 'pending',
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
            }

            $request->session()->forget('landing_pages_orders');
            return redirect()->route('landing-pages.thanks', $landingPage->slug)->with('success', 'Order has been placed successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function saveOrder(OrderLandingRequest $request, $id)
    {
        try {
            Log::info('Order creation started', ['request_data' => $request->all(), 'landing_page_id' => $id]);

            $data = $request->validated();
            Log::info('Request validated', ['validated_data' => $data]);

            $landingPage = LandingPage::find($id);
            if (!$landingPage) {
                throw new Exception('Landing Page not found.');
            }
            Log::info('Landing Page found', ['landing_page' => $landingPage]);

            $landingPageVariant = $landingPage->varieties()
                ->where('size_id', $data['size_id'])
                ->where('color_id', $data['color_id'])
                ->first();

            if (!$landingPageVariant) {
                throw new Exception('Variant not found for selected size and color.');
            }
            Log::info('Landing Page Variant found', ['variant' => $landingPageVariant]);

            if ($landingPageVariant->quantity < $data['quantity']) {
                throw new Exception('Quantity not available.');
            }

            $city = City::find($data['city_id']);
            if (!$city) {
                throw new Exception('City not found.');
            }
            Log::info('City found', ['city' => $city]);

            $shippingType = ShippingType::find($data['shipping_type_id']);
            if (!$shippingType) {
                throw new Exception('Shipping type not found.');
            }
            Log::info('Shipping Type found', ['shipping_type' => $shippingType]);

            $shippingCost = $landingPage->shippingCost($city, $shippingType);
            Log::info('Shipping cost calculated', ['shipping_cost' => $shippingCost]);

            $subtotal = $landingPageVariant->price * $data['quantity'];
            $total = $subtotal + $shippingCost;

            $data['subtotal'] = $subtotal;
            $data['shipping_cost'] = $shippingCost;
            $data['total'] = $total;
            $data['status'] = 'pending';

            Log::info('Order data before creation', ['order_data' => $data]);

            $order = $landingPage->orders()->create(
                Arr::except($data, ['color_id', 'size_id'])
            );

            if (!$order) {
                throw new Exception('Order creation failed.');
            }
            Log::info('Order created successfully', ['order' => $order]);

            $order->varieties()->create([
                'size_id' => $request['size_id'],
                'color_id' => $request['color_id'],
            ]);
            Log::info('Order varieties created');

            $landingPageVariant->quantity -= $data['quantity'];
            $landingPageVariant->save();
            Log::info('Landing Page Variant quantity updated', ['new_quantity' => $landingPageVariant->quantity]);

            $request->session()->forget('landing_pages_orders');
            Log::info('Session cleared');

            return redirect()->route('landing-pages.thanks', $landingPage->slug)->with('success', 'Order has been placed successfully');
        } catch (Exception $e) {
            Log::error('Order creation failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', $e->getMessage());
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
