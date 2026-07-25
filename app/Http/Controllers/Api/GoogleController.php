<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Exception;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    //
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\Response
     */

    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callbackgoogle()
    {
        try {
            $googleuser = Socialite::driver('google')->user();
            //dd($user); //for testing
            $user = User::where('google_id', $googleuser->getId())->first();

            if (!$user) {
                $new_user = User::create([
                    'name' => $googleuser->getName(),
                    'email' => $googleuser->getEmail(),
                    'google_id' => $googleuser->getId(),
                    'password' => bcrypt('12345678'),
                ]);

                Auth::login($new_user);
                return redirect()->intended('/admin/dashboard');
            } else {
                Auth::login($user);
                return redirect()->intended('/admin/dashboard');
            }
        } catch (Exception $e) {
            dd('Something went wrong    ' . $e->getMessage());
        }
    }
}
