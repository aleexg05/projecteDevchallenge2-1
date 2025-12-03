@extends('layouts.app')

@section('títol', 'Crear etiqueta')

@section('content')
<style>
/* Botons generals */
.btn {
    padding: 12px 20px;
    border-radius: 6px;
    font-size: 15px;
    text-decoration: none;
    border: 1px solid rgba(255, 255, 255, 0.3);
    background-color: rgba(255, 255, 255, 0.1);
    color: #fff; /* 👈 text en blanc */
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
    color: #fff; /* 👈 text en blanc */
    background-color: rgba(167, 139, 250, 0.1);
}
.btn-outline-primary:hover {
    background-color: #a78bfa;
    color: #1a0b2e;
}

.btn-outline-secondary {
    border-color: #60a5fa;
    color: #fff; /* 👈 text en blanc */
    background-color: rgba(96, 165, 250, 0.1);
}
.btn-outline-secondary:hover {
    background-color: #60a5fa;
    color: #0f3460;
}

h1 {
    color: #ffffff; /* 👈 text en blanc */
    text-align: center;
    margin-bottom: 30px;
    font-weight: 600;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

label {
    color: #ffffff; /* 👈 text en blanc */
    font-weight: 500;
}

input.form-control {
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.3);
    color: #fff; /* 👈 text en blanc */
}

input.form-control::placeholder {
    color: rgba(255,255,255,0.6);
}

.container {
    max-width: 800px;
    margin: 0 auto;
}
</style>
<div class="container py-4">
    <h1 class="mb-4 text-center">➕ Crear etiqueta</h1>

    <form action="{{ route('etiquetas.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="etiqueta_producte" class="form-label">Nom de l'etiqueta</label>
            <input type="text" name="etiqueta_producte" id="etiqueta_producte" class="form-control" required>
        </div>

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-outline-primary">✅ Crear etiqueta</button>
            <a href="{{ route('etiquetas.index') }}" class="btn btn-outline-secondary">❌ Cancel·lar</a>
        </div>
    </form>
</div>
@endsection
