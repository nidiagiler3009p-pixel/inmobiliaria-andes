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


// ==========================================
// 0. RUTAS DE AUTENTICACIÓN (LOGIN Y RECUPERACIÓN)
// ==========================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/recuperar-clave', [AuthController::class, 'recoverPassword'])->name('password.recover');
// Ruta para registrar la cita desde la ficha de cliente/propiedad
Route::post('/intranet/clients/send-message-appointment', [ClientController::class, 'sendMessageAndCreateAppointment'])->name('intranet.clients.send-message-appointment');
// ==========================================
// 1. RUTAS PÚBLICAS
// ==========================================
Route::get('/', [PublicController::class, 'index']);
Route::get('/conocenos', function () { return view('public-pages.conocenos'); });
Route::get('/unete', function () { return view('public-pages.unete'); })->name('unete.index');
Route::get('/contacto', [PublicController::class, 'contact'])->name('contacto');

// Ruta pública de asesorías (vista principal)
Route::get('/asesorias', [PublicController::class, 'asesorias'])->name('asesorias.index');

// Formularios públicos
Route::post('/contacto', [PublicController::class, 'storeContact'])->name('contact.store');
Route::post('/asesorias/guardar', [AdvisoryRequestController::class, 'store'])->name('asesorias.store');
Route::post('/trabaja-con-nosotros', [JobApplicationController::class, 'store'])->name('postulaciones.store');

// Trámites públicos
Route::get('/tramites', [TramiteController::class, 'indexPublic'])->name('tramites.public.index');
Route::post('/tramites', [TramiteController::class, 'storePublic'])->name('tramites.public.store');
Route::get('/catalogo', [PublicPropertyController::class, 'index'])->name('public.catalogo.index');
Route::get('/catalogo/{id}', [PublicPropertyController::class, 'show'])->name('public.catalogo.show');

// ==========================================
// 2. RUTAS DE LA INTRANET (PRIVADAS - REQUIEREN LOGIN)
// ==========================================
Route::middleware(['auth'])->group(function () {
    
    // Ruta vital de cierre de sesión
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Ruta de Resumen General
    Route::get('/intranet/resumen', function () {
        return view('intranet.users.resumen');
    })->name('admin.resumen');

    // Recursos de la Intranet
    Route::resource('intranet/clients', ClientController::class);
    Route::resource('intranet/properties', PropertyController::class);
    Route::resource('intranet/tramites', TramiteController::class);
    Route::resource('intranet/schedules', ScheduleController::class);
    Route::resource('intranet/social-links', SocialLinkController::class);
    Route::resource('intranet/users', UserController::class);

    // Módulo de Postulaciones en la Intranet
    Route::get('intranet/postulaciones', [JobApplicationController::class, 'indexIntranet'])->name('intranet.applications.index');
    Route::post('intranet/postulaciones/{application}/contratar', [JobApplicationController::class, 'contratar'])->name('intranet.applications.contratar');
    Route::delete('intranet/postulaciones/{application}', [JobApplicationController::class, 'destroy'])->name('intranet.applications.destroy');


Route::get('/admin/agenda', function () {
    $appointments = \App\Models\AppointmentTracking::where('user_id', auth()->id())->latest()->get();
    return view('intranet.users.agenda', compact('appointments'));
})->name('admin.agenda');

// 2. Módulo de Gestión de Citas (Vista de administración general)
Route::get('/admin/gestion-citas', [AppointmentController::class, 'gestionCitas'])->name('gestion.citas');

// 3. Acciones CRUD para Citas (Guardar, Actualizar y Eliminar)
Route::post('/admin/citas/store', [AppointmentController::class, 'store'])->name('gestion.citas.store');
Route::put('/admin/citas/{id}/update', [AppointmentController::class, 'update'])->name('admin.citas.updateIntegral');
Route::delete('/admin/citas/{id}', [AppointmentController::class, 'destroy'])->name('gestion.citas.destroy');
Route::get('/admin/citas-integrales', [AppointmentController::class, 'integrales'])->name('admin.citas-totales');   
Route::get('/admin/citas/crear', [AppointmentController::class, 'create'])->name('admin.citas.create');
Route::post('/admin/citas/guardar', [AppointmentController::class, 'storeManual'])->name('admin.citas.storeManual');

Route::get('intranet/accounting', [AccountingController::class, 'index'])->name('accounting.index');
    Route::get('intranet/accounting/expense/create', [AccountingController::class, 'createExpense'])->name('accounting.expense.create');
    Route::post('intranet/accounting/expense', [AccountingController::class, 'storeExpense'])->name('accounting.expense.store');
    Route::get('intranet/accounting/sale/create', [AccountingController::class, 'createSale'])->name('accounting.sale.create');
    Route::post('intranet/accounting/sale', [AccountingController::class, 'storeSale'])->name('accounting.sale.store');
    
    // ==========================================
// ACCIONES PARA LA BANDEJA DE CITAS INTEGRALES
// ==========================================
Route::patch('/admin/citas/{id}/gestionar', [AppointmentController::class, 'gestionar'])->name('admin.citas.gestionar');
Route::get('/admin/citas/{id}/edit', [AppointmentController::class, 'edit'])->name('admin.citas.edit');
Route::put('/admin/citas/{id}/update', [AppointmentController::class, 'updateIntegral'])->name('admin.citas.updateIntegral');
Route::get('/admin/citas/{id}/exportar', [AppointmentController::class, 'exportar'])->name('admin.citas.exportar');
Route::delete('/admin/citas/{id}/destroy', [AppointmentController::class, 'destroyIntegral'])->name('admin.citas.destroy');


});