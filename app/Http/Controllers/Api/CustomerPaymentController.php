<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Lib\Webspice;
use App\Models\Book;
use App\Models\Customer;
use App\Models\CustomerPayment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use PDF;

// use Image;

class CustomerPaymentController extends Controller
{
    public $webspice;
    protected $customer_payment;
    protected $customer_payments;
    protected $customer_paymentid;
    public $tableName;

    public function __construct(CustomerPayment $customer_payment, Webspice $webspice)
    {
        $this->webspice = $webspice;
        $this->customer_payments = $customer_payment;
        $this->tableName = 'customer_payments';
        $this->middleware('JWT');
    }

    public function index()
    {

        try {
            $paginate = request('paginate', 5);
            $searchTerm = request('search', '');

            $sortField = request('sort_field', 'created_at');
            if (!in_array($sortField, [
                'id',
                'payment_date',
                'payment_amount',
                'paid_by',
            ])) {
                $sortField = 'created_at';
            }
            $sortDirection = request('sort_direction', 'created_at');
            if (!in_array($sortDirection, ['asc', 'desc'])) {
                $sortDirection = 'desc';
            }

            $filled = array_filter(request([
                'id',
                'payment_date',
                'customer_id',
                'payment_amount',
                'payment_method',
                'discount_percentage',
            ]));

            $customer_payments = CustomerPayment::with(['customer','payment_method'])->when(count($filled) > 0, function ($query) use ($filled) {
                foreach ($filled as $column => $value) {
                    if ($column == 'payment_date') {
                        $dateExplode = explode(" to ", $value);
                        $startDate = $dateExplode[0];
                        $endDate = $dateExplode[1];
                        $query->whereBetween($column, [$startDate, $endDate]);
                    } else {
                        $query->where($column, 'LIKE', '%' . $value . '%');
                    }

                }

            })->when(request('search', '') != '', function ($query) use ($searchTerm) {
                $query->search(trim($searchTerm));
            })->orderBy($sortField, $sortDirection)->paginate($paginate);

            return response()->json($customer_payments);
        } catch (Exception $e) {
            // $this->webspice->message('error', $e->getMessage());
            return response()->json(
                [
                    'error' => $e->getMessage(),
                ], 401);
        }
    }

    public function store(Request $request)
    {
        // dd($request->all());
        #permission verfy
        // $this->webspice->permissionVerify('customer_payment.create');
        $request->validate(
            [
                'customer_id' => 'required',
                'payment_date' => 'required',
                'payment_amount' => 'numeric|min:0', // Ensure payment_amount is a number and greater than or equal to 0
                // 'payment_method' => 'required_if:payment_amount,0', // payment_method is required if payment_amount is greater than 0
                'payment_method' =>'required', // payment_method is required if payment_amount is greater than 0
                // 'payment_description' => Rule::requiredIf($request->payment_amount > 0), // payment_method is required if payment_amount is greater than 0
                'paid_by' => 'required', // payment_method is required if payment_amount is greater than 0
                'file' => 'nullable|mimes:jpeg,png,jpg,gif,svg,pdf|max:2048',
            ]
        );

        try {
            // Begin a database transaction
            \DB::beginTransaction();
            $input = $request->all();

            if ($request->hasFile('file')) {
                $destinationPath = 'assets/img/customer_payment/';

                $file = $request->file('file');
                $filename = time() . '-' . $file->getClientOriginalName();
                $uploadSuccess = $file->move(public_path($destinationPath), $filename);

                if ($uploadSuccess) {
                    $input['file'] = $filename;
                }
            }
            $input['transaction_type'] = 'direct_payment';
            $input['created_by'] = $this->webspice->getUserId();

            $inserted = CustomerPayment::create($input);
            $customerPaymentId = $inserted->id;
            if ($customerPaymentId) {
                // Update the balance of the corresponding customer
                $customer = Customer::find($request->customer_id);
                if ($customer) {
                    // Ensure the balance is not negative
                    $newBalance = $customer->balance - $request->payment_amount;
                    // if ($newBalance >= 0) {
                    // **** Customer advance pay allowed
                    $customer->balance = $newBalance;
                    $customer->save();
                    // }else{
                    //     \DB::rollback();
                    //     return response()->json(['error' => 'Payment amount must be less than or equal to balance'], 401);
                    // }
                }
            }
            // Commit the database transaction
            \DB::commit();

        } catch (Exception $e) {
            // $this->webspice->message('error', $e->getMessage());
            // Handle errors
            \DB::rollback();
            return response()->json(
                [
                    'error' => $e->getMessage(),
                ], 401);
        }

        // return redirect()->back();
    }

