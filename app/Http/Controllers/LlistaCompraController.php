<?php

namespace App\Http\Controllers;

use App\Models\User;


use App\Models\Categoria;
use App\Models\LlistaCompra;
use Illuminate\Http\Request;
use App\Models\Etiqueta;   
use Illuminate\Support\Facades\Auth;   // 👈 importa la façana correcta

class LlistaCompraController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $usuari */
        $usuari = Auth::user();
        $llistes = $usuari->llistesCreades()->get();

        return view('llistes.index', compact('llistes'));
    }

    public function create()
    {
        return view('llistes.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'nom' => 'required|string|max:255',
    ]);

    $llista = LlistaCompra::create([
        'nom' => $request->nom,
        'user_id' => auth()->id(),
    ]);

    // Crear categories per defecte
    $categoriesDefecte = [
        'Fruites i verdures',
        'Carn i peix',
        'Làctics i ous',
        'Pa i pastisseria',
        'Congelats',
        'Begudes',
        'Conserves',
        'Pasta i arròs',
        'Snacks i dolços',
        'Neteja',
        'Higiene personal',
        'Altres',
    ];

    foreach ($categoriesDefecte as $nom) {
        Categoria::create([
            'nom_categoria' => $nom,
            'id_llista_compra' => $llista->id_llista_compra,
        ]);
    }

    // Crear etiquetes per defecte
   $etiquetasDefecte = [
    'Urgent',
    'Opcional',
    'Important',
    'Per comprar aviat',
    'Ja en tinc',
];

foreach ($etiquetasDefecte as $nom) {
    Etiqueta::firstOrCreate([
        'etiqueta_producte' => $nom,
         'user_id' => null, // 👈 globals
    ]);
}

    return redirect()->route('llistes.index');}

    public function editar($id)
    {
        $llista = LlistaCompra::with('productes.categoria')->findOrFail($id);

        // Carreguem només les categories d’aquesta llista
        $categories = Categoria::where('id_llista_compra', $llista->id_llista_compra)->get();

        // Estats temporals dels productes
        $estats = session()->get("llista_{$id}_estats", []);
        foreach ($llista->productes as $producte) {
            if (!isset($estats[$producte->id_producte])) {
                $estats[$producte->id_producte] = false;
            }
        }
        session()->put("llista_{$id}_estats", $estats);

        return view('llistes.editar', compact('llista', 'categories', 'estats'));
    }

    public function toggleProducte($id_llista, $id_producte)
    {
        $estats = session()->get("llista_{$id_llista}_estats", []);

        // Alternem l’estat
        $estats[$id_producte] = !($estats[$id_producte] ?? false);

        session()->put("llista_{$id_llista}_estats", $estats);

        return redirect()->route('llistes.editar', $id_llista);
    }




    public function actualitzar(Request $request, $id)
    {

        $request->validate(['nom' => 'required|string|max:50']);

        $llista = LlistaCompra::where('id_llista_compra', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        $llista->nom = $request->nom;
        $llista->save();


        if ($llista->nom !== $request->nom) {
            $llista->nom = $request->nom;
            $llista->save();
            return redirect()->route('llistes.index')->with('success', 'Llista actualitzada.');
        }

        return redirect()->route('llistes.index')->with('info', 'No s\'han fet canvis.');
    }


    public function eliminar($id)
    {
        $llista = LlistaCompra::where('id_llista_compra', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Eliminar productes associats
        $llista->productes()->delete();

        // Eliminar usuaris compartits si tens pivot
        $llista->usuarisCompartits()->detach();

        // Ara sí, eliminar la llista
        $llista->delete();

        return redirect()->route('llistes.index');
    }
    public function editarNom($id)
    {
        // Busquem la llista per ID i usuari autenticat
        $llista = LlistaCompra::where('id_llista_compra', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Retornem la vista editarNom.blade.php amb la llista
        return view('llistes.editarNom', compact('llista'));
    }
}
