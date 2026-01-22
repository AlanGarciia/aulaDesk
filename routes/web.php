<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EspaiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsuariEspaiController;

// ✅ Notícies
use App\Http\Controllers\NoticiaController;
use App\Http\Controllers\NoticiaReaccioController;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('espais.index');
    }

    return view('landing');
});

Route::get('/dashboard', function () {
    return redirect()->route('espais.index');
})->middleware(['auth', 'verified'])->name('dashboard');


//Aixo s'eliminará
Route::get('/espais/{espai}/acces', [EspaiController::class, 'accesForm'])->name('espais.acces.form');
Route::post('/espais/{espai}/acces', [EspaiController::class, 'acces'])->name('espais.acces');

Route::middleware('auth')->group(function () {

    //espais
    Route::get('/espais', [EspaiController::class, 'index'])->name('espais.index');
    Route::get('/espais/create', [EspaiController::class, 'create'])->name('espais.create');
    Route::post('/espais', [EspaiController::class, 'store'])->name('espais.store');
    Route::get('/espais/{espai}', [EspaiController::class, 'show'])->name('espais.show');
    Route::get('/espais/{espai}/edit', [EspaiController::class, 'edit'])->name('espais.edit');
    Route::put('/espais/{espai}', [EspaiController::class, 'update'])->name('espais.update');
    Route::patch('/espais/{espai}', [EspaiController::class, 'update'])->name('espais.update');
    Route::delete('/espais/{espai}', [EspaiController::class, 'destroy'])->name('espais.destroy');

    //entrar al espai demana inici de sessio
    Route::get('/espais/{espai}/entrar', [EspaiController::class, 'entrarForm'])->name('espais.entrar.form');
    Route::post('/espais/{espai}/entrar', [EspaiController::class, 'entrar'])->name('espais.entrar');

    // PROFILE
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


//requireix iniciar sessio dins l'espai
Route::middleware('espai.session')->group(function () {
    Route::get('/espai', function () {
        return view('espai.index');
    })->name('espai.index');

    // usuaris
    Route::get('/espai/usuaris', [UsuariEspaiController::class, 'index'])->name('espai.usuaris.index');
    Route::get('/espai/usuaris/create', [UsuariEspaiController::class, 'create'])->name('espai.usuaris.create');
    Route::post('/espai/usuaris', [UsuariEspaiController::class, 'store'])->name('espai.usuaris.store');

    //editar y actualizar usuaris
    Route::get('/espai/usuaris/{usuariEspai}/edit', [UsuariEspaiController::class, 'edit'])->name('espai.usuaris.edit');
    Route::put('/espai/usuaris/{usuariEspai}', [UsuariEspaiController::class, 'update'])->name('espai.usuaris.update');

    Route::delete('/espai/usuaris/{usuariEspai}', [UsuariEspaiController::class, 'destroy'])->name('espai.usuaris.destroy');

    // ✅ TAULÓ DE NOTÍCIES (CRUD)
    // 🔥 FIX: el resource crea {noticies} por defecto, lo cambiamos a {noticia} para que el controller funcione perfecto
    Route::resource('/espai/noticies', NoticiaController::class)
        ->parameters(['noticies' => 'noticia'])
        ->names('espai.noticies');

    // ✅ REACCIONS (afegir / canviar / treure)
    Route::post('/espai/noticies/{noticia}/reaccio', [NoticiaReaccioController::class, 'store'])
        ->name('espai.noticies.reaccio');

    Route::delete('/espai/noticies/{noticia}/reaccio', [NoticiaReaccioController::class, 'destroy'])
        ->name('espai.noticies.reaccio.destroy');
});

require __DIR__.'/auth.php';