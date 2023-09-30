<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Lib\Webspice;
use App\Models\OptionGroup;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OptionGroupController extends Controller
{
    public $webspice;
    protected $option_group;
    protected $option_groups;
    protected $option_groupid;
    public $tableName;

    public function __construct(OptionGroup $option_group, Webspice $webspice)
    {
        $this->webspice = $webspice;
        $this->option_groups = $option_group;
        $this->tableName = 'option_groups';
        $this->middleware('JWT');
    }

    public function index()
    {
#permission verfy
$this->webspice->permissionVerify('option_group.manage');
        try {
            $paginate = request('paginate', 5);
            $searchTerm = request('search', '');

            $sortField = request('sort_field', 'created_at');
            if (!in_array($sortField, ['id', 'name'])) {
                $sortField = 'created_at';
            }
            $sortDirection = request('sort_direction', 'created_at');
            if (!in_array($sortDirection, ['asc', 'desc'])) {
                $sortDirection = 'desc';
            }

            $filled = array_filter(request([
                'id',
                'name',
            ]));

            $optionGroups = OptionGroup::when(count($filled) > 0, function ($query) use ($filled) {
                foreach ($filled as $column => $value) {
                    $query->where($column, 'LIKE', '%' . $value . '%');
                }

            })->when(request('search', '') != '', function ($query) use ($searchTerm) {
                $query->search(trim($searchTerm));
            })->orderBy($sortField, $sortDirection)->paginate($paginate);

            // return ProductResource::collection($products);
            return response()->json($optionGroups);
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
        $this->webspice->permissionVerify('option_group.create');

        $request->validate(
            [
                'name' => 'required|min:3|max:500|unique:option_groups',
            ],
            [
                'name.required' => 'option group Name field is required.',
                'name.unique' => 'The option group name has already been taken.',
                'name.regex' => 'The option group name format is invalid. Please enter alpabatic text.',
                'name.min' => 'The option group name must be at least 3 characters.',
                'name.max' => 'The option group name may not be greater than 20 characters.',
            ]
        );

        try {
            // $this->option_groups->create($data);
            $input = $request->all();
            $input['created_by'] = $this->webspice->getUserId();

            $this->option_groups->create($input);
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
            $option_group = OptionGroup::find($id);
            return $option_group;
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
        $this->webspice->permissionVerify('option_group.edit');

        # decrypt value
        // $id = $this->webspice->encryptDecrypt('decrypt', $id);

        $request->validate(
            [
                'name' => 'required|min:3|max:500|unique:option_groups,name,' . $id,
            ],
            [
                'name.required' => 'option_group Name field is required.',
                'name.unique' => '"' . $request->name . '" The option_group name has already been taken.',
                'name.regex' => 'The option_group name format is invalid. Please enter alpabatic text.',
                'name.min' => 'The option_group name must be at least 3 characters.',
                'name.max' => 'The option_group name may not be greater than 20 characters.',
            ]
        );
        try {
            $option_group = $this->option_groups->find($id);          
            $option_group->name = $request->name;
            $option_group->save();
        } catch (Exception $e) {
            // $this->webspice->message('error', $e->getMessage());
            return response()->json(
                [
                    'error' => $e->getMessage(),
                ], 401);
        }
        // return redirect()->route('option_groups.index');
    }

    public function destroy($id)
    {
        #permission verfy
        $this->webspice->permissionVerify('option_group.delete');
        try {
            # decrypt value
            // $id = $this->webspice->encryptDecrypt('decrypt', $id);

            $option_group = $this->option_groups->findById($id);
            $option_group->delete();
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
        // $this->webspice->permissionVerify('option_group.force_delete');
        try {
            #decrypt value
            $id = $this->webspice->encryptDecrypt('decrypt', $id);
            $option_group = OptionGroup::withTrashed()->findOrFail($id);
            $option_group->forceDelete();
        } catch (Exception $e) {
            $this->webspice->message('error', $e->getMessage());
        }
        return redirect()->back();
    }
    public function restore($id)
    {
        #permission verfy
        $this->webspice->permissionVerify('option_group.restore');
        try {
            $id = $this->webspice->encryptDecrypt('decrypt', $id);
            $option_group = OptionGroup::withTrashed()->findOrFail($id);
            $option_group->restore();
        } catch (Exception $e) {
            $this->webspice->message('error', $e->getMessage());
        }
        // return redirect()->route('option_groups.index', ['status' => 'archived'])->withSuccess(__('User restored successfully.'));
        return redirect()->route('option_groups.index');
    }

    public function restoreAll()
    {
        #permission verfy
        $this->webspice->permissionVerify('option_group.restore');
        try {
            $option_groups = OptionGroup::onlyTrashed()->get();
            foreach ($option_groups as $option_group) {
                $option_group->restore();
            }
        } catch (Exception $e) {
            $this->webspice->message('error', $e->getMessage());
        }
        return redirect()->route('option_groups.index');
        // return redirect()->route('option_groups.index')->withSuccess(__('All option_groups restored successfully.'));
    }


    public function getOptionGroups()
    {
        $data = OptionGroup::where('status', 1)->get();
        return response()->json($data);
    }

}
