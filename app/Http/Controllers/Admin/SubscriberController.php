<?php

namespace App\Http\Controllers\Admin;

use App\Subscriber;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubscriberController extends Controller
{

    public  function index()
    {
        $subscribers = Subscriber::latest()->get();
        return view('admin.subscriber',compact('subscribers'));
    }

    public function destroy($id)
    {
      $subscriber = Subscriber::findOrFail($id);
      $subscriber->delete();
      Toastr::success('subscriber deleted successfully :)','Success');
      return redirect()->back();
    }

}
