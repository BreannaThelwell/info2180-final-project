<?php

$password = ['$2y$10$dOhVZs8KNZAcsqDK48p2Tuu1hXfMjeW0/RKc6XAczxVZ2zcoKP3Ta', 
            '$2y$10$.mf15ul/lnyuHBym8en5d.uOaFksgl.xz/xT1ABUrH5Myvfi.kJJe', 
            '$2y$10$98C2BSKINH9M5DXGtmMCHeIO1RCUofUr/BF7WGjJBPqGo01SK4NEO', 
            '$2y$10$Ef8GNyXrNlNpcR0vnseWbOVbgmQrEzCvHTeC4kBHsdQwWdQ0QGuc.', 
            '$2y$10$/ox0gWfr12HtD9PKZyxYgehSywFSGEZX0gCoZd5I9WoThliWvCy3S'];

foreach ($password as $password){
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    echo "Password $password => Hash: $hashedPassword<br>";
}

?>