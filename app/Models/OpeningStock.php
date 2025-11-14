<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OpeningStock extends Model
{
    protected $fillable = [
        'opening_number',
        'file_path',
        'user_id',
        'location_id',
        'branch_id',
        'status',
    ];

    public static function openingNumber(){
        $yr = 'OP' . date("y");
        $len = strlen($yr);

        $openingNumber = DB::select("SELECT CONCAT('$yr',LPAD(max_opening_number+1,GREATEST(5,LENGTH(max_opening_number+1)),'0')) opening_number FROM
        (SELECT IFNULL( MAX(SUBSTRING(`opening_number`, $len+1)),'0') max_opening_number FROM opening_stocks WHERE opening_number LIKE '$yr%') p2");

        return $openingNumber[0]->opening_number;
    }

    public function openingStockSubs(){
        return $this->hasMany(OpeningStockSub::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function location(){
        return $this->belongsTo(Location::class);
    }

    public function branch(){
        return $this->belongsTo(Branch::class);
    }

    public static function filteredData($filters)
    {
        $fromdate = $filters['from_date'] ?? null;
        $todate = $filters['to_date'] ?? null;
        $opennumber = $filters['opennumber'] ?? null;
        $item_id = $filters['item_id'] ?? null;
        $opennumberid = $filters['opennumberid'] ?? null;


        $query = OpeningStock::with([
            'user',
            'location',
            'branch',
            'openingStockSubs.item',
            'openingStockSubs.bin'
        ]);

        if ($fromdate && $todate) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$fromdate, $todate]);
        } elseif ($fromdate) {
            $query->whereDate('created_at', $fromdate);
        } elseif ($todate) {
            $query->whereDate('created_at', $todate);
        }

        if (!empty($opennumber)) {
            $query->where('opening_number', $opennumber);
        }

        if (!empty($item_id)) {
            $query->whereHas('openingStockSubs', function ($q) use ($item_id) {
                $q->where('item_id', $item_id);
            });
        }

        if ($opennumberid) {
            $query->where('id', $opennumberid);
        }
        return $query->get();
    }


    public static function reportSummary($filters = [])
    {
        $open_numbers = self::filteredData($filters);

        $data = $open_numbers->map(function ($opnumber) {
            switch ($opnumber->status) {
                case 0:
                    $status = 'not printed';
                    break;
                case 1:
                    $status = 'printed';
                    break;
                default:
                    $status = 'unknown';
                    break;
            }

            return [
                'id' => $opnumber->id,
                'opening_number' => $opnumber->opening_number,
                'opening_date' => $opnumber->created_at ? $opnumber->created_at->format('d-m-Y') : '',
                'user' =>  $opnumber->user->username ?? '',
                'location' => $opnumber->location->name ?? 0,
                'branch' => $opnumber->branch->name ?? 0,
                'status' => $status,
            ];
        });

        return $data->values();
    }


    public static function reportDetailed($filters = [])
    {
        $openingStocks = self::filteredData($filters);

        $data = $openingStocks->flatMap(function ($openingStock) {

            switch ($openingStock->status) {
                case 0:
                    $status = 'not printed';
                    break;
                case 1:
                    $status = 'printed';
                    break;
                default:
                    $status = 'unknown';
                    break;
            }

            return $openingStock->openingStockSubs->map(function ($sub) use ($openingStock, $status) {
                return [
                    'opening_number'     => $openingStock->opening_number,
                    'item_name'          => $sub->item->item_code . '/' . $sub->item->name,
                    'manufacture_date'   => $sub->manufacture_date ? Carbon::parse($sub->manufacture_date)->format('d-m-Y') : '',
                    'best_before'        => $sub->best_before ? Carbon::parse($sub->best_before)->format('d-m-Y') : '',
                    'bin'                => $sub->bin->name ?? '',
                    'total_quantity'     => $sub->total_quantity ?? '',
                    'number_of_barcodes' => $sub->number_of_barcodes ?? '',
                    'batch'              => $sub->batch ?? '',
                    'location'            => $openingStock->location->name ?? '',
                    'branch'            => $openingStock->branch->name ?? '',
                    'batch'              => $sub->batch ?? '',
                    'user'               => $openingStock->user->username ?? '',
                    'status'             => $status,
                ];
            });
        });


        return $data->values();
    }
}
