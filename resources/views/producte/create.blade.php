@extends('layouts.app')

@section('títol', 'Crear producte')

@section('content')
<style>
    /* Títols */
    h1, h2, h3, h4, h5 {
        color: #ffffff;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }

    /* Text general */
    body, p, label, span, div, strong {
        color: #ffffff;
    }

    /* Formularis */
    .form-control, .form-select, input, textarea, select {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: #ffffff;
        backdrop-filter: blur(10px);
        padding: 10px 15px;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.5);
    }

    .form-control:focus, .form-select:focus {
        background: rgba(255, 255, 255, 0.15);
        border-color: #a78bfa;
        color: #ffffff;
        box-shadow: 0 0 0 0.2rem rgba(167, 139, 250, 0.25);
        outline: none;
    }

    .form-label {
        color: #ffffff;
        font-weight: 500;
        margin-bottom: 8px;
    }

    .form-text {
        color: rgba(255, 255, 255, 0.7);
    }

    /* Select options */
  select option {
    background-color: #2d2d2d; /* gris oscuro */
    color: #ffffff;           /* texto blanco */
}


    /* Botons */
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
        margin-right: 10px;
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

    .container {
        max-width: 600px;
        margin: 0 auto;
    }
</style>
<div class="container py-4">
    <h1 class="mb-4 text-center">➕ Crear producte</h1>

    <form action="{{ route('productes.store', $llista->id_llista_compra) }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="nom_producte" class="form-label">Nom del producte</label>
            <input type="text" name="nom_producte" id="nom_producte" class="form-control" placeholder="Escriu el nom del producte..." required>
        </div>

        <div class="mb-3">
            <label for="id_categoria" class="form-label">Categoria</label>
            <select name="id_categoria" id="id_categoria" class="form-select" required>
                @foreach($categories as $categoria)
                    <option value="{{ $categoria->id_categoria }}">{{ $categoria->nom_categoria }}</option>
                @endforeach
            </select>
        </div>

        <input type="hidden" name="id_llista_compra" value="{{ $llista->id_llista_compra }}">
        <p class="form-text">Afegint producte a la llista: <strong>{{ $llista->nom }}</strong></p>

       <select name="etiqueta_producte" id="etiqueta_producte" class="form-select">
    <option value="">Sense etiqueta</option>
    @foreach($etiquetas as $etiqueta)
        <option value="{{ $etiqueta->etiqueta_producte }}">{{ $etiqueta->etiqueta_producte }}</option>
    @endforeach
</select>


        <div class="text-center mt-4">
            <button type="submit" class="btn btn-outline-primary">✅ Crear producte</button>
            <a href="{{ route('llistes.editar', $llista->id_llista_compra) }}" class="btn btn-outline-secondary">❌ Cancel·lar</a>
        </div>
    </form>
</div>
@endsection