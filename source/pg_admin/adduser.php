<!DOCTYPE HTML>
<?php
    session_start();define('includeExist', true);
    include_once '../pg_includes/access_super.php';
    include_once '../core.php';
    include_once '../pg_includes/functions.php';
    $thisPageTitle = "Add User";
    
    //routing - experimental
    //route check before entering this page
    //route1 index2, route2 chanuser, route3 if route1 and route2 were followed
    if ((isset($_SESSION['route1']) && $_SESSION['route1'] == '1') && (isset($_SESSION['route2']) && $_SESSION['route2'] == '2')) {
        $_SESSION['route3'] = '3';
    }
    else {
        $_SESSION['route3'] = '0';
    }

    if ($_SESSION['route3'] != '3') {
        //immediately block user from any future usage
        mysqli_query($GLOBALS["conn"],"update eg_auth set num_attempt=$default_num_attempt_login where username='".$_SESSION['username']."'");
        header("Location: ../index.php?log=out");
        exit;
    }
    
    //preventing CSRF
    include_once '../pg_includes/token_validate.php';
?>

<html lang='en'>

<head><?php include_once '../pg_includes/header.php'; ?></head>

<body>
    
    <?php include_once '../pg_includes/loggedinfo.php'; ?>
        
    <hr>
    
    <?php
    
        if (isset($_REQUEST["submitted"]) && $proceedAfterToken) {
            $staffid1 = just_clean(mysqli_real_escape_string($GLOBALS["conn"],$_POST["staffid1"]),'min');
            $fullname1 = just_clean(mysqli_real_escape_string($GLOBALS["conn"],$_POST["fullname1"]),'min');
            $division1 = just_clean(mysqli_real_escape_string($GLOBALS["conn"],$_POST["division1"]),'min');
            $usertype1 = just_clean(mysqli_real_escape_string($GLOBALS["conn"],$_POST["usertype1"]),'min');
            $accessgrant1 = just_clean(mysqli_real_escape_string($GLOBALS["conn"],$_POST["accessgrant1"]),'min');

            if ($_REQUEST["submitted"] == "Insert") {
                $stmt_count = $new_conn->prepare("select count(*) from eg_auth where username = ?");
                $stmt_count->bind_param("s", $staffid1);
                $stmt_count->execute();
                $stmt_count->bind_result($num_results_affected_username);
                $stmt_count->fetch();
                $stmt_count->close();
                
                if ($num_results_affected_username == 0) {
                    if (!empty($staffid1) && !empty($fullname1) && !empty($division1) && !empty($usertype1)) {
                        $stmt_insert = $new_conn->prepare("insert into eg_auth values(DEFAULT,?,AES_ENCRYPT('$default_create_password','$password_aes_key'),?,?,?,?,'','OFF',0,DEFAULT,DEFAULT)");
                        $stmt_insert->bind_param("sssss", $staffid1, $usertype1, $fullname1, $division1, $accessgrant1);
                        $stmt_insert->execute();
                        $stmt_insert->close();
                        echo "<script>window.alert('User $fullname1 has been input into the database. Password has been set to $default_create_password');</script>";
                    } else {
                        echo "<script>window.alert('Your input has been cancelled. Check if any field(s) left emptied before posting.');</script>";
                    }
                } elseif ($num_results_affected_username >= 1) {
                    echo "<script>window.alert(\"Your input has been cancelled. Duplicate field value detected.\");</script>";
                }
            } elseif ($_REQUEST["submitted"] == "Update") {
                $id1 = $_POST["id1"];
            
                if (!empty($fullname1) && !empty($staffid1) && !empty($division1)) {
                    $stmt_update = $new_conn->prepare("update eg_auth set name=?, username=?, division=?, usertype=?, accessgrant=? where id=?");
                    $stmt_update->bind_param("sssssi", $fullname1, $staffid1, $division1, $usertype1, $accessgrant1, $id1);
                    $stmt_update->execute();
                    $stmt_update->close();
                    echo "<script>window.alert('The record has been updated.');</script>";
                } else {
                    echo "<script>window.alert('Error. Please make sure there were no empty field(s).<br/>The record has been restored to it original state.');</script>";
                }
            }
        }

        if (isset($_GET["edt"]) && $_GET["edt"] <> null && is_numeric($_GET["edt"])) {
            $get_id_upd = mysqli_real_escape_string($GLOBALS["conn"], $_GET["edt"]);

            $stmt3 = $new_conn->prepare("select id,username,usertype,division,name,accessgrant from eg_auth where id = ?");
            $stmt3->bind_param("i", $get_id_upd);
            $stmt3->execute();
            $stmt3->store_result();
            $stmt3->bind_result($id3,$username3,$usertype3,$division3,$name3,$accessgrant3);
            $stmt3->fetch();
            $stmt3->close();
        }
        
    ?>
    
    <?php if (!isset($_REQUEST["submitted"]) || $_REQUEST["submitted"] != "Update") { ?>
        <table class=whiteHeader>
            <tr class=yellowHeaderCenter><td><strong>User Menu </strong></td></tr>
            <tr class=greyHeaderCenter><td style='width:100%;'><br/>
            <form action="adduser.php" method="post" enctype="multipart/form-data">
                <table style='margin-left:auto;margin-right:auto;width:80%;'>
                    <tr>
                    <td style='text-align:right;width:30%;'><strong>IC/ID </strong></td>
                    <td style='text-align:left;'><input style="width:100%;border-radius:5px;height:30px;font-size:10pt;" type="text" name="staffid1" size="40" maxlength="255" <?php if (isset($username3)) {echo "readonly=readonly";} ?> value="<?php if (isset($username3)) {echo $username3;} ?>"/></td>
                    </tr>
                
                    <tr>
                    <td style='text-align:right;'><strong>Full Name </strong></td>
                    <td style='text-align:left;'><input style="width:100%;border-radius:5px;height:30px;font-size:10pt;" type="text" name="fullname1" size="40" maxlength="255" value="<?php if (isset($name3)) {echo $name3;} ?>"/></td>
                    </tr>
                                        
                    <tr>
                    <td style='text-align:right;'><strong>Address </strong></td>
                    <td style='text-align:left;'><textarea style="width:100%;border-radius:5px;font-size:10pt;" name="division1" cols="39" rows="5"><?php if (isset($division3)) {echo $division3;} ?></textarea></td>
                    </tr>

                    <tr>
                    <td style='text-align:right;'><strong>User Level </strong></td>
                    <td style='text-align:left;'>
                    <select name="usertype1" style="width:100%;border-radius:5px;height:30px;font-size:10pt;">
                        <option value='SUPER' <?php if (isset($usertype3) && $usertype3 == "SUPER") {echo "selected";}?>>SUPER</option>
                        <option value='STAFF' <?php if (isset($usertype3) && $usertype3 == "STAFF") {echo "selected";}?>>STAFF</option>
                        <option value='FALSE' <?php if (isset($usertype3) && $usertype3 == "FALSE") {echo "selected";}?>>DEACTIVATE</option>
                    </select></td>
                    </tr>

                    <tr>
                    <td style='text-align:right;'><strong>Access Granted </strong></td>
                    <td style='text-align:left;'>
                    <select name="accessgrant1" style="width:50%;border-radius:5px;height:30px;font-size:10pt;">
                        <option value='all' <?php if (isset($accessgrant3) && $accessgrant3 == "all") {echo "selected";}?>>ALL</option>
                        <?php
                            $queryBr = "select 38id, 38title from eg_dept order by 38title";
                            $resultBr = mysqli_query($GLOBALS["conn"],$queryBr);
                            while ($myrowBr=mysqli_fetch_array($resultBr))
                                {
                                    echo "<option value='".$myrowBr["38id"]."' ";
                                        if (isset($accessgrant3) && $accessgrant3 == $myrowBr["38id"]) {echo "selected";}
                                    echo ">".$myrowBr["38title"]."</option>";
                                }
                        ?>

                    </select></td>
                    </tr>

                    <tr>
                        <td colspan='2' style='text-align:center;'>
                        <input type="hidden" name="id1" value="<?php if (isset($id3)) {echo $id3;} ?>" />
                        <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?? '' ?>" />
                        <input type="hidden" name="submitted" value="<?php if (isset($_GET['edt'])) {echo "Update";} else {echo "Insert";}?>" />
                        <input type="submit" class='sButton' name="submit_button" value="<?php if (isset($_GET['edt'])) {echo "Update";} else {echo "Insert";}?>" />
                        </td>
                    </tr>
                </table>
            </form>
            </td></tr>
        </table><br/>
    <?php }//if !isset submitted ?>
    
    <div style='text-align:center;'><a class='sButtonCyan' href='../pg_admin/chanuser.php'><span class='fas fa-arrow-circle-left'></span> Back to user account</a></div>
    
    <hr>

    <?php include_once '../pg_includes/footer.php';?>
    
</body>

</html>
<?php mysqli_close($GLOBALS["conn"]); exit(); ?>
