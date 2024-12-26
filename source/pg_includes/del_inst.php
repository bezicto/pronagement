<?php

session_start();define('includeExist', true);
include_once '../pg_includes/access_isset.php';
include_once '../core.php';
include_once '../pg_includes/functions.php';

if ((isset($_GET["del"]) && $_GET["del"] <> null && is_numeric($_GET["del"])) && $_SESSION['editmode'] == 'SUPER') {
    $get_id_del = $_GET["del"];

    $stmt_item = $new_conn->prepare("select * from eg_item where id=?");
    $stmt_item->bind_param("i", $get_id_del);
    $stmt_item->execute();
    $result_item = $stmt_item->get_result();
    $num_results_affected = $result_item->num_rows;
    $myrow_item = $result_item->fetch_assoc();
        
    $inputdate = $myrow_item["39inputdate"];
    $dir_year = substr("$inputdate",0,4);
    $instimestamp = $myrow_item["41instimestamp"];
    $del_path = "$dir_year/$get_id_del"."_"."$instimestamp";

    if ($num_results_affected >= 1) {
        $get_delfilename = $del_path;
    } else {
        $get_delfilename = "NODIR";
    }
    
    mysqli_query($GLOBALS["conn"],"delete from eg_item where id='$get_id_del'");//delete traces from eg_item
            
    if (is_file("../$system_docs_directory/".$get_delfilename.".pdf")) {
        unlink("../$system_docs_directory/".$get_delfilename.".pdf");
    }
    
    echo "
    <script>function refreshAndClose() {window.opener.location.reload(true);window.close();}setTimeout('self.close()', 500)</script>
    <body onbeforeunload='refreshAndClose();'><div align=center>Item has been deleted. This windows will close automatically.</div></body>
    ";
} elseif ((isset($_GET["del"] ) && $_GET["del"] <> null && is_numeric($_GET["del"])) && $_SESSION['editmode'] == 'STAFF') {
    echo "<script>alert('Trying doing something illegal arent you ?');</script>";
}
