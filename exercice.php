<?php
declare(strict_types=1);

// Exemple de données
$articles = [
    ['id'=>1,'title'=>'Intro Laravel','views'=>120,'author'=>'Amina','category'=>'php','tags'=>['php','laravel'],'published'=>true],
    ['id'=>2,'title'=>'Symfony Basics','views'=>80,'author'=>'Omar','category'=>'php','tags'=>['php','symfony'],'published'=>true],
    ['id'=>3,'title'=>'Node.js Guide','views'=>200,'author'=>'Sara','category'=>'javascript','tags'=>['js','node'],'published'=>false],
    ['id'=>4,'title'=>'React 101','views'=>150,'author'=>'Amina','category'=>'javascript','tags'=>['js','react'],'published'=>true],
    ['id'=>5,'title'=>'Advanced PHP','views'=>60,'author'=>'Omar','category'=>'php','tags'=>['php'],'published'=>true],
];

// ------------------------------
// Étape 0 — Utilitaire slugify
// ------------------------------
function slugify(string $title): string {
    $slug = strtolower($title);
    $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
    return trim($slug, '-');
}

// ------------------------------
// Étape 1 — Filtrer les articles publiés
// ------------------------------
$published = array_filter($articles, fn($a) => $a['published'] ?? false);

// ------------------------------
// Étape 2 — Normaliser les articles
// ------------------------------
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

// ------------------------------
// Étape 3 — Trier par vues décroissantes
// ------------------------------
usort($normalized, fn($a, $b) => $b['views'] <=> $a['views']);

// ------------------------------
// Étape 4 — Calculer le résumé
// ------------------------------
$summary = [
    'count' => count($normalized),
    'views_sum' => array_sum(array_column($normalized, 'views')),
    'by_category' => array_reduce(
        $normalized,
        function($acc, $a) {
            $cat = $a['category'];
            $acc[$cat] = ($acc[$cat] ?? 0) + 1;
            return $acc;
        },
        []
    ),
];

// ------------------------------
// Étape 5 — Afficher le résultat
// ------------------------------
echo "Articles normalisés:\n";
print_r($normalized);

echo "\nRésumé:\n";
print_r($summary);
