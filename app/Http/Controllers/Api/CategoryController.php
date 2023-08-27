<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use DB;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $category = Category::all();
        // return response()->json($category);
        $paginate = request('paginate', 5);
        $searchTerm = request('search', '');

        $sortField = request('sort_field', 'created_at');
        if (!in_array($sortField, ['id', 'category_name'])) {
            $sortField = 'created_at';
        }
        $sortDirection = request('sort_direction', 'created_at');
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        $filled = array_filter(request([
            'category_name'
        ]));

        $categories = Category::when(count($filled) > 0, function ($query) use ($filled) {
            foreach ($filled as $column => $value) {
                $query->where($column, 'LIKE', '%' . $value . '%');
            }

        })
            ->when(request('search', '') != '', function ($query) use ($searchTerm) {
                $query->search(trim($searchTerm));
            })->orderBy($sortField, $sortDirection)->paginate($paginate);

        // return ProductResource::collection($products);
        return response()->json($categories);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'category_name' => 'required|unique:categories|max:255',
        ]);

        $category = new Category;
        $category->category_name = $request->category_name;

        $category->save();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = DB::table('categories')->where('id', $id)->first();
        return response()->json($category);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = array();
        $data['category_name'] = $request->category_name;
        DB::table('categories')->where('id', $id)->update($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::table('categories')->where('id', $id)->delete();
    }
}
