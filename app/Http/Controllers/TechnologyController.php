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
    public function show($name)
    {
        $find = Technology::where('name', $name)->get();
        return sizeof($find) !== 0 ? $find : response('No technology exists at this location.', 404);
    }

    /**
     * Create new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $request->validate(['name' => 'required|Unique:technologies,name','img_url' => 'required|URL']);
        $created = Technology::create($request->all());
        if ($created) return $created;
        else return response('There was an error creating the requested technology. Please check that you are providing a valid URL.', 400);
    }

    /**
     * Update the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $technology = Technology::find($id);
        $technology->update($request->all());
        return $technology;
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
        return $str;
    }
}
