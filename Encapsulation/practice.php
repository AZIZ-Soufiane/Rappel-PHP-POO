<?php
declare(strict_types=1);

class Article {
  public readonly int $id;          // immuable après construction
  private string $title;            // encapsulé
  private string $slug;             // dérivé
  private array $tags = [];         // encapsulé

  private static int $count = 0;    // partagé

  public function __construct(int $id, string $title, array $tags = []) {
    if ($id <= 0) throw new InvalidArgumentException("id > 0 requis.");
    $this->id = $id;
    $this->setTitle($title);
    $this->tags = $tags;
    self::$count++;
  }

  /** Usine avec LSB : retournera la sous-classe correcte si appelée depuis elle */
  public static function fromTitle(int $id, string $title): static {
    return new static($id, $title);
  }

  /** Getters (API publique minimale) */
  public function title(): string { return $this->title; }
  public function slug(): string { return $this->slug; }
  public function tags(): array { return $this->tags; }

  /** Setter encapsulant validation + mise à jour du slug */
  public function setTitle(string $title): void {
    $title = trim($title);
    if ($title === '') throw new InvalidArgumentException("Titre requis.");
    $this->title = $title;
    $this->slug  = static::slugify($title);
  }

  public function addTag(string $tag): void {
    $t = trim($tag);
    if ($t === '') throw new InvalidArgumentException("Tag vide.");
    $this->tags[] = $t;
  }

  public static function count(): int { return self::$count; }

  /** Protégé : surcharge possible côté sous-classe */
  protected static function slugify(string $value): string {
    $s = strtolower($value);
    $s = preg_replace('/[^a-z0-9]+/i', '-', $s) ?? '';
    return trim($s, '-');
  }
}

/** Sous-classe : spécialisation via `protected` et LSB */
class FeaturedArticle extends Article {
  protected static function slugify(string $value): string {
    return 'featured-' . parent::slugify($value);
  }
}

// Démo
$a = Article::fromTitle(1, 'Encapsulation & visibilité en PHP');
$b = FeaturedArticle::fromTitle(2, 'Lire moins, comprendre plus');
$b->addTag('best');

echo $a->slug() . PHP_EOL; // "encapsulation-visibilite-en-php"
echo $b->slug() . PHP_EOL; // "featured-lire-moins-comprendre-plus"
echo Article::count() . PHP_EOL; // 2
