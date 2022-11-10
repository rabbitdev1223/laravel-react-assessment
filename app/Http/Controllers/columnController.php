<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Column;
use App\Models\Combobox;
use App\Models\Layouts;
use App\Models\AutoUpdate;

class columnController extends Controller
{
    //
    public function response(Request $request,$method)
    {
        switch($method){
            case "getColumns":
                return $this->getColumns();
            case "getColumnDataWithLayout":
                return $this->getColumnDataWithLayout($request);
            case "addColumn":
                return $this->addColumn($request);
            case "getColumnWithCombobox":
                return $this->getColumnsWithCombobox($request);
            case "addCombobox":
                return $this->addComboboxValue($request);
            case "editColumn":
                return $this->editColumn($request);
            case "editCombobox":
                return $this->editComboboxValue($request);
            case "deleteCombobox":
                return $this->deleteComboboxValue($request);
            case "deleteColumn":
                return $this->deleteColumn($request);
            case "getColumnsWithMass":
                return $this->getColumnsWithMass();
            case "setMass":
                return $this->setMass($request);
            case "getOperations":
                return $this->getOperations();
            case "getAutoRules":
                return $this->getAutoRules();
            case "setRule":
                return $this->setRule($request);
            case "deleteRule":
                return $this->deleteRule($request);
            case "runRule":
                return $this->runRule($request);
            case "copyRule":
                return $this->copyRule($request);
            case "saveColumnOrder":
                return $this->saveColumnOrder($request);
            case "changeHide":
                return $this->changeHide($request);
            case "getFormulas":
                return $this->getFormulas($request);
            case "setFormula":
                return $this->setFormula($request);
        }
    }
    public function getColumns()
    {
        $Column = new Column;
        $columns = Column::join("tbl_column_types","tbl_column.type",'=','tbl_column_types.id')
                ->leftJoin("tbl_layouts","tbl_column.layout","=",'tbl_layouts.id')
                ->select("tbl_column.id",
                'tbl_column.name',
                'tbl_column.description',
                'tbl_column.size',
                'tbl_column.desize',
                'tbl_column.manual_editing',
                'tbl_column.mass_update',
                'tbl_column_types.type',
                'tbl_column.layout',
                'tbl_column.hide',
                'tbl_column.mass_val',
                'tbl_column.auto_update',
                'tbl_layouts.layout_name',
                'tbl_column.report',
                'tbl_column_types.id as type_id')
                ->orderBy("tbl_column.order")
                ->get();
        for ($i=0; $i < count($columns); $i++) { 
            if($columns[$i]['type_id'] == 2)
            {
                $columnId = $columns[$i]['id'];
                $comboboxs = Combobox::where("column_id",$columnId)->get();
                $columns[$i]['combobox'] = $comboboxs;
            }
            if($columns[$i]['auto_update'] != 0)
            {
                $autoUpdate = $columns[$i]['auto_update'];
                $autoUpdateRule = AutoUpdate::where("id",$autoUpdate)->get();
                $columns[$i]['rule'] = $autoUpdateRule;
            }
        }
        $column_types = DB::table("tbl_column_types")->get();
        return response()->json(["columns" => $columns, "column_type" => $column_types]) ;
    }

    public function saveColumnOrder(Request $request)
    {
        $columns = $request->datas;
        for ($i=0; $i < count($columns); $i++) { 
            $up = Column::where("id",$columns[$i])->update(['order' => ($i +1)]);
        }
        return response()->json(['msg' => "success"]);
    }

    public function getColumnDataWithLayout(Request $request)
    {
        $layout = $request->layout;
        $columns = Column::join("tbl_column_types","tbl_column.type",'=','tbl_column_types.id')
                ->join("tbl_layouts","tbl_column.layout","=",'tbl_layouts.id')
                ->select("tbl_column.id",
                'tbl_column.name',
                'tbl_column.description',
                'tbl_column.size',
                'tbl_column.desize',
                'tbl_column.manual_editing',
                'tbl_column.mass_update',
                'tbl_column_types.type',
                'tbl_column.layout',
                'tbl_column.hide',
                'tbl_layouts.layout_name',
                'tbl_column_types.id as type_id')
                ->where("tbl_column.layout","=",$layout)
                ->orderBy("tbl_column.order")
                ->get();
        for ($i=0; $i < count($columns); $i++) { 
            if($columns[$i]['type'] == 2)
            {
                $columnId = $columns[$i]['id'];
                $comboboxs = Combobox::where("column_id",$columnId)->get();
                $columns[$i]['combobox'] = $comboboxs;
            }
        }
        $column_types = DB::table("tbl_column_types")->get();
        return response()->json(["columns" => $columns, "column_type" => $column_types]) ;      
    }

