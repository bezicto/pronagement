<!DOCTYPE HTML>
<?php
    session_start();define('includeExist', true);
    include_once '../pg_includes/access_isset.php';
    include_once '../core.php';
    include_once '../pg_includes/functions.php';
    $thisPageTitle = "Item Details";
        
    $get_id_det = 0; if (isset($_GET["det"]) && is_numeric($_GET["det"])) {$get_id_det = $_GET["det"];} else {$get_id_det = 0;}

    $get_scstr = ""; if (isset($_SESSION["sear_scstr"])) {$get_scstr = $_SESSION["sear_scstr"];}
        
    if (isset($_GET["infname"])) {$get_infname = $_GET["infname"];}
    if (isset($_GET["page"])) {$get_page = $_GET["page"];}
    
    $stmt_item = $new_conn->prepare("select * from eg_item where id=?");
    $stmt_item->bind_param("i", $get_id_det);
    $stmt_item->execute();
    $result_item = $stmt_item->get_result();
    $num_results_affected = $result_item->num_rows;
    $myrow_item = $result_item->fetch_assoc();
        
    if ($num_results_affected >= 1) {
        $id5 = $myrow_item["id"];
        $eg_tier1_id5 = $myrow_item["eg_tier1_id"];
        $eg_tier2_id5 = $myrow_item["eg_tier2_id"];
        $eg_dept_id5 = $myrow_item["eg_dept_id"];

        $field6_5 = $myrow_item["38field6"];
        $field5_5 = $myrow_item["38field5"];
        $field4_5 = $myrow_item["38field4"];

        $inputdate5 = $myrow_item["39inputdate"];
            $dir_year5 = substr("$inputdate5",0,4);
        $input_by5 = $myrow_item["39inputby"];
        $input_by5 = $myrow_item["39inputby"];
        $lastupdateby5 = $myrow_item["40lastupdateby"];
                
        $instimestamp5 = $myrow_item["41instimestamp"];
    }
?>

<html lang='en'>

<head>
    <?php include_once '../pg_includes/header.php'; ?>
</head>

<body>
        
    <?php include_once '../pg_includes/loggedinfo.php'; ?>
    
    <hr>

    <div style='text-align:center'>
        <?php
            if ($num_results_affected >= 1)
            {
                echo "<table class=whiteHeader>";
                echo "<tr class=yellowHeaderCenter><td><strong>Record Details</strong> : ";
                echo "</td></tr></table>";
                
                echo "<table class=whiteHeaderNoCenter style='border: 1px solid lightgrey; max-width:100%;overflow-x: auto;' width=100%>";
                            
                    echo "<tr><td style='text-align:right;'><strong>Control Number :</strong></td><td style='text-align:left;vertical-align:top;'>$id5</td></tr>";
                    echo "<tr><td style='text-align:right;'><strong>$tier1_menu :</strong></td><td style='text-align:left;vertical-align:top;'>".getTitleNameFromID("eg_tier1",$eg_tier1_id5)."</td></tr>";
                    echo "<tr><td style='text-align:right;'><strong>$tier2_menu :</strong></td><td style='text-align:left;vertical-align:top;'>".getTitleNameFromID("eg_tier2",$eg_tier2_id5)."</td></tr>";
                    echo "<tr><td style='text-align:right;'><strong>$tier3_menu :</strong></td><td style='text-align:left;vertical-align:top;'>".getTitleNameFromID("eg_dept",$eg_dept_id5)."</td></tr>";
                    
                    if ($field4_5 != '' && $tag_field4_show)
                        {echo "<tr><td style='text-align:right;'><strong>$tag_field4_simple :</strong></td><td style='text-align:left;vertical-align:top;'>$field4_5</td></tr>";}
                    
                    echo "<tr><td style='text-align:right;'><strong>$tag_field5_simple :</strong></td><td style='text-align:left;vertical-align:top;'>$field5_5</td></tr>";

                    echo "<tr><td style='text-align:right;'><strong>$tag_field6_simple :</strong></td><td style='text-align:left;vertical-align:top;'>$field6_5</td></tr>";

                    if(is_file("../$system_docs_directory/$dir_year5/$id5"."_"."$instimestamp5.pdf")) {
                        echo "<tr><td style='text-align:right;' width=150px><strong>File :</strong></td><td style='text-align:left;vertical-align:top;'><a href='../$system_docs_directory/$dir_year5/$id5"."_"."$instimestamp5.pdf' target='_blank'><img src='../pg_assets/images/pdf_yes.png' width=24 alt='Click to view' onmouseover=\"Tip('PDF Available.')\" onmouseout=\"UnTip()\"></a></td></tr>";
                    }

                echo "</table>";
            } else {
                echo "<div style='padding-top:10px;font-size:18px;'>Item is not available.</div>";
            }
        ?>

        <br/>
        <?php
            echo "<a class='sButtonCyan' href='javascript:history.go(-1)'><span class='fas fa-arrow-circle-left'></span> Back to previous page</a>";
        ?>
    </div>

    <hr>
        
    <?php include_once '../pg_includes/footer.php';?>

</body>

</html>
<?php mysqli_close($GLOBALS["conn"]); exit(); ?>
