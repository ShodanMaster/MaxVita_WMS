<?php

namespace App\Imports\Master;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CustomerImport implements ToModel, WithValidation, WithHeadingRow
{
    /**
    * @param ToModel $row
    */
    public function model(array $row)
    {

        return new Customer([
            'name'    => $row['name'],
            'customer_code'    => $row['customer_code'],
            'shipping_code'  => $row['shipping_code'],
            'customer_address'  => $row['customer_address'],
            'zip_code'=> $row['zip_code'],
            'gst_number'=> $row['gst_number'],
        ]);
    }


    public function rules(): array
    {

        return [

            '*.name' => 'required|unique:customers,name',
            '*.customer_code' => 'required',
            '*.shipping_code' => 'nullable',
            '*.customer_address' => 'required',
            '*.zip_code	' => 'nullable',
            '*.gst_number' => 'required',
        ];
    }
}
