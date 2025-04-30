<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(Category::all());
    }

public function show($id)
{
    $category = \App\Models\Category::find($id);

    if (!$category) {
        return response()->json(['message' => 'Catégorie non trouvée'], 404);
    }

    return response()->json($category);
}
}