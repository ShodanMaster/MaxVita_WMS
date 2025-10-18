<?php

namespace App\Imports\Master;

use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use App\Models\Uom;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ItemImport implements ToModel, WithValidation, WithHeadingRow
{
    /**
    * @param ToModel $row
    */
    public function model(array $row)
    {
        $category=Category::select('id')->where('name',$row['category'])->first();
        $uom=Uom::select('id')->where('name',$row['uom'])->first();

        $spqQuantity = 1;

        if (strtolower($row['item_type']) === 'fg') {
            $spqQuantity = $row['spq_quantity'];
        }

        $data = [
                'category_id' => $category->id,
                'uom_id' => $uom->id,
                'item_code' => $row['item_code'],
                'name' => $row['item_description'],
                'in_stock' => $row['in_stock'],
                'item_type' => $row['item_type'],
                'spq_quantity' => $spqQuantity,
                'price' => $row['price'],
            ];

        if(strtolower($row['item_type']) === 'fg'){
            $fgData = [
                'single_packet_weight' => $row['single_packet_weight'],
                'sku_code' => $row['sku_code'],
                'gst_rate' => $row['gst_rate'],
            ];

            $data = array_merge($data, $fgData);
        }

        $item = new Item($data);
        $item->save();

        $locationNames = array_map('trim', explode(',', $row['location_name']));
        $locationIds = Location::whereIn('name', $locationNames)->pluck('id')->toArray();

        $item->locations()->sync($locationIds);

        return $item;

    }

    public function rules(): array
    {

        return [
            '*.category' => 'required|exists:categories,name',
            '*.uom' => 'required|exists:uoms,name',
            '*.item_code' => 'required|unique:items,item_code',
            '*.item_description' => 'required|unique:items,name',
            '*.in_stock' => 'nullable|integer',
            '*.price' => 'nullable',
            '*.item_type' => 'required|in:FG,RM',
            '*.location_name' => 'required',
        ];

    }

    public function WithValidator($validator)
    {
        $validator->after(function ($validator) {
            foreach ($validator->getData() as $index => $row) {
                $rowIndex = $index + 2; // Excel rows start at 2 (after heading)

                if (!isset($row['location_name'])) {
                    $validator->errors()->add("{$index}.location_name", "Location name is required.");
                    continue;
                }

                $locations = array_map('trim', explode(',', $row['location_name']));
                $invalidLocations = [];
                
                foreach ($locations as $locName) {
                    if (!Location::where('name', $locName)->exists()) {
                        $invalidLocations[] = $locName;
                    }
                }

                if (count($invalidLocations)) {
                    $validator->errors()->add("{$index}.location_name", 'Invalid locations: ' . implode(', ', $invalidLocations));
                }
            }
        });
    }

}
