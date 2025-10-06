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
            'vendor_code'=> Vendor::vendorCode(),
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
            '*.location' => 'nullable',
            '*.address' => 'nullable',
            '*.city' => 'nullable',
            '*.state' => 'nullable',
            '*.zip_code' => 'nullable',
        ];
    }
}
