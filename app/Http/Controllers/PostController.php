<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use TopSystem\TopAdmin\Models\Post;

class PostController extends Controller
{
    public function getPosts(Request $request){
        $postModel = new Post();
        if ($request->has('category_id')){
            $postModel = $postModel->where('category_id',$request->get('category_id'));
        }
        $response = $postModel->select(['title','tags'])->paginate(10);
        return response()->json($response);
    }

    public function getPost(Request $request, $id){
        $response = Post::find($id);
        return response()->json(['code' => 0,'data' => $response]);
    }
}
