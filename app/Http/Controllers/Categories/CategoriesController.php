<?php

namespace App\Http\Controllers\Categories;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CategoriesController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:categories.index')->only('index');
        $this->middleware('can:categories.store')->only('store');
        $this->middleware('can:categories.show')->only('show');
        $this->middleware('can:categories.update')->only('update');
        $this->middleware('can:categories.delete')->only('delete');
    }

    public function index()
    {
        $categories =Category::all();
        return response()->json(["categories"=>$categories], 200);
    }

    public function store(Request $request)
    {
        $rules = array(
            'name' => 'required|unique:categories',
            'description' => 'required',
        );

        $messages = array();

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {

            $messages = $validator->messages();

            return response()->json(["messages" => $messages], 500);
        }

        $category = Category::create($request->all());

        return response()->json(["category" => $category, "message" => "Category has been created successfully"], 200);
    }

    public function show($id)
    {
        $category = Category::find($id);

        if ($category!='') {

            return response()->json(["category"=>$category], 200);
        }else{
            return response()->json(["messages" => "Category not found"], 500);

        }
    }

    public function update(Request $request, $id)
    {
        $rules = array(
            'name' => 'required|unique:categories,name,'.$id,
            'description' => 'required',
        );

        $messages = array();

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {

            $messages = $validator->messages();

            return response()->json(["messages" => $messages], 500);
        }

        $category = Category::find($id);

        if ($category!='') {
            $category->name = $request->name;
            $category->description = $request->description;
            $category->update();

            return response()->json(["category" => $category, "message"=>"Category has been updated successfully"], 200);
        }else{
            return response()->json(["messages" => "Category not found"], 500);

        }

    }


    public function destroy($id)
    {
        $category = Category::find($id);

        if ($category!='') {
            Category::destroy($id);

            return response()->json(["message"=>"Category has been deleted successfully"], 200);
        }else{
            return response()->json(["messages" => "Category not found"], 500);

        }
    }
}
