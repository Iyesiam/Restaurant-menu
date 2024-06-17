<?php
include 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['table_id']) && isset($data['order_items'])) {
    $table_id = $data['table_id'];
    $order_items = $data['order_items'];  // This should be an array of {menu_item_id, quantity}

    $errors = [];

    foreach ($order_items as $item) {
        $menu_item_id = $item['menu_item_id'];
        $quantity = $item['quantity'];
        $sql = "INSERT INTO orders (table_id, menu_item_id, quantity) VALUES ('$table_id', '$menu_item_id', '$quantity')";
        if (!$conn->query($sql)) {
            $errors[] = "Error inserting item ID $menu_item_id: " . $conn->error;
        }
    }

    if (empty($errors)) {
        echo json_encode(['message' => 'Order placed successfully']);
    } else {
        echo json_encode(['message' => 'Errors occurred: ' . implode(', ', $errors)]);
    }
} else {
    echo json_encode(['message' => 'Invalid input']);
}

$conn->close();
?>
