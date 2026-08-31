<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccountingExpense;
use App\Models\SalesAccounting;
use App\Models\Property;
use App\Models\Client;
use App\Models\User;
use App\Models\AccountingTransaction;
use App\Models\AccountingExpenseMovement;
use App\Models\ExpenseCategory;
use App\Models\ExpenseSubcategory;
use App\Models\VehicleCostConfiguration;
use App\Models\AccountingVehicleTrip;
use Carbon\Carbon;
use App\Models\AccountingAdvisorCommission;
use App\Models\CommissionConfiguration;
use App\Models\CommissionRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\AccountingTransactionParticipant;
use App\Models\AccountingInvoice;
use App\Models\ExpenseGroup;


class AccountingController extends Controller
{
    public function index() {
        $transactions = AccountingTransaction::with(['client','prospect','tramite','property'])->latest()->paginate(10);
        $expenses = AccountingExpense::with(['category','accountingTransaction'])->latest()->limit(10)->get();
        $expenseMovements = AccountingExpenseMovement::with(['category','subcategory','accountingTransaction'])->where('is_active', true)->orderByDesc('expense_date')->orderByDesc('id')->limit(10)->get();
        $pendingTransactions = AccountingTransaction::where('status', 'Pendiente')->count();
        $totalTransactions = AccountingTransaction::count();
        $grossIncome = AccountingTransaction::sum('gross_income');
        $netProfit = AccountingTransaction::sum('net_profit');
        return view('intranet.accounting.index', compact('transactions','expenses','expenseMovements','pendingTransactions','totalTransactions','grossIncome','netProfit'));
    }

    public function review(AccountingTransaction $transaction) {
        $this->syncSuggestedParticipantsFromProperty($transaction);
        $transaction->load(['client','prospect','tramite','property','advisorCommissions.advisor','activeParticipants.user','expenses.category','vehicleTrips' => function ($query) { $query->where('is_active', true)->orderByDesc('trip_date')->orderByDesc('id'); },'vehicleTrips.vehicleCostConfiguration']);
        $totalVehicleKilometers = $transaction->vehicleTrips->sum('kilometers');
        $totalVehicleCost = $transaction->vehicleTrips->sum('calculated_cost');
        $automaticCommissionSummary = $this->calculateAutomaticCommissions($transaction);
        $properties = Property::orderBy('title')->get(['id','title','service_type','property_type','location','price','price_dropped','status']);
        $users = User::orderBy('name')->get(['id','name','last_name','role']);
        $expenseCategories = \App\Models\ExpenseCategory::where('is_active', true)->where('expense_type', 'Directo')->orderBy('name')->get();
        return view('intranet.accounting.review', compact('transaction','properties','users','expenseCategories','totalVehicleKilometers','totalVehicleCost','automaticCommissionSummary'));
    }

    public function updateTransaction(Request $request, AccountingTransaction $transaction) {
        if ($transaction->status === 'Cerrada') return redirect()->route('accounting.review', $transaction->id)->with('error', 'Esta operación ya está cerrada y sus valores no pueden modificarse directamente.');
        $validated = $request->validate([
            'operation_type' => 'required|in:Trámite / Servicio,Corretaje / Propiedad',
            'property_id' => 'nullable|exists:properties,id',
            'published_price' => 'nullable|numeric|min:0',
            'closing_price' => 'nullable|numeric|min:0',
            'brokerage_percentage' => 'nullable|numeric|min:0|max:100',
            'brokerage_amount' => 'nullable|numeric|min:0',
            'brokerage_mode' => 'nullable|in:percentage,fixed',
            'service_amount' => 'nullable|numeric|min:0',
            'general_expenses_prorated' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:5000',
        ]);
        if ($validated['operation_type'] === 'Trámite / Servicio') {
            $request->validate(['service_amount' => 'required|numeric|min:0']);
            $validated['property_id'] = $validated['published_price'] = $validated['closing_price'] = $validated['brokerage_percentage'] = $validated['brokerage_amount'] = null;
        }
        if ($validated['operation_type'] === 'Corretaje / Propiedad') {
            $request->validate(['property_id' => 'required|exists:properties,id','closing_price' => 'required|numeric|min:0','brokerage_mode' => 'required|in:percentage,fixed']);
            if ($validated['brokerage_mode'] === 'percentage') {
                $request->validate(['brokerage_percentage' => 'required|numeric|min:0|max:100']);
                $validated['brokerage_amount'] = round($validated['closing_price'] * ($validated['brokerage_percentage'] / 100), 2);
            }
            if ($validated['brokerage_mode'] === 'fixed') {
                $request->validate(['brokerage_amount' => 'required|numeric|min:0']);
            }
            $validated['service_amount'] = null;
        }
        $transaction->update([
            'operation_type' => $validated['operation_type'],
            'property_id' => $validated['property_id'] ?? null,
            'published_price' => $validated['published_price'] ?? null,
            'closing_price' => $validated['closing_price'] ?? null,
            'brokerage_percentage' => $validated['brokerage_percentage'] ?? null,
            'brokerage_amount' => $validated['brokerage_amount'] ?? null,
            'service_amount' => $validated['service_amount'] ?? null,
            'general_expenses_prorated' => $validated['general_expenses_prorated'] ?? 0,
            'notes' => $validated['notes'] ?? $transaction->notes,
            'status' => $transaction->status === 'Pendiente' ? 'En cálculo' : $transaction->status,
        ]);
        $transaction->recalculateTotals();
        return redirect()->route('accounting.review', $transaction->id)->with('success', 'La operación contable fue actualizada correctamente.');
    }

    public function storeTransactionExpense(Request $request, AccountingTransaction $transaction) {
        if ($transaction->status === 'Cerrada') return redirect()->route('accounting.review', $transaction->id)->with('error', 'No se pueden agregar gastos a una operación cerrada.');
        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'expense_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'provider' => 'nullable|string|max:150',
            'document_number' => 'nullable|string|max:100',
            'payment_status' => 'required|in:Pendiente,Pagado',
            'payment_method' => 'nullable|string|max:80',
            'payment_reference' => 'nullable|string|max:150',
            'notes' => 'nullable|string|max:2000',
        ]);
        $category = \App\Models\ExpenseCategory::where('id', $validated['expense_category_id'])->where('is_active', true)->where('expense_type', 'Directo')->first();
        if (!$category) return redirect()->route('accounting.review', $transaction->id)->with('error', 'La categoría seleccionada no corresponde a un gasto directo activo.');
        AccountingExpense::create([
            'expense_category_id' => $category->id,
            'accounting_transaction_id' => $transaction->id,
            'expense_name' => $validated['expense_name'],
            'expense_category' => 'Variable',
            'expense_type' => 'Directo',
            'amount' => $validated['amount'],
            'expense_date' => $validated['expense_date'],
            'provider' => $validated['provider'] ?? null,
            'document_number' => $validated['document_number'] ?? null,
            'payment_status' => $validated['payment_status'],
            'payment_method' => $validated['payment_method'] ?? null,
            'payment_reference' => $validated['payment_reference'] ?? null,
            'paid_at' => $validated['payment_status'] === 'Pagado' ? now() : null,
            'notes' => $validated['notes'] ?? null,
            'is_active' => true,
        ]);
        return redirect()->route('accounting.review', $transaction->id)->with('success', 'El gasto directo fue registrado correctamente.');
    }

    public function createExpense() { return view('intranet.accounting.expenses_create'); }

    public function storeExpense(Request $request) {
        $request->validate(['concept' => 'required|string|max:255','amount' => 'required|numeric','expense_date' => 'required|date','category' => 'required|string']);
        AccountingExpense::create(['user_id' => auth()->id() ?? 1,'concept' => $request->concept,'amount' => $request->amount,'expense_date' => $request->expense_date,'category' => $request->category,'notes' => $request->notes]);
        return redirect()->route('accounting.index')->with('success', '¡Gasto registrado correctamente!');
    }

    public function createSale() {
        $properties = Property::where('status', 'Disponible')->get();
        $clients = Client::all();
        $users = User::all();
        return view('intranet.accounting.sales_create', compact('properties','clients','users'));
    }

    public function storeSale(Request $request) {
        $request->validate(['property_id' => 'required|exists:properties,id','client_id' => 'required|exists:clients,id','sale_price' => 'required|numeric','commission' => 'required|numeric','sale_date' => 'required|date']);
        SalesAccounting::create($request->all());
        Property::where('id', $request->property_id)->update(['status' => 'Vendido']);
        return redirect()->route('accounting.index')->with('success', '¡Venta y comisión registrada con éxito!');
    }
