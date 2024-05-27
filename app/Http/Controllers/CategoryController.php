<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    public function show($id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|unique:categories|max:255',
            ]);

            // Crear la categoría solo si los datos de entrada son válidos
            $category = Category::create([
                'name' => $request->name,
            ]);

            // Devolver una respuesta adecuada después de crear la categoría
            return response()->json(['message' => 'Categoría creada exitosamente', 'category' => $category], 201);
        } catch (\Exception $e) {
            // Manejar cualquier excepción y devolver una respuesta de error
            return response()->json(['error' => 'Error al crear la categoría: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $category = Category::findOrFail($id);

            $request->validate([
                'name' => 'required|string|unique:categories,name,' . $category->id,
            ]);

            $category->update($request->all());

            return response()->json($category, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar la categoría'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar la categoría'], 500);
        }
    }
}
