<?php

namespace App\Http\Controllers\Api\Reports;
use App\Http\Controllers\Controller;
use App\Lib\Webspice;
use App\Models\Customer;
use App\Models\CustomerPayment;
use Illuminate\Http\Request;
use Exception;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use App\Exports\CustomerPaymentReportExport;

class CustomerPaymentReportController extends Controller
{
    public $webspice;
    public function __construct(Webspice $webspice)
    {
        $this->webspice = $webspice;
        $this->middleware('JWT');
    }

    public function index(Request $request){
        # permission verfy
        $this->webspice->permissionVerify('report.customer_payment');
        # data validation
        $request->validate(
            [
                'customer_id' => 'required',
                'date_range' => 'required',
            ]
        );
        try { 
            $dateExplode = explode(" to ", $request->date_range);
            $startDate = $dateExplode[0];
            $endDate = $dateExplode[1];
            $customer = null;
            $query = CustomerPayment::with(['customer','paymentmethod'])->whereBetween('payment_date', [$startDate, $endDate]);
            if($request->customer_id == 'all'){
                $customer = new \stdClass();
                $customer->customer_name = "All";
                $customer->customer_phone = "";
                $customer->customer_address = "";
            }else{
                $query->where('customer_id',$request->customer_id);
                $customer = Customer::find($request->customer_id);
            }
            $customerPyments = $query->orderBy('payment_date','DESC')->get();
            if($request->btn_type=='excel'){
                // ini_set('max_execution_time', 10 * 60); //10 min
                // ini_set('memory_limit', '2048M');
                return Excel::download(new CustomerPaymentReportExport($customerPyments,$customer,"Customer Payment",$request->date_range), 'category.xlsx',\Maatwebsite\Excel\Excel::XLSX);
            }else if($request->btn_type=='pdf'){
               
                $pdf = PDF::loadView('pdf-export.report.customer_payment_report_pdf', [
                    'customer' => $customer,
                    'date_range' => $request->date_range,
                    'customer_payments' => $customerPyments
                ]);
                return $pdf->output();
            
            }else if($request->btn_type=='print'){
               
                $pdf = PDF::loadView('pdf-export.report.customer_payment_report_pdf', [
                    'customer' => $customer,
                    'date_range' => $request->date_range,
                    'customer_payments' => $customerPyments
                ]);
                  // Output the PDF to the browser
                  return $pdf->stream('customer_payment_report.pdf');
            }
            return response()->json([
                'report_type' => $request->btn_type,
                'customer' => $customer,
                'date_range' => $request->date_range,
                'customer_payments' => $customerPyments,
            ]);

        } catch (Exception $e) {
            return response()->json(
                [
                    'error' => $e->getMessage(),
                ], 401);
        }
    } 

}
