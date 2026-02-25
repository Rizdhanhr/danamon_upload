<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Gate;
use App\Models\UploadRecipient;
use Carbon\Carbon;
use DB;


class DashboardController extends Controller
{
    public function index(){
        $total_recipient = UploadRecipient::where('status',3)
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->sum('total_recipient');

        $total_amount = UploadRecipient::where('status',3)
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->sum('total_amount');

        $total_batch = UploadRecipient::where('status',3)
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->count();
        // $gates = Gate::abilities();

        // dd($gates);
        return view('dashboard.index',compact('total_recipient','total_amount','total_batch'));
    }

    public function getData(Request $request){
        DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
        $tipe = $request->tipe;
        $sql = "SELECT LEFT(MONTHNAME(CONCAT(YEAR(CURRENT_DATE()), '-', MONTHS.month_number, '-01')),3) AS x, ";

        if($tipe == 'idr'){
            $sql .= "COALESCE(SUM(upload_recipient.total_amount), 0) AS y ";
        }else{
            $sql .= "COALESCE(SUM(upload_recipient.total_recipient), 0) AS y ";
        }


        $sql .= "FROM
        (SELECT 1 AS month_number UNION ALL
        SELECT 2 UNION ALL
        SELECT 3 UNION ALL
        SELECT 4 UNION ALL
        SELECT 5 UNION ALL
        SELECT 6 UNION ALL
        SELECT 7 UNION ALL
        SELECT 8 UNION ALL
        SELECT 9 UNION ALL
        SELECT 10 UNION ALL
        SELECT 11 UNION ALL
        SELECT 12) AS MONTHS
        LEFT JOIN
            upload_recipient ON MONTH(upload_recipient.created_at) = MONTHS.month_number
            AND YEAR(upload_recipient.created_at) = YEAR(CURRENT_DATE())
            AND upload_recipient.status = 3
        GROUP BY
            MONTHS.month_number
        ORDER BY
        MONTHS.month_number";

        $results = DB::select($sql);

        return response()->json(['data' => $results]);
    }
}
