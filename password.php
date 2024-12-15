<?php

$passwords = ['password123', 'password124', 'password126', 'password127'];

foreach ($password as $password){
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    echo "Password $password => Hash: $hashedPassword<br>";
}

?>