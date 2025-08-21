<?php
$hashed_password = password_hash("1234", PASSWORD_DEFAULT);
echo "". $hashed_password ."";
if (password_verify("1234", "$2y$10$4b.8fxe4Zb7wwjJ8OCnSy.iF7gH0gnZsWj0UHcOPq0VhBLDz.M5Hm")) {
    echo "Password is correct";
} else {
    echo "     Password is incorrect";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Payment Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    /* Custom styles for input focus and card number formatting */
    input:focus {
        outline: none;
        ring: 2px;
        ring-color: #2563eb;
    }

    #card-number {
        letter-spacing: 2px;
    }
    </style>
</head>

<body class="bg-gray-100 font-sans">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white shadow-lg rounded-lg flex max-w-4xl w-full">
            <!-- Left Side: Payment Image -->
            <div class="w-1/2 hidden md:block">
                <img src="images/banner.jpg" alt="Secure Payment" class="object-cover h-full w-full rounded-l-lg">
            </div>
            <!-- Right Side: Payment Form -->
            <div class="w-full md:w-1/2 p-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">Secure Payment</h2>
                <form id="payment-form" action="test_card.php" method="POST" class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Cardholder Name</label>
                        <input type="text" id="name" name="name" placeholder="TEST USER"
                            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-600"
                            required>
                    </div>
                    <div>
                        <label for="number" class="block text-sm font-medium text-gray-700">Card Number</label>
                        <input type="text" id="card-number" name="number" placeholder="4242 4242 4242 4242"
                            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-600"
                            maxlength="19" required>
                    </div>
                    <div class="flex space-x-4">
                        <div class="w-1/2">
                            <label for="expiration_month" class="block text-sm font-medium text-gray-700">Expiration
                                Month</label>
                            <input type="number" id="expiration_month" name="expiration_month" placeholder="MM"
                                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-600"
                                min="1" max="12" required>
                        </div>
                        <div class="w-1/2">
                            <label for="expiration_year" class="block text-sm font-medium text-gray-700">Expiration
                                Year</label>
                            <input type="number" id="expiration_year" name="expiration_year" placeholder="YYYY"
                                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-600"
                                min="2025" max="2040" required>
                        </div>
                    </div>
                    <div>
                        <label for="security_code" class="block text-sm font-medium text-gray-700">Security Code
                            (CVC)</label>
                        <input type="number" id="security_code" name="security_code" placeholder="123"
                            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-600"
                            maxlength="4" required>
                    </div>
                    <button type="submit"
                        class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition duration-200">
                        Pay Now
                    </button>
                </form>
            </div>
        </div>
    </div>
    <script>
    // Format card number with spaces every 4 digits
    document.getElementById('card-number').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        let formatted = '';
        for (let i = 0; i < value.length; i++) {
            if (i > 0 && i % 4 === 0) formatted += ' ';
            formatted += value[i];
        }
        e.target.value = formatted;
    });
    </script>
</body>

</html>