<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wheel;

class HomePageController extends Controller
{
  public function index()
  {
      $products = Product::with('category')
          ->where('is_featured', true)
          ->where('is_published', true)
          ->availableInUserCountry()
          ->latest()
          ->limit(15)
          ->get();
      return view('front.homepage', compact('products'));
  }

    public function wheel(Wheel $wheel)
    {
        return view('front.wheel', compact('wheel'));
    }

}
