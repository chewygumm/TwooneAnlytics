<?php

namespace Config;

use CodeIgniter\Config\Filters as BaseFilters;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseFilters
{
    /**
     * Aliases for Filters.
     *
     * @var array<string, class-string|list<class-string>>
     */
    public array $aliases = [

        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,

        // FILTER LOGIN
        'auth'          => \App\Filters\AuthFilter::class,

    ];

    /**
     * Required Filters
     *
     * @var array<string, array<string, list<string>>>
     */
    public array $required = [
        'before' => [
            'forcehttps',
            'pagecache',
        ],
        'after' => [
            'pagecache',
            'performance',
            'toolbar',
        ],
    ];

    /**
     * Global Filters
     *
     * @var array<string, array<string, list<string>>>
     */
    public array $globals = [
        'before' => [
            // 'honeypot',
            // 'csrf',
            // 'invalidchars',
        ],
        'after' => [
            // 'honeypot',
            // 'secureheaders',
        ],
    ];

    /**
     * Method Filters
     *
     * @var array<string, list<string>>
     */
    public array $methods = [];

    /**
     * Filter berdasarkan URI
     *
     * @var array<string, array<string, list<string>>>
     */
    public array $filters = [

        'auth' => [

            'before' => [

                'dashboard',
                'dashboard/*',

                'transaksi',
                'transaksi/*',

                'kuesioner',
                'kuesioner/*',

                'kategori-produk',
                'kategori-produk/*',

                'preparation',
                'preparation/*',

                'clustering',
                'clustering/*',

                'interpretasi',
                'interpretasi/*',

                'laporan',
                'laporan/*',

            ],

        ],

    ];
}