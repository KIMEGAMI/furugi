<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

class SeoController extends Controller
{
    private const PRIVATE_PATHS = [
        '/admin',
        '/auction-items',
        '/category-sales',
        '/dashboard',
        '/profile',
        '/sales',
    ];

    public function sitemap(): Response
    {
        $lastModified = config('seo.updated_at', now()->toDateString());
        $urls = [];

        foreach (config('seo.pages', []) as $routeName => $page) {
            if (! Route::has($routeName)) {
                continue;
            }

            $urls[] = [
                'loc' => route($routeName),
                'lastmod' => $lastModified,
                'changefreq' => $page['changefreq'] ?? 'monthly',
                'priority' => $page['priority'] ?? '0.5',
                'image' => $routeName === 'home'
                    ? asset(ltrim(config('seo.image', '/images/furugi-manager-hero.png'), '/'))
                    : null,
                'image_title' => $routeName === 'home'
                    ? config('seo.site_name', 'FURUPRO')
                    : null,
            ];
        }

        $xml = view('seo.sitemap', ['urls' => $urls])->render();

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }

    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
        ];

        foreach (self::PRIVATE_PATHS as $path) {
            $lines[] = 'Disallow: '.$path;
        }

        $lines[] = 'Sitemap: '.route('seo.sitemap');

        return response(implode("\n", $lines)."\n", 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
}
