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
use App\Http\Controllers\PropertyImageController;
use App\Http\Controllers\ClientTramiteController;


/* |-------------------------------------------------------------------------- | Web Routes |-------------------------------------------------------------------------- */

// ========================================== // 0. AUTENTICACIÓN // ==========================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/recuperar-clave', [AuthController::class, 'recoverPassword'])->name('password.recover');

// ========================================== // 1. RUTAS PÚBLICAS // ==========================================
Route::get('/', [PublicController::class, 'index']);
Route::get('/conocenos', [PublicController::class, 'conocenos'])->name('conocenos');
Route::get('/unete', fn() => view('public-pages.unete'))->name('unete.index');
Route::get('/contacto', [PublicController::class, 'contact'])->name('contacto');
Route::get('/asesorias', [PublicController::class, 'asesorias'])->name('asesorias.index');

Route::post('/contacto', [PublicController::class, 'storeContact'])->name('contact.store');
Route::post('/asesorias/guardar', [AdvisoryRequestController::class, 'store'])->name('asesorias.store');
Route::post('/trabaja-con-nosotros', [JobApplicationController::class, 'store'])->name('postulaciones.store');

Route::get('/tramites', [TramiteController::class, 'indexPublic'])->name('tramites.public.index');
Route::post('/tramites', [TramiteController::class, 'storePublic'])->name('tramites.public.store');
Route::get('/catalogo', [PublicPropertyController::class, 'index'])->name('public.catalogo.index');
Route::get('/catalogo/{property}', [PublicPropertyController::class, 'show'])->name('public.catalogo.show');

Route::get('/contacto/enviar-mensaje', fn() => redirect()->route('public.catalogo.index'));
Route::post('/contacto/enviar-mensaje', [PublicPropertyController::class, 'sendPublicMessage'])->name('public.messages.send');
Route::post('/propiedad/agendar-cita', [PublicPropertyController::class, 'storeAppointment'])->name('public.appointments.store');
Route::post('/citas/confirmar', [PublicPropertyController::class, 'confirmAppointment'])->name('public.appointments.confirm');

// ========================================== // 2. INTRANET / PANEL ADMIN // ==========================================
Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/intranet/resumen', fn() => view('intranet.users.resumen'))->name('admin.resumen');

    Route::resource('intranet/clients', ClientController::class);
    Route::resource('intranet/properties', PropertyController::class);
    Route::resource('intranet/tramites', TramiteController::class);
    Route::resource('intranet/schedules', ScheduleController::class);
    Route::resource('intranet/social-links', SocialLinkController::class);
    Route::resource('intranet/users', UserController::class);


    Route::get('/intranet/postulaciones', [JobApplicationController::class, 'indexIntranet'])->name('intranet.applications.index');
    Route::post('/intranet/postulaciones/{application}/contratar', [JobApplicationController::class, 'contratar'])->name('intranet.applications.contratar');
    Route::delete('/intranet/postulaciones/{application}', [JobApplicationController::class, 'destroy'])->name('intranet.applications.destroy');

    // IMÁGENES
    Route::get('/intranet/properties/{property}/images', [PropertyImageController::class, 'index'])->name('properties.images.index');
    Route::post('/intranet/properties/{property}/images', [PropertyImageController::class, 'store'])->name('properties.images.store');
    Route::post('/intranet/properties/images/reorder', [PropertyImageController::class, 'reorder'])->name('properties.images.reorder');
    Route::post('/intranet/properties/images/{image}/primary', [PropertyImageController::class, 'setPrimary'])->name('properties.images.primary');
    Route::delete('/intranet/properties/images/{image}', [PropertyImageController::class, 'destroy'])->name('properties.images.destroy');

    // AGENDA Y CITAS INTEGRALES
    Route::get('/admin/agenda', [AppointmentController::class, 'index'])->name('admin.agenda');
    Route::post('/admin/agenda', [AppointmentController::class, 'storeManual'])->name('admin.agenda.store');
    Route::get('/admin/gestion-citas', [AppointmentController::class, 'gestionCitas'])->name('gestion.citas');
    Route::get('/admin/citas-integrales', [AppointmentController::class, 'integrales'])->name('admin.citas-totales');
    Route::get('/admin/citas/crear', [AppointmentController::class, 'create'])->name('admin.citas.create');
    Route::post('/admin/gestion-citas', [AppointmentController::class, 'storeManual'])->name('gestion.citas.store');
    Route::post('/admin/citas-integrales', [AppointmentController::class, 'storeIntegral'])->name('admin.citas.storeIntegral');
    Route::post('/admin/integrales/store', [AppointmentController::class, 'storeIntegral'])->name('admin.integrales.store');
     Route::post(
    '/intranet/cartera/{id}/regresar-origen',
    [AppointmentController::class, 'returnPortfolioToSource']
)
    ->whereNumber('id')
    ->name('admin.cartera.regresar-origen');
    Route::patch(
    '/intranet/client-tramites/{clientTramite}/iniciar',
    [ClientTramiteController::class, 'iniciar']
)
    ->whereNumber('clientTramite')
    ->name('client-tramites.iniciar');


