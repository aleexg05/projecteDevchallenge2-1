@extends('layouts.app')

@section('títol', 'Gestionar etiquetes')

@section('content')
<style>
    /* Botó de crear etiqueta més avall i a la dreta */
    .create-button {
        text-align: right;
        margin-right: 60px;
        margin-bottom: 32px;
    }
    

    /* Botons generals */
    .btn {
        padding: 12px 20px;
        margin-top:20px;
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

    #nomEtiqueta {
        margin-top:30px;
        margin-left: 20px;
        padding-bottom:20px;
        color: #ffffff;
        font-size: 18px;
    }

    #botoEditar {
        margin-top: 10px;
    }

    .list-group-item {
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding-top:10px;
        border-radius: 8px;
        margin-bottom: 12px;
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }

    .list-group-item:hover {
        background: rgba(255, 255, 255, 0.1);
        box-shadow: 0 4px 12px rgba(167, 139, 250, 0.3);
        transform: translateX(5px);
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
    <h1 class="mb-4 text-center">🏷️ Gestionar etiquetes</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Botó de crear etiqueta -->
    <div class="create-button">
        <a href="{{ route('etiquetas.create') }}" class="btn btn-main">➕ Afegir etiqueta</a>
        <a href="{{ route('llistes.index') }}" class="btn btn-outline-secondary">← Tornar a les llistes</a>
    </div>

    <!-- Etiquetes -->
    @if($etiquetas->count())
        <ul class="list-group">
            @foreach($etiquetas as $etiqueta)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong id="nomEtiqueta">{{ $etiqueta->etiqueta_producte }}</strong>
                    <form action="{{ route('etiquetas.destroy', $etiqueta->id_etiqueta) }}" method="POST" onsubmit="return confirm('Eliminar etiqueta?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                    </form>
                </li>
            @endforeach
        </ul>
    @else
        <div class="alert alert-info text-center">
            No hi ha etiquetes creades. <a href="{{ route('etiquetas.create') }}" style="color: #a78bfa; text-decoration: underline;">Crea una nova etiqueta</a>.
        </div>
    @endif
</div>
@endsection
