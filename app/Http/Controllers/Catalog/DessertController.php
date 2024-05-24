<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Dessert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DessertController extends Controller
{
    public function index()
    {
        $dessert = Dessert::paginate(8);
        return $dessert->items();
    }
    public function show($id)
    {
        $dessert = Dessert::find($id);
        if (!$dessert) {
            return response()->json(['message' => 'Десерты закончились'], 404);
        }
        return response()->json(['data' => $dessert], 200);
    }

    // *CREATE (Создать новое суши)*
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_dessert' => 'required|string|max:255',
            'compound_dessert' => 'required|string',
            'id_view_dessert' => 'nullable|exists:view_sushi,id_view_sushi',
            'price_dessert' => 'required|integer',
            'percent_discount_dessert' => 'integer|default:0',
            'discounted_price_dessert' => 'integer',
            'img_dessert' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $dessert = Dessert::create($request->all());
        return response()->json(['data' => $dessert], 201);
    }

    // *UPDATE (Обновить суши)*
    public function update(Request $request, $id)
    {
        $dessert = Dessert::find($id);
        if (!$dessert) {
            return response()->json(['message' => 'Десертов нет в наличии'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name_dessert' => 'sometimes|string|max:255',
            'compound_dessert' => 'sometimes|string',
            'id_view_dessert' => 'sometimes|nullable|exists:view_sushi,id_view_sushi',
            'price_dessert' => 'sometimes|integer',
            'percent_discount_dessert' => 'sometimes|integer|default:0',
            'discounted_price_dessert' => 'sometimes|integer',
            'img_dessert' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $dessert->update($request->all());
        return response()->json(['data' => $dessert], 200);
    }

    // *DELETE (Удалить суши)*
    public function destroy($id)
    {
        $dessert = Dessert::find($id);
        if (!$dessert) {
            return response()->json(['message' => 'Десерт с данным id не существует'], 404);
        }
        $dessert->delete();
        return response()->json(['message' => 'Определённый десерт удалён'], 204);
    }
}