Route::post(
    '/intranet/client-tramites/{clientTramite}/sin-exito',
    [ClientTramiteController::class, 'finalizarSinExito']
)
    ->whereNumber('clientTramite')
    ->name('client-tramites.sin-exito');
    // Cartera
    Route::post('/intranet/integrales/{tipo}/{id}/cartera', [AppointmentController::class, 'moveIntegralToPortfolio'])->where('tipo', 'contact|advisory|tramite')->whereNumber('id')->name('admin.integrales.cartera');
    Route::get('/intranet/cartera', [AppointmentController::class, 'cartera'])->name('admin.cartera');

    // Prospectos
    Route::get('/intranet/prospectos/{id}/historial', [AppointmentController::class, 'historialProspecto'])->whereNumber('id')->name('admin.prospectos.historial');
    Route::put('/intranet/prospectos/{id}/perfil', [AppointmentController::class, 'updateProspectProfile'])->whereNumber('id')->name('admin.prospectos.perfil.update');
    Route::post('/intranet/prospectos/{id}/movimientos', [AppointmentController::class, 'storeProspectMovement'])->whereNumber('id')->name('admin.prospectos.movimientos.store');
    Route::post('/intranet/cartera/{prospect}/convertir-cliente', [AppointmentController::class, 'convertProspectToClient'])->whereNumber('prospect')->name('admin.cartera.convertir-cliente');

    // Acciones sobre citas e integrales (IDs alfanuméricos)
    Route::get('/admin/citas/{id}/edit', [AppointmentController::class, 'edit'])->where('id', '[A-Za-z0-9_]+')->name('admin.citas.edit');
    Route::get('/admin/citas/{id}/exportar', [AppointmentController::class, 'exportar'])->where('id', '[A-Za-z0-9_]+')->name('admin.citas.exportar');
    Route::patch('/admin/citas/{id}/gestionar', [AppointmentController::class, 'gestionar'])->where('id', '[A-Za-z0-9_]+')->name('admin.citas.gestionar');
    Route::put('/admin/citas/{id}', [AppointmentController::class, 'updateIntegral'])->where('id', '[A-Za-z0-9_]+')->name('admin.citas.update');
    Route::match(['put', 'patch'], '/intranet/citas/{id}/estado', [AppointmentController::class, 'cambiarEstado'])->where('id', '[A-Za-z0-9_]+')->name('citas.estado');

    // Eliminación (reciclaje)
    Route::delete('/admin/citas/{id}', [AppointmentController::class, 'destroyIntegral'])->where('id', '[A-Za-z0-9_]+')->name('gestion.citas.destroy');
    Route::delete('/admin/integrales/{id}', [AppointmentController::class, 'destroyIntegral'])->where('id', '[A-Za-z0-9_]+')->name('admin.integrales.destroy');

    // Restauración y borrado permanente
    Route::patch('/admin/citas/{id}/restaurar', [AppointmentController::class, 'restaurar'])->where('id', '[A-Za-z0-9_]+')->name('admin.citas.restaurar');
    Route::patch('/admin/integrales/{id}/restaurar', [AppointmentController::class, 'restaurar'])->where('id', '[A-Za-z0-9_]+')->name('admin.integrales.restaurar');
    Route::delete('/admin/citas/{id}/forzar-eliminar', [AppointmentController::class, 'forzarEliminar'])->where('id', '[A-Za-z0-9_]+')->name('admin.citas.forzar-eliminar');
    Route::delete('/admin/integrales/{id}/forzar-eliminar', [AppointmentController::class, 'forzarEliminar'])->where('id', '[A-Za-z0-9_]+')->name('admin.integrales.forzarEliminar');

    // Cartera y clientes
    Route::post('/intranet/citas/{id}/cartera', [AppointmentController::class, 'moveToPortfolio'])->whereNumber('id')->name('gestion.citas.cartera');
    Route::patch('/intranet/clients/{client}/confirmar-revision', [ClientController::class, 'confirmReview'])->name('clients.confirm-review');
    Route::post('/intranet/citas/{id}/exportar-cliente', [AppointmentController::class, 'exportAppointmentToClient'])->whereNumber('id')->name('gestion.citas.exportar-cliente');
    Route::post(
    '/intranet/client-tramites/{clientTramite}/con-exito',
    [ClientTramiteController::class, 'finalizarConExito']
)
    ->whereNumber('clientTramite')
    ->name('client-tramites.con-exito');


    // Trámites
    Route::patch('/intranet/tramites/{tramite}/iniciar', [TramiteController::class, 'iniciar'])->whereNumber('tramite')->name('tramites.iniciar');
    Route::post('/intranet/tramites/{tramite}/con-exito', [TramiteController::class, 'finalizarConExito'])->whereNumber('tramite')->name('tramites.con-exito');
    Route::post('/intranet/tramites/{tramite}/sin-exito', [TramiteController::class, 'finalizarSinExito'])->whereNumber('tramite')->name('tramites.sin-exito');

    // Contabilidad
    Route::get('/intranet/accounting/expense', fn() => redirect()->route('accounting.index'));
    Route::get('/intranet/accounting/sale', fn() => redirect()->route('accounting.index'));

    Route::get('/intranet/accounting', [AccountingController::class, 'index'])->name('accounting.index');
    Route::get('/intranet/accounting/{transaction}/review', [AccountingController::class, 'review'])->whereNumber('transaction')->name('accounting.review');
    Route::patch('/intranet/accounting/{transaction}', [AccountingController::class, 'updateTransaction'])->whereNumber('transaction')->name('accounting.transaction.update');
    Route::get('/intranet/accounting/expenses/create', [AccountingController::class, 'createExpenseMovement'])->name('accounting.movements.create');
    Route::post('/intranet/accounting/expenses', [AccountingController::class, 'storeExpenseMovement'])->name('accounting.movements.store');
    Route::get('/intranet/accounting/ledger', [AccountingController::class, 'expenseLedger'])->name('accounting.ledger');
    Route::get('/intranet/accounting/vehicle-costs', [AccountingController::class, 'vehicleCosts'])->name('accounting.vehicle-costs');
    Route::post('/intranet/accounting/vehicle-costs', [AccountingController::class, 'storeVehicleCost'])->name('accounting.vehicle-costs.store');
    Route::get('/intranet/accounting/commission-settings', [AccountingController::class, 'commissionSettings'])->name('accounting.commission-settings');
    Route::post('/intranet/accounting/commission-settings', [AccountingController::class, 'storeCommissionSettings'])->name('accounting.commission-settings.store');




