<?php

namespace App\Imports\Master;

use App\Models\Reason;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ReasonImport implements ToModel, WithValidation, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {

        return new Reason([
            'reason'    => $row['reason'],
            'description'    => $row['description'],
        ]);

    }

    public function rules(): array
    {

        return [
            '*.reason' => 'required|unique:reasons,reason',
            '*.description' => 'required',
        ];

    }
}
