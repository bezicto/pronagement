<?php
    
    defined('includeExist') || die("<div style='text-align:center;margin-top:100px;'><span style='font-size:40px;color:blue;'><strong>WARNING</strong></span><h2>Forbidden: Direct access prohibited</h2><em>HTTP Response Code</em></div>");
    
    //preventing CSRF
    if (isset($_POST) && !empty($_POST))
    {
        if (isset($_POST['token']) && $_POST['token'] == $_SESSION['token']) {
            $proceedAfterToken = true;
        }
        else
        {
            $proceedAfterToken = false;
            echo "<div style='text-align:center;margin-top:100px;'><span style='font-size:40px;color:blue;'><strong>405</strong></span><h2>Forbidden: Method Not Allowed</h2><em>HTTP Response Code</em></div>";
            exit;
        }
    }
    //generate new token for future validation
    $_SESSION['token'] = md5(uniqid(mt_rand(), true)).openssl_encrypt(uniqid(time()), "AES-128-CTR",time().$password_aes_key,0,"1234567891011121");
