<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use TopSystem\TopAdmin\Models\Category;

class CategoryController extends Controller
{
    public function getCategories(Request $request){
        $postModel = new Category();
        $response = $postModel->paginate(10);
        return response()->json($response);
    }
}
