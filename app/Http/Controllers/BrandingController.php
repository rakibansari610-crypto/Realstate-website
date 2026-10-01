<?php 


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandingController extends Controller
{
    public function edit(Organization $org)
    {
        $theme = $org->theme ?? [];
        return view('su.org.branding', compact('org','theme'));
    }

    public function update(Request $req, Organization $org)
    {
        $data = $req->validate([
            // colors / text toggles in JSON-ish fields
            'primary_color'         => 'nullable|string|max:20',
            'header_text_color'     => 'nullable|string|max:20',
            'login_bg_color'        => 'nullable|string|max:20',
            'button_color'          => 'nullable|string|max:20',
            'login_heading'         => 'nullable|string|max:190',
            'sidebar_color'         => 'nullable|string|max:20',
            'sidebar_font_color'         => 'nullable|string|max:20',
            'sidebar_font_hover'         => 'nullable|string|max:20',

            // file uploads
            'logo'                  => 'nullable|image|max:1024',        // 1MB
            'login_bg'              => 'nullable|image|max:4096',        // 4MB
            'favicon'               => 'nullable|mimes:ico,png|max:512', // 512KB
        ]);

        // Merge theme values
        $theme = $org->theme ?? [];
        $theme['colors']['primary']  = $data['primary_color']     ?? ($theme['colors']['primary']  ?? '#667eea');
        $theme['colors']['header']   = $data['header_text_color'] ?? ($theme['colors']['header']   ?? '#ffffff');
        $theme['colors']['login_bg'] = $data['login_bg_color']    ?? ($theme['colors']['login_bg'] ?? '#1e2a78');
        $theme['colors']['button']   = $data['button_color']      ?? ($theme['colors']['button']   ?? '#764ba2');
        $theme['text']['login_h1']   = $data['login_heading']     ?? ($theme['text']['login_h1']   ?? $org->name);

         $theme['colors']['sidebar_color']  = $data['sidebar_color']     ?? ($theme['colors']['sidebar_color']  ?? '#667eea');
          $theme['colors']['sidebar_font_color']  = $data['sidebar_font_color']     ?? ($theme['colors']['sidebar_font_color']  ?? '#667eea');
           $theme['colors']['sidebar_font_hover']  = $data['sidebar_font_hover']     ?? ($theme['colors']['sidebar_font_hover']  ?? '#667eea');

        // Save files (public disk)
        $base = "org/{$org->id}/branding";

        if ($req->hasFile('logo')) {
            if ($org->logo_path) Storage::disk('public')->delete($org->logo_path);
            $org->logo_path = $req->file('logo')->store($base, 'public');
        }
        if ($req->hasFile('login_bg')) {
            if ($org->login_bg_path) Storage::disk('public')->delete($org->login_bg_path);
            $org->login_bg_path = $req->file('login_bg')->store($base, 'public');
        }
        if ($req->hasFile('favicon')) {
            if ($org->favicon_path) Storage::disk('public')->delete($org->favicon_path);
            $org->favicon_path = $req->file('favicon')->store($base, 'public');
        }

        $org->theme = $theme;
        $org->save();

        return back()->with('success', 'Branding updated.');
    }
}
