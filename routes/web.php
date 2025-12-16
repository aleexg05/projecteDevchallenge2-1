<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EtiquetaController;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProducteController;
use App\Http\Controllers\LlistaCompraController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});


// Google Auth
Route::get('/auth/google/redirect', function () {
    return Socialite::driver('google')->redirect();
})->name('google.redirect');

Route::get('/auth/google/callback', function () {
    try {
        $googleUser = Socialite::driver('google')->user();
    } catch (\Exception $e) {
        return redirect('/')->with('error', 'Error al autenticar con Google');
    }

    $user = User::updateOrCreate(
        ['email' => $googleUser->getEmail()],
        ['name' => $googleUser->getName()]
    );

    Auth::login($user, true);

    return redirect()->route('llistes.index');
})->name('google.callback');


// Categories generals
Route::get('/index', [CategoriaController::class, 'index'])->name('index');
Route::get('/categoria/create', [CategoriaController::class, 'create'])->name('categoria.create');
Route::post('/categoria', [CategoriaController::class, 'store'])->name('categoria.store');
Route::get('/categoria', [CategoriaController::class, 'index'])->name('categoria.index');
Route::get('/categoria/eliminarCategoria', [CategoriaController::class, 'eliminarCategoria'])->name('categoria.eliminarCategoria');
Route::delete('/categoria/{id_categoria}', [CategoriaController::class, 'eliminar'])->name('categoria.eliminar');
Route::get('/categoria/{id_categoria}/editar', [CategoriaController::class, 'editar'])->name('categoria.editar');
Route::put('/categoria/{id_categoria}', [CategoriaController::class, 'actualitzar'])->name('categoria.actualitzar');

// Productes fora de llistes (no recomanat, però mantinc per compatibilitat)
Route::get('/producte/create', [ProducteController::class, 'create'])->name('producte.create');
Route::post('/producte/{id_llista_compra}', [ProducteController::class, 'store'])->name('producte.store');
Route::delete('/producte/{id}', [ProducteController::class, 'destroy'])->name('producte.destroy');

Route::middleware(['auth'])->group(function () {
    // Llistes
    Route::get('/llistes', [LlistaCompraController::class, 'index'])->name('llistes.index');
    Route::get('/llistes/create', [LlistaCompraController::class, 'create'])->name('llistes.create');
    Route::post('/llistes', [LlistaCompraController::class, 'store'])->name('llistes.store');
    Route::get('/llistes/{id}/editar', [LlistaCompraController::class, 'editar'])->name('llistes.editar');
    Route::put('/llistes/{id}', [LlistaCompraController::class, 'actualitzar'])->name('llistes.actualitzar');
    Route::delete('/llistes/{id}', [LlistaCompraController::class, 'eliminar'])->name('llistes.eliminar');
    Route::get('/llistes/{id}/editarNom', [LlistaCompraController::class, 'editarNom'])->name('llistes.editarNom');

    // Etiquetes (amb suport per llistes opcionals)
    Route::get('/llistes/{id_llista}/etiquetas', [EtiquetaController::class, 'index'])->name('llistes.etiquetas.index');
    Route::get('/etiquetas', [EtiquetaController::class, 'index'])->name('etiquetas.index');
    Route::get('/etiquetas/create', [EtiquetaController::class, 'create'])->name('etiquetas.create');
    Route::post('/etiquetas', [EtiquetaController::class, 'store'])->name('etiquetas.store');
    Route::delete('/etiquetas/{id_etiqueta}', [EtiquetaController::class, 'destroy'])->name('etiquetas.destroy');

    // Categories dins d’una llista
    Route::get('/llistes/{id}/categories/create', [CategoriaController::class, 'create'])->name('categories.create');
    Route::post('/llistes/{id}/categories', [CategoriaController::class, 'store'])->name('categories.store');
    Route::get('/llistes/{id}/categories', [CategoriaController::class, 'index'])->name('categories.index');

    // Categories generals
    Route::post('/categories', [CategoriaController::class, 'store'])->name('categories.store');
    Route::get('/categories/{id}/editar', [CategoriaController::class, 'editar'])->name('categories.editar');
    Route::put('/categories/{id}', [CategoriaController::class, 'actualitzar'])->name('categories.actualitzar');
    Route::delete('/categories/{id}', [CategoriaController::class, 'eliminar'])->name('categories.eliminar');

    // Productes dins d’una llista
    Route::get('/llistes/{id}/productes', [ProducteController::class, 'index'])->name('productes.index');
    Route::get('/llistes/{id}/productes/create', [ProducteController::class, 'create'])->name('productes.create');
    Route::post('/llistes/{id}/productes', [ProducteController::class, 'store'])->name('productes.store');
    Route::get('/llistes/{id_llista}/productes/{id_producte}/edit', [ProducteController::class, 'edit'])->name('productes.edit');
    Route::put('/llistes/{id_llista}/productes/{id_producte}', [ProducteController::class, 'update'])->name('productes.update');
    Route::delete('/llistes/{id_llista}/productes/{id_producte}', [ProducteController::class, 'destroy'])->name('productes.destroy');

    // Toggle comprat
    Route::put('/llistes/{id_llista}/productes/{id_producte}/toggle', [ProducteController::class, 'toggle'])->name('productes.toggle');

    // Categories generals
    Route::get('/categories', [CategoriaController::class, 'index'])->name('categories.index');
    Route::delete('/categories/{id}', [CategoriaController::class, 'eliminar'])->name('categories.eliminar');
    Route::get('/etiquetas', [EtiquetaController::class, 'index'])->name('etiquetas.index');
    Route::get('/etiquetas/create', [EtiquetaController::class, 'create'])->name('etiquetas.create');
    Route::post('/etiquetas', [EtiquetaController::class, 'store'])->name('etiquetas.store');
    Route::delete('/etiquetas/{id_etiqueta}', [EtiquetaController::class, 'destroy'])->name('etiquetas.destroy');

Route::middleware(['auth'])->group(function () {
    // ... (les rutes existents)
    
    // Rutes per gestionar compartició
    Route::get('/llistes/{id}/compartir', [LlistaCompraController::class, 'mostrarCompartir'])
        ->name('llistes.compartir.mostrar');
    Route::post('/llistes/{id}/compartir', [LlistaCompraController::class, 'compartir'])
        ->name('llistes.compartir');
    Route::delete('/llistes/{id}/compartir/{userId}', [LlistaCompraController::class, 'deixarCompartir'])
        ->name('llistes.deixar-compartir');
    Route::put('/llistes/{id}/compartir/{userId}/rol', [LlistaCompraController::class, 'canviarRol'])
        ->name('llistes.canviar-rol');
    Route::delete('/llistes/{id}/sortir', [LlistaCompraController::class, 'sortir'])
        ->name('llistes.sortir');
});

});





// Dashboard
Route::get('/dashboard', function () {
    return redirect()->intended('/llistes');
})->middleware(['auth', 'verified'])->name('dashboard');

// Perfil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
