<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Seed\ArticleFactory;

$options = getopt('', ['count::', 'out::', 'topic::']);
$count = isset($options['count']) ? (int)$options['count'] : 10;
$out   = $options['out'] ?? 'storage/articles.seed.json';
$topic = $options['topic'] ?? null;

$factory  = new ArticleFactory();
$articles = $factory->make($count, $topic);

// Créer le dossier si nécessaire
$dir = dirname($out);
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

$json = json_encode($articles, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
file_put_contents($out, $json);

echo "✅ Seed généré : $out (" . count($articles) . " articles)\n";
