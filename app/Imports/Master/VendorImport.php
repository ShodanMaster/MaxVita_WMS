<?php

namespace App\Imports\Master;

use App\Models\Vendor;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;


class VendorImport implements ToModel, WithValidation, WithHeadingRow
{
    /**
    * @param ToModel $row
     */

    public function model(array $row)
    {

        return new Vendor([
            'name'    => $row['name'],
            'vendor_code'=> $row['vendor_code'],
            'location'  => $row['location'],
            'address'  => $row['address'],
            'city' => $row['city'],
            'state' => $row['state'],
            'zip_code' => $row['zip_code'],
        ]);
    }


    public function rules(): array
    {

        return [

            '*.name' => 'required',
            '*.vendor_code' => 'required|unique:vendors,vendor_code',
            '*.location' => 'required',
            '*.address' => 'required',
            '*.city' => 'required',
            '*.state' => 'required',
            '*.zip_code' => 'required',
        ];
    }
}
