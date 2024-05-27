<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class LoanController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Validar los datos de entrada
            $validatedData = $request->validate([
                'user_id' => 'required|exists:users,id',
                'book_id' => 'required|exists:books,id',
                'loan_date' => 'required|date',
                'return_date' => 'required|date|after:loan_date',
            ]);

            // Registro de mensaje de información
            Log::info('Datos validados con éxito: ' . json_encode($validatedData));

            // Verificar si ya existe un préstamo activo para el mismo libro y usuario
            $existingLoan = Loan::where('user_id', $validatedData['user_id'])
                ->where('book_id', $validatedData['book_id'])
                ->where(function ($query) {
                    $query->whereNull('return_date') // Préstamo aún no devuelto
                    ->orWhere('return_date', '>', now()); // Préstamo devuelto en el futuro
                })
                ->first();
            Log::info('Dato inportante: ' . $existingLoan);
            if ($existingLoan) {
                // Registro de mensaje de error
                Log::error('Ya hay un préstamo activo para este libro y usuario.');
                return response()->json('Ya hay un préstamo activo para este libro y usuario.', 400);
            }

            // Registro de mensaje de información
            Log::info('No hay préstamo activo para este libro y usuario.');

            // Verificar si hay suficiente stock disponible para el libro
            $book = Book::findOrFail($validatedData['book_id']);
            if ($book->stock <= 0) {
                // Registro de mensaje de error
                Log::error('No hay suficiente stock disponible para este libro.');
                return response()->json(['error' => 'No hay suficiente stock disponible para este libro.'], 400);
            }

            // Registro de mensaje de información
            Log::info('Stock disponible para el libro.');

            // Crear un nuevo préstamo
            $loan = Loan::create($validatedData);

            // Registro de mensaje de información
            Log::info('Préstamo creado con éxito.');

            // Actualizar el stock del libro
            $book->stock -= 1;
            $book->save();

            return response()->json($loan, 201);
        } catch (ValidationException $e) {
            // Manejar errores de validación
            Log::error('Error de validación al crear un préstamo: ' . json_encode($e->errors()));
            return response()->json(['error' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            // Manejar el caso en el que no se encuentra el usuario o el libro
            Log::error('Usuario o libro no encontrado.');
            return response()->json(['error' => 'Usuario o libro no encontrado.'], 404);
        } catch (\Exception $e) {
            // Manejar errores inesperados
            Log::error('Error al crear un préstamo: ' . $e->getMessage());
            return response()->json(['error' => 'No se pudo crear el préstamo.'], 500);
        }
    }
}
