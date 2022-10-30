<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subheader',
        'description',
        'hashtags',
        'slug',
        'img_url',
        'carousel_img_urls',
        'site_url',
        'github_url',
        'features',
        'technologies'
    ];
}
