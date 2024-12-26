<!DOCTYPE HTML>
<?php
    session_start();define('includeExist', true);
    include_once '../pg_includes/access_isset.php';
    include_once '../core.php';
    include_once '../pg_includes/functions.php';
    $thisPageTitle = "Administration";
    $_SESSION['ref'] = 'search.php';
    
    //route tracing for future page
    $_SESSION['route1'] = '1';

?>

<?php
    
    if ($_SESSION['needtochangepwd']) {
        echo "<meta http-equiv=\"refresh\" content=\"0;url=passchange.php?upd=.g\" />"; exit;
    }

    //clear all session variable for searching if sc = cl
    if (isset($_GET['sc']) && $_GET['sc'] == 'cl') {
        unset($_SESSION['sear_scstr']);
        unset($_SESSION['sear_sctype']);
        unset($_SESSION['sear_page']);
    }

    //session management for search fields
    if (isset($_GET['scstr'])) {
        $_SESSION['sear_scstr'] = just_clean($_GET['scstr']);
    }
    if (isset($_GET['sctype']) && (is_numeric($_GET['sctype']) || $_GET['sctype'] == 'EveryThing' || $_GET['sctype'] == 'Control Number' || $_GET['sctype'] == 'Author' )) {
        $_SESSION['sear_sctype'] = $_GET['sctype'];
    }
    if (isset($_GET['page']) && is_numeric($_GET['page'])) {
        $_SESSION['sear_page'] = $_GET['page'];
    }

?>

<html lang='en'>

<head>
    <?php include_once '../pg_includes/header.php'; ?>
    <style>
    @media only screen and (max-width: 800px) {
        .hidewhensmallerscreen {
        display: none;
        }
    }
    </style>
</head>

