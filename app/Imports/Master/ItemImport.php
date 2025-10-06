<?php

namespace App\Imports\Master;

use App\Models\Category;
use App\Models\Item;
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
            ];

        if(strtolower($row['item_type']) === 'fg'){
            $fgData = [
                'sku_code' => $row['sku_code'],
                'gst_rate' => $row['gst_rate'],
            ];

            $data = array_merge($data, $fgData);
        }

        return new Item($data);

    }

    public function rules(): array
    {

        return [
            '*.category' => 'required|exists:categories,name',
            '*.uom' => 'required|exists:uoms,name',
            '*.item_code' => 'required|unique:items,item_code',
            '*.item_description' => 'required|unique:items,name',
            '*.in_stock' => 'nullable|integer',
            '*.item_type' => 'required|in:FG,RM',
        ];

    }
}
