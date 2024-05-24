<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Drink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DrinkController extends Controller
{
    public function index()
    {
        $drink = Drink::paginate(8);
        return $drink->items();
    }
    public function show($id)
    {
        $drink = Drink::find($id);
        if (!$drink) {
            return response()->json(['message' => 'Напитки закончились'], 404);
        }
        return response()->json(['data' => $drink], 200);
    }

    // *CREATE (Создать новое суши)*
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_drink' => 'required|string|max:255',
            'compound_drink' => 'required|string',
            'id_view_drink' => 'nullable|exists:view_sushi,id_view_sushi',
            'price_drink' => 'required|integer',
            'percent_discount_drink' => 'integer|default:0',
            'discounted_price_drink' => 'integer',
            'img_drink' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $drink = Drink::create($request->all());
        return response()->json(['data' => $drink], 201);
    }

    // *UPDATE (Обновить суши)*
    public function update(Request $request, $id)
    {
        $drink = Drink::find($id);
        if (!$drink) {
            return response()->json(['message' => 'Напитков нет в наличии'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name_drink' => 'sometimes|string|max:255',
            'compound_drink' => 'sometimes|string',
            'id_view_drink' => 'sometimes|nullable|exists:view_sushi,id_view_sushi',
            'price_drink' => 'sometimes|integer',
            'percent_discount_drink' => 'sometimes|integer|default:0',
            'discounted_price_drink' => 'sometimes|integer',
            'img_drink' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $drink->update($request->all());
        return response()->json(['data' => $drink], 200);
    }

    // *DELETE (Удалить суши)*
    public function destroy($id)
    {
        $drink = Drink::find($id);
        if (!$drink) {
            return response()->json(['message' => 'Напиток с данным id не существует'], 404);
        }
        $drink->delete();
        return response()->json(['message' => 'Напиток удалён'], 204);
    }
}
