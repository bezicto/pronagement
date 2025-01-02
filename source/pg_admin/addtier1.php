<!DOCTYPE HTML>
<?php
    session_start();define('includeExist', true);
    include_once  '../core.php';
    include_once  '../pg_includes/access_super.php';
    include_once  '../pg_includes/functions.php';
    
    $thisPageTitle = "Add $tier1_menu";
    $thisFileName = "addtier1.php";
    $table_name = "eg_tier1";
    $item_name = $tier1_menu;

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
    }
    </style>
</head>

<body>
    
    <?php include_once  '../pg_includes/loggedinfo.php'; ?>
    
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
            $desc1 = setDefaultForPostVar($_POST["desc1"]);
            $part1 = setDefaultForPostVar($_POST["part1"]);
            $datestart1 = setDefaultForPostVar($_POST["datestart1"]);
            $dateend1 = setDefaultForPostVar($_POST["dateend1"]);
            
            if ($_REQUEST["submitted"] == "Insert") {
                $stmt_count = $new_conn->prepare("select count(*) from $table_name where 38title = ?");
                $stmt_count->bind_param("s", $title1);
                $stmt_count->execute();
                $stmt_count->bind_result($num_results_affected);
                $stmt_count->fetch();
                $stmt_count->close();
                
                if ($num_results_affected == 0) {
                    if (!empty($title1)) {
                        $stmt_insert = $new_conn->prepare("insert into $table_name values(DEFAULT,?,?,?,?,?)");
                        $stmt_insert->bind_param("sssss",$title1,$desc1,$part1,$datestart1,$dateend1);
                        $stmt_insert->execute();
                        $stmt_insert->close();
                        echo "<script>window.alert('$title1 has been inputed into the database.');location.href='addtier1.php';</script>";exit;
                    } else {
                        echo "<script>window.alert('Your input has been cancelled.Check if any field(s) left emptied before posting.');</script>";
                    }
                } elseif ($num_results_affected >= 1) {
                    echo "<script>window.alert('Your input has been cancelled. Duplicate detected.');location.href='addtier1.php';</script>";exit;
                }
            } elseif ($_REQUEST["submitted"] == "Update") {
                $id1 = $_POST["id1"];

                if (!empty($title1)) {
                    $stmt_update = $new_conn->prepare("update $table_name set 38title=?, 38desc=?, 38participant=?, 38datestart=?, 38dateend=? where 38id=?");
                    $stmt_update->bind_param("sssssi",$title1,$desc1,$part1,$datestart1,$dateend1,$id1);
                    $stmt_update->execute();
                    $stmt_update->close();
                    echo "<script>window.alert('The record has been updated.');location.href='addtier1.php';</script>";exit;
                } else {
                    echo "<script>window.alert('Error. Please make sure there were no empty field(s).<br/>The record has been restored to it original state.');location.href='addtier1.php';</script>";exit;
                }
            }
        }

        if (isset($_GET["edt"]) && is_numeric($_GET["edt"])) {
            $get_id_upd = mysqli_real_escape_string($GLOBALS["conn"], $_GET["edt"]);

            $stmt3 = $new_conn->prepare("select 38id, 38title, 38desc, 38participant, 38datestart, 38dateend from $table_name where 38id = ?");
            $stmt3->bind_param("i", $get_id_upd);
            $stmt3->execute();
            $stmt3->store_result();
            $stmt3->bind_result($id3, $title3, $desc3, $part3, $datestart3, $dateend3);
            $stmt3->fetch();
            $stmt3->close();
        }
    ?>

    <?php if ((isset($_GET['show']) && $_GET['show'] == 'form') || (isset($_GET['edt']) && is_numeric($_GET['edt']))) {?>
    <table class=whiteHeader>
        <tr class=yellowHeaderCenter><td colspan=2><strong><?php echo $item_name;?> <?php echo $text_form;?>:</strong></td></tr>
        <tr class=greyHeaderCenter><td colspan=2><br/>
        <form action="<?php echo $thisFileName;?>" method="post" enctype="multipart/form-data">

                <strong><?php echo $tier1_menu;?>: </strong>
                <br/><input type="text" name="title1" style="width:50%;border-radius:5px;height:30px;font-size:10pt;" value="<?php if (isset($title3)) {echo $title3;} ?>"/>

                <br/><br/><strong><?php echo $tier1_menu_description?>: </strong>
                <br/><input type="text" name="desc1" style="width:50%;border-radius:5px;height:30px;font-size:10pt;" value="<?php if (isset($desc3)) {echo $desc3;} ?>"/>

                <br/><br/><strong><?php echo $tier1_menu_additional_multiline_notes;?>: </strong>
                <br/><textarea name="part1" style='width:50%;border-radius:5px;font-size:10pt;' rows=10><?php if (isset($part3)) {echo $part3;} ?></textarea>

                <br/><br/><strong><?php echo $tier1_datestart;?>: </strong>
                <br/><input type="date" name="datestart1" style="border-radius:5px;height:30px;font-size:10pt;" value="<?php if (isset($datestart3)) {echo $datestart3;} ?>"/>

                <br/><br/><strong><?php echo $tier1_dateend;?>: </strong>
                <br/><input type="date" name="dateend1" style="border-radius:5px;height:30px;font-size:10pt;" value="<?php if (isset($dateend3)) {echo $dateend3;} ?>"/>
    
                <input type="hidden" name="id1" value="<?php if (isset($id3)) {echo $id3;} ?>" />
                <input type="hidden" name="submitted" value="<?php if (isset($_GET['edt'])) {echo "Update";} else {echo "Insert";}?>" />
                
                <br/><br/>
                <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?? '' ?>">
                <input class='sButton' type="submit" name="submit_button" value="<?php if (isset($_GET['edt'])) {echo "Update";} else {echo "Insert";}?>" />
                <input class='sButtonRed' type="button" value="Cancel" onclick="location.href='<?php echo $thisFileName;?>';">

        </form>
        </td></tr>
    </table>
    <br/><br/>
    <?php }?>
    
    <?php if (!isset($_GET["edt"])) {?>
    <table class='whiteHeader thisRtable'>
        <tr class=yellowHeaderCenter><td colspan=7><strong><?php echo $item_name;?> <?php echo $text_listing_and_control;?>: </strong>[<a href='addtier1.php?show=form'>Add new</a>]</td></tr>
        <tr class=whiteHeaderCenterUnderline>
            <td style='width:5%;vertical-align:top;'>ID</td>
            <td style='text-align:left;vertical-align:top;'><?php echo $tier1_menu;?></td>
            <td style='text-align:left;vertical-align:top;'><?php echo $tier1_datestart;?></td>
            <td style='text-align:left;vertical-align:top;'><?php echo $tier1_dateend;?></td>
            <td style='text-align:left;color:blue;vertical-align:top;'><?php echo $text_completion;?></td>
            <td style='width:150;vertical-align:top;'><?php echo $text_options;?></td>
            <td style='text-align:left;vertical-align:top;'><?php echo $tier2_menu_plural;?></td>
        </tr>
        <?php
            $n = 1;

            $stmt_fdb = $new_conn->prepare("select 38id, 38title, 38desc, 38participant, 38datestart, 38dateend from $table_name order by 38datestart desc");
            $stmt_fdb->execute();
            $result_fdb = $stmt_fdb->get_result();
            while($myrow_fdb = $result_fdb->fetch_assoc())
            {
                $title_fdb = $myrow_fdb["38title"];
                $desc_fdb = $myrow_fdb["38desc"];
                $part_fdb = $myrow_fdb["38participant"];
                $datestart_fdb = $myrow_fdb["38datestart"];
                $dateend_fdb = $myrow_fdb["38dateend"];
                $id_fdb = $myrow_fdb["38id"];

                $stmt3_pct = $new_conn->prepare("select sum(39cur_achv) as sumtotal, count(38id) as totaltasks from eg_tier2 where eg_tier1_id=? and 38tasktype='targetsetting'");
                $stmt3_pct->bind_param("i", $id_fdb);
                $stmt3_pct->execute();
                $stmt3_pct->store_result();
                $stmt3_pct->bind_result($cur_achv_pct,$total_project_pct);
                $stmt3_pct->fetch();
                $stmt3_pct->close();

                if ($cur_achv_pct <>0 && $total_project_pct<>0) {
                    $completion = round(($cur_achv_pct/($total_project_pct*100))*100,2);
                } else  {
                    $completion = 0;
                }

                echo "<tr class='yellowHover thisRrow'>
                    <td class='thisRdata' style='vertical-align:top;'>$n</td>
                    <td data-label='$tier1_menu' class='thisRdata' style='text-align:left;vertical-align:top;'>$title_fdb<br/><em>$desc_fdb</em>";
                    if ($part_fdb != '') {
                        echo "<br/><em style='color:navy;'>$tier1_menu_additional_multiline_notes:</em> $part_fdb";
                    }
                    echo "</td>
                    <td data-label='$tier1_datestart' class='thisRdata' style='text-align:left;vertical-align:top;'>$datestart_fdb</td>
                    <td data-label='$tier1_dateend' class='thisRdata' style='text-align:left;vertical-align:top;'>$dateend_fdb</td>
                    <td data-label='$text_completion' class='thisRdata' style='text-align:left;vertical-align:top;color:blue;'>$completion%</td>
                    <td data-label='$text_options' class='thisRdata' style='vertical-align:top;'>
                        <a title='Delete this record' href='$thisFileName?del=$id_fdb' onclick=\"return confirm('Are you sure ? You are advisable to change all items based on this value to the other value before proceeding.');\"><img src='../pg_assets/images/delete.gif'></a>
                        <a title='Update this record' href='$thisFileName?edt=$id_fdb'><img src='../pg_assets/images/pencil.gif'></a>
                    </td>
                    <td data-label='$tier2_menu_plural' class='thisRdata' style='text-align:left;vertical-align:top;'>[<a href='addtier2.php?pid=$id_fdb'>View $tier2_menu_plural</a>]</td>
                </tr>";
                $n = $n + 1;
            }
            $stmt_fdb->close();
        ?>
    </table>
    <?php }?>
    
    <hr>
    
    <?php include_once '../pg_includes/footer.php';?>
    
</body>

</html>
<?php mysqli_close($GLOBALS["conn"]); exit(); ?>
