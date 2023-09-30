<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Lib\Webspice;
use App\Models\SubCategory;
use Exception;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{

    public $webspice;
    protected $Subategory;
    protected $SubCategories;
    protected $SubCategoryId;
    public $tableName;

    public function __construct(SubCategory $SubCategory, Webspice $webspice)
    {
        $this->webspice = $webspice;
        $this->SubCategories = $SubCategory;
        $this->tableName = 'sub_categories';
        $this->middleware('JWT');
    }

    public function index()
    {
#permission verfy
$this->webspice->permissionVerify('sub_category.manage');
        try {
            $paginate = request('paginate', 5);
            $searchTerm = request('search', '');

            $sortField = request('sort_field', 'created_at');
            if (!in_array($sortField, ['id', 'sub_category_name'])) {
                $sortField = 'created_at';
            }
            $sortDirection = request('sort_direction', 'created_at');
            if (!in_array($sortDirection, ['asc', 'desc'])) {
                $sortDirection = 'desc';
            }

            $filled = array_filter(request([
                'id',
                'sub_category_name',
            ]));

            $subCategories = SubCategory::with('category')->when(count($filled) > 0, function ($query) use ($filled) {
                foreach ($filled as $column => $value) {
                    $query->where($column, 'LIKE', '%' . $value . '%');
                }

            })->when(request('category_id', '') != '', function ($query) {
                $query->where('category_id', request('category_id'));
            })->when(request('search', '') != '', function ($query) use ($searchTerm) {
                $query->search(trim($searchTerm));
            })->orderBy($sortField, $sortDirection)->paginate($paginate);

            return response()->json($subCategories);
        } catch (Exception $e) {
            return response()->json(
                [
                    'error' => $e->getMessage(),
                ], 401);
        }
    }

    public function store(Request $request)
    {

        #permission verfy
        $this->webspice->permissionVerify('sub_category.create');

        $request->validate(
            [
                'sub_category_name' => 'required|regex:/^[a-zA-Z 0-9]+$/u|min:2|max:20|unique:sub_categories',
                'category_id' => 'required',
            ],
            [
                'sub_category_name.required' => 'SubCategory Name field is required.',
                'sub_category_name.unique' => 'The SubCategory name has already been taken.',
                'sub_category_name.regex' => 'The SubCategory name format is invalid. Please enter alpabatic text.',
                'sub_category_name.min' => 'The SubCategory name must be at least 3 characters.',
                'sub_category_name.max' => 'The SubCategory name may not be greater than 20 characters.',
            ]
        );

        try {
            $input = $request->all();
            $input['created_by'] = $this->webspice->getUserId();
            $this->SubCategories->create($input);
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
            $SubCategory = SubCategory::with('category')->find($id);
            return $SubCategory;
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

        #permission verfy
        $this->webspice->permissionVerify('sub_category.edit');

        # decrypt value
        // $id = $this->webspice->encryptDecrypt('decrypt', $id);

        $request->validate(
            [
                'sub_category_name' => 'required|regex:/^[a-zA-Z 0-9]+$/u|min:2|max:20|unique:sub_categories,sub_category_name,' . $id,
            ],
            [
                'sub_category_name.required' => 'SubCategory Name field is required.',
                'sub_category_name.unique' => '"' . $request->sub_category_name . '" The SubCategory name has already been taken.',
                'sub_category_name.regex' => 'The SubCategory name format is invalid. Please enter alpabatic text.',
                'sub_category_name.min' => 'The SubCategory name must be at least 3 characters.',
                'sub_category_name.max' => 'The SubCategory name may not be greater than 20 characters.',
            ]
        );
        try {
            $SubCategory = SubCategory::find($id);
            $SubCategory->sub_category_name = $request->sub_category_name;
            $SubCategory->category_id = $request->category_id;
            $SubCategory->updated_by = $this->webspice->getUserId();
            $SubCategory->save();
        } catch (Exception $e) {
            return response()->json(
                [
                    'error' => $e->getMessage(),
                ], 401);
        }
    }

    public function destroy($id)
    {
        #permission verfy
        $this->webspice->permissionVerify('sub_category.delete');
        try {
            # decrypt value
            // $id = $this->webspice->encryptDecrypt('decrypt', $id);

            $SubCategory = SubCategory::findById($id);
            $SubCategory->delete();
        } catch (Exception $e) {
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
        $this->webspice->permissionVerify('SubCategory_group.force_delete');
        try {
            #decrypt value
            $id = $this->webspice->encryptDecrypt('decrypt', $id);
            $SubCategory = SubCategory_group::withTrashed()->findOrFail($id);
            $SubCategory->forceDelete();
        } catch (Exception $e) {
            $this->webspice->message('error', $e->getMessage());
        }
        return redirect()->back();
    }
    public function restore($id)
    {
        #permission verfy
        $this->webspice->permissionVerify('SubCategory_group.restore');
        try {
            $id = $this->webspice->encryptDecrypt('decrypt', $id);
            $SubCategory = SubCategory::withTrashed()->findOrFail($id);
            $SubCategory->restore();
        } catch (Exception $e) {
            $this->webspice->message('error', $e->getMessage());
        }
        // return redirect()->route('SubCategorys.index', ['status' => 'archived'])->withSuccess(__('User restored successfully.'));
        return redirect()->route('SubCategorys.index');
    }

    public function restoreAll()
    {
        #permission verfy
        $this->webspice->permissionVerify('SubCategory_group.restore');
        try {
            $SubCategorys = SubCategory::onlyTrashed()->get();
            foreach ($SubCategorys as $SubCategory) {
                $SubCategory->restore();
            }
        } catch (Exception $e) {
            $this->webspice->message('error', $e->getMessage());
        }
        return redirect()->route('SubCategorys.index');
        // return redirect()->route('SubCategorys.index')->withSuccess(__('All SubCategorys restored successfully.'));
    }

    public function getCategoryWiseSubCategories(Request $request)
    {
        $data = SubCategory::where('category_id', $request->category_id)->get();

        return response()->json($data);
    }

}
