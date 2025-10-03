<?php
header('Content-Type: application/json');
require_once __DIR__ . '/model/config/db_connect.php';
try {
        $searchTerm = $_POST['search_query'] ?? '';
        $sql = "SELECT p.Property_id,p.Property_name,p.Property_province,
p.Property_district,p.Property_subdistrict,p.Property_image,
h.Host_firstname,h.Host_lastname,p.Property_image,
AVG(rv.Rating) AS Rating 
        FROM property p
        INNER JOIN host h on p.Host_id = h.Host_id
        LEFT JOIN review rv on p.Property_id = rv.Property_id 
        WHERE p.Property_status = 'approve'";
        $params = [];
        if (!empty($searchTerm)) {
                $sql .= "AND( p.Property_name LIKE ? OR p.Property_province LIKE ?)";
                $likeTerm = "%" . $searchTerm . "%";
                $params[] = $likeTerm;
                $params[] = $likeTerm;
        }
        $sql .= "GROUP BY p.Property_id,p.Property_name,p.Property_province,p.Property_district,p.Property_subdistrict,h.Host_firstname,
        h.Host_lastname,p.Property_image";
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($products, JSON_UNESCAPED_UNICODE);
        exit();
} catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database Error: ' . $e->getMessage()]);
        exit();
}

