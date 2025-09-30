<?php

namespace App\Imports\Master;

use App\Models\Branch;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class BranchImport implements ToModel, WithValidation, WithHeadingRow
{
    /**
    * @param ToModel $row
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
