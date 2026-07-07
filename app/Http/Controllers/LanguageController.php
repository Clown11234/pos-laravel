<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switchLanguage($locale)
    {
        // ခွင့်ပြုထားတဲ့ ဘာသာစကား ဟုတ်မဟုတ် စစ်ဆေး
        if (in_array($locale, ['en', 'mm'])) {
            session()->put('locale', $locale); // Session ထဲသို့ တန်ဖိုးထည့်ခြင်း
        }

        return redirect()->back();
    }
}
