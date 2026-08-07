<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Routing\Controller as BaseController; // <-- Usar la clase base de enrutamiento directamente

class AuthController extends BaseController // <-- Extender de BaseController
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $field = filter_var($credentials['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        if (Auth::attempt([$field => $credentials['email'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.resumen'));
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no son correctas.',
        ]);
    }

    public function recoverPassword(Request $request)
    {
        $request->validate([
            'cedula' => 'required|string',
            'email' => 'required|email|exists:users,email',
            'hire_date' => 'required|date',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::where('email', $request->email)
                    ->where('cedula', $request->cedula)
                    ->where('hire_date', $request->hire_date)
                    ->first();

        if (!$user) {
            return back()->withErrors(['recover' => 'Los datos ingresados no coinciden con nuestros registros corporativos.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('login')->with('success', '¡Contraseña actualizada con éxito! Ya puedes ingresar.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}