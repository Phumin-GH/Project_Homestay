<?php
// รับ token จาก URL เช่น reset_password.php?token=xxxx
$token = $_GET['token'] ?? null;

// ถ้าไม่มี token อาจจะแสดงข้อความหรือ redirect ออกไป
if (!$token) {
    // ในการใช้งานจริง ควร redirect ไปหน้า error หรือหน้าหลัก
    header('Location: index.php');
    exit();
    // สำหรับตัวอย่างนี้ เราจะตั้ง token จำลองไว้เพื่อให้หน้าเว็บแสดงผลได้
    // $token = 'a1b2c3d4e5f6g7h8';
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        /* --- General Styles --- */
        :root {
            --primary-color: #4a69bd;
            /* สีหลัก (น้ำเงิน) */
            --background-color: #f4f7f6;
            /* สีพื้นหลัง (เทาอ่อน) */
            --text-color: #333333;
            /* สีตัวอักษร */
            --border-color: #dddddd;
            /* สีเส้นขอบ */
            --white-color: #ffffff;
            --error-color: #e74c3c;
            /* สีสำหรับข้อความผิดพลาด */
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* --- Container --- */
        .reset-container {
            background-color: var(--white-color);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        /* --- Header --- */
        .form-header h1 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-header p {
            font-size: 14px;
            color: #777;
            margin-bottom: 30px;
        }

        /* --- Form Elements --- */
        .form-group {
            text-align: left;
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(74, 105, 189, 0.2);
        }

        /* --- Button --- */
        .btn-submit {
            width: 100%;
            padding: 14px;
            background-color: var(--primary-color);
            color: var(--white-color);
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-submit:hover {
            background-color: #3b569b;
            /* Darker shade of primary color */
        }
    </style>
</head>

<body>

    <div class="reset-container">
        <div class="form-header">
            <h1><?php echo htmlspecialchars($token); ?></h1>
            <h1>Reset Your Password</h1>
            <p>Please enter your new password below.</p>
        </div>

        <!-- <form method="POST" action="controls/process_reset.php"> -->
        <div class="form-group">
            <label for="password">New Password</label>
            <input type="password" id="password" name="password" placeholder="Enter a strong password" required>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password"
                placeholder="Enter your new password again" required>
        </div>

        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

        <button type="submit" class="btn-submit">Update Password</button>

    </div>
    <script>
        document.querySelector('.btn-submit').addEventListener('click', async (e) => {
            e.preventDefault();
            const password = document.getElementById('password').value.trim();
            const confirm_password = document.getElementById('confirm_password').value.trim();
            const token = "<?php echo htmlspecialchars($token); ?>";
            try {
                const response = await fetch('controls/process_reset.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({
                            password,
                            confirm: confirm_password,
                            token
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Redirect to login page after successful password reset
                            alert(data.message);
                            window.location.href = data.reddit;
                        } else {
                            alert(data.message);
                            window.location.reload();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert(error);
                        window.location.href = data.reddit;
                    });

            } catch (error) {
                console.error('Error:', error);
                alert(error);
                window.location.href = 'users/user-login.php';

            }
        });
    </script>
</body>

</html>