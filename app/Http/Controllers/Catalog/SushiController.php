<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Sushi;
use Illuminate\Http\Request;

class SushiController extends Controller
{
    public function index(Request $request)
    {
        $sushi = Sushi::query();

        // Фильтрация по имени
        if ($request->has('name')) {
            $sushi->where('name_sushi', 'like', '%' . $request->input('name') . '%');
        }

        // Фильтрация по цене (минимум и максимум)
        if ($request->has('min_price')) {
            $sushi->where('price_sushi', '>=', $request->input('min_price'));
        }
        if ($request->has('max_price')) {
            $sushi->where('price_sushi', '<=', $request->input('max_price'));
        }

        // Фильтрация по граммам
        if ($request->has('grams')) {
            $sushi->where('grams', $request->input('grams'));
        }

        // Другие фильтры...

        // Пагинация (необязательно)
        $sushi = $sushi->paginate(10);

        return response()->json($sushi);
    }
}
