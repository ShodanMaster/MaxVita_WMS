<?php

namespace App\Imports\Transaction;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class OpeningStockImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        
    }
}
