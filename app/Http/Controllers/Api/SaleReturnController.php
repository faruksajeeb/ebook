<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Lib\Webspice;
use App\Models\Book;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\SaleReturn;
use App\Models\SaleReturnDetail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use PDF;
// use Image;

class SaleReturnController extends Controller
{
    public $webspice;
    protected $saleReturn;
    protected $saleReturns;
    protected $saleReturnid;
    public $tableName;

    public function __construct(SaleReturn $saleReturn, Webspice $webspice)
    {
        $this->webspice = $webspice;
        $this->sale_returns = $saleReturn;
        $this->tableName = 'sale_returns';
        $this->middleware('JWT');
    }

    public function index()
    {
        #permission verfy
        $this->webspice->permissionVerify('sale_return.manage');
        try {
            $paginate = request('paginate', 5);
            $searchTerm = request('search', '');

            $sortField = request('sort_field', 'created_at');
            if (!in_array($sortField, [
                'id',
                'sale_return_date',
                'total_amount',
                'discount_percentage',
                'discount_amount',
                'vat_percentage',
                'vat_amount',
                'net_amount',
            ])) {
                $sortField = 'created_at';
            }
            $sortDirection = request('sort_direction', 'created_at');
            if (!in_array($sortDirection, ['asc', 'desc'])) {
                $sortDirection = 'desc';
            }

            $filled = array_filter(request([
                'id',
                'sale_return_date',
                'customer_id',
                'total_amount',
                'discount_percentage',
            ]));

            $saleReturns = SaleReturn::with(['customer'])->when(count($filled) > 0, function ($query) use ($filled) {
                foreach ($filled as $column => $value) {
                    if ($column == 'sale_return_date') {
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

            return response()->json($saleReturns);
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
        $this->webspice->permissionVerify('sale_return.create');
        $request->validate(
            [
                'customer_id' => 'required',
                'sale_return_date' => 'required',
                'attach_file' => 'nullable|mimes:jpeg,png,jpg,gif,svg,pdf|max:2048',
            ],
            [
                // 'cart_items.required' => 'The cart must contain at least one item.',
            ]
        );

        try {

            if (($request->cart_items == null || count($request->cart_items) == 0)) {
                return response()->json(
                    [
                        'error' => 'The cart must contain at least one item.',
                    ], 401);
            }
            // Begin a database transaction
            \DB::beginTransaction();
            $input = $request->all();

            if ($request->hasFile('attach_file')) {
                // $image = Image::make($request->file('attach_file'));
                // $imageName = time() . '-' . $request->file('attach_file')->getClientOriginalName();

                $destinationPath = 'assets/img/sale_return/';

                $file = $request->file('attach_file');
                $filename = time() . '-' . $file->getClientOriginalName();
                $uploadSuccess = $file->move(public_path($destinationPath), $filename);

                if ($uploadSuccess) {
                    $input['attach_file'] = $filename;
                }
            }
            $input['created_by'] = $this->webspice->getUserId();

            $inserted = SaleReturn::create($input);
            $saleReturnId = $inserted->id;
            if ($saleReturnId) {
                // Update the balance of the corresponding customer
                $customer = Customer::find($request->customer_id);
                if ($customer) {
                    // Ensure the balance is not negative
                    $newBalance = $customer->balance - $request->net_amount;                   
                    $customer->balance = $newBalance;
                    $customer->save();                    
                }
            }
            # if Cart Items
            if ($request->cart_items != null && count($request->cart_items) > 0) {
                foreach ($input['cart_items'] as $item) {
                    if ($item['quantity'] <= 0) {continue;}
                    // Update the quantity of the corresponding book
                    $book = Book::find($item['id']);

                    if ($book) {
                        // Ensure the quantity is not negative
                        $newQuantity = $book->stock_quantity + $item['quantity'];
                        $book->stock_quantity = $newQuantity;
                        $book->save();
                    }
                    SaleReturnDetail::create([
                        'sale_return_id' => $saleReturnId,
                        'book_id' => $item['id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['price'],
                        'sub_total' => $item['quantity'] * $item['price'],
                        'discount_percentage' => 0,
                        'discount_amount' => 0,
                        'vat_percentage' => 0,
                        'vat_amount' => 0,
                        'net_sub_total' => $item['quantity'] * $item['price'],
                    ]);

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
            $saleReturn = SaleReturn::with('customer')->find($id);
           $saleReturnDetails = SaleReturnDetail::leftJoin('books', 'sale_return_details.book_id', '=', 'books.id')
                ->select('books.id', 'books.title', 'sale_return_details.unit_price as price', 'sale_return_details.quantity', 'sale_return_details.sub_total')
                ->where('sale_return_details.sale_return_id', $id)->get();
           
            $data = [
                'sale_return' => $saleReturn,
                'sale_return_details' => $saleReturnDetails
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
        // dd($request->file('attach_file'));
        // dd($request->isMethod('put'));
        #permission verfy
        $this->webspice->permissionVerify('sale_return.edit');

        # decrypt value
        // $id = $this->webspice->encryptDecrypt('decrypt', $id);

        $request->validate(
            [
                'customer_id' => 'required',
                'sale_return_date' => 'required',
                'attach_file' => 'nullable|mimes:jpeg,png,jpg,gif,svg,pdf|max:2048',
            ]
        );
        if (($request->cart_items == null || count($request->cart_items) == 0)) {
            return response()->json(
                [
                    'error' => 'The cart must contain at least one item.',
                ], 401);
        }

        try {
            $input = $request->all();

            // Begin a database transaction
            \DB::beginTransaction();

            $oldInvoice = SaleReturn::find($id);
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
                Customer::where('id', $previousCustomerID)->increment('balance', $oldInvoice->net_amount);

                // Update the new customer's balance
                Customer::where('id', $newCustomerID)->decrement('balance', $request->net_amount);

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
                $customer->balance -= $invoiceChanges;
                $customer->save();
            }

            if ($request->hasFile('attach_file')) {

                $destinationPath = 'assets/img/sale_return/';
                // $uploadSuccess = $image->save($destinationPath . $imageName);

                $file = $request->file('attach_file');
                $filename = time() . '-' . $file->getClientOriginalName();
                $uploadSuccess = $file->move(public_path($destinationPath), $filename);
                if ($uploadSuccess) {
                    //Delete Old File
                    $imgExist = SaleReturn::where('id', $id)->first();
                    $existingImage = $imgExist->attach_file;
                    if ($existingImage) {

                        if (Storage::disk('local')->exists($destinationPath . $existingImage)) {
                            unlink($destinationPath . $existingImage);
                        }
                    }
                    $input['attach_file'] = $filename;
                }
            }

            $input['updated_by'] = $this->webspice->getUserId();

            $oldInvoice->update($input);

            $this->updateSaleReturnDetails($request, $id);

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
        // return redirect()->route('sale_returns.index');
    }

    public function updateSaleReturnDetails($request, $id)
    {

        # Get the existing sale details for the invoice
        $existingItemsSaleReturnDetails = SaleReturnDetail::where('sale_return_id', $id)->get();
        $existingItemIds = $existingItemsSaleReturnDetails->pluck('book_id')->toArray();
        # Identify items to remove (existing items not in the update request)
        if (!empty($request->cart_items)) {
            // Use array_column on $items
            $regColumnValues = array_column($request->cart_items, 'id');
        } else {
            // Handle the case where $items is empty or null
            $regColumnValues = [];
        }
        $itemsToRemoveBookIds = array_diff($existingItemIds, $regColumnValues);
        #adjust with coresponding book before remove
        $removeItems = $existingItemsSaleReturnDetails->whereIn('book_id', $itemsToRemoveBookIds);
        foreach ($removeItems as $removeItem) {
            $book = Book::find($removeItem->book_id);
            if ($book) {
                // Ensure the quantity is not negative
                $newQuantity = $book->stock_quantity - $removeItem->quantity;
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
        $regularItemsToRemoveIds = $existingItemsSaleReturnDetails->whereIn('book_id', $itemsToRemoveBookIds)->pluck('id')->toArray();
#   dd($regularItemsToRemoveIds);
        SaleReturnDetail::whereIn('id', $regularItemsToRemoveIds)->delete();

        if ($request->cart_items) {
            foreach ($request->cart_items as $detailData) {

                $saleReturnDetail = SaleReturnDetail::where('sale_return_id', $id)->where('flag', 'regular_item')->where('book_id', $detailData['id'])->first();
                //dd($saleReturnDetail->toArray());
                $book = Book::find($detailData['id']);

                if (!$saleReturnDetail) {
                    // Update the quantity of the corresponding book
                    if ($book) {
                        // Ensure the quantity is not negative
                        $newQuantity = $book->stock_quantity + $detailData['quantity'];
                        if ($newQuantity >= 0) {
                            $book->stock_quantity = $newQuantity;
                            $book->save();
                        } else {
                            \DB::rollBack();
                            return response()->json(
                                [
                                    'error' => 'Return Item (' . $book->title . ') Stock Quantity Insufficient!',
                                ], 401);
                        }
                    }
                    // If the item doesn't have an ID, it's a new item, so create it
                    SaleReturnDetail::create([
                        'sale_return_id' => $id,
                        'book_id' => $detailData['id'],
                        'quantity' => $detailData['quantity'],
                        'unit_price' => $detailData['price'],
                        'sub_total' => $detailData['quantity'] * $detailData['price'],
                        'discount_percentage' => 0,
                        'discount_amount' => 0,
                        'vat_percentage' => 0,
                        'vat_amount' => 0,
                        'net_sub_total' => $detailData['quantity'] * $detailData['price'],
                    ]);

                } else {
                    // Update the quantity of the corresponding book
                    if ($book) {
                        // Ensure the quantity is not negative
                        $itemQtyChanges = $detailData['quantity'] - $saleReturnDetail->quantity;
                        // $newQuantity = $book->stock_quantity + $detailData['quantity'];
                        // if ($newQuantity >= 0) {
                        $book->stock_quantity += $itemQtyChanges;
                        $book->save();
                        // }
                    }

                    // If the item has an ID, it's an existing item, so update it
                    $saleReturnDetail->update([
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

    }

    private function calculateInvoiceChanges($oldInvoice, $newInvoiceData)
    {
        $oldTotal = $oldInvoice->net_amount;
        $newTotal = $newInvoiceData->net_amount;
        $invoiceChanges = $newTotal - $oldTotal;

        return $invoiceChanges;
    }
    public function destroy($id)
    {
        #permission verfy
        $this->webspice->permissionVerify('sale_return.delete');
        try {
            # decrypt value
            // $id = $this->webspice->encryptDecrypt('decrypt', $id);

            $saleReturn = $this->sale_returns->findById($id);
            $saleReturn->delete();
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
        $this->webspice->permissionVerify('sale_return.force_delete');
        try {
            #decrypt value
            $id = $this->webspice->encryptDecrypt('decrypt', $id);
            $saleReturn = SaleReturn::withTrashed()->findOrFail($id);
            $saleReturn->forceDelete();
        } catch (Exception $e) {
            $this->webspice->message('error', $e->getMessage());
        }
        return redirect()->back();
    }
    public function restore($id)
    {
        #permission verfy
        $this->webspice->permissionVerify('sale_return.restore');
        try {
            $id = $this->webspice->encryptDecrypt('decrypt', $id);
            $saleReturn = SaleReturn::withTrashed()->findOrFail($id);
            $saleReturn->restore();
        } catch (Exception $e) {
            $this->webspice->message('error', $e->getMessage());
        }
        // return redirect()->route('sale_returns.index', ['status' => 'archived'])->withSuccess(__('User restored successfully.'));
        return redirect()->route('sale_returns.index');
    }

    public function restoreAll()
    {
        #permission verfy
        $this->webspice->permissionVerify('sale_return.restore');
        try {
            $saleReturns = SaleReturn::onlyTrashed()->get();
            foreach ($saleReturns as $saleReturn) {
                $saleReturn->restore();
            }
        } catch (Exception $e) {
            $this->webspice->message('error', $e->getMessage());
        }
        return redirect()->route('sale_returns.index');
        // return redirect()->route('sale_returns.index')->withSuccess(__('All sale_returns restored successfully.'));
    }

    public function getsale_returns()
    {
        $data = SaleReturn::where('status', 1)->get();
        return response()->json($data);
    }

    public function exportInvoicePdf($id)
    {
        try {
            // dd('hello');
            ini_set('max_execution_time', 30 * 60); //30 min
            ini_set('memory_limit', '2048M');
            $saleReturn = SaleReturn::with('customer')->find($id);
            // $saleReturnRegularDetails = SaleReturnDetail::with(['book'])->where('sale_return_id', $id)->where('flag', 'regular_item')->get();
            $saleReturnRegularDetails = SaleReturnDetail::leftJoin('books', 'sale_return_details.book_id', '=', 'books.id')
                ->select('books.id', 'books.title', 'sale_return_details.unit_price as price', 'sale_return_details.quantity', 'sale_return_details.sub_total')
                ->where('sale_return_details.sale_return_id', $id)->where('sale_return_details.flag', 'regular_item')->get();
            $saleReturnCourtesyDetails = SaleReturnDetail::leftJoin('books', 'sale_return_details.book_id', '=', 'books.id')
                ->select('books.id', 'books.title', 'sale_return_details.unit_price', 'sale_return_details.quantity as courtesy_quantity', 'sale_return_details.sub_total')->where('sale_return_id', $id)->where('flag', 'courtesy_copy')->get();

            $paymentInfo = CustomerPayment::where('sale_return_id', $id)->get();
       
            $pdf = PDF::loadView('pdf-export.sale_invoice', [
                'sale' => $saleReturn,
                'sale_regular_details' => $saleReturnRegularDetails,
                'sale_courtesy_details' => $saleReturnCourtesyDetails,
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
