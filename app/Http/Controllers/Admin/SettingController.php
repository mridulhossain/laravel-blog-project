<?php

namespace App\Http\Controllers\Admin;

//use http\Client\Curl\User;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;


class SettingController extends Controller
{
    public function index()
    {
        return view('admin.setting');
    }

    public function updateProfile(Request $request)
    {
        $this->validate($request,[
            'name' => 'required',
            'email' => 'required|email',
            'image' => 'required|image',
        ]);

        $image = $request->file('image');
        $slug = Str::slug($request->name);
        $user =User::findOrFail(Auth::id());
        if (isset($image))
        {
            $currentdate= Carbon::now()->toDateString();
            $imagename =$slug.'-'.$currentdate.'-'.uniqid().'.'.$image->getClientOriginalExtension();

            if (!Storage::disk('public')->exists('profile'))
            {
                Storage::disk('public')->makeDirectory('profile');
            }
//            delete profile
            if (Storage::disk('public')->exists('profile/'.$user->image))
            {
                Storage::disk('public')->delete('profile/'.$user->image);
            }

            $profile = Image::make($image)->resize(500,500)->save($imagename);
            Storage::disk('public')->put('profile/'.$imagename,$profile);
    }else{
        $imagename = $user->image;
        }
        $user->name = $request->name;
        $user->email = $request->email;
        $user->image = $imagename;
        $user->about = $request->about;
        $user->save();
        Toastr::success('User Profile Updated Successfully!','Success');
        return redirect()->back();
    }

    public function updatePassword(Request $request)
    {
       $this->validate($request,[
          'old_password' => 'required',
           'password' => 'required|confirmed ',
       ]);

       $hashtedpass =Auth::user()->password;

       if (Hash::check($request->old_password,$hashtedpass)){

            if (!Hash::check($request->password,$hashtedpass))
            {
                $user =User::find(Auth::id());
                $user->password = Hash::make($request->password);
                $user->save();
                Toastr::success('Your Password Changed Successfully!','Success');
                Auth::logout();
                return redirect()->back();
            }else{
                Toastr::error('New Password cannot be the same as old Password','Error');
                return redirect()->back();
            }
       }else{
           Toastr::error('Current password not match','Error');
           return redirect()->back();
       }
    }
}
