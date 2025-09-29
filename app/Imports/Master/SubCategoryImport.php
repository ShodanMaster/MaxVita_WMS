<?php

namespace App\Imports\Master;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SubCategoryImport implements ToModel, WithValidation, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
        $category_id = Category::where('name', $row['category'])->value('id');


        return new SubCategory([
            'category_id'    => $category_id,
            'name'=> $row['name'],
            'sub_category_code'=> $row['sub_category_code'],
            'description'  => $row['description'],
        ]);

    }

    public function rules(): array
    {

        return [
            '*.category' =>'required|exists:categories,name',
            '*.name' =>'required|unique:sub_categories,name',
            '*.sub_category_code' => 'required',
            '*.description' => 'required',
        ];
    }
}
