// เชื่อมต่อฐานข้อมูล
include '../config/db_connect.php';

// ดึงข้อมูลผู้ดูแลระบบ
$admin_email = $_SESSION['Admin_email'];
$sql = "SELECT * FROM admins WHERE Admin_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $admin_email);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// สถิติต่างๆ
$sql_properties = "SELECT COUNT(*) as total FROM properties";
$total_properties = $conn->query($sql_properties)->fetch_assoc()['total'];

$sql_hosts = "SELECT COUNT(*) as total FROM hosts";
$total_hosts = $conn->query($sql_hosts)->fetch_assoc()['total'];

$sql_users = "SELECT COUNT(*) as total FROM users";
$total_users = $conn->query($sql_users)->fetch_assoc()['total'];

$sql_bookings = "SELECT COUNT(*) as total FROM bookings";
$total_bookings = $conn->query($sql_bookings)->fetch_assoc()['total'];

$sql_income = "SELECT SUM(Total_price) as total FROM bookings";
$total_income = $conn->query($sql_income)->fetch_assoc()['total'] ?? 0;

$sql_reviews = "SELECT COUNT(*) as total FROM reviews";
$total_reviews = $conn->query($sql_reviews)->fetch_assoc()['total'];