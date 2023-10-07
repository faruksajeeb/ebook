<?php

namespace App\Http\Controllers\Api\Reports;
use App\Http\Controllers\Controller;
use App\Lib\Webspice;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Http\Request;
use Exception;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use App\Exports\CustomerWiseSaleReportExport;
use App\Exports\CategoryWiseSaleReportExport;

class SaleReportController extends Controller
{
    public $webspice;
    public function __construct(Webspice $webspice)
    {
        $this->webspice = $webspice;
        $this->middleware('JWT');
    }

    public function customerWiseSale(Request $request){
        # permission verfy
        $this->webspice->permissionVerify('report.sale');
        # data validation
        $request->validate(
            [
                'customer_id' => 'required',
                'date_range' => 'required',
            ]
        );
        try { 
            $customer = null;
            $query = Sale::with(['customer']);
            if (strpos($request->date_range, 'to')) {  
                $dateExplode = explode(" to ", $request->date_range);
                $startDate = $dateExplode[0];
                $endDate = $dateExplode[1];              
                $query->whereBetween('sale_date', [$startDate, $endDate]);
            } else {
                $query->where('sale_date',$request->date_range);
            }
            
            if($request->customer_id == 'all'){
                $customer = new \stdClass();
                $customer->customer_name = "All";
                $customer->customer_phone = "";
                $customer->customer_address = "";
            }else{
                $query->where('customer_id',$request->customer_id);
                $customer = Customer::find($request->customer_id);
            }
            $sales = $query->orderBy('sale_date','DESC')->get();
            if($sales->count()==0){
                return response()->json(
                    [
                        'error' => "No record found!",
                    ], 404);
            }
            if($request->btn_type=='excel'){
                // ini_set('max_execution_time', 10 * 60); //10 min
                // ini_set('memory_limit', '2048M');
                return Excel::download(new CustomerWiseSaleReportExport($sales,$customer,"Sale",$request->date_range), 'category.xlsx',\Maatwebsite\Excel\Excel::XLSX);
            }else if($request->btn_type=='pdf'){
               
                $pdf = PDF::loadView('pdf-export.report.sale.customer_wise_sale_report_pdf', [
                    'customer' => $customer,
                    'date_range' => $request->date_range,
                    'sales' => $sales
                ]);
                return $pdf->output();
            
            }else if($request->btn_type=='print'){
               
                $pdf = PDF::loadView('pdf-export.report.sale.customer_wise_sale_report_pdf', [
                    'customer' => $customer,
                    'date_range' => $request->date_range,
                    'sales' => $sales
                ]);
                  // Output the PDF to the browser
                  return $pdf->stream('sale_report.pdf');
            }
            return response()->json([
                'report_type' => $request->btn_type,
                'customer' => $customer,
                'date_range' => $request->date_range,
                'sales' => $sales,
            ]);

        } catch (Exception $e) {
            return response()->json(
                [
                    'error' => $e->getMessage(),
                ], 401);
        }
    } 
    public function categoryWiseSale(Request $request){
        # permission verfy
        $this->webspice->permissionVerify('report.sale');
        # data validation
        $request->validate(
            [
                'category_id' => 'required',
                'date_range' => 'required',
            ]
        );
        try { 
            $query = SaleDetail::select(
                'sales.sale_date',
                'books.title as book_name',
                'authors.author_name',
                'publishers.publisher_name',
                'categories.category_name',
                'customers.customer_name',
                'sale_details.quantity',
                'sale_details.sub_total',
            );
            $query->leftJoin('sales', 'sales.id', '=', 'sale_details.sale_id');
            $query->leftJoin('customers', 'customers.id', '=', 'sales.customer_id');
            $query->leftJoin('books', 'books.id', '=', 'sale_details.book_id');
            $query->leftJoin('authors', 'authors.id', '=', 'books.author_id');
            $query->leftJoin('publishers', 'publishers.id', '=', 'books.publisher_id');
            $query->leftJoin('categories', 'categories.id', '=', 'books.category_id');
            if (strpos($request->date_range, 'to')) {  
                $dateExplode = explode(" to ", $request->date_range);
                $startDate = $dateExplode[0];
                $endDate = $dateExplode[1];              
                $query->whereBetween('sales.sale_date', [$startDate, $endDate]);
            } else {
                $query->where('sales.sale_date',$request->date_range);
            }
            if($request->customer_id != 'all'){
                $query->where('sales.customer_id',$request->customer_id);
            }
            if($request->category_id != 'all'){
                $query->where('books.category_id',$request->category_id);
            }
            if($request->author_id != 'all'){
                $query->where('books.author_id',$request->author_id);
            }
            if($request->publisher_id != 'all'){
                $query->where('books.publisher_id',$request->publisher_id);
            }
            $sales = $query->orderBy('sales.sale_date','DESC')->get();
            if($sales->count()==0){
                return response()->json(
                    [
                        'error' => "No record found!",
                    ], 404);
            }
            if($request->btn_type=='excel'){
                return Excel::download(new CategoryWiseSaleReportExport($sales,"Sale report",$request->date_range), 'category.xlsx',\Maatwebsite\Excel\Excel::XLSX);
            }else if($request->btn_type=='pdf'){
               
                $pdf = PDF::loadView('pdf-export.report.sale.category_wise_sale_report_pdf', [
                    'date_range' => $request->date_range,
                    'sales' => $sales
                ]);
                return $pdf->output();
            
            }else if($request->btn_type=='print'){
               
                $pdf = PDF::loadView('pdf-export.report.sale.category_wise_sale_report_pdf', [
                    'date_range' => $request->date_range,
                    'sales' => $sales
                ]);
                  // Output the PDF to the browser
                  return $pdf->stream('sale_report.pdf');
            }
            return response()->json([
                'report_type' => $request->btn_type,
                'date_range' => $request->date_range,
                'sales' => $sales,
            ]);

        } catch (Exception $e) {
            return response()->json(
                [
                    'error' => $e->getMessage(),
                ], 401);
        }
    } 

}
