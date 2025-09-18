<?php
$password = 'phumin.pm2003';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
echo $hashed_password;
$hash = '$2y$10$F1ny6.wfBvZlS9OuS9KRsuVo.O2ee13GQMuuKu4TKtrocG2qzkOb6';
$verify = password_verify('phumin.pm2003', $hash);
if ($verify === true) {
    echo "true";
} else {
    echo "false";
}
