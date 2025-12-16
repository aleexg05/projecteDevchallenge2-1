<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Categoria;
use App\Models\LlistaCompra;
use Illuminate\Http\Request;
use App\Models\Etiqueta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LlistaCompraController extends Controller
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

    public function index()
    {
        /** @var \App\Models\User $usuari */
        $usuari = Auth::user();
        $llistes = $usuari->llistesCreades()->get();
        $llistesCompartides = $usuari->llistesCompartides()->with('creador')->get();

        return view('llistes.index', compact('llistes', 'llistesCompartides'));
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
        ];

        foreach ($etiquetasDefecte as $nom) {
            Etiqueta::firstOrCreate([
                'etiqueta_producte' => $nom,
                'user_id' => null,
            ]);
        }

        return redirect()->route('llistes.index');
    }

    public function editar($id)
    {
        $llista = LlistaCompra::with('productes.categoria')->findOrFail($id);

        // Verificar que l'usuari té accés (creador o compartit)
        $usuari = Auth::user();
        if ($llista->user_id !== $usuari->id && !$usuari->llistesCompartides->contains($llista)) {
            abort(403, 'No tens permís per accedir a aquesta llista');
        }

        // Carreguem només les categories d'aquesta llista
        $categories = Categoria::where('id_llista_compra', $llista->id_llista_compra)->get();

        // Estats temporals dels productes
        $estats = session()->get("llista_{$id}_estats", []);
        foreach ($llista->productes as $producte) {
            if (!isset($estats[$producte->id_producte])) {
                $estats[$producte->id_producte] = false;
            }
        }
        session()->put("llista_{$id}_estats", $estats);

        // Verificar si pot editar
        $potEditar = $this->potEditar($llista);

        return view('llistes.editar', compact('llista', 'categories', 'estats', 'potEditar'));
    }

    public function toggleProducte($id_llista, $id_producte)
    {
        $estats = session()->get("llista_{$id_llista}_estats", []);
        $estats[$id_producte] = !($estats[$id_producte] ?? false);
        session()->put("llista_{$id_llista}_estats", $estats);

        return redirect()->route('llistes.editar', $id_llista);
    }

    public function actualitzar(Request $request, $id)
    {
        $request->validate(['nom' => 'required|string|max:50']);

        $llista = LlistaCompra::findOrFail($id);

        // Verificar permisos
        if (!$this->potEditar($llista)) {
            abort(403, 'No tens permisos per actualitzar aquesta llista');
        }

        $llista->nom = $request->nom;
        $llista->save();

        return redirect()->route('llistes.index') ;
     }

    public function eliminar($id)
    {
        $llista = LlistaCompra::findOrFail($id);

        // Verificar permisos
        if (!$this->potEditar($llista)) {
            abort(403, 'No tens permisos per eliminar aquesta llista');
        }

        // Eliminar productes associats
        $llista->productes()->delete();

        // Eliminar usuaris compartits
        $llista->usuarisCompartits()->detach();

        // Eliminar la llista
        $llista->delete();

        return redirect()->route('llistes.index');
    }

    public function editarNom($id)
    {
        $llista = LlistaCompra::findOrFail($id);

        // Verificar permisos
        if (!$this->potEditar($llista)) {
            abort(403, 'No tens permisos per editar el nom d\'aquesta llista');
        }

        return view('llistes.editarNom', compact('llista'));
    }

    // --- MÈTODES PER GESTIONAR COMPARTICIÓ ---

    public function mostrarCompartir($id)
    {
        $llista = LlistaCompra::where('id_llista_compra', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $usuarisCompartits = $llista->usuarisCompartits;
        $altresUsuaris = User::where('id', '!=', Auth::id())
            ->whereNotIn('id', $usuarisCompartits->pluck('id'))
            ->get();

        return view('compartir.index', compact('llista', 'usuarisCompartits', 'altresUsuaris'));
    }

    public function compartir(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'rol' => 'required|in:visualitzador,administrador',
        ]);

        $llista = LlistaCompra::where('id_llista_compra', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Verificar que no sigui el creador
        if ($request->user_id == Auth::id()) {
            return back()->with('error', 'No pots compartir la llista amb tu mateix');
        }

        // Verificar que no estigui ja compartit
        if ($llista->usuarisCompartits()->where('user_id', $request->user_id)->exists()) {
            return back()->with('error', 'La llista ja està compartida amb aquest usuari');
        }

        // Compartir la llista amb el rol especificat
        $llista->usuarisCompartits()->attach($request->user_id, ['rol' => $request->rol]);

        return back()->with('success', 'Llista compartida correctament');
    }

    public function deixarCompartir($id, $userId)
    {
        $llista = LlistaCompra::where('id_llista_compra', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $llista->usuarisCompartits()->detach($userId);

        return back()->with('success', 'Usuari eliminat de la llista compartida');
    }

    public function canviarRol(Request $request, $id, $userId)
    {
        $request->validate([
            'rol' => 'required|in:visualitzador,administrador',
        ]);

        $llista = LlistaCompra::where('id_llista_compra', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $llista->usuarisCompartits()->updateExistingPivot($userId, ['rol' => $request->rol]);

        return back()->with('success', 'Permisos actualitzats correctament');
    }

    public function sortir($id)
    {
        $llista = LlistaCompra::findOrFail($id);

        // Verificar que l'usuari tingui accés a aquesta llista compartida
        if (!Auth::user()->llistesCompartides->contains($llista)) {
            abort(403, 'No tens accés a aquesta llista');
        }

        // Eliminar l'usuari de la llista compartida
        $llista->usuarisCompartits()->detach(Auth::id());

        return redirect()->route('llistes.index')->with('success', 'Has sortit de la llista compartida');
    }
}
