<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Lib\Webspice;
use App\Models\purchase;
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
                'title',
                'price',
                'stock_quantity',
            ])) {
                $sortField = 'created_at';
            }
            $sortDirection = request('sort_direction', 'created_at');
            if (!in_array($sortDirection, ['asc', 'desc'])) {
                $sortDirection = 'desc';
            }

            $filled = array_filter(request([
                'id',
                'title',
                'publisher_id',
                'author_id',
                'category_id',
                'sub_category_id',
                'stock_quantity',
                'price',
            ]));

            $purchases = Purchase::with(['publisher', 'author', 'category', 'sub_category'])->when(count($filled) > 0, function ($query) use ($filled) {
                foreach ($filled as $column => $value) {
                    $query->where($column, 'LIKE', '%' . $value . '%');
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
        #permission verfy
        // $this->webspice->permissionVerify('purchase.create');

        // Unique check __> purchase name, author, publisher

        $request->validate(
            [
                'title' => ['required', 'min:1', 'max:1000', Rule::unique('purchases')->where(function ($query) use ($request) {
                    return $query->where('title', $request->title)
                        ->where('publisher_id', $request->publisher_id)
                        ->where('author_id', $request->author_id);
                })],
                'publisher_id' => 'required',
                'author_id' => 'required',
                'buying_discount_percentage' => 'required|numeric',
                'selling_discount_percentage' => 'required|numeric',
                'buying_vat_percentage' => 'required|numeric',
                'selling_vat_percentage' => 'required|numeric',
                'price' => ['required', 'numeric', 'min:0.01'],
                'publication_year' => ['required', 'integer', 'between:1950,2050', 'date_format:Y'],
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ],
            [
                'title.unique' => 'The purchase (title,publisher_id,author_id) has already been taken.',
            ]
        );

        try {
            // $this->purchases->create($data);
            $input = $request->all();
            // dd($input);
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

                // $file = $request->file('photo');
                // $filename = $file->getClientOriginalName();
                // $uploadedPath = $file->move(public_path($destinationPath), $filename);
                if ($uploadSuccess) {
                    $input['photo'] = $imageName;
                }
            }
            $input['created_by'] = $this->webspice->getUserId();
            // dd($input);
            $this->purchases->create($input);
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
            $purchase = Purchase::with(['publisher', 'author', 'category', 'sub_category'])->find($id);
            return $purchase;
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
