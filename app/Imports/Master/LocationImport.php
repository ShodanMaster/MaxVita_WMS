<?php

namespace App\Imports\Master;

use App\Models\Branch;
use App\Models\Location;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class LocationImport implements ToModel, WithValidation, WithHeadingRow
{
    /**
    * @param ToModel $row
    */
    public function model(array $row)
    {
        $branch=Branch::select('id')->where('name',$row['warehouse_name'])->first();

        $location_type = $this->getLcoationType($row['location_type']);

        return new Location([
            'branch_id'    => $branch->id,
            'name'    => $row['name'],
            'prefix'    => $row['prefix'],
            'location_code'    => $row['prefix'],
            'location_type' => $location_type,
            'description'       => $row['description'],
        ]);

    }

    public function rules(): array
    {

        return [
            '*.warehouse_name' => 'required|exists:branches,name',
            '*.name' => 'required|unique:locations,name',
            '*.prefix' => 'required|unique:locations,prefix',
            '*.location_type' => 'required',
            '*.description' => 'nullable',
        ];

    }

    private function getLcoationType($value){

        $location_type = 0;
        $value = strtolower($value);

        switch ($value) {
            case 'qc pending':
                $location_type = 1;
                break;
            case 'storage':
                $location_type = 2;
                break;
            case 'rejection':
                $location_type = 3;
                break;
            case 'dispatch':
                $location_type = 4;
                break;
            default:
                $location_type = 0;
                break;
        }

        return $location_type;
    }
}