    public function show($id)
    {
        try {
            $customer_payment = CustomerPayment::with('customer')->find($id);
            // $customer_paymentRegularDetails = SaleDetail::with(['book'])->where('customer_payment_id', $id)->where('flag', 'regular_item')->get();
            $customer_paymentRegularDetails = SaleDetail::leftJoin('books', 'customer_payment_details.book_id', '=', 'books.id')
                ->select('books.id', 'books.title', 'customer_payment_details.unit_price as price', 'customer_payment_details.quantity', 'customer_payment_details.sub_total')
                ->where('customer_payment_details.customer_payment_id', $id)->where('customer_payment_details.flag', 'regular_item')->get();
            $customer_paymentCourtesyDetails = SaleDetail::leftJoin('books', 'customer_payment_details.book_id', '=', 'books.id')
                ->select('books.id', 'books.title', 'customer_payment_details.unit_price', 'customer_payment_details.quantity as courtesy_quantity', 'customer_payment_details.sub_total')->where('customer_payment_id', $id)->where('flag', 'courtesy_copy')->get();

            $paymentInfo = CustomerPayment::where('customer_payment_id', $id)->get();
            $data = [
                'customer_payment' => $customer_payment,
                'customer_payment_regular_details' => $customer_paymentRegularDetails,
                'customer_payment_courtesy_details' => $customer_paymentCourtesyDetails,
                'payment_details' => $paymentInfo,
            ];
            return $data;
        } catch (Exception $e) {
            // $this->webspice->message('error', $e->getMessage());
            return response()->json(
                [
                    'error' => $e->getMessage(),
                ], 401);
        }
    }

    public function update(Request $request, $id)
    {

        // dd($request->all());
        // dd($request->file('file'));
        // dd($request->isMethod('put'));
        #permission verfy
        // $this->webspice->permissionVerify('customer_payment.edit');

        # decrypt value
        // $id = $this->webspice->encryptDecrypt('decrypt', $id);

        $request->validate(
            [
                'customer_id' => 'required',
                'payment_date' => 'required',
                'payment_amount' => 'numeric|min:0', // Ensure payment_amount is a number and greater than or equal to 0
                // 'payment_method' => 'required_if:payment_amount,0', // payment_method is required if payment_amount is greater than 0
                'payment_method' =>'required', // payment_method is required if payment_amount is greater than 0
                // 'payment_description' => Rule::requiredIf($request->payment_amount > 0), // payment_method is required if payment_amount is greater than 0
                'paid_by' => 'required', // payment_method is required if payment_amount is greater than 0
                'file' => 'nullable|mimes:jpeg,png,jpg,gif,svg,pdf|max:2048',
            ]
        );
        if (($request->cart_items == null || count($request->cart_items) == 0) && ($request->courtesy_cart_items == null || count($request->courtesy_cart_items) <= 0)) {
            return response()->json(
                [
                    'error' => 'The cart/ courtesy cart must contain at least one item.',
                ], 401);
        }

        try {
            $input = $request->all();

            // Begin a database transaction
            \DB::beginTransaction();

            $oldInvoice = CustomerPayment::find($id);
            if (!$oldInvoice) {
                // Handle if the invoice does not exist
                \DB::rollBack();
                return response()->json(
                    [
                        'error' => 'Invoice not found',
                    ], 401);
            }

            $previousCustomerID = $oldInvoice->customer_id;
            $newCustomerID = $request->customer_id;
            # Check Customer Changes
            if ($previousCustomerID != $newCustomerID) {

                // Update the previous customer's balance
                Customer::where('id', $previousCustomerID)->decrement('balance', $oldInvoice->due_amount);

                // Update the new customer's balance
                Customer::where('id', $newCustomerID)->increment('balance', $request->due_amount);

            } else {
                // Calculate the changes in the invoice amount
                $invoiceChanges = $this->calculateInvoiceChanges($oldInvoice, $request);
                # Update the balance of the corresponding customer
                $customer = Customer::find($previousCustomerID);
                if (!$customer) {
                    // Handle if the customer does not exist
                    \DB::rollBack();
                    return response()->json(
                        [
                            'error' => 'Customer not found',
                        ], 401);
                }
                $customer->balance += $invoiceChanges;
                $customer->save();
            }

            #  Update the payment history (if exist) of the corresponding customer

            $customerPayment = CustomerPayment::where('customer_payment_id', $id)->latest()->first();

            if ($customerPayment) {
                if ($request->payment_amount != 0) {

                    $customerPayment->customer_id = $request->customer_id;
                    $customerPayment->payment_date = $request->customer_payment_date;
                    $customerPayment->payment_amount = $request->payment_amount;
                    $customerPayment->payment_method = $request->payment_method;
                    $customerPayment->paid_by = $request->paid_by;
                    $customerPayment->payment_description = $request->payment_description;
                    $customerPayment->transaction_type = 'invoice_update';
                    $customerPayment->updated_by = $this->webspice->getUserId();
                    $customerPayment->save();
                } else {
                    // CustomerPayment::where('id', $customerPayment->id)->delete();
                    $customerPayment->delete();
                }

            } else {
                // Insert into customer payment if pay-amount is grater than 0
                if ($request->payment_amount > 0) {
                    $paymentTransaction = new CustomerPayment([
                        'customer_id' => $request->customer_id,
                        'customer_payment_id' => $id,
                        'payment_date' => $request->customer_payment_date,
                        'payment_amount' => $request->payment_amount,
                        'payment_method' => $request->payment_method,
                        'paid_by' => $request->paid_by,
                        'payment_description' => $request->payment_description,
                        // 'file' => ,
                        'transaction_type' => 'invoice_update',
                        'created_by' => $this->webspice->getUserId(),
                    ]);
                    $paymentTransaction->save();
                }
            }

            if ($request->hasFile('file')) {

                $destinationPath = 'assets/img/customer_payment/';
                // $uploadSuccess = $image->save($destinationPath . $imageName);

                $file = $request->file('file');
                $filename = time() . '-' . $file->getClientOriginalName();
                $uploadSuccess = $file->move(public_path($destinationPath), $filename);
                if ($uploadSuccess) {
                    //Delete Old File
                    $imgExist = CustomerPayment::where('id', $id)->first();
                    $existingImage = $imgExist->file;
                    if ($existingImage) {

                        if (Storage::disk('local')->exists($destinationPath . $existingImage)) {
                            unlink($destinationPath . $existingImage);
                        }
                    }
                    $input['file'] = $filename;
                }
            }

            $input['updated_by'] = $this->webspice->getUserId();

            $oldInvoice->update($input);

            $this->updateSaleDetails($request, $id);

            // Commit the database transaction
            \DB::commit();
        } catch (Exception $e) {
            // $this->webspice->message('error', $e->getMessage());
            // Handle errors
            \DB::rollback();
            return response()->json(
                [
                    'error' => $e->getMessage(),
                ], 401);
        }
        // return redirect()->route('customer_payments.index');
    }

