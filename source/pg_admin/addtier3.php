<!DOCTYPE HTML>
<?php
    session_start();define('includeExist', true);
    include_once '../core.php';
    include_once '../pg_includes/access_super.php';
    include_once '../pg_includes/functions.php';
    
    $thisPageTitle = "Add $tier3_menu_plural";
    $thisFileName = "addtier3.php";
    $table_name = "eg_tier3";
    $item_name = $tier3_menu;

    if (isset($_GET['tid']) && is_numeric($_GET['tid'])) {
        $_SESSION['tid'] = $_GET['tid'];
    }

    //preventing CSRF
    include_once '../pg_includes/token_validate.php';
?>

<html lang='en'>

<head>
    <?php include_once '../pg_includes/header.php'; ?>
    <style>
    @media only screen and (max-width: 600px) {
        /* Jadikan setiap baris satu blok */
        table.thisRtable {
            border-collapse: collapse;
        }
        
        tr.thisRrow, td.thisRdata {
        display: block;
        text-align: left;
        width: 100%;
        }

        .whiteHeaderCenterUnderline:nth-of-type(2) {
        display: none;
        }

        /* Paparkan tajuk baris sebelum setiap nilai data */
        td.thisRdata::before {
        content: attr(data-label);
        font-weight: bold;
        color: green; /* Warna hijau untuk data-label */
        text-decoration: underline; /* Garisan bawah untuk data-label */
        display: block;
        text-align: left;
        margin-bottom: 5px; /* Ruang antara label dan nilai */
        }

        /* Tambahkan hr selepas setiap baris */
        tr.thisRrow::after {
        content: "";
        display: block;
        width: 100%;
        border-bottom: 1px solid #000;
        margin: 10px 0; /* Ruang di atas dan di bawah garis */
        }

        tr.thisRrow:last-child {
            display: none;
        }

    }
    </style>
