<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Auth;
use App\Models\Layouts;

class authController extends Controller
{
    //
    public function login(Request $request)
    {
        $email = $request->email;
        $password = $request->password;
        $check = Auth::where("email",$email)->where("password",md5($password))->get();
        if (count($check)) {
            $source = json_encode(["email" => $check[0]->email,'level' => $check[0]->level, "password" => $check[0]->password]);
            $result = md5($source);
            return response()->json(['success' => 1,"login" => $check[0],'token' => $result]);
        }else{
            $checkEmail = Auth::where("email",$email)->get();
            if (count($checkEmail)) {
                return response()->json(['success' => 0,"message" => "Password is not match"]);   
            }
            return response()->json(['success' => 0,"message" => "Email is not exist"]);
        }
    }
    public function register(Request $request)
    {
        $email = $request->email;
        $password = $request->password;
        $fullname = $request->fullname;
        $checkEmail = Auth::where("email",$email)->get();
        if (count($checkEmail)) {
            return response()->json(['success' => 0,"message" => "Email is already exist."]);   
        }
        $access = [];
        $layouts = Layouts::get();
        for ($i=0; $i < count($layouts); $i++) { 
            $temp = ["custom_access" => 0, "mass_access" => 0, "auto_access" => 0 ,"layout" => $layouts[$i]->id, "layout_access" => 0];
            array_push($access,$temp);
        }

        $in = Auth::insert([
            'fullname' => $fullname,
            'password' => md5($password),
            'email' => $email,
            "access" => json_encode($access)
        ]);
        return response()->json(['success' => 1, "message" =>"You have been successfully registered."]);
    }

    public function getUsers(Request $request)
    {
        $users = Auth::get();
        return response()->json($users);
    }

    public function setAccess(Request $request)
    {
        $access = $request->access;
        $userId = $request->userId;
        $layoutId = $request->layoutId;
        $userInfo = Auth::where("id",$userId)->get();
        $userAccess = json_decode($userInfo[0]->access);
        $newAccess = [];
        $cnt = 0;
        for ($i=0; $i < count($userAccess); $i++) { 
            if ($userAccess[$i]->layout == $layoutId) {
                $cnt ++;
                array_push($newAccess,$access);
            }else{
                array_push($newAccess,$userAccess[$i]);
            }
        }
        if ($cnt == 0) {
            array_push($newAccess,$access);
        }

        $up = Auth::where("id",$userId)->update(['access' => json_encode($newAccess)]);
        return response()->json(['message' => "success"]);
    }

    public function checkToken(Request $request)
    {
        $allusers = Auth::get();
        $token = $request->token;
        $flag = 0;
        $userData = [];
        for ($i=0; $i < count($allusers); $i++) { 
            $cont = ["email" => $allusers[$i]->email,'level' => $allusers[$i]->level, "password" => $allusers[$i]->password];
            $content = json_encode($cont);
            if (md5($content) == $token) {
                $flag = 1;
                $userData = $allusers[$i];
            }
        }
        if ($flag == 1) {
            return response()->json(['success' => 1, "user" => $userData]);
        }else{
            return response()->json(['success' => 0]);
        }
    }
    public function setPassword(Request $request)
    {
        $email = $request->email;
        $password = $request->password;
        $newpassword = $request->newpassword;
        // dd($email,$password,$newpassword);
        $check = Auth::where("email",$email)->where("password",md5($password))->get();
        if (count($check)) {
            $newmdapassword = md5($newpassword);
            $up = Auth::where("email",$email)->update([
                "password" => $newmdapassword
            ]);
            $source = json_encode(["email" => $check[0]->email,'level' => $check[0]->level, "password" => $newmdapassword]);
            $result = md5($source);
            return response()->json(['success' => 1,"login" => $check[0],'token' => $result]);
        }else{
            $checkEmail = Auth::where("email",$email)->get();
            if (count($checkEmail)) {
                return response()->json(['success' => 0,"message" => "Password is not match"]);   
            }
            return response()->json(['success' => 0,"message" => "Email is not exist"]);
        }
    }
}
