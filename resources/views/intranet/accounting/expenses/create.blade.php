@extends('layouts.admin')

@section('admin_content')

<div class="min-h-screen bg-slate-50 py-8">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">


        {{-- ========================================================= --}}
        {{-- ENCABEZADO --}}
        {{-- ========================================================= --}}

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-7">

            <div class="flex items-center gap-3">

                <div class="w-11 h-11 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                    <i class="fa-solid fa-receipt text-lg"></i>
                </div>

                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900">
                        Registrar gasto
                    </h1>

                    <p class="text-sm text-slate-500 mt-1">
                        Registra y clasifica los gastos reales de la inmobiliaria.
                    </p>
                </div>

            </div>


            <a
                href="{{ route('accounting.expenses') }}"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold transition"
            >
                <i class="fa-solid fa-arrow-left"></i>
                Volver a Gastos
            </a>

        </div>



        {{-- ========================================================= --}}
        {{-- ERRORES --}}
        {{-- ========================================================= --}}

        @if ($errors->any())

            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4">

                <div class="flex items-start gap-3">

                    <i class="fa-solid fa-circle-exclamation text-red-600 mt-1"></i>

                    <div>

                        <div class="font-bold text-red-800 mb-2">
                            Revisa la información ingresada
                        </div>

                        <ul class="list-disc pl-5 text-sm text-red-700 space-y-1">

                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach

                        </ul>

                    </div>

                </div>

            </div>

        @endif



        {{-- ========================================================= --}}
        {{-- FORMULARIO --}}
        {{-- ========================================================= --}}

        <form
            action="{{ route('accounting.movements.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden"
        >

            @csrf



            {{-- ===================================================== --}}
            {{-- INFORMACIÓN DEL GASTO --}}
            {{-- ===================================================== --}}

            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">

                <div class="flex items-center gap-3">

                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center">
                        <i class="fa-solid fa-file-invoice-dollar text-sm"></i>
                    </div>

                    <div>

                        <h2 class="font-extrabold text-slate-900">
                            Información del gasto
                        </h2>

                        <p class="text-xs text-slate-500 mt-0.5">
                            Selecciona grupo, categoría y subcategoría.
                        </p>

                    </div>

                </div>

            </div>



            <div class="p-6">


                {{-- ================================================= --}}
                {{-- CLASIFICACIÓN --}}
                {{-- ================================================= --}}

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">


                    {{-- GRUPO --}}
                    <div>

                        <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                            Grupo de gasto *
                        </label>

                        <select
                            id="expense_group_id"
                            required
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:border-emerald-500 focus:ring-emerald-500"
                        >

                            <option value="">
                                Seleccionar grupo
                            </option>

                            @foreach($groups as $group)

                                <option value="{{ $group->id }}">
                                    {{ $group->name }}
                                </option>

                            @endforeach

                        </select>

                        <p class="text-[11px] text-slate-400 mt-1.5">
                            Selecciona el grupo principal del gasto.
                        </p>

                    </div>



                    {{-- CATEGORÍA --}}
                    <div>

                        <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                            Categoría *
                        </label>

                        <select
                            name="expense_category_id"
                            id="expense_category_id"
                            required
                            disabled
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:border-emerald-500 focus:ring-emerald-500 disabled:bg-slate-100 disabled:text-slate-400"
                        >

                            <option value="">
                                Primero selecciona un grupo
                            </option>

                            @foreach($categories as $category)

                                <option
                                    value="{{ $category->id }}"
                                    data-group="{{ $category->expense_group_id }}"
                                    {{ old('expense_category_id') == $category->id ? 'selected' : '' }}
                                >
                                    {{ $category->name }}
                                </option>

                            @endforeach

                        </select>

                        <p class="text-[11px] text-slate-400 mt-1.5">
                            Se mostrarán las categorías del grupo seleccionado.
                        </p>

                    </div>



                    {{-- SUBCATEGORÍA --}}
                    <div>

                        <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                            Subcategoría
                        </label>

                        <select
                            name="expense_subcategory_id"
                            id="expense_subcategory_id"
                            disabled
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:border-emerald-500 focus:ring-emerald-500 disabled:bg-slate-100 disabled:text-slate-400"
                        >

                            <option value="">
                                Primero selecciona una categoría
                            </option>

                            @foreach($subcategories as $subcategory)

                                <option
                                    value="{{ $subcategory->id }}"
                                    data-category="{{ $subcategory->expense_category_id }}"
                                    {{ old('expense_subcategory_id') == $subcategory->id ? 'selected' : '' }}
                                >
                                    {{ $subcategory->name }}
                                </option>

                            @endforeach

                        </select>

                        <p class="text-[11px] text-slate-400 mt-1.5">
                            Incluye la opción Otros cuando corresponda.
                        </p>

                    </div>

                </div>



                {{-- ================================================= --}}
                {{-- DATOS PRINCIPALES --}}
                {{-- ================================================= --}}

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">


                    {{-- CONCEPTO --}}
                    <div class="lg:col-span-2">

                        <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                            Concepto *
                        </label>

                        <input
                            type="text"
                            name="concept"
                            value="{{ old('concept') }}"
                            required
                            maxlength="255"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:border-emerald-500 focus:ring-emerald-500"
                            placeholder="Ej. Pago de agua agosto"
                        >

                    </div>



                    {{-- VALOR --}}
                    <div>

                        <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                            Valor *
                        </label>

                        <div class="relative">

                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">
                                $
                            </span>

                            <input
                                type="number"
                                name="amount"
                                value="{{ old('amount') }}"
                                step="0.01"
                                min="0.01"
                                required
                                class="w-full border border-slate-300 rounded-xl pl-8 pr-4 py-3 focus:border-emerald-500 focus:ring-emerald-500"
                                placeholder="0.00"
                            >

                        </div>

                    </div>



                    {{-- FECHA --}}
                    <div>

                        <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                            Fecha del gasto *
                        </label>

                        <input
                            type="date"
                            name="expense_date"
                            value="{{ old('expense_date', now()->toDateString()) }}"
                            required
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:border-emerald-500 focus:ring-emerald-500"
                        >

                    </div>



                    {{-- PROVEEDOR --}}
                    <div class="md:col-span-2 lg:col-span-4">

                        <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                            Proveedor / Beneficiario
                        </label>

                        <input
                            type="text"
                            name="provider"
                            value="{{ old('provider') }}"
                            maxlength="180"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:border-emerald-500 focus:ring-emerald-500"
                            placeholder="Ej. Empresa Eléctrica Riobamba"
                        >

                    </div>

                </div>

            </div>



            {{-- ===================================================== --}}
            {{-- COMPROBANTE --}}
            {{-- ===================================================== --}}

            <div class="border-t border-slate-200 px-6 py-5">

                <div class="mb-5">

                    <h3 class="font-extrabold text-slate-900">
                        Comprobante / factura
                    </h3>

                    <p class="text-xs text-slate-500 mt-1">
                        Información documental que respalda el gasto.
                    </p>

                </div>


                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">


                    {{-- TIPO --}}
                    <div>

                        <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                            Tipo de documento
                        </label>

                        <select
                            name="document_type"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:border-emerald-500 focus:ring-emerald-500"
                        >

                            <option value="">Sin documento</option>

                            <option value="Factura"
                                {{ old('document_type') === 'Factura' ? 'selected' : '' }}>
                                Factura
                            </option>

                            <option value="Nota de venta"
                                {{ old('document_type') === 'Nota de venta' ? 'selected' : '' }}>
                                Nota de venta
                            </option>

                            <option value="Recibo"
                                {{ old('document_type') === 'Recibo' ? 'selected' : '' }}>
                                Recibo
                            </option>

                            <option value="Comprobante"
                                {{ old('document_type') === 'Comprobante' ? 'selected' : '' }}>
                                Comprobante
                            </option>

                            <option value="Otro"
                                {{ old('document_type') === 'Otro' ? 'selected' : '' }}>
                                Otro
                            </option>

                        </select>

                    </div>



                    {{-- NÚMERO --}}
                    <div>

                        <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                            Número de documento
                        </label>

                        <input
                            type="text"
                            name="document_number"
                            value="{{ old('document_number') }}"
                            maxlength="120"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:border-emerald-500 focus:ring-emerald-500"
                            placeholder="Ej. 001-001-000000125"
                        >

                    </div>



                    {{-- ARCHIVO --}}
                    <div>

                        <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                            Archivo del comprobante
                        </label>

                        <input
                            type="file"
                            name="document_file"
                            accept=".pdf,.jpg,.jpeg,.png"
                            class="w-full border border-slate-300 rounded-xl px-3 py-2.5 bg-white text-sm"
                        >

                        <p class="text-[11px] text-slate-400 mt-1.5">
                            PDF, JPG, JPEG o PNG. Máximo 5 MB.
                        </p>

                    </div>



                    {{-- PRESUPUESTADO --}}
                    <div>

                        <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                            Presupuestado
                        </label>

                        <select
                            name="was_budgeted"
                            required
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:border-emerald-500 focus:ring-emerald-500"
                        >

                            <option value="1"
                                {{ old('was_budgeted', '1') == '1' ? 'selected' : '' }}>
                                Sí
                            </option>

                            <option value="0"
                                {{ old('was_budgeted') == '0' ? 'selected' : '' }}>
                                No — gasto no previsto
                            </option>

                        </select>

                    </div>

                </div>

            </div>



            {{-- ===================================================== --}}
            {{-- INFORMACIÓN DE PAGO --}}
            {{-- ===================================================== --}}

            <div class="border-t border-slate-200 px-6 py-5">

                <div class="mb-5">

                    <h3 class="font-extrabold text-slate-900">
                        Información de pago
                    </h3>

                    <p class="text-xs text-slate-500 mt-1">
                        Estado y medio utilizado para cancelar el gasto.
                    </p>

                </div>


                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">


                    {{-- ESTADO --}}
                    <div>

                        <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                            Estado *
                        </label>

                        <select
                            name="payment_status"
                            required
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:border-emerald-500 focus:ring-emerald-500"
                        >

                            <option
                                value="Pendiente"
                                {{ old('payment_status', 'Pendiente') === 'Pendiente' ? 'selected' : '' }}
                            >
                                Pendiente
                            </option>

                            <option
                                value="Pagado"
                                {{ old('payment_status') === 'Pagado' ? 'selected' : '' }}
                            >
                                Pagado
                            </option>

                        </select>

                    </div>



                    {{-- MÉTODO --}}
                    <div>

                        <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                            Método
                        </label>

                        <select
                            name="payment_method"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:border-emerald-500 focus:ring-emerald-500"
                        >

                            <option value="">Sin especificar</option>

                            <option value="Efectivo"
                                {{ old('payment_method') === 'Efectivo' ? 'selected' : '' }}>
                                Efectivo
                            </option>

                            <option value="Transferencia"
                                {{ old('payment_method') === 'Transferencia' ? 'selected' : '' }}>
                                Transferencia
                            </option>

                            <option value="Tarjeta"
                                {{ old('payment_method') === 'Tarjeta' ? 'selected' : '' }}>
                                Tarjeta
                            </option>

                            <option value="Cheque"
                                {{ old('payment_method') === 'Cheque' ? 'selected' : '' }}>
                                Cheque
                            </option>

                            <option value="Otro"
                                {{ old('payment_method') === 'Otro' ? 'selected' : '' }}>
                                Otro
                            </option>

                        </select>

                    </div>



                    {{-- REFERENCIA --}}
                    <div>

                        <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                            Referencia
                        </label>

                        <input
                            type="text"
                            name="payment_reference"
                            value="{{ old('payment_reference') }}"
                            maxlength="150"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:border-emerald-500 focus:ring-emerald-500"
                            placeholder="Transferencia, cheque, etc."
                        >

                    </div>

                </div>

            </div>



            {{-- ===================================================== --}}
            {{-- OBSERVACIONES --}}
            {{-- ===================================================== --}}

            <div class="border-t border-slate-200 px-6 py-5">

                <label class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-2">
                    Observaciones
                </label>

                <textarea
                    name="notes"
                    rows="3"
                    maxlength="5000"
                    class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:border-emerald-500 focus:ring-emerald-500"
                    placeholder="Información adicional del gasto..."
                >{{ old('notes') }}</textarea>

            </div>



            {{-- ===================================================== --}}
            {{-- BOTONES --}}
            {{-- ===================================================== --}}

            <div class="border-t border-slate-200 bg-slate-50 px-6 py-5 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">

                <a
                    href="{{ route('accounting.expenses') }}"
                    class="inline-flex justify-center items-center gap-2 px-5 py-3 rounded-xl border border-slate-300 bg-white text-slate-700 font-bold hover:bg-slate-100 transition"
                >
                    Cancelar
                </a>


                <button
                    type="submit"
                    class="inline-flex justify-center items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-xl font-bold shadow-sm transition"
                >

                    <i class="fa-solid fa-floppy-disk"></i>

                    Registrar gasto

                </button>

            </div>

        </form>

    </div>

