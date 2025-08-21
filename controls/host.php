// เชื่อมต่อฐานข้อมูล
include '../config/db_connect.php';

// ดึงข้อมูลเจ้าของบ้านพัก
$host_email = $_SESSION['Host_email'];
$sql = "SELECT * FROM hosts WHERE Host_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $host_email);
$stmt->execute();
$result = $stmt->get_result();
$host = $result->fetch_assoc();

// ดึงข้อมูลบ้านพักของเจ้าของ
$sql_properties = "SELECT * FROM properties WHERE Host_id = ? ORDER BY Property_created_at DESC";
$stmt_properties = $conn->prepare($sql_properties);
$stmt_properties->bind_param("i", $host['Host_id']);
$stmt_properties->execute();
$properties_result = $stmt_properties->get_result();

// ดึงการจองของบ้านพักทั้งหมด
$sql_bookings = "SELECT b.*, p.Property_name, u.User_firstname, u.User_lastname, u.User_phone
FROM bookings b
JOIN properties p ON b.Property_id = p.Property_id
JOIN users u ON b.User_email = u.User_email
WHERE p.Host_id = ?
ORDER BY b.Booking_date DESC";
$stmt_bookings = $conn->prepare($sql_bookings);
$stmt_bookings->bind_param("i", $host['Host_id']);
$stmt_bookings->execute();
$bookings_result = $stmt_bookings->get_result();

// คำนวณรายได้รวม
$sql_income = "SELECT SUM(Total_price) as total_income FROM bookings b
JOIN properties p ON b.Property_id = p.Property_id
WHERE p.Host_id = ?";
$stmt_income = $conn->prepare($sql_income);
$stmt_income->bind_param("i", $host['Host_id']);
$stmt_income->execute();
$income_result = $stmt_income->get_result();
$total_income = $income_result->fetch_assoc()['total_income'] ?? 0;

// ดึงรีวิวของบ้านพักทั้งหมด
$sql_reviews = "SELECT r.*, p.Property_name, u.User_firstname, u.User_lastname
FROM reviews r
JOIN properties p ON r.Property_id = p.Property_id
JOIN users u ON r.User_email = u.User_email
WHERE p.Host_id = ?
ORDER BY r.Review_date DESC";
$stmt_reviews = $conn->prepare($sql_reviews);
$stmt_reviews->bind_param("i", $host['Host_id']);
$stmt_reviews->execute();
$reviews_result = $stmt_reviews->get_result();