public function expenses(Request $request)
{
    $month = (int) $request->input('month', now()->month);
    $year = (int) $request->input('year', now()->year);

    $fromDate = $request->input('from_date');
    $toDate = $request->input('to_date');

    if ($month < 1 || $month > 12) {
        $month = now()->month;
    }

    if ($year < 2000 || $year > 2100) {
        $year = now()->year;
    }

    /*
    |--------------------------------------------------------------------------
    | Validación del rango personalizado
    |--------------------------------------------------------------------------
    */
    if (($fromDate && !$toDate) || (!$fromDate && $toDate)) {
        return back()
            ->withInput()
            ->with('error', 'Para consultar por rango debes seleccionar la fecha inicial y la fecha final.');
    }

    if ($fromDate && $toDate && $fromDate > $toDate) {
        return back()
            ->withInput()
            ->with('error', 'La fecha inicial no puede ser posterior a la fecha final.');
    }

    /*
    |--------------------------------------------------------------------------
    | Consulta base
    |--------------------------------------------------------------------------
    */
    $query = AccountingExpenseMovement::with([
            'category',
            'subcategory',
            'accountingTransaction'
        ])
        ->where('is_active', true);

    /*
    |--------------------------------------------------------------------------
    | Filtro
    |--------------------------------------------------------------------------
    | Si existen ambas fechas, el rango personalizado tiene prioridad.
    | Caso contrario se utiliza mes + año.
    */
    if ($fromDate && $toDate) {

        $query->whereBetween('expense_date', [
            $fromDate,
            $toDate
        ]);

        $filterType = 'range';

    } else {

        $query->whereYear('expense_date', $year)
            ->whereMonth('expense_date', $month);

        $filterType = 'month';
    }

    /*
    |--------------------------------------------------------------------------
    | Movimientos
    |--------------------------------------------------------------------------
    */
    $movements = $query
        ->orderByDesc('expense_date')
        ->orderByDesc('id')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | Categorías y subcategorías
    |--------------------------------------------------------------------------
    */
    $categories = ExpenseCategory::with([
            'subcategories' => function ($query) {
                $query->where('is_active', true)
                    ->orderBy('name');
            }
        ])
        ->where('is_active', true)
        ->orderBy('name')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | Totales
    |--------------------------------------------------------------------------
    */
$totalExpenses = $movements->sum('amount');

$paidExpenses = $movements
    ->where('payment_status', 'Pagado')
    ->sum('amount');

$pendingExpenses = $movements
    ->where('payment_status', 'Pendiente')
    ->sum('amount');


$groups = ExpenseGroup::where('is_active', true)
    ->with([
        'categories' => function ($query) {
            $query->where('is_active', true)
                ->orderBy('name')
                ->with([
                    'subcategories' => function ($subQuery) {
                        $subQuery->where('is_active', true)
                            ->orderBy('name');
                    }
                ]);
        }
    ])
    ->orderBy('name')
    ->get();


/*
|--------------------------------------------------------------------------
| Vista
|--------------------------------------------------------------------------
*/

return view('intranet.accounting.expenses.index', compact(
    'month',
    'year',
    'fromDate',
    'toDate',
    'filterType',
    'movements',
    'categories',
    'groups',
    'totalExpenses',
    'paidExpenses',
    'pendingExpenses'
));
}
public function expensesReport(Request $request)
{
    $month = (int) $request->input('month', now()->month);
    $year = (int) $request->input('year', now()->year);

    $fromDate = $request->input('from_date');
    $toDate = $request->input('to_date');

    if ($month < 1 || $month > 12) {
        $month = now()->month;
    }

    if ($year < 2000 || $year > 2100) {
        $year = now()->year;
    }

    if (($fromDate && !$toDate) || (!$fromDate && $toDate)) {
        return redirect()
            ->route('accounting.expenses')
            ->with('error', 'Para generar el reporte por rango debes seleccionar la fecha inicial y la fecha final.');
    }

    if ($fromDate && $toDate && $fromDate > $toDate) {
        return redirect()
            ->route('accounting.expenses')
            ->with('error', 'La fecha inicial no puede ser posterior a la fecha final.');
    }

    $query = AccountingExpenseMovement::with([
            'category',
            'subcategory',
            'accountingTransaction'
        ])
        ->where('is_active', true);

    if ($fromDate && $toDate) {

        $query->whereBetween('expense_date', [
            $fromDate,
            $toDate
        ]);

        $filterType = 'range';

    } else {

        $query->whereYear('expense_date', $year)
            ->whereMonth('expense_date', $month);

        $filterType = 'month';
    }

    $movements = $query
        ->orderBy('expense_date')
        ->orderBy('id')
        ->get();

    $totalExpenses = $movements->sum('amount');

    $paidExpenses = $movements
        ->where('payment_status', 'Pagado')
        ->sum('amount');

    $pendingExpenses = $movements
        ->where('payment_status', 'Pendiente')
        ->sum('amount');

    return view('intranet.accounting.expenses.report', compact(
        'month',
        'year',
        'fromDate',
        'toDate',
        'filterType',
        'movements',
        'totalExpenses',
        'paidExpenses',
        'pendingExpenses'
    ));
}
public function createExpenseMovement()
{
    $groups = ExpenseGroup::where('is_active', true)
        ->orderBy('name')
        ->get();

    $categories = ExpenseCategory::where('is_active', true)
        ->whereNotNull('expense_group_id')
        ->orderBy('name')
        ->get();

    $subcategories = ExpenseSubcategory::where('is_active', true)
        ->orderBy('name')
        ->get();

    return view(
        'intranet.accounting.expenses.create',
        compact(
            'groups',
            'categories',
            'subcategories'
        )
    );
}

    public function storeExpenseMovement(Request $request) {
        $validated = $request->validate([
            'document_file' => ['nullable','file','mimes:pdf,jpg,jpeg,png','max:5120'],
            'expense_category_id' => ['required','exists:expense_categories,id'],
            'expense_subcategory_id' => ['nullable','exists:expense_subcategories,id'],
            'accounting_transaction_id' => ['nullable','exists:accounting_transactions,id'],
            'concept' => ['required','string','max:255'],
            'amount' => ['required','numeric','min:0.01'],
            'expense_date' => ['required','date'],
            'provider' => ['nullable','string','max:180'],
            'document_type' => ['nullable','string','max:80'],
            'document_number' => ['nullable','string','max:120'],
            'payment_status' => ['required','in:Pendiente,Pagado'],
            'payment_method' => ['nullable','string','max:80'],
            'payment_reference' => ['nullable','string','max:150'],
            'was_budgeted' => ['required','boolean'],
            'notes' => ['nullable','string','max:5000'],
        ]);
        if (!empty($validated['expense_subcategory_id'])) {
            $validSubcategory = ExpenseSubcategory::where('id', $validated['expense_subcategory_id'])->where('expense_category_id', $validated['expense_category_id'])->where('is_active', true)->exists();
            if (!$validSubcategory) return back()->withInput()->withErrors(['expense_subcategory_id' => 'La subcategoría no pertenece a la categoría seleccionada.']);
        }
        $documentPath = $request->hasFile('document_file') ? $request->file('document_file')->store('accounting/documents', 'public') : null;
        AccountingExpenseMovement::create([
            'expense_category_id' => $validated['expense_category_id'],
            'expense_subcategory_id' => $validated['expense_subcategory_id'] ?? null,
            'accounting_transaction_id' => $validated['accounting_transaction_id'] ?? null,
            'concept' => $validated['concept'],
            'amount' => $validated['amount'],
            'expense_date' => $validated['expense_date'],
            'provider' => $validated['provider'] ?? null,
            'document_type' => $validated['document_type'] ?? null,
            'document_number' => $validated['document_number'] ?? null,
            'document_path' => $documentPath,
            'payment_status' => $validated['payment_status'],
            'payment_method' => $validated['payment_method'] ?? null,
            'payment_reference' => $validated['payment_reference'] ?? null,
            'paid_at' => $validated['payment_status'] === 'Pagado' ? now() : null,
            'was_budgeted' => $validated['was_budgeted'],
            'notes' => $validated['notes'] ?? null,
            'is_active' => true,
        ]);
        return redirect()->route('accounting.expenses')->with('success', 'El gasto fue registrado correctamente.');
    }