</div>



{{-- ============================================================= --}}
{{-- FILTRO DINÁMICO --}}
{{-- GRUPO → CATEGORÍA → SUBCATEGORÍA --}}
{{-- ============================================================= --}}

<script>
document.addEventListener('DOMContentLoaded', function () {

    const groupSelect = document.getElementById('expense_group_id');
    const categorySelect = document.getElementById('expense_category_id');
    const subcategorySelect = document.getElementById('expense_subcategory_id');

    if (!groupSelect || !categorySelect || !subcategorySelect) {
        return;
    }


    const categoryOptions = Array.from(
        categorySelect.querySelectorAll('option[data-group]')
    );

    const subcategoryOptions = Array.from(
        subcategorySelect.querySelectorAll('option[data-category]')
    );


    const oldCategory = @json(old('expense_category_id'));
    const oldSubcategory = @json(old('expense_subcategory_id'));



    /*
    |--------------------------------------------------------------------------
    | Recuperar grupo después de error de validación
    |--------------------------------------------------------------------------
    */

    if (oldCategory) {

        const previousCategory = categoryOptions.find(function(option) {
            return String(option.value) === String(oldCategory);
        });

        if (previousCategory) {
            groupSelect.value = previousCategory.dataset.group;
        }
    }



    /*
    |--------------------------------------------------------------------------
    | FILTRAR CATEGORÍAS
    |--------------------------------------------------------------------------
    */

    function filterCategories(preserveOld = false) {

        const groupId = groupSelect.value;

        categorySelect.disabled = !groupId;


        categoryOptions.forEach(function(option) {

            const visible =
                String(option.dataset.group) === String(groupId);

            option.hidden = !visible;

            if (!visible && option.selected) {
                option.selected = false;
            }

        });


        if (!groupId) {

            categorySelect.value = '';
            categorySelect.disabled = true;

            subcategorySelect.value = '';
            subcategorySelect.disabled = true;

            filterSubcategories();

            return;
        }


        if (
            preserveOld &&
            oldCategory &&
            categoryOptions.some(function(option) {

                return (
                    String(option.value) === String(oldCategory) &&
                    String(option.dataset.group) === String(groupId)
                );

            })
        ) {

            categorySelect.value = oldCategory;

        } else {

            categorySelect.value = '';

        }


        filterSubcategories(preserveOld);
    }



    /*
    |--------------------------------------------------------------------------
    | FILTRAR SUBCATEGORÍAS
    |--------------------------------------------------------------------------
    */

    function filterSubcategories(preserveOld = false) {

        const categoryId = categorySelect.value;

        subcategorySelect.disabled = !categoryId;


        subcategoryOptions.forEach(function(option) {

            const visible =
                String(option.dataset.category) === String(categoryId);

            option.hidden = !visible;

            if (!visible && option.selected) {
                option.selected = false;
            }

        });


        if (!categoryId) {

            subcategorySelect.value = '';
            subcategorySelect.disabled = true;

            return;
        }


        if (
            preserveOld &&
            oldSubcategory &&
            subcategoryOptions.some(function(option) {

                return (
                    String(option.value) === String(oldSubcategory) &&
                    String(option.dataset.category) === String(categoryId)
                );

            })
        ) {

            subcategorySelect.value = oldSubcategory;

        } else {

            subcategorySelect.value = '';

        }
    }



    /*
    |--------------------------------------------------------------------------
    | EVENTOS
    |--------------------------------------------------------------------------
    */

    groupSelect.addEventListener('change', function () {

        filterCategories(false);

    });


    categorySelect.addEventListener('change', function () {

        filterSubcategories(false);

    });



    /*
    |--------------------------------------------------------------------------
    | ESTADO INICIAL
    |--------------------------------------------------------------------------
    */

    if (oldCategory) {

        filterCategories(true);

    } else {

        categorySelect.disabled = true;
        subcategorySelect.disabled = true;

    }

});
</script>

@endsection