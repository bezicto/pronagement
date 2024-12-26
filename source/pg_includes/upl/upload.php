<?php
defined('includeExist') || die("<div style='text-align:center;margin-top:100px;'><span style='font-size:40px;color:blue;'><strong>WARNING</strong></span><h2>Forbidden: Direct access prohibited</h2><em>HTTP Response Code</em></div>");

if (!is_dir($affected_directory)) {
    mkdir($affected_directory,0777,true);
    file_put_contents("$affected_directory/index.php", "<html lang='en'><head><title>403 Forbidden</title></head><body><div style='text-align:center;margin-top:100px;'><span style='font-size:40px;color:blue;'><strong>403</strong></span><h2>Forbidden: Access prohibited</h2><em>HTTP Response Code</em></div></body></html>");
    file_put_contents("$affected_directory/.htaccess", "<Files *.php>\ndeny from all\n</Files>\nErrorDocument 403 \"<html lang='en'><head><title>403 Forbidden</title></head><body><div style='text-align:center;margin-top:100px;'><span style='font-size:40px;color:blue;'><strong>403</strong></span><h2>Forbidden: Access prohibited</h2><em>HTTP Response Code</em></div></body></html>\"");
}

$pathparts = pathinfo($_FILES[$affected_filefield]['name']);
$affected_fileextension = strtolower($pathparts["extension"]);

//only certain extension will be allowed
if ($affected_fileextension == 'pdf') {
    $proceedupload = 'TRUE';
} else {
    $proceedupload = 'FALSE';
}

if($_FILES[$affected_filefield]['size'] > $targetted_filemaxsize) {
    $successupload = 'FALSE SIZE';
} else {
    $successupload = 'TRUE';
}

if ($successupload == 'TRUE' && $proceedupload == 'TRUE') {
    if(is_uploaded_file($_FILES[$affected_filefield]['tmp_name']) &&$upload_type == "text") {
            move_uploaded_file($_FILES[$affected_filefield]['tmp_name'],$affected_directory.'/'.$idUpload.'_'.$timestampUpload.'.'.$affected_fileextension);
        }
    
    echo "<span style='color:blue;'>$successful_upload_mesage</span>";
} elseif ($successupload == 'FALSE SIZE' && $proceedupload == 'TRUE') {
    echo "<span style='color:red;'>$incorrect_filesize_mesage</span>";
} else {
    echo "<span style='color:red;'>$incorrect_filetype_mesage</span>";
}
