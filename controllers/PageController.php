<?php

namespace App\Http\Controllers;

use Igaster\LaravelTheme\Facades\Theme;

class PageController extends Controller
{
    /**
     * Get and set default site template/theme
     *
     * @param $theme
     * @param string $lang
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function switchTheme($theme)
    {
        if (!Theme::exists($theme)) {
            return abort(404);
        }

        Theme::set($theme);

        return view('index');
    }
}
