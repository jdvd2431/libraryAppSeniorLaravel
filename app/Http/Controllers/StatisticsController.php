<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Loan;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StatisticsController extends Controller
{

    //Libros mas Populares
    public function popularBooks()
    {
        try {
            $popularBooks = Loan::select('books.title', \DB::raw('COUNT(loans.id) as loan_count'))
                ->join('books', 'loans.book_id', '=', 'books.id')
                ->groupBy('books.title')
                ->orderByDesc('loan_count')
                ->take(10) // Obtener los 10 libros más populares
                ->get();

            return response()->json($popularBooks);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Error al recuperar los libros populares.'], 500);
        }
    }
    //Usuarios que tiene mas moviemito o son mas activos
    public function activeUsers()
    {
        try {
            $activeUsers = Loan::select('users.name', \DB::raw('COUNT(loans.id) as loan_count'))
                ->join('users', 'loans.user_id', '=', 'users.id')
                ->groupBy('users.name')
                ->orderByDesc('loan_count')
                ->take(10) // Obtener los 10 usuarios más activos
                ->get();

            return response()->json($activeUsers);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Error al recuperar los usuarios activos.'], 500);
        }
    }
    //categoria de libro mas prestada
    public function loansByCategory()
    {
        try {
            $loansByCategory = Book::select('category', \DB::raw('COUNT(*) as loan_count'))
                ->join('loans', 'books.id', '=', 'loans.book_id')
                ->groupBy('category')
                ->orderByDesc('loan_count')
                ->get();

            return response()->json($loansByCategory);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Error al recuperar los préstamos por categoría.'], 500);
        }
    }

//Para identificar a los usuarios con préstamos vencidos y la cantidad de préstamos vencidos
    public function usersWithOverdueLoans()
    {
        try {
            $users = DB::table('loans')
                ->join('users', 'loans.user_id', '=', 'users.id')
                ->select('users.id', 'users.name', DB::raw('count(loans.id) as overdue_loans_count'))
                ->where('loans.return_date', '<', now())
                ->groupBy('users.id', 'users.name')
                ->orderBy('overdue_loans_count', 'desc')
                ->get();

            return response()->json($users, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener usuarios con préstamos vencidos: ' . $e->getMessage()], 500);
        }
    }

//Para analizar las tendencias de préstamos a lo largo del tiempo, por mes
    public function loanTrends()
    {
        try {
            $trends = Loan::selectRaw("strftime('%Y-%m', loan_date) as month, COUNT(*) as loan_count")
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->get();
            Log::info('Dato inportante: ' . $trends);
            return response()->json($trends);
        } catch (\Exception $e) {
            Log::error('Error al obtener las tendencias de préstamos: ' . $e->getMessage());
            return response()->json(['error' => 'No se pudieron obtener las tendencias de préstamos.'], 500);
        }
    }

//Para calcular la duración promedio de los préstamos.
    public function averageLoanDuration()
    {
        try {
            $averageDuration = Loan::selectRaw('AVG(julianday(return_date) - julianday(loan_date)) as avg_duration')
                ->first();

            return response()->json($averageDuration);
        } catch (\Exception $e) {
            Log::error('Error al obtener la duración promedio de los préstamos: ' . $e->getMessage());
            return response()->json(['error' => 'No se pudo obtener la duración promedio de los préstamos.'], 500);
        }
    }

}
