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

/*
|--------------------------------------------------------------------------
| LANDING & PÚBLICO
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| ESPAIS (fuera del espai)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

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

/*
|--------------------------------------------------------------------------
| ESPAI (requiere sesión dentro del espai)
|--------------------------------------------------------------------------
*/

Route::middleware('espai.session')->group(function () {

    Route::get('/espai', fn() => view('espai.index'))->name('espai.index');

    /*
    |--------------------------------------------------------------------------
    | USUARIS
    |--------------------------------------------------------------------------
    */
    Route::get('/espai/usuaris', [UsuariEspaiController::class, 'index'])
        ->name('espai.usuaris.index')
        ->middleware('canEspai:users.view');

    Route::get('/espai/usuaris/create', [UsuariEspaiController::class, 'create'])
        ->name('espai.usuaris.create')
        ->middleware('canEspai:users.create');

    Route::post('/espai/usuaris', [UsuariEspaiController::class, 'store'])
        ->name('espai.usuaris.store')
        ->middleware('canEspai:users.create');

    Route::get('/espai/usuaris/{usuariEspai}/edit', [UsuariEspaiController::class, 'edit'])
        ->name('espai.usuaris.edit')
        ->middleware('canEspai:users.update');

    Route::put('/espai/usuaris/{usuariEspai}', [UsuariEspaiController::class, 'update'])
        ->name('espai.usuaris.update')
        ->middleware('canEspai:users.update');

    Route::delete('/espai/usuaris/{usuariEspai}', [UsuariEspaiController::class, 'destroy'])
        ->name('espai.usuaris.destroy')
        ->middleware('canEspai:users.delete');


    /*
    |--------------------------------------------------------------------------
    | NOTÍCIES
    |--------------------------------------------------------------------------
    */
    Route::resource('/espai/noticies', NoticiaController::class)
        ->parameters(['noticies' => 'noticia'])
        ->names('espai.noticies')
        ->middleware('canEspai:noticies.view');

    Route::post('/espai/noticies/{noticia}/reaccio', [NoticiaReaccioController::class, 'store'])
        ->name('espai.noticies.reaccio')
        ->middleware('canEspai:noticies.reaccionar');

    Route::delete('/espai/noticies/{noticia}/reaccio', [NoticiaReaccioController::class, 'destroy'])
        ->name('espai.noticies.reaccio.destroy')
        ->middleware('canEspai:noticies.reaccionar');


    /*
    |--------------------------------------------------------------------------
    | AULES
    |--------------------------------------------------------------------------
    */
    Route::resource('/espai/aules', AulaController::class)
        ->parameters(['aules' => 'aula'])
        ->names('espai.aules')
        ->middleware('canEspai:aulas.view');

    Route::get('/espai/aules/{aula}/admin', [AulaAdminController::class, 'show'])
        ->name('espai.aules.admin')
        ->middleware('canEspai:aulas.manage');

    Route::post('/espai/aules/{aula}/admin', [AulaAdminController::class, 'update'])
        ->name('espai.aules.admin.update')
        ->middleware('canEspai:aulas.manage');

    Route::post('/espai/aules/{aula}/horari', [AulaHorarioController::class, 'update'])
        ->name('espai.aules.horari.update')
        ->middleware('canEspai:aulas.horari.update');


    /*
    |--------------------------------------------------------------------------
    | ALUMNES
    |--------------------------------------------------------------------------
    */
    Route::get('/espai/alumnes', [AlumneController::class, 'index'])
        ->name('espai.alumnes.index')
        ->middleware('canEspai:students.view');

    Route::get('/espai/alumnes/create', [AlumneController::class, 'create'])
        ->name('espai.alumnes.create')
        ->middleware('canEspai:students.create');

    Route::post('/espai/alumnes', [AlumneController::class, 'store'])
        ->name('espai.alumnes.store')
        ->middleware('canEspai:students.create');

    Route::delete('/espai/alumnes/{alumne}', [AlumneController::class, 'destroy'])
        ->name('espai.alumnes.destroy')
        ->middleware('canEspai:students.delete');

    Route::get('/espai/alumnes/{alumne}/edit', [AlumneController::class, 'edit'])
        ->name('espai.alumnes.edit')
        ->middleware('canEspai:students.update');

    Route::put('/espai/alumnes/{alumne}', [AlumneController::class, 'update'])
        ->name('espai.alumnes.update')
        ->middleware('canEspai:students.update');

    Route::get('/espai/alumnes/{alumne}/info', [AlumneController::class, 'info'])
        ->name('espai.alumnes.info')
        ->middleware('canEspai:students.view');

    Route::get('/espai/alumnes/import', [AlumneController::class, 'importForm'])
        ->name('espai.alumnes.import.form')
        ->middleware('canEspai:students.import');

    Route::post('/espai/alumnes/import', [AlumneController::class, 'import'])
        ->name('espai.alumnes.import')
        ->middleware('canEspai:students.import');

    Route::get('/espai/alumnes/export', [AlumneController::class, 'export'])
        ->name('espai.alumnes.export')
        ->middleware('canEspai:students.export');


    /*
    |--------------------------------------------------------------------------
    | GRUPS
    |--------------------------------------------------------------------------
    */
    Route::get('/espai/grups', [GrupController::class, 'index'])
        ->name('espai.grups.index')
        ->middleware('canEspai:groups.view');

    Route::get('/espai/grups/create', [GrupController::class, 'create'])
        ->name('espai.grups.create')
        ->middleware('canEspai:groups.create');

    Route::post('/espai/grups', [GrupController::class, 'store'])
        ->name('espai.grups.store')
        ->middleware('canEspai:groups.create');

    Route::get('/espai/grups/{grup}/edit', [GrupController::class, 'edit'])
        ->name('espai.grups.edit')
        ->middleware('canEspai:groups.update');

    Route::put('/espai/grups/{grup}', [GrupController::class, 'update'])
        ->name('espai.grups.update')
        ->middleware('canEspai:groups.update');

    Route::delete('/espai/grups/{grup}', [GrupController::class, 'destroy'])
        ->name('espai.grups.destroy')
        ->middleware('canEspai:groups.delete');

    Route::get('/espai/grups/{grup}/veure', [GrupController::class, 'veure'])
        ->name('espai.grups.veure')
        ->middleware('canEspai:groups.view');


    /*
    |--------------------------------------------------------------------------
    | GUARDIES
    |--------------------------------------------------------------------------
    */
    Route::get('/espai/guardies', [GuardiaController::class, 'index'])
        ->name('espai.guardies.index')
        ->middleware('canEspai:guardies.view');

    Route::get('/espai/guardia/solicita', [GuardiaController::class, 'solicitaGuardia'])
        ->name('espai.guardia.solicitaGuardia')
        ->middleware('canEspai:guardies.create');

    Route::post('/espai/guardia/solicita', [GuardiaController::class, 'guardarSolicitud'])
        ->name('espai.guardia.guardarSolicitud')
        ->middleware('canEspai:guardies.create');

    Route::post('/espai/guardia/{solicitud}/acceptar', [GuardiaController::class, 'acceptar'])
        ->name('espai.guardia.acceptar')
        ->middleware('canEspai:guardies.manage');


    /*
    |--------------------------------------------------------------------------
    | FRANGES HORÀRIES
    |--------------------------------------------------------------------------
    */
    Route::prefix('espai/franges')->middleware('canEspai:aulas.view')->group(function () {
        Route::get('/',              [FranjaHorariaController::class, 'index'])->name('espai.franges.index');
        Route::get('/create',        [FranjaHorariaController::class, 'create'])->name('espai.franges.create')->middleware('canEspai:aulas.manage');
        Route::post('/',             [FranjaHorariaController::class, 'store'])->name('espai.franges.store')->middleware('canEspai:aulas.manage');
        Route::get('/{franja}/edit', [FranjaHorariaController::class, 'edit'])->name('espai.franges.edit')->middleware('canEspai:aulas.manage');
        Route::put('/{franja}',      [FranjaHorariaController::class, 'update'])->name('espai.franges.update')->middleware('canEspai:aulas.manage');
        Route::delete('/{franja}',   [FranjaHorariaController::class, 'destroy'])->name('espai.franges.destroy')->middleware('canEspai:aulas.manage');
    });


    /*
    |--------------------------------------------------------------------------
    | ASSIGNACIÓ DE ROLS A USUARIS
    |--------------------------------------------------------------------------
    */
    Route::get('/espai/usuaris/{usuariEspai}/roles', [UsuariEspaiController::class, 'assignRolesForm'])
        ->name('espai.usuaris.roles')
        ->middleware('canEspai:users.update');

    Route::post('/espai/usuaris/{usuariEspai}/roles', [UsuariEspaiController::class, 'assignRoles'])
        ->name('espai.usuaris.roles.update')
        ->middleware('canEspai:users.update');

    Route::post('/espai/usuaris/{usuariEspai}/roles', [UsuariEspaiController::class, 'assignRoles'])
        ->name('espai.usuaris.roles.store')
        ->middleware('canEspai:users.update');


    /*
    |--------------------------------------------------------------------------
    | TICKETS D'AULA
    |--------------------------------------------------------------------------
    */
    Route::get('/espai/tickets', [AulaTicketController::class, 'index'])
        ->name('espai.tickets.index')
        ->middleware('canEspai:tickets.view');

    Route::post('/espai/aules/{aula}/tickets', [AulaTicketController::class, 'store'])
        ->name('espai.aules.tickets.store')
        ->middleware('canEspai:tickets.create');

    Route::put('/espai/aules/{aula}/tickets/{ticket}', [AulaTicketController::class, 'update'])
        ->name('espai.aules.tickets.update')
        ->middleware('canEspai:tickets.update');

    Route::delete('/espai/aules/{aula}/tickets/{ticket}', [AulaTicketController::class, 'destroy'])
        ->name('espai.aules.tickets.destroy')
        ->middleware('canEspai:tickets.delete');


    /*
    |--------------------------------------------------------------------------
    | ROLES
    |--------------------------------------------------------------------------
    */
    Route::prefix('espai/roles')->middleware('canEspai:roles.view')->group(function () {
        Route::get('/', [BaseRoleController::class, 'index'])->name('espai.roles.index');
        Route::get('/create', [BaseRoleController::class, 'create'])->name('espai.roles.create')->middleware('canEspai:roles.create');
        Route::post('/', [BaseRoleController::class, 'store'])->name('espai.roles.store')->middleware('canEspai:roles.create');
        Route::get('/{role}/edit', [BaseRoleController::class, 'edit'])->name('espai.roles.edit')->middleware('canEspai:roles.update');
        Route::put('/{role}', [BaseRoleController::class, 'update'])->name('espai.roles.update')->middleware('canEspai:roles.update');
        Route::delete('/{role}', [BaseRoleController::class, 'destroy'])->name('espai.roles.destroy')->middleware('canEspai:roles.delete');
    });


    /*
    |--------------------------------------------------------------------------
    | PERMISSIONS
    |--------------------------------------------------------------------------
    */
    Route::prefix('espai/permissions')->middleware('canEspai:permissions.view')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('espai.permissions.index');
        Route::get('/create', [PermissionController::class, 'create'])->name('espai.permissions.create')->middleware('canEspai:permissions.create');
        Route::post('/', [PermissionController::class, 'store'])->name('espai.permissions.store')->middleware('canEspai:permissions.create');
        Route::get('/{permission}/edit', [PermissionController::class, 'edit'])->name('espai.permissions.edit')->middleware('canEspai:permissions.update');
        Route::put('/{permission}', [PermissionController::class, 'update'])->name('espai.permissions.update')->middleware('canEspai:permissions.update');
        Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('espai.permissions.destroy')->middleware('canEspai:permissions.delete');
    });

});

require __DIR__ . '/auth.php';