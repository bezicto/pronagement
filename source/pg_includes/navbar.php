<?php
  defined('includeExist') || die("<div style='text-align:center;margin-top:100px;'><span style='font-size:40px;color:blue;'><strong>WARNING</strong></span><h2>Forbidden: Direct access prohibited</h2><em>HTTP Response Code</em></div>");
?>

<script>
function myShowMenuItemReg() {
    var x = document.getElementById("myTopnav");
    if (x.className === "topnav") {x.className += " responsive";}
    else {x.className = "topnav";}
}
</script>

<div class="topnav" id="myTopnav">
  <a href="<?php echo $appendroot;?>dashboard.php?sc=cl" class="active"><img alt='Menu Icon' src='<?php echo $appendroot;?><?php echo $menu_icon;?>' width=20></a>
  
        <div class="dropdownNB">
          <button class="dropbtn"><span class="fa fa-desktop"></span> <?php echo $tier1_menu;?>
            <span class="fa fa-caret-down"></span>
          </button>
                <div class="dropdownNB-content">
                  <?php if (!$_SESSION['needtochangepwd'] && $_SESSION['editmode'] == 'SUPER') {?>
                    <a href="<?php echo $appendroot;?>pg_admin/addtier1.php"><span class="fas fa-project-diagram"></span> Entry Management</a>
                    <a href="<?php echo $appendroot;?>pg_admin/search.php?sc=cl"><span class="fas fa-clipboard-list"></span> <?php echo $document_summited_menu;?></a>
                  <?php }
                  if (!$_SESSION['needtochangepwd']) {
                  ?>
                    <a href="<?php echo $appendroot;?>pg_admin/dept_evd.php"><span class="fas fa-building"></span> <?php echo $token_links_menu;?></a>
                  <?php }
                  ?>
                </div>
        </div>
  
  <?php if (!$_SESSION['needtochangepwd'] && $_SESSION['editmode'] == 'SUPER') {?>
        <div class="dropdownNB">
          <button class="dropbtn"><span class="fa fa-toolbox"></span> Administration
            <span class="fa fa-caret-down"></span>
          </button>
                <div class="dropdownNB-content">
                  <a href="<?php echo $appendroot;?>pg_admin/chanuser.php"><span class="fas fa-user-circle"></span> User Account Management</a>
                  <a href="<?php echo $appendroot;?>pg_admin/adddept.php"><span class="fas fa-building"></span> <?php echo $tier3_menu;?> Management</a>
                </div>
        </div>
  <?php }?>
  
  <a href="<?php echo $appendroot;?>pg_admin/passchange.php?upd=.g"><span class="fa fa-key"></span> Change Password</a>
  <a href="<?php echo $appendroot;?>index.php?log=out" onclick="return confirm('Are you sure?')"><span class="fa fa-user"></span> Logout</a>
  <a href="javascript:void(0);" style="font-size:15px;" class="icon" onclick="myShowMenuItemReg()">&#9776;</a>
  
</div>
