<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Work;

define('NUMBER_OF_WORKS_PER_PAGE', 20);

class WorkController extends Controller
{
    /**
     * Display a listing of the resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return Work::all();
    }

    /**
     * Retrieve an individual resource by ID.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        return Work::find($id);
    }

    /**
     * Find multiple resources matching certain criteria and page accordingly.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request) {
        $name = isset($request['name']) ? $request['name'] : '';
        $technology = isset($request['technology']) ? $request['technology'] : '';
        $feature = isset($request['feature']) ? $request['feature'] : '';
        $page = isset($request['page']) ? $request['page'] : '';

        $results = Work::where('name', 'LIKE', '%'.$name.'%', 'AND')
            ->where('features', 'LIKE', '%'.$feature.'%', 'AND')
            ->where('technologies', 'LIKE', '%'.$technology.'%')
            ->offset((NUMBER_OF_WORKS_PER_PAGE * intval($page)) - NUMBER_OF_WORKS_PER_PAGE)
            ->limit(NUMBER_OF_WORKS_PER_PAGE)
            ->get();

        return sizeof($results) > 0 ? response($results, 200) : response(['messages'=>'Your search yielded zero results.']);
    }

    /**
     * Create new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $request->validate([
            'name' => 'Required|Unique:works,name',
            'subheader' => 'Required',
            'description' => 'Required',
            'hashtags' => 'Required',
            'slug' => 'Required',
            'img_url' => 'Required|URL',
            'carousel_img_urls' => 'Required',
            'site_url' => 'Required|URL',
            'github_url' => 'Required|URL|Regex:/https:\/\/(www\.)?github\.com\/.+/',
            'features' => 'Required',
            'technologies' => 'Required'
        ]);

        return Work::create($request->all());
    }

    /**
     * Update the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $nameValidator = '';
        $work = Work::find($id);

        if (isset($request['name'])) {
            if ($request['name'] !== $work['name']) {
                $nameValidator = 'Unique:works,name';
            }
        }

        $request->validate([
            'name' => $nameValidator,
            'img_url' => 'URL',
            'site_url' => 'URL',
            'github_url' => 'URL|Regex:/https:\/\/(www\.)?github\.com\/.+/'
        ]);

        $work->update($request->all());
        return $work;
    }

    /**
     * Delete the specified resource.
     *
     * @param  int  $id
     * @return str  $str
     */
    public function destroy($id) {
        $work = Work::destroy($id);
        $str = $work > 0 ? "Work at id #".$id." has been deleted." : "Error deleting work at id #".$id.".";
        $responseCode = $work > 0 ? 200 : 400;
        return response(['messages'=>[$str]], $responseCode);
    }
}
