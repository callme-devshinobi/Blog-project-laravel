<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class SiteSettings
{
    public const DEFAULT_COLORS = [
        'brand'  => '#6c63ff',
        'header' => '#0c0c16',
        'page'   => '#f6f7fb',
    ];

    /** Copy the saved values into the config, so views can use config('devnotes.site.*'). */
    public static function apply(): void
    {
        $site = config('devnotes.site', []);

        $text = [
            'name'         => 'site_name',
            'footer'       => 'site_footer',
            'hero_eyebrow' => 'hero_eyebrow',
            'hero_title'   => 'hero_title',
            'hero_desc'    => 'hero_desc',
        ];

        foreach ($text as $configKey => $settingKey) {
            if (PlanSettings::has($settingKey)) {
                $site[$configKey] = PlanSettings::get($settingKey);
            }
        }

        $site['logo_path']       = PlanSettings::get('logo_path');
        $site['hero_image_path'] = PlanSettings::get('hero_image_path');

        foreach (['brand' => 'color_brand', 'header' => 'color_header', 'page' => 'color_page'] as $name => $settingKey) {
            if ($value = PlanSettings::get($settingKey)) {
                $site['colors'][$name] = $value;
            }
        }

        config(['devnotes.site' => $site]);
    }

    public static function logoUrl(): ?string
    {
        $path = config('devnotes.site.logo_path');

        return $path ? Storage::disk('public')->url($path) : null;
    }

    public static function heroImageUrl(): string
    {
        $path = config('devnotes.site.hero_image_path');

        return $path
            ? Storage::disk('public')->url($path)
            : (string) config('devnotes.site.hero_image');
    }

    // ---------- colours ----------

    /** Only a plain #rrggbb value is ever accepted, so nothing else can reach the page's CSS. */
    public static function hex(?string $value, string $fallback): string
    {
        return is_string($value) && preg_match('/^#[0-9a-fA-F]{6}$/', $value)
            ? strtolower($value)
            : $fallback;
    }

    public static function rgb(string $hex): array
    {
        return [hexdec(substr($hex, 1, 2)), hexdec(substr($hex, 3, 2)), hexdec(substr($hex, 5, 2))];
    }

    /** 0 (black) to 1 (white). */
    public static function luminance(string $hex): float
    {
        [$r, $g, $b] = array_map(function ($v) {
            $v /= 255;

            return $v <= 0.03928 ? $v / 12.92 : (($v + 0.055) / 1.055) ** 2.4;
        }, static::rgb($hex));

        return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    }

    /** Make a colour darker (negative amount) or lighter (positive), from -1 to 1. */
    public static function shade(string $hex, float $amount): string
    {
        [$r, $g, $b] = static::rgb($hex);

        $f = fn ($v) => (int) round($amount < 0 ? $v * (1 + $amount) : $v + (255 - $v) * $amount);

        return sprintf('#%02x%02x%02x', $f($r), $f($g), $f($b));
    }

    /** The <style> block that applies the chosen colours. Built only from validated values. */
    public static function css(): string
    {
        $c = config('devnotes.site.colors', []);

        $brand  = static::hex($c['brand']  ?? null, self::DEFAULT_COLORS['brand']);
        $header = static::hex($c['header'] ?? null, self::DEFAULT_COLORS['header']);
        $page   = static::hex($c['page']   ?? null, self::DEFAULT_COLORS['page']);

        [$r, $g, $b] = static::rgb($brand);

        $brandDark = static::shade($brand, -0.12);
        $header2   = static::shade($header, 0.06);

        return '<style id="site-colors">'
            . ":root{--indigo:{$brand};--indigo-dark:{$brandDark};--bg-dark:{$header};--bg-dark-2:{$header2};--indigo-soft:rgba({$r},{$g},{$b},.12)}"
            . ':root:not([data-theme="dark"]){--bg-light:' . $page . '}'
            . "[data-theme=\"dark\"]{--indigo-soft:rgba({$r},{$g},{$b},.2)}"
            . '</style>';
    }
}