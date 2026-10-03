<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $properties = Property::query()->live()->select('slug', 'updated_at')->get();

        return response()
            ->view('sitemap.index', compact('properties'))
            ->header('Content-Type', 'text/xml');
    }
}
