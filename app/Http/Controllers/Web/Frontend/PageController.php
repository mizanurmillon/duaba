<?php

namespace App\Http\Controllers\Web\Frontend;

use App\Http\Controllers\Controller;
use App\Models\DynamicPage;
use App\Models\Faq;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function termsAndConditions()
    {
        $dynamicPage = DynamicPage::query()
            ->where('status', 'active')
            ->where('id', 1)
            ->firstOrFail();
        return view('frontend.layouts.pages.singleDynamicPage', compact('dynamicPage'));
    }

    public function legal()
    {
        $dynamicPage = DynamicPage::query()
            ->where('status', 'active')
            ->where('id', 2)
            ->firstOrFail();
        return view('frontend.layouts.pages.singleDynamicPage', compact('dynamicPage'));
    }

    public function help()
    {
        $dynamicPage = DynamicPage::query()
            ->where('status', 'active')
            ->where('id', 3)
            ->firstOrFail();
        return view('frontend.layouts.pages.singleDynamicPage', compact('dynamicPage'));
    }
}
