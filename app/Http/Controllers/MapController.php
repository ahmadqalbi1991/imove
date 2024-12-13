<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MapController extends Controller
{
    //
    public function showMap()
{
    $from = 'New York';
    $to = 'Los Angeles';
    
    return view('admin.map.index', compact('from', 'to'));
}
}
