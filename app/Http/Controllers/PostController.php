<?php

namespace App\Http\Controllers;

use App\Category;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PostController extends Controller
{

    public function index()
    {
        $posts = Post::latest()->paginate(6);
        return view('posts',compact('posts'));
    }

    public function details($slug)
    {
            $post = Post::where('slug',$slug)->first();
            $blogkey = 'blog_'.$post->id;
            if (!Session::has($blogkey)){
                $post->increment('view_count');
                Session::put($blogkey, 1);
            }
            $randomposts = Post::all()->random(3);
            return view('post',compact('post','randomposts'));
    }
    public function postByCategory($slug)
    {
       $category = Category::where('slug',$slug)->first();
       return view('category',compact('category'));
    }
}
