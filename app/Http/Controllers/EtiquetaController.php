<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Etiqueta;

class EtiquetaController extends Controller
{
    public function index()
{
    // Globals + personals de l’usuari autenticat
    $etiquetas = Etiqueta::whereNull('user_id')
        ->orWhere('user_id', auth()->id())
        ->get();

    return view('etiquetas.index', compact('etiquetas'));
}

public function store(Request $request)
{
    $request->validate([
        'etiqueta_producte' => 'required|string|max:100',
    ]);

    // 👇 totes les que es creen des de la vista són personals
    Etiqueta::create([
        'etiqueta_producte' => $request->etiqueta_producte,
        'user_id' => auth()->id(),
    ]);

    return redirect()->route('etiquetas.index');
}
public function destroy($id_etiqueta)
{
    $etiqueta = Etiqueta::where('user_id', auth()->id())
        ->findOrFail($id_etiqueta);

    $etiqueta->delete();

    return redirect()->route('etiquetas.index');
}
public function create()
{
    return view('etiquetas.create'); // 👈 aquí poses el formulari
}

}
