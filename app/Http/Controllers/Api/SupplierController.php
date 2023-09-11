<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Lib\Webspice;
use App\Models\Supplier;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Image;

class SupplierController extends Controller
{
    public $webspice;
    protected $supplier;
    protected $suppliers;
    protected $supplierid;
    public $tableName;

    public function __construct(Supplier $supplier, Webspice $webspice)
    {
        $this->webspice = $webspice;
        $this->suppliers = $supplier;
        $this->tableName = 'suppliers';
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
                'supplier_name',
                'supplier_email',
                'supplier_phone',
                'supplier_address',
            ])) {
                $sortField = 'created_at';
            }
            $sortDirection = request('sort_direction', 'created_at');
            if (!in_array($sortDirection, ['asc', 'desc'])) {
                $sortDirection = 'desc';
            }

            $filled = array_filter(request([
                'id',
                'supplier_name',
                'supplier_email',
                'supplier_phone',
                'supplier_address',
            ]));

            $suppliers = Supplier::when(count($filled) > 0, function ($query) use ($filled) {
                foreach ($filled as $column => $value) {
                    $query->where($column, 'LIKE', '%' . $value . '%');
                }

            })->when(request('search', '') != '', function ($query) use ($searchTerm) {
                $query->search(trim($searchTerm));
            })->orderBy($sortField, $sortDirection)->paginate($paginate);

            return response()->json($suppliers);
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
        // $this->webspice->permissionVerify('supplier.create');

        $request->validate(
            [
                'supplier_phone' => 'required|regex:/^[a-zA-Z 0-9]+$/u|min:3|max:20|unique:suppliers',
                'supplier_name' => 'required',
                'supplier_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ],
            [
                'supplier_name.required' => 'supplier name field is required.',
                'supplier_phone.unique' => 'The supplier phone has already been taken.',
                'supplier_name.regex' => 'The supplier name format is invalid. Please enter alpabatic text.',
                'supplier_name.min' => 'The supplier name must be at least 3 characters.',
                'supplier_name.max' => 'The supplier name may not be greater than 20 characters.',
            ]
        );

        try {
            // $this->suppliers->create($data);
            $input = $request->all();
            if ($request->hasFile('supplier_photo')) {
                $image = Image::make($request->file('supplier_photo'));
                $imageName = time() . '-' . $request->file('supplier_photo')->getClientOriginalName();

                $destinationPath = 'assets/img/supplier/';
                $uploadSuccess = $image->save($destinationPath . $imageName);

                /**
                 * Generate Thumbnail Image Upload on Folder Code
                 */
                $destinationPathThumbnail = public_path('assets/img/supplier/thumbnail/');
                $image->resize(50, 50);
                $image->save($destinationPathThumbnail . $imageName);

                // $file = $request->file('supplier_photo');
                // $filename = $file->getClientOriginalName();
                // $uploadedPath = $file->move(public_path($destinationPath), $filename);
                if ($uploadSuccess) {
                    $input['supplier_photo'] = $imageName;
                }
            }
            $input['created_by'] = $this->webspice->getUserId();

            $this->suppliers->create($input);
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
            $supplier = Supplier::find($id);
            return $supplier;
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
        // $this->webspice->permissionVerify('supplier.edit');

        # decrypt value
        // $id = $this->webspice->encryptDecrypt('decrypt', $id);

        $request->validate(
            [
                'supplier_phone' => 'required|regex:/^[a-zA-Z 0-9]+$/u|min:3|max:20|unique:suppliers,supplier_phone,' . $id,
                'supplier_name' => 'required',
                'supplier_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ],
            [
                'supplier_name.required' => 'supplier Name field is required.',
                'supplier_phone.unique' => '"' . $request->supplier_phone . '" The supplier phone has already been taken.',
                'supplier_name.regex' => 'The supplier name format is invalid. Please enter alpabatic text.',
                'supplier_name.min' => 'The supplier name must be at least 3 characters.',
                'supplier_name.max' => 'The supplier name may not be greater than 20 characters.',
            ]
        );
        try {
            $supplier = Supplier::find($id);
            $supplier->supplier_name = $request->supplier_name;
            $supplier->supplier_phone = $request->supplier_phone;
            $supplier->supplier_email = $request->supplier_email;
            $supplier->supplier_address = $request->supplier_address;
            if ($request->hasFile('supplier_photo')) {
                $image = Image::make($request->file('supplier_photo'));
                $imageName = time() . '-' . $request->file('supplier_photo')->getClientOriginalName();

                $destinationPath = 'assets/img/supplier/';
                $uploadSuccess = $image->save($destinationPath . $imageName);

                /**
                 * Generate Thumbnail Image Upload on Folder Code
                 */
                $destinationPathThumbnail = public_path('assets/img/supplier/thumbnail/');
                $image->resize(50, 50);
                $image->save($destinationPathThumbnail . $imageName);
                if ($uploadSuccess) {
                    //Delete Old File
                    $imgExist = Supplier::where('id', $id)->first();
                    $existingImage = $imgExist->supplier_photo;
                    if ($existingImage) {
                      
                        if (Storage::disk('local')->exists($destinationPath . $existingImage)) {                           
                            unlink($destinationPath . $existingImage);
                        }
                        if (Storage::disk('local')->exists($destinationPathThumbnail . $existingImage)) {
                            unlink($destinationPathThumbnail . $existingImage);
                        }
                    }
                    $supplier->supplier_photo = $imageName;
                }
            }
            $supplier->updated_by = $this->webspice->getUserId();
            $supplier->save();
        } catch (Exception $e) {
            // $this->webspice->message('error', $e->getMessage());
            return response()->json(
                [
                    'error' => $e->getMessage(),
                ], 401);
        }
        // return redirect()->route('suppliers.index');
    }

    public function destroy($id)
    {
        #permission verfy
        // $this->webspice->permissionVerify('supplier.delete');
        try {
            # decrypt value
            // $id = $this->webspice->encryptDecrypt('decrypt', $id);

            $supplier = $this->suppliers->findById($id);
            $supplier->delete();
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
        $this->webspice->permissionVerify('supplier.force_delete');
        try {
            #decrypt value
            $id = $this->webspice->encryptDecrypt('decrypt', $id);
            $supplier = Supplier::withTrashed()->findOrFail($id);
            $supplier->forceDelete();
        } catch (Exception $e) {
            $this->webspice->message('error', $e->getMessage());
        }
        return redirect()->back();
    }
    public function restore($id)
    {
        #permission verfy
        $this->webspice->permissionVerify('supplier.restore');
        try {
            $id = $this->webspice->encryptDecrypt('decrypt', $id);
            $supplier = Supplier::withTrashed()->findOrFail($id);
            $supplier->restore();
        } catch (Exception $e) {
            $this->webspice->message('error', $e->getMessage());
        }
        // return redirect()->route('suppliers.index', ['status' => 'archived'])->withSuccess(__('User restored successfully.'));
        return redirect()->route('suppliers.index');
    }

    public function restoreAll()
    {
        #permission verfy
        $this->webspice->permissionVerify('supplier.restore');
        try {
            $suppliers = Supplier::onlyTrashed()->get();
            foreach ($suppliers as $supplier) {
                $supplier->restore();
            }
        } catch (Exception $e) {
            $this->webspice->message('error', $e->getMessage());
        }
        return redirect()->route('suppliers.index');
        // return redirect()->route('suppliers.index')->withSuccess(__('All suppliers restored successfully.'));
    }

    public function getsuppliers()
    {
        $data = Supplier::where('status', 1)->get();
        return response()->json($data);
    }

}
