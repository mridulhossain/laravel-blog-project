<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Notifications\AuthorPostApproved;
use App\Notifications\NewPostNotification;
use App\Post;
use App\Subscriber;
use App\Tag;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
//use Faker\Provider\Image;
//use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification;
use Intervention\Image\Facades\Image;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use function GuzzleHttp\Psr7\str;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::latest()->get();
        return view('admin.post.index',compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.post.create',compact('categories','tags'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       $this->validate($request,[
            'title' => 'required',
            'image' => 'required|mimes:jpg,png,jpeg,bmp',
            'categories' => 'required',
            'tags' => 'required',
            'body' => 'required'
       ]);

       $image = $request->file('image');
       $slug = Str::slug($request->title);
       if (isset($image))
       {
//           make unique name for image
          $currenttime = Carbon::now()->toDateString();
          $imagename = $slug.'-'.$currenttime.'-'.uniqid().'.'.$image->getClientOriginalExtension();

           if (!Storage::disk('public')->exists('post'))
           {
               Storage::disk('public')->makeDirectory('post');
           }
//           image resize for post
           $resizeimage = Image::make($image)->resize(1600,1066)->save($imagename);
           Storage::disk('public')->put('post/'.$imagename,$resizeimage);
       }else{
           $imagename = 'default.png';
       }

       $post = new Post();
       $post->user_id = Auth::id();
       $post->title = $request->title;
       $post->slug = $slug;
       $post->image = $imagename;
       $post->body = $request->body;
       if (isset($request->status)){
           $post->status = true;
       }else{
           $post->status = false;
       }
       $post->is_approved= true;
       $post->save();

       $post->categories()->attach($request->categories);
       $post->tags()->attach($request->tags);
       $subscribers = Subscriber::all();
       foreach ($subscribers as $subscriber)
       {
           Notification::route('mail',$subscriber->email)
               ->notify(new NewPostNotification($post));
       }
       Toastr::success('Post Successfully Saved :)','Success');
       return redirect()->route('admin.post.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return view('admin.post.show',compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.post.edit',compact('post','categories','tags'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        $this->validate($request,[
            'title' => 'required',
            'image' => 'image',
            'categories' => 'required',
            'tags' => 'required',
            'body' => 'required'
        ]);

        $image = $request->file('image');
        $slug = Str::slug($request->title);
        if (isset($image))
        {
//           make unique name for image
            $currenttime = Carbon::now()->toDateString();
            $imagename = $slug.'-'.$currenttime.'-'.uniqid().'.'.$image->getClientOriginalExtension();

            if (!Storage::disk('public')->exists('post'))
            {
                Storage::disk('public')->makeDirectory('post');
            }

//            delete old image

            if (Storage::disk('public')->exists('post/'.$post->image))
            {
                Storage::disk('public')->delete('post/'.$post->image);
            }


//           image resize for post
            $resizeimage = Image::make($image)->resize(1600,1066)->save($imagename);
            Storage::disk('public')->put('post/'.$imagename,$resizeimage);
        }else{
            $imagename = $post->image;
        }

        $post->user_id = Auth::id();
        $post->title = $request->title;
        $post->slug = $slug;
        $post->image = $imagename;
        $post->body = $request->body;
        if (isset($request->status)){
            $post->status = true;
        }else{
            $post->status = false;
        }
        $post->is_approved= true;
        $post->save();

        $post->categories()->sync($request->categories);
        $post->tags()->sync($request->tags);
        Toastr::success('Post Successfully Updated :)','Success');
        return redirect()->route('admin.post.index');
    }

    public function pending()
    {
        $posts = Post::where('is_approved','false')->get();
        return view('admin.post.pending',compact('posts'));
    }

    public function approval($id)
    {
       $post =  Post::find($id);
       if ($post->is_approved == false)
       {
           $post->is_approved = true;
           $post->save();
           $post->user->notify(new AuthorPostApproved($post));
           Toastr::success('Post successfully Approved :)','Success');
       }else{
           Toastr::info('Post already Approved :(','Info');
       }
       return redirect()->back();
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        if (Storage::disk('public')->exists('post/'.$post->image))
        {
            Storage::disk('public')->delete('post/'.$post->image);
        }
        $post->categories()->detach();
        $post->tags()->detach();
        $post->delete();
        Toastr::success('Post Successfully Deleted :)','Success');
        return redirect()->route('admin.post.index');
    }
}
