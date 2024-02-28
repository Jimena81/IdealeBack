<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Photo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PhotoController extends Controller
{
    // Método para almacenar una nueva foto
    public function store(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|max:2048', // Validación del archivo de imagen, máximo 2MB
            ]);

            $imagePath = $request->file('image')->store('photos', 'public'); // Guardar la imagen en el sistema de archivos

            // Guardar la referencia de la imagen en la base de datos
            $photo = new Photo();
            $photo->file_name = $request->file('image')->getClientOriginalName();
            $photo->file_path = $imagePath;
            $photo->save();

            return back()->with('success', 'Imagen cargada correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al cargar la foto: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar la foto. Por favor, contacta al administrador.');
        }
    }

    // Método para eliminar una foto
    public function delete($id)
    {
        try {
            $photo = Photo::findOrFail($id);

            // Eliminar la imagen del sistema de archivos
            Storage::disk('public')->delete($photo->file_path);

            // Eliminar el registro de la base de datos
            $photo->delete();

            return back()->with('success', 'Foto eliminada exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar la foto: ' . $e->getMessage());
            return back()->with('error', 'Error al eliminar la foto. Por favor, contacta al administrador.');
        }
    }

    // Método para mostrar una foto
    public function show($id)
    {
        try {
            $photo = Photo::findOrFail($id);

            return response()->file(storage_path('app/public/' . $photo->file_path));
        } catch (\Exception $e) {
            Log::error('Error al mostrar la foto: ' . $e->getMessage());
            return back()->with('error', 'Error al mostrar la foto. Por favor, contacta al administrador.');
        }
    }
}