public function invoiceCustomer(AccountingTransaction $transaction) {
    $transaction->load(['client','prospect','property','invoice']);
    if ($transaction->status === 'Pendiente') return redirect()->route('accounting.review', $transaction->id)->with('error', 'Primero debes guardar los datos de facturación de la operación.');
    $invoice = $transaction->invoice;
    if (!$invoice) {
        $client = $transaction->client;
        $invoice = new AccountingInvoice();
        $invoice->accounting_transaction_id = $transaction->id;
        $invoice->client_id = $client?->id;
        $invoice->identification_type = 'cedula';
        $invoice->identification_number = $client?->identification_card;
        $invoice->customer_name = trim(($client?->name ?? '') . ' ' . ($client?->last_name ?? ''));
        $invoice->phone = $client?->phone;
        $invoice->email = $client?->email;
        $invoice->document_type = 'factura';
        $invoice->status = 'Borrador';
        if ($transaction->operation_type === 'Corretaje / Propiedad') {
            $invoice->subtotal = (float) ($transaction->brokerage_amount ?? 0);
        } else {
            $invoice->subtotal = (float) ($transaction->service_amount ?? 0);
        }
        $invoice->tax_percentage = 0;
        $invoice->tax_amount = 0;
        $invoice->total = $invoice->subtotal;
    }
    return view('intranet.accounting.invoice_customer', compact('transaction','invoice'));
}

