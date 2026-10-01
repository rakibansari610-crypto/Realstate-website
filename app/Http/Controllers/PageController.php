<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;

class PageController extends Controller
{
    public function index()
    {
        $page = Page::where('type', 'privacy_policy')->first();

        return view('org.privacy-policy.index', compact('page'));
    }

    public function edit()
    {
        $page = Page::where('type', 'privacy_policy')->first();

        return view('org.privacy-policy.form', compact('page'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'description' => 'required'
        ]);

        Page::updateOrCreate(
            ['type' => 'privacy_policy'],
            ['description' => $request->description]
        );

        return redirect()->route('privacy.index')
                         ->with('success', 'Privacy Policy Updated Successfully!');
    }


    public function termsIndex()
    {
        $page = Page::where('type', 'terms_conditions')->first();

        return view('org.terms-conditions.index', compact('page'));
    }

    public function termsEdit()
    {
        $page = Page::where('type', 'terms_conditions')->first();

        return view('org.terms-conditions.form', compact('page'));
    }

    public function termsUpdate(Request $request)
    {
        $request->validate([
            'description' => 'required'
        ]);

        Page::updateOrCreate(
            ['type' => 'terms_conditions'],
            ['description' => $request->description]
        );

        return redirect()->route('terms.conditions.index')
                         ->with('success', 'Terms & Conditions Updated Successfully!');
    }
}
