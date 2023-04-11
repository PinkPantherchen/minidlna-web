<?php
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
 <meta http-equiv="content-type" content="text/html; charset=utf-8" />
 <link rel="stylesheet" type="text/css" href="../css/<?php print $_SESSION['style']; ?>/ui.dynatree.css" />
 <link rel="stylesheet" type="text/css" href="../css/default.css" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.js" type="text/javascript"></script>
<script src="../script/jquery.easing.js" type="text/javascript"></script>
<script src="../script/jquery.filetree.js" type="text/javascript"></script>
<link href="../css/jquery.filetree.css" rel="stylesheet" type="text/css" media="screen" />
<script type="text/javascript">
function activeDialog(id) {
  switch(id) {
    case 1:
      $('#vFile1').fileTree({ root: '/', script: '../inc/jquery.filetree.php' }, function(file) { 
        document.getElementById("value1").value = file;
      });
    	break;
    case 2:			
      $('#vFile2').fileTree({ root: '../db/theme/', script: '../inc/jquery.filetree.php' }, function(file) { 
        document.getElementById("value10").value = file;
      });
      break;        
    case 8:			
      $('#vFile8').fileTree({ root: '/', script: '../inc/jquery.filetree.php' }, function(file) { 
        document.getElementById("value8").value = file;
      });
      break;
  };
};

</script>
 <meta name="language" content="<?php print $_SESSION['lang']; ?>" />
 <meta name="content-language" content="<?php print $_SESSION['lang']; ?>" />
 <?php
 // Sprache einbinden
 require_once("../lng/".$_SESSION['lang'].".lng");
 // Config einbinden
 require_once("../inc/config.inc.php");
 // Klasse einbinden
 require_once("../default/fkt.allgemein.inc.php");
 require_once("../default/cls.error.inc.php"); 
 // Klasse instanzieren
 try
 {
   $qPHP = new PHP_func();
 } catch(ExtException $e) {
   print $e->errorMessage();  
 } 
 ?>
