<!DOCTYPE HTML>
<?php
    session_start();define('includeExist', true);
    
    if (isset($_GET['token'])) {
        $_SESSION['accesskey'] = $_GET['token'];
    }
    
    if (!isset($_SESSION['accesskey'])) {
        include_once '../pg_includes/access_isset.php';
    }

    include_once '../core.php';
    include_once '../pg_includes/functions.php';
    $thisPageTitle = "Add new item";

    $stmt_get_tier_ids = $new_conn->prepare("select 38id, eg_tier1_id, eg_tier2_id, eg_dept_id, 38target, 38targetstatement, 39achv, 39achv_timestamp from eg_tier3 where 38accesskey=?");
    $stmt_get_tier_ids->bind_param("s",$_SESSION['accesskey']);
    $stmt_get_tier_ids->execute();
    $stmt_get_tier_ids->store_result();
    $stmt_get_tier_ids->bind_result($eg_tier3_id,$eg_tier1_id,$eg_tier2_id,$eg_dept_id,$target,$target_statement,$achv,$achv_timestamp);
    $stmt_get_tier_ids->fetch();
    if ($stmt_get_tier_ids->num_rows == 0) {
        echo "<div style='text-align:center;margin-top:100px;'><span style='font-size:40px;color:blue;'><strong>405</strong></span><h2>Forbidden: Method Not Allowed</h2><em>HTTP Response Code</em></div>";
        exit;
    }
    $stmt_get_tier_ids->close();

    $is_upd = false; if ((isset($_GET["upd"]) && is_numeric($_GET["upd"])) || (isset($_POST['submit_button']) && $_POST['submit_button'] == 'Update')) {$is_upd = true;}
    
    //convert to bytes for things involving file size; original is in MB
    $system_allow_document_maxsize = $system_allow_document_maxsize*1000000;
?>

<html lang='en'>

