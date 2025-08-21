<?php
// charge.php
header('Content-Type: application/json');

require_once __DIR__ . '/vendor/autoload.php';

// ตั้งค่า Key (Sandbox)
define('OMISE_PUBLIC_KEY','pkey_test_64nbbhnxh0371dz2kzi');
define('OMISE_SECRET_KEY', 'skey_test_64nbbhodcchurub65uw');
// (ออปชัน) กำหนด API Version ให้แน่นอน
define('OMISE_API_VERSION', '2019-05-29');

try {
    // รับ JSON จาก fetch
    $raw = file_get_contents('php://input');
    $req = json_decode($raw, true);

    if (!isset($req['token'], $req['amount'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'invalid request']);
        exit;
    }

    // !! สำคัญ: amount ควร “คำนวณจากฝั่งเซิร์ฟเวอร์จริงๆ” ตามตะกร้าสินค้า/ออเดอร์ ไม่ใช่เชื่อค่าจาก client
    $amount = (int) $req['amount']; // หน่วยสตางค์
    if ($amount < 100) {
        echo json_encode(['success' => false, 'message' => 'amount too small']);
        exit;
    }

    // สร้าง Charge ด้วย token ที่มาจาก Omise.js
    $charge = OmiseCharge::create([
        'amount'      => $amount,
        'currency'    => 'thb',
        'card'        => $req['token'],
        'description' => $req['description'] ?? 'No description',
        // เปิด 3-D Secure ในโปรดักชันอาจต้องใช้ source / authorize_uri เพิ่มเติม
    ]);

    // ตรวจสถานะ
    if (($charge['status'] ?? '') === 'successful') {
        echo json_encode([
            'success'   => true,
            'charge_id' => $charge['id'],
            'status'    => $charge['status']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'charge not successful',
            'status'  => $charge['status'] ?? 'unknown'
        ]);
    }
} catch (Exception $e) {
    // ข้อความ error จาก Omise SDK
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}