</head>

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

            $stmt_del = $new_conn->prepare("delete from eg_item where eg_tier2_id = ?");
            $stmt_del->bind_param("i", $get_id_del);
            $stmt_del->execute();
            $stmt_del->close();
            echo "<script>window.alert('Item has been deleted.');location.href='addtier3.php';</script>";exit;
        }

        if (isset($_GET["tkn"]) && is_numeric($_GET["tkn"])) {
            $get_id_tkn = mysqli_real_escape_string($GLOBALS["conn"], $_GET["tkn"]);
            $accesskey_new = md5(md5(uniqid(mt_rand(), true)).openssl_encrypt(uniqid(time()), "AES-128-CTR",time().$get_id_tkn.mt_rand(),0,"1234567891011121"));

            $stmt_tupdate = $new_conn->prepare("update $table_name set 38accesskey=? where 38id=?");
            $stmt_tupdate->bind_param("si",$accesskey_new,$get_id_tkn);
            $stmt_tupdate->execute();
            $stmt_tupdate->close();
            echo "<script>window.alert('Access URL has been resetted.');location.href='addtier3.php';</script>";exit;
        }

        if (isset($_GET['setacvid']) && is_numeric($_GET['setacvid'])) {
            
            $stmt_fdb = $new_conn->prepare("select 38id, eg_dept_id, 38target, 38targetstatement, 38accesskey, 39achv, 39achv_timestamp from $table_name where eg_tier2_id=".$_SESSION['tid']." and 38id=".$_GET['setacvid']);
            $stmt_fdb->execute();
            $result_fdb = $stmt_fdb->get_result();
            $myrow_fdb = $result_fdb->fetch_assoc();
                $eg_dept_id_fdb = $myrow_fdb["eg_dept_id"];
            
            $stmt_gdb = $new_conn->prepare("select 38field6 from eg_item where eg_tier2_id='".$_SESSION['tid']."' and eg_dept_id='$eg_dept_id_fdb' order by id desc limit 1");
            $stmt_gdb->execute();
            $result_gdb = $stmt_gdb->get_result();
            $myrow_gdb = $result_gdb->fetch_assoc();
                $user_insert_achv_gdb = $myrow_gdb["38field6"] ?? 0;
            
            $timestamp1 = time();
            $stmt_update = $new_conn->prepare("update $table_name set 39achv=?, 39achv_timestamp=? where 38id=? and eg_tier2_id=?");
            $stmt_update->bind_param("dsii",$user_insert_achv_gdb,$timestamp1,$_GET['setacvid'],$_SESSION['tid']);
            $stmt_update->execute();
            $stmt_update->close();
        }
        
        if (isset($_REQUEST["submitted"]) && $proceedAfterToken) {
            $dept1 = mysqli_real_escape_string($GLOBALS["conn"],$_POST["dept1"]);
            $target1 = mysqli_real_escape_string($GLOBALS["conn"],$_POST["target1"]);
            $targetstatement1 = setDefaultForPostVar($_POST["targetstatement1"]);
            $accesskey1 = md5(md5(uniqid(mt_rand(), true)).openssl_encrypt(uniqid(time()), "AES-128-CTR",time().$dept1,0,"1234567891011121"));

            $achv1 = mysqli_real_escape_string($GLOBALS["conn"],$_POST["achv1"]);
            
            if ($_REQUEST["submitted"] == "Insert") {
                $stmt_count = $new_conn->prepare("select count(*) from $table_name where eg_dept_id=? and eg_tier2_id=?");
                $stmt_count->bind_param("ii", $dept1,$_SESSION['tid']);
                $stmt_count->execute();
                $stmt_count->bind_result($num_results_affected);
                $stmt_count->fetch();
                $stmt_count->close();
                
                if ($num_results_affected == 0) {
                    if (!empty($dept1)) {
                        $stmt_insert = $new_conn->prepare("insert into $table_name values(DEFAULT,?,?,?,?,?,?,DEFAULT,DEFAULT)");
                        $stmt_insert->bind_param("iiidss", $_SESSION['pid'], $_SESSION['tid'], $dept1,$target1,$targetstatement1,$accesskey1);
                        $stmt_insert->execute();
                        $stmt_insert->close();
                        echo "<script>window.alert('Item has been inputed into the database.');location.href='addtier3.php';</script>";
                    } else {
                        echo "<script>window.alert('Your input has been cancelled.Check if any field(s) left emptied before posting.');location.href='addtier3.php';</script>";exit;
                    }
                } elseif ($num_results_affected >= 1) {
                    echo "<script>window.alert('Your input has been cancelled. Duplicate detected.');location.href='addtier3.php';</script>";exit;
                }
            } elseif ($_REQUEST["submitted"] == "Update") {
                $id1 = $_POST["id1"];
                $timestamp1 = time();

                if (!empty($dept1)) {
                    $stmt_update = $new_conn->prepare("update $table_name set eg_dept_id=?, 38target=?, 38targetstatement=? where 38id=? and eg_tier2_id=?");
                    $stmt_update->bind_param("idsii",$dept1,$target1,$targetstatement1,$id1,$_SESSION['tid']);
                    $stmt_update->execute();
                    $stmt_update->close();

                    if ($achv1 <> 0) {
                        $stmt_update = $new_conn->prepare("update $table_name set 39achv=?, 39achv_timestamp=? where 38id=? and eg_tier2_id=?");
                        $stmt_update->bind_param("dsii",$achv1,$timestamp1,$id1,$_SESSION['tid']);
                        $stmt_update->execute();
                        $stmt_update->close();
                    }
                    echo "<script>window.alert('The record has been updated.');location.href='addtier3.php';</script>";exit;
                } else {
                    echo "<script>window.alert('Error. Please make sure there were no empty field(s).<br/>The record has been restored to it original state.');location.href='addtier3.php';</script>";exit;
                }
            }
        }

        if (isset($_GET["edt"]) && is_numeric($_GET["edt"])) {
            $get_id_upd = mysqli_real_escape_string($GLOBALS["conn"], $_GET["edt"]);

            $stmt3 = $new_conn->prepare("select 38id, eg_dept_id, 38target, 38targetstatement, 39achv, 39achv_timestamp from $table_name where 38id=? and eg_tier2_id=?");
            $stmt3->bind_param("ii", $get_id_upd,$_SESSION['tid']);
            $stmt3->execute();
            $stmt3->store_result();
            $stmt3->bind_result($id3, $eg_dept_id3, $target3, $targetstatement3, $achv3, $achv_timestamp3);
            $stmt3->fetch();
            $stmt3->close();
        }
        
    ?>

    <?php
        $stmt3_pc = $new_conn->prepare("select sum(39achv) as cur_total from eg_tier3 where eg_tier2_id=?");
        $stmt3_pc->bind_param("i",$_SESSION['tid']);
        $stmt3_pc->execute();
        $stmt3_pc->store_result();
        $stmt3_pc->bind_result($cur_total);
        $stmt3_pc->fetch();
        $stmt3_pc->close();

        $stmt3_in = $new_conn->prepare("select 38id, 38title, 38datestart, 38dateend, 38target, 38targetunit from eg_tier2 where 38id=?");
        $stmt3_in->bind_param("i",$_SESSION['tid']);
        $stmt3_in->execute();
        $stmt3_in->store_result();
        $stmt3_in->bind_result($id3a, $title3a, $datestart3a, $dateend3a, $target3a, $targetunit3a);
        $stmt3_in->fetch();
        $stmt3_in->close();

        $stmt3_pct3 = $new_conn->prepare("select sum(38target) as cur_total from eg_tier3 where eg_tier2_id=?");
        $stmt3_pct3->bind_param("i",$_SESSION['tid']);
        $stmt3_pct3->execute();
        $stmt3_pct3->store_result();
        $stmt3_pct3->bind_result($cur_target_total);
        $stmt3_pct3->fetch();
        $stmt3_pct3->close();
        
        $current_percentage = 0;
        if ($target3a <> 0) {
            $current_percentage = round(($cur_total/$target3a)*100,2);
            if ($current_percentage >= 100) {
                $current_percentage = 100;
            }
        }

        if (isset($targetunit3a) && ($targetunit3a=='decimalpoint' || $targetunit3a=='percentage')) {
            $stepin = "step='.01'";
        } else {
            $stepin = "";
        }

        //update achievement in tier2 table whenever possible
        $stmt_update_achv = $new_conn->prepare("update eg_tier2 set 39cur_achv=? where 38id=?");
        $stmt_update_achv->bind_param("di", $current_percentage, $_SESSION['tid']);
        $stmt_update_achv->execute();
        $stmt_update_achv->close();
    ?>
    <table class='whiteHeader'>
        <tr>
            <td style="background-color:blue;color:white;width:100%;"><?php echo $tier1_menu;?>:<br/> <?php echo getTitleNameFromID("eg_tier1", $_SESSION['pid']);?></td>
        </tr>
    </table>
    <table class='whiteHeader'>
        <tr>
            <td style="background-color:green;color:white;text-align:left;">
                <span style='color:yellow;'><?php echo $tier2_menu;?>:</span> <?php echo getTitleNameFromID("eg_tier2", $_SESSION['tid']);?>
                <br/><span style='color:yellow;'><?php echo $tier2_target;?>:</span> <?php echo "$target3a ($targetunit3a)";?>
                <br/><span style='color:yellow;'><?php echo $text_duration;?>:</span> <?php echo "$datestart3a - $dateend3a";?>
            </td>
            <td style="background-color:green;color:white;width:5%;font-size:18pt;">
                <?php
                    echo "$current_percentage%";
                ?>
            </td>
        </tr>
    </table>
    <br/>
    
    <?php if ((isset($_GET['show']) && $_GET['show'] == 'form') || (isset($_GET['edt']) && is_numeric($_GET['edt']))) {?>
    <table class=whiteHeader>
        <tr class=yellowHeaderCenter><td colspan=2><strong><?php echo $item_name;?> <?php echo $text_form;?>:</strong></td></tr>
        <tr class=greyHeaderCenter><td colspan=2><br/>
        <form action="<?php echo $thisFileName;?>" method="post" enctype="multipart/form-data">
                <strong><?php echo $item_name;?></strong>
                <br/>
                <select name="dept1" style='border-radius:5px;height:30px;font-size:10pt;'>
                    <option value='0'>--Select one--</option>
                    <?php
                        $queryB = "select 38id, 38title from eg_dept order by 38title";
                        $resultB = mysqli_query($GLOBALS["conn"],$queryB);
                        while ($myrowB=mysqli_fetch_array($resultB))
                            {
                                echo "<option value='".$myrowB["38id"]."' ";
                                    if (isset($eg_dept_id3) && $eg_dept_id3 == $myrowB["38id"]) {echo "selected";}
                                echo ">".$myrowB["38title"]."</option>";
                            }
                    ?>
                </select>

                <br/><br/><strong><?php echo $tier3_target_statement;?>: </strong>
                <br/>
                <textarea name="targetstatement1" style="width:50%;border-radius:5px;font-size:10pt;" rows="5"><?php if (isset($targetstatement3)) {echo $targetstatement3;} ?></textarea>

                <br/><br/><strong><?php echo $tier3_target_setting;?>: (<?php echo $targetunit3a;?>, <span style='color:magenta;'>max value: <?php echo $target3a-$cur_target_total;?></span>)</strong>

                <br/><input type="number" <?php echo $stepin;?> name="target1" style='width:150px;border-radius:5px;height:30px;font-size:10pt;' value="<?php if (isset($target3)) {echo $target3;} ?>"/>
                
                <?php if ($_SESSION['editmode'] == 'SUPER') { ?>
                <br/><br/>
                <table style='margin-left:auto;margin-right:auto;background-color:lightpink;width:350px;border-radius: 10px;'>
                        <tr><td>
                            <u>Achievement Override</u><br/><br/>
                            <strong>Value: (<?php echo $targetunit3a;?>)</strong>
                            <br/><input type="number" <?php echo $stepin;?> name="achv1" style='width:150px;border-radius:5px;height:30px;font-size:10pt;' value="<?php if (isset($achv3)) {echo $achv3;} ?>"/>
                            <br/>Last updated: <?php if (isset($achv_timestamp3) && $achv_timestamp3 <> '1577836800') {echo date('Y-m-d',$achv_timestamp3);} else {echo "N/A";} ?>
                        </td></tr>
                </table>
                <?php }?>
    
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
    <?php }?>
    
    <?php if (!isset($_GET["edt"])) {?>
    <table class='whiteHeader thisRtable'>
        <tr class=yellowHeaderCenter><td colspan=<?php if ($need_verification) {echo "10";} else {echo "9";} ?>><strong><?php echo $item_name;?> <?php echo $text_listing_and_control;?>: </strong>[<a href='addtier3.php?show=form'>Add new</a>]</td></tr>
        <tr class=whiteHeaderCenterUnderline>
            <td style='width:5%;'>ID</td>
            <td style='text-align:left;'><?php echo $item_name;?></td>
            <td style='text-align:left;'><?php echo $tier3_target_statement;?></td>
            <td style='text-align:left;'><?php echo $tier3_target_setting;?></td>
            <td style='text-align:left;color:blue;'><?php echo $text_current_achievement;?></td>
            <?php if ($need_verification) {?>
                <td style='text-align:left;color:orange;'><?php echo $text_user_submitted_achievement;?></td>
            <?php }?>
            <td style='text-align:left;color:blue;'><?php echo $text_balance;?></td>
            <td style='text-align:left;color:blue;'><?php echo $text_updated;?></td>
            <td style='width:150;'><?php echo $text_options;?></td>
            <td style='text-align:left;'><?php echo $text_progress_report;?></td>
        </tr>
        <?php
            $n = 1;
            
            $total_1 = 0;
            $total_2 = 0;
            $total_3 = 0;
            $total_2a = 0;

            $stmt_fdb = $new_conn->prepare("select 38id, eg_dept_id, 38target, 38targetstatement, 38accesskey, 39achv, 39achv_timestamp  from $table_name where eg_tier2_id=".$_SESSION['tid']);
            $stmt_fdb->execute();
            $result_fdb = $stmt_fdb->get_result();
            while($myrow_fdb = $result_fdb->fetch_assoc())
            {
                $eg_dept_id_fdb = $myrow_fdb["eg_dept_id"];
                $target_fdb = $myrow_fdb["38target"];
                $targetstatement_fdb = $myrow_fdb["38targetstatement"];
                $accesskey_fdb = $myrow_fdb["38accesskey"];
                $achv_fdb = $myrow_fdb["39achv"];
                $achv_timestamp_fdb = $myrow_fdb["39achv_timestamp"];
                $id_fdb = $myrow_fdb["38id"];
                
                $total_1 = $total_1 + $target_fdb;
                $total_2 = $total_2 + $achv_fdb;

                $stmt_gdb = $new_conn->prepare("select 38field6 from eg_item where eg_tier2_id='".$_SESSION['tid']."' and eg_dept_id='$eg_dept_id_fdb' order by id desc");
                $stmt_gdb->execute();
                $result_gdb = $stmt_gdb->get_result();
                $myrow_gdb = $result_gdb->fetch_assoc();
                $field6_gdb = $myrow_gdb["38field6"] ?? 0;
                    $total_2a = $total_2a + $field6_gdb;

                echo "<tr class='yellowHover thisRrow'>";
                    echo "<td>$n</td>";
                    echo "<td data-label='$item_name' class='thisRdata' style='text-align:left;'>".getTitleNameFromID("eg_dept", $eg_dept_id_fdb)."</td>";
                    echo "<td data-label='$tier3_target_statement' class='thisRdata' style='text-align:left;'>$targetstatement_fdb</td>";
                    echo "<td data-label='$tier3_target_setting' class='thisRdata' style='text-align:left;'>$target_fdb</td>";
                    echo "<td data-label='$text_current_achievement' class='thisRdata' style='text-align:left;color:blue;'>$achv_fdb </td>";
                    if ($need_verification) {
                        echo "<td data-label='$text_user_submitted_achievement' class='thisRdata' style='text-align:left;color:orange;'>$field6_gdb ";
                        if ($field6_gdb > $achv_fdb) {
                            echo "<a onclick=\"return confirm('Are you sure to agree with this user submitted value?');\" href='addtier3.php?setacvid=$id_fdb' title='Set as current achievement'><img src='../pg_assets/images/undo.png' width=14px></a>";
                        }
                        echo "</td>";
                    }
                    echo "<td data-label='$text_balance' class='thisRdata' style='text-align:left;color:blue;'>".$target_fdb-$achv_fdb."</td>";
                    echo "<td data-label='$text_updated' class='thisRdata' style='text-align:left;color:blue;'>";
                        if ($achv_timestamp_fdb <> '1577836800') {
                            echo date('Y-m-d',$achv_timestamp_fdb);
                        } else {
                            echo "N/A";
                        }
                    echo "</td>";
                    echo "<td data-label='$text_options' class='thisRdata'>";
                        echo "<a title='Delete this record' href='$thisFileName?del=$id_fdb' onclick=\"return confirm('Are you sure ? You are advisable to change all items based on this value to the other value before proceeding.');\"><img width=16 src='../pg_assets/images/delete.gif'></a> ";
                        echo "<a title='Update this record' href='$thisFileName?edt=$id_fdb'><img width=16 src='../pg_assets/images/pencil.gif'></a> ";
                        echo "<a title='Reset token' href='$thisFileName?tkn=$id_fdb' onclick=\"return confirm('Are you sure ? URL will be resetted.');\"><img width=16 src='../pg_assets/images/tokenkey.png'></a>";
                    echo "</td>";
                    echo "<td data-label='$text_progress_report' class='thisRdata' style='text-align:left;'><a href='reg.php?token=$accesskey_fdb' onclick='return openPopup(this.href,950,580);'>&plusmn; Progress Evidence</a></td>";
                echo "</tr>";
                $n = $n + 1;
            }
            $stmt_fdb->close();

            $total_3 = $total_1 - $total_2;
        ?>
        <tr class='thisRrow' style='background-color:lightgrey;'>
            <td class='thisRdata' colspan=3 style='text-align:right;'>Total:</td>
            <td class='thisRdata' style='text-align:left;color:red;'><?php echo $total_1;?></td>
            <td class='thisRdata' style='text-align:left;color:red;'><?php echo $total_2;?></td>
            <?php if ($need_verification) { ?>
                <td class='thisRdata' style='text-align:left;color:orange;'><?php echo $total_2a;?></td>
            <?php } ?>
            <td class='thisRdata' style='text-align:left;color:red;'><?php echo $total_3;?></td>
            <td class='thisRdata' colspan=3></td>
        </tr>
    </table>
    <?php }?>
    
    <hr>
    
    <?php if (!isset($_GET['edt'])) {?>
    <div style='text-align:center;margin-top:10px;margin-bottom:10px;'>
        <a class='sButtonCyan' href='addtier2.php'><span class='fas fa-arrow-circle-left'></span> Back to <?php echo $tier2_menu_plural;?></a>
    </div>
    <?php }?>
    
    <?php include_once '../pg_includes/footer.php';?>
    
</body>

</html>
<?php mysqli_close($GLOBALS["conn"]); exit(); ?>
