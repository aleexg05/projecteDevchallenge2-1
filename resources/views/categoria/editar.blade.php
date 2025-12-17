@extends('layouts.app')

@section('títol', 'Editar categoria')

@section('content')
<style>
    /* Títols */
    h1, h2, h3, h4, h5 {
        color: #ffffff;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }

    /* Text general */
    body, p, label, span, div {
        color: #ffffff;
    }

    /* Formularis */
    .form-control, input, textarea, select {
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

    .form-control:focus {
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
    <h1 class="mb-4 text-center">✏️ Editar categoria</h1>

    @if ($errors->any())
        <div class="alert alert-danger" style="background: rgba(220, 38, 38, 0.2); border: 1px solid rgba(220, 38, 38, 0.5); color: #ffffff; border-radius: 6px; padding: 15px; margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('categories.actualitzar', $categoria->id_categoria) }}" method="POST">
        @csrf
        @method('PUT')

        @if(!empty($categoria->id_llista_compra))
            <input type="hidden" name="id_llista_compra" value="{{ $categoria->id_llista_compra }}">
        @endif

        <div class="mb-3">
            <label for="nom_categoria" class="form-label">Nom de la categoria</label>
            <input type="text" name="nom_categoria" id="nom_categoria" class="form-control"
                   value="{{ old('nom_categoria', $categoria->nom_categoria) }}" required>
        </div>

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-outline-primary">✅ Desar canvis</button>
            <a href="{{ route('categories.index', $categoria->id_llista_compra) }}" class="btn btn-outline-secondary">❌ Cancel·lar</a>
        </div>
    </form>
</div>
@endsection