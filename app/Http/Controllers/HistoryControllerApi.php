<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistoryControllerApi extends Controller
{
    //
    public function create (Request $request){
        // return response(["data" => $request->all()]);
        $user = $request->user()->name;
        $tableName = $user.'_history';
        $stockTableName = $user.'_stock';

        date_default_timezone_set("Asia/Jakarta");
        $ldate = date('Y-m-d H:i:s');

        try{
            DB::table($tableName)->insert([
                'ItemId' => $request->itemid,
                'ItemName' => $request->itemname,
                'Type' => $request->type,
                'Quantity' => $request->quantity,
                'Description' => $request->description,
                'created_at' => $ldate,
                'updated_at' => $ldate,
                ]
            );
            
            $item = DB::table($stockTableName)->where('id',$request->itemid)->get();
            $qty = $item[0]->Quantity;
            
            if ($request->type == 'In'){
                DB::table($stockTableName)
                ->where('id',$request->itemid)
                ->update([
                    'Quantity' => $qty + $request->quantity,
                    'updated_at' => $ldate,
                    ]);
            } else {
                DB::table($stockTableName)
                ->where('id',$request->itemid)
                ->update([
                    'Quantity' => $qty - $request->quantity,
                    'updated_at' => $ldate,
                    ]);
            }
        } catch (Exception $e) {
            $error = substr($e,strpos($e,"Incorrect"),strpos($e, "at")-strpos($e,"Incorrect"));
            return response(["code" => 'BAD', "message"=>'check the inputs. '.$error]);
        }
        return response(["code"=>'OK', "message"=>'data added successfully']);
    }

    public function view (Request $request){
        
        $user = $request->user()->name;
        $tableName = $user.'_history';

        // return response(["message"=>'here']);
        if (is_null($request->startdate)){
            $request->startdate = "1990-01-01";
        } else {
            $request->startdate = date('Y-m-d H:i:s', strtotime($request->startdate));
        }
        if (is_null($request->endate)){
            date_default_timezone_set("Asia/Jakarta");
            $ldate = date('Y-m-d H:i:s');
            $request->enddate = $ldate;
        } else {
            $request->enddate = date('Y-m-d H:i:s', strtotime($request->enddate));
        }

        return response (['code' => 'test' , 'data' => [$request->startdate,$request->enddate]]);

        try{

            $stockList = DB::table($tableName)
            ->where('ItemName', 'like', '%'.$request->name.'%')
            ->where('Type', 'like', '%'.$request->type.'%')
            ->where('Description', 'like', '%'.$request->description.'%')
            ->where('updated_at','>=', $request->startdate)
            ->where('updated_at','<=', $request->enddate)
            ->get();
            
        } catch (Exception $e){
            return response(["code" => 'BAD', "message"=>'check the inputs']);
        }
        return response (["code"=>'OK', "data"=>$stockList]);
    }

    public function edit (Request $request, $id){
        $user = $request->user()->name;
        $tableName = $user.'_history';

        try{

            $selected = DB::table($tableName)
            ->where('id', $id)
            ->get();
        } catch (Exception $e){
            return response(["code" => 'BAD', "message"=>'check the inputs']);
        }

        return response (["code"=>'OK', 'data'=>$selected]);
    }

    public function update (Request $request, $id){
        $user = $request->user()->name;
        $tableName = $user.'_history';
        $stockTableName = $user.'_stock';

        date_default_timezone_set("Asia/Jakarta");
        $ldate = date('Y-m-d H:i:s');

        $selected = DB::table($tableName)->where('id', $id);
        
        if (! $selected->exists()){
            return response(["code" => 'BAD', 'message' => 'data is unavailable']);
        }
        
        try{
            $oldSelectedQty = $selected->get()[0]->Quantity;
            $selected->update([
                    'Quantity' => $request->quantity,
                    'Description' => $request->description,
                    'updated_at' => $ldate,
                    ]);
            $selected = $selected->get();
            $item = DB::table($stockTableName)->where('id',$selected[0]->ItemId)->get();
            $qty = $item[0]->Quantity;
            
            if ($selected[0]->Type == "In"){
                DB::table($stockTableName)
                    ->where('id',$selected[0]->ItemId)
                    ->update([
                        'Quantity' => $qty + ($request->quantity - $oldSelectedQty),
                        'updated_at' => $ldate,
                ]);
            } else {
                DB::table($stockTableName)
                        ->where('id',$selected[0]->ItemId)
                        ->update([
                            'Quantity' => $qty - ($request->quantity - $oldSelectedQty),
                            'updated_at' => $ldate,
                ]);
            }
        }catch(Exception $e){
            $error = substr($e,strpos($e,"Incorrect"),strpos($e, "at")-strpos($e,"Incorrect"));
            return response(["code" => 'BAD', "message"=>'check the inputs. '.$error]);
        }
            return (["code" => 'OK', 'message' => 'data edited succesfully']);
        }

    public function delete (Request $request, $id){
        $user = $request->user()->name;
        $tableName = $user.'_history';

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
        return response(["code" => 'OK', 'message' => 'data deleted succesfully']);
    }
}
