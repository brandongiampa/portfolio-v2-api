<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Work;

define('NUMBER_OF_WORKS_PER_PAGE', 20);
define('MINIMUM_DESCRIPTION_WORD_COUNT', 50);
define('MSG_UNKNOWN_ERROR', 'There was an unknown error when processing your request.');

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
        $work = Work::find($id);
        if ($work) return $work;
        else return response(MSG_UNKNOWN_ERROR, 500); 
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
        $request = $this->sanitize_split_strings($request);
        $msgs = $this->get_error_messages_array_for_post_request($request);

        if (sizeof($msgs) < 1) return Work::create($request->all());
        else return response($msgs, 400);
    }

    /**
     * Update the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $arr = array();
        $work = Work::find($id);

        $request = $this->sanitize_split_strings($request);

        if (isset($request['name'])) {
            if ($request['name'] !== $work['name']) {
                if (Work::where('name', $request['name'])->count() > 0) {
                    array_push($arr, "This name already exists in our database.");
                }
            }
        }
        if (isset($request['img_url'])) {
            if (!filter_var($request['img_url'], FILTER_VALIDATE_URL)) {
                array_push($arr, "Please use a valid URL for the image.");
            }
        }
        if (isset($request['carousel_img_urls'])) {
            $explode = explode(',', $request['carousel_img_urls']);
            foreach ($explode as $carousel_img_url) {
                if (!filter_var($carousel_img_url, FILTER_VALIDATE_URL)) {
                    array_push($arr, "Please use a valid URL for all carousel images.");
                    break;
                }
            }
        }
        if (isset($request['site_url'])) {
            if (!filter_var($request['site_url'], FILTER_VALIDATE_URL)) {
                array_push($arr, "Please use a valid URL for the website.");
            }
        }
        if (isset($request['github_url'])) {
            if (!filter_var($request['github_url'], FILTER_VALIDATE_URL)) {
                array_push($arr, "Please use a valid Github URL..");
            }
            if (!preg_match('/https:\/\/(www\.)?github\.com\/.+/', $request['github_url'])) {
                array_push($arr, "The URL you provided for Github does not point to the Github website.");
            }
        }

        if (sizeof($arr) < 1) {
            if ($work->update($request->all())) return $work;
            else return response(['messages'=>'There was an unknown error trying to process your request.'], 500);
        }
        else return response(['messages'=>$arr], 500);
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

    /**
     * Validate input object properties before creating a resource.
     *
     * @param  arr  $request
     * @return arr  $request
     */
    private function get_error_messages_array_for_request($request) {
        $arr = array();

        //Validations
        if (!isset($request['name'])) array_push($arr, "No name specified.");
        if (isset($request['name'])) {
            if (Work::where('name', $request['name'])->count() > 0) {
                array_push($arr, "This name already exists in our database.");
            }
        }
        if (!isset($request['subheader'])) array_push($arr, "No subheader specified.");
        if (!isset($request['description'])) array_push($arr, "No description specified.");
        if (isset($request['description'])) {
            if (sizeof(explode(" ", $request['description'])) < MINIMUM_DESCRIPTION_WORD_COUNT) {
                array_push($arr, "Please make your description at least 50 words long.");
            }
        }
        if (!isset($request['hashtags'])) array_push($arr, "No hashtags specified.");
        if (!isset($request['slug'])) array_push($arr, "No slug specified.");
        if (!isset($request['img_url'])) array_push($arr, "No image URL specified.");
        if (isset($request['img_url'])) {
            if (!filter_var($request['img_url'], FILTER_VALIDATE_URL)) {
                array_push($arr, "Please use a valid URL for the image.");
            }
        }
        if (!isset($request['carousel_img_urls'])) array_push($arr, "Plesae specify at least one image URL for the carousel.");
        if (isset($request['carousel_img_urls'])) {
            $explode = explode(',', $request['carousel_img_urls']);
            foreach ($explode as $carousel_img_url) {
                if (!filter_var($carousel_img_url, FILTER_VALIDATE_URL)) {
                    array_push($arr, "Please use a valid URL for all carousel images.");
                    break;
                }
            }
        }
        if (!isset($request['site_url'])) array_push($arr, "No site URL specified.");
        if (isset($request['site_url'])) {
            if (!filter_var($request['site_url'], FILTER_VALIDATE_URL)) {
                array_push($arr, "Please use a valid URL for the webite.");
            }
        }
        if (!isset($request['github_url'])) array_push($arr, "No Github URL specified.");
        if (isset($request['github_url'])) {
            if (!filter_var($request['github_url'], FILTER_VALIDATE_URL)) {
                array_push($arr, "Please use a valid Github URL..");
            }
            if (!preg_match('/https:\/\/(www\.)?github\.com\/.+/', $request['github_url'])) {
                array_push($arr, "The URL you provided for Github does not point to the Github website.");
            }
        }
        if (!isset($request['technologies'])) array_push($arr, "No technologies specified.");
        if (!isset($request['features'])) array_push($arr, "No features specified.");
        return $arr;
    }

    /**
     * Trim strings in request intended to be exploded by comma separators.
     *
     * @param  arr  $request
     * @return arr  $request
     */
    private function sanitize_split_strings($request) {
        if (isset($request['hashtags'])) {
            $explode = explode(',', $request['hashtags']);
            
            for ($i=0; $i<sizeof($explode); $i++) {
                $explode[$i] = trim($explode[$i]);
            }
            $request['hashtags'] = implode(',', $explode);
        }
        if (isset($request['carousel_img_urls'])) {
            $explode = explode(',', $request['carousel_img_urls']);
            
            for ($i=0; $i<sizeof($explode); $i++) {
                $explode[$i] = trim($explode[$i]);
            }
            $request['carousel_img_urls'] = implode(',', $explode);
        }
        if (isset($request['technologies'])) {
            $explode = explode(',', $request['technologies']);
            
            for ($i=0; $i<sizeof($explode); $i++) {
                $explode[$i] = trim($explode[$i]);
            }
            $request['technologies'] = implode(',', $explode);
        }
        if (isset($request['features'])) {
            $explode = explode(',', $request['features']);
            
            for ($i=0; $i<sizeof($explode); $i++) {
                $explode[$i] = trim($explode[$i]);
            }
            $request['features'] = implode(',', $explode);
        }
        return $request;
    }
}
