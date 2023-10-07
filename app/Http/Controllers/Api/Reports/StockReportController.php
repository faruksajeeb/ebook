<?php

namespace App\Http\Controllers\Api\Reports;
use App\Http\Controllers\Controller;
use App\Lib\Webspice;
use App\Models\Book;
use Illuminate\Http\Request;
use Exception;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use App\Exports\StockReportExport;
use App\Exports\StockAlertReportExport;

class StockReportController extends Controller
{
    public $webspice;
    public function __construct(Webspice $webspice)
    {
        $this->webspice = $webspice;
        $this->middleware('JWT');
    }

    public function stockReport(Request $request){
        # permission verfy
        $this->webspice->permissionVerify('report.stock');
        # data validation
        // $request->validate(
        //     [
        //         'customer_id' => 'required',
        //         'date_range' => 'required',
        //     ]
        // );
        try { 
            $customer = null;
            $query = Book::with(['publisher','author','category']);
            
            if($request->category_id != 'all'){
                $query->where('category_id',$request->category_id);
            }
            if($request->author_id != 'all'){
                $query->where('author_id',$request->author_id);
            }
            if($request->publisher_id != 'all'){
                $query->where('publisher_id',$request->publisher_id);
            }
            if($request->stock_status == 'stock_available'){
                $query->where('stock_quantity','>',0);
            }else if($request->stock_status == 'stock_out'){
                $query->where('stock_quantity',0);
            }
           
            $stocks = $query->get();
            if($stocks->count()==0){
                return response()->json(
                    [
                        'error' => "No record found!",
                    ], 404);
            }
            if($request->btn_type=='excel'){
                // ini_set('max_execution_time', 10 * 60); //10 min
                // ini_set('memory_limit', '2048M');
                return Excel::download(new StockReportExport($stocks,"Stock Report"), 'stock_report.xlsx',\Maatwebsite\Excel\Excel::XLSX);
            }else if($request->btn_type=='pdf'){
               
                $pdf = PDF::loadView('pdf-export.report.stock.stock_report_pdf', [
                    
                    'stocks' => $stocks
                ]);
                return $pdf->output();
            
            }else if($request->btn_type=='print'){
               
                $pdf = PDF::loadView('pdf-export.report.stock.stock_report_pdf', [                   
                    'stocks' => $stocks
                ]);
                  // Output the PDF to the browser
                  return $pdf->stream('stock_report.pdf');
            }
            return response()->json([
                'report_type' => $request->btn_type,
                'stocks' => $stocks,
            ]);

        } catch (Exception $e) {
            return response()->json(
                [
                    'error' => $e->getMessage(),
                ], 401);
        }
    } 

    public function stockAlertReport(Request $request){
        # permission verfy
        $this->webspice->permissionVerify('report.stock');
        # data validation
        $request->validate(
            [
                'stock_less_than' => 'required',
            ]
        );
        try { 
            $customer = null;
            $query = Book::with(['publisher','author','category']);
            
            if($request->category_id != 'all'){
                $query->where('category_id',$request->category_id);
            }
            if($request->author_id != 'all'){
                $query->where('author_id',$request->author_id);
            }
            if($request->publisher_id != 'all'){
                $query->where('publisher_id',$request->publisher_id);
            }
            
            $query->where('stock_quantity','<',$request->stock_less_than);
            
           
            $stocks = $query->get();
            if($stocks->count()==0){
                return response()->json(
                    [
                        'error' => "No record found!",
                    ], 404);
            }
            if($request->btn_type=='excel'){
                // ini_set('max_execution_time', 10 * 60); //10 min
                // ini_set('memory_limit', '2048M');
                return Excel::download(new StockAlertReportExport($stocks,"Stock Alert Report"), 'stock_alert_report.xlsx',\Maatwebsite\Excel\Excel::XLSX);
            }else if($request->btn_type=='pdf'){
               
                $pdf = PDF::loadView('pdf-export.report.stock.stock_alert_report_pdf', [
                    
                    'stocks' => $stocks
                ]);
                return $pdf->output();
            
            }else if($request->btn_type=='print'){
               
                $pdf = PDF::loadView('pdf-export.report.stock.stock_alert_report_pdf', [                   
                    'stocks' => $stocks
                ]);
                  // Output the PDF to the browser
                  return $pdf->stream('stock_report.pdf');
            }
            return response()->json([
                'report_type' => $request->btn_type,
                'stocks' => $stocks,
            ]);

        } catch (Exception $e) {
            return response()->json(
                [
                    'error' => $e->getMessage(),
                ], 401);
        }
    }
}
