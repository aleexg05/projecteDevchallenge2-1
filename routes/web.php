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
Route::get('/etiquetas', [EtiquetaController::class, 'index'])->name('etiquetas.index');
Route::get('/llistes/{id_llista}/etiquetas', [EtiquetaController::class, 'index'])
    ->name('etiquetas.index');
    
    Route::get('etiquetas', [EtiquetaController::class, 'index'])->name('etiquetas.index');
Route::get('etiquetas/create', [EtiquetaController::class, 'create'])->name('etiquetas.create');
Route::post('etiquetas', [EtiquetaController::class, 'store'])->name('etiquetas.store');
Route::delete('etiquetas/{id_etiqueta}', [EtiquetaController::class, 'destroy'])->name('etiquetas.destroy');


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


});

// Google Auth
Route::get('/google-auth/redirect', function () {
    return Socialite::driver('google')->redirect();
});
Route::get('/google-auth/callback', function () {
    $user_google = Socialite::driver('google')->user();

    $user = User::updateOrCreate([
        'email' => $user_google->getEmail(),
    ], [
        'name' => $user_google->getName(),
        'email' => $user_google->getEmail(),
    ]);

    Auth::login($user, true);

    return redirect()->route('llistes.index');
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
