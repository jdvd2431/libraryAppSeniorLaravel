<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Obtener IDs de usuarios y libros existentes
        $userIds = User::pluck('id')->toArray();
        $bookIds = Book::pluck('id')->toArray();

        // Crear varios préstamos utilizando la factory de préstamos
        Loan::factory(50)->create([
            'user_id' => function () use ($userIds) {
                return \Faker\Provider\Base::randomElement($userIds);
            },
            'book_id' => function () use ($bookIds) {
                return \Faker\Provider\Base::randomElement($bookIds);
            },
        ]);
    }
}
