<?php
    
    function createModalPopupMenuAuto($menuID,$menuTitle,$menuDialog)
    {
        echo "
            <script>
                \$(function() {\$('#$menuID').click(function(e) {e.preventDefault();\$('#$menuID-confirm').dialog('open');});
                $( '#$menuID-confirm' ).dialog({
                    resizable: false,height:160,modal: true,minWidth: 350,autoOpen:false,
                    buttons: {'OK': function() {\$( this ).dialog( 'close' );}}
                    });
                });
            </script>
            <style>
                .ui-button.ui-corner-all.ui-widget.ui-button-icon-only.ui-dialog-titlebar-close {display: none;}
            </style>
            <div id='$menuID-confirm' title='$menuTitle' style='display:none;'>
                <p>
                    <span class='ui-icon ui-icon-alert' style='float:left; margin:0 7px 20px 0;'></span>$menuDialog
                </p>
            </div>
            <script>
                $(document).ready(function(){
                    $('#$menuID-confirm').dialog('open');
                });
            </script>
        ";
    }

    function drawCircle($val) {
        if ($val >= 90) {
            $colorinside = "green";
        } elseif ($val >= 70 && $val < 90) {
            $colorinside = "orange";
        } elseif ($val >=50 && $val < 70) {
            $colorinside = "orange";
        } else {
            $colorinside = "red";
        }
        return "
        <div style='background:$colorinside;' class=\"circle-singleline\">$val%</div>
        ";
    }

    function drawCircleText($val) {
        return "
        <div style='background:navy;font-size:12pt;' class=\"circle-singleline\">$val</div>
        ";
    }
    
    function namePatronfromUsername($username)
    {
        $stmt = $GLOBALS["new_conn"]->prepare("select name from eg_auth where username=?");
        $stmt->bind_param("s",$username);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($returnvalue);//bind result from select statement
        $stmt->fetch();
        $stmt->close();

        return $returnvalue;
    }

    function getTitleNameFromID($table_name, $id)
    {
        $stmt = $GLOBALS["new_conn"]->prepare("select 38title from $table_name where 38id=?");
        $stmt->bind_param("i",$id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($returnvalue);//bind result from select statement
        $stmt->fetch();
        $stmt->close();

        return $returnvalue;
    }

    function getDateFromID($table_name, $id)
    {
        $stmt = $GLOBALS["new_conn"]->prepare("select 38datestart, 38dateend from $table_name where 38id=?");
        $stmt->bind_param("i",$id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($date1,$date2);
        $stmt->fetch();
        $stmt->close();

        return $date1." - ".$date2;
    }

    function getDescFromID($table_name, $id)
    {
        $stmt = $GLOBALS["new_conn"]->prepare("select 38desc from $table_name where 38id=?");
        $stmt->bind_param("i",$id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($desc);
        $stmt->fetch();
        $stmt->close();

        return $desc;
    }

    function getParticipantFromID($table_name, $id)
    {
        $stmt = $GLOBALS["new_conn"]->prepare("select 38participant from $table_name where 38id=?");
        $stmt->bind_param("i",$id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($part1);
        $stmt->fetch();
        $stmt->close();
        
        if ($part1 != '') {
            return $part1;
        } else {
            return "-";
        }
    }

    function just_clean($string,$cleanType = 'max')
    {
        //this is the ultimate sanitizing function
        //current version: 1.0.20220121

        $specialCharacters = array(
        '#' => '',
        '$' => '',
        '%' => '',
        '&' => '',
        '@' => '',
        '.' => '',
        '�' => '',
        '+' => '',
        '=' => '',
        '\\' => '',
        '/' => '',
        );

        foreach($specialCharacters as $character => $replacement) {
            $string = str_replace($character, '' . $replacement . '', $string);
        }

        // Remove all remaining other unknown characters
        if ($cleanType == 'max') {$string = preg_replace('/[^a-zA-Z0-9\-]/', ' ', $string);}
        $string = preg_replace('/^[\-]+/', ' ', $string);
        $string = preg_replace('/[\-]+$/', ' ', $string);
        $string = preg_replace('/[\-]{2,}/', ' ', $string);

        //remove all html related tags
        $string = strip_tags($string);

        //trim right and left for whitespaces, and any multiple whitespaces in between
        return trim(preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', htmlspecialchars($string)));
    }

    function setDefaultForPostVar($fieldVar) {
        return htmlspecialchars($fieldVar,ENT_QUOTES);
    }

    function highlight($text, $words) {
        $words = trim($words);
        $wordsArray = explode(' ', $words);
        foreach($wordsArray as $word) {
            if(strlen(trim($word)) > 2)
            {
                $word = just_clean($word);//remove unwanted special characters, replace it with nothing
                if ($word != '')
                    {
                        $hlstart = '<span style="background-color:yellow;">';
                        $hlend = '</span>';
                        $text = preg_replace("/$word/i", $hlstart.'\\0'.$hlend, $text);//php7
                    }
            }
        }
        return $text;
    }

    function getmicrotime()
    {
        list($usec, $sec) = explode(" ",microtime());
        return (float)$usec + (float)$sec;
    }

    function timetaken($timelog)
    {
        $now_array = explode(' ',date("D d/m/Y h:i a"));
        $lasttime_array = explode(' ',$timelog);
        
        $now_ampm = explode(':',$now_array[2]);
        if ($now_array[3] == 'pm' && $now_ampm[0] <> 12)
            {
                $now_ampm[0] = $now_ampm[0]+12;
            }

        $now_array[2]=$now_ampm[0].":".$now_ampm[1];
    
        $lasttime_ampm = explode(':',$lasttime_array[2]);
        if ($lasttime_array[3] == 'pm' && $lasttime_ampm[0] <> 12)
            {
                $lasttime_ampm[0] = $lasttime_ampm[0]+12;
            }

        $lasttime_array[2]=$lasttime_ampm[0].":".$lasttime_ampm[1];
                                                    
        $now_days = explode('/',$now_array[1]);
        $now_days = implode('-',$now_days);
        $lasttime_days = explode('/',$lasttime_array[1]);
        $lasttime_days = implode('-',$lasttime_days);
        
        $now = strtotime(date($now_days." ".$now_array[2]));
        $lasttime = strtotime($lasttime_days." ".$lasttime_array[2]);
        
        $dateDiff = $now-$lasttime;
        $fullDays = floor($dateDiff/(60*60*24));
        $fullHours = floor(($dateDiff-($fullDays*60*60*24))/(60*60));
        $fullMinutes = floor(($dateDiff-($fullDays*60*60*24)-($fullHours*60*60))/60);

        return "$fullDays"."d, $fullHours"."h, $fullMinutes"."m";
    }

    function cutText($str,$cutcount) {
        if ($str <> null) {
            $text = substr($str, 0, $cutcount);
            if (strlen($str) >= $cutcount) {
                return $text."...";
            } else {
                return $str;
            }
        } else {
            return $str;
        }
    }
    
    function dotFileTypes($filetypes)
    {
        $separated = preg_split ("/\,/", $filetypes);
        $str = "";
        foreach ($separated as $result) {
            $str .= ".".$result.",";
        }
        return rtrim($str,",");
    }

    function quotesFileTypes($filetypes)
    {
        $separated = preg_split ("/\,/", $filetypes);
        $str = "";
        foreach ($separated as $result) {
            $str .= "'".$result."',";
        }
        return rtrim($str,",");
    }

    //create new field based on table row for new item insert on reg.php
    /*
    1 $mandatory = either true (if this field is needed) or false (not needed)
    2 $tag_show = check if tag is set to show or not usually either true or false
    3 $tag_simple_name = simple tag name. refer to config.php
    4 $textfield_name = field name for text field for inputting desciptor
    5 $textfield_defaultvalue = default value for text field above
    6,7 $textfield_width,$textfield_maxlength = controls text field width and maxlength
    8 is_upd = pass if user use the form as update form (true) otherwise it will be as insert new (false)
    9 db_field_value = inserted database field value for err.. a field
    10 $extrabit = you can inject additional html control. it will be beside the textfield
    */
        function regRowGenerate(
        $mandatory,
        $tag_show,
        $tag_simple_name,
        $textfield_name,
        $textfield_defaultvalue,
        $textfield_width,
        $textfield_maxlength,
            $is_upd = false,
            $db_field_value = "",
            $extrabit = "")
        {
            if ($tag_show)
            {
                if ($is_upd) {
                    $textfield_value = $db_field_value;
                }
                else {
                    $textfield_value = $textfield_defaultvalue;
                }

                if ($mandatory) {
                    $required_field = 'required';
                }
                else {
                    $required_field = '';
                }
                
                echo "<tr>";
                    echo "<td style='text-align:right;vertical-align:top;'><strong>$tag_simple_name</strong></td>";
                    echo "<td style='vertical-align:top;'> : ";
                        echo "<input type='text' id='$textfield_name' name='$textfield_name' style='width:$textfield_width' value='$textfield_value' maxlength='$textfield_maxlength' $required_field/> $extrabit";
                    echo "</td>";
                echo "</tr>\n\n";
            }
        }

        function regRowGenerateTextArea(
            $mandatory,
            $tag_show,
            $tag_simple_name,
            $textfield_name,
            $textfield_defaultvalue,
            $textfield_width,
            $textfield_maxlength,
                $is_upd = false,
                $db_field_value = "",
                $extrabit = "")
            {
                if ($tag_show)
                {
                    if ($is_upd) {
                        $textfield_value = $db_field_value;
                    }
                    else {
                        $textfield_value = $textfield_defaultvalue;
                    }
    
                    if ($mandatory) {
                        $required_field = 'required';
                    }
                    else {
                        $required_field = '';
                    }
                    
                    echo "<tr>";
                        echo "<td style='text-align:right;vertical-align:top;'><strong>$tag_simple_name</strong></td>";
                        echo "<td style='vertical-align:top;'> : ";
                            echo "<textarea id='$textfield_name' name='$textfield_name' style='width:$textfield_width;' rows=6 $required_field>$textfield_value</textarea> $extrabit";
                        echo "</td>";
                    echo "</tr>\n\n";
                }
            }

        function regRowGenerateNumber(
            $mandatory,
            $tag_show,
            $tag_simple_name,
            $textfield_name,
            $textfield_defaultvalue,
            $textfield_stepin,
            $textfield_width,
            $textfield_maxlength,
                $is_upd = false,
                $db_field_value = "",
                $extrabit = "")
            {
                if ($tag_show)
                {
                    if ($is_upd) {
                        $textfield_value = $db_field_value;
                    }
                    else {
                        $textfield_value = $textfield_defaultvalue;
                    }
    
                    if ($mandatory) {
                        $required_field = 'required';
                    }
                    else {
                        $required_field = '';
                    }
                    
                    echo "<tr>";
                        echo "<td style='text-align:right;vertical-align:top;'><strong>$tag_simple_name</strong></td>";
                        echo "<td style='vertical-align:top;'> : ";
                            echo "<input type='number' step='$textfield_stepin' id='$textfield_name' name='$textfield_name' style='width:$textfield_width' value='$textfield_value' maxlength='$textfield_maxlength' $required_field/> $extrabit";
                        echo "</td>";
                    echo "</tr>\n\n";
                }
            }

            //file upload box extension checker for reg.php --must be put at <head>
        function generateFileBoxAllowedExtensionRule($fileFieldName, $allowedExtensionList)
        {
            echo "
            $(function() {
                $('#$fileFieldName').change(function() {
                    var fileExtension = ["; echo quotesFileTypes($allowedExtensionList); echo "];
                    if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                        alert('Only "; echo dotFileTypes($allowedExtensionList); echo " formats are allowed.');
                        $fileFieldName.value = '';
                    }
                })
            })\n\n
            ";
        }
        
        //generate file upload box for reg.php
        function regRowGenerateFileUploadBox($whatThisFor,$fileMaxSize,$filefieldName,$fileAllowedExtension,$is_upd = false,$fileLocation = null)
        {
            echo "
            <tr>
                <td style='text-align:right;vertical-align:top;'><strong>$whatThisFor <span style='color:red;'>(Max ".($fileMaxSize/1000000)." MB)</span></strong></td>
                <td>:
                <input type='file' id='$filefieldName' name='$filefieldName' size='38' accept='"; echo dotFileTypes($fileAllowedExtension); echo "' />";
                if ($is_upd && $fileLocation != null && is_file($fileLocation))
                {
                    echo "[<a target='_blank' href='$fileLocation'>View Existing File</a>]";
                }
                echo "</td>
            </tr>\n\n
            ";
        }
