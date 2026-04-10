<?php

use App\Http\Controllers\AlumneController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EspaiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsuariEspaiController;
use App\Http\Controllers\NoticiaController;
use App\Http\Controllers\NoticiaReaccioController;
use App\Http\Controllers\AulaController;
use App\Http\Controllers\AulaTicketController;
use App\Http\Controllers\AulaAdminController;
use App\Http\Controllers\FranjaHorariaController;
use App\Http\Controllers\GuardiaController;
use App\Http\Controllers\EspaiShareController;
use App\Http\Controllers\GrupController;
use App\Http\Controllers\AulaHorarioController;
use App\Http\Controllers\BaseRoleController;
use App\Http\Controllers\PermissionController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('espais.index');
    }

    return view('presentacion.landing');
});

Route::get('/dashboard', function () {
    return redirect()->route('espais.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/funcionalitats', fn() => view('presentacion.funcionalitats'))->name('funcionalitats');
Route::get('/plans', fn() => view('presentacion.plans'))->name('plans');
Route::get('/comFunciona', fn() => view('presentacion.comFunciona'))->name('comFunciona');
Route::get('/faq', fn() => view('presentacion.faq'))->name('faq');
Route::get('/sobreNosotros', fn() => view('presentacion.sobreNosotros'))->name('sobreNosotros');
Route::get('/contacte', fn() => view('presentacion.contacte'))->name('contacte');
Route::get('/blog', fn() => view('presentacion.blog'))->name('blog');
Route::get('/suport', fn() => view('presentacion.suport'))->name('suport');

Route::middleware('auth')->group(function () {

    // espais
    Route::get('/espais', [EspaiController::class, 'index'])->name('espais.index');
    Route::get('/espais/create', [EspaiController::class, 'create'])->name('espais.create');
    Route::post('/espais', [EspaiController::class, 'store'])->name('espais.store');
    Route::get('/espais/{espai}', [EspaiController::class, 'show'])->name('espais.show');
    Route::get('/espais/{espai}/edit', [EspaiController::class, 'edit'])->name('espais.edit');
    Route::put('/espais/{espai}', [EspaiController::class, 'update'])->name('espais.update');
    Route::patch('/espais/{espai}', [EspaiController::class, 'update'])->name('espais.update');
    Route::delete('/espais/{espai}', [EspaiController::class, 'destroy'])->name('espais.destroy');

    // compartir espai
    Route::post('/espais/{espai}/compartir', [EspaiShareController::class, 'store'])
        ->name('espais.compartir');

    // entrar al espai
    Route::get('/espais/{espai}/entrar', [EspaiController::class, 'entrarForm'])->name('espais.entrar.form');
    Route::post('/espais/{espai}/entrar', [EspaiController::class, 'entrar'])->name('espais.entrar');

    // PROFILE
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// requireix iniciar sessio dins l'espai
Route::middleware('espai.session')->group(function () {

    Route::get('/espai', fn() => view('espai.index'))->name('espai.index');

    // usuaris
    Route::get('/espai/usuaris', [UsuariEspaiController::class, 'index'])->name('espai.usuaris.index');
    Route::get('/espai/usuaris/create', [UsuariEspaiController::class, 'create'])->name('espai.usuaris.create');
    Route::post('/espai/usuaris', [UsuariEspaiController::class, 'store'])->name('espai.usuaris.store');

    Route::get('/espai/usuaris/{usuariEspai}/edit', [UsuariEspaiController::class, 'edit'])->name('espai.usuaris.edit');
    Route::put('/espai/usuaris/{usuariEspai}', [UsuariEspaiController::class, 'update'])->name('espai.usuaris.update');
    Route::delete('/espai/usuaris/{usuariEspai}', [UsuariEspaiController::class, 'destroy'])->name('espai.usuaris.destroy');

    // noticies
    Route::resource('/espai/noticies', NoticiaController::class)
        ->parameters(['noticies' => 'noticia'])
        ->names('espai.noticies');

    // reaccionar
    Route::post('/espai/noticies/{noticia}/reaccio', [NoticiaReaccioController::class, 'store'])
        ->name('espai.noticies.reaccio');

    Route::delete('/espai/noticies/{noticia}/reaccio', [NoticiaReaccioController::class, 'destroy'])
        ->name('espai.noticies.reaccio.destroy');

    // aules
    Route::resource('/espai/aules', AulaController::class)
        ->parameters(['aules' => 'aula'])
        ->names('espai.aules');

    Route::get('/espai/aules/{aula}/admin', [AulaAdminController::class, 'show'])
        ->name('espai.aules.admin');

    Route::post('/espai/aules/{aula}/admin', [AulaAdminController::class, 'update'])
        ->name('espai.aules.admin.update');

    // guardar horario con grupos
    Route::post('/espai/aules/{aula}/horari', [AulaHorarioController::class, 'update'])
        ->name('espai.aules.horari.update');

    // franjas
    Route::resource('/espai/franges', FranjaHorariaController::class)
        ->parameters(['franges' => 'franja'])
        ->names('espai.franges');

    // Tickets
    Route::post('/espai/aules/{aula}/tickets', [AulaTicketController::class, 'store'])
        ->name('espai.aules.tickets.store');

    Route::patch('/espai/aules/{aula}/tickets/{ticket}', [AulaTicketController::class, 'update'])
        ->name('espai.aules.tickets.update');

    Route::delete('/espai/aules/{aula}/tickets/{ticket}', [AulaTicketController::class, 'destroy'])
        ->name('espai.aules.tickets.destroy');

    // Guardies
    Route::get('/espai/guardies', [GuardiaController::class, 'index'])
        ->name('espai.guardies.index');

    Route::get('/espai/guardies/solicitar', [GuardiaController::class, 'solicitaGuardia'])
        ->name('espai.guardia.solicitaGuardia');

    Route::post('/espai/guardies/solicitar', [GuardiaController::class, 'guardarSolicitud'])
        ->name('espai.guardia.solicitaGuardia.post');

    // Acceptar guardia
    Route::post('/espai/guardies/{solicitud}/acceptar', [GuardiaController::class, 'acceptar'])
        ->name('espai.guardies.acceptar');

    // alumnes
    Route::get('/espai/alumnes', [AlumneController::class, 'index'])->name('espai.alumnes.index');
    Route::get('/espai/alumnes/create', [AlumneController::class, 'create'])->name('espai.alumnes.create');
    Route::post('/espai/alumnes', [AlumneController::class, 'store'])->name('espai.alumnes.store');
    Route::delete('/espai/alumnes/{alumne}', [AlumneController::class, 'destroy'])->name('espai.alumnes.destroy');
    Route::get('/espai/alumnes/{alumne}/edit', [AlumneController::class, 'edit'])->name('espai.alumnes.edit');
    Route::put('/espai/alumnes/{alumne}', [AlumneController::class, 'update'])->name('espai.alumnes.update');
    Route::get('/espai/alumnes/{alumne}/info', [AlumneController::class, 'info'])
        ->name('espai.alumnes.info');

    // alumnes importar y exportar
    Route::get('/espai/alumnes/import', [AlumneController::class, 'importForm'])
        ->name('espai.alumnes.import.form');

    Route::post('/espai/alumnes/import', [AlumneController::class, 'import'])
        ->name('espai.alumnes.import');

    Route::get('/espai/alumnes/export', [AlumneController::class, 'export'])
        ->name('espai.alumnes.export');

    // grups
    Route::get('/espai/grups', [GrupController::class, 'index'])->name('espai.grups.index');
    Route::get('/espai/grups/create', [GrupController::class, 'create'])->name('espai.grups.create');
    Route::post('/espai/grups', [GrupController::class, 'store'])->name('espai.grups.store');
    Route::get('/espai/grups/{grup}/edit', [GrupController::class, 'edit'])->name('espai.grups.edit');
    Route::put('/espai/grups/{grup}', [GrupController::class, 'update'])->name('espai.grups.update');
    Route::delete('/espai/grups/{grup}', [GrupController::class, 'destroy'])->name('espai.grups.destroy');
    Route::get('/grups/{grup}/veure', [GrupController::class, 'veure'])->name('espai.grups.veure');

    Route::get('/espai/aules/{aula}/tickets', [AulaTicketController::class, 'index'])->name('espai.aules.tickets.index');

    // Mostrar todos los tickets abiertos
    Route::get('/espai/tickets', [AulaTicketController::class, 'allOpen'])
        ->name('espai.tickets.index');

    // Asignación de roles a usuarios
    Route::get('/espai/usuaris/{usuariEspai}/roles', [UsuariEspaiController::class, 'assignRolesForm'])
        ->name('espai.usuaris.roles');

    Route::post('/espai/usuaris/{usuariEspai}/roles', [UsuariEspaiController::class, 'assignRoles'])
        ->name('espai.usuaris.roles.store');

    // Roles del espai (UNIFICADO)
    Route::prefix('espai/roles')->group(function () {
        Route::get('/', [BaseRoleController::class, 'index'])->name('espai.roles.index');
        Route::get('/create', [BaseRoleController::class, 'create'])->name('espai.roles.create');
        Route::post('/', [BaseRoleController::class, 'store'])->name('espai.roles.store');
        Route::get('/{role}/edit', [BaseRoleController::class, 'edit'])->name('espai.roles.edit');
        Route::put('/{role}', [BaseRoleController::class, 'update'])->name('espai.roles.update');
        Route::delete('/{role}', [BaseRoleController::class, 'destroy'])->name('espai.roles.destroy');
    });

    // permissos del espai
    Route::prefix('espai/permissions')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('espai.permissions.index');
        Route::get('/create', [PermissionController::class, 'create'])->name('espai.permissions.create');
        Route::post('/', [PermissionController::class, 'store'])->name('espai.permissions.store');
        Route::get('/{permission}/edit', [PermissionController::class, 'edit'])->name('espai.permissions.edit');
        Route::put('/{permission}', [PermissionController::class, 'update'])->name('espai.permissions.update');
        Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('espai.permissions.destroy');
    });

});

require __DIR__ . '/auth.php';