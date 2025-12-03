<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;
use App\Models\LlistaCompra;
use App\Http\Controllers\Etiqueta;


class CategoriaController extends Controller
{
    /**
     * Mostra totes les categories.
     */
    public function index($id_llista)
    {
        // Carreguem la llista
        $llista = LlistaCompra::findOrFail($id_llista);

        // Categories només d'aquesta llista
        $categories = Categoria::where('id_llista_compra', $llista->id_llista_compra)->get();

        return view('categoria.index', compact('llista', 'categories'));
    }

    public function editar($id_categoria)
    {
        $categoria = Categoria::findOrFail($id_categoria);

        return view('categoria.editar', compact('categoria'));
    }


    public function eliminar($id)
    {
        $categoria = Categoria::findOrFail($id);

        // Guardem l'ID de la llista abans d'eliminar
        $id_llista = $categoria->id_llista_compra;

        // Eliminar productes associats abans d'eliminar la categoria
        $categoria->productes()->delete();

        $categoria->delete();

        return redirect()->route('categories.index', $id_llista);
    }

    public function create($id_llista)
    {
        return view('categoria.create', compact('id_llista'));
    }

    public function store(Request $request, $id_llista)
    {
        // Validem el nom
        $request->validate([
            'nom_categoria' => 'required|string|max:50',
        ]);

        // Creem la categoria vinculada a la llista
        Categoria::create([
            'nom_categoria' => $request->nom_categoria,
            'id_llista_compra' => $id_llista,   // 🔹 aquí vinculem la categoria a la llista
        ]);

        return redirect()->route('llistes.editar', $id_llista);
    }

    public function actualitzar(Request $request, $id_categoria)
    {
        $request->validate([
            'nom_categoria' => 'required|string|max:255',
        ]);

        $categoria = Categoria::findOrFail($id_categoria);

        // Guardem l'ID de la llista abans d'actualitzar
        $id_llista = $categoria->id_llista_compra;

        $categoria->nom_categoria = $request->nom_categoria;
        $categoria->save();

        // 👉 Passem l'ID de la llista a la ruta
        return redirect()->route('categories.index', $id_llista);
    }
}
