<?php

namespace App\Imports\Master;

use App\Models\Bin;
use App\Models\Location;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class BinImport implements ToModel, WithValidation, WithHeadingRow
{
    /**
    * @param ToModel $row
    */
    public function model(array $row)
    {
        $storage_loc_id=Location::select('id')->where('name',$row['storage_location'])->first();

        return new Bin([
            'location_id'    => $storage_loc_id->id,
            'name'    => $row['name'],
            'bin_code'    => Bin::binCode($row['bin_type']),
            'bin_type'    => $row['bin_type'],
            'description'       => $row['description'],
        ]);

    }

    public function rules(): array
    {

        return [
            '*.name' => 'required|unique:bins,name',
            '*.bin_type' => 'required|in:FG,RM',
            '*.storage_location' => 'required|exists:locations,name',
            '*.description' => 'nullable',
        ];

    }
}
