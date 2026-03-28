<?php
// Admin panel configuration
// Change ADMIN_PASSWORD to secure your panel
define('ADMIN_PASSWORD', 'Hotomobil2025');
define('DATA_FILE',   dirname(__DIR__) . '/data/units.json');
define('UPLOAD_DIR',  dirname(__DIR__) . '/uploads/units/');
define('UPLOAD_URL',  'uploads/units/');
define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/webp']);
define('MAX_SIZE', 5 * 1024 * 1024); // 5 MB

function loadUnits(): array {
    if (!file_exists(DATA_FILE)) return ['units' => []];
    return json_decode(file_get_contents(DATA_FILE), true) ?: ['units' => []];
}

function saveUnits(array $data): void {
    file_put_contents(DATA_FILE, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function unitById(string $id): ?array {
    $data = loadUnits();
    foreach ($data['units'] as $u) {
        if ($u['id'] === $id) return $u;
    }
    return null;
}

function defaultImage(string $model): string {
    if (stripos($model, 'cyber') !== false) {
        return 'https://peru-dragonfly-236453.hostingersite.com/wp-content/uploads/2025/11/gorsel_2025-11-09_164137048.png';
    }
    return 'https://peru-dragonfly-236453.hostingersite.com/wp-content/uploads/2025/11/after.png';
}
