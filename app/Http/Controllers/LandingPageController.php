<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Region;
use Illuminate\Support\Arr;
use App\Models\ShippingType;
use Illuminate\Http\Request;
use App\Services\JtExpressService;
use App\Http\Requests\LandingPage\OrderLandingRequest;
use App\Models\{Governorate, LandingPage, WebsiteSetting, LandingPageSetting, LandingPageNavbarItems, LandingPageOrder};

class LandingPageController extends Controller
{
    public function __construct(protected JtExpressService $jtExpressService){}

    public function show($slug)
    {
        $landingPage = LandingPage::where('slug', $slug)
            ->with(['media', 'features', 'aboutItems', 'featuresItems', 'dealOfTheWeekItems', 'productsItems', 'whyChooseUsItems',
                'feedbacksItems', 'comparesItems', 'faqsItems', 'varieties', 'colors', 'sizes', 'orders', 'topBars',
                'LandingPageShippingZones', 'LandingPageShippingTypes', 'LandingPageGovernorates', 'LandingPageRegions', 'shippingTypes',
                'shippingGovernorates', 'bundles'])
            ->firstOrFail();


        $landingPageSettings = LandingPageSetting::latest()->first();
        $governorates        = Governorate::all();
        $settings            = WebsiteSetting::query()->latest()->first();
        $landingPageNavItems = LandingPageNavbarItems::all();
        $media               = $landingPage->media;
        $totalPrice          = $landingPage->after_discount_price ? $landingPage->after_discount_price : $landingPage->price;
        $productFeatures     = $landingPage->features;

        return view('landing-pages.landing-page', [
            'landingPage'           => $landingPage,
            'settings'              => $settings,
            'landingPageSettings'   => $landingPageSettings,
            'governorates'          => $governorates,
            'landingPageNavItems'   => $landingPageNavItems,
            'media'                 => $media,
            'totalPrice'            => $totalPrice,
            'productFeatures'       => $productFeatures,
        ]);
    }

    public function saveBundleData(Request $request, $id)
    {
        $request->validate([
            'landing_page_bundle_id' => 'required|exists:landing_page_bundles,id',
            'quantity'               => 'required|integer|min:1',
            'varieties'              => 'required|array',
            'varieties.*.color_id'   => 'required|exists:colors,id',
            'varieties.*.size_id'    => 'required|exists:sizes,id',
        ]);

        $landingPage = LandingPage::find($id);;
        $bundle = $landingPage->bundles()->where('id', $request->landing_page_bundle_id)->first();

        if (!$bundle) return response()->json(['error' => 'Bundle not found'], 404);

        $quantity = $request->quantity;
        $varieties = $request->varieties;
        $totalPrice = $bundle->price * $quantity;

        $landingPagesOrders = [
            'landing_page_bundle_id' => $request->landing_page_bundle_id,
            'quantity'               => $quantity,
            'total_price'            => $totalPrice,
            'varieties'              => $varieties,
            'landing_page_id'        => $id,
        ];

        $request->session()->put('landing_pages_orders', [$id => $landingPagesOrders]);

        return response()->json(['message' => 'Bundle data saved successfully', 'price' => $totalPrice, 'success' => true]);
    }

    public function showPurchaseForm(Request $request, $slug)
    {
        if (!$request->session()->has('landing_pages_orders')) {
            return redirect()->route('landing-pages.show', $slug)->with('error', 'Please select a bundle first');
        }

        $landingPage = LandingPage::where('slug', $slug)->firstOrFail();
        $landingPageOrder = (object) $request->session()->get('landing_pages_orders')[$landingPage->id];

        if (!$landingPageOrder) return back()->with('error', 'Please select a bundle first');

        $bundle = $landingPage->bundles()->where('id', $landingPageOrder->landing_page_bundle_id)->first();

        $totalPrice = $landingPageOrder->total_price;
        $varieties = $landingPageOrder->varieties;
        $quantity = $landingPageOrder->quantity;

        $landingPageSettings = LandingPageSetting::latest()->first();
        $websiteSettings = WebsiteSetting::latest()->first();
        $governorates = Governorate::all();

        return view('landing-page-purhcase-form', compact('landingPage', 'landingPageSettings', 'governorates', 'websiteSettings', 'bundle', 'totalPrice', 'varieties', 'quantity'));
    }

    function getCombinationPrice(Request $request, $id)
    {
        $size = $request->get('size_id');
        $color = $request->get('color_id');

        $landingPage = LandingPage::find($id);
        $combination = $landingPage->varieties()->where('size_id', $size)->where('color_id', $color)->first();

        return response()->json($combination);
    }

    public function saveOrder(OrderLandingRequest $request, $id)
    {
        try {
            $slug = LandingPage::select('slug')->find($id)->value('slug');

            $this->orderPost($request, $id);

            $request->session()->forget('landing_pages_orders');
            return redirect()->route('landing-pages.thanks', $slug)->with('success', 'Order has been placed successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function orderPost(OrderLandingRequest $request, $id): LandingPageOrder
    {
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
                return $order;
            }
        }

        throw new Exception('LandingPage not found');
    }
}
