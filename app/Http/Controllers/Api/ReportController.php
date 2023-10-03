<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Lib\Webspice;
use App\Models\Customer;
use App\Models\CustomerPayment;
use Illuminate\Http\Request;
use Exception;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use App\Exports\CategoryExport;

class ReportController extends Controller
{
    public $webspice;
    public function __construct(Webspice $webspice)
    {
        $this->webspice = $webspice;
        $this->middleware('JWT');
    }

    public function customerPayment(Request $request){
    //    dd($request->all());
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
            $customer = Customer::find($request->customer_id);
            $customerPyments = CustomerPayment::with(['payment_method'])->where('customer_id',$request->customer_id)
            ->whereBetween('payment_date', [$startDate, $endDate])->orderBy('payment_date','DESC')->get();
            if($request->btn_type=='excel'){
                ini_set('max_execution_time', 10 * 60); //10 min
                ini_set('memory_limit', '2048M');
                return Excel::download(new CategoryExport, 'category.xlsx',\Maatwebsite\Excel\Excel::XLSX);
            }else if($request->btn_type=='pdf'){
                $data = [];
                $pdf = PDF::loadView('pdf-export.category', ['data' => $data]);
                return $pdf->output();
            }
            return response()->json([
                'report_type' => $request->btn_type,
                'customer' => $customer,
                'customer_payments' => $customerPyments,
            ]);

        } catch (Exception $e) {
            return response()->json(
                [
                    'error' => $e->getMessage(),
                ], 401);
        }
    }
 
    public function customerPaymentExportExcel(Request $request)
    {
        dd($request->all());
        try {
            // dd('hello');
            ini_set('max_execution_time', 30 * 60); //30 min
            ini_set('memory_limit', '2048M');
            return Excel::download(new CategoryExport, 'category.xlsx');
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }

    }
    public function exportPdf()
    {
        try {
            // dd('hello');
            ini_set('max_execution_time', 30 * 60); //30 min
            ini_set('memory_limit', '2048M');
            $data = [];
            $pdf = PDF::loadView('pdf-export.category', ['data' => $data]);
            return $pdf->output();
            // return $pdf->download('itsolutionstuff.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }

    }

    public function import()
    {
        // Excel::import(new CategoryImport, request()->file('file'));

        return back();
    }

    
    public function exportInvoicePdf($id)
    {
        try {
            // dd('hello');
            ini_set('max_execution_time', 30 * 60); //30 min
            ini_set('memory_limit', '2048M');
            $customer_payment = CustomerPayment::with('customer')->find($id);
            // $customer_paymentRegularDetails = SaleDetail::with(['book'])->where('customer_payment_id', $id)->where('flag', 'regular_item')->get();
            $customer_paymentRegularDetails = SaleDetail::leftJoin('books', 'customer_payment_details.book_id', '=', 'books.id')
                ->select('books.id', 'books.title', 'customer_payment_details.unit_price as price', 'customer_payment_details.quantity', 'customer_payment_details.sub_total')
                ->where('customer_payment_details.customer_payment_id', $id)->where('customer_payment_details.flag', 'regular_item')->get();
            $customer_paymentCourtesyDetails = SaleDetail::leftJoin('books', 'customer_payment_details.book_id', '=', 'books.id')
                ->select('books.id', 'books.title', 'customer_payment_details.unit_price', 'customer_payment_details.quantity as courtesy_quantity', 'customer_payment_details.sub_total')->where('customer_payment_id', $id)->where('flag', 'courtesy_copy')->get();

            $paymentInfo = CustomerPayment::where('customer_payment_id', $id)->get();

            $pdf = PDF::loadView('pdf-export.customer_payment_invoice', [
                'customer_payment' => $customer_payment,
                'customer_payment_regular_details' => $customer_paymentRegularDetails,
                'customer_payment_courtesy_details' => $customer_paymentCourtesyDetails,
                'payment_details' => $paymentInfo,
            ]);
            return $pdf->output();
            // return $pdf->download('itsolutionstuff.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }

    }

}
