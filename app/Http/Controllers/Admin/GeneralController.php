<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Support\PlanSettings;
use App\Support\SiteSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GeneralController extends Controller
{
    public function index()
    {
        return view('admin.general.index', [
            'logoUrl'       => SiteSettings::logoUrl(),
            'heroUrl'       => SiteSettings::heroImageUrl(),
            'hasCustomHero' => PlanSettings::has('hero_image_path'),
            'defaults'      => SiteSettings::DEFAULT_COLORS,
        ]);
    }

    public function updateBranding(Request $request)
    {
        $data = $request->validate([
            'site_name'   => ['required', 'string', 'min:2', 'max:40'],
            'site_footer' => ['nullable', 'string', 'max:120'],
            'logo'        => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:1024', 'dimensions:max_width=2000,max_height=2000'],
            'remove_logo' => ['nullable', 'boolean'],
        ]);

        PlanSettings::set([
            'site_name'   => trim($data['site_name']),
            'site_footer' => trim((string) ($data['site_footer'] ?? '')),
        ]);

        $this->replaceImage($request, 'logo', 'logo_path', 'remove_logo');

        ActivityLog::record('site.updated', 'An admin changed the site name, logo or footer');

        return back()->with('success', 'Name, logo and footer saved.');
    }

    public function updateColors(Request $request)
    {
        $isHex = 'regex:/^#[0-9a-fA-F]{6}$/';

        $data = $request->validate([
            'color_brand' => ['required', $isHex, function ($attribute, $value, $fail) {
                if (SiteSettings::luminance($value) > 0.5) {
                    $fail('The accent color is too light for the white text on buttons. Pick a darker one.');
                }
            }],
            'color_header' => ['required', $isHex, function ($attribute, $value, $fail) {
                if (SiteSettings::luminance($value) > 0.2) {
                    $fail('The header and footer use white text, so pick a dark color for them.');
                }
            }],
            'color_page' => ['required', $isHex, function ($attribute, $value, $fail) {
                if (SiteSettings::luminance($value) < 0.6) {
                    $fail('The page background must be light, because the text on it is dark.');
                }
            }],
        ]);

        PlanSettings::set([
            'color_brand'  => strtolower($data['color_brand']),
            'color_header' => strtolower($data['color_header']),
            'color_page'   => strtolower($data['color_page']),
        ]);

        ActivityLog::record('site.updated', 'An admin changed the site colors');

        return back()->with('success', 'Colors saved.');
    }

    public function resetColors()
    {
        PlanSettings::forget(['color_brand', 'color_header', 'color_page']);

        ActivityLog::record('site.updated', 'An admin reset the site colors to the defaults');

        return back()->with('success', 'Colors are back to the defaults.');
    }

    public function updateHero(Request $request)
    {
        $data = $request->validate([
            'hero_eyebrow'     => ['nullable', 'string', 'max:60'],
            'hero_title'       => ['required', 'string', 'min:5', 'max:120'],
            'hero_desc'        => ['nullable', 'string', 'max:300'],
            'hero_image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072', 'dimensions:max_width=5000,max_height=5000'],
            'reset_hero_image' => ['nullable', 'boolean'],
        ]);

        PlanSettings::set([
            'hero_eyebrow' => trim((string) ($data['hero_eyebrow'] ?? '')),
            'hero_title'   => trim($data['hero_title']),
            'hero_desc'    => trim((string) ($data['hero_desc'] ?? '')),
        ]);

        $this->replaceImage($request, 'hero_image', 'hero_image_path', 'reset_hero_image');

        ActivityLog::record('site.updated', 'An admin changed the home page hero');

        return back()->with('success', 'Home page hero saved.');
    }

    /** Save a new upload (deleting the old file), or remove the saved one. */
    private function replaceImage(Request $request, string $field, string $settingKey, string $removeField): void
    {
        $old = PlanSettings::get($settingKey);

        if ($request->hasFile($field)) {
            $new = $request->file($field)->store('site', 'public');

            PlanSettings::set([$settingKey => $new]);

            if ($old) {
                Storage::disk('public')->delete($old);
            }
        } elseif ($request->boolean($removeField) && $old) {
            PlanSettings::forget([$settingKey]);

            Storage::disk('public')->delete($old);
        }
    }
}