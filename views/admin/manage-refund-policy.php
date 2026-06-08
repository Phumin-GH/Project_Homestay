<?php
session_start();
require_once __DIR__ . '/../../controls/log_admin.php';

if (!isset($_SESSION["Admin_email"])) {
    header("Location: admin-login.php");
    exit();
}


?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการนโยบายการคืนเงิน - Admin Dashboard</title>
    <link rel="website icon" type="png" href="../../public/images/logo.png">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/main-menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .policy-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-header {
            background: linear-gradient(155deg, #1e5470 0%, #74adc9ff 100%);
            color: white;
            padding: 3rem 2rem;
            border-radius: 16px;
            margin-bottom: 3rem;
            text-align: center;
        }

        .page-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .page-header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .policy-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .policy-card {
            background: #ffffff;
            padding: 2rem;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e5e5;
        }

        .policy-card h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .policy-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }

        .policy-table th,
        .policy-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #e5e5e5;
        }

        .policy-table th {
            background: #f8f9ff;
            font-weight: 600;
            color: #1a1a1a;
        }

        .policy-table tr:hover {
            background: #f8f9ff;
        }

        .btn-primary {
            background: #1e5470;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary:hover {
            background: #74adc9;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(30, 84, 112, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-right: 0.5rem;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background-color: #ffffff;
            margin: 5% auto;
            padding: 0;
            border-radius: 16px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px) scale(0.9);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-header {
            background: linear-gradient(155deg, #1e5470 0%, #74adc9ff 100%);
            color: white;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 16px 16px 0 0;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .close {
            color: white;
            font-size: 2rem;
            font-weight: bold;
            cursor: pointer;
            border: none;
            background: none;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s ease;
        }

        .close:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .modal-body {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #1a1a1a;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e5e5e5;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.2s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #1e5470;
            box-shadow: 0 0 0 3px rgba(30, 84, 112, 0.1);
        }

        .modal-footer {
            padding: 1rem 2rem 2rem;
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            display: none;
        }

        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .alert-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        @media (max-width: 768px) {
            .policy-container {
                padding: 1rem;
            }

            .policy-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .page-header {
                padding: 2rem 1rem;
                margin-bottom: 2rem;
            }

            .page-header h1 {
                font-size: 2rem;
            }

            .modal-content {
                margin: 10% auto;
                width: 95%;
            }
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar">
            <div class="logo">
                <h1>
                    <img src="../../public/images/logo.png" alt="Logo" class="logo-image"
                        style="width: 3.5rem; height: 3.5rem;">
                    Homestay bookings
                </h1>
            </div>
        </nav>
    </header>

    <div class="layout">
        <aside class="sidebar" id="sidebar">
            <button class="toggle-sidebar" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <ul class="sidebar-menu">
                <li><a href="admin-dashboard.php" title="รายงานข้อมูล"><i class="fa-solid fa-ranking-star"></i><span
                            class="menu-label">รายงานข้อมูล</span></a></li>
                <li><a href="profile.php" title="ข้อมูลส่วนตัว"><i class="fas fa-user"></i><span
                            class="menu-label">ข้อมูลส่วนตัว</span></a></li>
                <li><a href="approve-properties.php" title="อนุมัติสถานที่พัก"><i
                            class="fa-solid fa-house-medical-circle-check"></i><span
                            class="menu-label">อนุมัติสถานที่พัก</span></a></li>
                <li><a href="manage-hosts.php" title="จัดการผู้ใช้งานสถานที่พัก"><i class="fas fa-users"></i><span
                            class="menu-label">ข้อมูลเจ้าของบ้านที่พัก</span></a></li>
                <li><a href="manage-users.php" title="จัดการผู้ใช้งาน"><i class="fas fa-user-friends"></i><span
                            class="menu-label">ข้อมูลผู้ใช้</span></a></li>
                <li><a href="manage-refund.php" title="คำร้องขอคืนเงินผู้ใช้งาน" class="active"><i
                            class="fas fa-hand-holding-usd"></i><span
                            class="menu-label">จัดการคำร้องขอคืนเงิน</span></a></li>
                <li><a href="manage-reviews.php" title="รีวิวจากผู้ใช้"><i class="fas fa-star"></i><span
                            class="menu-label">รีวิวจากผู้ใช้งาน</span></a></li>
                <li><a href="violations.php" title="จัดการเรื่องร้องเรียน"><i
                            class="fas fa-exclamation-triangle"></i><span
                            class="menu-label">จัดการเรื่องร้องเรียน</span></a></li>
                <li><a href="../../controls/logout.php" title="ออกจากระบบ"><i class="fas fa-sign-out-alt"></i><span
                            class="menu-label">Logout</span></a></li>
            </ul>
            <div class="sidebar-footer">
                <div>
                    <i class="fas fa-user-circle"></i>
                    <span class="menu-label"><?php echo htmlspecialchars($_SESSION['Admin_email']); ?></span>
                </div>
            </div>
        </aside>

        <div class="main-with-sidebar">
            <div class="policy-container">
                <div class="page-header">
                    <h1><i class="fas fa-file-contract"></i> จัดการนโยบายการคืนเงิน</h1>
                    <p>กำหนดและแก้ไขนโยบายการคืนเงินของระบบ</p>
                </div>

                <!-- Alert Messages -->
                <div id="alertSuccess" class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <span id="successMessage"></span>
                </div>
                <div id="alertError" class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <span id="errorMessage"></span>
                </div>

                <div class="policy-grid">
                    <!-- Current Policies -->
                    <div class="policy-card">
                        <h3><i class="fas fa-list"></i> นโยบายปัจจุบัน</h3>
                        <table class="policy-table" id="policyTable">
                            <thead>
                                <tr>
                                    <th>ก่อนเช็คอิน (วัน)</th>
                                    <th>เปอร์เซ็นต์คืน</th>
                                    <th>คำอธิบาย</th>
                                    <th>การจัดการ</th>

                                </tr>
                            </thead>
                            <tbody id="policyTableBody">
                                <!-- Policies will be loaded here -->
                            </tbody>
                        </table>
                        <button class="btn-primary" onclick="showAddModal()">
                            <i class="fas fa-plus"></i> เพิ่มนโยบายใหม่
                        </button>
                    </div>

                    <!-- Policy Statistics -->
                    <div class="policy-card">
                        <h3><i class="fas fa-chart-bar"></i> สถิติการใช้งาน</h3>
                        <div id="policyStats">
                            <!-- Statistics will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Policy Modal -->
    <div id="policyModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">เพิ่มนโยบายใหม่</h2>
                <button class="close" onclick="closePolicyModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="policyForm">
                    <input type="hidden" id="policyId" name="policy_id">
                    <div class="form-group">
                        <label for="beforeCheckin">จำนวนวันก่อนเช็คอิน:</label>
                        <input type="number" id="beforeCheckin" name="before_checkin" min="0" required>
                        <small>ระบุจำนวนวันก่อนวันเช็คอินที่ต้องยกเลิก</small>
                    </div>
                    <div class="form-group">
                        <label for="refundPercent">เปอร์เซ็นต์การคืนเงิน:</label>
                        <input type="number" id="refundPercent" name="refund_percent" min="0" max="100" required>
                        <small>ระบุเปอร์เซ็นต์ที่จะคืนเงิน (0-100)</small>
                    </div>
                    <div class="form-group">
                        <label for="policyDescription">รายละเอียดนโยบาย:</label>
                        <textarea id="policyDescription" name="policy_description" rows="3"
                            placeholder="อธิบายรายละเอียดของนโยบายนี้"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closePolicyModal()">ยกเลิก</button>
                <button type="button" class="btn-primary" onclick="savePolicyData()">
                    <i class="fas fa-save"></i> บันทึก
                </button>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main-with-sidebar');
            sidebar.classList.toggle("collapsed");
            mainContent.classList.toggle("sidebar-collapsed");
        }

        // Load policies when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadPolicies();
            loadPolicyStats();
        });

        // Load all policies
        async function loadPolicies() {
            try {
                const response = await fetch('../../api/get_policy.php');
                const policies = await response.json();

                const tbody = document.getElementById('policyTableBody');
                tbody.innerHTML = '';

                if (policies.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" style="text-align: center;">ไม่พบนโยบายการคืนเงิน</td></tr>';
                    return;
                }

                policies.forEach(policy => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${policy.Before_checkin} วัน</td>
                        <td>${policy.Refund_percen}%</td>
                        <td>${policy.Policy_description || 'ไม่มีคำอธิบาย'}</td>
                        <td>
                            <button class="btn-secondary" onclick="editPolicy(${policy.Re_Policy_id})">
                                <i class="fas fa-edit"></i> แก้ไข
                            </button>
                            <button class="btn-danger" onclick="deletePolicy(${policy.Re_Policy_id})">
                                <i class="fas fa-trash"></i> ลบ
                            </button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            } catch (error) {
                console.error('Error loading policies:', error);
                showAlert('error', 'ไม่สามารถโหลดข้อมูลนโยบายได้');
            }
        }

        // Load policy statistics
        async function loadPolicyStats() {
            try {
                const response = await fetch('../../api/get_policy.php?stats=1');
                const stats = await response.json();

                const statsDiv = document.getElementById('policyStats');
                statsDiv.innerHTML = `
                    <div style="margin-bottom: 1rem;">
                        <strong>จำนวนนโยบายทั้งหมด:</strong> ${stats.total_policies || 0}
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <strong>นโยบายที่ใช้บ่อยที่สุด:</strong> ${stats.most_used_policy || 'ไม่มีข้อมูล'}
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <strong>การใช้งานรวม:</strong> ${stats.total_usage || 0} ครั้ง
                    </div>
                `;
            } catch (error) {
                console.error('Error loading policy stats:', error);
            }
        }

        // Show add modal
        function showAddModal() {
            document.getElementById('modalTitle').textContent = 'เพิ่มนโยบายใหม่';
            document.getElementById('policyForm').reset();
            document.getElementById('policyId').value = '';
            document.getElementById('policyModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        // Edit policy
        async function editPolicy(policyId) {
            try {
                const response = await fetch(`../../api/get_policy.php?id=${policyId}`);
                const policy = await response.json();

                if (policy) {
                    document.getElementById('modalTitle').textContent = 'แก้ไขนโยบาย';
                    document.getElementById('policyId').value = policy.Re_Policy_id;
                    document.getElementById('beforeCheckin').value = policy.Before_checkin;
                    document.getElementById('refundPercent').value = policy.Refund_percen;
                    document.getElementById('policyDescription').value = policy.Policy_description || '';
                    document.getElementById('policyModal').style.display = 'block';
                    document.body.style.overflow = 'hidden';
                }
            } catch (error) {
                console.error('Error loading policy:', error);
                showAlert('error', 'ไม่สามารถโหลดข้อมูลนโยบายได้');
            }
        }

        // Delete policy
        async function deletePolicy(policyId) {
            if (!confirm('คุณแน่ใจหรือไม่ที่จะลบนโยบายนี้?')) {
                return;
            }

            try {
                const response = await fetch('../../controls/manage_policy.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        'action': 'delete',
                        'policy_id': policyId
                    })
                });

                const result = await response.json();

                if (result.success) {
                    showAlert('success', result.message || 'ลบนโยบายเรียบร้อยแล้ว');
                    loadPolicies();
                    loadPolicyStats();
                } else {
                    showAlert('error', result.message || 'ไม่สามารถลบนโยบายได้');
                }
            } catch (error) {
                console.error('Error deleting policy:', error);
                showAlert('error', 'เกิดข้อผิดพลาดในการลบนโยบาย');
            }
        }

        // Save policy data
        async function savePolicyData() {
            const form = document.getElementById('policyForm');
            const formData = new FormData(form);

            const policyId = document.getElementById('policyId').value;
            const action = policyId ? 'update' : 'create';
            formData.append('action', action);

            try {
                const response = await fetch('../../controls/manage_policy.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success === true) {
                    showAlert('success', policyId ? 'แก้ไขนโยบายเรียบร้อยแล้ว' : 'เพิ่มนโยบายเรียบร้อยแล้ว');
                    closePolicyModal();
                    loadPolicies();
                    loadPolicyStats();
                } else {
                    showAlert('error', result.message || 'ไม่สามารถบันทึกนโยบายได้');
                }
            } catch (error) {
                console.error('Error saving policy:', error);
                showAlert('error', 'เกิดข้อผิดพลาดในการบันทึกนโยบาย');
            }
        }

        // Close modal
        function closePolicyModal() {
            document.getElementById('policyModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Show alert
        function showAlert(type, message) {
            const alertElement = document.getElementById(type === 'success' ? 'alertSuccess' : 'alertError');
            const messageElement = document.getElementById(type === 'success' ? 'successMessage' : 'errorMessage');

            messageElement.textContent = message;
            alertElement.style.display = 'block';

            setTimeout(() => {
                alertElement.style.display = 'none';
            }, 5000);
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('policyModal');
            if (event.target === modal) {
                closePolicyModal();
            }
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closePolicyModal();
            }
        });
    </script>
</body>

</html>