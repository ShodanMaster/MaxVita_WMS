<?php

namespace App\Imports\Master;

use App\Models\Uom;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UomImport implements ToModel, WithValidation, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {

        return new Uom([
            'name'    => $row['name'],
            'uom_code'=> $row['uom_code'],
        ]);
    }

    public function rules(): array
    {

        return [

            '*.name' => 'required|unique:uoms,name',
            '*.uom_code' => 'required|unique:uoms,uom_code',
        ];
    }

}
