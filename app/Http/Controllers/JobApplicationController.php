<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail; // Importante para enviar correos

class JobApplicationController extends Controller
{
    // --- PARTE PÚBLICA ---

    // Guardar la postulación que llega desde la web
    public function store(Request $request)
    {
        $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'celular' => 'required|string|max:50',
            'correo' => 'required|email|max:255|unique:users,email',
            'profesion' => 'required|string|max:255',
            'ciudad' => 'required|string|max:255',
            'experiencia' => 'required|string',
            'cv' => 'required|mimes:pdf|max:5120', // Máx 5MB en PDF
        ]);

        $cvPath = null;
        if ($request->hasFile('cv')) {
            $cvPath = $request->file('cv')->store('cvs_postulantes', 'public');
        }

        $jobApplication = JobApplication::create([
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'celular' => $request->celular,
            'correo' => $request->correo,
            'profesion' => $request->profesion,
            'ciudad' => $request->ciudad,
            'experiencia' => $request->experiencia,
            'cv_path' => $cvPath,
        ]);

        // Enviar correo electrónico de notificación con el PDF adjunto
        try {
            $cvFullPath = storage_path('app/public/' . $cvPath);

            $contenidoMensaje = "Has recibido una nueva postulación de trabajo:\n\n" .
                                "Nombres: {$jobApplication->nombres} {$jobApplication->apellidos}\n" .
                                "Celular: {$jobApplication->celular}\n" .
                                "Correo: {$jobApplication->correo}\n" .
                                "Profesión / Área: {$jobApplication->profesion}\n" .
                                "Ciudad: {$jobApplication->ciudad}\n" .
                                "Experiencia: {$jobApplication->experiencia}\n";

            Mail::raw($contenidoMensaje, function ($message) use ($jobApplication, $cvFullPath) {
                $message->to('inmobiliarialosandesecuador@gmail.com')
                        ->subject('Nueva Postulación de Empleo: ' . $jobApplication->nombres . ' ' . $jobApplication->apellidos);
                
                // Adjuntar el archivo PDF de la hoja de vida
                if ($cvFullPath && file_exists($cvFullPath)) {
                    $message->attach($cvFullPath);
                }
            });
        } catch (\Exception $e) {
            // Manejo de errores de correo si llega a fallar la conexión SMTP
        }

        return redirect()->back()->with('success', '¡Tu postulación ha sido enviada con éxito! Nos pondremos en contacto contigo.');
    }

    // --- PARTE DE LA INTRANET ---

    // Listar postulaciones en la Intranet para que el admin las revise
    public function indexIntranet()
    {
        $applications = JobApplication::latest()->paginate(10);
        return view('intranet.applications.index', compact('applications'));
    }

    // Acción de Contratar (Pasa los datos a la tabla users y borra la postulación)
    public function contratar(JobApplication $application)
    {
        $user = User::create([
            'name' => $application->nombres,
            'last_name' => $application->apellidos,
            'email' => $application->correo,
            'phone' => $application->celular,
            'city' => $application->ciudad,
            'profession' => $application->profesion,
            'experience_years' => $application->experiencia,
            'cv_file_path' => $application->cv_path, // Reutilizamos su PDF guardado
            'password' => Hash::make('Temporal2026*'), // Contraseña temporal
            'role' => 'Asesor', // Rol por defecto
            'monthly_goal' => 0,
            'status' => 'Activo',
        ]);

        // Eliminamos de la lista de postulantes ya que ahora es empleado
        $application->delete();

        return redirect()->route('intranet.users.edit', $user->id)
            ->with('success', '¡Postulante contratado con éxito! Ajusta su rol y meta mensual.');
    }

    // Eliminar postulante rechazado
    public function destroy(JobApplication $application)
    {
        if ($application->cv_path) {
            Storage::disk('public')->delete($application->cv_path);
        }
        $application->delete();

        return redirect()->back()->with('success', 'Postulación eliminada del sistema.');
    }
}