<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index()
    {
        try {
            // Obtener una lista paginada de usuarios
            $users = User::paginate(10);
            return response()->json($users);
        } catch (\Exception $e) {
            // Manejar errores inesperados
            return response()->json(['error' => 'No se pudieron obtener los usuarios.'], 500);
        }
    }

    public function show($id)
    {
        try {
            // Buscar un usuario por su ID
            $user = User::findOrFail($id);
            return response()->json($user);
        } catch (ModelNotFoundException $e) {
            // Manejar el caso en el que no se encuentra el usuario
            return response()->json(['error' => 'Usuario no encontrado.'], 404);
        } catch (\Exception $e) {
            // Manejar errores inesperados
            return response()->json(['error' => 'No se pudo obtener el usuario.'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Validar los datos de entrada
            $validatedData = $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string',
                'role' => 'required|string', // Agregar más validaciones según sea necesario
            ]);

            // Hash de la contraseña antes de guardarla en la base de datos
            $validatedData['password'] = Hash::make($validatedData['password']);

            // Crear un nuevo usuario
            $user = User::create($validatedData);
            return response()->json($user, 201);
        } catch (ValidationException $e) {
            // Manejar errores de validación
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Manejar errores inesperados
            return response()->json(['error' => 'No se pudo crear el usuario.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Buscar el usuario por su ID
            $user = User::findOrFail($id);

            // Validar los datos de entrada para la actualización
            $validatedData = $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'password' => 'sometimes|string',
                'role' => 'required|string', // Agregar más validaciones según sea necesario
            ]);

            // Hash de la contraseña si se proporciona
            if ($request->filled('password')) {
                $validatedData['password'] = Hash::make($validatedData['password']);
            }

            // Actualizar el usuario con los datos validados
            $user->update($validatedData);
            return response()->json($user, 200);
        } catch (ModelNotFoundException $e) {
            // Manejar el caso en el que no se encuentra el usuario
            return response()->json(['error' => 'Usuario no encontrado.'], 404);
        } catch (ValidationException $e) {
            // Manejar errores de validación
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Manejar errores inesperados
            return response()->json(['error' => 'No se pudo actualizar el usuario.'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Buscar el usuario por su ID y eliminarlo
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json(null, 204);
        } catch (ModelNotFoundException $e) {
            // Manejar el caso en el que no se encuentra el usuario
            return response()->json(['error' => 'Usuario no encontrado.'], 404);
        } catch (\Exception $e) {
            // Manejar errores inesperados
            return response()->json(['error' => 'No se pudo eliminar el usuario.'], 500);
        }
    }
}
