<?php
session_start();
session_destroy();
unset($_SESSION['User_email']);
header("Location: ../index.php");
exit;
