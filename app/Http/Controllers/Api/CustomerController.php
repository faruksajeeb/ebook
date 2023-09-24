<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Lib\Webspice;
use App\Models\Customer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Image;

class CustomerController extends Controller
{
    public $webspice;
    protected $customer;
    protected $customers;
    protected $customerid;
    public $tableName;

    public function __construct(Customer $customer, Webspice $webspice)
    {
        $this->webspice = $webspice;
        $this->customers = $customer;
        $this->tableName = 'customers';
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
                'customer_name',
                'customer_email',
                'customer_phone',
                'customer_address',
            ])) {
                $sortField = 'created_at';
            }
            $sortDirection = request('sort_direction', 'created_at');
            if (!in_array($sortDirection, ['asc', 'desc'])) {
                $sortDirection = 'desc';
            }

            $filled = array_filter(request([
                'id',
                'customer_name',
                'customer_email',
                'customer_phone',
                'customer_address',
            ]));

            $customers = customer::when(count($filled) > 0, function ($query) use ($filled) {
                foreach ($filled as $column => $value) {
                    $query->where($column, 'LIKE', '%' . $value . '%');
                }

            })->when(request('search', '') != '', function ($query) use ($searchTerm) {
                $query->search(trim($searchTerm));
            })->orderBy($sortField, $sortDirection)->paginate($paginate);

            return response()->json($customers);
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
        // $this->webspice->permissionVerify('customer.create');

        $request->validate(
            [
                'customer_phone' => 'required|regex:/^[a-zA-Z 0-9]+$/u|min:3|max:20|unique:customers',
                'customer_name' => 'required',
                'discount_percentage' => 'numeric|required|min:0',
                'customer_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ],
            [
                'customer_name.required' => 'customer name field is required.',
                'customer_phone.unique' => 'The customer phone has already been taken.',
                'customer_name.regex' => 'The customer name format is invalid. Please enter alpabatic text.',
                'customer_name.min' => 'The customer name must be at least 3 characters.',
                'customer_name.max' => 'The customer name may not be greater than 20 characters.',
            ]
        );

        try {
            // $this->customers->create($data);
            $input = $request->all();
            if ($request->hasFile('customer_photo')) {
                $image = Image::make($request->file('customer_photo'));
                $imageName = time() . '-' . $request->file('customer_photo')->getClientOriginalName();

                $destinationPath = 'assets/img/customer/';
                $uploadSuccess = $image->save($destinationPath . $imageName);

                /**
                 * Generate Thumbnail Image Upload on Folder Code
                 */
                $destinationPathThumbnail = public_path('assets/img/customer/thumbnail/');
                $image->resize(50, 50);
                $image->save($destinationPathThumbnail . $imageName);

                // $file = $request->file('customer_photo');
                // $filename = $file->getClientOriginalName();
                // $uploadedPath = $file->move(public_path($destinationPath), $filename);
                if ($uploadSuccess) {
                    $input['customer_photo'] = $imageName;
                }
            }
            $input['created_by'] = $this->webspice->getUserId();

            $this->customers->create($input);
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
            $customer = Customer::find($id);
            return $customer;
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
        // $this->webspice->permissionVerify('customer.edit');

        # decrypt value
        // $id = $this->webspice->encryptDecrypt('decrypt', $id);

        $request->validate(
            [
                'customer_phone' => 'required|regex:/^[a-zA-Z 0-9]+$/u|min:3|max:20|unique:customers,customer_phone,' . $id,
                'customer_name' => 'required',
                'discount_percentage' => 'numeric|required|min:0',
                'customer_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ],
            [
                'customer_name.required' => 'customer Name field is required.',
                'customer_phone.unique' => '"' . $request->customer_phone . '" The customer phone has already been taken.',
                'customer_name.regex' => 'The customer name format is invalid. Please enter alpabatic text.',
                'customer_name.min' => 'The customer name must be at least 3 characters.',
                'customer_name.max' => 'The customer name may not be greater than 20 characters.',
            ]
        );
        try {
            $customer = Customer::find($id);
            $customer->customer_name = $request->customer_name;
            $customer->customer_phone = $request->customer_phone;
            $customer->customer_email = $request->customer_email;
            $customer->customer_address = $request->customer_address;
            $customer->discount_percentage = $request->discount_percentage;
            if ($request->hasFile('customer_photo')) {
                $image = Image::make($request->file('customer_photo'));
                $imageName = time() . '-' . $request->file('customer_photo')->getClientOriginalName();

                $destinationPath = 'assets/img/customer/';
                $uploadSuccess = $image->save($destinationPath . $imageName);

                /**
                 * Generate Thumbnail Image Upload on Folder Code
                 */
                $destinationPathThumbnail = public_path('assets/img/customer/thumbnail/');
                $image->resize(50, 50);
                $image->save($destinationPathThumbnail . $imageName);
                if ($uploadSuccess) {
                    //Delete Old File
                    $imgExist = Customer::where('id', $id)->first();
                    $existingImage = $imgExist->customer_photo;
                    if ($existingImage) {
                      
                        if (Storage::disk('local')->exists($destinationPath . $existingImage)) {                           
                            unlink($destinationPath . $existingImage);
                        }
                        if (Storage::disk('local')->exists($destinationPathThumbnail . $existingImage)) {
                            unlink($destinationPathThumbnail . $existingImage);
                        }
                    }
                    $customer->customer_photo = $imageName;
                }
            }
            $customer->updated_by = $this->webspice->getUserId();
            $customer->save();
        } catch (Exception $e) {
            // $this->webspice->message('error', $e->getMessage());
            return response()->json(
                [
                    'error' => $e->getMessage(),
                ], 401);
        }
        // return redirect()->route('customers.index');
    }

    public function destroy($id)
    {
        #permission verfy
        // $this->webspice->permissionVerify('customer.delete');
        try {
            # decrypt value
            // $id = $this->webspice->encryptDecrypt('decrypt', $id);

            $customer = $this->customers->findById($id);
            $customer->delete();
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
        $this->webspice->permissionVerify('customer.force_delete');
        try {
            #decrypt value
            $id = $this->webspice->encryptDecrypt('decrypt', $id);
            $customer = customer::withTrashed()->findOrFail($id);
            $customer->forceDelete();
        } catch (Exception $e) {
            $this->webspice->message('error', $e->getMessage());
        }
        return redirect()->back();
    }
    public function restore($id)
    {
        #permission verfy
        $this->webspice->permissionVerify('customer.restore');
        try {
            $id = $this->webspice->encryptDecrypt('decrypt', $id);
            $customer = customer::withTrashed()->findOrFail($id);
            $customer->restore();
        } catch (Exception $e) {
            $this->webspice->message('error', $e->getMessage());
        }
        // return redirect()->route('customers.index', ['status' => 'archived'])->withSuccess(__('User restored successfully.'));
        return redirect()->route('customers.index');
    }

    public function restoreAll()
    {
        #permission verfy
        $this->webspice->permissionVerify('customer.restore');
        try {
            $customers = customer::onlyTrashed()->get();
            foreach ($customers as $customer) {
                $customer->restore();
            }
        } catch (Exception $e) {
            $this->webspice->message('error', $e->getMessage());
        }
        return redirect()->route('customers.index');
        // return redirect()->route('customers.index')->withSuccess(__('All customers restored successfully.'));
    }

    public function getCustomers()
    {
        $data = Customer::where('status', 1)->get();
        return response()->json($data);
    }

    
    public function getBalance($id)
    {
        $customer = Customer::find($id);

        if ($customer) {
            return response()->json(['balance' => $customer->balance]);
        } else {
            return response()->json(['message' => 'Customer not found'], 404);
        }
    }
    public function getDiscountPercentage($id)
    {
        $customer = Customer::find($id);

        if ($customer) {
            return response()->json(['discount_percentage' => $customer->discount_percentage]);
        } else {
            return response()->json(['message' => 'Customer not found'], 404);
        }
    }

}
