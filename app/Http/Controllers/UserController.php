<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    // Método para registrar un nuevo usuario administrador
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
            ]);

            // Crear el usuario como administrador
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->role = 'admin'; // Asignar el rol de administrador
            $user->save();

            return redirect()->back()->with('success', 'Usuario administrador registrado exitosamente.');
        } catch (ValidationException $e) {
            // Manejar errores de validación
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // Manejar otros errores
            return redirect()->back()->with('error', 'Se produjo un error al registrar el usuario administrador.');
        }
    }

    // Método para mostrar el formulario de inicio de sesión
    public function showLoginForm()
    {
        return view('login');
    }

    // Método para procesar la solicitud de inicio de sesión
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // La autenticación ha sido exitosa
            return redirect()->intended('/dashboard'); // Redirigir a la página de dashboard o a donde desees
        }

        // La autenticación ha fallado
        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no son válidas.',
        ])->withInput();
    }

    // Método para cerrar sesión
    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
