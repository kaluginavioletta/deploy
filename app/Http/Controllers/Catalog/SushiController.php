<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\CartOrder;
use App\Models\Dessert;
use App\Models\Drink;
use App\Models\Favorite;
use App\Models\Order;
use App\Models\Product;
use App\Models\Sushi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class SushiController extends Controller
{
    public function index()
    {
        $sushi = Sushi::paginate(8);
        return $sushi->items();
    }

    // READ (Получить одно суши по ID)
    public function show($id)
    {
        $sushi = Sushi::find($id);
        if (!$sushi) {
            return response()->json(['message' => 'Суши закончились'], 404);
        }
        return response()->json(['data' => $sushi], 200);
    }

    // CREATE (Создать новое суши)
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_sushi' => 'required|string|max:255',
            'compound_sushi' => 'required|string',
            'id_view_sushi' => 'nullable|exists:view_sushi,id_view_sushi',
            'price_sushi' => 'required|integer',
            'percent_discount_sushi' => 'integer|default:0',
            'discounted_price_sushi' => 'integer',
            'img_sushi' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $sushi = Sushi::create($request->all());
        return response()->json(['data' => $sushi], 201);
    }

    // *UPDATE (Обновить суши)*
    public function update(Request $request, $id)
    {
        $sushi = Sushi::find($id);
        if (!$sushi) {
            return response()->json(['message' => 'Суши закончились'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name_sushi' => 'sometimes|string|max:255',
            'compound_sushi' => 'sometimes|string',
            'id_view_sushi' => 'sometimes|nullable|exists:view_sushi,id_view_sushi',
            'price_sushi' => 'sometimes|integer',
            'percent_discount_sushi' => 'sometimes|integer|default:0',
            'discounted_price_sushi' => 'sometimes|integer',
            'img_sushi' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $sushi->update($request->all());
        return response()->json(['data' => $sushi], 200);
    }

    // *DELETE (Удалить суши)*
    public function destroy($id)
    {
        $sushi = Sushi::find($id);
        if (!$sushi) {
            return response()->json(['message' => 'Суши с данным id не существует'], 404);
        }
        $sushi->delete();
        return response()->json(['message' => 'Определённый суши удалён'], 204);
    }
}
