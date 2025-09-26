<?php

namespace App\Imports\Master;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class BranchImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        //
    }
}
