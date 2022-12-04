<?php

namespace App\Http\Controllers\Posts;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostsController extends Controller
{

    public function __construct()
    {

        $this->middleware('can:posts.index')->only('index');
        $this->middleware('can:posts.store')->only('store');
        $this->middleware('can:posts.show')->only('show');
        $this->middleware('can:posts.update')->only('update');
        $this->middleware('can:posts.delete')->only('delete');
        $this->middleware('can:posts.publish')->only('updateState');
    }


    public function index()
    {
        $posts = Post::all();
        return response()->json(["posts" => $posts], 200);
    }

    public function store(Request $request)
    {
        $rules = array(
            'name' => 'required|unique:posts',
            'description' => 'required',
        );

        $messages = array();

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {

            $messages = $validator->messages();

            return response()->json(["messages" => $messages], 500);
        }

        $post =  new Post();
        $post->name = $request->name;
        $post->description = $request->description;
        $post->author = Auth::user()->id;
        $post->state = '0';
        $post->category_id = $request->category_id;
        $post->save();

        return response()->json(["post" => $post, "message" => "Post has been created successfully"], 200);
    }

    public function show($id)
    {
        $post = Post::find($id);

        if ($post != '') {
            return response()->json(["post" => $post], 200);
        } else {
            return response()->json(["messages" => "Post not found"], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $rules = array(
            'name' => 'required|unique:posts,name,' . $id,
            'description' => 'required',
        );

        $messages = array();

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {

            $messages = $validator->messages();

            return response()->json(["messages" => $messages], 500);
        }

        $post = Post::find($id);


        if ($post != '') {

            $post->name = $request->name;
            $post->description = $request->description;
            $post->category_id = $request->category_id;

            if ($post->author == Auth::user()->id) {
                $post->update();

                return response()->json(["post" => $post, "message" => "Post has been updated successfully"], 200);
            } else {
                $post->update();

                return response()->json(["post" => $post, "message" => "Not authorized"], 403);
            }
        } else {
            return response()->json(["messages" => "Post not found"], 500);
        }
    }

    public function destroy($id)
    {
        $post = Post::find($id);

        if ($post != '') {

            if ($post->author == Auth::user()->id) {
                Post::destroy($id);
                return response()->json(["message" => "Post has been deleted successfully"], 200);
            } else {
                return response()->json(["post" => $post, "message" => "Not authorized"], 403);
            }
        } else {
            return response()->json(["messages" => "Post not found"], 500);
        }
    }

    public function updateState(Request $request, $id)
    {

        $post = Post::find($id);

        if ($post != '') {
            $post->state = '1';
            $post->update();

            return response()->json(["post" => $post, "message" => "Post has been published successfully"], 200);
        } else {
            return response()->json(["messages" => "Post not found"], 500);
        }
    }
}
