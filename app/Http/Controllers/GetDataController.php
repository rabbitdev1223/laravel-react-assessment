<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Item;
use App\Models\Poster;
class GetDataController extends Controller
{
    //
    public function fetchData($order){

        if ($order == 1)
            $response = Http::get('http://www.omdbapi.com/?s=Matrix&apikey=720c3666');
        else if ($order == 2)
            $response = Http::get('http://www.omdbapi.com/?s=Matrix%20Reloaded&apikey=720c3666');
        else if ($order == 3)
            $response = Http::get('http://www.omdbapi.com/?s=Matrix%20Revolutions&apikey=720c3666');

        $json_result = json_decode($response->body());
        
        foreach($json_result->Search as $record){
            
            if (isset($record->Poster)){

                //check whether poster exists
                $poster = Poster::where('url',$record->Poster)->first();

                if ($poster == null){ //no exist
                    //create a new poster
                    $poster = new Poster;
                    $poster->url = $record->Poster;
                    $poster->save();

                    $item = new Item;
                    $item->title = $record->Title;
                    $item->year = $record->Year;
                    $item->imdbID = $record->imdbID;
                    $item->type = $record->Type;
                    $item->poster_id = $poster->id;
                    $item->save();
                }
                else{

                    //check whether Item exists
                    $item = Item::where('title',$record->Title)
                                ->where('year',$record->Year)
                                ->where('imdbID',$record->imdbID)
                                ->where('type',$record->Type)
                                ->where('poster_id',$poster->id)
                        ->first();
                    
                    if ($item == null){ //if not exist, then create 
                        $item = new Item;
                        $item->title = $record->Title;
                        $item->year = $record->Year;
                        $item->imdbID = $record->imdbID;
                        $item->type = $record->Type;
                        $item->poster_id = $poster->id;
                        $item->save();
                    }
                    
                    
                }
                
            }
        }
        return response()->json($json_result->Search);
    }

}
