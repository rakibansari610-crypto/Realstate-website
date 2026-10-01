<?php

namespace App\Http\Controllers;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;

class ChangePasswordController extends Controller
{
    public function change_password()
    {
        return view('org.change_password');
    }
    
   public function change_password_store(Request $request)
    {
        $user = auth()->user(); 

        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6|confirmed', // expects new_password_confirmation
        ], [
            'old_password.required' => 'Please enter your old password.',
            'new_password.required' => 'Please enter a new password.',
            'new_password.min' => 'New password must be at least 6 characters.',
            'new_password.confirmed' => 'Confirm password does not match the new password.',
        ]); 

        if (!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors(['old_password' => 'Old password is incorrect.']);
        } 

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password changed successfully!');
    }


    public function reset_password($id)
    {
        try {
            $id = Crypt::decrypt($id); 
            $partner = Partner::find($id);  
            if (!$partner) { 
                return redirect()->back()->with('error', 'User not found.');
            }
            return view('org.reset_password', compact('partner'));
        } catch (\Exception $e) { 
            return redirect()->back()->with('error', 'Invalid or expired link.');
        }
    }



 
    public function reset_password_store(Request $request)
    { 
        $request->validate([
            'new_password' => 'required|min:6|confirmed',
        ], [
            'new_password.required' => 'Please enter a new password.',
            'new_password.min' => 'New password must be at least 6 characters.',
            'new_password.confirmed' => 'Confirm password does not match the new password.',
        ]);
 
        $id = Crypt::decrypt($request->id);
 
        $user = User::find($id);
        if (!$user) {
            return back()->with('error', 'User not found.');
        }
 
        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password changed successfully!');
    }




}
