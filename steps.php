<?php
declare(strict_types=1);

// ------------------------------
// Étape 1 — Helpers
// ------------------------------
function strOrNull(?string $s): ?string {
    $s = $s !== null ? trim($s) : null;
    return $s === '' ? null : $s;
}

function intOrZero(int|string|null $v): int {
    return max(0, (int)($v ?? 0));
}

// ------------------------------
// Étape 2 & 3 — Fonction buildArticle
// ------------------------------
function buildArticle(array $row): array {
    // Defaults via ??=
    $row['title']     ??= 'Sans titre';
    $row['excerpt']   ??= null;
    $row['views']     ??= 0;
    $row['published'] ??= true;
    $row['author']    ??= 'N/A';

    // Normalisation
    return [
        'title'     => trim((string)$row['title']),
        'excerpt'   => strOrNull($row['excerpt']),
        'views'     => intOrZero($row['views']),
        'published' => (bool)$row['published'],
        'author'    => trim((string)$row['author']),
    ];
}

// ------------------------------
// Étape 4 — Tests
// ------------------------------
$testCases = [
    ['title' => 'PHP 8 en pratique', 'excerpt' => '', 'views' => 300, 'published' => true, 'author' => 'Yassine'],
    ['title' => null, 'excerpt' => null, 'views' => null, 'published' => null, 'author' => null],
    ['title' => '', 'excerpt' => '  ', 'views' => '0', 'published' => false, 'author' => ''],
];

foreach ($testCases as $i => $case) {
    echo "Test case " . ($i + 1) . ":\n";
    print_r(buildArticle($case));
    echo "\n";
}
