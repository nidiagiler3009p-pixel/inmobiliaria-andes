<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccountingExpense;
use App\Models\SalesAccounting;
use App\Models\Property;
use App\Models\Client;
use App\Models\User;

class AccountingController extends Controller
{
    // Mostrar resumen de contabilidad (gastos y ventas)
    public function index()
    {
        $expenses = AccountingExpense::with('user')->latest()->paginate(10);
        $sales = SalesAccounting::with(['property', 'client', 'user'])->latest()->paginate(10);
        
        return view('intranet.accounting.index', compact('expenses', 'sales'));
    }

    // --- GESTIÓN DE GASTOS ---

    public function createExpense()
    {
        return view('intranet.accounting.expenses_create');
    }

    public function storeExpense(Request $request)
    {
        $request->validate([
            'concept' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'expense_date' => 'required|date',
            'category' => 'required|string',
        ]);

        AccountingExpense::create([
            'user_id' => auth()->id() ?? 1,
            'concept' => $request->concept,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'category' => $request->category,
            'notes' => $request->notes,
        ]);

        return redirect()->route('accounting.index')->with('success', '¡Gasto registrado correctamente!');
    }

    // --- GESTIÓN DE VENTAS Y COMISIONES ---

    public function createSale()
    {
        $properties = Property::where('status', 'Disponible')->get();
        $clients = Client::all();
        $users = User::all();
        return view('intranet.accounting.sales_create', compact('properties', 'clients', 'users'));
    }

    public function storeSale(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'client_id' => 'required|exists:clients,id',
            'sale_price' => 'required|numeric',
            'commission' => 'required|numeric',
            'sale_date' => 'required|date',
        ]);

        SalesAccounting::create($request->all());

        // Opcional: Cambiar el estado de la propiedad a 'Vendido' automáticamente
        Property::where('id', $request->property_id)->update(['status' => 'Vendido']);

        return redirect()->route('accounting.index')->with('success', '¡Venta y comisión registrada con éxito!');
    }
}