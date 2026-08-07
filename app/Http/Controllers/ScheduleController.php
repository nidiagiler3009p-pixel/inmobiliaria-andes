<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Property;
use App\Models\Client;

class ScheduleController extends Controller
{
    // Mostrar agenda de eventos y citas
    public function index()
    {
        $schedules = Schedule::with(['user', 'property', 'client'])->latest()->paginate(10);
        return view('intranet.schedules.index', compact('schedules'));
    }

    // Formulario para agendar una cita o evento
    public function create()
    {
        $users = User::all();
        $properties = Property::all();
        $clients = Client::all();
        return view('intranet.schedules.create', compact('users', 'properties', 'clients'));
    }

    // Guardar cita
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'event_type' => 'required|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time',
            'status' => 'required|string',
        ]);

        Schedule::create($request->all());

        return redirect()->route('schedules.index')->with('success', '¡Cita o evento agendado con éxito!');
    }

    // Actualizar cita
    public function update(Request $request, Schedule $schedule)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'start_time' => 'required|date',
            'status' => 'required|string',
        ]);

        $schedule->update($request->all()); 

        return redirect()->route('schedules.index')->with('success', '¡Agenda actualizada correctamente!');
    }

    // Eliminar cita
    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('schedules.index')->with('success', 'Evento eliminado de la agenda.');
    }
}