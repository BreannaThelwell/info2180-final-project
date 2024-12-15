<?php

//Array containing the list of passwords
$password = ['password123', 
            'password124', 
            'password125', 
            'password126', 
            'password127'];

//Loops the array for each password
foreach ($password as $password){
    
    //Hash the password using the default algorithm
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    //Outputs the original password and its hashed version
    echo "Password $password => Hash: $hashedPassword<br>";
}

?>