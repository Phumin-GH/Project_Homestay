<?php
// /api/get_daily_new_users.php
header('Content-Type: application/json');
require_once __DIR__ . '/../model/config/db_connect.php'; // <<-- ตรวจสอบ Path ให้ถูกต้อง

$sql = "SELECT month_create , SUM(total)AS  Total_users FROM 
(SELECT DATE_FORMAT(Create_at, '%Y-%m') AS month_create ,COUNT(User_id) AS total FROM user 
GROUP BY month_create 
UNION
SELECT DATE_FORMAT(Create_at, '%Y-%m') AS month_create ,COUNT(Host_id)AS Total_hosts FROM host
GROUP BY month_create ) AS con 
GROUP BY month_create
ORDER BY month_create
";
// $sql = "SELECT
//     COALESCE(u.month_create, h.month_create) AS month,
//     COALESCE(u.total_users, 0) AS Total_users,
//     COALESCE(h.total_hosts, 0) AS Total_host
// FROM
//     (SELECT
//         DATE_FORMAT(Create_at, '%Y-%m') AS month_create,
//         COUNT(User_id) AS total_users
//      FROM user
//      GROUP BY month_create) AS u
// LEFT JOIN
//     (SELECT
//         DATE_FORMAT(Create_at, '%Y-%m') AS month_create,
//         COUNT(Host_id) AS total_hosts
//      FROM host
//      GROUP BY month_create) AS h
// ON u.month_create = h.month_create
// UNION
// SELECT
//     COALESCE(u.month_create, h.month_create) AS month,
//     COALESCE(u.total_users, 0) AS new_users,
//     COALESCE(h.total_hosts, 0) AS new_hosts
// FROM
//     (SELECT
//         DATE_FORMAT(Create_at, '%Y-%m') AS month_create,
//         COUNT(User_id) AS total_users
//      FROM user
//      GROUP BY month_create) AS u

// RIGHT JOIN
//     (SELECT
//         DATE_FORMAT(Create_at, '%Y-%m') AS month_create,
//         COUNT(Host_id) AS total_hosts
//      FROM host
//      GROUP BY month_create) AS h
// ON u.month_create = h.month_create
// ORDER BY
//     month ASC;
// ";
$stmt = $conn->prepare($sql);
$stmt->execute([]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// เตรียมข้อมูลสำหรับส่งกลับ
$user_data = ['labels' => [], 'user' => []];
foreach ($results as $row) {
    $user_data['labels'][] = $row['month_create'];
    $user_data['user'][] = (int) $row['Total_users'];
}
echo json_encode($user_data);
