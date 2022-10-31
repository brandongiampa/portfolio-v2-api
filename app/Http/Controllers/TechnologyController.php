<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Technology;
use Illuminate\Http\Response;

class TechnologyController extends Controller
{
    /**
     * Display a listing of the resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return Technology::all();
    }

    /**
     * Display the specified resource.
     *
     * @param  str  $name
     * @return \Illuminate\Http\Response
     */

    public function show($id) {
        $technology = Technology::find($id);
        return $technology ? $technology : response('No technology exists at this id.', 404);
    }

    /**
     * Create new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //$request->validate(['name' => 'required|Unique:technologies,name','img_url' => 'required|URL']);
        $msgs = array();
        if (!isset($request['name'])) array_push($msgs, "Please specify a technology name.");
        if (isset($request['name'])) {
            if (Technology::where('name', $request['name'])->count() > 0) {
                array_push($msgs, "That name already exists in our database.");
            }
        }
        if (!isset($request['img_url'])) array_push($msgs, "Please specify a URL for the corresponding img file.");
        if (isset($request['img_url'])) {
            if (!filter_var($request['img_url'], FILTER_VALIDATE_URL)) {
                array_push($msgs, "Please specify a valid URL for the img file.");
            }
        }
        if (sizeof($msgs) > 0) return response(['messages'=>$msgs], 400);

        $created = Technology::create($request->all());
        if ($created) return $created;
        else return response(['messages'=>'There was an unknown error creating the requested technology.'], 400);
    }

    /**
     * Update the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $technology = Technology::find($id);
        if (!$technology) return response(['messages'=>'There is no technology at that location.'], 404);

        $msgs = array();

        if (isset($request['name'])) {
            $matching_technologies = Technology::where('name', $request['name'])->get();

            if (sizeof($matching_technologies) > 1 || (sizeof($matching_technologies) === 1 && $matching_technologies[0]['id'] != $id)) {
                array_push($msgs, "That name already exists in our database.");
            }
        }

        if (isset($request['img_url'])) {
            if (!filter_var($request['img_url'], FILTER_VALIDATE_URL)) {
                array_push($msgs, "Please specify a valid URL for the img file.");
            }
        }
        if (sizeof($msgs) < 1) {
            if ($technology->update($request->all()) > 0) return $technology;
            else return response(['messages'=>"There was an unknown error processing your request."], 400);
        }
        else return response(['messages'=>$msgs], 400);
    }

    /**
     * Delete the specified resource.
     *
     * @param  int  $id
     * @return  str  $str
     */
    public function destroy($id) {
        $technology = Technology::destroy($id);
        $str = $technology > 0 ? "Technology at id #".$id." has been deleted." : "Error deleting technology at id #".$id.".";
        return ['messages'=>$str];
    }
}
