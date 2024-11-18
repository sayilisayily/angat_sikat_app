<?php
include 'connection.php'; // Include the database connection

$category = $_GET['category'];

$category_tables = [
    'Activities' => 'events', // Table name for Activities
    'Purchases' => 'purchases', // Table name for Purchases
    'Maintenance' => 'maintenance', // Table name for Maintenance
    'Operational Expenses' => 'operational_expenses' // Table name for Operational Expenses
];

$table = $category_tables[$category] ?? null;

if ($table) {
    $sql = "SELECT title FROM $table";
    $result = $conn->query($sql);

    $titles = [];
    while ($row = $result->fetch_assoc()) {
        $titles[] = $row['title'];
    }

    echo json_encode($titles);
} else {
    echo json_encode([]);
}

$conn->close();
?>
