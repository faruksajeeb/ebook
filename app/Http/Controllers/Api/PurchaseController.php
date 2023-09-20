<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Lib\Webspice;
use App\Models\Book;
use App\Models\purchase;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Fluent;
use Image;

class PurchaseController extends Controller
{
    public $webspice;
    protected $purchase;
    protected $purchases;
    protected $purchaseid;
    public $tableName;

    public function __construct(purchase $purchase, Webspice $webspice)
    {
        $this->webspice = $webspice;
        $this->purchases = $purchase;
        $this->tableName = 'purchases';
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
                'purchase_date',
                'total_amount',
                'discount_percentage',
                'discount_amount',
                'vat_percentage',
                'vat_amount',
                'net_amount',
                'pay_amount',
                'due_amount',
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
                'purchase_date',
                'supplier_id',
                'total_amount',
                'discount_percentage',
            ]));

            $purchases = Purchase::with(['supplier'])->when(count($filled) > 0, function ($query) use ($filled) {
                foreach ($filled as $column => $value) {
                    if ($column == 'purchase_date') {
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

            return response()->json($purchases);
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
        // $this->webspice->permissionVerify('purchase.create');

        // Unique check __> purchase name, author, publisher

        $validator = $request->validate(
            [
                'supplier_id' => 'required',
                'purchase_date' => 'required',
                'pay_amount' => 'numeric|min:0', // Ensure pay_amount is a number and greater than or equal to 0
                // 'payment_method' => 'required_if:pay_amount,0', // payment_method is required if pay_amount is greater than 0
                'payment_method' => Rule::requiredIf($request->pay_amount > 0), // payment_method is required if pay_amount is greater than 0
                'payment_description' => Rule::requiredIf($request->pay_amount > 0), // payment_method is required if pay_amount is greater than 0
                'paid_by' => Rule::requiredIf($request->pay_amount > 0), // payment_method is required if pay_amount is greater than 0
                'attach_file' => 'nullable|mimes:jpeg,png,jpg,gif,svg,pdf|max:2048',
            ],
            [
                // 'cart_items.required' => 'The cart must contain at least one item.',
            ]
        );

        try {

            if (($request->cart_items == null || count($request->cart_items) == 0) && ($request->courtesy_cart_items == null || count($request->courtesy_cart_items) <= 0)) {
                return response()->json(
                    [
                        'error' => 'The cart/ courtesy cart must contain at least one item.',
                    ], 401);
            }

            $input = $request->all();

            if ($request->hasFile('attach_file')) {
                // $image = Image::make($request->file('attach_file'));
                // $imageName = time() . '-' . $request->file('attach_file')->getClientOriginalName();

                $destinationPath = 'assets/img/purchase/';

                $file = $request->file('attach_file');
                $filename = time() . '-' . $file->getClientOriginalName();
                $uploadSuccess = $file->move(public_path($destinationPath), $filename);

                // $uploadSuccess = $image->save($destinationPath . $imageName);

                /**
                 * Generate Thumbnail Image Upload on Folder Code
                 */
                // $destinationPathThumbnail = public_path('assets/img/purchase/thumbnail/');
                // $image->resize(50, 50);
                // $image->save($destinationPathThumbnail . $imageName);

                if ($uploadSuccess) {
                    $input['attach_file'] = $filename;
                }
            }
            $input['created_by'] = $this->webspice->getUserId();

            $inserted = Purchase::create($input);
            $purchaseId = $inserted->id;
            if ($purchaseId) {
                // Update the balance of the corresponding supplier
                $supplier = Supplier::find($request->supplier_id);
                if ($supplier) {
                    // Ensure the balance is not negative
                    $newBalance = $supplier->balance + $request->due_amount;
                    if ($newBalance >= 0) {
                        $supplier->balance = $newBalance;
                        $supplier->save();
                    }
                }
                // Insert into supplier payment if pay-amount is grater than 0
                if($request->pay_amount>=0){
                    SupplierPayment::create([
                        'supplier_id' => $request->supplier_id,
                        'purchase_id' => $purchaseId,
                        'payment_date' =>$request->purchase_date,
                        'payment_amount' => $request->pay_amount,
                        'payment_method' => $request->payment_method,
                        'paid_by' =>$request->paid_by ,
                        'payment_description' => $request->payment_description,
                        // 'file' => ,
                        'created_by' => $this->webspice->getUserId(),
                    ]);
                }
            }
           
            // dd($input['cart_items']);
            # if Cart Items
            if ($request->cart_items != null && count($request->cart_items) > 0) {
                foreach ($input['cart_items'] as $item) {
                    if ($item['quantity'] <= 0) {continue;}
                    PurchaseDetail::create([
                        'purchase_id' => $purchaseId,
                        'book_id' => $item['id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['price'],
                        'sub_total' => $item['quantity'] * $item['price'],
                        'discount_percentage' => 0,
                        'discount_amount' => 0,
                        'vat_percentage' => 0,
                        'vat_amount' => 0,
                        'net_sub_total' => $item['quantity'] * $item['price'],
                        'flag' => 'regular_item',
                    ]);
                    // Update the quantity of the corresponding book
                    $book = Book::find($item['id']);

                    if ($book) {
                        // Ensure the quantity is not negative
                        $newQuantity = $book->stock_quantity + $item['quantity'];
                        if ($newQuantity >= 0) {
                            $book->stock_quantity = $newQuantity;
                            $book->save();
                        } else {
                            // Handle insufficient quantity error
                            return response()->json(['error' => 'Insufficient quantity in stock.']);
                        }
                    }
                }
            }
            if ($request->courtesy_cart_items != null && count($request->courtesy_cart_items) > 0) {
                foreach ($input['courtesy_cart_items'] as $item) {
                    if ($item['courtesy_quantity'] <= 0) {continue;}
                    PurchaseDetail::create([
                        'purchase_id' => $purchaseId,
                        'book_id' => $item['id'],
                        'quantity' => $item['courtesy_quantity'],
                        'unit_price' => $item['unit_price'],
                        'sub_total' => $item['courtesy_quantity'] * $item['unit_price'],
                        'discount_percentage' => 0,
                        'discount_amount' => 0,
                        'vat_percentage' => 0,
                        'vat_amount' => 0,
                        'net_sub_total' => $item['courtesy_quantity'] * $item['unit_price'],
                        'flag' => 'courtesy_copy',
                    ]);
                    // Update the quantity of the corresponding book
                    $book = Book::find($item['id']);

                    if ($book) {
                        // Ensure the quantity is not negative
                        $newQuantity = $book->stock_quantity + $item['courtesy_quantity'];
                        if ($newQuantity >= 0) {
                            $book->stock_quantity = $newQuantity;
                            $book->save();
                        } else {
                            // Handle insufficient quantity error
                            return response()->json(['error' => 'Insufficient quantity in stock.']);
                        }
                    }
                }
            }
        } catch (Exception $e) {
            // $this->webspice->message('error', $e->getMessage());
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
            $purchase = Purchase::with('supplier')->find($id);
            // $purchaseRegularDetails = PurchaseDetail::with(['book'])->where('purchase_id', $id)->where('flag', 'regular_item')->get();
            $purchaseRegularDetails = PurchaseDetail::leftJoin('books', 'purchase_details.book_id', '=', 'books.id')
            ->select('books.id','books.title','purchase_details.unit_price as price', 'purchase_details.quantity','purchase_details.sub_total')
            ->where('purchase_details.purchase_id', $id)->where('purchase_details.flag', 'regular_item')->get();
            $purchaseCourtesyDetails = PurchaseDetail::leftJoin('books', 'purchase_details.book_id', '=', 'books.id')
            ->select('books.id','books.title','purchase_details.unit_price', 'purchase_details.quantity as courtesy_quantity','purchase_details.sub_total')->where('purchase_id', $id)->where('flag', 'courtesy_copy')->get();

            $paymentInfo = SupplierPayment::where('purchase_id',$id)->get();
            $data = [
                'purchase' => $purchase,
                'purchase_regular_details' => $purchaseRegularDetails,
                'purchase_courtesy_details' => $purchaseCourtesyDetails,
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
        dd($request->all());
        // dd($request->isMethod('put'));
        #permission verfy
        // $this->webspice->permissionVerify('purchase.edit');

        # decrypt value
        // $id = $this->webspice->encryptDecrypt('decrypt', $id);

        $request->validate(
            [
                'title' => ['required', 'min:1', 'max:1000', Rule::unique('purchases')->ignore($id, 'id')->where(function ($query) use ($request) {
                    return $query->where('title', $request->title)
                        ->where('publisher_id', $request->publisher_id)
                        ->where('author_id', $request->author_id);
                })],
                'publisher_id' => 'required',
                'author_id' => 'required',
                'buying_discount_percentage' => 'required',
                'selling_discount_percentage' => 'required',
                'buying_vat_percentage' => 'required',
                'selling_vat_percentage' => 'required',
                'price' => ['required', 'numeric', 'min:0.01'],
                'publication_year' => 'required',
            ],
            [
                'title.unique' => 'The purchase (title,publisher_id,author_id) has already been taken.',
            ]
        );
        try {
            $input = $request->all();
            if ($request->hasFile('photo')) {
                $image = Image::make($request->file('photo'));
                $imageName = time() . '-' . $request->file('photo')->getClientOriginalName();

                $destinationPath = 'assets/img/purchase/';
                $uploadSuccess = $image->save($destinationPath . $imageName);

                /**
                 * Generate Thumbnail Image Upload on Folder Code
                 */
                $destinationPathThumbnail = public_path('assets/img/purchase/thumbnail/');
                $image->resize(50, 50);
                $image->save($destinationPathThumbnail . $imageName);
                if ($uploadSuccess) {
                    //Delete Old File
                    $imgExist = Purchase::where('id', $id)->first();
                    $existingImage = $imgExist->photo;
                    if ($existingImage) {

                        if (Storage::disk('local')->exists($destinationPath . $existingImage)) {
                            unlink($destinationPath . $existingImage);
                        }
                        if (Storage::disk('local')->exists($destinationPathThumbnail . $existingImage)) {
                            unlink($destinationPathThumbnail . $existingImage);
                        }
                    }
                    $input['photo'] = $imageName;
                }
            }

            $input['updated_by'] = $this->webspice->getUserId();
            Purchase::where('id', $id)->update($input);
        } catch (Exception $e) {
            // $this->webspice->message('error', $e->getMessage());
            return response()->json(
                [
                    'error' => $e->getMessage(),
                ], 401);
        }
        // return redirect()->route('purchases.index');
    }

    public function destroy($id)
    {
        #permission verfy
        // $this->webspice->permissionVerify('purchase.delete');
        try {
            # decrypt value
            // $id = $this->webspice->encryptDecrypt('decrypt', $id);

            $purchase = $this->purchases->findById($id);
            $purchase->delete();
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
        $this->webspice->permissionVerify('purchase.force_delete');
        try {
            #decrypt value
            $id = $this->webspice->encryptDecrypt('decrypt', $id);
            $purchase = Purchase::withTrashed()->findOrFail($id);
            $purchase->forceDelete();
        } catch (Exception $e) {
            $this->webspice->message('error', $e->getMessage());
        }
        return redirect()->back();
    }
    public function restore($id)
    {
        #permission verfy
        $this->webspice->permissionVerify('purchase.restore');
        try {
            $id = $this->webspice->encryptDecrypt('decrypt', $id);
            $purchase = Purchase::withTrashed()->findOrFail($id);
            $purchase->restore();
        } catch (Exception $e) {
            $this->webspice->message('error', $e->getMessage());
        }
        // return redirect()->route('purchases.index', ['status' => 'archived'])->withSuccess(__('User restored successfully.'));
        return redirect()->route('purchases.index');
    }

    public function restoreAll()
    {
        #permission verfy
        $this->webspice->permissionVerify('purchase.restore');
        try {
            $purchases = Purchase::onlyTrashed()->get();
            foreach ($purchases as $purchase) {
                $purchase->restore();
            }
        } catch (Exception $e) {
            $this->webspice->message('error', $e->getMessage());
        }
        return redirect()->route('purchases.index');
        // return redirect()->route('purchases.index')->withSuccess(__('All purchases restored successfully.'));
    }

    public function getpurchases()
    {
        $data = Purchase::where('status', 1)->get();
        return response()->json($data);
    }

}
