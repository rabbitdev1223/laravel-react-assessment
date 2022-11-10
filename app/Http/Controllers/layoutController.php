<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Layouts;
use App\Models\Template;
use Illuminate\Support\Facades\DB;

class layoutController extends Controller
{
    //
    public function response($method,Request $request)
    {
        switch($method)
        {
            case "getLayouts":
                return $this->getLayouts($request);
            case "editLayout":
                return $this->editLayout($request);
            case "addLayout":
                return $this->addLayout($request);
            case "deleteLayout":
                return $this->deleteLayout_1($request);
            case "saveReport":
                return $this->saveReport($request);
            case "getReports":
                return $this->getReports($request);
            case "getTemplate":
                return $this->getTemplate($request);
            case "setTemplate":
                return $this->setTemplate($request);
            case "insertTemplate":
                return $this->insertTemplate($request);
        }
    }

    public function getLayouts(Request $request)
    {
        $res = Layouts::get();
        return response()->json(["res" => $res]);
    }
    public function editLayout(Request $request)
    {
        $id = $request->id;
        $layout = $request->layout;
        $up = Layouts::where("id","=",$id)->update($layout);
        return response()->json(["msg" => "success"]);
    }
    public function addLayout(Request $request)
    {
        $layout = $request->layout;
        $in = Layouts::insert($layout);
        return response()->json(["msg" => "success"]);
    }

    public function deleteLayout_1(Request $request)
    {
        $id =  $request->id;
        $checked = $request->checked;
        $del = Layouts::where("id","=",$id)
        ->update(["layout_active" => $checked]);
        return response()->json(["msg" => $del]);
    }

    public function saveReport(Request $request)
    {
        $report = $request->report;
        $layout = $request->layout;
        $userId = $request->userId;
        $reportId = $request->templateId;
        $check = DB::table("tbl_reports")
                    ->where("re_report",$reportId)
                    ->count();
        if ($check) {
            $up = DB::table("tbl_reports")
                    ->where("re_report",$reportId)
                    ->update([
                        're_content' => json_encode($report)
                    ]);
        }else{
            $in = DB::table("tbl_reports")
                    ->insert([
                        're_layout'=> $layout,
                        're_userId'=> $userId,
                        're_report'=> $reportId,
                        're_content'=> json_encode($report)
                    ]);
        }
        return response()->json(['message'=>'success']);
    }

    public function getReports(Request $request)
    {
        $result = DB::table("tbl_reports")->get();
        return response()->json($result) ;
    }

    public function getTemplate(Request $request)
    {
        $result = Template::get();
        return response()->json($result) ;
    }
    public function setTemplate(Request $request)
    {
        $Id = $request->id;
        $data = $request->data;
        $up = Template::where("id",$Id)
                        ->update($data);
        return response()->json(["message"=>"success"]);
    }
    public function insertTemplate(Request $request)
    {
        $data = $request->data;
        $in = Template::insert($data);
        return response()->json(['message'=>'success']);
    }
}

