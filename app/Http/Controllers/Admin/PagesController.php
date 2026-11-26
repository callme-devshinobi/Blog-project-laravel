<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Support\PlanSettings;
use App\Support\SiteContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PagesController extends Controller
{
    /** A link may be a full web address, a path on this site ("/contact") or an email link. Nothing else. */
    private const LINK = 'regex:/^(https?:\/\/|\/|mailto:)[^\s<>"\']*$/i';

    // ------------------------------------------------------------------ About

    public function about()
    {
        return view('admin.pages.about', [
            'c'           => SiteContent::about(),
            'portraitUrl' => SiteContent::portraitUrl(),
        ]);
    }

    public function saveAbout(Request $request)
    {
        $data = $request->validate([
            'eyebrow'             => ['nullable', 'string', 'max:60'],
            'title'               => ['required', 'string', 'max:140'],
            'intro'               => ['nullable', 'string', 'max:500'],
            'author_heading'      => ['nullable', 'string', 'max:100'],
            'lead'                => ['nullable', 'string', 'max:400'],
            'body'                => ['nullable', 'string', 'max:3000'],
            'chips'               => ['nullable', 'array', 'max:12'],
            'chips.*.v'           => ['nullable', 'string', 'max:30'],
            'portrait_name'       => ['nullable', 'string', 'max:60'],
            'portrait_role'       => ['nullable', 'string', 'max:60'],
            'portrait'            => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:max_width=4000,max_height=4000'],
            'remove_portrait'     => ['nullable', 'boolean'],
            'stats'               => ['nullable', 'array', 'max:4'],
            'stats.*.value'       => ['nullable', 'string', 'max:20'],
            'stats.*.label'       => ['nullable', 'string', 'max:40'],
            'find_heading'        => ['nullable', 'string', 'max:80'],
            'find'                => ['nullable', 'array', 'max:6'],
            'find.*.tag'          => ['nullable', 'string', 'max:24'],
            'find.*.title'        => ['nullable', 'string', 'max:80'],
            'find.*.text'         => ['nullable', 'string', 'max:300'],
            'why_heading'         => ['nullable', 'string', 'max:80'],
            'why_body'            => ['nullable', 'string', 'max:2000'],
            'cta_title'           => ['nullable', 'string', 'max:100'],
            'cta_text'            => ['nullable', 'string', 'max:300'],
            'cta_primary_label'   => ['nullable', 'string', 'max:40'],
            'cta_primary_url'     => ['nullable', 'string', 'max:200', self::LINK],
            'cta_secondary_label' => ['nullable', 'string', 'max:40'],
            'cta_secondary_url'   => ['nullable', 'string', 'max:200', self::LINK],
        ]);

        $t = fn (string $key) => trim((string) ($data[$key] ?? ''));

        $content = [
            'eyebrow'             => $t('eyebrow'),
            'title'               => $t('title'),
            'intro'               => $t('intro'),
            'author_heading'      => $t('author_heading'),
            'lead'                => $t('lead'),
            'body'                => $t('body'),
            'chips'               => array_column($this->rows($data['chips'] ?? [], ['v'], 12), 'v'),
            'portrait_name'       => $t('portrait_name'),
            'portrait_role'       => $t('portrait_role'),
            'stats'               => $this->rows($data['stats'] ?? [], ['value', 'label'], 4),
            'find_heading'        => $t('find_heading'),
            'find'                => $this->rows($data['find'] ?? [], ['tag', 'title', 'text'], 6),
            'why_heading'         => $t('why_heading'),
            'why_body'            => $t('why_body'),
            'cta_title'           => $t('cta_title'),
            'cta_text'            => $t('cta_text'),
            'cta_primary_label'   => $t('cta_primary_label'),
            'cta_primary_url'     => $t('cta_primary_url'),
            'cta_secondary_label' => $t('cta_secondary_label'),
            'cta_secondary_url'   => $t('cta_secondary_url'),
        ];

        PlanSettings::set(['page_about' => json_encode($content, JSON_UNESCAPED_UNICODE)]);

        // the portrait photo
        $old = PlanSettings::get('about_portrait_path');

        if ($request->hasFile('portrait')) {
            $new = $request->file('portrait')->store('site', 'public');
            PlanSettings::set(['about_portrait_path' => $new]);

            if ($old) {
                Storage::disk('public')->delete($old);
            }
        } elseif ($request->boolean('remove_portrait') && $old) {
            PlanSettings::forget(['about_portrait_path']);
            Storage::disk('public')->delete($old);
        }

        ActivityLog::record('pages.updated', 'An admin edited the About page');

        return back()->with('success', 'About page saved.');
    }

    // ---------------------------------------------------------------- Contact

    public function contact()
    {
        return view('admin.pages.contact', ['c' => SiteContent::contact()]);
    }

    public function saveContact(Request $request)
    {
        $data = $request->validate([
            'eyebrow'          => ['nullable', 'string', 'max:60'],
            'title'            => ['required', 'string', 'max:100'],
            'intro'            => ['nullable', 'string', 'max:500'],
            'emails'           => ['nullable', 'array', 'max:5'],
            'emails.*.title'   => ['nullable', 'string', 'max:60'],
            'emails.*.text'    => ['nullable', 'string', 'max:300'],
            'emails.*.address' => ['nullable', 'email', 'max:120'],
            'response_title'   => ['nullable', 'string', 'max:60'],
            'response_text'    => ['nullable', 'string', 'max:300'],
            'social_heading'   => ['nullable', 'string', 'max:60'],
            'social_text'      => ['nullable', 'string', 'max:300'],
            'social'           => ['nullable', 'array', 'max:8'],
            'social.*.label'   => ['nullable', 'string', 'max:30'],
            'social.*.url'     => ['nullable', 'string', 'max:200', self::LINK],
            'form_enabled'     => ['required', 'in:0,1'],
            'form_heading'     => ['nullable', 'string', 'max:60'],
            'form_note'        => ['nullable', 'string', 'max:160'],
        ]);

        $t = fn (string $key) => trim((string) ($data[$key] ?? ''));

        $content = [
            'eyebrow'        => $t('eyebrow'),
            'title'          => $t('title'),
            'intro'          => $t('intro'),
            'emails'         => $this->rows($data['emails'] ?? [], ['title', 'text', 'address'], 5),
            'response_title' => $t('response_title'),
            'response_text'  => $t('response_text'),
            'social_heading' => $t('social_heading'),
            'social_text'    => $t('social_text'),
            'social'         => $this->rows($data['social'] ?? [], ['label', 'url'], 8),
            'form_enabled'   => $data['form_enabled'] === '1',
            'form_heading'   => $t('form_heading'),
            'form_note'      => $t('form_note'),
        ];

        PlanSettings::set(['page_contact' => json_encode($content, JSON_UNESCAPED_UNICODE)]);

        ActivityLog::record('pages.updated', 'An admin edited the Contact page');

        return back()->with('success', 'Contact page saved.');
    }

    // -------------------------------------------------------------------- FAQ

    public function faq()
    {
        return view('admin.pages.faq', ['c' => SiteContent::faq()]);
    }

    public function saveFaq(Request $request)
    {
        $data = $request->validate([
            'title'      => ['nullable', 'string', 'max:100'],
            'subtitle'   => ['nullable', 'string', 'max:200'],
            'items'      => ['nullable', 'array', 'max:20'],
            'items.*.q'  => ['nullable', 'string', 'max:160'],
            'items.*.a'  => ['nullable', 'string', 'max:1000'],
        ]);

        $content = [
            'title'    => trim((string) ($data['title'] ?? '')),
            'subtitle' => trim((string) ($data['subtitle'] ?? '')),
            'items'    => $this->rows($data['items'] ?? [], ['q', 'a'], 20),
        ];

        PlanSettings::set(['page_faq' => json_encode($content, JSON_UNESCAPED_UNICODE)]);

        ActivityLog::record('pages.updated', 'An admin edited the FAQ');

        return back()->with('success', 'FAQ saved.');
    }

    // ------------------------------------------------------------------ Reset

    public function reset(string $page)
    {
        PlanSettings::forget(["page_{$page}"]);

        ActivityLog::record('pages.updated', "An admin put the {$page} text back to the original");

        return back()->with('success', 'The original text is back.');
    }

    /** Trim every cell, drop empty rows, and keep the order the admin chose. */
    private function rows(?array $input, array $keys, int $max): array
    {
        return collect($input ?? [])
            ->map(fn ($row) => collect($keys)->mapWithKeys(fn ($k) => [$k => trim((string) ($row[$k] ?? ''))])->all())
            ->filter(fn ($row) => collect($row)->filter()->isNotEmpty())
            ->take($max)
            ->values()
            ->all();
    }
}