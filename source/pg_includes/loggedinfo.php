<?php
    defined('includeExist') || die("<div style='text-align:center;margin-top:100px;'><span style='font-size:40px;color:blue;'><strong>WARNING</strong></span><h2>Forbidden: Direct access prohibited</h2><em>HTTP Response Code</em></div>");

    if (isset($_SESSION['username'])) {
        include_once $appendroot.'pg_includes/navbar.php';
    }
