<?php

namespace App\Http\Controllers\product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;

class ShowProductController extends Controller
{
    public function showAllProduct()
    {
    	$response = [
    		'status' => true,
    		'product' => Product::all()
    	];

    	return response()->json(['message' => $response]);
    }
}
