<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Etiqueta; 



use Illuminate\Support\Facades\Auth;
use App\Models\LlistaCompra;
use App\Models\Producte;
use App\Models\Categoria;

class ProducteController extends Controller
{
    // Mostrar productes d’una llista
    public function index($id_llista)
    {
        $llista = LlistaCompra::where('id_llista_compra', $id_llista)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $productes = $llista->productes;

        return view('producte.index', compact('llista', 'productes'));
    }

    // Formulari per crear un producte dins d’una llista
   public function create($id_llista)
{


    $llista = LlistaCompra::findOrFail($id_llista);
    $categories = Categoria::where('id_llista_compra', $id_llista)->get();
    $etiquetas = Etiqueta::all(); 

    return view('producte.create', compact('llista', 'categories', 'etiquetas'));
}


    // Guardar un nou producte
    public function store(Request $request, $id_llista)
{
    $llista = LlistaCompra::findOrFail($id_llista);

    $request->validate([
    'nom_producte' => 'required|string|max:255',
    'id_categoria' => 'required|exists:categories,id_categoria',
    'etiqueta_producte' => 'nullable|string|max:50',
    'comprat' => 'boolean',
]);

Producte::create([
    'nom_producte'     => $request->nom_producte,
    'id_categoria'     => $request->id_categoria,
    'id_llista_compra' => $llista->id_llista_compra,
    'etiqueta_producte'=> $request->etiqueta_producte, // 👈 guarda el nom
    'comprat'          => $request->comprat ?? false,
]);


    return redirect()->route('llistes.editar', $llista->id_llista_compra);
}


    // Formulari per editar un producte
   public function edit($id_llista, $id_producte)
{
    $llista = LlistaCompra::findOrFail($id_llista);

    $producte = Producte::where('id_llista_compra', $id_llista)
                        ->where('id_producte', $id_producte)
                        ->firstOrFail();

    $categories = Categoria::where('id_llista_compra', $id_llista)->get();
    $etiquetas = Etiqueta::all(); // 👈 passa les etiquetes

    return view('producte.editar', compact('llista', 'producte', 'categories', 'etiquetas'));
}


    // Actualitzar un producte
    public function update(Request $request, $id_llista, $id_producte)
    {
        $producte = Producte::where('id_llista_compra', $id_llista)
                            ->where('id_producte', $id_producte)
                            ->firstOrFail();

        $request->validate([
            'nom_producte' => 'required|string|max:255',
            'id_categoria' => 'required|exists:categories,id_categoria',
            'etiqueta_producte' => 'nullable|string|max:50',
            'comprat' => 'boolean',
        ]);

        $producte->update([
            'nom_producte'     => $request->nom_producte,
            'id_categoria'     => $request->id_categoria,
            'etiqueta_producte'=> $request->etiqueta_producte,
            'comprat'          => $request->comprat ?? false,
        ]);

        return redirect()->route('llistes.editar', $id_llista);
    }

    // Eliminar un producte
    public function destroy($id_llista, $id_producte)
    {
        $producte = Producte::where('id_llista_compra', $id_llista)
                            ->where('id_producte', $id_producte)
                            ->firstOrFail();

        $producte->delete();

        return redirect()->route('llistes.editar', $id_llista);
    }

    // Toggle comprat/no comprat
    public function toggle($id_llista, $id_producte)
    {
        $producte = Producte::where('id_llista_compra', $id_llista)
                            ->where('id_producte', $id_producte)
                            ->firstOrFail();

        $producte->comprat = !$producte->comprat;
        $producte->save();

        return redirect()->route('llistes.editar', $id_llista);
    }


}
