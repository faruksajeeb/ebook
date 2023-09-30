<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Lib\Webspice;
use App\Models\Publisher;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Image;

class PublisherController extends Controller
{
    public $webspice;
    protected $publisher;
    protected $publishers;
    protected $publisherid;
    public $tableName;

    public function __construct(publisher $publisher, Webspice $webspice)
    {
        $this->webspice = $webspice;
        $this->publishers = $publisher;
        $this->tableName = 'publishers';
        $this->middleware('JWT');
    }

    public function index()
    {

        #permission verfy
        $this->webspice->permissionVerify('publisher.manage');
        try {
            $paginate = request('paginate', 5);
            $searchTerm = request('search', '');

            $sortField = request('sort_field', 'created_at');
            if (!in_array($sortField, [
                'id',
                'publisher_name',
                'publisher_email',
                'publisher_phone',
                'publisher_address',
            ])) {
                $sortField = 'created_at';
            }
            $sortDirection = request('sort_direction', 'created_at');
            if (!in_array($sortDirection, ['asc', 'desc'])) {
                $sortDirection = 'desc';
            }

            $filled = array_filter(request([
                'id',
                'publisher_name',
                'publisher_email',
                'publisher_phone',
                'publisher_address',
            ]));

            $publishers = publisher::when(count($filled) > 0, function ($query) use ($filled) {
                foreach ($filled as $column => $value) {
                    $query->where($column, 'LIKE', '%' . $value . '%');
                }

            })->when(request('search', '') != '', function ($query) use ($searchTerm) {
                $query->search(trim($searchTerm));
            })->orderBy($sortField, $sortDirection)->paginate($paginate);

            return response()->json($publishers);
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
        $this->webspice->permissionVerify('publisher.create');

        $request->validate(
            [
                'publisher_name' => 'required|regex:/^[a-zA-Z 0-9]+$/u|min:3|max:20|unique:publishers',
              
                'publisher_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ],
            [
                'publisher_name.required' => 'publisher name field is required.',
                'publisher_phone.unique' => 'The publisher phone has already been taken.',
                'publisher_name.regex' => 'The publisher name format is invalid. Please enter alpabatic text.',
                'publisher_name.min' => 'The publisher name must be at least 3 characters.',
                'publisher_name.max' => 'The publisher name may not be greater than 20 characters.',
            ]
        );

        try {
            // $this->publishers->create($data);
            $input = $request->all();
            if ($request->hasFile('publisher_photo')) {
                $image = Image::make($request->file('publisher_photo'));
                $imageName = time() . '-' . $request->file('publisher_photo')->getClientOriginalName();

                $destinationPath = 'assets/img/publisher/';
                $uploadSuccess = $image->save($destinationPath . $imageName);

                /**
                 * Generate Thumbnail Image Upload on Folder Code
                 */
                $destinationPathThumbnail = public_path('assets/img/publisher/thumbnail/');
                $image->resize(50, 50);
                $image->save($destinationPathThumbnail . $imageName);

                // $file = $request->file('publisher_photo');
                // $filename = $file->getClientOriginalName();
                // $uploadedPath = $file->move(public_path($destinationPath), $filename);
                if ($uploadSuccess) {
                    $input['publisher_photo'] = $imageName;
                }
            }
            $input['created_by'] = $this->webspice->getUserId();

            $this->publishers->create($input);
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
            $publisher = publisher::find($id);
            return $publisher;
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
        $this->webspice->permissionVerify('publisher.edit');

        # decrypt value
        // $id = $this->webspice->encryptDecrypt('decrypt', $id);

        $request->validate(
            [
                'publisher_name' => 'required|regex:/^[a-zA-Z 0-9]+$/u|min:3|max:20|unique:publishers,publisher_name,' . $id,               
                'publisher_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ],
            [
                'publisher_name.required' => 'publisher Name field is required.',
                'publisher_phone.unique' => '"' . $request->publisher_phone . '" The publisher phone has already been taken.',
                'publisher_name.regex' => 'The publisher name format is invalid. Please enter alpabatic text.',
                'publisher_name.min' => 'The publisher name must be at least 3 characters.',
                'publisher_name.max' => 'The publisher name may not be greater than 20 characters.',
            ]
        );
        try {
            $publisher = publisher::find($id);
            $publisher->publisher_name = $request->publisher_name;
            $publisher->publisher_phone = $request->publisher_phone;
            $publisher->publisher_email = $request->publisher_email;
            $publisher->publisher_address = $request->publisher_address;
            if ($request->hasFile('publisher_photo')) {
                $image = Image::make($request->file('publisher_photo'));
                $imageName = time() . '-' . $request->file('publisher_photo')->getClientOriginalName();

                $destinationPath = 'assets/img/publisher/';
                $uploadSuccess = $image->save($destinationPath . $imageName);

                /**
                 * Generate Thumbnail Image Upload on Folder Code
                 */
                $destinationPathThumbnail = public_path('assets/img/publisher/thumbnail/');
                $image->resize(50, 50);
                $image->save($destinationPathThumbnail . $imageName);
                if ($uploadSuccess) {
                    //Delete Old File
                    $imgExist = publisher::where('id', $id)->first();
                    $existingImage = $imgExist->publisher_photo;
                    if ($existingImage) {
                      
                        if (Storage::disk('local')->exists($destinationPath . $existingImage)) {                           
                            unlink($destinationPath . $existingImage);
                        }
                        if (Storage::disk('local')->exists($destinationPathThumbnail . $existingImage)) {
                            unlink($destinationPathThumbnail . $existingImage);
                        }
                    }
                    $publisher->publisher_photo = $imageName;
                }
            }
            $publisher->updated_by = $this->webspice->getUserId();
            $publisher->save();
        } catch (Exception $e) {
            // $this->webspice->message('error', $e->getMessage());
            return response()->json(
                [
                    'error' => $e->getMessage(),
                ], 401);
        }
        // return redirect()->route('publishers.index');
    }

    public function destroy($id)
    {
        #permission verfy
        $this->webspice->permissionVerify('publisher.delete');
        try {
            # decrypt value
            // $id = $this->webspice->encryptDecrypt('decrypt', $id);

            $publisher = $this->publishers->findById($id);
            $publisher->delete();
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
        $this->webspice->permissionVerify('publisher.force_delete');
        try {
            #decrypt value
            $id = $this->webspice->encryptDecrypt('decrypt', $id);
            $publisher = publisher::withTrashed()->findOrFail($id);
            $publisher->forceDelete();
        } catch (Exception $e) {
            $this->webspice->message('error', $e->getMessage());
        }
        return redirect()->back();
    }
    public function restore($id)
    {
        #permission verfy
        $this->webspice->permissionVerify('publisher.restore');
        try {
            $id = $this->webspice->encryptDecrypt('decrypt', $id);
            $publisher = publisher::withTrashed()->findOrFail($id);
            $publisher->restore();
        } catch (Exception $e) {
            $this->webspice->message('error', $e->getMessage());
        }
        // return redirect()->route('publishers.index', ['status' => 'archived'])->withSuccess(__('User restored successfully.'));
        return redirect()->route('publishers.index');
    }

    public function restoreAll()
    {
        #permission verfy
        $this->webspice->permissionVerify('publisher.restore');
        try {
            $publishers = publisher::onlyTrashed()->get();
            foreach ($publishers as $publisher) {
                $publisher->restore();
            }
        } catch (Exception $e) {
            $this->webspice->message('error', $e->getMessage());
        }
        return redirect()->route('publishers.index');
        // return redirect()->route('publishers.index')->withSuccess(__('All publishers restored successfully.'));
    }

    public function getPublishers()
    {
        $data = Publisher::where('status', 1)->get();
        return response()->json($data);
    }

}
