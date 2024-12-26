<!DOCTYPE HTML>
<?php
    //note scenso/signon users will be redirected after login to this.
    
    session_start();define('includeExist', true);

    include_once 'core.php';
    include_once 'pg_includes/functions.php';
    $thisPageTitle = "Dashboard";
    $_SESSION['ref'] = 'search.php';
    
    //route tracing for future page
    $_SESSION['route1'] = '1';

    if (
        (isset($_GET['dpid']) && !is_numeric($_GET['dpid'])) ||
        (isset($_GET['dtid']) && !is_numeric($_GET['dtid'])) ||
        (isset($_GET['tnum']) && !is_numeric($_GET['tnum']))
    ) {
        echo "Illegal directive. System aborted.";
        exit;
    }

?>

<?php
    if (isset($_SESSION['needtochangepwd']) && $_SESSION['needtochangepwd'] && isset($_SESSION['username'])) {
        echo "<meta http-equiv=\"refresh\" content=\"0;url=pg_admin/passchange.php?upd=.g\" />"; exit;
    }
?>

<html lang='en'>

<head>
    <?php include_once 'pg_includes/header.php'; ?>
    <style>
        @media screen and (max-width: 600px) {
        table.problematic_table {
            width: 100% !important;
            min-width: 0 !important;
        }
        td.problematic_data {
            width: 100%;
        }
        }
    </style>
</head>

