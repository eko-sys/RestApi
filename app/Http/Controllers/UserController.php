<?php
namespace App\Http\Controllers;
 
use App\User; 
use Validator;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller 
{
 
  public function login(Request $request){ 
 
    $credentials = [
        'email' => $request->email, 
        'password' => $request->password
    ];
 
    if( Auth::attempt($credentials) ){ 
        $user = Auth::user(); 
        $success['token'] =  $user->createToken('Login')->accessToken; 
        return response()->json(['success' => $success], 200);
    } else { 
        return response()->json(['error'=>'Unauthorized'], 401);
    } 
  }
    
  public function register(Request $request) 
    { 
        $validator = Validator::make($request->all(), [ 
        'name' => 'required', 
        'email' => 'required|email', 
        'password' => 'required', 
        'password_confirmation' => 'required|same:password', 
        ]);
    
        if ($validator->fails()) { 
            return response()->json([ 'error'=> $validator->errors() ]);
        }
 
        $data = $request->all(); 
        
        $data['password'] = bcrypt($data['password']);
        
        $user = User::create($data); 
        $success['token'] =  $user->createToken('AppName')->accessToken;
        
        return response()->json(['success'=>$success], 200);
        
    }
}