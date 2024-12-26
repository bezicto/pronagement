<!DOCTYPE HTML>
<?php
    session_start();define('includeExist', true);
    include_once 'core.php';
    include_once 'pg_includes/functions.php';
    
    $thisPageTitle = "Add $tier2_menu_plural";
    $thisFileName = "addtier2.php";
    $table_name = "eg_tier2";
    $item_name = $tier2_menu;

    if (isset($_GET['pid']) && is_numeric($_GET['pid'])) {
        $_SESSION['pid'] = $_GET['pid'];
    } else {
        echo "<div style='text-align:center;margin-top:100px;'><span style='font-size:40px;color:blue;'><strong>403</strong></span><h2>Forbidden: Access prohibited</h2><em>HTTP Response Code</em></div>";
        exit;
    }

    if (isset($_SESSION['validSession']) && isset($_SESSION['username']) && $_SESSION['accessgranted'] == 'all') {
        $thisColspan=9;
    } else {
        $thisColspan=8;
    }

?>

<html lang='en'>

<head><?php include_once  'pg_includes/header.php'; ?>
<style>
  @media only screen and (max-width: 800px) {
    /* Jadikan setiap baris satu blok */
    table.thisRtable {
        border-collapse: collapse;
    }
    
    tr.thisRrow, td.thisRdata {
      display: block;
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
    td.thisRdata[colspan="<?php echo $thisColspan-1;?>"] {
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
            include_once  'pg_includes/loggedinfo.php';
        }
    ?>
    
    <hr>

    <table class=whiteHeader>
        <tr><td style="background-color:blue;color:white;">
            <?php echo $tier1_menu;?>:<br/> <?php echo getTitleNameFromID("eg_tier1", $_SESSION['pid']);?>
            <br/><br/>Date: <br/> <?php echo getDateFromID("eg_tier1", $_SESSION['pid']);?>
            <br/><br/>Participant: <br/> <?php echo getParticipantFromID("eg_tier1", $_SESSION['pid']);?>
        </td></tr>
    </table>
    <br/>
        
        <?php
        //for target setting
        $stmt_fdb = $new_conn->prepare("select 38id, 38title, 38desc, 38datestart, 38dateend, 38target, 38targetunit from $table_name where 38tasktype='targetsetting' and eg_tier1_id=".$_SESSION['pid']);
        $stmt_fdb->execute();
        $result_fdb = $stmt_fdb->get_result();
        if ($result_fdb->num_rows >= 1) {
        ?>
        
        <br/>
        <div style="overflow-x:auto;">
        <table class='whiteHeader thisRtable'>
            <tr class=whiteHeaderCenterUnderline>
                <td colspan=<?php echo $thisColspan;?> style='background-color:lightyellow;'>Statements with Target</td>
            </tr>
            <tr class=whiteHeaderCenterUnderline>
                <td style='width:5%;'>ID</td>
                <td style='text-align:left;'>Title</td>
                <td style='text-align:left;'>Description</td>
                <td style='text-align:left;'>Date Start</td>
                <td style='text-align:left;'>Date End</td>
                <td style='text-align:left;'>Target</td>
                <td style='text-align:left;'>Target Unit</td>
                <td style='text-align:left;color:blue;'>Current Achievement</td>
                <?php if (isset($_SESSION['validSession']) && isset($_SESSION['username']) && $_SESSION['accessgranted'] == 'all') { ?>
                    <td style='text-align:left;color:blue;'>Update</td>
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

                    $appendsqlDept = "";
                    if ($_SESSION['accessgranted'] != 'all') {
                        $appendsqlDept = "and eg_dept_id=".$_SESSION['accessgranted'];
                    }
                    
                    $stmt3_pc = $new_conn->prepare("select sum(39achv) as cur_total from eg_tier3 where eg_tier2_id=? $appendsqlDept");
                    $stmt3_pc->bind_param("i",$id_fdb);
                    $stmt3_pc->execute();
                    $stmt3_pc->store_result();
                    $stmt3_pc->bind_result($cur_total);
                    $stmt3_pc->fetch();
                    $stmt3_pc->close();

                    $tokenkey = "N/A";
                    $stmt3_token = $new_conn->prepare("select 38accesskey from eg_tier3 where eg_tier2_id=? $appendsqlDept");
                    $stmt3_token->bind_param("i",$id_fdb);
                    $stmt3_token->execute();
                    $stmt3_token->store_result();
                    $stmt3_token->bind_result($tokenkey);
                    $stmt3_token->fetch();
                    $stmt3_token->close();

                    $current_percentage = 0;
                    if ($target_fdb <> 0) {
                            $current_percentage = round(($cur_total/$target_fdb)*100,2);
                            if ($current_percentage >= 100) {
                                $current_percentage = 100;
                            }
                    }
                    $current_percentage .= "%";

                    echo "<tr class='thisRrow yellowHover'>";
                        echo "<td class='thisRdata'>$n</td>";
                        echo "<td class='thisRdata' style='text-align:left;' data-label='Title'>$title_fdb</td>";
                        echo "<td class='thisRdata' style='text-align:left;' data-label='Description'>$desc_fdb</td>";
                        echo "<td class='thisRdata' style='text-align:left;' data-label='Date Start'>$datestart_fdb</td>";
                        echo "<td class='thisRdata' style='text-align:left;' data-label='Date End'>$dateend_fdb</td>";
                        echo "<td class='thisRdata' style='text-align:left;' data-label='Target'>$target_fdb</td>";
                        echo "<td class='thisRdata' style='text-align:left;' data-label='Target Unit'>$targetunit_fdb</td>";
                        echo "<td class='thisRdata' style='text-align:left;' data-label='Current Achievement'>$current_percentage</td>";
                        if (isset($_SESSION['validSession']) && isset($_SESSION['username']) && $_SESSION['accessgranted'] == 'all') {
                            echo "<td class='thisRdata' style='text-align:left;' data-label='Option'>";
                            if (isset($tokenkey) && $tokenkey != 'N/A') {
                                echo "[<a href='pg_admin/reg.php?token=$tokenkey' onclick='return openPopup(this.href,950,580);'>Update</a>]";
                            } else {
                                echo "N/A";
                            }
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
                } else  {
                    $completion = 0;
                }
                echo "<tr class='thisRrow' style='background-color:lightgrey;'><td class='thisRdata' colspan=".($thisColspan-1)." style='text-align:right;'>Completion:</td><td class='thisRdata' colspan=3 style='text-align:left;color:red;'>$completion%</td></tr>";
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
        <table  class='whiteHeader thisRtable'>
            <tr class=whiteHeaderCenterUnderline>
                <td colspan=4 style='background-color:lightyellow;'>Statements</td>
            </tr>
            <tr class=whiteHeaderCenterUnderline>
                <td style='width:5%;'>ID</td>
                <td style='text-align:left;'>Title</td>
                <td style='text-align:left;' colspan=2>Description</td>
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

                    echo "<tr class='thisRrow yellowHover'>";
                        echo "<td class='thisRdata'>$n</td>";
                        echo "<td class='thisRdata' style='text-align:left;' data-label='Title'>$title_fdb</td>";
                        echo "<td class='thisRdata' style='text-align:left;' data-label='Description'>$desc_fdb</td>";
                        echo "<td class='thisRdata'></td>";//prevent last td to be centered, so we make this emtpy <td>
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
    
    <hr>
    
    <div style='text-align:center;margin-top:10px;margin-bottom:10px;'>
        <a class='sButtonCyan' href='dashboard.php'><span class='fas fa-arrow-circle-left'></span> Back to Dashboard</a>
    </div>
      
    <?php include_once  'pg_includes/footer.php';?>
    
</body>

</html>
<?php mysqli_close($GLOBALS["conn"]); exit(); ?>
