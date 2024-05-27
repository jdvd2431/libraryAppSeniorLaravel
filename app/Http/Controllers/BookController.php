<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class BookController extends Controller
{
    public function index()
    {
        try {
            $books = Book::all();
            return response()->json($books);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Error al obtener los libros.');
        }
    }

    public function show($id)
    {
        try {
            $book = Book::findOrFail($id);
            return response()->json($book);
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Libro no encontrado.');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Error al obtener el libro.');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string',
                'author' => 'required|string',
                'description' => 'nullable|string',
                'isbn' => 'required|string|unique:books,isbn',
                'pages' => 'required|integer|min:1',
                'category' => 'required|string',
                'stock' => 'required|integer|min:0',
            ]);

            $book = Book::create($request->all());

            return response()->json($book, 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Error al crear el libro.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $book = Book::findOrFail($id);

            $request->validate([
                'title' => 'required|string',
                'author' => 'required|string',
                'description' => 'nullable|string',
                'isbn' => 'required|string|unique:books,isbn,' . $book->id,
                'pages' => 'required|integer|min:1',
                'category' => 'required|string',
                'stock' => 'required|integer|min:0',
            ]);

            $book->update($request->all());

            return response()->json($book, 200);
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Libro no encontrado.');
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Error al actualizar el libro.');
        }
    }

    public function destroy($id)
    {
        try {
            $book = Book::findOrFail($id);
            $book->delete();
            return response()->json(null, 204);
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Libro no encontrado.');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Error al eliminar el libro.');
        }
    }

    /**
     * Handle general exceptions.
     *
     * @param \Exception $exception
     * @param string $errorMessage
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleException(\Exception $exception, $errorMessage)
    {
        \Log::error($exception);
        return response()->json(['error' => $errorMessage], 500);
    }

    /**
     * Return not found response.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    private function notFoundResponse($message)
    {
        return response()->json(['error' => $message], 404);
    }
}
