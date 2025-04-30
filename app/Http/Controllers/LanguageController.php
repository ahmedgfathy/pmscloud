<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switch($locale)
    {
        if (!in_array($locale, ['en', 'ar'])) {
            return back();
        }

        app()->setLocale($locale);
        session()->put('locale', $locale);
        session()->save(); // Force save the session

        return redirect()->back();
    }
}
