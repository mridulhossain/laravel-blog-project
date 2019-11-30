<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use Brian2694\Toastr\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categorys = Category::latest()->get();
        return view('admin.category.index',compact('categorys'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.category.create');
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
           'name' => 'required|unique:categories',
           'image' => 'required|mimes:jpeg,bmp,png,jpg'
           ]);


//       get form image
        $image = $request->file('image');
        $slug  = Str::slug($request->name);
        if (isset($image))
        {
//            make unique name for image

            $currentdate = Carbon::now()->toDateString();

            $imagename = $slug.'-'.$currentdate.'-'.uniqid().'.'.$image->getClientOriginalExtension();

            //check category directory is exist

            if (!Storage::disk('public')->exists('category'))
            {
                Storage::disk('public')->makeDirectory('category');
            }

//            resize image and upload

            $category = Image::make($image)->resize(1600,479)->save($imagename);

//            save image
            Storage::disk('public')->put('category/'.$imagename,$category);

//            for category slider

            if (!Storage::disk('public')->exists('category/slider'))
            {
                Storage::disk('public')->makeDirectory('category/slider');
            }

//            resize for slider

            $slider = Image::make($image)->resize(500,333)->save($imagename);
            Storage::disk('public')->put('category/slider/'.$imagename,$slider);


        }
        else{
            $imagename = 'default.png';
        }

        $category = new Category();
        $category->name = $request->name;
        $category->slug = $slug;
        $category->image = $imagename;
        $category->save();
        \Brian2694\Toastr\Facades\Toastr::success('Category  Successfully Saved :)','Success');
        return redirect()->route('admin.category.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = Category::find($id);
        return view('admin.category.edit',compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'name' => 'required|unique:categories',
            'image' => 'mimes:jpeg,bmp,png,jpg'
        ]);


//       get form image
        $image = $request->file('image');
        $slug  = Str::slug($request->name);
        $category = Category::find($id);
        if (isset($image))
        {
//            make unique name for image

            $currentdate = Carbon::now()->toDateString();

            $imagename = $slug.'-'.$currentdate.'-'.uniqid().'.'.$image->getClientOriginalExtension();

            //check category directory is exist

            if (!Storage::disk('public')->exists('category'))
            {
                Storage::disk('public')->makeDirectory('category');
            }

//            delete old image
            if (Storage::disk('public')->exists('category/'.$category->image))
            {
                Storage::disk('public')->delete('category/'.$category->image);
            }


//            resize image and upload

            $categoryimage = Image::make($image)->resize(1600,479)->save($imagename);

//            save image
            Storage::disk('public')->put('category/'.$imagename,$categoryimage);

//            for category slider

            if (!Storage::disk('public')->exists('category/slider'))
            {
                Storage::disk('public')->makeDirectory('category/slider');
            }

//            delete old image
            if (Storage::disk('public')->exists('category/slider/'.$category->image))
            {
                Storage::disk('public')->delete('category/slider/'.$category->image);
            }

//            resize for slider

            $slider = Image::make($image)->resize(500,333)->save($imagename);
            Storage::disk('public')->put('category/slider/'.$imagename,$slider);


        }
        else{
            $imagename = $category->image;
        }


        $category->name = $request->name;
        $category->slug = $slug;
        $category->image = $imagename;
        $category->save();
        \Brian2694\Toastr\Facades\Toastr::success('Category successfully updated!','Success');
        return redirect()->route('admin.category.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       $category =  Category::find($id);
       if (Storage::disk('public')->exists('category/'.$category->image))
       {
           Storage::disk('public')->delete('category/'.$category->image);
       }
        if (Storage::disk('public')->exists('category/slider/'.$category->image))
        {
            Storage::disk('public')->delete('category/slider/'.$category->image);
        }
        $category->delete();
        \Brian2694\Toastr\Facades\Toastr::success('Category successfully Deleted!','Success');
        return redirect()->back();
    }
}
