<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTADO DE PERSONAL
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $users = User::latest()->paginate(10);

        /*
        |--------------------------------------------------------------------------
        | POSTULANTES
        |--------------------------------------------------------------------------
        |
        | Si la tabla/modelo JobApplication ya existe, obtenemos las
        | postulaciones enviadas desde la página pública "Únete".
        |
        */

        $jobApplications = JobApplication::latest()->get();

        return view(
            'intranet.users.index',
            compact(
                'users',
                'jobApplications'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | NUEVO USUARIO
    |--------------------------------------------------------------------------
    */
 public function create()
{
    return view('intranet.users.create_advisor');
}


    /*
    |--------------------------------------------------------------------------
    | GUARDAR USUARIO
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',

            'email' =>
                'required|email|unique:users,email',

            'password' =>
                'required|string|min:6',

            'phone' =>
                'required|string|max:20',

            'city' =>
                'required|string|max:100',

            'profession' =>
                'required|string|max:100',

            'experience_years' =>
                'required|string|max:50',

            'role' =>
                'required|in:Trámites,Asesor,Contador,Publicista,Administrador/Gerente',

            'monthly_goal' =>
                'required|integer',

            'cv_file' =>
                'nullable|mimes:pdf|max:5120',
        ]);


        $data =
            $request->except([
                'password',
                'cv_file'
            ]);


        $data['password'] =
            Hash::make(
                $request->password
            );


        /*
        |--------------------------------------------------------------------------
        | ESTADO INICIAL
        |--------------------------------------------------------------------------
        */

        if (!$request->filled('status')) {
            $data['status'] = 'Activo';
        }


        /*
        |--------------------------------------------------------------------------
        | CV
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('cv_file')) {

            $data['cv_file_path'] =
                $request
                    ->file('cv_file')
                    ->store(
                        'cvs',
                        'public'
                    );
        }


        User::create($data);


        return redirect()
            ->route('users.index')
            ->with(
                'success',
                '¡Personal registrado con éxito en la plataforma!'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | VER PERFIL
    |--------------------------------------------------------------------------
    */
    public function show(User $user)
    {
        $user->load([
            'properties',
            'clients',
            'tramites',
            'socialLinks'
        ]);


        return view(
            'intranet.users.show',
            compact('user')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EDITAR
    |--------------------------------------------------------------------------
    */
    public function edit(User $user)
    {
        return view(
            'intranet.users.edit',
            compact('user')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR
    |--------------------------------------------------------------------------
    */
    public function update(
        Request $request,
        User $user
    ) {

        $request->validate([

            'name' =>
                'required|string|max:255',

            'last_name' =>
                'required|string|max:255',

            'email' =>
                'required|email|unique:users,email,' .
                $user->id,

            'role' =>
                'required|in:Trámites,Asesor,Contador,Publicista,Administrador/Gerente',

            'monthly_goal' =>
                'required|integer',

            'cv_file' =>
                'nullable|mimes:pdf|max:5120',

        ]);


        $data =
            $request->except([
                'password',
                'cv_file'
            ]);


        /*
        |--------------------------------------------------------------------------
        | CONTRASEÑA
        |--------------------------------------------------------------------------
        */

        if ($request->filled('password')) {

            $data['password'] =
                Hash::make(
                    $request->password
                );
        }


        /*
        |--------------------------------------------------------------------------
        | NUEVO CV
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('cv_file')) {

            if ($user->cv_file_path) {

                Storage::disk('public')
                    ->delete(
                        $user->cv_file_path
                    );
            }


            $data['cv_file_path'] =
                $request
                    ->file('cv_file')
                    ->store(
                        'cvs',
                        'public'
                    );
        }


        $user->update($data);


        return redirect()
            ->route('users.index')
            ->with(
                'success',
                '¡Información del usuario actualizada correctamente!'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | ACTIVAR / DESACTIVAR USUARIO
    |--------------------------------------------------------------------------
    |
    | IMPORTANTE:
    |
    | Esta función antes eliminaba físicamente al usuario.
    | Ahora NO se elimina ningún registro.
    |
    | La ruta users.destroy se conserva para no modificar routes/web.php.
    |
    */

    public function destroy(User $user)
    {
        $estadoActual =
            strtolower(
                trim(
                    (string) $user->status
                )
            );


        if ($estadoActual === 'activo') {

            $user->status =
                'Inactivo';

            $mensaje =
                'El usuario fue desactivado correctamente.';

        } else {

            $user->status =
                'Activo';

            $mensaje =
                'El usuario fue activado correctamente.';
        }


        $user->save();


        return redirect()
            ->route('users.index')
            ->with(
                'success',
                $mensaje
            );
    }
}