<body>
    <?php
        if (isset($_SESSION['username']) && isset($_SESSION['validSession'])) {
            //createdirectory for file upload
            if (!is_dir($system_docs_directory)) {
                mkdir($system_docs_directory,0755,true);
                file_put_contents("$system_docs_directory"."/index.php", "<html lang='en'><head><title>403 Forbidden</title></head><body><div style='text-align:center;margin-top:100px;'><span style='font-size:40px;color:blue;'><strong>403</strong></span><h2>Forbidden: Access prohibited</h2><em>HTTP Response Code</em></div></body></html>");
                file_put_contents("$system_docs_directory"."/.htaccess", "<Files *.php>\ndeny from all\n</Files>\nErrorDocument 403 \"<html lang='en'><head><title>403 Forbidden</title></head><body><div style='text-align:center;margin-top:100px;'><span style='font-size:40px;color:blue;'><strong>403</strong></span><h2>Forbidden: Access prohibited</h2><em>HTTP Response Code</em></div></body></html>\"");
            }

            //show login information for current user
            include_once 'pg_includes/loggedinfo.php';
            echo "<table class=whiteHeaderNoCenter><tr><td>
                    <span style='color:blue;'>You have logged in as:</span> ".namePatronfromUsername($_SESSION['username'])."
                    <br/><em><span style='color:black;font-size:10px'>Last logged in: ".$_SESSION['lastlogin']."</span></em>
                    <br/><em><span style='color:grey;font-size:10px'>Current Session ID: ".session_id()."</span></em>
                    <br/><br/>Running Pronagement $system_version
                </td></tr></table>";
        }
    ?>
                                                                    
    <hr>
    
    <?php
        
        $appendSQL = "";
        if (isset($_GET['dpid']) && is_numeric($_GET['dpid'])) {
            $appendSQL = " where 38id=".$_GET['dpid'];
        }

        $appendSQL2 = "";
        if (isset($_GET['dtid']) && is_numeric($_GET['dtid'])) {
            $appendSQL2 = " and 38id=".$_GET['dtid'];
        }

        echo "<div style='font-size:14pt;text-align:center;'><strong>Navigation:</strong></div>";
        //navigation_bar_generation
        $navbar_gen = "<div style='font-size:14pt;text-align:center;'><br/>";
        if ($appendSQL != '') {
            $navbar_gen .= "<button class='buttongreenSmall'><a style='color:white;' title='Click to view all $tier1_menu_plural' href='dashboard.php'>$tier1_menu_plural</a></button>";
        } else {
            $navbar_gen .= "<button class='buttonredSmall'><span style='color:white;'>$tier1_menu_plural</span></button>";
        }
        if ($appendSQL != '') {
            if ($appendSQL2 != '') {
                $navbar_gen .= " < <button class='buttongreenSmall'><a style='color:white;' title='Click to view all $tier2_menu_plural for this $tier1_menu_plural' href='dashboard.php?dpid=".$_GET['dpid']."#".$_GET['dtid']."'>".getTitleNameFromID("eg_tier1", $_GET['dpid'])."</a></button>";
            } else {
                $navbar_gen .= " < <button class='buttonredSmall'><span style='color:white;'>".getTitleNameFromID("eg_tier1", $_GET['dpid'])."</span></button>";
            }
        }
        if ($appendSQL2 != '') {
            $navbar_gen .= " < <button class='buttonredSmall'><strong style='color:white;'>$tier2_menu ".$_GET['tnum']."</strong></button>";
        }
        $navbar_gen .= "</div><br/><br/>";

        echo $navbar_gen;
    
    ?>
    <?php
    if (!isset($_GET['dtid'])) {
    ?>
        <table style='width:100%;border: 0px solid lightgrey;overflow-x:auto;text-align:center;'><tr>
        <?php
        
            //all project listing
            $queryB = "select 38id, 38title from eg_tier1 $appendSQL";
            $resultB = mysqli_query($GLOBALS["conn"],$queryB);
            while ($myrowB=mysqli_fetch_array($resultB))
            {
                $stmt_pct = $new_conn->prepare("select sum(39cur_achv) as sumtotal, count(38id) as totaltasks from eg_tier2 where eg_tier1_id=? and 38tasktype='targetsetting'");
                $stmt_pct->bind_param("i", $myrowB["38id"]);
                $stmt_pct->execute();
                $stmt_pct->store_result();
                $stmt_pct->bind_result($cur_achv_pct,$total_project_pct);
                $stmt_pct->fetch();
                $stmt_pct->close();

                $completion = 0;
                if ($cur_achv_pct <>0 && $total_project_pct<>0) {
                    $completion = round(($cur_achv_pct/($total_project_pct*100))*100,2);
                }

                echo "<td style='font-size:8pt;display: inline-block; padding: 5px; vertical-align:top;'>";
                    echo "<table style='width:99%;min-width:350px;background-color:lightblue;height:250px;border-radius:10px;box-shadow:5px 5px lightgrey;'>";
                    echo "<tr>
                            <td><span style='font-size:24pt;color:#C5F4FF;' class='fas fa-project-diagram'></span></td>
                            <td style='font-size:20pt;'>".getTitleNameFromID("eg_tier1", $myrowB["38id"])."<br/></td>
                            <td style='text-align:right;font-size:24pt;'>".drawCircle($completion)."</td>
                        </tr>";
                    if ($appendSQL == '') {
                        echo "<tr><td colspan=3 style='font-size:14pt;text-align:left;'><a class='sButtonCyan' href='dashboard.php?dpid=".$myrowB["38id"]."'>Details</a> ";
                        if (isset($_SESSION['username'])) {
                            echo "<a class='sButtonTeal' href='dashboardp.php?pid=".$myrowB["38id"]."'>Report</a>";
                        }
                        echo "</td></tr>";
                    }
                    echo "</table>";
                echo "</td>";
            }

            //tasks and total department involved count based on selected project
            if ($appendSQL != '') {

                //total tasks
                $stmt_pct = $new_conn->prepare("select count(38id) as total_tasks from eg_tier2 where eg_tier1_id=? and 38tasktype='targetsetting'");
                $stmt_pct->bind_param("i", $_GET['dpid']);
                $stmt_pct->execute();
                $stmt_pct->store_result();
                $stmt_pct->bind_result($total_tasks);
                $stmt_pct->fetch();
                $stmt_pct->close();

                //total departments
                $stmt_tdept = $new_conn->prepare("select count(distinct eg_dept_id) as total_dept_invs from eg_tier3 where eg_tier1_id=?");
                $stmt_tdept->bind_param("i", $_GET['dpid']);
                $stmt_tdept->execute();
                $stmt_tdept->store_result();
                $stmt_tdept->bind_result($total_dept_inv);
                $stmt_tdept->fetch();
                $stmt_tdept->close();

                echo "<td class='problematic_data' style='font-size:8pt;display: inline-block; padding: 5px;vertical-align:top;'>
                        <table class='problematic_table' style='width:99%;min-width:350px;background-color:#F785C1;height:250px;border-radius:10px;box-shadow:5px 5px lightgrey;'>
                            <tr>
                                <td style='background-color:#F785C1;'><span style='font-size:24pt;color:#CBCC49;' class='fas fa-tasks'></span></td>
                                <td style='background-color:#F785C1;font-size:12pt;'>Total $tier2_menu_plural:<br/></td>
                                <td style='background-color:#F785C1;text-align:right;'><span style='font-size:24pt;color:green;'>$total_tasks</span></td>
                            </tr>
                        </table>
                    </td>";
                
                echo "<td style='font-size:8pt;display: inline-block; padding: 5px;vertical-align:top;'>";
                    echo "<table style='width:99%;min-width:350px;background-color:lightpink;height:250px;border-radius:10px;box-shadow:5px 5px lightgrey;'>";
                        echo "<tr>";
                            echo "<td style='background-color:lightpink;'><span style='font-size:24pt;color:magenta;' class='fas fa-building'></span></td>";
                            echo "<td style='background-color:lightpink;font-size:12pt;text-align:left;'>Total $tier3_menu Involvement For $tier1_menu:<br/><ol>";
                                    $query_list_dept = "select distinct eg_dept_id as dept_invs from eg_tier3 where eg_tier1_id=".$_GET['dpid'];
                                    $result_list_dept = mysqli_query($GLOBALS["conn"],$query_list_dept);
                                    while ($myrow_list_dept=mysqli_fetch_array($result_list_dept))
                                    {
                                        echo "<li>".getTitleNameFromID("eg_dept", $myrow_list_dept['dept_invs'])."</li>";
                                    }
                            echo "</ol></td>";
                            echo "<td style='text-align:right;background-color:lightpink;'><span style='font-size:24pt;color:green;'>$total_dept_inv</span></td>";
                        echo "</tr>";
                    echo "</table>";
                echo "</td>";
                
                if ($show_highest_performing_task_owner) {
                    echo "<td style='font-size:8pt;display: inline-block; padding: 5px;vertical-align:top;'>";
                        echo "<table style='width:99%;min-width:350px;background-color:#FCB4FB;height:250px;border-radius:10px;box-shadow:5px 5px lightgrey;'>";
                            echo "<tr>";
                                echo "<td><span style='font-size:24pt;color:magenta;' class='fas fa-crown'></span></td>";
                                echo "<td style='font-size:16pt;color:purple;font-size:12pt;text-align:left;'>Top 3 Highest Performing $tier3_menu:<br/><ol style='font-size:14pt;color:navy;'>";
                                        $query_best_performance = "SELECT eg_dept_id, (sum(39achv)/sum(38target))*100 as percentagePerf FROM eg_tier3 where eg_tier1_id=".$_GET['dpid']." group by eg_dept_id order by percentagePerf desc limit 3;";
                                        $result_best_performance = mysqli_query($GLOBALS["conn"],$query_best_performance);
                                        while ($myrow_best_performance=mysqli_fetch_array($result_best_performance))
                                        {
                                            echo "<li>".getTitleNameFromID("eg_dept", $myrow_best_performance['eg_dept_id'])." - ".round($myrow_best_performance['percentagePerf'],2)."%</li>";
                                        }
                                        echo "</ol></td>";
                            echo "</tr>";
                        echo "</table>";
                    echo "</td>";
                }
            }
        ?>
        </tr></table>
    <?php
    }
    ?>

    <?php if ($appendSQL != '') {?>
    <hr/>
    <table style='width:100%;border: 0px solid lightgrey;overflow-x: auto;text-align:center;'><tr>
        <?php
        //all tasks listing for current selected project
        $queryB = "select 38id, eg_tier1_id, 38title, 39cur_achv, 38tasktype, 38desc from eg_tier2 where eg_tier1_id=".$_GET['dpid']." $appendSQL2";
        $resultB = mysqli_query($GLOBALS["conn"],$queryB);
        $n = 1;
        while ($myrowB=mysqli_fetch_array($resultB))
        {
            $label_n = $n;
            if ($appendSQL2 != '') {
                $label_n = $_GET['tnum'];
            }
            echo "<td style='font-size:8pt;display: inline-block; padding: 5px;'>";
                echo "<table id='".$myrowB["38id"]."' style='background-color:lightgreen;height:150px;border-radius:10px;text-align:left;width:350px;box-shadow:5px 5px lightgrey;'><tr>";
                        if (isset($_GET['tnum']) && is_numeric($_GET['tnum'])) {
                            echo "<td colspan=2>
                                    <span style='font-size:14pt;color:#53CC49;' class='fas fa-puzzle-piece'></span>
                                    <span style='font-size:10pt;color:grey;'>$tier2_menu $label_n</span><br/><br/>
                                    <span style='font-size:10pt;'>".getTitleNameFromID("eg_tier2", $myrowB["38id"])."</span>
                                </td>";
                        } else {
                            echo "<td>
                                    <span style='font-size:16pt;color:#53CC49;' class='fas fa-puzzle-piece'></span>
                                    <span style='font-size:10pt;color:grey;'>$tier2_menu $label_n</span>
                                  </td>
                                  <td>
                                    <span style='font-size:10pt;'>".cutText(getTitleNameFromID("eg_tier2", $myrowB["38id"]),60)."</span><br/>
                                  </td>";
                        }
                    
                        echo "<td style='text-align:right;font-size:12pt;'>";
                        if ($myrowB["38tasktype"] == 'targetsetting') {
                            echo "<span style='font-size:18pt;color:navy;'>".drawCircle(round($myrowB["39cur_achv"]))."</span>";
                        } else {
                            echo "<span style='font-size:18pt;color:navy;'>".drawCircleText("Statement")."</span>";
                        }
                    echo "</td>";
                echo "</tr>";
                if ($appendSQL2 == '') {
                    echo "<tr><td colspan=2 style='text-align:left;font-size:12pt;'><a class='sButtonTeal' href='dashboard.php?dpid=".$myrowB["eg_tier1_id"]."&dtid=".$myrowB["38id"]."&tnum=$n'>Details</a></td></tr>";
                }
                echo "</table>";
            echo "</td>";

            if ($appendSQL2 != '') {
                $stmt_tdept_task = $new_conn->prepare("select count(distinct eg_dept_id) as total_dept_invs from eg_tier3 where eg_tier2_id=?");
                $stmt_tdept_task->bind_param("i", $myrowB["38id"]);
                $stmt_tdept_task->execute();
                $stmt_tdept_task->store_result();
                $stmt_tdept_task->bind_result($total_dept_inv_task);
                $stmt_tdept_task->fetch();
                $stmt_tdept_task->close();
                
                echo "<td style='font-size:8pt;display: inline-block; padding: 5px; vertical-align:top;'>
                        <table style='width:99%;background-color:lightyellow;height:150px;border-radius:10px;box-shadow:5px 5px lightgrey;'>
                            <tr>
                                <td colspan=2 style='text-align:left;font-size:12pt;'>".$myrowB["38desc"]."</td>
                            </tr>
                        </table>
                    </td>";

                if ($myrowB["38tasktype"] == 'targetsetting') {
                    echo "<td style='font-size:8pt;display: inline-block; padding: 5px; vertical-align:top;'>
                            <table style='width:99%;background-color:lightpink;height:150px;border-radius:10px;box-shadow:5px 5px lightgrey;'>
                                <tr>
                                    <td><span style='font-size:24pt;color:#CC6349;' class='fas fa-building'></span></td><td style='font-size:12pt;'>Total $tier3_menu involvement for this $tier2_menu_plural:<br/></td>
                                    <td style='text-align:right;font-size:12pt;'><span style='font-size:24pt;color:green;'>$total_dept_inv_task</span></td>
                                </tr>
                            </table>
                        </td>";
                }
            }
            $n=$n+1;
        }
        ?>
    </tr></table>
    <?php }?>

    <?php if ($appendSQL != '' && $appendSQL2 != '') {?>
    <table style='border: 0px solid lightgrey;overflow-x: auto;text-align:center;width:100%;'><tr>
        <?php
        //all owners listing for current selected tasks and project
        $queryB = "select 38id, eg_dept_id, 38targetstatement, 38target, 39achv, 38accesskey from eg_tier3 where eg_tier2_id=".$_GET['dtid'];
        $resultB = mysqli_query($GLOBALS["conn"],$queryB);
        $n = 1;
        while ($myrowB=mysqli_fetch_array($resultB))
        {
            $percentage_achv_owner = ($myrowB["39achv"]/$myrowB["38target"])*100;
            $tokenkey = $myrowB["38accesskey"];

            echo "<td style='font-size:8pt;display: inline-block; padding: 5px; width:350px;vertical-align:top;'>";
                echo "<table style='width:99%;background-color:#FCE0B4;height:120px;border-radius:10px;box-shadow:5px 5px lightgrey;'>";
                    echo "<tr>
                            <td style='vertical-align:top;' colspan=2>
                                <span style='font-size:16pt;color:orange;' class='fas fa-user-cog'></span>
                                <span style='font-size:10pt;color:grey;'><br/>$tier3_menu $n</span>
                                <h3 style='color:orange;font-size:12pt;'>".getTitleNameFromID("eg_dept", $myrowB["eg_dept_id"])."</h3>
                            </td>
                            <td style='text-align:right;'>
                                <span style='font-size:16pt;color:navy;'>".drawCircle(round($percentage_achv_owner,2))."</span>
                            </td>
                    </tr>";


                echo "<tr style='background-color:#FCE0B4;'><td style='vertical-align:top;text-align:center;' colspan=3>";
                    echo "<table style='width:99%;background-color:#FFF6DD;height:120px;border-radius: 10px;'>";
                        echo "<tr>
                            <td style='vertical-align:top;text-align:center;' colspan=3>
                            <em>".$myrowB["38targetstatement"]."</em><br/><br/>
                            Progress:<br/>
                            <progress value='".$myrowB["39achv"]."' max='".$myrowB["38target"]."'></progress>
                                <br/><span style='color:blue;'>".round($myrowB["39achv"],2)."</span> / <span style='color:grey;'>".round($myrowB["38target"],2)."</span>
                            </td>
                        </tr>";
                        $queryBt = "select 38field6, 38field5 from eg_item where eg_tier2_id=".$_GET['dtid']." and eg_dept_id=".$myrowB['eg_dept_id']."  order by id desc limit 1";
                        $resultBt = mysqli_query($GLOBALS["conn"],$queryBt);
                        $myrowBt = mysqli_fetch_array($resultBt);
                        $last_statement = "-";
                        if ($myrowBt) {
                            $last_statement = $myrowBt["38field5"];
                            $last_progress = $myrowBt["38field6"];
                        }
                        $progressColor = "";
                        if (isset($last_progress) && ($last_progress > round($myrowB["39achv"],2))){
                            echo "<tr style='text-align:center;'><td style='vertical-align:top;text-align:left;color:red;' colspan=3><u>Unverified progress:</u><br/>$last_progress</td></tr>";
                            $progressColor = 'red';
                        }
                        echo "<tr><td style='vertical-align:top;text-align:left;color:$progressColor;' colspan=3><u>Latest Statement:</u><br/>$last_statement</td></tr>";

                        if (isset($_SESSION['validSession']) && isset($_SESSION['username']) && ($myrowB['eg_dept_id']==$_SESSION['accessgranted'] || $_SESSION['accessgranted'] == 'all')) {
                            echo "<tr><td style='vertical-align:top;text-align:left;' colspan=3><u>Option:</u><br/>[<a href='pg_admin/reg.php?token=$tokenkey' onclick='return openPopup(this.href,950,580);'>Update figure</a>]</td></tr>";
                        }
                    echo "</table>";
                echo "</td></tr>";
                echo "</table>";
            echo "</td>";
            $n=$n+1;
        }
        ?>
    </tr></table>
    <?php }?>

    <?php echo $navbar_gen;?>

    <hr>
        
    <?php include_once './pg_includes/footer.php';?>

</body>

</html>
<?php mysqli_close($GLOBALS["conn"]); exit(); ?>
