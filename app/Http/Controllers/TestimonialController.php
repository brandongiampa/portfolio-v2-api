<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Testimonial;

define('MINIMUM_TESTIMONIAL_LENGTH', 20);

class TestimonialController extends Controller
{
    /**
     * Display a listing of the resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return Testimonial::all();
    }

    /**
     * Create new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $error_msgs = $this->get_error_messages_array_for_post_request($request);

        if (sizeof($error_msgs) < 1) {
            try {
                mail("me@brandongiampa.com", "New Testimonial!", "Check your phpmyadmin NOW!");
                $request['notification_email_sent'] = true;
            }
            finally {
                return Testimonial::create($request->all());
            }
        }
        else {
            return response($error_msgs, 400);
        }
    }

    /**
     * Update the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        if (isset($request['company_url'])) {
            if (!filter_var($request['company_url'], FILTER_VALIDATE_URL)) {
                return response("Please specify a valid URL for the company website.", 400);
            }
        }
        $technology = Testimonial::find($id);
        $technology->update($request->all());
        return $technology;
    }

    /**
     * Delete the specified resource.
     *
     * @param  int  $id
     * @return str  $str
     */
    public function destroy($id) {
        $testimonial = Testimonial::destroy($id);
        $str = $testimonial > 0 ? "Testimonial at id #".$id." has been deleted." : "Error deleting testimonial at id #".$id.".";
        return ['messages'=>$str];
    }

    /**
     * Validate input object properties before creating a resource.
     *
     * @param  obj  $request
     * @return arr  $arr
     */
    private function get_error_messages_array_for_post_request($request) {
        $arr = array();

        if (!isset($request['author'])) array_push($arr, "No author specified.");
        if (!isset($request['company'])) array_push($arr, "No company specified.");
        if (isset($request['company_url'])) {
            if (!filter_var($request['company_url'], FILTER_VALIDATE_URL)) {
                array_push($arr, "Please use a valid URL for the company website.");
            }
        }
        if (!isset($request['text'])) array_push($arr, "No testimonial text.");
        if (isset($request['text'])) {
            if (strlen($request['text']) < MINIMUM_TESTIMONIAL_LENGTH) {
                array_push($arr, "Testimonial text is below the length of ".MINIMUM_TESTIMONIAL_LENGTH." characters.");
            }
        }
        return $arr;
    }
}
