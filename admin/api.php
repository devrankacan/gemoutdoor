<?php
require_once __DIR__ . '/auth.php';
requireAuth();
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

// ── helpers ───────────────────────────────────────────────
function jsonOk(mixed $payload = null): never {
    echo json_encode(['ok' => true, 'data' => $payload]);
    exit;
}
function jsonErr(string $msg): never {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $msg]);
    exit;
}
function generateId(): string {
    return 'u' . substr(md5(uniqid('', true)), 0, 8);
}

// ── actions ───────────────────────────────────────────────
switch ($action) {

    // ── GET single unit ──────────────────────────────────
    case 'get':
        $id = trim($_POST['id'] ?? '');
        $unit = unitById($id);
        if (!$unit) jsonErr('Unit not found.');
        jsonOk($unit);

    // ── SAVE (add or edit) ───────────────────────────────
    case 'save':
        $id      = trim($_POST['id'] ?? '');
        $model   = trim($_POST['model'] ?? '');
        $year    = trim($_POST['year'] ?? '');
        $color   = trim($_POST['color'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $vin     = trim($_POST['vin'] ?? '');
        $status  = trim($_POST['status'] ?? 'available');
        $price   = trim($_POST['price'] ?? '');
        $upgrades = array_values(array_filter(array_map('trim', (array)($_POST['upgrades'] ?? []))));

        if (!$model || !$vin) jsonErr('Model and VIN are required.');
        if (!in_array($status, ['available','sold','demo'])) $status = 'available';

        // Handle image upload if provided
        $imagePath = trim($_POST['existing_image'] ?? '');
        if (!empty($_FILES['image']['tmp_name'])) {
            // Validate
            $file = $_FILES['image'];
            if ($file['size'] > MAX_SIZE) jsonErr('Image too large (max 5 MB).');
            $mime = mime_content_type($file['tmp_name']);
            if (!in_array($mime, ALLOWED_TYPES)) jsonErr('Only JPEG, PNG, WebP allowed.');

            // Ensure upload dir
            if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);

            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'unit_' . preg_replace('/[^a-z0-9]/i','_', $vin) . '_' . time() . '.' . strtolower($ext);
            $dest = UPLOAD_DIR . $filename;

            if (!move_uploaded_file($file['tmp_name'], $dest)) jsonErr('Upload failed.');
            $imagePath = UPLOAD_URL . $filename;
        }

        $data = loadUnits();

        if ($id) {
            // Edit existing
            $found = false;
            foreach ($data['units'] as &$u) {
                if ($u['id'] === $id) {
                    $u['model']    = $model;
                    $u['year']     = $year;
                    $u['color']    = $color;
                    $u['location'] = $location;
                    $u['vin']      = $vin;
                    $u['status']   = $status;
                    $u['price']    = $price;
                    $u['image']    = $imagePath;
                    $u['upgrades'] = $upgrades;
                    $found = true;
                    break;
                }
            }
            unset($u);
            if (!$found) jsonErr('Unit not found for edit.');
        } else {
            // Add new
            $data['units'][] = [
                'id'       => generateId(),
                'model'    => $model,
                'year'     => $year,
                'color'    => $color,
                'location' => $location,
                'vin'      => $vin,
                'status'   => $status,
                'price'    => $price,
                'image'    => $imagePath,
                'upgrades' => $upgrades,
            ];
        }

        saveUnits($data);
        jsonOk();

    // ── DELETE ───────────────────────────────────────────
    case 'delete':
        $id = trim($_POST['id'] ?? '');
        if (!$id) jsonErr('No ID provided.');
        $data = loadUnits();
        $before = count($data['units']);
        $data['units'] = array_values(array_filter($data['units'], fn($u) => $u['id'] !== $id));
        if (count($data['units']) === $before) jsonErr('Unit not found.');
        saveUnits($data);
        jsonOk();

    default:
        jsonErr('Unknown action.');
}
