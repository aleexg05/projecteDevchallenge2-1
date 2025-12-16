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

    // Verificar si l'usuari té accés a la llista (propietari o compartit)
    private function verificarAcces($id_llista)
    {
        $llista = LlistaCompra::findOrFail($id_llista);
        $usuari = Auth::user();

        // Verificar si és el propietari o té accés compartit
        if ($llista->user_id !== $usuari->id && !$usuari->llistesCompartides->contains($llista)) {
            abort(403, 'No tens permís per accedir a aquesta llista');
        }

        return $llista;
    }

    // Mostrar productes d'una llista
    public function index($id_llista)
    {
        $llista = $this->verificarAcces($id_llista);
        $productes = $llista->productes;
        $potEditar = $this->potEditar($llista);

        return view('producte.index', compact('llista', 'productes', 'potEditar'));
    }

    // Formulari per crear un producte dins d'una llista
    public function create($id_llista)
    {
        $llista = $this->verificarAcces($id_llista);
        
        // Verificar permisos d'edició
        if (!$this->potEditar($llista)) {
            abort(403, 'No tens permisos per afegir productes a aquesta llista');
        }
        
        $categories = Categoria::where('id_llista_compra', $id_llista)->get();
        $etiquetas = Etiqueta::all();

        return view('producte.create', compact('llista', 'categories', 'etiquetas'));
    }

    // Guardar un nou producte
    public function store(Request $request, $id_llista)
    {
        $llista = $this->verificarAcces($id_llista);

        // Verificar permisos d'edició
        if (!$this->potEditar($llista)) {
            abort(403, 'No tens permisos per afegir productes a aquesta llista');
        }

        $request->validate([
            'nom_producte' => 'required|string|max:255',
            'id_categoria' => 'required|exists:categories,id_categoria',
            'etiqueta_producte' => 'nullable|string|max:50',
            'comprat' => 'boolean',
        ]);

        // Verificar si ja existeix un producte amb aquest nom en aquesta llista
        $producteExistent = Producte::where('id_llista_compra', $id_llista)
            ->where('nom_producte', $request->nom_producte)
            ->exists();

        if ($producteExistent) {
            return back()->withErrors(['nom_producte' => 'Ja existeix un producte amb aquest nom en aquesta llista.'])->withInput();
        }

        Producte::create([
            'nom_producte'     => $request->nom_producte,
            'id_categoria'     => $request->id_categoria,
            'id_llista_compra' => $llista->id_llista_compra,
            'etiqueta_producte' => $request->etiqueta_producte,
            'comprat'          => $request->comprat ?? false,
        ]);

        return redirect()->route('llistes.editar', $llista->id_llista_compra);
    }

    // Formulari per editar un producte
    public function edit($id_llista, $id_producte)
    {
        $llista = $this->verificarAcces($id_llista);

        // Verificar permisos d'edició
        if (!$this->potEditar($llista)) {
            abort(403, 'No tens permisos per editar productes d\'aquesta llista');
        }

        $producte = Producte::where('id_llista_compra', $id_llista)
            ->where('id_producte', $id_producte)
            ->firstOrFail();

        $categories = Categoria::where('id_llista_compra', $id_llista)->get();
        $etiquetas = Etiqueta::all();

        return view('producte.editar', compact('llista', 'producte', 'categories', 'etiquetas'));
    }

    // Actualitzar un producte
    public function update(Request $request, $id_llista, $id_producte)
    {
        $llista = $this->verificarAcces($id_llista);

        // Verificar permisos d'edició
        if (!$this->potEditar($llista)) {
            abort(403, 'No tens permisos per actualitzar productes d\'aquesta llista');
        }

        $producte = Producte::where('id_llista_compra', $id_llista)
            ->where('id_producte', $id_producte)
            ->firstOrFail();

        $request->validate([
            'nom_producte' => 'required|string|max:255',
            'id_categoria' => 'required|exists:categories,id_categoria',
            'etiqueta_producte' => 'nullable|string|max:50',
            'comprat' => 'boolean',
        ]);

        // Verificar si ja existeix un altre producte amb aquest nom en aquesta llista
        $producteExistent = Producte::where('id_llista_compra', $id_llista)
            ->where('nom_producte', $request->nom_producte)
            ->where('id_producte', '!=', $id_producte)
            ->exists();

        if ($producteExistent) {
            return back()->withErrors(['nom_producte' => 'Ja existeix un producte amb aquest nom en aquesta llista.'])->withInput();
        }

        $producte->update([
            'nom_producte'     => $request->nom_producte,
            'id_categoria'     => $request->id_categoria,
            'etiqueta_producte' => $request->etiqueta_producte,
            'comprat'          => $request->comprat ?? false,
        ]);

        return redirect()->route('llistes.editar', $id_llista);
    }

    // Eliminar un producte
    public function destroy($id_llista, $id_producte)
    {
        $llista = $this->verificarAcces($id_llista);

        // Verificar permisos d'edició
        if (!$this->potEditar($llista)) {
            abort(403, 'No tens permisos per eliminar productes d\'aquesta llista');
        }

        $producte = Producte::where('id_llista_compra', $id_llista)
            ->where('id_producte', $id_producte)
            ->firstOrFail();

        $producte->delete();

        return redirect()->route('llistes.editar', $id_llista);
    }

    // Toggle comprat/no comprat
    public function toggle($id_llista, $id_producte)
    {
        $llista = $this->verificarAcces($id_llista);

        $producte = Producte::where('id_llista_compra', $id_llista)
            ->where('id_producte', $id_producte)
            ->firstOrFail();

        $producte->comprat = !$producte->comprat;
        $producte->save();

        return redirect()->route('llistes.editar', $id_llista);
    }
}
