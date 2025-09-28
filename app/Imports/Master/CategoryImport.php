<?php

namespace App\Imports\Master;

use App\Models\Category;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CategoryImport implements ToModel, WithValidation, WithHeadingRow
{
    /**
    * @param Collection $collection
    */

    public function model(array $row)
    {

        return new Category([
            'name'    => $row['name'],
            'erp_code'    => $row['erp_code'],
            'description'  => $row['description'],
        ]);
    }


    public function rules(): array
    {

        return [

            '*.name' => 'required|unique:categories,name',
            '*.erp_code' => 'required|unique:categories,erp_code',
            '*.description' => 'nullable',
        ];
    }


}
