<?php

namespace App\Http\Controllers;

use App\Models\Sede;
use Illuminate\Http\Request;

class SedeController extends Controller
{
    /**
     * Switch the current sede for the super user.
     * Stores the selected sede in session.
     */
    public function switch(Request $request, string $slug)
    {
        $sede = Sede::where('slug', $slug)->where('activa', true)->firstOrFail();
        
        // Guardar la sede seleccionada en la sesión
        session(['sede_actual_id' => $sede->id]);
        
        // Limpiar el servicio seleccionado al cambiar de sede
        session()->forget('servicio_actual_id');
        
        // Redirigir a la lista de servicios para evitar quedar en un detalle de servicio de otra sede
        return redirect()->route('servicios.index')->with('success', "Vista cambiada a sede: {$sede->nombre}");
    }

    /**
     * Clear the current sede selection.
     */
    public function clear(Request $request)
    {
        session()->forget('sede_actual_id');
        
        return redirect()->back()->with('success', 'Sede deseleccionada');
    }
}
