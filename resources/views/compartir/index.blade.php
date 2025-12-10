@extends('layouts.app')

@section('títol', 'Compartir llista')

@section('content')
<style>
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
    }

    .btn:hover {
        background-color: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea, #764ba2);
        border: none;
    }

    .btn-danger {
        border-color: #ef4444;
        color: #ef4444;
        background-color: rgba(239, 68, 68, 0.1);
    }

    .btn-danger:hover {
        background-color: #ef4444;
        color: #fff;
    }

    .btn-secondary {
        border-color: #6b7280;
        color: #d1d5db;
        background-color: rgba(107, 114, 128, 0.1);
    }

    .btn-secondary:hover {
        background-color: #6b7280;
        color: #fff;
    }

    .card {
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        margin-bottom: 24px;
        padding: 24px;
    }

    .alert {
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        padding: 12px 16px;
        margin-bottom: 20px;
    }

    .alert-success {
        background: rgba(34, 197, 94, 0.2);
        border-color: #22c55e;
        color: #86efac;
    }

    .alert-danger {
        background: rgba(239, 68, 68, 0.2);
        border-color: #ef4444;
        color: #fca5a5;
    }

    h1,
    h2,
    h3 {
        color: #ffffff;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        color: #d1d5db;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .form-select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 6px;
        background-color: rgba(255, 255, 255, 0.1);
        color: #fff;
        font-size: 15px;
    }

    .form-select option {
        background-color: #1a1a2e;
        color: #fff;
    }

    .user-card {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        padding: 16px;
        background: rgba(255, 255, 255, 0.05);
        margin-bottom: 12px;
        transition: all 0.3s ease;
    }

    .user-card:hover {
        background: rgba(255, 255, 255, 0.1);
        transform: translateX(5px);
    }

    .user-info h4 {
        margin: 0 0 4px 0;
        color: #fff;
        font-size: 16px;
    }

    .user-info p {
        margin: 0;
        color: #9ca3af;
        font-size: 14px;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
    }

    .flex-end {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }
</style>

<div class="container">
    <h1>Compartir: {{ $llista->nom }}</h1>

    <!-- Missatges -->
    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Formulari per compartir amb nous usuaris -->
    <div class="card">
        <h3>Compartir amb un usuari</h3>

        @if ($altresUsuaris->isEmpty())
        <p style="color: #9ca3af;">No hi ha més usuaris per compartir aquesta llista.</p>
        @else
        <form action="{{ route('llistes.compartir', $llista->id_llista_compra) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="user_id" class="form-label">Selecciona un usuari</label>
                <select name="user_id" id="user_id" required class="form-select">
                    <option value="">-- Selecciona un usuari --</option>
                    @foreach ($altresUsuaris as $usuari)
                    <option value="{{ $usuari->id }}">
                        {{ $usuari->name }} ({{ $usuari->email }})
                    </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Compartir</button>
        </form>
        @endif
    </div>

    <!-- Llista d'usuaris amb qui ja s'ha compartit -->
    <div class="card">
        <h3>Usuaris amb accés ({{ $usuarisCompartits->count() }})</h3>

        @if ($usuarisCompartits->isEmpty())
        <p style="color: #9ca3af;">Aquesta llista no està compartida amb cap usuari.</p>
        @else
        @foreach ($usuarisCompartits as $usuari)
        <div class="user-card">
            <div class="user-info">
                <h4>{{ $usuari->name }}</h4>
                <p>{{ $usuari->email }}</p>
            </div>
            <form action="{{ route('llistes.deixar-compartir', [$llista->id_llista_compra, $usuari->id]) }}"
                method="POST"
                onsubmit="return confirm('Estàs segur que vols deixar de compartir amb aquest usuari?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    Eliminar Accés
                </button>
            </form>
        </div>
        @endforeach
        @endif
    </div>

    <!-- Botó per tornar -->
    <div>
        <a href="{{ route('llistes.index') }}" class="btn btn-secondary">
            Tornar a les llistes
        </a>
    </div>
</div>
@endsection