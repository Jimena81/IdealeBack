<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Photo;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    // Método para almacenar una nueva foto
    public function store(Request $request)
    {
        try {
            $request->validate([
                'photo' => 'required|image|max:2048', // Validación del archivo de imagen, máximo 2MB
            ]);

            // Agregar registro de depuración para verificar la solicitud
            dd($request->all());

            $photo = $request->file('photo');

            $fileData = file_get_contents($photo->getRealPath());

            // Agregar registro de depuración para verificar los datos del archivo
            dd($photo->getClientOriginalName(), $fileData);

            Photo::create([
                'file_name' => $photo->getClientOriginalName(),
                'file_data' => $fileData
            ]);

            return back()->with('success', 'Foto cargada exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar la foto: ' . $e->getMessage());
        }
    }

    // Método para eliminar una foto
    public function destroy($id)
    {
        try {
            $photo = Photo::findOrFail($id);

            // Eliminar la foto de la base de datos
            $photo->delete();

            return back()->with('success', 'Foto eliminada exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la foto: ' . $e->getMessage());
        }
    }
}