    public function updateSaleDetails($request, $id)
    {

        # Get the existing customer_payment details for the invoice
        $regularItemExistingSaleDetails = SaleDetail::where('customer_payment_id', $id)->where('flag', 'regular_item')->get();
        $regularExistingIds = $regularItemExistingSaleDetails->pluck('book_id')->toArray();
        # Identify items to remove (existing items not in the update request)
        if (!empty($request->cart_items)) {
            // Use array_column on $items
            $regColumnValues = array_column($request->cart_items, 'id');
        } else {
            // Handle the case where $items is empty or null
            $regColumnValues = [];
        }
        $regularItemsToRemoveBookIds = array_diff($regularExistingIds, $regColumnValues);
        #adjust with coresponding book before remove
        $removeItems = $regularItemExistingSaleDetails->whereIn('book_id', $regularItemsToRemoveBookIds);
        foreach ($removeItems as $removeItem) {
            $book = Book::find($removeItem->book_id);
            if ($book) {
                // Ensure the quantity is not negative
                $newQuantity = $book->stock_quantity + $removeItem->quantity;
                if ($newQuantity >= 0) {
                    $book->stock_quantity = $newQuantity;
                    $book->save();
                } else {
                    \DB::rollBack();
                    return response()->json(
                        [
                            'error' => 'Removal Item Stock Quantity Insufficient!',
                        ], 401);
                }
            }
        }
        $regularItemsToRemoveIds = $regularItemExistingSaleDetails->whereIn('book_id', $regularItemsToRemoveBookIds)->pluck('id')->toArray();
#   dd($regularItemsToRemoveIds);
        SaleDetail::whereIn('id', $regularItemsToRemoveIds)->delete();

        if ($request->cart_items) {
            foreach ($request->cart_items as $detailData) {

                $customer_paymentDetail = SaleDetail::where('customer_payment_id', $id)->where('flag', 'regular_item')->where('book_id', $detailData['id'])->first();
                //dd($customer_paymentDetail->toArray());
                $book = Book::find($detailData['id']);

                if (!$customer_paymentDetail) {
                    // Update the quantity of the corresponding book
                    if ($book) {
                        // Ensure the quantity is not negative
                        $newQuantity = $book->stock_quantity - $detailData['quantity'];
                        if ($newQuantity >= 0) {
                            $book->stock_quantity = $newQuantity;
                            $book->save();
                        } else {
                            \DB::rollBack();
                            return response()->json(
                                [
                                    'error' => 'Removal Item (' . $book->title . ') Stock Quantity Insufficient!',
                                ], 401);
                        }
                    }
                    // If the item doesn't have an ID, it's a new item, so create it
                    SaleDetail::create([
                        'customer_payment_id' => $id,
                        'book_id' => $detailData['id'],
                        'quantity' => $detailData['quantity'],
                        'unit_price' => $detailData['price'],
                        'sub_total' => $detailData['quantity'] * $detailData['price'],
                        'discount_percentage' => 0,
                        'discount_amount' => 0,
                        'vat_percentage' => 0,
                        'vat_amount' => 0,
                        'net_sub_total' => $detailData['quantity'] * $detailData['price'],
                        'flag' => 'regular_item',
                    ]);

                } else {
                    // Update the quantity of the corresponding book
                    if ($book) {
                        // Ensure the quantity is not negative
                        $itemQtyChanges = $detailData['quantity'] - $customer_paymentDetail->quantity;
                        // $newQuantity = $book->stock_quantity + $detailData['quantity'];
                        // if ($newQuantity >= 0) {
                        $book->stock_quantity -= $itemQtyChanges;
                        $book->save();
                        // }
                    }

                    // If the item has an ID, it's an existing item, so update it
                    $customer_paymentDetail->update([
                        'quantity' => $detailData['quantity'],
                        'unit_price' => $detailData['price'],
                        'sub_total' => $detailData['quantity'] * $detailData['price'],
                        'discount_percentage' => 0,
                        'discount_amount' => 0,
                        'vat_percentage' => 0,
                        'vat_amount' => 0,
                        'net_sub_total' => $detailData['quantity'] * $detailData['price'],
                    ]);
                }

            }
        }

        # Get the existing customer_payment details for the invoice
        $courtesyItemExistingSaleDetails = SaleDetail::where('customer_payment_id', $id)->where('flag', 'courtesy_copy')->get();
        $courtesyExistingIds = $courtesyItemExistingSaleDetails->pluck('book_id')->toArray();
        if (!empty($request->courtesy_cart_items)) {
            // Use array_column on $items
            $columnValues = array_column($request->courtesy_cart_items, 'id');
        } else {
            // Handle the case where $items is empty or null
            $columnValues = [];
        }
        # Identify items to remove (existing items not in the update request)
        $courtesyItemsToRemoveBookIds = array_diff($courtesyExistingIds, $columnValues);

        $removeCourtesyItems = $courtesyItemExistingSaleDetails->whereIn('book_id', $courtesyItemsToRemoveBookIds);
        foreach ($removeCourtesyItems as $removeItem) {
            $book = Book::find($removeItem->book_id);
            if ($book) {
                // Ensure the quantity is not negative
                $newQuantity = $book->stock_quantity + $removeItem->quantity;
                if ($newQuantity >= 0) {
                    $book->stock_quantity = $newQuantity;
                    $book->save();
                } else {
                    \DB::rollBack();
                    return response()->json(
                        [
                            'error' => 'Removal Item (' . $book->title . ') Stock Quantity Insufficient!',
                        ], 401);
                }
            }
        }
        $courtesyItemsToRemoveIds = $courtesyItemExistingSaleDetails->whereIn('book_id', $courtesyItemsToRemoveBookIds)->pluck('id')->toArray();
#       dd($courtesyItemsToRemoveIds);
        SaleDetail::whereIn('id', $courtesyItemsToRemoveIds)->delete();
        if ($request->courtesy_cart_items) {
            foreach ($request->courtesy_cart_items as $detailData) {

                $customer_paymentDetail = SaleDetail::where('customer_payment_id', $id)->where('flag', 'courtesy_copy')->where('book_id', $detailData['id'])->first();
                //dd($customer_paymentDetail->toArray());

                $book = Book::find($detailData['id']);
                if (!$customer_paymentDetail) {
                    // Update the quantity of the corresponding book
                    if ($book) {
                        // Ensure the quantity is not negative
                        $newQuantity = $book->stock_quantity - $detailData['courtesy_quantity'];
                        if ($newQuantity >= 0) {
                            $book->stock_quantity = $newQuantity;
                            $book->save();
                        } else {
                            \DB::rollBack();
                            return response()->json(
                                [
                                    'error' => 'Item (' . $book->title . ') Stock Quantity Insufficient!',
                                ], 401);
                        }
                    }
                    // If the item doesn't have an ID, it's a new item, so create it
                    SaleDetail::create([
                        'customer_payment_id' => $id,
                        'book_id' => $detailData['id'],
                        'quantity' => $detailData['courtesy_quantity'],
                        'unit_price' => $detailData['unit_price'],
                        'sub_total' => $detailData['courtesy_quantity'] * $detailData['unit_price'],
                        'discount_percentage' => 0,
                        'discount_amount' => 0,
                        'vat_percentage' => 0,
                        'vat_amount' => 0,
                        'net_sub_total' => $detailData['courtesy_quantity'] * $detailData['unit_price'],
                        'flag' => 'courtesy_copy',
                    ]);

                } else {
                    // Update the quantity of the corresponding book
                    if ($book) {
                        $itemQtyChanges = $detailData['courtesy_quantity'] - $customer_paymentDetail->quantity;
                        $book->stock_quantity -= $itemQtyChanges;
                        $book->save();
                        // }
                    }
                    // If the item has an ID, it's an existing item, so update it
                    $customer_paymentDetail->update([
                        'quantity' => $detailData['courtesy_quantity'],
                        'unit_price' => $detailData['unit_price'],
                        'sub_total' => $detailData['courtesy_quantity'] * $detailData['unit_price'],
                        'discount_percentage' => 0,
                        'discount_amount' => 0,
                        'vat_percentage' => 0,
                        'vat_amount' => 0,
                        'net_sub_total' => $detailData['courtesy_quantity'] * $detailData['unit_price'],
                    ]);

                }
            }
        }
    }

