<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

class SeoController extends Controller
{
    private const INDEXABLE_ROUTES = [
        'home' => ['weekly', '1.0'],
        'marketing.features' => ['monthly', '0.9'],
        'marketing.use-cases' => ['monthly', '0.9'],
        'marketing.pricing' => ['monthly', '0.8'],
        'register' => ['monthly', '0.7'],
        'legal.faq' => ['monthly', '0.8'],
        'legal.terms' => ['yearly', '0.5'],
        'legal.privacy' => ['yearly', '0.5'],
        'legal.commercial' => ['yearly', '0.5'],
        'legal.contact' => ['yearly', '0.5'],
    ];

    private const PRIVATE_PATHS = [
        '/admin',
        '/auction-items',
        '/category-sales',
        '/dashboard',
        '/premium',
        '/profile',
        '/sales',
    ];

    public function sitemap(): Response
    {
        $lastModified = now()->toDateString();
        $urls = [];

        foreach (self::INDEXABLE_ROUTES as $routeName => [$changeFrequency, $priority]) {
            if (! Route::has($routeName)) {
                continue;
            }

            $urls[] = [
                'loc' => route($routeName),
                'lastmod' => $lastModified,
                'changefreq' => $changeFrequency,
                'priority' => $priority,
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
