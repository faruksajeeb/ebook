<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class DashboardController extends Controller
{


    public function TodaySale()
    {
        $date = date('Y-m-d');
        $sale = DB::table('sales')->where('sale_date', $date)->sum('net_amount');
        return response()->json($sale);
    }

    public function TodayPurchase()
    {
        $date = date('Y-m-d');
        $purchase = DB::table('purchases')->where('purchase_date', $date)->sum('net_amount');
        return response()->json($purchase);
    }

    public function TotalDue()
    {
        $dueBalance = DB::table('customers')->where('balance','>', 0)->sum('balance');
        return response()->json($dueBalance);
    }
    public function TotalAdvance()
    {
        $advanceBalance = DB::table('customers')->where('balance','<', 0)->sum('balance');
        return response()->json($advanceBalance);
    }


    public function totalCustomer()
    {
        $res = DB::table('customers')->where('status', 1)->count();
        return response()->json($res);
    }

    public function outOfStock()
    {
        $product = DB::table('books')->where('stock_quantity', '<', 1)->get();
        return response()->json($product);
    }
    public function stockAlerts()
    {
        $product = DB::table('books')->whereBetween('stock_quantity', [1,4])->get();
        return response()->json($product);
    }
}
