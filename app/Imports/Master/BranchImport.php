<?php

namespace App\Imports\Master;

use App\Models\Branch;
use App\Models\Location;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class BranchImport implements ToModel, WithValidation, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
        return new Branch([
            'name'    => $row['warehouse_name'],
            'branch_code'    => $row['warehouse_code'],
            'address'    => $row['warehouse_name'],
        ]);

    }

    public function rules(): array
    {

        return [

            '*.warehouse_name' => 'required|unique:branches,name',
            '*.warehouse_code' => 'required|unique:branches,branch_code',
        ];

    }
}
