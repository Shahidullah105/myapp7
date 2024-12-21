<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
   public function edit(){
    $data=Setting::firstOrFail();
     return view('admin.setting.edit',compact('data'));
   }

   public function update(Request $request){
       
    //dd($request->all());

    $id=$request->id;
    $request->validate([
        'compay_name' => 'required|max:45',
        'email' => 'required',  
        'mobile' => 'required',
        'address' => 'required',
        'logo'=>'required|mimes:jpeg,jpg,png,gif,webp|max:100000',  
    ]);

    $oldimg=Setting::findOrFail(1);
    
    $deleteimg=public_path('logo/'.$oldimg['logo']);
    $image_rename='';

    if ($request->hasFile('logo')){
        $image = $request->file('logo');
        $ext=$image->getClientOriginalExtension();

        if(file_exists($deleteimg)){
          unlink($deleteimg);
        }


        $image_rename=time() . '_' . rand(100000, 10000000) . '.' .$ext; 
        $image->move(public_path('logo'), $image_rename);
    }
    else{
        $image_rename=$oldimg['logo'];  
    }


    
    $update = Setting::where('id',1)->update([
        'compay_name' => $request['compay_name'],
        'email' => $request['email'],
        'mobile' => $request['mobile'],
        'address' => $request['address'],
        'logo' => $image_rename ,
    ]);

    // Check if the insertion was successful
    if($update){
        return back()->with('success', 'Data updated Successfully');
    } else {
        return back()->with('error', 'Query Failed');   
    }
}

}
