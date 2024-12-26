<?php
    //overwrite PHP setting for error_reporting
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL);//e.g. (E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT); display all errors except notice and deprecated and strict
    
    //load version information
    include_once 'pg_includes/build.php';

    //load default config file; this file must present in order Pronagement to function properly.
    include_once "config.php";

    //header policy
    $hd_Strict_Transport_Security = "max-age=31536000";
    $hd_X_Frame_Options = "SAMEORIGIN";
    $hd_Referrer_Policy = "same-origin";
    $hd_Content_Security_Policy = "default-src $system_path $system_ip localhost ajax.googleapis.com 'unsafe-inline' 'unsafe-eval' img-src * data:; frame-ancestors default-src $system_path $system_ip localhost ajax.googleapis.com 'unsafe-inline' 'unsafe-eval' img-src * data:;";
    $hd_X_Content_Type_Options = "nosniff";
    $hd_Permissions_Policy = "accelerometer=(), camera=(), geolocation=(), gyroscope=(), magnetometer=(), microphone=(), payment=(), usb=()";

    //for mysqli database connection engine
    try {
        $conn = @mysqli_connect ($dbhost,$dbuser,$dbpass,$dbname) or die("Unable to connect to $dbname at $dbhost.");
        @mysqli_query($conn,"SET CHARACTER SET 'utf8'");
        @mysqli_query($conn,"SET SESSION collation_connection ='utf8_swedish_ci'");
    } catch (mysqli_sql_exception $e) {
        exit("Unable to connect to database. Check your configuration file.");
    }

    //new database connection for in use with prepared statement
    try {
        $new_conn = new mysqli($dbhost,$dbuser,$dbpass,$dbname);
        $new_conn->set_charset("utf8mb4");
    } catch (mysqli_sql_exception $e) {
        exit("Unable to connect to database. Check your configuration file.");
    }
    $new_conn->set_charset("utf8mb4");
