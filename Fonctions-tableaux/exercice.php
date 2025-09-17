<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Dataset
|--------------------------------------------------------------------------
*/
$articles = [
    ['id'=>1,'title'=>'Intro Laravel','category'=>'php','views'=>120,'author'=>'Amina','published'=>true,  'tags'=>['php','laravel']],
    ['id'=>2,'title'=>'PHP 8 en pratique','category'=>'php','views'=>300,'author'=>'Yassine','published'=>true,  'tags'=>['php']],
    ['id'=>3,'title'=>'Composer & Autoload','category'=>'outils','views'=>90,'author'=>'Amina','published'=>false, 'tags'=>['composer','php']],
    ['id'=>4,'title'=>'Validation FormRequest','category'=>'laravel','views'=>210,'author'=>'Sara','published'=>true,  'tags'=>['laravel','validation']],
];

/*
|--------------------------------------------------------------------------
| Utility: slugify
|--------------------------------------------------------------------------
*/
function slugify(string $title): string {
    $slug = strtolower($title);
    $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
    return trim($slug, '-');
}

/*
|--------------------------------------------------------------------------
| Step 1 — Keep only published articles
|--------------------------------------------------------------------------
*/
$published = array_values(array_filter($articles, fn($a) => $a['published'] ?? false));

/*
|--------------------------------------------------------------------------
| Step 2 — Normalize: id/slug/views/author/category
|--------------------------------------------------------------------------
*/
$normalized = array_map(
    fn($a) => [
        'id'       => $a['id'],
        'slug'     => slugify($a['title']),
        'views'    => $a['views'],
        'author'   => $a['author'],
        'category' => $a['category'],
    ],
    $published
);

/*
|--------------------------------------------------------------------------
| Step 3 — Sort by views descending
|--------------------------------------------------------------------------
*/
usort($normalized, fn($a, $b) => $b['views'] <=> $a['views']);

/*
|--------------------------------------------------------------------------
| Step 4 — Generate summary
|--------------------------------------------------------------------------
*/
$summary = [
    'count' => count($normalized),
    'views_sum' => array_sum(array_column($normalized, 'views')),
    'by_category' => array_reduce(
        $normalized,
        function(array $acc, $a) {
            $cat = $a['category'];
            $acc[$cat] = ($acc[$cat] ?? 0) + 1;
            return $acc;
        },
        []
    ),
];

/*
|--------------------------------------------------------------------------
| Step 5 — Show result
|--------------------------------------------------------------------------
*/
echo "Normalized Articles:\n";
print_r($normalized);

echo "\nSummary:\n";
print_r($summary);
