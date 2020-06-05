<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockControllerApi extends Controller
{
    //

    public function create (Request $request){
        // return response(["data" => $request->all()]);
        $user = $request->user()->name;
        $tableName = $user.'_Stock';

        date_default_timezone_set("Asia/Jakarta");
        $ldate = date('Y-m-d H:i:s');

        try {
            DB::table($tableName)->insert([
                'Name' => $request->name,
                'Quantity' => $request->quantity,
                'Description' => $request->description,
                'created_at' => $ldate,
                ]
            );
        } catch (Exception $e) {
            $error = substr($e,strpos($e,"Incorrect"),strpos($e, "at")-strpos($e,"Incorrect"));
            return response(["code" => 'BAD', "message"=>'check the inputs. '.$error]);
        }
        return response(["code"=>'OK', "message"=>'data added successfully']);

        
    }

    public function view (Request $request){
        $user = $request->user()->name;
        $tableName = $user.'_Stock';

        try{
            $stockList = DB::table($tableName)
            ->where('Name', 'like', '%'.$request->name.'%')
            ->where('Quantity', '>', '%'.$request->quantity.'%')
            ->where('Description', 'like', '%'.$request->description.'%')
            ->get();
        } catch (Exception $e) {
            return response(["code" => 'BAD', "message"=>'check the inputs']);
        }

        return response (["code"=>'OK', 'data'=>$stockList]);
    }

    public function edit (Request $request, $id){
        $user = $request->user()->name;
        $tableName = $user.'_Stock';

        try{
            $selected = DB::table($tableName)
            ->where('id', $id)
            ->get();
        } catch (Exception $e) {
            return response(["code" => 'BAD', "message"=>'check the inputs']);
        }

        return response (["code"=>'OK', "data"=>$selected]);
    }

    public function update (Request $request, $id){
        $user = $request->user()->name;
        $tableName = $user.'_Stock';

        date_default_timezone_set("Asia/Jakarta");
        $ldate = date('Y-m-d H:i:s');

        $selected = DB::table($tableName)
            ->where('id', $id);
        
        if (! $selected->exists()){
            return response(["code" => 'BAD', 'message' => 'data is unavailable']);
        }
        try{   
            $selected ->update([
                'Name' => $request->name,
                'Quantity' => $request->quantity,
                'Description' => $request->description,
                'updated_at' => $ldate,
                ]);
        } catch (Exception $e){
            $error = substr($e,strpos($e,"Incorrect"),strpos($e, "at")-strpos($e,"Incorrect"));
            return response(["code" => 'BAD', "message"=>'check the inputs. '.$error]);
        }
        return (['code' => 'OK', "message"=>'data updated successfully']);
    }

    public function delete (Request $request, $id){
        $user = $request->user()->name;
        $tableName = $user.'_Stock';

        $selected = DB::table($tableName)
            ->where('id', $id);
        
        if (! $selected->exists()){
            return response(["code" => 'BAD', 'message' => 'data is unavailable']);
        }
        try{
            $selected->delete();
        } catch (Exception $e){
            return response(["code" => 'BAD', "message"=>'check the inputs']);
        }

        return response(['code' => 'OK', "message"=>'data deleted successfully']);
    }

}
