<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Lib\Webspice;
use App\Models\purchase;
use App\Models\PurchaseDetail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
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

        $request->validate(
            [
                'supplier_id' => 'required',
                'purchase_date' => 'required',
                'cart_items' => ['required', 'array', function ($attribute, $value, $fail) {
                    if (empty($value)) {
                        $fail('The cart must contain at least one item.');
                    }
                }],
                'attach_file' => 'nullable|mimes:jpeg,png,jpg,gif,svg,pdf|max:2048',
            ],
            [
                'cart_items.required' => 'The cart must contain at least one item.',
            ]
        );

        try {
            // $this->purchases->create($data);
            $input = $request->all();
            //  dd($input);
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

            // dd($input['cart_items']);
            # if Cart Items
            if (count($input['cart_items']) > 0) {
                $inserted = Purchase::create($input);
                $purchaseId = $inserted->id;
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
                    ]);
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
            // foreach($purchase->purchaseDetails as $item){
            //     dd($item->book->toArray());
            // }
            $purchaseDetails = PurchaseDetail::with(['book'])->where('purchase_id', $id)->get();
            $data = [
                'purchase' => $purchase,
                'purchase_details' => $purchaseDetails,
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
