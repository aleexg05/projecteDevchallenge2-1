@extends('layouts.app')

@section('títol', 'Editar llista de compra')

@section('content')
<style>
    .list-group-item {
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        margin-bottom: 12px;
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        padding: 16px;
        transition: all 0.3s ease;
        width: 100%;
        text-align: left;
        cursor: pointer;
        color: #fff;
        font-size: 16px;
    }.text-empty {
    color: #ffffff; /* 👈 blanc */
    opacity: 0.9;
    font-style: italic;
}


    .list-group-item:hover {
        background: rgba(255, 255, 255, 0.1);
        box-shadow: 0 4px 12px rgba(167, 139, 250, 0.3);
    }

    .list-group-item.ratllat {
        text-decoration: line-through;
        opacity: 0.6;
        color: rgba(255, 255, 255, 0.5);
    }

    .categoria-nom {
        font-weight: 600;
        font-size: 16px;
        color: #a78bfa;
        margin-top: 24px;
        margin-bottom: 12px;
        text-shadow: 1px 1px 3px rgba(0,0,0,0.4);
    }

    .btn {
        padding: 10px 18px;
        border-radius: 6px;
        font-size: 14px;
        text-decoration: none;
        border: 1px solid rgba(255, 255, 255, 0.3);
        background-color: rgba(255, 255, 255, 0.1);
        color: #fff;
        transition: all 0.2s ease;
        display: inline-block;
    }

    .btn:hover {
        background-color: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    }

    .btn-outline-primary {
        border-color: #a78bfa;
        color: #a78bfa;
        background-color: rgba(167, 139, 250, 0.1);
    }
    .btn-outline-primary:hover {
        background-color: #a78bfa;
        color: #1a0b2e;
    }

    .btn-outline-secondary {
        border-color: #60a5fa;
        color: #60a5fa;
        background-color: rgba(96, 165, 250, 0.1);
    }
    .btn-outline-secondary:hover {
        background-color: #60a5fa;
        color: #0f3460;
    }

    h1, h4 {
        color: #ffffff;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }

    h1 {
        text-align: center;
        margin-bottom: 30px;
        font-weight: 600;
    }

    h4 {
        margin-top: 40px;
        margin-bottom: 20px;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
    }
</style>

<div class="container py-4">
    <h1 class="mb-4">✏️ Editar llista: {{ $llista->nom }}</h1>

    <!-- Botons d'accions -->
    <div class="button-group justify-content-center mb-4">
        <a href="{{ route('llistes.editarNom', $llista->id_llista_compra) }}" class="btn btn-outline-primary">✏️ Canviar nom</a>
        <a href="{{ route('categories.index', $llista->id_llista_compra) }}" class="btn btn-outline-secondary">🏷️ Gestionar categories</a>
        <a href="{{ route('productes.index', $llista->id_llista_compra) }}" class="btn btn-outline-secondary">📦 Gestionar productes</a>
        <a href="{{ route('etiquetas.index') }}" class="btn btn-outline-secondary">🏷️ Gestionar etiquetes</a>
    </div>

    <!-- Productes agrupats per categories -->
    <h4 class="mt-4 mb-3">📦 Productes en aquesta llista per categories</h4>

    @php
        $productesPerCategoria = $llista->productes->groupBy(function($producte) {
            return $producte->categoria->nom_categoria ?? 'Sense categoria';
        });
    @endphp

    @forelse($productesPerCategoria as $nomCategoria => $productes)
        <div class="categoria-nom">🏷️ {{ $nomCategoria }}</div>
        @foreach($productes as $producte)
            <form action="{{ route('productes.toggle', [$llista->id_llista_compra, $producte->id_producte]) }}" method="POST">
                @csrf
                @method('PUT')
                <button type="submit" class="list-group-item {{ $producte->comprat ? 'ratllat' : '' }}">
                    {{ $producte->nom_producte }}
                </button>
            </form>
        @endforeach
    @empty
        <p class="text-empty">No hi ha productes en aquesta llista.</p>
    @endforelse

    <!-- Productes agrupats per etiquetes -->
    <h4 class="mt-5 mb-3">🏷️ Productes en aquesta llista per etiquetes</h4>

    @php
        $productesPerEtiqueta = $llista->productes->groupBy(function($producte) {
            return $producte->etiqueta_producte ?? 'Sense etiqueta';
        });
    @endphp

    @forelse($productesPerEtiqueta as $nomEtiqueta => $items)
        <div class="categoria-nom">🏷️ {{ $nomEtiqueta }}</div>
        @foreach($items as $producte)
            <form action="{{ route('productes.toggle', [$llista->id_llista_compra, $producte->id_producte]) }}" method="POST">
                @csrf
                @method('PUT')
                <button type="submit" class="list-group-item {{ $producte->comprat ? 'ratllat' : '' }}">
                    {{ $producte->nom_producte }}
                </button>
            </form>
        @endforeach
    @empty
    
        <p class="text-empty">No hi ha productes amb etiquetes.</p>
    @endforelse

    <!-- Botó tornar -->
    <div class="text-end mt-4">
        <a href="{{ route('llistes.index') }}" class="btn btn-outline-primary">← Tornar a les llistes</a>
    </div>
</div>
@endsection
