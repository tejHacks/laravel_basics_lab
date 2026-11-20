<?php

namespace App\Http\Controllers\Demo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Single-action (invokable) controller with an optional route parameter.
 * Route: GET /demos/hello/{name?}  ->whereAlpha('name')
 */
class HelloController extends Controller
{
    public function __invoke(Request $request, ?string $name = null): View
    {
        return view('demo.hello', [
            'name' => $name,
            'shout' => $request->boolean('shout'),   // ?shout=1
        ]);
    }
}
