<?php
    defined('includeExist') || die("<div style='text-align:center;margin-top:100px;'><span style='font-size:40px;color:blue;'><strong>WARNING</strong></span><h2>Forbidden: Direct access prohibited</h2><em>HTTP Response Code</em></div>");

    $appendmflimit = "$offset, $rowsPerPage";

    if ($sctype_select == 'EveryThing') {
        if ($scstr_term == '') {
            $query1 = "select SQL_CALC_FOUND_ROWS * from eg_item ";
             $query1 .= " order by id desc LIMIT $appendmflimit";
        } else {
    
            $query1 = "select SQL_CALC_FOUND_ROWS *, match (38field5) against ('$scstr_term' in boolean mode) as score from eg_item where ";
            $query1 .= " match (38field5,50search_cloud) against ('$scstr_term' in boolean mode) order by score desc LIMIT $appendmflimit";
        }
    } elseif (isset($_SESSION['username']) && ($sctype_select == 'Control Number')) {
            if (is_numeric($scstr_term)) {
                if ($scstr_term <> '') {
                    $query1 = "select SQL_CALC_FOUND_ROWS * from eg_item where id=$scstr_term LIMIT $appendmflimit";
                } else {
                    $query1 = "select SQL_CALC_FOUND_ROWS * from eg_item LIMIT $appendmflimit";
                }
            } else {
                $query1 = "select SQL_CALC_FOUND_ROWS * from eg_item LIMIT $appendmflimit";
                echo "<script>alert('The input you have typed is not numerical. Please retype.');</script>";
            }
    } elseif ($sctype_select == 'Author') {
            if ($scstr_term <> '') {
                $query1 = "select SQL_CALC_FOUND_ROWS * from eg_item where 38field4 like '%$scstr_term%' LIMIT $appendmflimit";
            } else {
                $query1 = "select SQL_CALC_FOUND_ROWS * from eg_item LIMIT $appendmflimit";
            }
    } else {
        if ($scstr_term == '') {
            $query1 = "select SQL_CALC_FOUND_ROWS * from eg_item where ";
             $query1 .= " eg_tier1_id = '$sctype_select' order by id desc LIMIT $appendmflimit";
        } else {
            $query1 = "select SQL_CALC_FOUND_ROWS *, match (38field5) against ('$scstr_term' in boolean mode) as score from eg_item where ";
            $query1 .= " eg_tier1_id = '$sctype_select' and match (38field5,50search_cloud)";
            $query1 .= " against ('$scstr_term' in boolean mode)";
            $query1 .= " order by score desc LIMIT $appendmflimit";
        }
    }
