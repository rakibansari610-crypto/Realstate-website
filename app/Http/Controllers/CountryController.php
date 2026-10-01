<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;


class CountryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $countries = Country::when($request->filled('q'), function ($query) use ($request) {
                    $query->where('country_name', 'like', '%' . $request->q . '%');
                })
                ->orderBy('country_priority', 'asc')
                ->paginate(20)
                ->withQueryString();

            return view('org.country.index', compact('countries'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Unable to fetch countries.');
        }
    }

    public function create()
    {
        try {
            return view('org.country.create');
        } catch (\Exception $e) {
            return redirect()->route('org.country.index')
                ->with('error', 'Something went wrong.');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'country_name' => 'required|string|max:255',
            ]);

            Country::create([
                'country_name'     => $request->country_name,
                'country_currency' => $request->country_currency,
                'country_lang'     => $request->country_lang,
                'country_code'     => $request->country_code,
                'country_iso'      => $request->country_iso,
                'country_isd'      => $request->country_isd,
                'country_priority' => $request->country_priority ?? 0,
                'country_status'   => $request->active ?? 0,
                'country_default'  => $request->country_default ?? 0,
            ]);

            return redirect()
                ->route('org.country.index')
                ->with('success', 'Country created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create country.');
        }
    }

    public function edit(Request $request)
    {
        try {
            $id = decrypt($request->id);
            $country = Country::findOrFail($id);
            return view('org.country.edit', compact('country'));
        } catch (\Exception $e) {
            return redirect()
                ->route('org.country.index')
                ->with('error', 'Country not found.');
        }
    }

public function update(Request $request)
{
    try {
        $id = decrypt($request->id);

        $request->validate([
            'country_name' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request, $id) {

            // If this country is set as default, reset others
            if ($request->country_default == 1) {
                Country::where('id', '!=', $id)
                    ->update(['country_default' => 0]);
            }

            $country = Country::findOrFail($id);

            $country->update([
                'country_name'     => $request->country_name,
                'country_currency' => $request->country_currency,
                'country_lang'     => $request->country_lang,
                'country_code'     => $request->country_code,
                'country_iso'      => $request->country_iso,
                'country_isd'      => $request->country_isd,
                'country_priority' => $request->country_priority ?? 0,
                'country_status'   => $request->active ?? 0,
                'country_default'  => $request->country_default ?? 0,
            ]);
        });

        return redirect()
            ->route('org.country.index')
            ->with('success', 'Country updated successfully.');

    } catch (\Exception $e) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed to update country.');
    }
}


    public function destroy(Request $request)
    {
        try {
            $id = decrypt($request->id);
            $country = Country::findOrFail($id);
            $country->delete();

            return redirect()
                ->route('org.country.index')
                ->with('success', 'Country deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('org.country.index')
                ->with('error', 'Failed to delete country.');
        }
    }
}
