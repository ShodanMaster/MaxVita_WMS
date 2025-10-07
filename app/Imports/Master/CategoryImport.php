<?php

namespace App\Imports\Master;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CategoryImport implements ToModel, WithValidation, WithHeadingRow
{
    /**
    * @param ToModel $row
    */

    public function model(array $row)
    {

        return new Category([
            'name'    => $row['name'],
            'description'  => $row['description'],
        ]);
    }


    public function rules(): array
    {

        return [

            '*.name' => 'required|unique:categories,name',
            '*.description' => 'nullable',
        ];
    }


}
