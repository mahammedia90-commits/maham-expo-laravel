<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ResetPasswordController extends Controller
{
    public function update(Request $request)
    {  
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|confirmed|min:6',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->with('error', 'Invalid token or email.');
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // حذف التوكن بعد النجاح
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return back()->with('success', 'Password has been reset successfully!');
    }
}
