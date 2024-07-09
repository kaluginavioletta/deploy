<?php

namespace App\Http\Controllers;

use App\Models\Dessert;
use App\Models\Drink;
use App\Models\Product;
use App\Models\Sushi;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('id_product', 'desc')->limit(5)->get();

        return response()->json(['data' => $products->map(function ($product) {
            return [
                'id_product' => $product->id_product,
                'name' => $product->name,
                'grams' => $product->grams,
                'img' => $product->img,
                'price' => $product->price,
                'percent_discount' => $product->percent_discount,
                'discounted_price' => $product->discounted_price,
                'compound' => $product->compound,
            ];
        })->all()]);
    }
}
