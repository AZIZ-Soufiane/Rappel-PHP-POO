<?php
declare(strict_types=1);

// ------------------------------
// Exemple de données
// ------------------------------
$articles = [
    ['id'=>1,'title'=>'Intro Laravel','views'=>120,'author'=>'Amina','category'=>'php','tags'=>['php','laravel'],'published'=>true],
    ['id'=>2,'title'=>'Symfony Basics','views'=>80,'author'=>'Omar','category'=>'php','tags'=>['php','symfony'],'published'=>true],
    ['id'=>3,'title'=>'Node.js Guide','views'=>200,'author'=>'Sara','category'=>'javascript','tags'=>['js','node'],'published'=>false],
    ['id'=>4,'title'=>'React 101','views'=>150,'author'=>'Amina','category'=>'javascript','tags'=>['js','react'],'published'=>true],
    ['id'=>5,'title'=>'Advanced PHP','views'=>60,'author'=>'Omar','category'=>'php','tags'=>['php'],'published'=>true],
];

// ------------------------------
// Étape 1 — Utilitaire slugify
// ------------------------------
function slugify(string $title): string {
    $slug = strtolower($title);
    $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
    return trim($slug, '-');
}

// ------------------------------
// Étape 2 — Filtrer les articles publiés
// ------------------------------
$published = array_values(
    array_filter($articles, fn(array $a) => $a['published'] ?? false)
);

// ------------------------------
// Étape 3 — Mapper vers un format léger (id, title, slug, views)
// ------------------------------
$light = array_map(
    fn(array $a) => [
        'id'    => $a['id'],
        'title' => $a['title'],
        'slug'  => slugify($a['title']),
        'views' => $a['views'],
    ],
    $published
);

// ------------------------------
// Étape 4 — Top 3 par vues
// ------------------------------
$top = $light;
usort($top, fn($a, $b) => $b['views'] <=> $a['views']);
$top3 = array_slice($top, 0, 3);

// ------------------------------
// Étape 5 — Agréger : nombre d’articles par auteur
// ------------------------------
$byAuthor = array_reduce(
    $published,
    function(array $acc, array $a): array {
        $author = $a['author'];
        $acc[$author] = ($acc[$author] ?? 0) + 1;
        return $acc;
    },
    []
);

// ------------------------------
// Étape 6 — Fréquence des tags (flatten + reduce)
// ------------------------------
$allTags = array_merge(...array_map(fn($a) => $a['tags'], $published));

$tagFreq = array_reduce(
    $allTags,
    function(array $acc, string $tag): array {
        $acc[$tag] = ($acc[$tag] ?? 0) + 1;
        return $acc;
    },
    []
);

// ------------------------------
// Étape 7 — Afficher un mini-rapport
// ------------------------------
echo "Top 3 (views):\n";
foreach ($top3 as $a) {
    echo "- {$a['title']} ({$a['views']} vues) — {$a['slug']}\n";
}

echo "\nPar auteur:\n";
foreach ($byAuthor as $author => $count) {
    echo "- $author: $count article(s)\n";
}

echo "\nTags:\n";
foreach ($tagFreq as $tag => $count) {
    echo "- $tag: $count\n";
}
