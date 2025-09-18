<?php
declare(strict_types=1);

// Step 1 — Model User with promotion
class User {
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public ?string $bio = null,
        public int $articlesCount = 0,
    ) {}

    public function initials(): string {
        $parts = preg_split('/\s+/', trim($this->name));
        $letters = array_map(
            fn($p) => strtoupper(substr($p, 0, 1)),
            $parts
        );
        return implode('', $letters);
    }

    public function toArray(): array {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'email'         => $this->email,
            'bio'           => $this->bio,
            'articlesCount' => $this->articlesCount,
            'initials'      => $this->initials(),
        ];
    }
}

// Step 2 — Static Factory: fromArray(array): self
class UserFactory {
    public static function fromArray(array $u): User {
        $id    = max(1, (int)($u['id'] ?? 0));
        $name  = trim((string)($u['name'] ?? 'Inconnu'));
        $email = trim((string)($u['email'] ?? ''));
        if ($email === '') {
            throw new InvalidArgumentException('email requis');
        }
        $bio   = isset($u['bio']) ? (string)$u['bio'] : null;
        $count = (int)($u['articlesCount'] ?? 0);

        return new User($id, $name, $email, $bio, $count);
    }
}

// Step 3 — Map an array of inputs to objects
$raw = [
    ['id' => 1, 'name' => 'Amina Zouhair', 'email' => 'amina@example.com', 'articlesCount' => 5],
    ['id' => 2, 'name' => 'Jean Dupont', 'email' => 'jean@example.com'],
    ['id' => 3, 'name' => 'Lina Chen', 'email' => 'lina@example.com', 'bio' => 'Writer & traveler', 'articlesCount' => 8],
];

$users = array_map(
    fn(array $u) => UserFactory::fromArray($u),
    $raw
);

// Display report
foreach ($users as $user) {
    $data = $user->toArray();
    echo "- {$data['name']} ({$data['initials']}) — Articles: {$data['articlesCount']}\n";
}
