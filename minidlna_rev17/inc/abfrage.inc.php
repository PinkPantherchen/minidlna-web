<?php
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
 <meta http-equiv="content-type" content="text/html; charset=utf-8" />
 <link rel="stylesheet" type="text/css" href="../css/<?php print $_SESSION['style']; ?>/ui.dynatree.css" />
 <link rel="stylesheet" type="text/css" href="../css/default.css" />
 <meta name="language" content="<?php print $_SESSION['lang']; ?>" />
 <meta name="content-language" content="<?php print $_SESSION['lang']; ?>" />
</head>
<body>
<pre>
<?php
//error_reporting(5);
// Sprache einbinden
require_once("../lng/".$_SESSION['lang'].".lng");
// Config einbinden
include("config.inc.php");
if($jdownload=='true') { 
  include("../script/dlcapi.class.php");
  // Create instance; 
  $conFiles = new conFiles();
  $file = time();
}
$db   = new SQLite3("$path_to_db");
$dlcf = array();
function MakeJD() {
  global $dlcf;
  global $conFiles;
  global $file;
  // Create a new data model
  $intModelId = $conFiles->createDataModel();
  // BasePath
  $bP = constant("conFiles::dlc_content_generator_url");
  // Add a new package for the data model
  $intPackageId = $conFiles->addFilePackage($intModelId,'Dateisammlung','','','');
  // Add Links to this package
  foreach($dlcf as $name => $link) {
    $conFiles->addLink($intModelId,$intPackageId,$bP.$link,$name);
  }
  // Save as DLC container file
  $strDLCStream = $conFiles->createDLC($intModelId);
  file_put_contents('../jd/tmp/'.$file.'.dlc',$strDLCStream);
  // Save as CCF container file
  $strCCFStream = $conFiles->createCCF($intModelId);
  file_put_contents('../jd/tmp/'.$file.'.ccf',$strCCFStream);
  // Save as RSDF container file
  $strRSDFStream = $conFiles->createRSDF($intModelId);
  file_put_contents('../jd/tmp/'.$file.'.rsdf',$strRSDFStream);
  // Use this, if you want to see errors
  if ($conFiles->isError()) {
    //echo $conFiles->showError();
    return false;
  } else {
    return true;
  } 
}

function human_filesize($bytes, $dec = 2): string {
    $size   = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    if ($factor == 0) $dec = 0;

    return sprintf("%.{$dec}f %s", $bytes / (1024 ** $factor), $size[$factor]);
}