Route::get(
    '/intranet/accounting/operations',
    [AccountingController::class, 'operations']
)->name('accounting.operations');
Route::get(
    '/intranet/accounting/commission-report',
    [AccountingController::class, 'commissionReport']
)->name('accounting.commission-report');

Route::get(
    '/intranet/accounting/pyg',
    [AccountingController::class, 'pyg']
)->name('accounting.pyg');


    Route::post('/intranet/accounting/transactions/{transaction}/vehicle-trips', [AccountingController::class, 'storeVehicleTrip'])->name('accounting.vehicle-trips.store');
    Route::patch('/intranet/accounting/vehicle-trips/{trip}', [AccountingController::class, 'updateVehicleTrip'])->name('accounting.vehicle-trips.update');
    Route::delete('/intranet/accounting/vehicle-trips/{trip}', [AccountingController::class, 'destroyVehicleTrip'])->name('accounting.vehicle-trips.destroy');
    Route::post('/intranet/accounting/transactions/{transaction}/advisor-commissions', [AccountingController::class, 'storeAdvisorCommission'])->name('accounting.advisor-commissions.store');
    Route::patch('/intranet/accounting/transactions/{transaction}/participants/{role}', [AccountingController::class, 'updateParticipantRole'])->name('accounting.participants.role.update');
    Route::post('/intranet/accounting/transactions/{transaction}/participants', [AccountingController::class, 'storeParticipant'])->name('accounting.participants.store');
    Route::delete('/intranet/accounting/participants/{participant}', [AccountingController::class, 'destroyParticipant'])->name('accounting.participants.destroy');

    Route::get('/intranet/accounting/expense/create', [AccountingController::class, 'createExpense'])->name('accounting.expense.create');
    Route::post('/intranet/accounting/expense', [AccountingController::class, 'storeExpense'])->name('accounting.expense.store');
    Route::get('/intranet/accounting/sale/create', [AccountingController::class, 'createSale'])->name('accounting.sale.create');
    Route::post('/intranet/accounting/sale', [AccountingController::class, 'storeSale'])->name('accounting.sale.store');

    Route::get('/intranet/accounting/{transaction}/factura/cliente', [AccountingController::class, 'invoiceCustomer'])->whereNumber('transaction')->name('accounting.invoice.customer');
