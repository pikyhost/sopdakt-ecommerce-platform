<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductComparisonController extends Controller
{
   public function index()
   {
      return view('front.product-comparison-page');
   }
}