public function storeInvoiceCustomer(Request $request, AccountingTransaction $transaction) {
    if ($transaction->status === 'Pendiente') return redirect()->route('accounting.review', $transaction->id)->with('error', 'Primero debes guardar los datos económicos de la operación.');
    if ($transaction->status === 'Cerrada') return redirect()->route('accounting.review', $transaction->id)->with('error', 'Esta operación ya se encuentra cerrada.');
    $validated = $request->validate([
        'identification_type' => 'required|in:cedula,ruc,pasaporte,consumidor_final',
        'identification_number' => 'nullable|string|max:30',
        'customer_name' => 'required|string|max:255',
        'business_name' => 'nullable|string|max:255',
        'billing_address' => 'nullable|string|max:500',
        'phone' => 'nullable|string|max:50',
        'email' => 'nullable|email|max:255',
        'document_type' => 'required|in:factura,comprobante',
        'notes' => 'nullable|string|max:3000',
    ]);
    if ($transaction->operation_type === 'Corretaje / Propiedad') {
        $subtotal = (float) ($transaction->brokerage_amount ?? 0);
    } else {
        $subtotal = (float) ($transaction->service_amount ?? 0);
    }
    $invoice = AccountingInvoice::updateOrCreate(
        ['accounting_transaction_id' => $transaction->id],
        [
            'client_id' => $transaction->client_id,
            'identification_type' => $validated['identification_type'],
            'identification_number' => $validated['identification_number'] ?? null,
            'customer_name' => $validated['customer_name'],
            'business_name' => $validated['business_name'] ?? null,
            'billing_address' => $validated['billing_address'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'document_type' => $validated['document_type'],
            'subtotal' => round($subtotal, 2),
            'tax_percentage' => 0,
            'tax_amount' => 0,
            'total' => round($subtotal, 2),
            'status' => 'Borrador',
            'notes' => $validated['notes'] ?? null,
        ]
    );
    return redirect()->route('accounting.invoice.review', $transaction->id)->with('success', 'Los datos del cliente fueron guardados correctamente.');
}
public function issueInvoice(Request $request, AccountingTransaction $transaction) {
    $transaction->load('invoice');
    $invoice = $transaction->invoice;

    if (!$invoice) return redirect()->route('accounting.invoice.customer', $transaction->id)->with('error', 'No existe un borrador de facturación para esta operación.');
    if ($invoice->status === 'Emitida') return redirect()->route('accounting.invoice.review', $transaction->id)->with('error', 'Este comprobante ya fue emitido.');

    $validated = $request->validate(['tax_percentage' => 'required|numeric|min:0|max:100']);
    $subtotal = round((float) $invoice->subtotal, 2);
    $taxPercentage = round((float) $validated['tax_percentage'], 4);
    $taxAmount = round($subtotal * ($taxPercentage / 100), 2);
    $total = round($subtotal + $taxAmount, 2);

    $prefix = $invoice->document_type === 'factura' ? 'FAC' : 'COMP';
    $invoiceNumber = sprintf('%s-%s-%06d', $prefix, now()->format('Ym'), $invoice->id);

    $invoice->update([
        'tax_percentage' => $taxPercentage,
        'tax_amount' => $taxAmount,
        'total' => $total,
        'invoice_number' => $invoiceNumber,
        'status' => 'Emitida',
        'issued_at' => now(),
    ]);

    $transaction->update(['invoiced_at' => now()]);

    return redirect()->route('accounting.invoice.review', $transaction->id)->with('success', 'El comprobante fue emitido correctamente.');
}
public function invoiceReview(AccountingTransaction $transaction) {
    $transaction->load(['client', 'property', 'invoice']);
    $invoice = $transaction->invoice;
    if (!$invoice) return redirect()->route('accounting.invoice.customer', $transaction->id)->with('error', 'Primero debes guardar los datos de facturación del cliente.');
    return view('intranet.accounting.invoice_review', compact('transaction', 'invoice'));
}

    public function expenseLedger(Request $request) {
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);
        if ($month < 1 || $month > 12) $month = now()->month;
        if ($year < 2000 || $year > 2100) $year = now()->year;
        $movements = AccountingExpenseMovement::with(['category','subcategory','accountingTransaction'])->where('is_active', true)->whereYear('expense_date', $year)->whereMonth('expense_date', $month)->orderBy('expense_category_id')->orderBy('expense_subcategory_id')->orderBy('expense_date')->get();
        $categoryTotals = $movements->groupBy('expense_category_id')->map(fn($items) => ['category' => $items->first()->category,'total' => $items->sum('amount'),'count' => $items->count()]);
        $subcategoryTotals = $movements->groupBy(fn($m) => $m->expense_category_id . '-' . ($m->expense_subcategory_id ?? 'none'))->map(fn($items) => ['category' => $items->first()->category,'subcategory' => $items->first()->subcategory,'total' => $items->sum('amount'),'count' => $items->count()]);
        $totalExpenses = $movements->sum('amount');
        $paidExpenses = $movements->where('payment_status', 'Pagado')->sum('amount');
        $pendingExpenses = $movements->where('payment_status', 'Pendiente')->sum('amount');
        $unbudgetedExpenses = $movements->where('was_budgeted', false)->sum('amount');
        return view('intranet.accounting.ledger', compact('month','year','movements','categoryTotals','subcategoryTotals','totalExpenses','paidExpenses','pendingExpenses','unbudgetedExpenses'));
    }

    public function vehicleCosts() {
        $configurations = VehicleCostConfiguration::orderByDesc('effective_from')->orderByDesc('id')->get();
        $activeConfiguration = VehicleCostConfiguration::where('is_active', true)->orderByDesc('effective_from')->orderByDesc('id')->first();
        return view('intranet.accounting.vehicle-costs', compact('configurations','activeConfiguration'));
    }

    public function storeVehicleCost(Request $request) {
        $validated = $request->validate([
            'name' => ['required','string','max:150'],
            'effective_from' => ['required','date'],
            'fuel_price_per_gallon' => ['nullable','numeric','min:0'],
            'vehicle_efficiency_km_per_gallon' => ['nullable','numeric','min:0.01'],
            'oil_change_cost' => ['nullable','numeric','min:0'],
            'oil_change_interval_km' => ['nullable','numeric','min:0.01'],
            'tires_total_cost' => ['nullable','numeric','min:0'],
            'tires_lifespan_km' => ['nullable','numeric','min:0.01'],
            'maintenance_cost' => ['nullable','numeric','min:0'],
            'maintenance_interval_km' => ['nullable','numeric','min:0.01'],
            'annual_insurance_cost' => ['nullable','numeric','min:0'],
            'annual_registration_cost' => ['nullable','numeric','min:0'],
            'annual_other_vehicle_costs' => ['nullable','numeric','min:0'],
            'estimated_annual_km' => ['nullable','numeric','min:0.01'],
            'notes' => ['nullable','string','max:5000'],
        ]);
        $newEffectiveDate = Carbon::parse($validated['effective_from'])->startOfDay();
        $previous = VehicleCostConfiguration::where('is_active', true)->orderByDesc('effective_from')->orderByDesc('id')->first();
        if ($previous && $previous->effective_from && $newEffectiveDate->isSameDay($previous->effective_from)) {
            $previous->fill($validated);
            $previous->effective_until = null;
            $previous->is_active = true;
            $previous->calculateCosts();
            $previous->save();
            return redirect()->route('accounting.vehicle-costs')->with('success', 'La configuración activa fue actualizada y recalculada correctamente.');
        }
        if ($previous && $previous->effective_from && $newEffectiveDate->lt($previous->effective_from)) {
            return back()->withInput()->withErrors(['effective_from' => 'La nueva configuración no puede iniciar antes de la configuración activa.']);
        }
        if ($previous) {
            $previous->effective_until = $newEffectiveDate->copy()->subDay();
            $previous->is_active = false;
            $previous->save();
        }
        $configuration = new VehicleCostConfiguration();
        $configuration->fill($validated);
        $configuration->effective_until = null;
        $configuration->is_active = true;
        $configuration->calculateCosts();
        $configuration->save();
        return redirect()->route('accounting.vehicle-costs')->with('success', 'La nueva configuración de costos del vehículo fue guardada correctamente.');
    }

    public function storeVehicleTrip(Request $request, AccountingTransaction $transaction) {
        $validated = $request->validate(['trip_date' => ['required','date'],'concept' => ['required','string','max:180'],'origin' => ['nullable','string','max:180'],'destination' => ['nullable','string','max:180'],'kilometers' => ['required','numeric','min:0.01'],'notes' => ['nullable','string']]);
        $tripDate = Carbon::parse($validated['trip_date'])->startOfDay();
        $configuration = VehicleCostConfiguration::query()->whereDate('effective_from', '<=', $tripDate)->where(function ($query) use ($tripDate) { $query->whereNull('effective_until')->orWhereDate('effective_until', '>=', $tripDate); })->orderByDesc('effective_from')->first();
        if (!$configuration) return back()->withInput()->with('error', 'No existe una configuración de costo por kilómetro válida para la fecha seleccionada.');
        $costPerKm = (float) $configuration->total_cost_per_km;
        if ($costPerKm <= 0) return back()->withInput()->with('error', 'La configuración de vehículo seleccionada no tiene un costo por kilómetro válido.');
        $kilometers = (float) $validated['kilometers'];
        $calculatedCost = round($kilometers * $costPerKm, 2);
        AccountingVehicleTrip::create([
            'accounting_transaction_id' => $transaction->id,
            'vehicle_cost_configuration_id' => $configuration->id,
            'trip_date' => $tripDate,
            'concept' => $validated['concept'],
            'origin' => $validated['origin'] ?? null,
            'destination' => $validated['destination'] ?? null,
            'kilometers' => $kilometers,
            'cost_per_km' => $costPerKm,
            'calculated_cost' => $calculatedCost,
            'notes' => $validated['notes'] ?? null,
            'is_active' => true,
        ]);
        return back()->with('success', 'Recorrido registrado correctamente.');
    }

    public function updateVehicleTrip(Request $request, AccountingVehicleTrip $trip) {
        $validated = $request->validate(['trip_date' => ['required','date'],'concept' => ['required','string','max:180'],'origin' => ['nullable','string','max:180'],'destination' => ['nullable','string','max:180'],'kilometers' => ['required','numeric','min:0.01'],'notes' => ['nullable','string']]);
        $tripDate = Carbon::parse($validated['trip_date'])->startOfDay();
        $configuration = VehicleCostConfiguration::query()->whereDate('effective_from', '<=', $tripDate)->where(function ($query) use ($tripDate) { $query->whereNull('effective_until')->orWhereDate('effective_until', '>=', $tripDate); })->orderByDesc('effective_from')->first();
        if (!$configuration) return back()->withInput()->with('error', 'No existe una configuración de costo por kilómetro válida para la fecha seleccionada.');
        $costPerKm = (float) $configuration->total_cost_per_km;
        if ($costPerKm <= 0) return back()->withInput()->with('error', 'La configuración de vehículo seleccionada no tiene un costo por kilómetro válido.');
        $kilometers = (float) $validated['kilometers'];
        $calculatedCost = round($kilometers * $costPerKm, 2);
        $trip->update([
            'vehicle_cost_configuration_id' => $configuration->id,
            'trip_date' => $tripDate,
            'concept' => $validated['concept'],
            'origin' => $validated['origin'] ?? null,
            'destination' => $validated['destination'] ?? null,
            'kilometers' => $kilometers,
            'cost_per_km' => $costPerKm,
            'calculated_cost' => $calculatedCost,
            'notes' => $validated['notes'] ?? null,
        ]);
        return back()->with('success', 'Recorrido actualizado correctamente.');
    }

    public function destroyVehicleTrip(AccountingVehicleTrip $trip) { $trip->update(['is_active' => false]); return back()->with('success', 'Recorrido eliminado correctamente.'); }

    public function storeAdvisorCommission(Request $request, AccountingTransaction $transaction) {
        $validated = $request->validate([
            'user_id' => ['required','exists:users,id'],
            'role_in_transaction' => ['required','string','max:100'],
            'percentage' => ['nullable','numeric','min:0','max:100'],
            'calculation_base' => ['nullable','numeric','min:0'],
            'commission_amount' => ['required','numeric','min:0'],
            'notes' => ['nullable','string'],
        ]);
        AccountingAdvisorCommission::create([
            'accounting_transaction_id' => $transaction->id,
            'user_id' => $validated['user_id'],
            'role_in_transaction' => $validated['role_in_transaction'],
            'percentage' => $validated['percentage'] ?? null,
            'calculation_base' => $validated['calculation_base'] ?? null,
            'commission_amount' => $validated['commission_amount'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'Calculada',
        ]);
        $transaction->recalculateTotals();
        return back()->with('success', 'Comisión del asesor registrada correctamente.');
    }

    public function commissionSettings() {
        $configurations = CommissionConfiguration::with(['rules' => function ($query) { $query->orderBy('priority')->orderBy('id'); }])->orderByDesc('effective_from')->orderByDesc('id')->get();
        $activeConfiguration = CommissionConfiguration::with(['rules' => function ($query) { $query->orderBy('priority')->orderBy('id'); }])->where('is_active', true)->orderByDesc('effective_from')->orderByDesc('id')->first();
        return view('intranet.accounting.commission-settings', compact('configurations','activeConfiguration'));
    }

    public function storeCommissionSettings(Request $request) {
        $validated = $request->validate([
            'name' => ['required','string','max:150'],
            'effective_from' => ['required','date'],
            'default_sales_distribution' => ['required','in:equal,manual'],
            'allow_manual_distribution' => ['nullable','boolean'],
            'notes' => ['nullable','string','max:5000'],
            'rules' => ['required','array','min:1'],
            'rules.*.name' => ['required','string','max:150'],
            'rules.*.participation_type' => ['required','in:capture,sale,capture_and_sale,support,closing,other'],
            'rules.*.capture_origin' => ['required','in:agency,advisor,any'],
            'rules.*.percentage' => ['required','numeric','min:0','max:100'],
            'rules.*.distribution_type' => ['required','in:individual,pool_equal,pool_manual'],
            'rules.*.is_active' => ['nullable','boolean'],
            'rules.*.notes' => ['nullable','string','max:1000'],
        ]);
        $newEffectiveDate = Carbon::parse($validated['effective_from'])->startOfDay();
        $previous = CommissionConfiguration::where('is_active', true)->orderByDesc('effective_from')->orderByDesc('id')->first();
        if ($previous && $previous->effective_from && $newEffectiveDate->lt($previous->effective_from)) {
            return back()->withInput()->withErrors(['effective_from' => 'La nueva configuración no puede iniciar antes de la configuración activa.']);
        }
        DB::transaction(function () use ($validated, $newEffectiveDate, $previous) {
            if ($previous && $previous->effective_from && $newEffectiveDate->isSameDay($previous->effective_from)) {
                $configuration = $previous;
                $configuration->fill([
                    'name' => $validated['name'],
                    'effective_from' => $validated['effective_from'],
                    'effective_to' => null,
                    'is_active' => true,
                    'default_sales_distribution' => $validated['default_sales_distribution'],
                    'allow_manual_distribution' => isset($validated['allow_manual_distribution']) ? (bool) $validated['allow_manual_distribution'] : false,
                    'notes' => $validated['notes'] ?? null,
                ]);
                $configuration->save();
                $configuration->rules()->delete();
            } else {
                if ($previous) {
                    $previous->effective_to = $newEffectiveDate->copy()->subDay();
                    $previous->is_active = false;
                    $previous->save();
                }
                $configuration = CommissionConfiguration::create([
                    'name' => $validated['name'],
                    'effective_from' => $validated['effective_from'],
                    'effective_to' => null,
                    'is_active' => true,
                    'default_sales_distribution' => $validated['default_sales_distribution'],
                    'allow_manual_distribution' => isset($validated['allow_manual_distribution']) ? (bool) $validated['allow_manual_distribution'] : false,
                    'notes' => $validated['notes'] ?? null,
                ]);
            }
            $usedCodes = [];
            foreach ($validated['rules'] as $index => $ruleData) {
                $baseCode = Str::slug($ruleData['name'], '_');
                if ($baseCode === '') $baseCode = 'rule_' . ($index + 1);
                $code = $baseCode;
                $counter = 2;
                while (in_array($code, $usedCodes, true)) { $code = $baseCode . '_' . $counter; $counter++; }
                $usedCodes[] = $code;
                CommissionRule::create([
                    'commission_configuration_id' => $configuration->id,
                    'code' => $code,
                    'name' => $ruleData['name'],
                    'participation_type' => $ruleData['participation_type'],
                    'capture_origin' => $ruleData['capture_origin'],
                    'percentage' => $ruleData['percentage'],
                    'distribution_type' => $ruleData['distribution_type'],
                    'is_active' => isset($ruleData['is_active']) ? (bool) $ruleData['is_active'] : false,
                    'priority' => $index + 1,
                    'notes' => $ruleData['notes'] ?? null,
                ]);
            }
        });
        return redirect()->route('accounting.commission-settings')->with('success', 'La configuración de comisiones fue guardada correctamente.');
    }

    private function syncSuggestedParticipantsFromProperty(AccountingTransaction $transaction): void {
        $transaction->loadMissing('property');
        $property = $transaction->property;
        if (!$property) return;
        $hasParticipants = $transaction->participants()->exists();
        if ($hasParticipants) return;
        if (($property->capture_origin ?? 'agency') === 'advisor' && !empty($property->capturing_advisor_id)) {
            \App\Models\AccountingTransactionParticipant::create([
                'accounting_transaction_id' => $transaction->id,
                'user_id' => $property->capturing_advisor_id,
                'participation_type' => 'capture',
                'distribution_percentage' => null,
                'source' => 'property',
                'is_active' => true,
                'notes' => 'Participante sugerido automáticamente desde la propiedad.',
            ]);
        }
        if (!empty($property->user_id)) {
            \App\Models\AccountingTransactionParticipant::create([
                'accounting_transaction_id' => $transaction->id,
                'user_id' => $property->user_id,
                'participation_type' => 'sale',
                'distribution_percentage' => 100,
                'source' => 'property',
                'is_active' => true,
                'notes' => 'Vendedor sugerido automáticamente desde la propiedad.',
            ]);
        }
    }

    public function updateParticipantRole(Request $request, AccountingTransaction $transaction, string $role) {
        if (!in_array($role, ['capture','sale'], true)) abort(404);
        if ($transaction->status === 'Cerrada') return back()->with('error', 'La operación está cerrada.');
        $validated = $request->validate(['participant_kind' => 'required|in:agency,advisor','user_id' => 'nullable|exists:users,id']);
        if ($validated['participant_kind'] === 'advisor' && empty($validated['user_id'])) return back()->with('error', 'Selecciona un asesor.');
        $transaction->participants()->where('participation_type', $role)->where('is_active', true)->update(['is_active' => false]);
        if ($validated['participant_kind'] === 'agency') return back()->with('success', $role === 'capture' ? 'Captación asignada a la inmobiliaria.' : 'Venta asignada a la inmobiliaria.');
        AccountingTransactionParticipant::create([
            'accounting_transaction_id' => $transaction->id,
            'user_id' => $validated['user_id'],
            'participation_type' => $role,
            'distribution_percentage' => $role === 'sale' ? 100 : null,
            'source' => 'manual',
            'is_active' => true,
            'notes' => 'Participante actualizado desde Facturación.',
        ]);
        return back()->with('success', 'Participante actualizado correctamente.');
    }

    public function storeParticipant(Request $request, AccountingTransaction $transaction) {
        if ($transaction->status === 'Cerrada') return back()->with('error', 'La operación está cerrada.');
        $validated = $request->validate(['user_id' => 'required|exists:users,id','participation_type' => 'required|in:capture,sale,support,closing,other']);
        $exists = $transaction->activeParticipants()->where('user_id', $validated['user_id'])->where('participation_type', $validated['participation_type'])->exists();
        if ($exists) return back()->with('error', 'Ese asesor ya participa con esa función.');
        AccountingTransactionParticipant::create([
            'accounting_transaction_id' => $transaction->id,
            'user_id' => $validated['user_id'],
            'participation_type' => $validated['participation_type'],
            'distribution_percentage' => null,
            'source' => 'manual',
            'is_active' => true,
        ]);
        if ($validated['participation_type'] === 'sale') {
            $sellers = $transaction->activeParticipants()->where('participation_type', 'sale')->get();
            if ($sellers->count() > 0) {
                $percentage = round(100 / $sellers->count(), 4);
                foreach ($sellers as $seller) $seller->update(['distribution_percentage' => $percentage]);
            }
        }
        return back()->with('success', 'Participante agregado correctamente.');
    }

    public function destroyParticipant(AccountingTransactionParticipant $participant) {
        $transaction = $participant->transaction;
        if (!$transaction) return back()->with('error', 'Operación no encontrada.');
        if ($transaction->status === 'Cerrada') return back()->with('error', 'La operación está cerrada.');
        $wasSeller = $participant->participation_type === 'sale';
        $participant->update(['is_active' => false]);
        if ($wasSeller) {
            $sellers = $transaction->activeParticipants()->where('participation_type', 'sale')->get();
            if ($sellers->count() > 0) {
                $percentage = round(100 / $sellers->count(), 4);
                foreach ($sellers as $seller) $seller->update(['distribution_percentage' => $percentage]);
            }
        }
        return back()->with('success', 'Participante anulado correctamente.');
    }

    private function calculateAutomaticCommissions(AccountingTransaction $transaction): array {
        $brokerageBase = (float) ($transaction->brokerage_amount ?? 0);
        if ($brokerageBase <= 0) return ['configuration' => null,'brokerage_base' => 0,'commissions' => [],'total_advisor_commissions' => 0,'company_retention' => 0];
        $configuration = CommissionConfiguration::activeForDate(now()->toDateString());
        if (!$configuration) return ['configuration' => null,'brokerage_base' => $brokerageBase,'commissions' => [],'total_advisor_commissions' => 0,'company_retention' => $brokerageBase];
        $configuration->loadMissing('activeRules');
        $rules = $configuration->activeRules;
        $participants = $transaction->activeParticipants()->with('user')->get();
        $capturers = $participants->where('participation_type', 'capture')->values();
        $sellers = $participants->where('participation_type', 'sale')->values();
        $commissions = [];
        if ($capturers->count() === 1 && $sellers->count() === 1 && (int) $capturers->first()->user_id === (int) $sellers->first()->user_id) {
            $participant = $sellers->first();
            $rule = $rules->where('participation_type', 'capture_and_sale')->first(function ($rule) { return in_array($rule->capture_origin, ['advisor','any'], true); });
            if ($rule) {
                $percentage = (float) $rule->percentage;
                $amount = round($brokerageBase * ($percentage / 100), 2);
                $commissions[] = ['user_id' => $participant->user_id,'user' => $participant->user,'participation_type' => 'capture_and_sale','role_label' => 'Captación + venta','rule' => $rule,'percentage' => $percentage,'calculation_base' => $brokerageBase,'distribution_percentage' => null,'commission_amount' => $amount];
            }
            $total = collect($commissions)->sum('commission_amount');
            return ['configuration' => $configuration,'brokerage_base' => $brokerageBase,'commissions' => $commissions,'total_advisor_commissions' => round($total, 2),'company_retention' => round(max(0, $brokerageBase - $total), 2)];
        }
        if ($capturers->isNotEmpty()) {
            $captureRule = $rules->where('participation_type', 'capture')->first(function ($rule) { return in_array($rule->capture_origin, ['advisor','any'], true); });
            if ($captureRule) {
                foreach ($capturers as $participant) {
                    $percentage = (float) $captureRule->percentage;
                    $amount = round($brokerageBase * ($percentage / 100), 2);
                    $commissions[] = ['user_id' => $participant->user_id,'user' => $participant->user,'participation_type' => 'capture','role_label' => 'Captación','rule' => $captureRule,'percentage' => $percentage,'calculation_base' => $brokerageBase,'distribution_percentage' => null,'commission_amount' => $amount];
                }
            }
        }
        if ($sellers->isNotEmpty()) {
            $saleRule = $rules->where('participation_type', 'sale')->first(function ($rule) { return in_array($rule->capture_origin, ['any','agency','advisor'], true); });
            if ($saleRule) {
                $salePercentage = (float) $saleRule->percentage;
                $totalSaleCommission = round($brokerageBase * ($salePercentage / 100), 2);
                $sellerCount = $sellers->count();
                foreach ($sellers as $participant) {
                    if ($sellerCount === 1) $distribution = 100;
                    else {
                        $manualDistribution = (float) ($participant->distribution_percentage ?? 0);
                        $distribution = $manualDistribution > 0 ? $manualDistribution : round(100 / $sellerCount, 4);
                    }
                    $amount = round($totalSaleCommission * ($distribution / 100), 2);
                    $effectivePercentage = round($salePercentage * ($distribution / 100), 4);
                    $commissions[] = [
                        'user_id' => $participant->user_id,
                        'user' => $participant->user,
                        'participation_type' => 'sale',
                        'role_label' => 'Venta',
                        'rule' => $saleRule,
                        'percentage' => $effectivePercentage,
                        'sale_rule_percentage' => $salePercentage,
                        'calculation_base' => $brokerageBase,
                        'distribution_percentage' => $sellerCount > 1 ? $distribution : null,
                        'commission_amount' => $amount,
                    ];
                }
            }
        }
        $totalAdvisorCommissions = round(collect($commissions)->sum('commission_amount'), 2);
        $companyRetention = round(max(0, $brokerageBase - $totalAdvisorCommissions), 2);
        return ['configuration' => $configuration,'brokerage_base' => $brokerageBase,'commissions' => $commissions,'total_advisor_commissions' => $totalAdvisorCommissions,'company_retention' => $companyRetention];
    }

    public function closeTransaction(AccountingTransaction $transaction)
{
    if ($transaction->status === 'Cerrada') {
        return redirect()
            ->route('accounting.review', $transaction->id)
            ->with('error', 'Esta operación ya se encuentra cerrada.');
    }

    $transaction->load([
        'invoice',
        'activeParticipants.user'
    ]);

    if (!$transaction->invoice || $transaction->invoice->status !== 'Emitida') {
        return redirect()
            ->route('accounting.invoice.review', $transaction->id)
            ->with(
                'error',
                'Primero debes emitir la factura o comprobante interno.'
            );
    }

    $commissionSummary = $this->calculateAutomaticCommissions($transaction);

    DB::transaction(function () use ($transaction, $commissionSummary) {

        /*
        |--------------------------------------------------------------------------
        | 1. ANULAR COMISIONES ANTERIORES
        |--------------------------------------------------------------------------
        |
        | Conservamos los registros históricos, pero dejan de afectar
        | el resultado contable.
        |
        */

        $transaction->advisorCommissions()
            ->where('status', '!=', 'Anulada')
            ->update([
                'status' => 'Anulada'
            ]);


        /*
        |--------------------------------------------------------------------------
        | 2. GUARDAR COMISIONES DEFINITIVAS
        |--------------------------------------------------------------------------
        */

        foreach ($commissionSummary['commissions'] as $commission) {

            AccountingAdvisorCommission::create([
                'accounting_transaction_id' => $transaction->id,

                'user_id' => $commission['user_id'],

                'role_in_transaction' =>
                    $commission['role_label'],

                'percentage' =>
                    $commission['percentage'],

                'calculation_base' =>
                    $commission['calculation_base'],

                'commission_amount' =>
                    $commission['commission_amount'],

                'status' => 'Calculada',

                'notes' =>
                    'Comisión definitiva generada automáticamente al cerrar la operación.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | 3. RECALCULAR RESULTADOS
        |--------------------------------------------------------------------------
        */

        $transaction->recalculateTotals();

        $transaction->refresh();


        /*
        |--------------------------------------------------------------------------
        | 4. CERRAR OPERACIÓN
        |--------------------------------------------------------------------------
        */

        $transaction->update([
            'status' => 'Cerrada',
            'closed_at' => now(),
        ]);
    });


    return redirect()
        ->route('accounting.review', $transaction->id)
        ->with(
            'success',
            'La operación fue cerrada correctamente y sus comisiones quedaron registradas.'
        );
}
public function invoiceHistory(Request $request)
{
    $search = trim((string) $request->input('search', ''));
    $documentType = $request->input('document_type');
    $status = $request->input('status');

    $query = AccountingInvoice::query()
        ->with([
            'client',
            'transaction.property',
        ])
        ->where('status', 'Emitida');

    if ($search !== '') {
        $query->where(function ($q) use ($search) {
            $q->where('invoice_number', 'like', "%{$search}%")
              ->orWhere('customer_name', 'like', "%{$search}%")
              ->orWhere('identification_number', 'like', "%{$search}%")
              ->orWhere('business_name', 'like', "%{$search}%");
        });
    }

    if (in_array($documentType, ['factura', 'comprobante'], true)) {
        $query->where('document_type', $documentType);
    }

    if (in_array($status, ['abierta', 'cerrada'], true)) {
        $query->whereHas('transaction', function ($q) use ($status) {
            if ($status === 'cerrada') {
                $q->where('status', 'Cerrada');
            } else {
                $q->where('status', '!=', 'Cerrada');
            }
        });
    }

    $invoices = $query
        ->orderByDesc('issued_at')
        ->orderByDesc('id')
        ->paginate(15)
        ->withQueryString();

    $totalDocuments = AccountingInvoice::where('status', 'Emitida')->count();

    $totalInvoices = AccountingInvoice::where('status', 'Emitida')
        ->where('document_type', 'factura')
        ->count();

    $totalReceipts = AccountingInvoice::where('status', 'Emitida')
        ->where('document_type', 'comprobante')
        ->count();

    $totalBilled = AccountingInvoice::where('status', 'Emitida')
        ->sum('total');

    return view('intranet.accounting.invoice_history', compact(
        'invoices',
        'search',
        'documentType',
        'status',
        'totalDocuments',
        'totalInvoices',
        'totalReceipts',
        'totalBilled'
    ));
}
public function showInvoiceDocument(AccountingTransaction $transaction)
{
    $transaction->load(['client', 'property', 'invoice']);
    $invoice = $transaction->invoice;

    if (!$invoice) return redirect()->route('accounting.invoice.customer', $transaction->id)->with('error', 'No existe un documento para esta operación.');
    if ($invoice->status !== 'Emitida') return redirect()->route('accounting.invoice.review', $transaction->id)->with('error', 'Primero debes emitir el documento.');

    return view('intranet.accounting.invoice_document', compact('transaction', 'invoice'));
}

public function storeExpenseGroup(Request $request)
{
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:120'],
        'code' => ['nullable', 'string', 'max:80'],
        'description' => ['nullable', 'string', 'max:500'],
    ]);

    $code = $validated['code'] ?? null;

    if (!$code) {
        $code = Str::upper(
            Str::slug($validated['name'], '_')
        );
    } else {
        $code = Str::upper(
            Str::slug($code, '_')
        );
    }

    $exists = ExpenseGroup::where('code', $code)->exists();

    if ($exists) {
        return back()
            ->withInput()
            ->with('error', 'Ya existe un grupo con ese código.');
    }

    ExpenseGroup::create([
        'name' => $validated['name'],
        'code' => $code,
        'description' => $validated['description'] ?? null,
        'is_active' => true,
    ]);

    return redirect()
        ->route('accounting.expenses')
        ->with('success', 'El grupo de gasto fue creado correctamente.');
}


public function storeExpenseCategory(Request $request)
{
    $validated = $request->validate([
        'expense_group_id' => [
            'required',
            'exists:expense_groups,id',
        ],

        'name' => [
            'required',
            'string',
            'max:120',
        ],

        'code' => [
            'nullable',
            'string',
            'max:80',
        ],

        'expense_type' => [
            'required',
            'in:General,Directo',
        ],

        'description' => [
            'nullable',
            'string',
            'max:500',
        ],
    ]);

    $code = $validated['code'] ?? null;

    if (!$code) {
        $code = Str::upper(
            Str::slug($validated['name'], '_')
        );
    } else {
        $code = Str::upper(
            Str::slug($code, '_')
        );
    }

    $exists = ExpenseCategory::where('code', $code)->exists();

    if ($exists) {
        return back()
            ->withInput()
            ->with('error', 'Ya existe una categoría con ese código.');
    }

    $category = ExpenseCategory::create([
        'expense_group_id' => $validated['expense_group_id'],
        'name' => $validated['name'],
        'code' => $code,
        'expense_type' => $validated['expense_type'],
        'description' => $validated['description'] ?? null,
        'is_active' => true,
    ]);


    /*
    |--------------------------------------------------------------------------
    | Crear automáticamente la subcategoría "Otros"
    |--------------------------------------------------------------------------
    */

    ExpenseSubcategory::create([
        'expense_category_id' => $category->id,
        'name' => 'Otros',
        'code' => 'OTROS',
        'description' => 'Otros gastos relacionados con esta categoría.',
        'is_active' => true,
    ]);


    return redirect()
        ->route('accounting.expenses')
        ->with('success', 'La categoría fue creada correctamente.');
}


public function storeExpenseSubcategory(Request $request)
{
    $validated = $request->validate([
        'expense_category_id' => [
            'required',
            'exists:expense_categories,id',
        ],

        'name' => [
            'required',
            'string',
            'max:120',
        ],

        'code' => [
            'nullable',
            'string',
            'max:80',
        ],

        'description' => [
            'nullable',
            'string',
            'max:500',
        ],
    ]);

    $code = $validated['code'] ?? null;

    if (!$code) {
        $code = Str::upper(
            Str::slug($validated['name'], '_')
        );
    } else {
        $code = Str::upper(
            Str::slug($code, '_')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Evitar subcategorías duplicadas dentro de la misma categoría
    |--------------------------------------------------------------------------
    */

    $exists = ExpenseSubcategory::where(
        'expense_category_id',
        $validated['expense_category_id']
    )
        ->where(function ($query) use ($validated, $code) {

            $query
                ->whereRaw(
                    'LOWER(name) = ?',
                    [mb_strtolower($validated['name'])]
                )
                ->orWhere('code', $code);

        })
        ->exists();


    if ($exists) {
        return back()
            ->withInput()
            ->with('error', 'Esa subcategoría ya existe dentro de la categoría seleccionada.');
    }


    ExpenseSubcategory::create([
        'expense_category_id' => $validated['expense_category_id'],
        'name' => $validated['name'],
        'code' => $code,
        'description' => $validated['description'] ?? null,
        'is_active' => true,
    ]);


    return redirect()
        ->route('accounting.expenses')
        ->with('success', 'La subcategoría fue creada correctamente.');
}
public function transactionReport($transaction)
{
    $transaction = AccountingTransaction::with([
        'client',
        'prospect',
        'tramite',
        'property',
        'advisorCommissions',
        'expenses.category',
        'vehicleTrips',
        'participants',
        'invoice',
    ])->findOrFail($transaction);

    return view(
        'intranet.accounting.transactions.report',
        compact('transaction')
    );
}
public function operations(Request $request)
{
    $status = $request->get('status');
    $type = $request->get('type');
    $search = trim((string) $request->get('search'));

    $query = AccountingTransaction::with([
        'client',
        'prospect',
        'tramite',
    ])->latest('id');

    if ($status) {
        $query->where('status', $status);
    }

    if ($type) {
        $query->where('operation_type', $type);
    }

    if ($search !== '') {
        $query->where(function ($q) use ($search) {

            if (is_numeric($search)) {
                $q->orWhere('id', (int) $search);
            }

            $q->orWhereHas('client', function ($clientQuery) use ($search) {
                $clientQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });

            $q->orWhereHas('prospect', function ($prospectQuery) use ($search) {
                $prospectQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        });
    }

    $operations = $query
        ->paginate(15)
        ->withQueryString();

    $statuses = AccountingTransaction::query()
        ->whereNotNull('status')
        ->distinct()
        ->orderBy('status')
        ->pluck('status');

    $types = AccountingTransaction::query()
        ->whereNotNull('operation_type')
        ->distinct()
        ->orderBy('operation_type')
        ->pluck('operation_type');

    $totalOperations = AccountingTransaction::count();

    $pendingOperations = AccountingTransaction::where('status', 'Pendiente')->count();

    $closedOperations = AccountingTransaction::where('status', 'Cerrada')->count();

    $totalGrossIncome = AccountingTransaction::sum('gross_income');

    $totalNetProfit = AccountingTransaction::sum('net_profit');

    return view('intranet.accounting.operations.index', compact(
        'operations',
        'statuses',
        'types',
        'status',
        'type',
        'search',
        'totalOperations',
        'pendingOperations',
        'closedOperations',
        'totalGrossIncome',
        'totalNetProfit'
    ));
}
public function commissionReport(Request $request)
{
    $advisorId = $request->get('advisor_id');
    $status = $request->get('status');
    $fromDate = $request->get('from_date');
    $toDate = $request->get('to_date');

    $query = AccountingAdvisorCommission::with([
        'advisor',
        'accountingTransaction.client',
        'accountingTransaction.prospect',
    ])->latest('id');

    /*
     * FILTRO POR ASESOR
     */
    if ($advisorId) {
        $query->where('user_id', $advisorId);
    }

    /*
     * FILTRO POR ESTADO
     */
    if ($status) {
        $query->where('status', $status);
    }

    /*
     * FILTRO POR FECHA DE REGISTRO
     */
    if ($fromDate) {
        $query->whereDate('created_at', '>=', $fromDate);
    }

    if ($toDate) {
        $query->whereDate('created_at', '<=', $toDate);
    }

    $commissions = $query
        ->paginate(20)
        ->withQueryString();


    /*
     * QUERY PARA TOTALES
     *
     * Conserva filtros de asesor y fechas,
     * pero no aplica el filtro de estado para
     * poder mostrar Pagadas / Pendientes / Generadas.
     */
    $totalsQuery = AccountingAdvisorCommission::query()
        ->where('status', '!=', 'Anulada');

    if ($advisorId) {
        $totalsQuery->where('user_id', $advisorId);
    }

    if ($fromDate) {
        $totalsQuery->whereDate('created_at', '>=', $fromDate);
    }

    if ($toDate) {
        $totalsQuery->whereDate('created_at', '<=', $toDate);
    }


    $totalGenerated = (clone $totalsQuery)
        ->sum('commission_amount');

    $totalPaid = (clone $totalsQuery)
        ->where('status', 'Pagada')
        ->sum('commission_amount');

    $totalPending = (clone $totalsQuery)
        ->where('status', 'Pendiente')
        ->sum('commission_amount');

    $commissionCount = (clone $totalsQuery)->count();


    /*
     * SOLO ASESORES QUE YA TIENEN COMISIONES
     */
    $advisorIds = AccountingAdvisorCommission::query()
        ->whereNotNull('user_id')
        ->distinct()
        ->pluck('user_id');

    $advisors = User::whereIn('id', $advisorIds)
        ->orderBy('name')
        ->orderBy('last_name')
        ->get();


    /*
     * ESTADOS EXISTENTES EN BD
     */
    $statuses = AccountingAdvisorCommission::query()
        ->whereNotNull('status')
        ->distinct()
        ->orderBy('status')
        ->pluck('status');


    return view(
        'intranet.accounting.commissions.report',
        compact(
            'commissions',
            'advisors',
            'statuses',
            'advisorId',
            'status',
            'fromDate',
            'toDate',
            'totalGenerated',
            'totalPaid',
            'totalPending',
            'commissionCount'
        )
    );
}

public function pyg(Request $request)
{
    $month = (int) ($request->get('month') ?: now()->month);
    $year = (int) ($request->get('year') ?: now()->year);

    $fromDate = $request->get('from_date');
    $toDate = $request->get('to_date');

    /*
    |--------------------------------------------------------------------------
    | 1. Definir período
    |--------------------------------------------------------------------------
    */

    if ($fromDate && $toDate) {

        if ($fromDate > $toDate) {
            return back()->with('error', 'La fecha inicial no puede ser mayor que la fecha final.');
        }

        $filterType = 'range';

    } else {

        $filterType = 'month';

        $fromDate = null;
        $toDate = null;
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Operaciones cerradas
    |--------------------------------------------------------------------------
    */

    $transactionsQuery = AccountingTransaction::query()
        ->where('status', 'Cerrada');

    if ($filterType === 'range') {

        $transactionsQuery
            ->whereDate('closed_at', '>=', $fromDate)
            ->whereDate('closed_at', '<=', $toDate);

    } else {

        $transactionsQuery
            ->whereYear('closed_at', $year)
            ->whereMonth('closed_at', $month);
    }

    $transactions = $transactionsQuery
        ->orderBy('closed_at')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | 3. INGRESOS
    |--------------------------------------------------------------------------
    */

    $brokerageIncome = $transactions->sum(function ($transaction) {
        return (float) ($transaction->brokerage_amount ?? 0);
    });

    $serviceIncome = $transactions->sum(function ($transaction) {
        return (float) ($transaction->service_amount ?? 0);
    });

    $grossIncome = $brokerageIncome + $serviceIncome;

    /*
    |--------------------------------------------------------------------------
    | 4. IVA
    |--------------------------------------------------------------------------
    */

    $ivaPercentage = 15;

    $ivaAmount = $grossIncome * ($ivaPercentage / 100);

    $netIncome = $grossIncome - $ivaAmount;

    /*
    |--------------------------------------------------------------------------
    | 5. COSTOS DIRECTOS DE OPERACIONES
    |--------------------------------------------------------------------------
    */

    $directExpenses = $transactions->sum(function ($transaction) {
        return (float) ($transaction->direct_expenses_total ?? 0);
    });

    /*
    |--------------------------------------------------------------------------
    | 6. COMISIONES DE ASESORES
    |--------------------------------------------------------------------------
    */

    $advisorCommissions = $transactions->sum(function ($transaction) {
        return (float) ($transaction->advisor_commissions_total ?? 0);
    });

    /*
    |--------------------------------------------------------------------------
    | 7. GASTOS REALES DEL MÓDULO DE GASTOS
    |--------------------------------------------------------------------------
    |
    | Se cargan dinámicamente los grupos existentes en la base.
    | Si mañana se crea "Seguridad", aparecerá automáticamente.
    |
    */

    $expenseGroups = ExpenseGroup::query()
        ->where('is_active', true)
        ->with([
            'categories' => function ($categoryQuery) {
                $categoryQuery
                    ->where('is_active', true)
                    ->orderBy('name');
            },
            'categories.subcategories' => function ($subcategoryQuery) {
                $subcategoryQuery
                    ->where('is_active', true)
                    ->orderBy('name');
            },
        ])
        ->orderBy('name')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | 8. Movimientos del período
    |--------------------------------------------------------------------------
    */

    $movementsQuery = AccountingExpenseMovement::query()
        ->with([
            'category',
            'subcategory',
        ]);

    if ($filterType === 'range') {

        $movementsQuery
            ->whereDate('expense_date', '>=', $fromDate)
            ->whereDate('expense_date', '<=', $toDate);

    } else {

        $movementsQuery
            ->whereYear('expense_date', $year)
            ->whereMonth('expense_date', $month);
    }

    $expenseMovements = $movementsQuery
        ->orderBy('expense_date')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | 9. Consolidar grupos dinámicamente
    |--------------------------------------------------------------------------
    */

    $expenseGroupsData = $expenseGroups->map(function ($group) use (
        $expenseMovements,
        $grossIncome
    ) {

        $categoriesData = $group->categories->map(function ($category) use (
            $expenseMovements,
            $grossIncome
        ) {

            $categoryMovements = $expenseMovements
                ->where('expense_category_id', $category->id);

            $subcategoriesData = $category->subcategories->map(
                function ($subcategory) use ($categoryMovements, $grossIncome) {

                    $subcategoryMovements = $categoryMovements
                        ->where('expense_subcategory_id', $subcategory->id);

                    $subcategoryTotal = $subcategoryMovements->sum(function ($movement) {
                        return (float) ($movement->amount ?? 0);
                    });

                    $subcategoryPercentage = $grossIncome > 0
                        ? ($subcategoryTotal / $grossIncome) * 100
                        : 0;

                    return [
                        'id' => $subcategory->id,
                        'name' => $subcategory->name,
                        'total' => $subcategoryTotal,
                        'percentage' => $subcategoryPercentage,
                        'movements' => $subcategoryMovements->values(),
                    ];
                }
            );

            /*
             * Movimientos registrados directamente en una categoría
             * sin subcategoría.
             */
            $withoutSubcategory = $categoryMovements
                ->whereNull('expense_subcategory_id');

            $withoutSubcategoryTotal = $withoutSubcategory->sum(function ($movement) {
                return (float) ($movement->amount ?? 0);
            });

            $categoryTotal = $categoryMovements->sum(function ($movement) {
                return (float) ($movement->amount ?? 0);
            });

            $categoryPercentage = $grossIncome > 0
                ? ($categoryTotal / $grossIncome) * 100
                : 0;

            return [
                'id' => $category->id,
                'name' => $category->name,
                'total' => $categoryTotal,
                'percentage' => $categoryPercentage,

                'without_subcategory_total' => $withoutSubcategoryTotal,
                'without_subcategory_movements' => $withoutSubcategory->values(),

                'subcategories' => $subcategoriesData,
            ];
        });

        $groupTotal = $categoriesData->sum('total');

        $groupPercentage = $grossIncome > 0
            ? ($groupTotal / $grossIncome) * 100
            : 0;

        return [
            'id' => $group->id,
            'name' => $group->name,
            'total' => $groupTotal,
            'percentage' => $groupPercentage,
            'categories' => $categoriesData,
        ];
    });

    /*
    |--------------------------------------------------------------------------
    | 10. TOTAL GASTOS REALES
    |--------------------------------------------------------------------------
    */

    $realExpensesTotal = $expenseGroupsData->sum('total');

    /*
    |--------------------------------------------------------------------------
    | 11. TOTAL COSTOS Y GASTOS
    |--------------------------------------------------------------------------
    |
    | Por ahora:
    |
    | - gastos directos de operaciones
    | - comisiones asesores
    | - movimientos reales del módulo Gastos
    |
    */

    $totalExpenses =
        $directExpenses
        + $advisorCommissions
        + $realExpensesTotal;

    /*
    |--------------------------------------------------------------------------
    | 12. RESULTADO FINAL
    |--------------------------------------------------------------------------
    */

    $netProfit = $netIncome - $totalExpenses;

    /*
    |--------------------------------------------------------------------------
    | 13. PORCENTAJES
    |--------------------------------------------------------------------------
    */

    $ivaPercent = $grossIncome > 0
        ? ($ivaAmount / $grossIncome) * 100
        : 0;

    $netIncomePercent = $grossIncome > 0
        ? ($netIncome / $grossIncome) * 100
        : 0;

    $directExpensesPercent = $grossIncome > 0
        ? ($directExpenses / $grossIncome) * 100
        : 0;

    $advisorCommissionsPercent = $grossIncome > 0
        ? ($advisorCommissions / $grossIncome) * 100
        : 0;

    $realExpensesPercent = $grossIncome > 0
        ? ($realExpensesTotal / $grossIncome) * 100
        : 0;

    $totalExpensesPercent = $grossIncome > 0
        ? ($totalExpenses / $grossIncome) * 100
        : 0;

    $netProfitPercent = $grossIncome > 0
        ? ($netProfit / $grossIncome) * 100
        : 0;

    /*
    |--------------------------------------------------------------------------
    | 14. Cantidad de negocios
    |--------------------------------------------------------------------------
    */

    $closedBusinessCount = $transactions->count();

    /*
    |--------------------------------------------------------------------------
    | 15. Agrupar operaciones por tipo
    |--------------------------------------------------------------------------
    */

    $businessTypes = $transactions
        ->groupBy(function ($transaction) {
            return $transaction->operation_type ?: 'Sin especificar';
        })
        ->map(function ($items, $type) use ($grossIncome) {

            $income = $items->sum(function ($transaction) {
                return (float) ($transaction->gross_income ?? 0);
            });

            return [
                'type' => $type,
                'count' => $items->count(),
                'income' => $income,
                'percentage' => $grossIncome > 0
                    ? ($income / $grossIncome) * 100
                    : 0,
                'transactions' => $items->values(),
            ];
        })
        ->values();

    return view(
        'intranet.accounting.pyg.index',
        compact(
            'month',
            'year',
            'fromDate',
            'toDate',
            'filterType',

            'transactions',

            'brokerageIncome',
            'serviceIncome',
            'grossIncome',

            'ivaPercentage',
            'ivaAmount',
            'netIncome',

            'directExpenses',
            'advisorCommissions',

            'expenseGroupsData',
            'realExpensesTotal',

            'totalExpenses',
            'netProfit',

            'ivaPercent',
            'netIncomePercent',
            'directExpensesPercent',
            'advisorCommissionsPercent',
            'realExpensesPercent',
            'totalExpensesPercent',
            'netProfitPercent',

            'closedBusinessCount',
            'businessTypes'
        )
    );
}
    }