if(isset($_GET['id']) && $_GET['detail']=="false") {
  // Liste der Dateien in einem Verzeichnis  
  $sql = "SELECT `NAME`,`CLASS`,`OBJECT_ID`,`PATH` 
              FROM `OBJECTS` `OB`
         LEFT JOIN `DETAILS` `DT`
                ON `OB`.`DETAIL_ID`=`DT`.`ID`
             WHERE `OB`.`PARENT_ID`='".$_GET['id']."' AND
                   `OB`.`CLASS`<>'container.storageFolder'
          ORDER BY `OBJECT_ID`";      
  $res = $db->query($sql);
  if($debug_sqls=='true') print $sql."<br />";
  while($zeilen = $res->fetchArray()) {
    $downloadable = true;
    switch($zeilen['CLASS']) {
      case "item.videoItem":
        print "<a href='javascript:parent.iSrc(\"detail=true&amp;id=".$zeilen['OBJECT_ID']."\");'><img src='/icons/movie.gif' alt='movie' /> ".$zeilen['NAME']."</a><br />";
        break;
      case "item.imageItem.photo":
        print "<a href='javascript:parent.iSrc(\"detail=true&amp;id=".$zeilen['OBJECT_ID']."\");'><img src='/icons/image2.gif' alt='image' /> ".$zeilen['NAME']."</a><br />";                
        break;
      case "item.audioItem.musicTrack":
        print "<a href='javascript:parent.iSrc(\"detail=true&amp;id=".$zeilen['OBJECT_ID']."\");'><img src='/icons/sound2.gif' alt='sound' /> ".$zeilen['NAME']."</a><br />";
        break;
      default:
        $downloadable = false;
    }
    if($downloadable==true && $zeilen['PATH']!="" && $jdownload=='true') { $dlcf[$zeilen['NAME']] = $zeilen['PATH']; }
  }
  if(!empty($dlcf)) {
    print "<br /><br />";
    if(MakeJD()==true) {
      print "<a href='../jd/tmp/$file.dlc'><img src='../img/dlc.png' border='0'></a>";
      print "<a href='../jd/tmp/$file.ccf'><img src='../img/ccf.png' border='0'></a>";
      print "<a href='../jd/tmp/$file.rsdf'><img src='../img/rsdf.png' border='0'></a>";
    } else {
      print "<div style='border: 1px solid #440000'>".$conFiles->showError()."</div>";
    }
  }  
} elseif(isset($_GET['id']) && $_GET['detail']=="true") {
  // Einzelnes Element => Details anzeigen
  $sql = "SELECT * 
              FROM `OBJECTS` `OB`
         LEFT JOIN `DETAILS` `DT`
                ON `OB`.`DETAIL_ID`=`DT`.`ID`
             WHERE `OB`.`OBJECT_ID`='".$_GET['id']."' AND
                   `OB`.`CLASS`<>'container.storageFolder'
          ORDER BY `OBJECT_ID`";      
  $res = $db->query($sql);
  $zeilen = $res->fetchArray();
  $cols = $res->numColumns();
  if($debug_sqls=='true') print $sql."<br />";
  print "<table>\n";
  for($i = 1; $i < $cols; $i++) {
    switch($res->columnName($i)) {
      case "SIZE":
        if($zeilen[$i]!="") {
          print "<tr><td class='info'>".$res->columnName($i).":</td><td>".$zeilen[$i]." (".human_filesize($zeilen[$i]).")</td></tr>\n";
        }
        break;
      case "TIMESTAMP":
        if($zeilen[$i]!="") {
          print "<tr><td class='info'>".$res->columnName($i).":</td><td>".$zeilen[$i]." (".date('d.m.Y H:i:s',$zeilen[$i]).")</td></tr>\n";
        }
        break;      
      default:
        if($zeilen[$i]!="") {
          print "<tr><td class='info'>".$res->columnName($i).":</td><td>".$zeilen[$i]."</td></tr>\n";
        }
        break;
    }
  }
  if($fdownload=='true') { 
    $path_info = pathinfo($zeilen['PATH']);
    print "<tr><td></td><td><a href='".$zeilen['PATH']."'>".$mlng['downloal']."</a></td></tr>";
    print "<tr><td></td><td><a href='".$server_http."/MediaItems/".$zeilen['DETAIL_ID'].".".$path_info['extension']."'>".$mlng['download']."</a></td></tr>\n"; 
  }
  print "</table>";
} elseif(isset($_GET['genre'])) {
  unset($_SESSION['suchen']);
  unset($_SESSION['fclass']);
  // Alle Eintraege eines Genre
  $sql = "SELECT `NAME`,`CLASS`,`OBJECT_ID`,`PATH`  
              FROM `OBJECTS` `OB`
         LEFT JOIN `DETAILS` `DT`
                ON `OB`.`DETAIL_ID`=`DT`.`ID`
             WHERE `DT`.`GENRE`='".$_GET['genre']."' AND
                   `OB`.`CLASS`<>'container.storageFolder' AND
                   `PARENT_ID` NOT LIKE '64%'
          ORDER BY `OBJECT_ID`";  
  $res = $db->query($sql);
  if($debug_sqls=='true') print $sql."<br />"; 
  while($zeilen = $res->fetchArray()) {
    $downloadable = true;
    switch($zeilen['CLASS']) {
      case "item.videoItem":
        print "<a href='javascript:parent.iSrc(\"detail=true&amp;id=".$zeilen['OBJECT_ID']."\");'><img src='/icons/movie.gif' alt='movie' /> ".$zeilen['NAME']."</a><br />";
        break;
      case "item.imageItem.photo":
        print "<a href='javascript:parent.iSrc(\"detail=true&amp;id=".$zeilen['OBJECT_ID']."\");'><img src='/icons/image2.gif' alt='image' /> ".$zeilen['NAME']."</a><br />";        
        break;
      case "item.audioItem.musicTrack":
        print "<a href='javascript:parent.iSrc(\"detail=true&amp;id=".$zeilen['OBJECT_ID']."\");'><img src='/icons/sound2.gif' alt='sound' /> ".$zeilen['NAME']."</a><br />";
        break;
      default:
        $downloadable = false;
    }
    if($downloadable==true && $zeilen['PATH']!="" && $jdownload=='true') { $dlcf[$zeilen['NAME']] = $zeilen['PATH']; }
  }
  if(!empty($dlcf)) {
    print "<br /><br />";
    if(MakeJD()==true) {
      print "<a href='../jd/tmp/$file.dlc'><img src='../img/dlc.png' border='0'></a>";
      print "<a href='../jd/tmp/$file.ccf'><img src='../img/ccf.png' border='0'></a>";
      print "<a href='../jd/tmp/$file.rsdf'><img src='../img/rsdf.png' border='0'></a>";
    } else {
      print "<div style='border: 1px solid #440000;'>".$conFiles->showError()."</div>";
    }
  }
} elseif(isset($_GET['artist'])) {
  unset($_SESSION['suchen']);
  unset($_SESSION['fclass']);
  // Alle Eintraege eines Artisten
  $sql = "SELECT `NAME`,`CLASS`,`OBJECT_ID`,`PATH`  
              FROM `OBJECTS` `OB`
         LEFT JOIN `DETAILS` `DT`
                ON `OB`.`DETAIL_ID`=`DT`.`ID`
             WHERE `DT`.`ARTIST`='".$_GET['artist']."' AND
                   `OB`.`CLASS`<>'container.storageFolder' AND
                   `PARENT_ID` NOT LIKE '64%'
          ORDER BY `OBJECT_ID`";  
  $res = $db->query($sql);
  if($debug_sqls=='true') print $sql."<br />";
  while($zeilen = $res->fetchArray()) {
    $downloadable = true;
    switch($zeilen['CLASS']) {
      case "item.videoItem":
        print "<a href='javascript:parent.iSrc(\"detail=true&amp;id=".$zeilen['OBJECT_ID']."\");'><img src='/icons/movie.gif' alt='movie' /> ".$zeilen['NAME']."</a><br />";
        break;
      case "item.imageItem.photo":
        print "<a href='javascript:parent.iSrc(\"detail=true&amp;id=".$zeilen['OBJECT_ID']."\");'><img src='/icons/image2.gif' alt='image' /> ".$zeilen['NAME']."</a><br />";        
        break;
      case "item.audioItem.musicTrack":
        print "<a href='javascript:parent.iSrc(\"detail=true&amp;id=".$zeilen['OBJECT_ID']."\");'><img src='/icons/sound2.gif' alt='sound' /> ".$zeilen['NAME']."</a><br />";
        break;
      default:
        $downloadable = false;
        break;
    }
    if($downloadable==true && $zeilen['PATH']!="" && $jdownload=='true') { 
      $path_info = pathinfo($zeilen['PATH']);
      $dlcf[$zeilen['NAME']] = "/MediaItems/".$zeilen['DETAIL_ID'].".".$path_info['extension'];
    }
  }
  if(!empty($dlcf)) {
    print "<br /><br />";
    if(MakeJD()==true) {
      print "<a href='../jd/tmp/$file.dlc'><img src='../img/dlc.png' border='0'></a>";
      print "<a href='../jd/tmp/$file.ccf'><img src='../img/ccf.png' border='0'></a>";
      print "<a href='../jd/tmp/$file.rsdf'><img src='../img/rsdf.png' border='0'></a>";
    } else {
      print "<div style='border: 1px solid #440000;'>".$conFiles->showError()."</div>";
    }
  }   
} elseif(isset($_SESSION['suchen']) && $_SESSION['suchen']!="") {
  // Suche
  $suchen = $_SESSION['suchen'];
  $fclass = $_SESSION['fclass']; 
  $sql = "SELECT `NAME`,`CLASS`,`OBJECT_ID`,`PATH` "; 
  $part = "            FROM `OBJECTS` `OB`
         LEFT JOIN `DETAILS` `DT`
                ON `OB`.`DETAIL_ID`=`DT`.`ID`
             WHERE (`PATH`   LIKE '%$suchen%'
                OR `ARTIST`  LIKE '%$suchen%'
                OR `ALBUM`   LIKE '%$suchen%'
                OR `GENRE`   LIKE '%$suchen%'
                OR `TITLE`   LIKE '%$suchen%'
                OR `NAME`    LIKE '%$suchen%'
                OR `CREATOR` LIKE '%$suchen%'
                OR `COMMENT` LIKE '%$suchen%')
               AND `CLASS`   LIKE '%$fclass%' 
          ORDER BY `OBJECT_ID`";
  // Anzahl Ergebnisse ermitteln
  $res = $db->query("SELECT COUNT(*) as `cnt` $part");
  $row = $res->fetchArray();
  $row = $row['cnt'];          
  // Datenabfrage
  $res = $db->query($sql.$part);
  if($debug_sqls=='true') print $sql."\n".$part."<br />";
  print "<b><u>Gefundene Eintr&auml;ge:</u></b> $row\n\n";
  while($zeilen = $res->fetchArray()) {
    $downloadable = false;
    switch($zeilen['CLASS']) {
      case "item.videoItem":
        print "<a href='javascript:parent.iSrc(\"detail=true&amp;id=".$zeilen['OBJECT_ID']."\");'><img src='/icons/movie.gif' alt='movie' /> ".$zeilen['NAME']."</a><br />";
        $downloadable = true;          
        break;
      case "item.imageItem.photo":
        print "<a href='javascript:parent.iSrc(\"detail=true&amp;id=".$zeilen['OBJECT_ID']."\");'><img src='/icons/image2.gif' alt='image' /> ".$zeilen['NAME']."</a><br />";        
        $downloadable = true;
        break;
      case "item.audioItem.musicTrack":
        print "<a href='javascript:parent.iSrc(\"detail=true&amp;id=".$zeilen['OBJECT_ID']."\");'><img src='/icons/sound2.gif' alt='sound' /> ".$zeilen['NAME']."</a><br />";
        $downloadable = true;
        break;
      case "container.storageFolder":
        print "<img src='/icons/folder.gif' alt='folder' /> ".$zeilen['NAME']."</a><br />";
        break;
      case "container.playlistContainer":
        print "<img src='/icons/text.gif' alt='playlist' /> ".$zeilen['NAME']."</a><br />";
        break;
      case "container.person":
        print "<img src='/icons/hand.up.gif' alt='person' /> ".$zeilen['NAME']."</a><br />";
        break;
      case "container.person.musicArtist":
        print "<img src='/icons/hand.right.gif' alt='person.artist' /> ".$zeilen['NAME']."</a><br />";
        break;
      case "container.album";
      case "container.album.musicAlbum":
        print "<img src='/icons/box1.gif' alt='album.music' /> ".$zeilen['NAME']."</a><br />";
        break;
      case "container.album.photoAlbum":
        print "<img src='/icons/image3.gif' alt='album.images' /> ".$zeilen['NAME']."</a><br />";
        break; 
      default:       
        print "<img src='/icons/unkown.gif' alt='unkown' /> ".$zeilen['NAME']."</a><br />";
        break;
    }
    if($downloadable==true && $zeilen['PATH']!="" && $jdownload=='true') { $dlcf[$zeilen['NAME']] = $zeilen['PATH']; }
  }
  if(!empty($dlcf)) {
    print "<br /><br />";
    if(MakeJD()==true) {
      print "<a href='../jd/tmp/$file.dlc'><img src='../img/dlc.png' border='0'></a>";
      print "<a href='../jd/tmp/$file.ccf'><img src='../img/ccf.png' border='0'></a>";
      print "<a href='../jd/tmp/$file.rsdf'><img src='../img/rsdf.png' border='0'></a>";
    } else {
      print "<div style='border: 1px solid #440000;'>".$conFiles->showError()."</div>";
    }
  }          
} else {
  print "<i>Nothing to do</i>";
}
?>
</pre>
</body>
</html>