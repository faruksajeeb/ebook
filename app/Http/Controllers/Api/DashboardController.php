<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class DashboardController extends Controller
{


    public function TodaySell()
    {
        $date = date('d/m/Y');
        $sell = DB::table('orders')->where('order_date', $date)->sum('total');
        return response()->json($sell);
    }

    public function TodayIncome()
    {
        $date = date('d/m/Y');
        $income = DB::table('orders')->where('order_date', $date)->sum('pay');
        return response()->json($income);
    }

    public function TodayDue()
    {
        $date = date('d/m/Y');
        $todaydue = DB::table('orders')->where('order_date', $date)->sum('due');
        return response()->json($todaydue);
    }


    public function TodayExpense()
    {
        $date = date('d/m/Y');
        $expense = DB::table('expenses')->where('expense_date', $date)->sum('amount');
        return response()->json($expense);
    }

    public function Stockout()
    {

        $product = DB::table('products')->where('product_quantity', '<', '1')->get();
        return response()->json($product);
    }
}