    private function calculateInvoiceChanges($oldInvoice, $newInvoiceData)
    {
        $oldTotal = $oldInvoice->due_amount;
        $newTotal = $newInvoiceData->due_amount;
        $invoiceChanges = $newTotal - $oldTotal;

        return $invoiceChanges;
    }
    public function destroy($id)
    {
        #permission verfy
        // $this->webspice->permissionVerify('customer_payment.delete');
        try {
            # decrypt value
            // $id = $this->webspice->encryptDecrypt('decrypt', $id);

            $customer_payment = $this->customer_payments->findById($id);
            $customer_payment->delete();
        } catch (Exception $e) {
            // $this->webspice->message('error', $e->getMessage());
            return response()->json(
                [
                    'error' => $e->getMessage(),
                ], 401);
        }
        // return back();
    }

    public function forceDelete($id)
    {
        return response()->json(['error' => 'Unauthenticated.'], 401);
        #permission verfy
        $this->webspice->permissionVerify('customer_payment.force_delete');
        try {
            #decrypt value
            $id = $this->webspice->encryptDecrypt('decrypt', $id);
            $customer_payment = CustomerPayment::withTrashed()->findOrFail($id);
            $customer_payment->forceDelete();
        } catch (Exception $e) {
            $this->webspice->message('error', $e->getMessage());
        }
        return redirect()->back();
    }
    public function restore($id)
    {
        #permission verfy
        $this->webspice->permissionVerify('customer_payment.restore');
        try {
            $id = $this->webspice->encryptDecrypt('decrypt', $id);
            $customer_payment = CustomerPayment::withTrashed()->findOrFail($id);
            $customer_payment->restore();
        } catch (Exception $e) {
            $this->webspice->message('error', $e->getMessage());
        }
        // return redirect()->route('customer_payments.index', ['status' => 'archived'])->withSuccess(__('User restored successfully.'));
        return redirect()->route('customer_payments.index');
    }

    public function restoreAll()
    {
        #permission verfy
        $this->webspice->permissionVerify('customer_payment.restore');
        try {
            $customer_payments = CustomerPayment::onlyTrashed()->get();
            foreach ($customer_payments as $customer_payment) {
                $customer_payment->restore();
            }
        } catch (Exception $e) {
            $this->webspice->message('error', $e->getMessage());
        }
        return redirect()->route('customer_payments.index');
        // return redirect()->route('customer_payments.index')->withSuccess(__('All customer_payments restored successfully.'));
    }

    public function getcustomer_payments()
    {
        $data = CustomerPayment::where('status', 1)->get();
        return response()->json($data);
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
