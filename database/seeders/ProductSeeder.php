<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // check if table users is empty
         if(DB::table('products')->count() == 0){

            DB::table('products')->insert([
            ['name' => 'TELUR','uom' => 'KG'],
            ['name' => 'BERAS','uom' => 'KG'],
            ['name' => 'TEPUNG','uom' => 'KG'],
            ['name' => 'MIE INSTAN','uom' => 'PCS'],
            ['name' => 'MINYAK','uom' => 'L'],
            ['name' => 'TEH','uom' => 'PCS'],
            ['name' => 'GULA','uom' => 'KG'],
            
        ]);
    }else{
        echo "\nTable is not empty, therefore NOT";
    }
    }
}
