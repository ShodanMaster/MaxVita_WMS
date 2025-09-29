<?php

namespace App\Imports\Master;

use App\Models\Location;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UserImport implements ToModel, WithValidation, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
        $location=Location::where('name',$row['location'])->first();

        return new User([
            'branch_id' => $location->branch->id,
            'location_id' => $location->id,
            'name' => $row['name'],
            'email' => $row['email'],
            'username' => $row['username'],
            'password' => bcrypt('example@123'),
        ]);
    }

    public function rules(): array
    {
        return [
            '*.location' => 'required|exists:locations,name',
            '*.name' => 'required|string',
            '*.email' => 'required|email|unique:users,email',
            '*.username' => 'required|string|unique:users,username',
        ];
    }
}