<head>
    <?php include_once '../pg_includes/header.php'; ?>
    <script type="text/javascript">
        <?php
            generateFileBoxAllowedExtensionRule("file1", $system_allow_document_extension);
        ?>
    </script>
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

        .whiteTitleBar:nth-of-type(1) {
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

<body onbeforeunload="window.opener.location.reload(true);window.close();">
           
    <hr>

    <?php

        $stmt3_pc = $new_conn->prepare("select sum(39achv) as cur_total from eg_tier3 where eg_tier2_id=?");
        $stmt3_pc->bind_param("i",$eg_tier2_id);
        $stmt3_pc->execute();
        $stmt3_pc->store_result();
        $stmt3_pc->bind_result($cur_total);
        $stmt3_pc->fetch();
        $stmt3_pc->close();

        $stmt3_in = $new_conn->prepare("select 38id, 38title, 38datestart, 38dateend, 38target, 38targetunit from eg_tier2 where 38id=?");
        $stmt3_in->bind_param("i",$eg_tier2_id);
        $stmt3_in->execute();
        $stmt3_in->store_result();
        $stmt3_in->bind_result($id3_in, $title3_in, $datestart3_in, $dateend3_in, $target3_in, $targetunit3_in);
        $stmt3_in->fetch();
        $stmt3_in->close();

        $stmt3_pct3 = $new_conn->prepare("select sum(38target) as cur_total from eg_tier3 where eg_tier2_id=?");
        $stmt3_pct3->bind_param("i",$eg_tier2_id);
        $stmt3_pct3->execute();
        $stmt3_pct3->store_result();
        $stmt3_pct3->bind_result($cur_target_total);
        $stmt3_pct3->fetch();
        $stmt3_pct3->close();
        
        $current_percentage = 0;
        if ($target3_in <> 0) {
            $current_percentage = round(($cur_total/$target3_in)*100,2);
            if ($current_percentage >= 100) {
                $current_percentage = 100;
            }
        }

        //update achievement in tier2 table whenever possible
        $stmt_update_achv = $new_conn->prepare("update eg_tier2 set 39cur_achv=? where 38id=?");
        $stmt_update_achv->bind_param("di", $current_percentage, $eg_tier2_id);
        $stmt_update_achv->execute();
        $stmt_update_achv->close();
    ?>
    <table class=whiteHeader>
        <tr>
            <td style="background-color:blue;color:white;width:10%;"><?php echo $tier1_menu;?>:<br/> <?php echo getTitleNameFromID("eg_tier1", $eg_tier1_id);?></td>
        </tr>
    </table>
    <table class=whiteHeader>
        <tr>
            <td style="background-color:green;color:white;text-align:left;width:30%;">
                <span style='color:yellow;'><?php echo $tier2_menu;?>:</span> <?php echo getTitleNameFromID("eg_tier2", $eg_tier2_id);?>
                <br/><span style='color:yellow;'><?php echo $tier2_target;?>:</span> <?php echo "$target3_in ($targetunit3_in)";?>
                <br/><span style='color:yellow;'><?php echo $text_duration;?>:</span> <?php echo "$datestart3_in - $dateend3_in";?>
                <br/><span style='color:yellow;'><?php echo $tier2_menu." $text_completion";?>:</span> <?php echo "$current_percentage%";?>
            </td>
            <td style="background-color:lightgreen;color:black;width:40%;text-align:left;">
                <span style='font-size:12pt;color:grey;'><?php echo $tier3_menu;?>: </span><span style='font-size:12pt;'><?php echo getTitleNameFromID("eg_dept", $eg_dept_id);?></span>
                <br/><span style='color:red;'><?php echo $tier3_target_statement;?>:</span> <?php echo $target_statement;?>
                <br/><span style='color:red;'><?php echo $text_current_achievement;?>:</span> <?php echo "$achv / $target";?>
            </td>
        </tr>
        <tr>
            <?php
                $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                if (isset($_GET['token'])) {
                    $_SESSION['actuallink'] = $actual_link;
                }
            ?>
            <td colspan=3 style='background-color:lightyellow;'>Fetch URL: <input readonly=readonly type=text value="<?php echo $_SESSION['actuallink'];?>" style='width:100%;border-radius:5px;height:30px;font-size:10pt;'></td>
        </tr>
    </table>
    <br/>
    
    <?php
            
        if (isset($_REQUEST['submitted']) && $_REQUEST['submitted'] == 'TRUE') {
            //notice handler for posted item that do not assigned with a value
            $val_tier1_id1=setDefaultForPostVar($_POST["tier1_id1"] ?? '');
            $val_tier2_id1=setDefaultForPostVar($_POST["tier2_id1"] ?? '');
            $val_dept_id1=setDefaultForPostVar($_POST["dept_id1"] ?? '');
            
            $val_field2 = setDefaultForPostVar($_POST["field2"] ?? '');
            $val_field3 = setDefaultForPostVar($_POST["field3"] ?? '');

            $val_field4 = setDefaultForPostVar($_POST["field4"] ?? '');
            $val_field5 = setDefaultForPostVar($_POST["field5"] ?? '');
            $val_field6 = setDefaultForPostVar($_POST["field6"] ?? '');

            $val_filterfield1=setDefaultForPostVar($_POST["filterfield1"] ?? '');
            $search_cloud = "$val_field4 $val_field5";
                
            echo "<table class=whiteHeader>";
            echo "<tr><td>";

                if (!empty($_POST["field5"])) {
                    if (isset($_SESSION["username"])) {
                        $updusername = $_SESSION["username"];
                    } else {
                        $updusername = "Direct Link";
                    }

                    if ($_POST['submit_button'] == 'Insert') {
                        //insert into eg_item
                        mysqli_query($GLOBALS["conn"],
                                        "insert into eg_item values (DEFAULT,
                                        '$val_tier1_id1',
                                        '$val_tier2_id1','$val_dept_id1','$val_field4',
                                        '$val_field5','$val_field6',
                                        '".date("Y-m-d")."','$updusername',
                                        '',
                                        ".$_POST["instimestamp1"].",
                                        '$search_cloud'
                                        )");

                        //prompt user to get the id for the item that has been inputted
                        $queryN = "select id from eg_item where 41instimestamp='".$_POST["instimestamp1"]."' and 39inputby='$updusername'";
                        $resultN = mysqli_query($GLOBALS["conn"],$queryN);
                        $myrowN = mysqli_fetch_array($resultN);
                        $id1 = $myrowN["id"];
                                                            
                        echo "<script>alert('All provided data has been inputted into the database. The item ID will be displayed onto your screen, then press OK to continue.');window.location.replace('reg.php');</script>";
                    } elseif ($_POST['submit_button'] == 'Update') {
                        $id1=$_POST["id1"];
                        $inputdate1=$_POST["inputdate1"];

                        //updating main table -----
                        mysqli_query($GLOBALS["conn"],"update eg_item set
                        38field6='$val_field6',
                        38field5='$val_field5',
                        38field4='$val_field4',
                        eg_dept_id='$val_dept_id1',
                        eg_tier2_id='$val_tier2_id1',
                        eg_tier1_id='$val_tier1_id1',
                        40lastupdateby='$updusername',
                        50search_cloud='$search_cloud'
                        where id=$id1");

                        echo "<script>alert('All provided data has been updated into the database.');window.location.replace('reg.php');</script>";
                    }

                    //if not require verification, progress will updated to this newest value
                    //alpha, need to cater if user update older target
                    //----------------------
                    //----------------------
                    if (!$need_verification) {
                        $timestamp_nv = time();
                        $stmt_update_nv = $new_conn->prepare("update eg_tier3 set 39achv=?, 39achv_timestamp=? where 38id=? and eg_tier2_id=?");
                        $stmt_update_nv->bind_param("dsii",$val_field6,$timestamp_nv,$eg_tier3_id,$val_tier2_id1);
                        $stmt_update_nv->execute();
                        $stmt_update_nv->close();
                    }
                    
                    //start uploading things regardless insert new or update the existing one
                    $idUpload = $id1; //reassign and pass it to upload.php
                    $timestampUpload = $_POST["instimestamp1"]; //reassign and pass it to upload.php
                    if (isset($inputdate1)) {
                        $dir_year = substr($inputdate1,0,4);
                    } else {
                        $dir_year = date("Y");
                    }//pass it to upload.php
                    
                    //full text upload to server
                    if (isset($_FILES['file1']['name']) && $_FILES['file1']['name'] != null) {
                        echo "<br/><br/>File upload status: ";
                        
                        $affected_directory = "../$system_docs_directory/$dir_year";
                        $affected_filefield = "file1";
                        $targetted_filemaxsize = $GLOBALS["system_allow_document_maxsize"];
                        $successful_upload_mesage = "File attachment uploaded successfully !";
                        $incorrect_filetype_mesage = "Upload aborted ! Incorrect file type. Please update this record if you need to reupload the file using the record ID.";
                        $incorrect_filesize_mesage = "Upload aborted ! Attachment file size > than expected. Please update this record if you need to reupload the file using the record ID.";
                        $allow_parser_to_parse_internally = true;
                        $parse_txt_file = false;
                        $upload_type = "text";

                        include_once '../pg_includes/upl/upload.php';
                    }
                }

            echo "</td></tr>";
            echo "</table>";
        }//if submitted
        
        //initialize variables for in use with populate all fields below
        $id="";
        $tier1_id=$eg_tier1_id;
        $tier2_id=$eg_tier2_id;
        $dept_id=$eg_dept_id;
        $field4="";$field5="";$field6="";
        $inputdate="";$dir_year="";$lastupdateby="";
        $instimestamp="";

        //populate all fields with values for update operation
        $is_upd = false;
        if (isset($_GET["upd"]) && is_numeric($_GET["upd"])) {
            $get_id_upd = $_GET["upd"];
            $is_upd = true;

            //main table
            $query_value = "select * from eg_item where id = $get_id_upd";
            $result_value = mysqli_query($GLOBALS["conn"],$query_value);
            $myrow_value = mysqli_fetch_array($result_value);
                $id = $myrow_value["id"];
                $tier1_id = $myrow_value["eg_tier1_id"];
                $tier2_id = $myrow_value["eg_tier2_id"];
                $dept_id = $myrow_value["eg_dept_id"];
                $field4 = stripslashes($myrow_value["38field4"]);
                $field5 = stripslashes($myrow_value["38field5"]);
                $field6 = stripslashes($myrow_value["38field6"]);
                $inputdate = $myrow_value["39inputdate"];
                    $dir_year = substr("$inputdate",0,4);
                $lastupdateby = $myrow_value["40lastupdateby"];
                $instimestamp = $myrow_value["41instimestamp"];
        }
    ?>
    
            
    <?php
        //the main form for this page
        if ((isset($_GET['show']) && $_GET['show'] == 'form') || (isset($_GET['upd']) && is_numeric($_GET['upd']))) {
    ?>
            <table class=yellowHeader>
                <tr>
                    <td>
                        <strong>Fill in the fields for inserting a new record: </strong>
                    </td>
                </tr>
            </table>
            
            <form name="swadahform" action="reg.php" method="post" enctype="multipart/form-data">
                <table class=greyBody>

                        <?php
                            //populate hidden value
                            echo "<input type='hidden' name='tier1_id1' value='$tier1_id'>";
                            echo "<input type='hidden' name='tier2_id1' value='$tier2_id'>";
                            echo "<input type='hidden' name='dept_id1' value='$dept_id'>";

                            if (isset($targetunit3_in) && ($targetunit3_in=='decimalpoint' || $targetunit3_in=='percentage')) {
                                $stepin = ".01";
                            } else {
                                $stepin = "";
                            }

                            //generate form fields using special functions
                            regRowGenerate(true,$tag_field4_show,"$tag_field4_simple","field4","","80%","500",$is_upd,$field4);
                            regRowGenerateTextArea(true,$tag_field5_show,"$tag_field5_simple","field5","","80%","500",$is_upd,$field5,"<br/>(Statement must be described cumulatively)");
                            regRowGenerateNumber(true,$tag_field6_show,"$tag_field6_simple ($targetunit3_in)","field6","",$stepin,"50%","500",$is_upd,$field6,"<br/>(Figure must be a cumulative amount)");
                            regRowGenerateFileUploadBox($text_pdf_attachment,$system_allow_document_maxsize,"file1",$system_allow_document_extension,$is_upd,"../$system_docs_directory/$dir_year/"."$id"."_"."$instimestamp".".pdf");
                        ?>
                        
                        <tr><td colspan='2' style='text-align:center;vertical-align:top;'>
                            <?php
                            if (!$is_upd) {
                                echo "
                                    <input type='hidden' name='instimestamp1' value='".time()."' />
                                    <input type='hidden' name='submitted' value='TRUE' />
                                    <br/>
                                    <input type='submit' name='submit_button' value='Insert' />
                                    <input type='button' name='cancel_button' value='Clear' onclick=\"location.href='reg.php';\" />
                                ";
                            } elseif ($is_upd) {
                                echo "
                                    <input type='hidden' name='id1' value='$id' />
                                    <input type='hidden' name='instimestamp1' value='$instimestamp' />
                                    <input type='hidden' name='inputdate1' value='$inputdate' />
                                    <input type='hidden' name='submitted' value='TRUE' />
                                    <br/>
                                    <input type='submit' name='submit_button' value='Update' />
                                    <input type='button' name='cancel_button' value='Cancel' onclick=\"location.href='reg.php';\" />
                                ";
                            }
                            ?>
                            <br/><br/>
                        </td></tr>
                </table>
            </form>

    <?php
        }//if submitted
    ?>

    <?php if (!isset($_GET["upd"])) {?>
        <table class=yellowHeader style='margin-top:10px;'>
            <tr>
                <td>
                    <strong>Record for this target statement :</strong>[<a href='reg.php?show=form'>Add new</a>]
                </td>
            </tr>
        </table>
        <table class='greyBody thisRtable'>
            <tr class='whiteTitleBar' style='background-color:white;'>
                <td colspan=2><?php echo $tag_field4_simple;?></td>
                <td><?php echo $tag_field5_simple;?></td>
                <td><?php echo $tag_field6_simple;?></td>
                <td><?php echo $text_pdf_attachment;?></td>
                <td><?php echo $text_uploaded_on;?></td>
                <td><?php echo $text_options;?></td>
            </tr>
            <?php
                $n = 1;
                $stmt_fdb = $new_conn->prepare("select id,38field4,38field5,38field6,39inputdate,41instimestamp from eg_item where eg_tier1_id=$eg_tier1_id and eg_tier2_id='$eg_tier2_id' and eg_dept_id='$eg_dept_id' order by id desc");
                $stmt_fdb->execute();
                $result_fdb = $stmt_fdb->get_result();
                while($myrow_fdb = $result_fdb->fetch_assoc()) {
                    $field4_fdb = $myrow_fdb["38field4"];
                    $field5_fdb = $myrow_fdb["38field5"];
                    $field6_fdb = $myrow_fdb["38field6"];
                    $inputdate_fdb = $myrow_fdb["39inputdate"];

                    $id_fdb = $myrow_fdb["id"];
                    $dir_year_fdb = substr($inputdate_fdb,0,4);
                    $instimestamp_fdb = $myrow_fdb["41instimestamp"];
                    
                    echo "<tr class='yellowHover thisRrow'>";
                        echo "<td>$n</td>";
                        echo "<td data-label='$tag_field4_simple' class='thisRdata' style='text-align:left;color:blue;'>$field4_fdb</td>";
                        echo "<td data-label='$tag_field5_simple' class='thisRdata' style='text-align:left;color:blue;'>$field5_fdb</td>";
                        echo "<td data-label='$tag_field6_simple' class='thisRdata' style='text-align:left;color:blue;'>$field6_fdb</td>";
                        echo "<td data-label='$text_pdf_attachment' class='thisRdata' style='text-align:left;color:blue;'>";
                            if(is_file("../$system_docs_directory/$dir_year_fdb/$id_fdb"."_"."$instimestamp_fdb.pdf")) {
                                echo "<a href='../$system_docs_directory/$dir_year_fdb/$id_fdb"."_"."$instimestamp_fdb.pdf' target='_blank'><img src='../pg_assets/images/pdf_yes.png' width=24 alt='Click to view' onmouseover=\"Tip('PDF Available.')\" onmouseout=\"UnTip()\"></a>";
                            }
                        echo "</td>";
                        echo "<td data-label='$text_uploaded_on' class='thisRdata' style='text-align:left;color:blue;'>$inputdate_fdb</td>";
                        echo "<td data-label='$text_options' class='thisRdata'>";
                            echo "<a onclick='return openPopup(this.href,200,200);' target='_blank' href='../pg_includes/del_inst.php?del=$id_fdb'><img src='../pg_assets/images/delete.gif'></a> ";
                            if ($n == 1) {
                                echo "<a title='Update this record' href='reg.php?upd=$id_fdb'><img src='../pg_assets/images/pencil.gif'></a>";
                            }
                        echo "</td>";
                    echo "</tr>";
                    $n = $n + 1;
                }
                $stmt_fdb->close();

            ?>
        </table>
        <?php }?>

        <hr>
        
        <?php if (isset($_SESSION['username'])) { ?>
            <div style='text-align:center;margin-top:10px;margin-bottom:10px;'>
                <a class='sButtonCyan' href='javascript:window.opener.location.reload(true);window.close();'><span class='fas fa-arrow-circle-left'></span> Close</a>
            </div>
        <?php }
            
            if (!$is_upd) {
                include_once '../pg_includes/footer.php';
            }
        ?>

    </body>

    </html>
    
    <?php mysqli_close($GLOBALS["conn"]); exit(); ?>
