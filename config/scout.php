<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Search Engine
    |--------------------------------------------------------------------------
    |
    | Scout supports multiple search drivers: "database", "meilisearch",
    | "typesense", "algolia". For Lab 7, "database" requires no external
    | service and uses MySQL FULLTEXT indexes.
    |
    */

    'driver' => env('SCOUT_DRIVER', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Index Prefix
    |--------------------------------------------------------------------------
    |
    | A prefix is applied to all Scout index names. Use a unique prefix
    | per environment to avoid collisions when sharing a search service.
    |
    */

    'prefix' => env('SCOUT_PREFIX', ''),

    /*
    |--------------------------------------------------------------------------
    | Queue Data Syncing
    |--------------------------------------------------------------------------
    |
    | Syncing model data to the search backend. When enabled, Scout will
    | dispatch jobs to the queue to sync model changes asynchronously.
    |
    */

    'queue' => [
        'connection' => env('SCOUT_QUEUE_CONNECTION', env('QUEUE_CONNECTION', 'database')),
        'queue' => env('SCOUT_QUEUE', 'scout'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Driver
    |--------------------------------------------------------------------------
    |
    | The database driver uses MySQL FULLTEXT indexes. Ensure the
    | idx_books_fulltext index exists on the books table before searching.
    |
    */

    'database' => [
        'engine' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Meilisearch Configuration
    |--------------------------------------------------------------------------
    |
    | When using Meilisearch, define the host and API key below.
    |
    */

    'meilisearch' => [
        'host' => env('MEILISEARCH_HOST', 'http://localhost:7700'),
        'key' => env('MEILISEARCH_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Algolia Configuration
    |--------------------------------------------------------------------------
    |
    | When using Algolia, define the Application ID and Admin API key.
    |
    */

    'algolia' => [
        'id' => env('ALGOLIA_APP_ID', ''),
        'secret' => env('ALGOLIA_SECRET', ''),
    ],

];
