<?php

namespace App\Imports\Master;

use App\Models\Bin;
use App\Models\Location;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class BinImport implements ToModel, WithValidation, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
        $storage_loc_id=Location::select('id')->where('name',$row['storage_location'])->first();

        return new Bin([
            'location_id'    => $storage_loc_id->id,
            'name'    => $row['name'],
            'bin_code'    => $row['erp_code'],
            'bin_type'    => $row['bin_type'],
            'description'       => $row['description'],
        ]);

    }

    public function rules(): array
    {

        return [

            '*.name' => 'required|unique:bins,name',
            '*.erp_code' => 'required|unique:bins,bin_code',
            '*.bin_type' => 'required|in:FG,RM',
            '*.storage_location' => 'required|exists:locations,name',
            '*.description' => 'required',
        ];

    }
}
