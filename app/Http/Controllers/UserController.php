<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // Listar todos los usuarios/asesores en la Intranet
    public function index()
    {
        $users = User::latest()->paginate(10);
        return view('intranet.users.index', compact('users'));
    }

    // Mostrar formulario para registrar un nuevo miembro del equipo
    public function create()
    {
        return view('intranet.users.create');
    }

    // Guardar el nuevo usuario y gestionar su archivo CV si lo sube
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'profession' => 'required|string|max:100',
            'experience_years' => 'required|string|max:50',
            'role' => 'required|in:Trámites,Asesor,Contador,Publicista,Administrador/Gerente',
            'monthly_goal' => 'required|integer',
            'cv_file' => 'nullable|mimes:pdf|max:5120', // Máx 5MB y formato PDF
        ]);

        $data = $request->except(['password', 'cv_file']);
        $data['password'] = Hash::make($request->password);

        // Subir CV corporativo si se adjunta
        if ($request->hasFile('cv_file')) {
            $data['cv_file_path'] = $request->file('cv_file')->store('cvs', 'public');
        }

        User::create($data);

        return redirect()->route('users.index')->with('success', '¡Personal registrado con éxito en la plataforma!');
    }

    // Ver perfil detallado del usuario (metas, propiedades a cargo, etc.)
    public function show(User $user)
    {
        $user->load(['properties', 'clients', 'tramites', 'socialLinks']);
        return view('intranet.users.show', compact('user'));
    }

    // Mostrar formulario de edición
    public function edit(User $user)
    {
        return view('intranet.users.edit', compact('user'));
    }

    // Actualizar datos del usuario
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:Trámites,Asesor,Contador,Publicista,Administrador/Gerente',
            'monthly_goal' => 'required|integer',
            'cv_file' => 'nullable|mimes:pdf|max:5120',
        ]);

        $data = $request->except(['password', 'cv_file']);

        // Si actualizan la contraseña
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Si suben un nuevo CV, reemplazamos el anterior
        if ($request->hasFile('cv_file')) {
            if ($user->cv_file_path) {
                Storage::disk('public')->delete($user->cv_file_path);
            }
            $data['cv_file_path'] = $request->file('cv_file')->store('cvs', 'public');
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', '¡Información del usuario actualizada correctamente!');
    }

    // Dar de baja o eliminar un usuario
    public function destroy(User $user)
    {
        if ($user->cv_file_path) {
            Storage::disk('public')->delete($user->cv_file_path);
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Usuario eliminado del sistema.');
    }
}