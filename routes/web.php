<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\TramiteController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\SocialLinkController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdvisoryRequestController;
use App\Http\Controllers\PublicPropertyController;
use App\Http\Controllers\AppointmentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==========================================
// 0. AUTENTICACIÓN (LOGIN Y RECUPERACIÓN)
// ==========================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/recuperar-clave', [AuthController::class, 'recoverPassword'])->name('password.recover');


// ==========================================
// 1. RUTAS PÚBLICAS (SIN AUTH)
// ==========================================
Route::get('/', [PublicController::class, 'index']);
Route::get('/conocenos', [PublicController::class, 'conocenos'])->name('conocenos');
Route::get('/unete', function () { return view('public-pages.unete'); })->name('unete.index');
Route::get('/contacto', [PublicController::class, 'contact'])->name('contacto');

// Asesorías públicas
Route::get('/asesorias', [PublicController::class, 'asesorias'])->name('asesorias.index');

// Formularios públicos
Route::post('/contacto', [PublicController::class, 'storeContact'])->name('contact.store');
Route::post('/asesorias/guardar', [AdvisoryRequestController::class, 'store'])->name('asesorias.store');
Route::post('/trabaja-con-nosotros', [JobApplicationController::class, 'store'])->name('postulaciones.store');

// Trámites y Catálogo públicos
Route::get('/tramites', [TramiteController::class, 'indexPublic'])->name('tramites.public.index');
Route::post('/tramites', [TramiteController::class, 'storePublic'])->name('tramites.public.store');
Route::get('/catalogo', [PublicPropertyController::class, 'index'])->name('public.catalogo.index');
Route::get('/catalogo/{property}', [PublicPropertyController::class, 'show'])->name('public.catalogo.show');

// Envío de mensajes y reserva de citas públicas
Route::get('/contacto/enviar-mensaje', function () {
    return redirect()->route('public.catalogo.index');
});

Route::post('/contacto/enviar-mensaje', [PublicPropertyController::class, 'sendPublicMessage'])->name('public.messages.send');
Route::post('/propiedad/agendar-cita', [PublicPropertyController::class, 'storeAppointment'])->name('public.appointments.store');
Route::post('/citas/confirmar', [PublicPropertyController::class, 'confirmAppointment'])->name('public.appointments.confirm');

// ==========================================
// 2. INTRANET / PANEL DE ADMINISTRACIÓN (REQUIEREN AUTH)
// ==========================================
Route::middleware(['auth'])->group(function () {
    
    // Cierre de sesión
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Resumen General
    Route::get('/intranet/resumen', function () {
        return view('intranet.users.resumen');
    })->name('admin.resumen');

    // Recursos Principales
    Route::resource('intranet/clients', ClientController::class);
    Route::resource('intranet/properties', PropertyController::class);
    Route::resource('intranet/tramites', TramiteController::class);
    Route::resource('intranet/schedules', ScheduleController::class);
    Route::resource('intranet/social-links', SocialLinkController::class);
    Route::resource('intranet/users', UserController::class);

    // Postulaciones
    Route::get('/intranet/postulaciones', [JobApplicationController::class, 'indexIntranet'])->name('intranet.applications.index');
    Route::post('/intranet/postulaciones/{application}/contratar', [JobApplicationController::class, 'contratar'])->name('intranet.applications.contratar');
    Route::delete('/intranet/postulaciones/{application}', [JobApplicationController::class, 'destroy'])->name('intranet.applications.destroy');

    // ------------------------------------------
    // MÓDULO DE AGENDA Y CITAS INTEGRALES
    // ------------------------------------------
    // Agenda personal del usuario
    Route::get('/admin/agenda', [AppointmentController::class, 'index'])->name('admin.agenda');
    Route::post('/admin/agenda', [AppointmentController::class, 'storeManual'])->name('admin.agenda.store');
    
    // Vistas Principales de Gestión
    Route::get('/admin/gestion-citas', [AppointmentController::class, 'gestionCitas'])->name('gestion.citas');
    Route::get('/admin/citas-integrales', [AppointmentController::class, 'integrales'])->name('admin.citas-totales'); 
    
    // Guardar Citas Manuales vs Registros Integrales
    Route::get('/admin/citas/crear', [AppointmentController::class, 'create'])->name('admin.citas.create');
    Route::post('/admin/gestion-citas', [AppointmentController::class, 'storeManual'])->name('gestion.citas.store');
    Route::post('/admin/citas-integrales', [AppointmentController::class, 'storeIntegral'])->name('admin.citas.storeIntegral');
    Route::post('/admin/integrales/store', [AppointmentController::class, 'storeIntegral'])->name('admin.integrales.store');

    // Acciones sobre Citas e Integrales
    Route::get('/admin/citas/{id}/edit', [AppointmentController::class, 'edit'])->name('admin.citas.edit');
    Route::get('/admin/citas/{id}/exportar', [AppointmentController::class, 'exportar'])->name('admin.citas.exportar');
    Route::patch('/admin/citas/{id}/gestionar', [AppointmentController::class, 'gestionar'])->name('admin.citas.gestionar');
    Route::put('/admin/citas/{id}', [AppointmentController::class, 'updateIntegral'])->name('admin.citas.update');
    Route::match(['put', 'patch'], '/intranet/citas/{id}/estado', [AppointmentController::class, 'cambiarEstado'])->name('citas.estado');
    
    // Eliminación (Reciclaje / Soft Delete)
    Route::delete('/admin/citas/{id}', [AppointmentController::class, 'destroyIntegral'])->name('gestion.citas.destroy');
    Route::delete('/admin/integrales/{id}', [AppointmentController::class, 'destroyIntegral'])->name('admin.integrales.destroy');
    
    // Papelera, Restauración y Eliminación Definitiva
    Route::patch('/admin/citas/{id}/restaurar', [AppointmentController::class, 'restaurar'])->name('admin.citas.restaurar');
    Route::patch('/admin/integrales/{id}/restaurar', [AppointmentController::class, 'restaurar'])->name('admin.integrales.restaurar');
    
    Route::delete('/admin/citas/{id}/forzar-eliminar', [AppointmentController::class, 'forzarEliminar'])->name('admin.citas.forzar-eliminar');
    Route::delete('/admin/integrales/{id}/forzar-eliminar', [AppointmentController::class, 'forzarEliminar'])->name('admin.integrales.forzarEliminar');

    // ------------------------------------------
    // MÓDULO DE CONTABILIDAD
    // ------------------------------------------
    // Redirección de seguridad si intentan entrar por GET a los métodos POST de contabilidad
    Route::get('/intranet/accounting/expense', function () { return redirect()->route('accounting.index'); });
    Route::get('/intranet/accounting/sale', function () { return redirect()->route('accounting.index'); });

    Route::get('/intranet/accounting', [AccountingController::class, 'index'])->name('accounting.index');
    Route::get('/intranet/accounting/expense/create', [AccountingController::class, 'createExpense'])->name('accounting.expense.create');
    Route::post('/intranet/accounting/expense', [AccountingController::class, 'storeExpense'])->name('accounting.expense.store');
    Route::get('/intranet/accounting/sale/create', [AccountingController::class, 'createSale'])->name('accounting.sale.create');
    Route::post('/intranet/accounting/sale', [AccountingController::class, 'storeSale'])->name('accounting.sale.store');

});