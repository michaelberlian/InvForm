<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistoryControllerApi extends Controller
{
    //
    public function create (Request $request){
        // return response(["data" => $request->all()]);
        $user = $request->user()->name;
        $tableName = $user.'_History';
        $stockTableName = $user.'_Stock';

        date_default_timezone_set("Asia/Jakarta");
        $ldate = date('Y-m-d H:i:s');

        DB::table($tableName)->insert([
            'ItemId' => $request->itemid,
            'Type' => $request->type,
            'Quantity' => $request->quantity,
            'Description' => $request->description,
            'created_at' => $ldate,
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
            
        return response(["message"=>'data succesfully added']);
    }

    public function view (Request $request){
        
        $user = $request->user()->name;
        $tableName = $user.'_History';
        $stockTableName = $user.'_Stock';

        // return response(["message"=>'here']);
        $stockList = DB::table($tableName)
            ->leftJoin($stockTableName, $tableName.'.ItemId', '=', $stockTableName.'.id')
            ->select($tableName.'.id', $tableName.'.ItemId', $stockTableName.'.Name', $tableName.'.Type', $tableName.'.Quantity', $tableName.'.Description', $tableName.'.created_at')
            ->where('Name', 'like', '%'.$request->name.'%')
            ->where('Type', 'like', '%'.$request->type.'%')
            ->where($tableName.'.Description', 'like', '%'.$request->description.'%')
            ->where($tableName.'.created_at','>=', $request->startdate)
            ->where($tableName.'.created_at','<=',$request->enddate)
            ->get();

        return response (['data'=>$stockList]);
    }

    public function edit (Request $request, $id){
        $user = $request->user()->name;
        $tableName = $user.'_History';

        $selected = DB::table($tableName)
            ->where('id', $id)
            ->get();

        return response (['data'=>$selected]);
    }

    public function update (Request $request, $id){
        $user = $request->user()->name;
        $tableName = $user.'_History';
        $stockTableName = $user.'_Stock';

        date_default_timezone_set("Asia/Jakarta");
        $ldate = date('Y-m-d H:i:s');

        $selected = DB::table($tableName)->where('id', $id);
        
        if (! $selected->exists()){
            return response(['message' => 'data is unavailable']);
        }
        
        $oldSelectedQty = $selected->get()[0]->Quantity;
        $selected->update([
                'Quantity' => $request->quantity,
                'Description' => $request->description,
                'updated_at' => $ldate,
            ]);
        $selected = $selected->get();
        $item = DB::table($stockTableName)->where('id',$selected[0]->ItemId)->get();
        $qty = $item[0]->Quantity;
        // return response (["message" => $item]);        

        if ($selected[0]->Type == "In"){
            DB::table($stockTableName)
                ->where('id',$selected[0]->ItemId)
                ->update([
                    'Quantity' => $qty + ($request->quantity - $oldSelectedQty),
                    'updated_at' => $ldate,
                ]);
        } else {
            // return response (["message" => "here"]); 
            DB::table($stockTableName)
                ->where('id',$selected[0]->ItemId)
                ->update([
                    'Quantity' => $qty - ($request->quantity - $oldSelectedQty),
                    'updated_at' => $ldate,
                ]);
        }
        return (['message' => 'data edited succesfully']);
    }

    public function delete (Request $request, $id){
        $user = $request->user()->name;
        $tableName = $user.'_History';

        $selected = DB::table($tableName)
            ->where('id', $id);
        
        if (! $selected->exists()){
            return response(['message' => 'data is unavailable']);
        }
        
        $selected->delete();
        return response(['message' => 'data deleted succesfully']);
    }
}
