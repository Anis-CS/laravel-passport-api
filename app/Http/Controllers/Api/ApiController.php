<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use function Nette\Utils\data;

class ApiController extends Controller
{
    // POST [ name, email, password ]

    public function register(Request $request){
        //Validation value
         $request->validate([
            "name"  => "required | string",
            "email" =>  "required | string | email | unique:users",
            "password"  => "required | confirmed"
        ]);

        //Create Users
        User::Create([
            "name"  =>  $request->name,
            "email"  =>  $request->email,
            "password"  => bcrypt($request->password)
        ]);
        return response()->json([
            'status' => true,
            'message' =>'User Request Successfully',
            "data"  => []
        ]);

    }
    // POST [ email, password ]

    public function login(Request $request)
    {
        //validation
        $request->validate([
            "email" => "required | email |string",
            "password" => "required"
        ]);
        $user = User::where('email', $request->email)->first();
        if (!empty($user)){
            //user existed
            if (Hash::check($request->password, $user->password)){
                //password match
                $token = $user->createToken('mytoken')->accessToken;
                //Create Token
                return response()->json([
                    "status"=>true,
                    "message"=>"User Login successfully.",
                    "token"=> $token,
                    "data"=>[]
                ]);
            }else{
                return response()->json([
                    "status"=>false,
                    "message"=>"Password didn`t matched.",
                    "data"=>[]
                ]);
            }

        }else{
            return response()->json([
                "status"=>false,
                "message"=>"Invalid Email Address",
                "data"=>[]
            ]);
        }

    }

    // GET [ Auth: Token ]

    public function profile(){
        $userData = auth()->user();
        return response()->json([
            "status"=>true,
            "message"=>"Profile Information",
            "data"=>$userData
        ]);
    }
    // GET [ Auth: Token ]

    public function logout(){
        $token = auth()->user()->token();
        $token->revoke();
        return response()->json([
            "status"=>true,
            "message"=>"Your Profile logout successfully."
        ]);
    }

}
