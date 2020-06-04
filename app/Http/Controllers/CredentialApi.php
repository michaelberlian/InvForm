<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Lcobucci\JWT\Parser;


class CredentialApi extends Controller
{
    public function register(Request $request){
        $validatedData = $request->validate([
            'name'=>'required|max:55|unique:users',
            'email'=>'email|required|unique:users',
            'password'=>'required|confirmed',
        ]);

        $dbname = $validatedData['name'].'_Stock';

        Schema::create($dbname, function (Blueprint $table) {
            $table->id();
            $table->string("Name");
            $table->integer("Quantity");
            $table->text("Description");
            $table->timestamps();
        });

        $dbname = $validatedData['name'].'_History';
        Schema::create($dbname, function (Blueprint $table) {
            $table->id();
            $table->string("ItemId");
            $table->string("Type"); #in/out
            $table->integer("Quantity");
            $table->text("Description");
            $table->timestamps();
        });


        $validatedData['password'] = bcrypt($request->password);

        $user = User::create($validatedData);


        $accessToken = $user->createToken('authToken')->accessToken;

        return response(['user'=> $user, 'access_token'=> $accessToken]);

    }

    public function login(Request $request){
        $loginData = $request->validate([
            'email'=>'email|required',
            'password'=>'required',
        ]);

        if (!auth()->attempt($loginData)){
            return response(['message' => 'Invalid credentials']);
        }
        
        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        return response(['user'=> auth()->user(), 'access_token'=> $accessToken]);
    }

    public function logout(Request $request){
        $id = $request->user()->id;
        DB::table('oauth_access_tokens')
        ->where('user_id', $id)
        ->update([
            'revoked' => true
        ]);

        return response(['message'=>'logout successfully']);
    }

    public function get_user_data(Request $request){
        $user = $request->user();
        return response (['user'=>$user]);
    }
}