    public function addColumn(Request $request)
    {
        $column = $request->body;
        $in = Column::insert($column);
        return response()->json(["msg" => "success"]);
    }

    public function getColumnsWithCombobox(Request $request)
    {
        $Id = $request->id;
        $values = Combobox::where("column_id",'=',$Id)->get();
        return response()->json(["res" => $values]);
    }
    public function addComboboxValue(Request $request)
    {
        $Id = $request->id;
        $data = $request->data;
        $data['column_id'] = $Id;
        $in = Combobox::insert($data);
        $values = Combobox::where("column_id",'=',$Id)->get();
        return response()->json(["res" => $values]);
    }
    public function editColumn(Request $request)
    {
        $Id = $request->id;
        $data = $request->data;
        $up = Column::where("id",$Id)
                ->update($data);
        return response()->json(["msg" => "Success"]);
    }

    public function editComboboxValue(Request $request)
    {
        $Id = $request->id;
        $data = $request->data;
        $up = Combobox::where("id","=",$Id)
                ->update($data);
        $values = Combobox::where("column_id",'=',$Id)->get();
        return response()->json(["res" => $values]);
    }

    public function deleteComboboxValue(Request $request)
    {
        $Id = $request->id;
        $columnId = $request->columnId;
        $del = Combobox::where("id","=",$Id)->delete();
        $values = Combobox::where("column_id","=",$columnId)->get();
        return response()->json(["res" => $values]);
    }
    public function deleteColumn(Request $request)
    {
        $Id = $request->id;
        $del = Column::where("id","=",$Id)->delete();
        return response()->json(["msg" => "success"]);
    }

    public function getColumnsWithMass()
    {
        $mass = Column::where("mass_update",1)->get();
        for ($i=0; $i < count($mass); $i++) { 
            if($mass[$i]['type'] == 2)
            {
                $columnId = $mass[$i]['id'];
                $comboboxs = Combobox::where("column_id",$columnId)->get();
                $mass[$i]['combobox'] = $comboboxs;
            }
        }
        return response()->json($mass);
    }

    public function setMass(Request $request)
    {
        $id = $request->id;
        $val = $request->val;
        $up = Column::where("id",$id)->update(["mass_val"=>$val]);
        return response()->json(["msg" => "success"]);
    }

    public function getOperations()
    {
        $result = DB::table("tbl_operations")->get();
        return response()->json($result);
    }
    public function getAutoRules()
    {
        $result = AutoUpdate::get();
        return response()->json($result);
    }

    public function setRule(Request $request)
    {
        $id = $request->id;
        $data = $request->data;
        $up = AutoUpdate::where("id",$id)->update($data);
        return response()->json(["msg" => "success"]);
    }
    public function deleteRule(Request $request)
    {
        $id = $request->id;
        $del = AutoUpdate::where("id",$id)->delete();
        return response()->json(['msg' => "success"]);
    }

    public function runRule(Request $request)
    {
        $id = $request->id;
        $target = AutoUpdate::where("id",$id)->get();
        $targetId = $target[0]->target_column;
        $up = Column::where("id",$targetId)->update(["auto_update" => $id]);
        return response()->json(["msg" => "success"]);
    }

    public function copyRule(Request $request)
    {
        $data = $request->data;
        $in = AutoUpdate::insert([
            "exist_column" => $data['exist_column'],
            "operation_id" => $data['operation_id'],
            "match_value" => $data['match_value'],
            'source_column' => $data['source_column'],
            'source_constant' => $data['source_constant'],
            "target_column" => $data['target_column'],
            "layout" => $data['layout']
        ]);
        return response()->json(['msg' => "success"]);
    }
    public function changeHide(Request $request)
    {
        $id = $request->id;
        $checked = $request->checked;
        $up = Column::where("id","=",$id)->update(["hide" => $checked]);
        return response()->json(['msg' => "success"]);
    }

    public function getFormulas(Request $request)
    {
        $result = DB::table("tbl_computedcolumn")->get();
        return response()->json($result);
    }
    
    public function setFormula(Request $request)
    {
        $columnId = $request->columnId;
        $columns = json_encode($request->columns) ;
        $operation = $request->operation;
        $check = DB::table("tbl_computedcolumn")->where("fo_target",$columnId)->get();
        if (count($check)) {
            $up = DB::table("tbl_computedcolumn")
                    ->where("fo_target",$columnId)
                    ->update([
                        'fo_columns' => $columns,
                        "fo_operation" => $operation 
                    ]);
        }else{
            $in = DB::table("tbl_computedcolumn")
                    ->insert([
                        "fo_target" => $columnId,
                        "fo_columns" => $columns,
                        "fo_operation" => $operation
                    ]);
        }
        return response()->json(["message" => "success"]);
    }
}

