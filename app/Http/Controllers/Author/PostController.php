<?php

namespace App\Http\Controllers\Author;

use App\Category;
use App\Notifications\NewAuthorPost;
use App\Notifications\NewPostNotification;
use App\post;
use App\Subscriber;
use App\Tag;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Auth::user()->posts()->latest()->get();
        return view('author.post.index',compact('posts'));
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
        return view('author.post.create',compact('categories','tags'));
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
        $post->is_approved= false;
        $post->save();

        $post->categories()->attach($request->categories);
        $post->tags()->attach($request->tags);
        $users = User::where('role_id','1')->get();
        Notification::send($users,new NewAuthorPost($post));
        $subscribers = Subscriber::all();
        foreach ($subscribers as $subscriber)
        {
            Notification::route('mail',$subscriber->email)
                ->notify(new NewPostNotification($post));
        }
        Toastr::success('Post Successfully Saved :)','Success');
        return redirect()->route('author.post.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(post $post)
    {
        if ($post->user_id == Auth::id()){
            return view('author.post.show',compact('post'));
        }else{
            Toastr::error('Sorry you can not exist other post :(','Error');
            return redirect()->back();
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(post $post)
    {
        if ($post->user_id == Auth::id()){
            $categories = Category::all();
            $tags = Tag::all();
            return view('author.post.edit',compact('post','categories','tags'));
        }else{
            Toastr::error('Sorry you can not exist other post :(','Error');
            return redirect()->back();
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, post $post)
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
        return redirect()->route('author.post.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(post $post)
    {

        if ($post->user_id != Auth::id()){
            Toastr::error('Sorry you can not exist other post :(','Error');
            return redirect()->back();
        }


        if (Storage::disk('public')->exists('post/'.$post->image))
        {
            Storage::disk('public')->delete('post/'.$post->image);
        }
        $post->categories()->detach();
        $post->tags()->detach();
        $post->delete();
        Toastr::success('Post Successfully Deleted :)','Success');
        return redirect()->route('author.post.index');
        }
}