<body>
    
    <?php include_once '../pg_includes/loggedinfo.php'; ?>
                                                                    
    <hr>
                            
    <div style='text-align:center;background-color:white;padding-top:20px;'>
        <form action="search.php" method="get" enctype="multipart/form-data">
            
            <input type="text" placeholder="Enter search terms here and press Search" name="scstr" style='width:300px;border-radius:5px;height:30px;font-size:10pt;' maxlength="255"
                value="<?php if (isset($_SESSION["sear_scstr"]) && $_SESSION["sear_scstr"] <> '') {echo $_SESSION["sear_scstr"];}?>"/>
                        
            <select style='width:150px;border-radius:5px;height:30px;font-size:10pt;' id="sctype" name="sctype" style="font-size:11px;">
            <?php
                if (isset($_SESSION['sear_sctype'])) {
                    $sctype_select = $_SESSION['sear_sctype'];
                } else {
                    $sctype_select = '';
                }
                    
                echo '<option value="EveryThing" '; if ($sctype_select == 'EveryThing') {echo 'selected';} echo ' >Everything</option> ';
                
                $query_typelist= "select 38id, 38title from eg_tier1";
                $result_typelist = mysqli_query($GLOBALS["conn"],$query_typelist);
                while ($row_typelist = mysqli_fetch_array($result_typelist)) {
                    echo '<option value="'.$row_typelist['38id'].'"'; if($row_typelist['38id']==$sctype_select) {echo ' selected';} echo '> Type: '. $row_typelist['38title'] . '</option>'."\n";
                }
        
                if (isset($_SESSION['username'])) {
                    echo '<option value="Control Number" '; if ($sctype_select == 'Control Number') {echo 'selected';} echo ' >Control Number</option>';
                    echo '<option value="Author" '; if ($sctype_select == 'Author') {echo 'selected';} echo " >$tag_field4_simple</option> ";
                }
            ?>
            </select>

            <input type="hidden" name='sc' value='cl'/>
            <button type="submit" class='sButtonTeal'><span class="fa fa-search"></span> Search</button>

        </form><br/>
    </div>
                            
    <div style='text-align:center;'>
        <?php
            //start paging 1
            $rowsPerPage = 25;
            $pageNum = 1;
            if (isset($_SESSION['sear_page'])) {
                $currentPage = $_SESSION['sear_page'];
                $pageNum = $currentPage;
            }
            $offset = ($pageNum - 1) * $rowsPerPage;
            
            if (isset($_SESSION["sear_scstr"]) || (isset($_SESSION["sear_sctype"]) && $_SESSION['sear_sctype'] <> null)) {
                $scstr_term = $_SESSION["sear_scstr"];
                $latest = "FALSE";
                include_once '../pg_includes/index2_s_boolean.php';
            } else {
                $latest = "TRUE";
                $query1 = "select SQL_CALC_FOUND_ROWS * from eg_item order by id desc LIMIT $offset, $rowsPerPage";
            }
                                    
            $time_start = getmicrotime();
            $result1 = mysqli_query($GLOBALS["conn"],$query1);
                                                    
            //start paging 2
            if (isset($paging_type) && $paging_type == 2) {
                $row = mysqli_fetch_array($result_count);
                $num_results_affected_paging = $row["total"];
            } else {
                $row = mysqli_fetch_row(mysqli_query($GLOBALS["conn"],"SELECT FOUND_ROWS()"));
                $num_results_affected_paging = $row[0];
            }
            $maxPage = ceil($num_results_affected_paging/$rowsPerPage);
            $self = htmlspecialchars($_SERVER['PHP_SELF']);

            $time_end = getmicrotime();
            $time = round($time_end - $time_start, 5);

            if ($latest == "FALSE") {
                echo "<table class=whiteHeaderNoCenter><tr><td>";
                    echo "<strong>Total records found :</strong> $num_results_affected_paging ";
                    echo "<strong>in</strong> $time seconds <strong>for</strong> ' $scstr_term '";
                echo "</td></tr></table>";
                include_once '../pg_includes/index2_sgmtdsrch.php';
            } else {
                echo "<table class=whiteHeaderNoCenter><tr><td><strong>Latest addition to the database : </strong></td></tr></table>";
            }
    
            echo "<table class=whiteHeaderNoCenter>";
                                                                
            $n = $offset + 1;
    
            while ($myrow1 = mysqli_fetch_array($result1)) {
                echo "<tr class=yellowHover>";
                
                    $id2 = $myrow1["id"];
                    $eg_tier1_id2 = $myrow1["eg_tier1_id"];
                    $eg_tier2_id2 = $myrow1["eg_tier2_id"];
                    $eg_dept_id2 = $myrow1["eg_dept_id"];
                    $field5_2 = $myrow1["38field5"];
                    $field4_2 = $myrow1["38field4"];
                    $field6_2 = $myrow1["38field6"];
                    $inputdate2 = $myrow1["39inputdate"];
                    $inputby2 = $myrow1["39inputby"];
                    $instimestamp2 = $myrow1["41instimestamp"];
                    $dir_year2 = substr("$inputdate2",0,4);
                    
                    echo "<td style='text-align:center;vertical-align:top;' width=40>";
                        echo "<a href=javascript:window.scrollTo(0,0)>
                        <img src='../pg_assets/images/topofpage.gif' onmouseover=\"Tip('Go to top of this page')\" onmouseout=\"UnTip()\">
                        </a>
                        <br/>$n</td>";
                                        
                    echo "<td style='text-align:left;vertical-align:top;'>";
                        if (!$_SESSION['needtochangepwd']) {
                            echo "<a style='font-size:12pt' class=myclassOri href='details.php?det=$id2'>";
                                if (isset($scstr_term) && $scstr_term <> null) {
                                    echo highlight($field5_2,$scstr_term);
                                } else {
                                    echo $field5_2;
                                }
                            echo "</a>";
                        } else {
                            echo $field5_2;
                        }

                        if(is_file("../$system_docs_directory/$dir_year2/$id2"."_"."$instimestamp2.pdf")) {
                            echo " <a href='../$system_docs_directory/$dir_year2/$id2"."_"."$instimestamp2.pdf' target='_blank'>
                            <img src='../pg_assets/images/pdf_yes.png' width=16 alt='Click to view' onmouseover=\"Tip('PDF Available.')\" onmouseout=\"UnTip()\">
                            </a>";
                        }

                        if ($field4_2 != '') {
                            echo "<br/><a class=myclass2 href='search.php?scstr=$field4_2&sctype=Author&sc=cl'>$field4_2</a>";
                        }
                        
                        echo "<br/><br/><div class='hidewhensmallerscreen'>
                        <span class='text-span-lightgrey' style='color:#C70039;'>".cutText(getTitleNameFromID("eg_tier1",$eg_tier1_id2),40)."</span>
                         <span style='color:green;'>></span> <span class='text-span-lightcoral' style='color:#900C3F;'>".cutText(getTitleNameFromID("eg_tier2",$eg_tier2_id2),40)."</span>
                          <span style='color:green;'>></span> <span class='text-span-thistle' style='color:darkgreen;'>".getTitleNameFromID("eg_dept",$eg_dept_id2)."</span>
                          <br/></div>";
                    echo "</td>";
                                                    
                    echo "<td style='vertical-align:top;font-size:10px;' width=100>";
                        echo "  Achievement: <span style='color:blue;'>$field6_2</span>
                                <br/><strong><span style='color:black;'>Date Added</span> :</strong> $inputdate2 ";
                    echo "</td>";
                
                echo "</tr>";
                echo "<tr><td colspan=3></td></tr>";
                $n = $n +1 ;
            }
            echo "</table>";
                                
        //start paging 3
        if ($maxPage > 1) {
            echo "<table class=whiteHeader>";
                echo "<tr>";
                    if ($pageNum > 1) {
                        $page = $pageNum - 1;
                        $prev = " [ <a href=\"$self?page=$page\">Prev</a> ] ";
                        $first = " [ <a href=\"$self?page=1\">First</a> ] ";
                    } else {
                        $prev  = " [Prev] ";
                        $first = " [First] ";
                    }

                    if ($pageNum < $maxPage) {
                        $page = $pageNum + 1;
                        $next = " [ <a href=\"$self?page=$page\">Next</a> ] ";
                        $last = " [ <a href=\"$self?page=$maxPage\">Last</a> ] ";
                    } else {
                        $next = " [Next] ";
                        $last = " [Last] ";
                    }
            
                    if ($num_results_affected_paging > $rowsPerPage) {
                        echo "<td style='text-align:right;width:33%;'>" . $first . $prev . "</td><td style='text-align:center;width:34%;'> Page <strong>$pageNum</strong> of <strong>$maxPage</strong> </td><td style='text-align:left;width:33%;'>" . $next . $last . "</td>";
                    }
                echo "</tr>";
            echo "</table>";
        }
    
        ?>
    </div>
    
    <hr>
        
    <?php include_once '../pg_includes/footer.php';?>

</body>

</html>
<?php mysqli_close($GLOBALS["conn"]); exit(); ?>
