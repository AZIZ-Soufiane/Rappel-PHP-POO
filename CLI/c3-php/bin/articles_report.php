#!/usr/bin/env php
<?php
declare(strict_types=1);

/**
 * Usage:
 *   php bin/articles_report.php --input=storage/seeds/articles.seed.json [--limit=3] [--dry-run] [-v] [--help]
 *   cat file.json | php bin/articles_report.php --input=-
 */

const EXIT_OK          = 0;
const EXIT_USAGE       = 2;
const EXIT_DATA_ERROR  = 3;

/**
 * Print usage/help message
 */
function usage(): void {
    $msg = <<<TXT
Articles Report — Options:
  --input=PATH    Path to JSON file or '-' for STDIN (required)
  --limit[=N]     Limit number of articles displayed (optional)
  --dry-run       No side effects (informational)
  -v              Verbose mode
  --help          Show this help

Examples:
  php bin/articles_report.php --input=storage/seeds/articles.seed.json --limit=3
TXT;
    fwrite(STDOUT, $msg . PHP_EOL);
}

/**
 * Read JSON from a file or STDIN
 */
function readJsonFrom(string $input): array {
    $json = '';
    if ($input === '-') {
        // On Windows Git Bash, ensure we can read STDIN safely
        $json = '';
        while (!feof(STDIN)) {
            $json .= fread(STDIN, 1024);
        }
    } else {
        if (!is_file($input)) {
            fwrite(STDERR, "Error: file not found: $input\n");
            exit(EXIT_DATA_ERROR);
        }
        $json = file_get_contents($input);
    }

    try {
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    } catch (Throwable $e) {
        fwrite(STDERR, "JSON Error: " . $e->getMessage() . PHP_EOL);
        exit(EXIT_DATA_ERROR);
    }

    if (!is_array($data)) {
        fwrite(STDERR, "Error: invalid JSON format\n");
        exit(EXIT_DATA_ERROR);
    }

    return $data;
}

/**
 * Normalize an article to ensure consistent keys
 */
function normalizeArticle(array $a): array {
    $title = trim((string)($a['title'] ?? 'Untitled'));
    return [
        'id'        => (int)($a['id'] ?? 0),
        'title'     => $title,
        'views'     => (int)($a['views'] ?? 0),
        'published' => (bool)($a['published'] ?? true),
        'author'    => (string)($a['author'] ?? 'N/A'),
    ];
}

// ---- main ----
$opts = getopt('v', ['input:', 'limit::', 'dry-run', 'help']);

// Show help if requested
if (isset($opts['help'])) {
    usage();
    exit(EXIT_OK);
}

// Input is required
$input = $opts['input'] ?? null;
if ($input === null) {
    fwrite(STDERR, "Error: --input is required (file path or '-')\n\n");
    usage();
    exit(EXIT_USAGE);
}

$limit   = isset($opts['limit']) ? max(1, (int)$opts['limit']) : null;
$verbose = isset($opts['v']);
$dryRun  = isset($opts['dry-run']);

// Verbose info
if ($verbose) {
    fwrite(STDOUT, "[v] Reading from " . ($input === '-' ? 'STDIN' : $input) . PHP_EOL);
}

// Read and normalize articles
$items = array_map('normalizeArticle', readJsonFrom($input));

// Keep only published articles
$published = array_values(array_filter($items, fn($a) => $a['published']));

// Sort by views descending
usort($published, fn($a, $b) => $b['views'] <=> $a['views']);

// Apply limit
if ($limit !== null) {
    $published = array_slice($published, 0, $limit);
}

// Dry-run message
if ($dryRun) {
    fwrite(STDOUT, "[dry-run] No side effects.\n");
}

// Print report
fwrite(STDOUT, "Published articles (top".($limit ? " $limit" : "")."):\n");
foreach ($published as $a) {
    fwrite(STDOUT, "- {$a['title']} ({$a['views']} views) — {$a['author']}\n");
}

$total  = count($items);
$countP = count($published);
$views  = array_reduce($published, fn($acc, $a) => $acc + $a['views'], 0);

fwrite(STDOUT, "Summary: total=$total, published=$countP, views_sum=$views\n");

exit(EXIT_OK);