<?php
    if (!isset($_SESSION['validSession']) || !isset($_SESSION['editmode']) || (isset($_SESSION['editmode']) && $_SESSION['editmode'] <> 'SUPER')) {
        echo "<div style='text-align:center;margin-top:100px;'><span style='font-size:40px;color:blue;'><strong>403</strong></span><h2>Forbidden: Access prohibited</h2><em>HTTP Response Code</em></div>";
        exit;
    }