Route::post('/intranet/accounting/{transaction}/factura/cliente', [AccountingController::class, 'storeInvoiceCustomer'])->whereNumber('transaction')->name('accounting.invoice.customer.store');
Route::get('/intranet/accounting/{transaction}/factura/revision', [AccountingController::class, 'invoiceReview'])->whereNumber('transaction')->name('accounting.invoice.review');
Route::post('/intranet/accounting/{transaction}/factura/emitir', [AccountingController::class, 'issueInvoice'])->whereNumber('transaction')->name('accounting.invoice.issue');
Route::get('/intranet/accounting/{transaction}/factura/documento', [AccountingController::class, 'showInvoiceDocument'])->whereNumber('transaction')->name('accounting.invoice.document');
Route::post(
    '/intranet/accounting/{transaction}/cerrar',
    [AccountingController::class, 'closeTransaction']
)->whereNumber('transaction')
 ->name('accounting.transaction.close');
 Route::get(
    '/intranet/accounting/invoices/history',
    [AccountingController::class, 'invoiceHistory']
)->name('accounting.invoice.history');
Route::get(
    '/intranet/accounting/expenses',
    [AccountingController::class, 'expenses']
)->name('accounting.expenses');
Route::get(
    '/intranet/accounting/expenses/report',
    [AccountingController::class, 'expensesReport']
)->name('accounting.expenses.report');

Route::post(
    '/intranet/accounting/expense-groups',
    [AccountingController::class, 'storeExpenseGroup']
)->name('accounting.expense-groups.store');


Route::post(
    '/intranet/accounting/expense-categories',
    [AccountingController::class, 'storeExpenseCategory']
)->name('accounting.expense-categories.store');


Route::post(
    '/intranet/accounting/expense-subcategories',
    [AccountingController::class, 'storeExpenseSubcategory']
)->name('accounting.expense-subcategories.store');
Route::get(
    '/intranet/accounting/{transaction}/report',
    [AccountingController::class, 'transactionReport']
)->name('accounting.transaction.report');

});