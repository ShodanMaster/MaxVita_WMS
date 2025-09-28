<?php

namespace App\Imports\Master;

use App\Models\Brand;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class BrandImport implements ToModel, WithValidation, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {

        return new Brand([
            'name'    => $row['name'],
        ]);

    }

    public function rules(): array
    {

        return [
            '*.name' => 'required|unique:brands,name',
        ];

    }
}
