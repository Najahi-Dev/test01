<?php
header('Content-Type: application/json');
$action = $_GET['action'] ?? '';
$file = 'products.json';
$data = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
if ($action === 'list') {
    echo json_encode($data);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add') {
        $new = [
            'id' => time(),
            'name' => $_POST['name'] ?? '',
            'price' => (float)($_POST['price'] ?? 0),
            'quantity' => (int)($_POST['quantity'] ?? 0)
        ];
        $data[] = $new;
        file_put_contents($file, json_encode($data));
        echo json_encode(['status' => 'success']);
        exit;
    } elseif ($action === 'edit') {
        $id = $_POST['id'] ?? '';
        foreach ($data as &$prod) {
            if ($prod['id'] == $id) {
                $prod['name'] = $_POST['name'] ?? $prod['name'];
                $prod['price'] = (float)($_POST['price'] ?? $prod['price']);
                $prod['quantity'] = (int)($_POST['quantity'] ?? $prod['quantity']);
            }
        }
        file_put_contents($file, json_encode($data));
        echo json_encode(['status' => 'success']);
        exit;
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? '';
        $data = array_values(array_filter($data, fn($p) => $p['id'] != $id));
        file_put_contents($file, json_encode($data));
        echo json_encode(['status' => 'success']);
        exit;
    }
}
echo json_encode(['status' => 'error']);

