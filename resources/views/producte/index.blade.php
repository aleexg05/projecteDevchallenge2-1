@extends('layouts.app')

@section('títol', 'Gestionar productes')

@section('content')
<style>
    /* Títols */
    h1 {
        color: #ffffff;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        margin-bottom: 0;
    }
.alert {
    padding-left:20px;

}
    /* Text general */
    body, p, label, span, div, strong {
        color: #ffffff;
    }

    .text-muted {
       
        color: rgba(255, 255, 255, 0.6) !important;
    }

    /* Botons */
    .btn {
        padding: 10px 18px;
        margin-top:20px;
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

    .list-group {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .list-group-item {
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        margin-bottom: 12px;
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        padding: 16px;
        transition: all 0.3s ease;
    }

    .list-group-item:hover {
        background: rgba(255, 255, 255, 0.1);
        box-shadow: 0 4px 12px rgba(167, 139, 250, 0.3);
    }

    .header-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .alert {
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        color: #fff;
    }

    .alert-success {
        background: rgba(34, 197, 94, 0.15);
        border-color: rgba(34, 197, 94, 0.5);
    }

    .alert-info {
        background: rgba(96, 165, 250, 0.15);
        border-color: rgba(96, 165, 250, 0.5);
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
    }
</style>

<div class="container py-4">
    <div class="header-actions">
        <h1>📦 Gestionar productes — {{ $llista->nom }}</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('productes.create', $llista->id_llista_compra) }}" class="btn btn-outline-primary">➕ Afegir producte</a>
            <a href="{{ route('llistes.editar', $llista->id_llista_compra) }}" class="btn btn-outline-secondary">← Tornar a la llista</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($productes->count())
        <ul class="list-group">
            @foreach($productes as $producte)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $producte->nom_producte }}</strong>
                        <span class="text-muted">({{ $producte->categoria->nom_categoria ?? 'Sense categoria' }})</span>
                        @if(!empty($producte->etiqueta_producte))
                            <span class="text-muted ms-2">— {{ $producte->etiqueta_producte }}</span>
                        @endif
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('productes.edit', [$llista->id_llista_compra, $producte->id_producte]) }}" 
   class="btn btn-sm btn-outline-warning">Editar</a>

<form action="{{ route('productes.destroy', [$llista->id_llista_compra, $producte->id_producte]) }}" 
      method="POST" onsubmit="return confirm('Eliminar producte?');">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
</form>

                    </div>
                </li>
            @endforeach
        </ul>
    @else
            <div id="alert" class="alert alert-info text-center">
                No hi ha productes en aquesta llista.
            </div>
    @endif
</div>
@endsection