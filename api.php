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
        $imagePath = '';
        if (!empty($_FILES['image']['name'])) {
            if (!is_dir('uploads')) {
                mkdir('uploads');
            }
            $filename = time() . '_' . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $filename);
            $imagePath = 'uploads/' . $filename;
        }
        $new = [
            'id' => time(),
            'name' => $_POST['name'] ?? '',
            'price' => (float)($_POST['price'] ?? 0),
            'quantity' => (int)($_POST['quantity'] ?? 0),
            'image' => $imagePath
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
                if (!empty($_FILES['image']['name'])) {
                    if (!is_dir('uploads')) {
                        mkdir('uploads');
                    }
                    $filename = time() . '_' . basename($_FILES['image']['name']);
                    move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $filename);
                    if (!empty($prod['image']) && file_exists($prod['image'])) {
                        unlink($prod['image']);
                    }
                    $prod['image'] = 'uploads/' . $filename;
                }
            }
        }
        file_put_contents($file, json_encode($data));
        echo json_encode(['status' => 'success']);
        exit;
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? '';
        foreach ($data as $index => $p) {
            if ($p['id'] == $id) {
                if (!empty($p['image']) && file_exists($p['image'])) {
                    unlink($p['image']);
                }
                unset($data[$index]);
            }
        }
        $data = array_values($data);
        file_put_contents($file, json_encode($data));
        echo json_encode(['status' => 'success']);
        exit;
    }
}
echo json_encode(['status' => 'error']);

