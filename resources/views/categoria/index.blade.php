@extends('layouts.app')

@section('títol', 'Gestionar categories')

@section('content')
<style>
    /* Botó de crear categoria més avall i a la dreta */
    .create-button {
        text-align: right;
        margin-right: 60px;
        margin-bottom: 32px;
    }

    /* Botons generals */
    .btn {
        padding: 12px 20px;
        border-radius: 6px;
        font-size: 15px;
        text-decoration: none;
        border: 1px solid rgba(255, 255, 255, 0.3);
        background-color: rgba(255, 255, 255, 0.1);
        color: #fff;
        transition: all 0.2s ease;
        display: inline-block;
        margin-left: 20px;
        margin-bottom: 20px;
    }

    .btn:hover {
        background-color: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    }

    /* Variants minimalistes */
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

    .btn-outline-warning {
        border-color: #ffc107;
        color: #ffc107;
        background-color: rgba(255, 193, 7, 0.1);
    }
    .btn-outline-warning:hover {
        background-color: #ffc107;
        color: #000;
    }

    .btn-outline-danger {
        border-color: #ef4444;
        color: #ef4444;
        background-color: rgba(239, 68, 68, 0.1);
    }
    .btn-outline-danger:hover {
        background-color: #ef4444;
        color: #fff;
    }

    /* Botons d'acció coherents (crear / tornar) */
    .btn-top {
        padding: 12px 20px;
        border-radius: 6px;
        font-size: 15px;
        text-decoration: none;
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: #fff;
        transition: all 0.2s ease;
        display: inline-block;
        margin-left: 12px;
    }

    .btn-top:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    }

    .btn-top-main {
        border: none;
        background: linear-gradient(135deg, #a78bfa, #60a5fa);
        color: #1a0b2e;
        font-weight: 600;
    }

    .btn-top-main:hover {
        background: linear-gradient(135deg, #8b5cf6, #3b82f6);
        color: #fff;
    }

    .btn-top-secondary {
        border-color: #60a5fa;
        color: #60a5fa;
        background-color: rgba(96, 165, 250, 0.1);
    }

    .btn-top-secondary:hover {
        background-color: #60a5fa;
        color: #0f3460;
    }

    .btn-main {
        border: none;
        background: linear-gradient(135deg, #a78bfa, #60a5fa);
        color: #1a0b2e;
        font-weight: 600;
    }
    .btn-main:hover {
        background: linear-gradient(135deg, #8b5cf6, #3b82f6);
        color: #fff;
    }

    #nomCategoria {
        margin-left: 20px;
        margin-bottom: 40px;
        color: #ffffff;
        font-size: 18px;
    }

    #botoEditar {
        margin-top: 10px;
    }

    .list-group-item {
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }

    .list-group-item:hover {
        background: rgba(255, 255, 255, 0.1);
        box-shadow: 0 4px 12px rgba(167, 139, 250, 0.3);
        transform: translateX(5px);
    }

    /* Disposició en dues columnes auto-ajustables */
    .category-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 16px;
    }

    .alert {
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        color: #fff;
    }

    .alert-success {
        border-color: rgba(34, 197, 94, 0.5);
        background: rgba(34, 197, 94, 0.15);
    }

    .alert-info {
        border-color: rgba(96, 165, 250, 0.5);
        background: rgba(96, 165, 250, 0.15);
    }

    h1 {
        color: #ffffff;
        text-align: center;
        margin-bottom: 30px;
        font-weight: 600;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
    }
</style>

<div class="container py-4">
    <h1 class="mb-4 text-center">
        🏷️ Categories de la llista: {{ $llista->nom }}
    </h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Botó de crear categoria -->
    <div class="create-button">
        @if($potEditar)
            <a href="{{ route('categories.create', $llista->id_llista_compra) }}" class="btn-top btn-top-main">+ Crear categoria</a>
        @endif
        <a href="{{ route('llistes.editar', $llista->id_llista_compra) }}" class="btn-top btn-top-secondary">← Tornar a la llista</a>
    </div>
   

    <!-- Categories -->
    @if($categories->count())
        <div class="category-grid">
            @foreach($categories as $categoria)
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <strong id="nomCategoria">{{ $categoria->nom_categoria }}</strong>
                    @if($potEditar)
                        <div class="d-flex gap-2">
                            <a id="botoEditar" href="{{ route('categories.editar', $categoria->id_categoria) }}" class="btn btn-sm btn-outline-warning">
                                Editar
                            </a>
                            <form action="{{ route('categories.eliminar', $categoria->id_categoria) }}" method="POST" onsubmit="return confirm('Segur que vols eliminar aquesta categoria?');">
                                @csrf
                                @method('DELETE')
                                <button id="botoEliminar" type="submit" class="btn btn-sm btn-outline-danger">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info text-center">
            @if($potEditar)
                No hi ha categories creades per aquesta llista. <a href="{{ route('categories.create', $llista->id_llista_compra) }}" style="color: #a78bfa; text-decoration: underline;">Crea una nova categoria</a>.
            @else
                No hi ha categories creades per aquesta llista.
            @endif
        </div>
    @endif

   
</div>
@endsection