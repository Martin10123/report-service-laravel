<?php

namespace App\Http\Controllers;

use App\Models\Sede;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class SedeController extends Controller
{
    /**
     * Display a listing of all sedes for configuration.
     */
    public function index()
    {
        $sedes = Sede::with('areas')->orderBy('nombre')->get();
        $areas = \App\Models\Area::activas()->orderBy('codigo')->get();
        
        return Inertia::render('Sedes/Index', [
            'todasSedes' => $sedes,
            'areasDisponibles' => $areas,
        ]);
    }

    /**
     * Update the specified sede in storage.
     */
    public function update(Request $request, Sede $sede)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'tiene_areas_multiples' => 'boolean',
            'tiene_parqueadero' => 'boolean',
            'tiene_gradas' => 'boolean',
            'areas' => 'nullable|array|max:4',
            'areas.*' => 'exists:areas,id',
        ]);

        // Actualizar el slug si el nombre cambió
        if ($sede->nombre !== $validated['nombre']) {
            $validated['slug'] = Str::slug($validated['nombre']);
        }

        // Actualizar numero_areas basado en las áreas seleccionadas
        $validated['numero_areas'] = isset($validated['areas']) ? count($validated['areas']) : 0;

        // Extraer áreas antes de actualizar
        $areasIds = $validated['areas'] ?? [];
        unset($validated['areas']);

        $sede->update($validated);

        // Sincronizar áreas
        $sede->areas()->sync($areasIds);

        return back()->with('success', 'Sede actualizada correctamente');
    }

    /**
     * Toggle the active status of a sede.
     */
    public function toggleActive(Sede $sede)
    {
        $sede->activa = !$sede->activa;
        $sede->save();

        $status = $sede->activa ? 'activada' : 'desactivada';
        
        return back()->with('success', "Sede {$status} correctamente");
    }

    /**
     * Switch the current sede for the super user.
     * Stores the selected sede in session and as user preference.
     */
    public function switch(Request $request, string $slug)
    {
        $sede = Sede::where('slug', $slug)->where('activa', true)->firstOrFail();
        
        // Guardar la sede seleccionada en la sesión
        session(['sede_actual_id' => $sede->id]);
        
        // Guardar como sede preferida del usuario para persistir entre sesiones
        if ($request->user()) {
            $request->user()->update(['sede_preferida_id' => $sede->id]);
        }
        
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
