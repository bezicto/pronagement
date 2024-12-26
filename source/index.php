<!DOCTYPE HTML>
<?php
    session_start();define('includeExist', true);
    include_once 'core.php';
    include_once 'pg_includes/functions.php';
    $thisPageTitle  = "Main Page";

    //check if password_aes_key if still default
    if ($password_aes_key == "45C799DB3EBC65DFBC69A0F36F605E6CA2447CD519C50B7DA0D0D45D2B0F2431") {
        echo "Please change the AES Key Value in config.php first. After that refresh this page.";exit;
    }

    //check if admin existed or not, if not exist then create one
    $stmt_countadmin = $new_conn->prepare("select count(*) from eg_auth where username='admin'");
    $stmt_countadmin->execute();
    $stmt_countadmin->bind_result($num_results_affected_username);
    $stmt_countadmin->fetch();$stmt_countadmin->close();
    if ($num_results_affected_username == 0) {
        $newRandomPassword = time();
        $stmt_insert = $new_conn->prepare("insert into eg_auth values(DEFAULT,'admin',AES_ENCRYPT('$newRandomPassword','$password_aes_key'),'SUPER','Administrator','Administrative','all','','OFF',0,DEFAULT,DEFAULT)");
        $stmt_insert->execute();
        $stmt_insert->close();
        echo "User: <strong>admin</strong> has been created. Default password is: <strong>$newRandomPassword</strong><br/>Copy this value and then refresh this page. It will not appear again.";
        exit;
    }

    //log out active user whenever they arrived at this page
    if (isset($_SESSION['username_guest']) && (isset($_GET['log']) && $_GET['log'] == 'out')) {
        mysqli_query($GLOBALS["conn"],"update eg_auth set online='OFF' where username='".$_SESSION['username_guest']."'");
        
        //clear search sessions
        unset($_SESSION['sear_scstr']);
        unset($_SESSION['sear_sctype']);
        unset($_SESSION['sear_page']);
        unset($_SESSION['appendurl']);
        unset($_SESSION['appendfilter']);

        //clear routes
        unset($_SESSION['route1']);
        unset($_SESSION['route2']);
        unset($_SESSION['route3']);
        unset($_SESSION['needtochangepwd']);
        
        //clear user session
        unset($_SESSION['username_guest']);

    } elseif (isset($_SESSION['username_guest'])) {
        header("Location: search.php");
        die();
    }
    
    //if username is set and log is defined
    if (isset($_SESSION['username']) && isset($_GET['log'])) {
        if ($_GET['log'] == 'out') {
            mysqli_query($GLOBALS["conn"],"update eg_auth set online='OFF' where username='".$_SESSION['username']."'");
        } elseif ($_GET['log'] == 'block') {
            mysqli_query($GLOBALS["conn"],"update eg_auth set num_attempt=$default_num_attempt_login where username='".$_SESSION['username']."'");
            echo "<script>alert('Illegal operation. You have been blocked.');</script>";
        }
        
        unset($_SESSION['username']);
        unset($_SESSION['editmode']);
        unset($_SESSION['lastlogin']);
        unset($_SESSION['accessgranted']);
        unset($_SESSION['ref']);
        unset($_SESSION['validSession']);
        session_destroy();
    } elseif (isset($_SESSION['username'])) {
        header("Location: dashboard.php");
        die();
    }
    
?>

<html lang='en'>

<head>
    <?php include_once 'pg_includes/header.php'; ?>
</head>

<body>

    <table style="padding-top:30px;" class=transparentCenter100percent>
        <tr>
            <td>
                <img alt='Main Logo' width=<?php echo $main_logo_width;?> src="./<?php echo $main_logo;?>">
                <br/><?php echo $intro_words;?>
                <a style='margin-top:20px;' class='sButtonCyan' href='in.php'>Login</a> <a style='margin-top:10px;' class='sButtonRed' href='dashboard.php'>Dashboard</a>
            </td>
        </tr>
    </table>

    <br/>
    
    <?php
        include_once './pg_includes/footer.php';
    ?>

    <?php
    $ip = getenv('HTTP_CLIENT_IP')?:
    getenv('HTTP_X_FORWARDED_FOR')?:
    getenv('HTTP_X_FORWARDED')?:
    getenv('HTTP_FORWARDED_FOR')?:
    getenv('HTTP_FORWARDED')?:
    getenv('REMOTE_ADDR');
    
    //echo "<br/><br/><br/><div style='width:100%;text-align:center;'>Recorded IP: $ip</div>";
    
    ?>

    
</body>

</html>
<?php mysqli_close($GLOBALS["conn"]); exit(); ?>
