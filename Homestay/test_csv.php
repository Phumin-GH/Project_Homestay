<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "cashflow_db";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// เปลี่ยน path ให้ตรงกับไฟล์ csv ของคุณ
$csvFile = fopen("รายรับ-รายจ่าย(fake)3.2.csv", "r");

if ($csvFile !== false) {
    // ข้ามบรรทัดแรก (หัวตาราง)
    fgetcsv($csvFile);

    while (($data = fgetcsv($csvFile, 1000, ",")) !== false) {
        $id = $conn->real_escape_string($data[0]);
        $timestamp = $conn->real_escape_string($data[1]);
        $date = $data[2]; // ต้องอยู่ในรูปแบบ yyyy-mm-dd
        $type = $data[3];
        $amount = $data[4];
        $description = $data[5];
        $category = $data[6];
        $user = $data[7];
        
        

        // ตรวจสอบว่าเป็นวันที่ถูกต้องหรือไม่
        if (DateTime::createFromFormat('Y-m-d', $date) !== false) {
            $sql = "INSERT INTO transactions (id,insert_at, date,type,amount,dst_id,cate_id,user_id)
                    VALUES ('$id', '$timestamp', '$date','$type','$amount','$description','$category','$user')";

            if ($conn->query($sql) === TRUE) {
                echo "Inserted: $user<br>";
            } else {
                echo "Error: " . $conn->error . "<br>";
            }
        } else {
            echo "Invalid date format for: $user<br>";
        }
    }

    fclose($csvFile);
} else {
    echo "Failed to open file.";
}

$conn->close();
?>
