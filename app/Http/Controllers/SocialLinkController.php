<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SocialLink;
use App\Models\User;

class SocialLinkController extends Controller
{
    // Listar redes y cuentas
    public function index()
    {
        $links = SocialLink::with('user')->latest()->paginate(10);
        return view('intranet.social_links.index', compact('links'));
    }

    public function create()
    {
        $users = User::all();
        return view('intranet.social_links.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'platform' => 'required|string|max:255',
            'url_or_value' => 'required|string',
        ]);

        SocialLink::create([
            'user_id' => $request->user_id, // NULL si es de la empresa global
            'platform' => $request->platform,
            'url_or_value' => $request->url_or_value,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('social-links.index')->with('success', '¡Enlace o cuenta guardada con éxito!');
    }

    public function destroy(SocialLink $socialLink)
    {
        $socialLink->delete();
        return redirect()->route('social-links.index')->with('success', 'Registro eliminado correctamente.');
    }
}