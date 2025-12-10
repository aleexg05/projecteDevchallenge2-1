@extends('layouts.app')

@section('títol', 'Les meves llistes de compra')

@section('content')
<style>
    /* Botons principals centrats i separats */
    .button-group {
        display: flex;
        justify-content: center;
        gap: 32px;
        margin-bottom: 40px;
    }

    /* Botó de crear llista més avall i a la dreta */
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

    .btn-outline-primary.active {
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

    .btn-outline-secondary.active {
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

    .btn-outline-orange {
        border-color: #fb923c;
        color: #fb923c;
        background-color: rgba(251, 146, 60, 0.1);
    }

    .btn-outline-orange:hover {
        background-color: #fb923c;
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

    #nomLlista {
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

    .alert a {
        color: #a78bfa;
        text-decoration: underline;
    }

    .alert a:hover {
        color: #c4b5fd;
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

    .llista-info {
        font-size: 13px;
        color: #9ca3af;
        margin-left: 20px;
        margin-top: 5px;
    }

    .section {
        display: none;
    }

    .section.active {
        display: block;
    }
</style>

<div class="container py-4">
    <!-- Botons principals -->
    <div class="button-group text-center mb-5">
        <a href="#" class="btn btn-outline-primary" onclick="mostrarSeccio('meves'); return false;" id="btn-meves">Les meves llistes</a>
        <a href="#" class="btn btn-outline-secondary" onclick="mostrarSeccio('compartides'); return false;" id="btn-compartides">Llistes compartides amb mi</a>
    </div>

    <!-- Botó de crear llista -->
    <div class="create-button">
        <a href="{{ route('llistes.create') }}" class="btn btn-main">+ Crear llista</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- SECCIÓ: Les meves llistes -->
    <div id="seccio-meves" class="section active">
        @if($llistes->count())
        <ul class="list-group">
            @foreach($llistes as $llista)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <strong id="nomLlista">{{ $llista->nom }}</strong>
                    <div class="llista-info">
                        {{ $llista->productes->count() }} productes
                        @if($llista->usuarisCompartits && $llista->usuarisCompartits->count() > 0)
                        · Compartida amb {{ $llista->usuarisCompartits->count() }} {{ $llista->usuarisCompartits->count() == 1 ? 'persona' : 'persones' }}
                        @endif
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a id="botoEditar" href="{{ route('llistes.editar', $llista->id_llista_compra) }}" class="btn btn-sm btn-outline-warning">
                        Editar
                    </a>
                    <a href="{{ route('llistes.compartir.mostrar', $llista->id_llista_compra) }}" class="btn btn-sm btn-outline-primary">
                        Compartir
                    </a>
                    <form action="{{ route('llistes.eliminar', $llista->id_llista_compra) }}" method="POST" onsubmit="return confirm('Segur que vols eliminar aquesta llista?');">
                        @csrf
                        @method('DELETE')
                        <button id="botoEliminar" type="submit" class="btn btn-sm btn-outline-danger">
                            Eliminar
                        </button>
                    </form>
                </div>
            </li>
            @endforeach
        </ul>
        @else
        <div class="alert alert-info text-center">
            No tens cap llista creada. <a href="{{ route('llistes.create') }}">Crea una nova llista</a>.
        </div>
        @endif
    </div>

    <!-- SECCIÓ: Llistes compartides amb mi -->
    <div id="seccio-compartides" class="section">
        @if($llistesCompartides->count())
        <ul class="list-group">
            @foreach($llistesCompartides as $llista)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <strong id="nomLlista">{{ $llista->nom }}</strong>
                    <div class="llista-info">
                        Propietari: {{ $llista->creador->name }} · {{ $llista->productes->count() }} productes
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('llistes.editar', $llista->id_llista_compra) }}" class="btn btn-sm btn-outline-warning">
                        Veure
                    </a>
                    <form action="{{ route('llistes.sortir', $llista->id_llista_compra) }}" method="POST" onsubmit="return confirm('Estàs segur que vols sortir d\'aquesta llista compartida?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-orange">
                            Sortir
                        </button>
                    </form>
                </div>
            </li>
            @endforeach
        </ul>
        @else
        <div class="alert alert-info text-center">
            No tens cap llista compartida amb tu.
        </div>
        @endif
    </div>
</div>

<script>
    function mostrarSeccio(tipus) {
        // Amagar totes les seccions
        document.getElementById('seccio-meves').classList.remove('active');
        document.getElementById('seccio-compartides').classList.remove('active');

        // Treure l'estat actiu dels botons
        document.getElementById('btn-meves').classList.remove('active');
        document.getElementById('btn-compartides').classList.remove('active');

        // Mostrar la secció seleccionada
        if (tipus === 'meves') {
            document.getElementById('seccio-meves').classList.add('active');
            document.getElementById('btn-meves').classList.add('active');
        } else {
            document.getElementById('seccio-compartides').classList.add('active');
            document.getElementById('btn-compartides').classList.add('active');
        }
    }

    // Activar el botó "Les meves llistes" per defecte
    document.getElementById('btn-meves').classList.add('active');
</script>
@endsection