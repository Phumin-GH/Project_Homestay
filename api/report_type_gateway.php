<?php
// /api/report_type_gateway.php

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');
require_once __DIR__ . '/../model/config/db_connect.php'; // <<-- ตรวจสอบ Path ให้ถูกต้อง

$chartType = $_GET['type'] ?? ''; // รับประเภทของกราฟที่ต้องการ

$sql = '';
$data = ['labels' => [], 'data' => []];
switch ($chartType) {
    case 'gateway':

        $sql = "SELECT gateway,SUM(total_gateway) AS final_total
        FROM (SELECT Payment_gateway AS gateway, COUNT(*) AS total_gateway 
        FROM booking
        WHERE Payment_gateway IS NOT NULL AND Payment_gateway != ''
        GROUP BY gateway
        UNION ALL
        SELECT Walkin_gateway AS gateway, COUNT(*) AS total_gateway 
        FROM walkin
        WHERE Walkin_gateway IS NOT NULL AND Walkin_gateway != ''
        GROUP BY gateway
        ) AS combined_gateways
        GROUP BY gateway;
        ORDER BY final_total";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $data = ['labels' => [], 'data' => []];
        foreach ($results as $row) {
            $data['labels'][] = ucfirst($row['gateway']);
            $data['data'][] = (int) $row['final_total'];
        }
        break;
    case 'property':
        $sql = "SELECT p.Property_name AS House, COUNT(b.Booking_id) AS booking_count
            FROM booking b
            INNER JOIN property p ON b.Property_id = p.Property_id
            LEFT JOIN host h ON p.Host_id = h.Host_id
            WHERE b.Booking_status = 'successful' AND b.Create_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND h.Host_id =2
            GROUP BY p.Property_id, p.Property_name
            UNION ALL 
            SELECT p.Property_name AS House, COUNT(w.Walkin_id) AS booking_count
            FROM walkin w
            INNER JOIN property p ON w.Property_id = p.Property_id
            LEFT JOIN host h ON p.Host_id = h.Host_id
            WHERE w.Walkin_status = 'successful' AND w.Create_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND h.Host_id =2
            GROUP BY p.Property_id, p.Property_name
            ORDER BY booking_count DESC LIMIT 5;";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $data = ['labels' => [], 'data' => []];
        foreach ($results as $row) {
            $data['labels'][] = $row['House'];
            $data['data'][] = (int) $row['booking_count'];
        }
        break;
    case 'users':
        $sql = "SELECT
        COALESCE(u.month_create, h.month_create) AS month,
        COALESCE(u.total_users, 0) AS new_users,
        COALESCE(h.total_hosts, 0) AS new_hosts
    FROM
        (SELECT DATE_FORMAT(Create_at, '%Y-%m') AS month_create, COUNT(User_id) AS total_users
         FROM user WHERE Create_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH) GROUP BY month_create) AS u
    LEFT JOIN
        (SELECT DATE_FORMAT(Create_at, '%Y-%m') AS month_create, COUNT(Host_id) AS total_hosts
         FROM host WHERE Create_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH) GROUP BY month_create) AS h
    ON u.month_create = h.month_create

    UNION

    SELECT
        COALESCE(u.month_create, h.month_create) AS month,
        COALESCE(u.total_users, 0) AS new_users,
        COALESCE(h.total_hosts, 0) AS new_hosts
    FROM
        (SELECT DATE_FORMAT(Create_at, '%Y-%m') AS month_create, COUNT(User_id) AS total_users
         FROM user WHERE Create_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH) GROUP BY month_create) AS u
    RIGHT JOIN
        (SELECT DATE_FORMAT(Create_at, '%Y-%m') AS month_create, COUNT(Host_id) AS total_hosts
         FROM host WHERE Create_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH) GROUP BY month_create) AS h
    ON u.month_create = h.month_create
    ORDER BY month ASC;
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute([]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $user_data = ['labels' => [], 'user' => []];
        foreach ($results as $row) {
            $data['labels'][] = $row['month'];
            $data['user'][] = (int) $row['new_users'];
            $data['host'][] = (int) $row['new_hosts'];
        }
        break;
    case 'revenue':
        $sql = "SELECT 
            DATE_FORMAT(t.Created_at, '%Y-%m') AS month,
            SUM(t.Total_amount) AS total_revenue,
            SUM(t.Platform_fee) AS total_revenue_plat,
            SUM(t.Host_payout) AS total_revenue_host
        FROM transactions t
        LEFT JOIN booking b ON t.Booking_id = b.Booking_id
        WHERE b.Payment_status = 'paid' AND t.Created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(t.Created_at, '%Y-%m')
        ORDER BY month ASC;";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $data = ['labels' => [], 'total' => [], 'platform' => [], 'host' => []];
        foreach ($results as $row) {
            $dateObj = DateTime::createFromFormat('!Y-m', $row['month']);
            $data['labels'][] = $dateObj->format('F Y'); // เช่น "October 2025"
            $data['total'][] = (float) $row['total_revenue'];
            $data['platform'][] = (float) $row['total_revenue_plat'];
            $data['host'][] = (float) $row['total_revenue_host'];
        }
        break;
    case 'booking':
        $sql = "SELECT DATE_FORMAT(Create_at,'%Y-%m') AS month ,COUNT(*) AS total_booking FROM booking 
            GROUP BY month";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $data = ['labels' => [], 'data' => []];
        foreach ($results as $row) {
            $dateObj = DateTime::createFromFormat('!Y-m', $row['month']);
            $data['labels'][] = $dateObj->format('F Y'); // เช่น "October 2025"
            $data['data'][] = (float) $row['total_booking'];
        }
        break;
}
echo json_encode($data);
