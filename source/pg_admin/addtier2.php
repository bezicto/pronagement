<!DOCTYPE HTML>
<?php
    session_start();define('includeExist', true);
    include_once '../core.php';
    include_once '../pg_includes/access_super.php';
    include_once '../pg_includes/functions.php';
    
    $thisPageTitle = "Add $tier2_menu_plural";
    $thisFileName = "addtier2.php";
    $table_name = "eg_tier2";
    $item_name = $tier2_menu;

    if (isset($_GET['pid']) && is_numeric($_GET['pid'])) {
        $_SESSION['pid'] = $_GET['pid'];
    }

    //preventing CSRF
    include_once  '../pg_includes/token_validate.php';
?>

<html lang='en'>

<head>
    <?php include_once  '../pg_includes/header.php'; ?>
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

        .whiteHeaderCenterUnderline:nth-of-type(1),
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

        /* Center the last TD */
        td.thisRdata[colspan="7"] {
            text-align: center !important;
            display: block;
            width: 100%;
        }

        /* Ensure the completion row is centered */
        tr.thisRrow:last-child td.thisRdata:last-child {
            text-align: center !important;
        }
    }
    </style>
</head>

<body>
    
    <?php
        if (!isset($_GET['show']) || (isset($_GET['show']) && $_GET['show'] != 'print')) {
            include_once  '../pg_includes/loggedinfo.php';
        }
    ?>
    
    <hr>
    
    <?php
        
        if (isset($_GET["del"]) && is_numeric($_GET["del"])) {
            $get_id_del = mysqli_real_escape_string($GLOBALS["conn"], $_GET["del"]);

            $stmt_del = $new_conn->prepare("delete from $table_name where 38id = ?");
            $stmt_del->bind_param("i", $get_id_del);
            $stmt_del->execute();
            $stmt_del->close();

            //delete at related table too
            $stmt_del = $new_conn->prepare("delete from eg_tier3 where eg_tier2_id = ?");
            $stmt_del->bind_param("i", $get_id_del);
            $stmt_del->execute();
            $stmt_del->close();

            $stmt_del = $new_conn->prepare("delete from eg_item where eg_tier2_id = ?");
            $stmt_del->bind_param("i", $get_id_del);
            $stmt_del->execute();
            $stmt_del->close();
        }
        
        if (isset($_REQUEST["submitted"]) && $proceedAfterToken) {
            $title1 = setDefaultForPostVar($_POST["title1"]);
            $desc1 = setDefaultForPostVar($_POST["desc1"]);
            $tasktype1 = setDefaultForPostVar($_POST["tasktype1"]);
            $datestart1 = mysqli_real_escape_string($GLOBALS["conn"],$_POST["datestart1"]);
            $dateend1 = mysqli_real_escape_string($GLOBALS["conn"],$_POST["dateend1"]);
            $target1 = mysqli_real_escape_string($GLOBALS["conn"],$_POST["target1"]);
            $targetunit1 = mysqli_real_escape_string($GLOBALS["conn"],$_POST["targetunit1"]);
            
            if ($_REQUEST["submitted"] == "Insert") {
                $stmt_count = $new_conn->prepare("select count(*) from $table_name where 38title=? and eg_tier1_id=?");
                $stmt_count->bind_param("si", $title1,$_SESSION['pid']);
                $stmt_count->execute();
                $stmt_count->bind_result($num_results_affected);
                $stmt_count->fetch();
                $stmt_count->close();
                
                if ($num_results_affected == 0) {
                    if (!empty($title1)) {
                        $stmt_insert = $new_conn->prepare("insert into $table_name values(DEFAULT,?,?,?,?,?,?,?,?,DEFAULT)");
                        $stmt_insert->bind_param("isssssds", $_SESSION['pid'], $title1,$desc1,$tasktype1,$datestart1,$dateend1,$target1,$targetunit1);
                        $stmt_insert->execute();
                        $stmt_insert->close();
                        echo "<script>window.alert('$title1 has been inputed into the database.');location.href='addtier2.php';</script>";exit;
                    } else {
                        echo "<script>window.alert('Your input has been cancelled.Check if any field(s) left emptied before posting.');location.href='addtier2.php';</script>";exit;
                    }
                } elseif ($num_results_affected >= 1) {
                    echo "<script>window.alert('Your input has been cancelled. Duplicate detected.');location.href='addtier2.php';</script>";exit;
                }
            } elseif ($_REQUEST["submitted"] == "Update") {
                $id1 = $_POST["id1"];

                if (!empty($title1)) {
                    $stmt_update = $new_conn->prepare("update $table_name set 38title=?, 38desc=?, 38tasktype=?, 38datestart=?, 38dateend=?, 38target=?, 38targetunit=? where 38id=? and eg_tier1_id=?");
                    $stmt_update->bind_param("sssssdsii",$title1,$desc1,$tasktype1,$datestart1,$dateend1,$target1,$targetunit1,$id1,$_SESSION['pid']);
                    $stmt_update->execute();
                    $stmt_update->close();
                    echo "<script>window.alert('The record has been updated.');location.href='addtier2.php';</script>";exit;
                } else {
                    echo "<script>window.alert('Error. Please make sure there were no empty field(s).<br/>The record has been restored to it original state.');location.href='addtier2.php';</script>";exit;
                }
            }
        }

        if (isset($_GET["edt"]) && is_numeric($_GET["edt"])) {
            $get_id_upd = mysqli_real_escape_string($GLOBALS["conn"], $_GET["edt"]);

            $stmt3 = $new_conn->prepare("select 38id, 38title, 38desc, 38tasktype, 38datestart, 38dateend, 38target, 38targetunit from $table_name where 38id=? and eg_tier1_id=?");
            $stmt3->bind_param("ii", $get_id_upd,$_SESSION['pid']);
            $stmt3->execute();
            $stmt3->store_result();
            $stmt3->bind_result($id3, $title3, $desc3, $tasktype3, $datestart3, $dateend3, $target3, $targetunit3);
            $stmt3->fetch();
            $stmt3->close();
        }
        
    ?>

    <table class=whiteHeader>
        <tr><td style="background-color:blue;color:white;">
            <?php echo $tier1_menu;?>:<br/> <?php echo getTitleNameFromID("eg_tier1", $_SESSION['pid']);?>
            <br/><br/><?php echo $text_date;?>: <br/> <?php echo getDateFromID("eg_tier1", $_SESSION['pid']);?>
            <br/><br/><?php echo $text_participant;?>: <br/> <?php echo getParticipantFromID("eg_tier1", $_SESSION['pid']);?>
        </td></tr>
    </table>
    <br/>
    
    <?php if ((isset($_GET['show']) && $_GET['show'] == 'form') || (isset($_GET['edt']) && is_numeric($_GET['edt']))) {?>
    <table class=whiteHeader>
        <tr class=yellowHeaderCenter><td colspan=2><strong><?php echo $item_name;?> <?php echo $text_form;?>:</strong></td></tr>
        <tr class=greyHeaderCenter><td colspan=2><br/>
        <form action="<?php echo $thisFileName;?>" method="post" enctype="multipart/form-data">
                <strong><?php echo $tier2_menu;?>:</strong>
                <br/><input type="text" name="title1" style="width:50%;border-radius:5px;height:30px;font-size:10pt;" value="<?php if (isset($title3)) {echo $title3;} ?>"/>

                <br/><br/><strong><?php echo $tier2_menu_desc;?>: </strong>
                <br/>
                <textarea name="desc1" style="width:50%;border-radius:5px;font-size:10pt;" rows="5"><?php if (isset($desc3)) {echo $desc3;} ?></textarea>

                <br/><br/><strong><?php echo $text_type;?>: </strong>
                <br/>
                <select name="tasktype1" style='border-radius:5px;height:30px;font-size:10pt;'>
                    <option value="targetsetting" <?php if (isset($tasktype3) && $tasktype3=='targetsetting') {echo "selected";} ?>>Require target setting (see below)</option>
                    <option value="statement" <?php if (isset($tasktype3) && $tasktype3=='statement') {echo "selected";} ?>>Statement only - without target setting</option>
                </select>

                <table style='margin-top:20px;width:30%;margin-left:auto;margin-right:auto;'>
                    <tr><td><hr><?php echo $text_target_setting;?></td></tr>
                </table>

                <br/><br/><strong><?php echo $tier2_datestart;?>: </strong>
                <br/><input type="date" name="datestart1" style='border-radius:5px;height:30px;font-size:10pt;' value="<?php if (isset($datestart3)) {echo $datestart3;} ?>"/>

                <br/><br/><strong><?php echo $tier2_dateend;?>: </strong>
                <br/><input type="date" name="dateend1" style='border-radius:5px;height:30px;font-size:10pt;' value="<?php if (isset($dateend3)) {echo $dateend3;} ?>"/>

                <?php
                if (isset($targetunit3) && ($targetunit3=='decimalpoint' || $targetunit3=='percentage')) {
                    $stepin = "step='.01'";
                } else {
                    $stepin = "";
                }
                ?>
                <br/><br/><strong><?php echo $tier2_target;?>: </strong>
                <br/><input type="number" <?php echo $stepin;?> name="target1" style="border-radius:5px;height:30px;font-size:10pt;" value="<?php if (isset($target3)) {echo $target3;} ?>"/>
                <select name="targetunit1" style='border-radius:5px;height:30px;font-size:10pt;'>
                    <option value="integer" <?php if (isset($targetunit3) && $targetunit3=='integer') {echo "selected";} ?>>Integer</option>
                    <option value="decimalpoint" <?php if (isset($targetunit3) && $targetunit3=='decimalpoint') {echo "selected";} ?>>Decimal Point</option>
                    <option value="percentage" <?php if (isset($targetunit3) && $targetunit3=='percentage') {echo "selected";} ?>>Percentage</option>
                </select>
    
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
    
    
    <?php
    if (!isset($_GET["edt"])) {
    ?>

        <?php
        if (!isset($_GET['show']) || (isset($_GET['show']) && $_GET['show'] != 'print')) {
        ?>
        <br/><table class=whiteHeader>
            <tr class=yellowHeaderCenter><td colspan=10><strong><?php echo $item_name;?> <?php echo $text_listing_and_control;?>: </strong>[<a href='addtier2.php?show=form'>Add new</a>] [<a href='addtier2.php?show=print'>Print This Page</a>]</td></tr>
        </table>
        <?php
        }

        //for target setting
        $stmt_fdb = $new_conn->prepare("select 38id, 38title, 38desc, 38datestart, 38dateend, 38target, 38targetunit from $table_name where 38tasktype='targetsetting' and eg_tier1_id=".$_SESSION['pid']);
        $stmt_fdb->execute();
        $result_fdb = $stmt_fdb->get_result();
        if ($result_fdb->num_rows >= 1) {
        ?>
        
        <div style="overflow-x:auto;">
        <table class='whiteHeader thisRtable'>
            <tr class=whiteHeaderCenterUnderline>
                <td colspan=10 style='background-color:lightyellow;'><?php echo $text_target_listing;?></td>
            </tr>
            <tr class=whiteHeaderCenterUnderline>
                <td style='width:5%;'>ID</td>
                <td style='text-align:left;'><?php echo $tier2_menu;?></td>
                <td style='text-align:left;'><?php echo $tier2_menu_desc;?></td>
                <td style='text-align:left;'><?php echo $tier2_datestart;?></td>
                <td style='text-align:left;'><?php echo $tier2_dateend;?></td>
                <td style='text-align:left;'><?php echo $tier2_target;?></td>
                <td style='text-align:left;'><?php echo $tier2_target_unit;?></td>
                <td style='text-align:left;'><span style='color:blue;'><?php echo $text_current_achievement;?></span></td>
                <?php
                if (!isset($_GET['show']) || (isset($_GET['show']) && $_GET['show'] != 'print')) {
                ?>
                    <td style='width:150;'><?php echo $text_options;?></td>
                    <td style='text-align:left;'><?php echo $tier3_menu_plural;?></td>
                <?php }?>
            </tr>
            <?php
                
                $n = 1;
                while($myrow_fdb = $result_fdb->fetch_assoc())
                {
                    $title_fdb = $myrow_fdb["38title"];
                    $desc_fdb = $myrow_fdb["38desc"];
                    $id_fdb = $myrow_fdb["38id"];
                    $datestart_fdb = $myrow_fdb["38datestart"];
                    $dateend_fdb = $myrow_fdb["38dateend"];
                    $target_fdb = $myrow_fdb["38target"];
                    $targetunit_fdb = $myrow_fdb["38targetunit"];
                    
                    $stmt3_pc = $new_conn->prepare("select sum(39achv) as cur_total from eg_tier3 where eg_tier2_id=?");
                    $stmt3_pc->bind_param("i",$id_fdb);
                    $stmt3_pc->execute();
                    $stmt3_pc->store_result();
                    $stmt3_pc->bind_result($cur_total);
                    $stmt3_pc->fetch();
                    $stmt3_pc->close();

                    $current_percentage = 0;
                    if ($target_fdb <> 0) {
                        $current_percentage = round(($cur_total/$target_fdb)*100,2);
                        if ($current_percentage >= 100) {
                            $current_percentage = 100;
                        }
                    }
                    $current_percentage .= "%";

                    echo "<tr class='yellowHover thisRrow'>";
                        echo "<td>$n</td>";
                        echo "<td data-label='$tier2_menu' class='thisRdata' style='text-align:left;'>$title_fdb</td>";
                        echo "<td data-label='$tier2_menu_desc' class='thisRdata' style='text-align:left;'>$desc_fdb</td>";
                        echo "<td data-label='$tier2_datestart' class='thisRdata' style='text-align:left;'>$datestart_fdb</td>";
                        echo "<td data-label='$tier2_dateend' class='thisRdata' style='text-align:left;'>$dateend_fdb</td>";
                        echo "<td data-label='$tier2_target' class='thisRdata' style='text-align:left;'>$target_fdb</td>";
                        echo "<td data-label='$tier2_target_unit' class='thisRdata' style='text-align:left;'>$targetunit_fdb</td>";
                        echo "<td data-label='$text_current_achievement' class='thisRdata' style='text-align:left;'>$current_percentage</td>";
                        if (!isset($_GET['show']) || (isset($_GET['show']) && $_GET['show'] != 'print')) {
                            echo "<td data-label='$text_options' class='thisRdata'>";
                                echo "<a title='Delete this record' href='$thisFileName?del=$id_fdb' onclick=\"return confirm('Are you sure ? You are advisable to change all items based on this value to the other value before proceeding.');\"><img src='../pg_assets/images/delete.gif'></a> ";
                                echo "<a title='Update this record' href='$thisFileName?edt=$id_fdb'><img src='../pg_assets/images/pencil.gif'></a>";
                            echo "</td>";
                            
                            echo "<td data-label='$tier3_menu_plural' class='thisRdata' style='text-align:left;'>";
                            $stmt3r = $new_conn->prepare("select eg_dept_id from eg_tier3 where eg_tier2_id=$id_fdb");
                            $stmt3r->execute();
                            $result_gdb = $stmt3r->get_result();
                            while($myrow_gdb = $result_gdb->fetch_assoc())
                            {
                                $dept_gdb = $myrow_gdb["eg_dept_id"];
                                echo getTitleNameFromID("eg_dept",$dept_gdb).",";
                            }
                            if ($result_gdb->num_rows >= 1) {
                                echo "<br/><br/>";
                            }
                                echo "<a href='addtier3.php?tid=$id_fdb'>&plusmn; Configure $tier3_menu_plural</a>";
                            echo "</td>";
                        }
                        
                    echo "</tr>";
                    $n = $n + 1;
                }
                $stmt_fdb->close();

                $stmt3_pct = $new_conn->prepare("select sum(39cur_achv) as sumtotal, count(38id) as totaltasks from eg_tier2 where eg_tier1_id=? and 38tasktype='targetsetting'");
                $stmt3_pct->bind_param("i", $_SESSION['pid']);
                $stmt3_pct->execute();
                $stmt3_pct->store_result();
                $stmt3_pct->bind_result($cur_achv_pct,$total_project_pct);
                $stmt3_pct->fetch();
                $stmt3_pct->close();

                if ($cur_achv_pct <>0 && $total_project_pct<>0) {
                    $completion = round(($cur_achv_pct/($total_project_pct*100))*100,2);
                } else {
                    $completion = 0;
                }
                echo "<tr class='thisRrow' style='background-color:lightgrey;'>
                        <td class='thisRdata' colspan=7 style='text-align:right;'>Completion:</td>
                        <td class='thisRdata' colspan=3 style='text-align:left;color:red;'>$completion%</td>
                    </tr>";
            ?>
        </table>
        </div>
        <?php
        }
        ?>

        <?php
        //for statements
        $stmt_fdb = $new_conn->prepare("select 38id, 38title, 38desc, 38datestart, 38dateend, 38target, 38targetunit from $table_name where 38tasktype='statement' and eg_tier1_id=".$_SESSION['pid']);
        $stmt_fdb->execute();
        $result_fdb = $stmt_fdb->get_result();
        if ($result_fdb->num_rows >= 1) {
        ?>
        <br/><br/>
        <div style="overflow-x:auto;">
        <table class=whiteHeader>
            <tr class=whiteHeaderCenterUnderline>
                <td colspan=10 style='background-color:lightyellow;'>Statements</td>
            </tr>
            <tr class=whiteHeaderCenterUnderline>
                <td style='width:5%;'>ID</td>
                <td style='text-align:left;'><?php echo $tier2_menu;?></td>
                <td style='text-align:left;'><?php echo $tier2_menu_desc;?></td>
                <?php
                if (!isset($_GET['show']) || (isset($_GET['show']) && $_GET['show'] != 'print')) {
                ?>
                    <td style='width:150;'>Options</td>
                <?php
                }
                ?>
            </tr>
            <?php
                
                $n = 1;
                while($myrow_fdb = $result_fdb->fetch_assoc())
                {
                    $title_fdb = $myrow_fdb["38title"];
                    $desc_fdb = $myrow_fdb["38desc"];
                    $id_fdb = $myrow_fdb["38id"];
                    $datestart_fdb = $myrow_fdb["38datestart"];
                    $dateend_fdb = $myrow_fdb["38dateend"];
                    $target_fdb = $myrow_fdb["38target"];
                    $targetunit_fdb = $myrow_fdb["38targetunit"];
                    
                    $stmt3_pc = $new_conn->prepare("select sum(39achv) as cur_total from eg_tier3 where eg_tier2_id=?");
                    $stmt3_pc->bind_param("i",$id_fdb);
                    $stmt3_pc->execute();
                    $stmt3_pc->store_result();
                    $stmt3_pc->bind_result($cur_total);
                    $stmt3_pc->fetch();
                    $stmt3_pc->close();

                    $current_percentage = 0;
                    if ($target_fdb <> 0) {
                            $current_percentage = round(($cur_total/$target_fdb)*100,2);
                    }
                    $current_percentage .= "%";

                    echo "<tr class=yellowHover>";
                        echo "<td>$n</td>";
                        echo "<td style='text-align:left;'>$title_fdb</td>";
                        echo "<td style='text-align:left;'>$desc_fdb</td>";
                        if (!isset($_GET['show']) || (isset($_GET['show']) && $_GET['show'] != 'print')) {
                            echo "<td>";
                                echo "<a title='Delete this record' href='$thisFileName?del=$id_fdb' onclick=\"return confirm('Are you sure ? You are advisable to change all items based on this value to the other value before proceeding.');\"><img src='../pg_assets/images/delete.gif'></a> ";
                                echo "<a title='Update this record' href='$thisFileName?edt=$id_fdb'><img src='../pg_assets/images/pencil.gif'></a>";
                            echo "</td>";
                        }
                    echo "</tr>";
                    $n = $n + 1;
                }
                $stmt_fdb->close();
            ?>
        </table>
        </div>
        <?php
        }
        ?>
    <?php }?>
    
    <hr>
    
    <?php if (!isset($_GET['edt'])) {
        if (!isset($_GET['show']) || (isset($_GET['show']) && $_GET['show'] != 'print')) {
    ?>
        <div style='text-align:center;margin-top:10px;margin-bottom:10px;'>
            <a class='sButtonCyan' href='addtier1.php'><span class='fas fa-arrow-circle-left'></span> Back to <?php echo $tier1_menu;?></a>
        </div>
    <?php } else {
    ?>
        <div style='text-align:center;margin-top:10px;margin-bottom:10px;'>
            <a class='sButtonCyan' href='addtier2.php'><span class="fas fa-window-close"></span> Close Print Preview</a>
        </div>
    <?php
    }
    }
    ?>
    
    <?php include_once  '../pg_includes/footer.php';?>
    
</body>

</html>
<?php mysqli_close($GLOBALS["conn"]); exit(); ?>
