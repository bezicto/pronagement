<!DOCTYPE HTML>
<?php
    session_start();define('includeExist', true);
    include_once '../core.php';
    include_once '../pg_includes/access_isset.php';
    include_once '../pg_includes/functions.php';
    
    $thisPageTitle = "$tier3_menu Documentation Handler List";
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

    <table class=whiteHeader>
        <tr class=yellowHeaderCenter><td colspan=4><strong><?php echo $item_name;?> <?php echo $text_listing_and_control;?>:</strong></td></tr>
        <tr class=whiteHeaderCenterUnderline>
            <td style='width:5%;'>ID</td>
            <td style='text-align:left;'>Name</td>
            <td style='text-align:left;'>Links and <?php echo $tier2_menu_plural;?></td>
        </tr>
        <?php
            $n = 1;

            $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://".$_SERVER['HTTP_HOST'] . substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['REQUEST_URI'], "/"));

            $appendsql = "";
            if ($_SESSION['accessgranted'] != 'all') {
                $appendsql = "and 38id=".$_SESSION['accessgranted'];
            }

            //detailing
            $stmt_fdb = $new_conn->prepare("select 38id, 38title from $table_name where 38id<>0 $appendsql");
            $stmt_fdb->execute();
            $result_fdb = $stmt_fdb->get_result();
            while($myrow_fdb = $result_fdb->fetch_assoc())
            {
                $title_fdb = $myrow_fdb["38title"];
                $id_fdb = $myrow_fdb["38id"];

                echo "<tr class=yellowHover>";
                    echo "<td style='vertical-align:top;width:5%;'>$n</td>";
                    echo "<td style='text-align:left;vertical-align:top;width:35%'>$title_fdb</td>";
                    echo "<td style='text-align:left;'>";
                        
                        if (!isset($_GET['sel'])) {
                            $stmt_gdb = $new_conn->prepare("select eg_tier1_id,eg_tier2_id,38accesskey,38target,39achv from eg_tier3 where eg_dept_id=$id_fdb group by eg_tier1_id");
                        } elseif (isset($_GET['sel']) && is_numeric($_GET['sel'])) {
                            $stmt_gdb = $new_conn->prepare("select eg_tier1_id,eg_tier2_id,38accesskey,38target,39achv from eg_tier3 where eg_dept_id=$id_fdb and eg_tier1_id<>0 and eg_tier1_id=".$_GET['sel']);
                        }
                        $stmt_gdb->execute();
                        $result_gdb = $stmt_gdb->get_result();
                        while($myrow_gdb = $result_gdb->fetch_assoc())
                        {
                            $accesskey_fdb = $myrow_gdb["38accesskey"];
                            $eg_tier1_id = $myrow_gdb["eg_tier1_id"];
                            $eg_tier2_id = $myrow_gdb["eg_tier2_id"];
                            $starget = $myrow_gdb["38target"];
                            $sachv = $myrow_gdb["39achv"];

                            if (!isset($_GET['sel'])) {
                                echo "<li><a href='dept_evd.php?sel=$eg_tier1_id' style='color:blue;'>".getTitleNameFromID("eg_tier1",$eg_tier1_id)."</a></li>";
                            } elseif (isset($_GET['sel']) && is_numeric($_GET['sel'])) {
                                echo "<div style='background-color:lightgrey;'>
                                    <span style='color:blue;'>".getTitleNameFromID("eg_tier1",$eg_tier1_id)." > ".getTitleNameFromID("eg_tier2",$eg_tier2_id)." > </span>
                                    <br/>".getDescFromID("eg_tier2",$eg_tier2_id)."
                                    <li>Target: <span style='color:green;'>$starget</span></li>
                                    <li>Endorsed Achievement: <span style='color:red;'>$sachv</span></li>
                                    <li><a href='$actual_link"."/reg.php?token=$accesskey_fdb' onclick='return openPopup(this.href,950,580);'>$actual_link"."/reg.php?token=$accesskey_fdb</a></li><br/><br/>
                                </div>";
                            }
                        }
                    echo "</td>";
                echo "</tr>";
                $n = $n + 1;
            }
            $stmt_fdb->close();

        ?>
    </table>
    
    <hr>

    <?php
    if (isset($_GET['sel']) && is_numeric($_GET['sel'])) {
    ?>
        <div style='text-align:center;margin-top:10px;margin-bottom:10px;'>
            <a class='sButtonCyan' href='javascript:history.go(-1);'><span class='fas fa-arrow-circle-left'></span> Back</a>
        </div>
    <?php
    }
    ?>
    
    <?php include_once '../pg_includes/footer.php';?>
    
</body>

</html>
<?php mysqli_close($GLOBALS["conn"]); exit(); ?>
