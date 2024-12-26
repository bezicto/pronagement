<!DOCTYPE HTML>
<?php
    session_start();define('includeExist', true);
    include_once '../core.php';
    include_once '../pg_includes/access_super.php';
    include_once '../pg_includes/functions.php';
    
    $thisPageTitle = "Add $tier3_menu";
    $thisFileName = "adddept.php";
    $table_name = "eg_dept";
    $item_name = $tier3_menu;

    //preventing CSRF
    include_once '../pg_includes/token_validate.php';
?>

<html lang='en'>

<head><?php include_once '../pg_includes/header.php'; ?></head>

<body>
    
    <?php include_once '../pg_includes/loggedinfo.php'; ?>
    
    <hr>
    
    <?php
        
        if (isset($_GET["del"]) && is_numeric($_GET["del"])) {
            $get_id_del = mysqli_real_escape_string($GLOBALS["conn"], $_GET["del"]);

            $stmt_del = $new_conn->prepare("delete from $table_name where 38id = ?");
            $stmt_del->bind_param("i", $get_id_del);
            $stmt_del->execute();
            $stmt_del->close();
        }
        
        if (isset($_REQUEST["submitted"]) && $proceedAfterToken) {
            $title1 = setDefaultForPostVar($_POST["title1"]);
            
            if ($_REQUEST["submitted"] == "Insert") {
                $stmt_count = $new_conn->prepare("select count(*) from $table_name where 38title = ?");
                $stmt_count->bind_param("s", $title1);
                $stmt_count->execute();
                $stmt_count->bind_result($num_results_affected);
                $stmt_count->fetch();
                $stmt_count->close();
                
                if ($num_results_affected == 0) {
                    if (!empty($title1)) {
                        $stmt_insert = $new_conn->prepare("insert into $table_name values(DEFAULT,?)");
                        $stmt_insert->bind_param("s",$title1);
                        $stmt_insert->execute();
                        $stmt_insert->close();
                        echo "<script>window.alert('$title1 has been inputed into the database.');</script>";
                    } else {
                        echo "<script>window.alert('Your input has been cancelled.Check if any field(s) left emptied before posting.');</script>";
                    }
                } elseif ($num_results_affected >= 1) {
                    echo "<script>window.alert('Your input has been cancelled. Duplicate detected.');</script>";
                }
            } elseif ($_REQUEST["submitted"] == "Update") {
                $id1 = $_POST["id1"];

                if (!empty($title1)) {
                    $stmt_update = $new_conn->prepare("update $table_name set 38title=? where 38id=?");
                    $stmt_update->bind_param("si", $title1,$id1);
                    $stmt_update->execute();
                    $stmt_update->close();
                    echo "<script>window.alert('The record has been updated.');</script>";
                } else {
                    echo "<script>window.alert('Error. Please make sure there were no empty field(s).<br/>The record has been restored to it original state.');</script>";
                }
            }
        }

        if (isset($_GET["edt"]) && is_numeric($_GET["edt"])) {
            $get_id_upd = mysqli_real_escape_string($GLOBALS["conn"], $_GET["edt"]);

            $stmt3 = $new_conn->prepare("select 38id, 38title from $table_name where 38id = ?");
            $stmt3->bind_param("i", $get_id_upd);
            $stmt3->execute();
            $stmt3->store_result();
            $stmt3->bind_result($id3, $title3);
            $stmt3->fetch();
            $stmt3->close();
        }
        
    ?>

    <table class=whiteHeader>
        <tr class=yellowHeaderCenter><td colspan=2><strong><?php echo $item_name;?> <?php echo $text_form;?>:</strong></td></tr>
        <tr class=greyHeaderCenter><td colspan=2><br/>
        <form action="<?php echo $thisFileName;?>" method="post" enctype="multipart/form-data">

                <strong><?php echo $item_name;?> Name: </strong>
                <br/><input type="text" name="title1" style='width:50%;border-radius:5px;height:30px;font-size:10pt;' value="<?php if (isset($title3)) {echo $title3;} ?>"/>
    
                <input type="hidden" name="id1" value="<?php if (isset($id3)) {echo $id3;} ?>" />
                <input type="hidden" name="submitted" value="<?php if (isset($_GET['edt'])) {echo "Update";} else {echo "Insert";}?>" />
                
                <br/><br/>
                <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?? '' ?>">
                <input type="submit" class='sButton' name="submit_button" value="<?php if (isset($_GET['edt'])) {echo "Update";} else {echo "Insert";}?>" />
                <input type="button" class='sButtonRed' value="Cancel" onclick="location.href='<?php echo $thisFileName;?>';">

        </form>
        </td></tr>
    </table>
    
    <br/><br/>
    <table class=whiteHeader>
        <tr class=yellowHeaderCenter><td colspan=4><strong><?php echo $item_name;?> <?php echo $text_listing_and_control;?>:</strong></td></tr>
        <tr class=whiteHeaderCenterUnderline>
            <td style='width:5%;'>ID</td>
            <td style='text-align:left;'>Name</td>
            <td style='width:150;'>Options</td>
        </tr>
        <?php
            $n = 1;
            $stmt_fdb = $new_conn->prepare("select 38id, 38title from $table_name");
            $stmt_fdb->execute();
            $result_fdb = $stmt_fdb->get_result();
            while($myrow_fdb = $result_fdb->fetch_assoc())
            {
                $title_fdb = $myrow_fdb["38title"];
                $id_fdb = $myrow_fdb["38id"];

                echo "<tr class=yellowHover>
                    <td>$n</td>
                    <td style='text-align:left;'>$title_fdb</td>
                    <td>
                        <a title='Delete this record' href='$thisFileName?del=$id_fdb' onclick=\"return confirm('Are you sure ? You are advisable to change all items based on this value to the other value before proceeding.');\"><img src='../pg_assets/images/delete.gif'></a>
                         <a title='Update this record' href='$thisFileName?edt=$id_fdb'><img src='../pg_assets/images/pencil.gif'></a>
                    </td>
                </tr>";
                $n = $n + 1;
            }
            $stmt_fdb->close();
        ?>
    </table>
    
    <hr>
    
    <?php include_once '../pg_includes/footer.php';?>
    
</body>

</html>
<?php mysqli_close($GLOBALS["conn"]); exit(); ?>
