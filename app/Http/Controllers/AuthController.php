<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        if($validator->fails()){
            $errors = $validator->errors();
            $this->responser(false, "Bad Request", null, $errors, 400);
        }
        $alreadyExists = User::where('email', $request->input('email'))->first();
        if($alreadyExists){
            return $this->responser(false, 'Error', null, 'User already Exists', 409);
        }
        try {
            $user = new User();
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $plainPassword = $request->input('password');
            $user->password = Hash::make($plainPassword);

            $user->save();
            return $this->responser(true, 'Created', $user, null, 201);
        } catch (\Exception $e){
            return $this->responser(false, 'Error', null, 'User registration Failed', 409);
        }
    }

    public function login(Request $request)
    {
        $rules = [
            'email.required' => 'Email can not be empty.',
            'password.required' => 'Password can not be empty.'
        ];
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
        'password' => 'required|string',
        ], $rules);

        if($validator->fails()) {
            $errors = $validator->errors();
            return $this->responser(false, 'Bad Request', null, $errors, 400);
        }

        $credentials = $request->only("email", "password");
        $token = null;
        if(!$token = Auth::attempt($credentials)) {
            return $this->responser(false, 'Unauthorized', null, null, 401);
        }
        /* $token = $this->createNewToken($token);
        $response = new Response(['success' => true], 200);
        $response->withCookie(Cookie::create('token', $token['token'], $token['expires_in']));
        return $response; */
        return $this->responser(true, 'Accepted', $this->createNewToken($token), null, 200);
    }

    public function logout(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);

        try {
            Auth::invalidate($request->token);
            $details = "Logged Out";
            return $this->responser(true, "Accepted", $details, null, 200);
        } catch (JWTException $e) {
            $details = "Please Try again!";
            return $this->responser(false, "Internal Error", $details, null, 500);
        }
    }

    public function refresh()
    {
        $refreshToken = $this->createNewToken(Auth::refresh());
        return $this->responser(true, "Accepted", $refreshToken, null, 200);
    }

    public function getAuthUser(Request $request)
    {
        $this->validate($request, ['token' => 'required']);
        $user = Auth::authenticate($request->token);
        $response = ['user' => $user];
        return $this->responser(true, "Accepted", $response, null, 200);
    }
}
