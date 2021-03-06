<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Lcobucci\JWT\Parser;
use Mockery\CountValidator\Exact;
use PDO;
use Symfony\Component\Process\ExecutableFinder;

class CredentialApi extends Controller
{
    public function register(Request $request){
        try {
            $validatedData = $request->validate([
                'name'=>'required|max:55|unique:users',
                'email'=>'email|required|unique:users',
                'password'=>'required|confirmed',
                ]);
        } catch (Exception $e){
            return response(['code' => 'BAD', 'message' => 'password confirmation does not match']);
        }
        
        try {

            $dbname = $validatedData['name'].'_stock';
            Schema::create($dbname, function (Blueprint $table) {
                $table->id();
                $table->string("Name");
                $table->integer("Quantity");
                $table->text("Description");
                $table->timestamps();
            });
            
            $dbnameHistory = $validatedData['name'].'_history';
            Schema::create($dbnameHistory, function (Blueprint $table) use($dbname) {
                $table->id();
                $table->unsignedBigInteger("ItemId");
                $table->string("ItemName");
                $table->string("Type"); #in/out
                $table->integer("Quantity");
                $table->text("Description");
                $table->timestamps();
            });
        } catch (Exception $e){
            $error = substr($e,strpos($e,"Incorrect"),strpos($e, "at")-strpos($e,"Incorrect"));
            return response(["code" => 'BAD', "message"=>'check the inputs. '.$error]);
        }

        $validatedData['password'] = bcrypt($request->password);

        $user = User::create($validatedData);

        return response(['code' => 'OK', 'user'=> $user]);

    }

    public function update_password (Request $request){
        $user = $request->user();
        try {
            $validatedData = $request->validate([
                'password'=>'required',
                'new_password' => 'required|confirmed'
            ]);
        } catch (Exception $e){
            return response(['code' => 'BAD', 'message' => 'new password confirmation does not match']);
        }

        if(strcmp($request->password, $request->new_password) == 0){
            return response(['code' => 'BAD', 'message' => 'the new and old password cannot be the same']);
        }

        if ((Hash::check($request->password, Auth::user()->password))){
            $new_password = bcrypt($request->new_password);
            
            DB::table('users')
            ->where('id', $user->id)
            ->update([
                'password' => $new_password
                ]);
            
            DB::table('oauth_access_tokens')
            ->where('user_id', $user->id)
            ->update([
                'revoked' => true
            ]);
            return response(['code' => 'OK', 'message' => 'password changed successfully']);
        }
        return response(['code' => 'BAD', 'message' => 'wrong password']);
    }

    public function login(Request $request){
        $loginData = $request->validate([
            'email'=>'email|required',
            'password'=>'required',
        ]);

        if (!auth()->attempt($loginData)){
            return response(["code" => 'BAD', 'message' => 'Invalid credentials']);
        }
        
        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        return response(['code' => 'OK', 'user'=> auth()->user(), 'access_token'=> $accessToken]);
    }

    public function logout(Request $request){

        $id = $request->user()->id;
        DB::table('oauth_access_tokens')
        ->where('user_id', $id)
        ->update([
            'revoked' => true
        ]);

        return response(['code' => 'OK', 'message'=>'logout successfully']);
    }

    public function get_user_data(Request $request){
        $user = $request->user();
        return response (['code' => 'OK', 'user'=>$user]);
    }

}
