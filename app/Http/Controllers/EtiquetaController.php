<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Etiqueta;
use App\Models\LlistaCompra;
use Illuminate\Support\Facades\Auth;

class EtiquetaController extends Controller
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

    public function index(Request $request, $id_llista = null)
    {
        $llista = null;
        $potEditar = true; // Per defecte, si no hi ha llista, l'usuari pot gestionar les seves pròpies etiquetes
        $propietariLlista = null;

        if ($id_llista) {
            $llista = LlistaCompra::findOrFail($id_llista);
            $potEditar = $this->potEditar($llista);
            $propietariLlista = $llista->creador;
        }

        // Si hi ha llista, mostrem les etiquetes del propietari de la llista + globals
        // Si no hi ha llista, mostrem les globals + les pròpies
        if ($llista) {
            // Mostrar etiquetes globals, les personals del propietari de la llista (no vinculades a cap llista)
            // i les específiques d'aquesta llista
            $etiquetas = Etiqueta::where(function($q) use ($llista) {
                    $q->whereNull('user_id')
                      ->orWhere(function($subq) use ($llista) {
                          $subq->where('user_id', $llista->user_id)
                               ->whereNull('id_llista_compra');
                      })
                      ->orWhere('id_llista_compra', $llista->id_llista_compra);
                })
                ->get();
        } else {
            // Mostrar només etiquetes globals i les pròpies personals (no les que són específiques de llistes compartides)
            $etiquetas = Etiqueta::whereNull('user_id')
                ->orWhere(function($q) {
                    $q->where('user_id', auth()->id())
                      ->whereNull('id_llista_compra');
                })
                ->get();
        }

        $returnTo = $request->query('return_to');

        return view('etiquetas.index', compact('etiquetas', 'returnTo', 'llista', 'potEditar', 'propietariLlista'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'etiqueta_producte' => 'required|string|max:100',
            'return_to' => 'nullable|url',
            'id_llista' => 'nullable|exists:llistes_compra,id_llista_compra',
        ]);

        $llista = null;
        if ($request->id_llista) {
            $llista = LlistaCompra::findOrFail($request->id_llista);
            
            // Verificar permisos
            if (!$this->potEditar($llista)) {
                abort(403, 'No tens permisos per crear etiquetes en aquesta llista');
            }
        }

        // Verificar duplicitats per propietari de la llista (si aplica) o per usuari autenticat
        // Regles de deduplicació:
        // - En context de llista: evitar duplicats entre globals, del propietari i les específiques d'aquesta llista
        // - Fora de llista: evitar duplicats entre globals i les pròpies de l'usuari
        if ($llista) {
            $etiquetaExistent = Etiqueta::where('etiqueta_producte', $request->etiqueta_producte)
                ->where(function ($query) use ($llista) {
                    $query->whereNull('user_id')
                          ->orWhere('user_id', $llista->user_id)
                          ->orWhere('id_llista_compra', $llista->id_llista_compra);
                })
                ->exists();
        } else {
            $etiquetaExistent = Etiqueta::where('etiqueta_producte', $request->etiqueta_producte)
                ->where(function ($query) {
                    $query->where('user_id', auth()->id())
                          ->orWhereNull('user_id');
                })
                ->exists();
        }

        if ($etiquetaExistent) {
            return back()->withErrors(['etiqueta_producte' => 'Ja existeix una etiqueta amb aquest nom.'])->withInput();
        }

        // Crear etiqueta segons el context
        if ($llista) {
            // Si el creador és el propietari de la llista, l'etiqueta és personal del propietari
            if ($llista->user_id === auth()->id()) {
                Etiqueta::create([
                    'etiqueta_producte' => $request->etiqueta_producte,
                    'user_id' => $llista->user_id,
                    'id_llista_compra' => null,
                ]);
            } else {
                // Si és un usuari convidat/administrador, l'etiqueta queda vinculada només a aquesta llista
                Etiqueta::create([
                    'etiqueta_producte' => $request->etiqueta_producte,
                    'user_id' => auth()->id(),
                    'id_llista_compra' => $llista->id_llista_compra,
                ]);
            }
        } else {
            // Fora de llista, etiqueta personal de l'usuari
            Etiqueta::create([
                'etiqueta_producte' => $request->etiqueta_producte,
                'user_id' => auth()->id(),
                'id_llista_compra' => null,
            ]);
        }

        $returnTo = $request->input('return_to');

        return $returnTo ? redirect($returnTo) : redirect()->route('etiquetas.index');
    }

    public function destroy(Request $request, $id_etiqueta)
    {
        $id_llista = $request->query('id_llista');
        
        if ($id_llista) {
            $llista = LlistaCompra::findOrFail($id_llista);
            
            // Verificar permisos
            if (!$this->potEditar($llista)) {
                abort(403, 'No tens permisos per eliminar etiquetes en aquesta llista');
            }
                        // En context de llista, permetre eliminar etiquetes del propietari, globals i les específiques d'aquesta llista
                        $etiqueta = Etiqueta::where(function($q) use ($llista) {
                                        $q->where('user_id', $llista->user_id)
                                            ->orWhereNull('user_id')
                                            ->orWhere('id_llista_compra', $llista->id_llista_compra);
                                })
                                ->findOrFail($id_etiqueta);
        } else {
            // Fora de llista, permetre gestionar també les globals
            $etiqueta = Etiqueta::where(function($q){
                $q->where('user_id', auth()->id())
                  ->orWhereNull('user_id');
            })
            ->findOrFail($id_etiqueta);
        }

        $etiqueta->delete();

        $returnTo = $request->input('return_to');

        return $returnTo ? redirect($returnTo) : redirect()->route('etiquetas.index');
    }

    public function create(Request $request)
    {
        $returnTo = $request->query('return_to');
        $id_llista = $request->query('id_llista');
        
        $llista = null;
        if ($id_llista) {
            $llista = LlistaCompra::findOrFail($id_llista);
            
            // Verificar permisos
            if (!$this->potEditar($llista)) {
                abort(403, 'No tens permisos per crear etiquetes en aquesta llista');
            }
        }
        
        return view('etiquetas.create', compact('returnTo', 'id_llista'));
    }
}
