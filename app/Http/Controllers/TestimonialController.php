<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Testimonial;

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
        $request->validate([
            'author' => 'Required|Unique:testimonials,author',
            'company' => 'Required',
            'company_url' => 'URL',
            'text' => 'Required'
        ]);

        return Testimonial::create($request->all());
    }

    /**
     * Update the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $request->validate([
            'company_url' => 'URL'
        ]);
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
        return $str;
    }
}
