<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;
use App\Models\LlistaCompra;
use App\Http\Controllers\Etiqueta;
use Illuminate\Support\Facades\Auth;


class CategoriaController extends Controller
{
    // Mètode helper per verificar si l'usuari pot editar la llista
    private function potEditar($llista)
    {
        $usuari = Auth::user();
        
        // El propietari sempre pot editar
        if ($llista->user_id === $usuari->id) {
            return true;
        }
        
        // Comprovar si és un usuari compartit amb rol d'administrador
        $compartit = $llista->usuarisCompartits()->where('user_id', $usuari->id)->first();
        
        return $compartit && $compartit->pivot->rol === 'administrador';
    }

    /**
     * Mostra totes les categories.
     */
    public function index($id_llista)
    {
        // Carreguem la llista
        $llista = LlistaCompra::findOrFail($id_llista);

        // Categories només d'aquesta llista
        $categories = Categoria::where('id_llista_compra', $llista->id_llista_compra)->get();
        
        $potEditar = $this->potEditar($llista);

        return view('categoria.index', compact('llista', 'categories', 'potEditar'));
    }

    public function editar($id_categoria)
    {
        $categoria = Categoria::findOrFail($id_categoria);
        $llista = LlistaCompra::findOrFail($categoria->id_llista_compra);
        
        // Verificar permisos
        if (!$this->potEditar($llista)) {
            abort(403, 'No tens permisos per editar categories d\'aquesta llista');
        }

        return view('categoria.editar', compact('categoria'));
    }


    public function eliminar($id)
    {
        $categoria = Categoria::findOrFail($id);
        $llista = LlistaCompra::findOrFail($categoria->id_llista_compra);

        // Verificar permisos
        if (!$this->potEditar($llista)) {
            abort(403, 'No tens permisos per eliminar categories d\'aquesta llista');
        }

        // Guardem l'ID de la llista abans d'eliminar
        $id_llista = $categoria->id_llista_compra;

        // Eliminar productes associats abans d'eliminar la categoria
        $categoria->productes()->delete();

        $categoria->delete();

        return redirect()->route('categories.index', $id_llista);
    }

    public function create($id_llista)
    {
        $llista = LlistaCompra::findOrFail($id_llista);
        
        // Verificar permisos
        if (!$this->potEditar($llista)) {
            abort(403, 'No tens permisos per crear categories en aquesta llista');
        }
        
        return view('categoria.create', compact('id_llista'));
    }

    public function store(Request $request, $id_llista)
    {
        $llista = LlistaCompra::findOrFail($id_llista);
        
        // Verificar permisos
        if (!$this->potEditar($llista)) {
            abort(403, 'No tens permisos per crear categories en aquesta llista');
        }
        // Validem el nom
        $request->validate([
            'nom_categoria' => 'required|string|max:50',
        ]);

        // Verificar si ja existeix una categoria amb aquest nom en aquesta llista
        $categoriaExistent = Categoria::where('id_llista_compra', $id_llista)
            ->where('nom_categoria', $request->nom_categoria)
            ->exists();

        if ($categoriaExistent) {
            return back()->withErrors(['nom_categoria' => 'Ja existeix una categoria amb aquest nom en aquesta llista.'])->withInput();
        }

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
        $llista = LlistaCompra::findOrFail($categoria->id_llista_compra);

        // Verificar permisos
        if (!$this->potEditar($llista)) {
            abort(403, 'No tens permisos per actualitzar categories d\'aquesta llista');
        }

        // Guardem l'ID de la llista abans d'actualitzar
        $id_llista = $categoria->id_llista_compra;

        // Verificar si ja existeix una altra categoria amb aquest nom en aquesta llista
        $categoriaExistent = Categoria::where('id_llista_compra', $id_llista)
            ->where('nom_categoria', $request->nom_categoria)
            ->where('id_categoria', '!=', $id_categoria)
            ->exists();

        if ($categoriaExistent) {
            return back()->withErrors(['nom_categoria' => 'Ja existeix una categoria amb aquest nom en aquesta llista.'])->withInput();
        }

        $categoria->nom_categoria = $request->nom_categoria;
        $categoria->save();

        // 👉 Passem l'ID de la llista a la ruta
        return redirect()->route('categories.index', $id_llista);
    }
}
