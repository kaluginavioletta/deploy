<?php

namespace App\Http\Controllers;

use App\Models\Dessert;
use App\Models\Drink;
use App\Models\Sushi;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index()
    {
        $sushiWithDiscount = Sushi::where('percent_discount_sushi', '>', 0)
            ->orderBy('percent_discount_sushi', 'desc')
            ->limit(5)
            ->get();

        $drinksWithDiscount = Drink::where('percent_discount_drink', '>', 0)
            ->orderBy('percent_discount_drink', 'desc')
            ->limit(5)
            ->get();

        $dessertsWithDiscount = Dessert::where('percent_discount_dessert', '>', 0)
            ->orderBy('percent_discount_dessert', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'sushiWithDiscount' => $sushiWithDiscount,
            'drinksWithDiscount' => $drinksWithDiscount,
            'dessertsWithDiscount' => $dessertsWithDiscount,
        ]);
    }
}
