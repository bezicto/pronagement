<?php
    defined('includeExist') || die("<div style='text-align:center;margin-top:100px;'><span style='font-size:40px;color:blue;'><strong>WARNING</strong></span><h2>Forbidden: Direct access prohibited</h2><em>HTTP Response Code</em></div>");
    
    if ($_SESSION["sear_scstr"] <> null) {
        $arrays_T = (explode(" ",$scstr_term));
        $dotarrays_T = sizeof($arrays_T) - 1;
        
        if ($dotarrays_T <> 0) {
            echo "<table class=whiteHeaderNoCenter><tr><td colspan=5>";
                echo "<strong>Simplified search suggestions : </strong>";
                for ($numbering_scheme_T=0; $numbering_scheme_T<=$dotarrays_T; $numbering_scheme_T++)
                {
                    $currentarray = just_clean($arrays_T[$numbering_scheme_T]);
                    echo "<a href='".$_SESSION['ref']."?scstr=$currentarray'>$currentarray</a> ";
                }
            echo "</td></tr></table>";
        }
    }