</head>
<body>
<pre>
<?php
include("../script/dlcapi.class.php");
$conFiles = new conFiles();
if(isset($_POST['submit'])) {
  $content_old = "";
  $filename = "../inc/config.inc.php";
  //foreach(array_map('htmlentities',file($filename)) as $line) { $content_old .= $line; } 
  foreach(file($filename) as $line) { $content_old .= $line; } 
  $content_new = $content_old; 
  $content_new = str_replace("path_to_db = '".$path_to_db."';","path_to_db = '".$_POST['value1']."';",$content_new);
  $content_new = str_replace("server_http = '".$server_http."';","server_http = '".$_POST['value11']."';",$content_new);
  $content_new = str_replace("debug_sqls = '".$debug_sqls."';","debug_sqls = '".$_POST['value2']."';",$content_new);
  $content_new = str_replace("fdownload  = '".$fdownload."';","fdownload  = '".$_POST['value3']."';",$content_new);
  $content_new = str_replace("jdownload  = '".$jdownload."';", "jdownload  = '".$_POST['value4']."';",$content_new);
  // DBManager 2022
  $content_new = str_replace("password  = '".$password."';","password  = '".$_POST['value9']."';",$content_new);
  $content_new = str_replace("theme  = '".$theme."';","theme  = '".$_POST['value10']."';",$content_new);

  if(is_writable($filename) && $content_old!==$content_new) {
    file_put_contents($filename,html_entity_decode($content_new));
    $path_to_db = $_POST['value1'];
    $server_http = $_POST['value11'];
    $debug_sql  = $_POST['value2'];
    $fdownload  = $_POST['value3'];
    $jdownload  = $_POST['value4'];
    $password   = $_POST['value9'];
    $theme      = $_POST['value10'];
    print "<h1>".$mlng['edit_cfg1_true']."</h1>";
  } elseif ($content_old===$content_new) {
    print "<h1 style='color: #FF0000;'>".$mlng['edit_cfg1_false1']."</h1>";
  } else {
    print "<h1 style='color: #FF0000;'>".$mlng['edit_cfg1_false2']."</h1>";
  }
  $content_old = "";  
  $filename = "../script/dlcapi.class.php";
  //foreach(array_map('htmlentities',file($filename)) as $line) { $content_old .= $line; }
  foreach(file($filename) as $line) { $content_old .= $line; }
  $content_new = $content_old;  
  $content_new = str_replace(constant("conFiles::dlc_content_generator_id"),$_POST['value5'],$content_new);
  $content_new = str_replace(constant("conFiles::dlc_content_generator_name"),$_POST['value6'],$content_new);
  $content_new = str_replace(constant("conFiles::dlc_content_generator_url"),$_POST['value7'],$content_new);
  $content_new = str_replace(constant("conFiles::dlc_cache_keys_filename"),$_POST['value8'],$content_new);
  if(is_writable($filename) && $content_old!==$content_new) {
    file_put_contents($filename,html_entity_decode($content_new));
    print "<h1>".$mlng['edit_cfg2_true']."</h1>";
  } elseif ($content_old===$content_new) {
    print "<h1 style='color: #FF0000;'>".$mlng['edit_cfg2_false1']."</h1>";
  } else {
    print "<h1 style='color: #FF0000;'>".$mlng['edit_cfg2_false2']."</h1>";
  }                          
} else {
?>
</pre>
<form method="post" action="config.edit.inc.php">
<?php
print "<h1 class='edit'>".$mlng['edit_setting']."</h1><br />";
print "<table>\n<tr><td colspan='3' class='edit_h'>".$mlng['edit_cfg1']."</td></tr>\n<tr>";
// inc/config.inc.php
print "<td class='edit_t'>".$mlng['edit_db']."</td>";
print "<td class='edit_u'><input type='text' name='value1' id='value1' value='$path_to_db' size='100%' />
       <input type='button' name='vbutton1' onclick='javascript:activeDialog(1);' value='".$mlng['edit_selF1']."' />
       <br /><i>".$mlng['edit_db_desc']."</i></td><td rowspan='4'><div id='vFile1' class='demo1'></div></td></tr>\n<tr>";
print "<tr><td class='edit_t'>".$mlng['edit_http']."</td>";
print "<td class='edit_u'><input type='text' name='value11' id='value11' value='$server_http' size='100%' />
       <br /><i>".$mlng['edit_http_desc']."</i></td></tr>\n";
print "<td class='edit_t'>".$mlng['edit_sql']."</td><td class='edit_u'>";
if($debug_sqls=='false') {
  print "<input type='radio' name='value2' value='true' />".$mlng['edit_sql_true']."  ";
  print "<input type='radio' name='value2' value='false' checked='checked' />".$mlng['edit_sql_false'];
} else {
  print "<input type='radio' name='value2' value='true' checked='checked'/>".$mlng['edit_sql_true']."  ";
  print "<input type='radio' name='value2' value='false' />".$mlng['edit_sql_false'];
}
print "<br /><i>".$mlng['edit_sql_desc']."</i></td></tr>\n<tr>";
print "<td class='edit_t'>".$mlng['edit_fdown']."</td><td class='edit_u'>";
if($fdownload=='false') {
  print "<input type='radio' name='value3' value='true' />".$mlng['edit_fdown_true']."  ";
  print "<input type='radio' name='value3' value='false' checked='checked' />".$mlng['edit_fdown_false'];
} else {
  print "<input type='radio' name='value3' value='true' checked='checked'/>".$mlng['edit_fdown_true']."  ";
  print "<input type='radio' name='value3' value='false' />".$mlng['edit_fdown_false'];
}
print "<br /><i>".$mlng['edit_fdown_desc']."</i></td></tr>\n<tr>";
print "<td class='edit_t'>".$mlng['edit_jdown']."</td><td class='edit_u'>";
if($jdownload=='false') {
  print "<input type='radio' name='value4' value='true' />".$mlng['edit_jdown_true']."  ";
  print "<input type='radio' name='value4' value='false' checked='checked' />".$mlng['edit_jdown_false'];
} else {
  print "<input type='radio' name='value4' value='true' checked='checked'/>".$mlng['edit_jdown_true']."  ";
  print "<input type='radio' name='value4' value='false' />".$mlng['edit_jdown_false'];
}
print "<br /><i>".$mlng['edit_jdown_desc']."</i></td></tr>\n";
// Databasemanager 2022
print "<tr><td class='edit_t'>".$mlng['edit_pass']."</td>";
print "<td class='edit_u'><input type='text' name='value9' id='value9' value='$password' size='100%' />";
print "<br /><i>(".$mlng['edit_pass_desc'].")</i></td><td rowspan='2'><div id='vFile2' class='demo1'></div></td></tr>\n<tr>";
print "<td class='edit_t'>".$mlng['edit_theme']."</td>";
print "<td class='edit_u'><input type='text' name='value10' id='value10' value='$theme' size='100%' />
       <input type='button' name='vbutton2' onclick='javascript:activeDialog(2);' value='".$mlng['edit_selF1']."' />
       <br /><i>/".$mlng['edit_theme_desc'].")</i></td></tr>";

// script/dlcapi.class.php
print "<tr><td colspan='3' class='edit_h'>".$mlng['edit_cfg2']."</td></tr>\n<tr>";
print "<td class='edit_t'>".$mlng['edit_cfid']."</td>";
print "<td class='edit_u'><input type='text' name='value5' value='".constant("conFiles::dlc_content_generator_id")."' size='100%' /><br /><i>".$mlng['edit_cfid_desc']."</i></td>";
print "<td rowspan='4'><div id='vFile8' class='demo8'></div></td>";
print "</tr>\n<tr>";
print "<td class='edit_t'>".$mlng['edit_cfname']."</td>";
print "<td class='edit_u'><input type='text' name='value6' value='".constant("conFiles::dlc_content_generator_name")."' size='100%' /><br /><i>".$mlng['edit_cfname_desc']."</i></td>";
print "</tr>\n<tr>";
print "<td class='edit_t'>".$mlng['edit_cfurl']."</td>";
print "<td class='edit_u'><input type='text' name='value7' value='".constant("conFiles::dlc_content_generator_url")."' size='100%' /><br /><i>".$mlng['edit_cfurl_desc']."</i></td>";
print "</tr>\n<tr>";
print "<td class='edit_t'>".$mlng['edit_cfkey']."</td>";
print "<td class='edit_u'><input type='text' name='value8' id='value8' value='".constant("conFiles::dlc_cache_keys_filename")."' size='100%' />
       <input type='button' name='vbutton8' onclick='javascript:activeDialog(8);' value='".$mlng['edit_selF1']."' />
       <br /><i>".$mlng['edit_cfkey_desc']."</i></td>";
     
print "</tr>\n<tr>";

// Submit
print "<td></td><td><input type='submit' name='submit' value='".$mlng['edit_submit']."' /></td></tr></table>\n";
?>
</form>
<?php } ?>
</body>
</html>
