<?php
class PHP_func {
/*******************************************************************************
 *                                                                             *
 *                      MySQL-Verbindungsaufbau                                *
 *                                                                             *
 ******************************************************************************/

  // anderen Klassen zugaenglich : protected
 //protected $prot;          // Geschützte Variable
 
 // Oeffentliche Variablen : public
 //fkt: text_split()
 public $SplitArray;              // Array mit gesplitteten Werten
 public $SplitArray2;             // Array mit gesplitteten Werten
 public $SplitUBound;             // Anzahl der Splittergebnisse
 public $SplitUBound2;            // Anzahl der Splittergebnisse
 public $TextFindPos;                           // Position des gefundenen Textes
 public $FileName;                // Dateiname          
 
 // interne Variablen : private
 //private $priv;             // Private Variable
 
 function __construct()
 {
  // Alles hier wird default bei Instanzierung aufgerufen
 }
 
 function __destruct()
 {
  // Alles hier wird bei  Scriptende aufgerufen
 }
 
 function __get($strVariable)
 {
  // Aufruf einer nicht definierten Variable
  throw new ExtException(basename(__FILE__),"Variable '<b>".$strVariable."
            </b>' existiert nicht!",__LINE__, __CLASS__ . "{}", __FUNCTION__. "()");
 }
 
 function __set($strVariable,$strValue) {
  // Aufruf einer nicht definierten Variable, aber Weiterverarbeitung
  switch($strVariable) {
    default:
      throw new ExtException(basename(__FILE__),"Variable '<b>"
            .$strVariable."</b>' existiert nicht!",__LINE__, __CLASS__ . "{}", __FUNCTION__. "()");
      break;
  }
 }

  //Zu Seite wechseln
  public function NavigatePage($seite) {
    echo "<script language='javascript'>\n";
    echo "  location.replace('".$seite."');\n";
    echo "</script>\n";
  }
   
  //Seite neu laden
  public function ReloadPage() {
    echo "<script language='javascript'>\n";
    echo "  location.reload();\n";
    echo "</script>\n";
  }
 
  // Text ersetzen
  public function istr_replace($search,$replace,$source) {
    if($source!="") {
      $new_string = str_replace($search,$replace,$source);
      if($new_string!="") {
        return $new_string;
      } else {
        throw new ExtException(basename(__FILE__),"Textersetzung: $search durch $replace"
                  ." in String($source) schlug fehl",__LINE__, __CLASS__ . "{}", __FUNCTION__. "()");
      }
    }  
  }
 
  // Text erstetzen (regEx)
  public function pstr_replace($search,$replace,$source) {
    if($source!="") {
      $new_string =  preg_replace($search,$replace,$source);
      if($new_string!="") {
        return $new_string;
      } else {
        throw new ExtException(basename(__FILE__),"Textersetzung: $search durch $replace"
                  ." in String($source) schlug fehl",__LINE__, __CLASS__ . "{}", __FUNCTION__. "()");
      }
    }
  }

  // Text suchen
  public function text_find($search,$source,$start=-1) {
    // $regEx (default: false)
      //      -> true, dann muss $search eine regulare Expression sein!!!
    if($start!=-1) {
      $search2 = substr($search,1,strlen($search)-2);
      $TextFindPos = strpos($search2,$source,$start);
      $TextFindPos = $search2;
    }
    if(preg_match($search,$source)==1) {
      return true;
    } else {
      return false;
    }                              
  }
 
  // Text in Array Splitten
  public function text_split($search,$source,$retError=0,$limit=0) {
    /* Example for split:
    '//'          Einzelne Zeichen
    '/ /'         Spaces
    '/[.]/'       Punkt :)
    $limit => wieviel mal geteilt werden darf .., default: 0 (parameter weglassen ;)
    $retError =>  Return Error, wenn kein Split (default), 1 = Return $source
    */
    $this->SplitArray = preg_split($search,$source,$limit);
    $this->SplitUBound = count($this->SplitArray)-1;
    if(!$this->SplitUBound) {
      if($retError==0) {
        throw new ExtException(basename(__FILE__),"Textaufteilen: $source bei $search aufteilen"
                  ." schlug fehl",__LINE__, __CLASS__ . "{}", __FUNCTION__. "()");
      } else {
        $this->SplitUBound = 0;
      }
    }
  }

  // Text in Array Splitten (Sekundärfunktion)
  public function text_split2($search,$source,$retError=0,$limit=0) {
    /* Example for split:
    '//'          Einzelne Zeichen
    '/ /'         Spaces
    $limit => wieviel mal geteilt werden darf .., default: 0 (parameter weglassen ;)
    $retError =>  Return Error, wenn kein Split (default), 1 = Return $source
    */
    $this->SplitArray2 = preg_split($search,$source,$limit);
    $this->SplitUBound2 = count($this->SplitArray2)-1;
    if(!$this->SplitUBound2) {
      if($retError==0) {
        throw new ExtException(basename(__FILE__),"Textaufteilen: $source bei $search aufteilen"
                  ." schlug fehl",__LINE__, __CLASS__ . "{}", __FUNCTION__. "()");
      } else {
        $this->SplitUBound2 = 0;
      }
    }
  }  
 
  // Array Leestring durch integer 0 ersetzen
  public function rZero($searchVal) {
    if($searchVal=="") {
      return 0;
    } else {
      return $searchVal;
    }
  }
 
  // Umlaute ersetzen
  public function RUml($text,$u8=false) {
    if($u8==true) { $text = utf8_decode($text); }
    $umlaute = array("Ä","Ö","Ü","ä","ö","ü","ß");
    $korrekt = array("Ae","Oe","Ue","ae","oe","ue","ss");
    $text = str_replace($umlaute,$korrekt,$text);
    return $text;
  }
 
  // Text abkuerzen, zu lange Texte mit .. verkuerzen
  public function shortName($text,$laenge=15) {
    if(strlen($text)>$laenge) {
      return substr($text,0,$laenge)."...";
    } else {
      return $text;
    }
  }
 
    /* Verschluesseln */
  public function encrypt($string, $key) {
    $result = '';
    for($i=0; $i<strlen($string); $i++) {
      $char = substr($string, $i, 1);
      $keychar = substr($key, ($i % strlen($key))-1, 1);
      $char = chr(ord($char)+ord($keychar));
      $result.=$char;
    }

    return base64_encode($result);
  }

  /* Entschluesseln*/
  public function decrypt($string, $key) {
    $result = '';
    $string = base64_decode($string);

    for($i=0; $i<strlen($string); $i++) {
      $char = substr($string, $i, 1);
      $keychar = substr($key, ($i % strlen($key))-1, 1);
      $char = chr(ord($char)-ord($keychar));
      $result.=$char;
    }

    return $result;
  }
 
  // PW Generation
  function register_generate_salt() {
    $pattern = "1234567890abcdefghijklmnopqrstuvwxyz";
    for ($i=0; $i<10; $i++)
    {
      if (isset($key))
        $key .= $pattern[rand(0,35)];
      else
        $key = $pattern[rand(0,35)];
    }
    return $key;
  }
 
  function create_passwd ($pattern="",$length=0) {
    // Optionale Mitgabe des "pattern"
    if($pattern=="") { $pattern = "2345679abcdefghjkmnpqrstuvwxyz"; }
    if($length==0)  { $length = 10; }
    for ($i=0; $i<$length; $i++)
    {
      if (isset($passwd))
        $passwd .= $pattern[rand(0,29)];
      else
        $passwd = $pattern[rand(0,29)];
      }
    return $passwd;
  }
 
  function mysql2date($mysql_date,$format="") {
    // Format: zb. "d.m.Y" oder "D, d.M.Y"
    if($mysql_date!=NULL) {
      $this->text_split("/-/",$mysql_date);
      if($format=="" && $this->SplitUBound==2) {
        return implode(".",array_reverse($this->SplitArray));
      } elseif($format!="" & $this->SplitUBound==2) {
        $timestr = mktime(0,0,0,$this->SplitArray[1],$this->SplitArray[2],$this->SplitArray[0]);
        return date($format, $timestr);
      } else {
        $this->SplitUBound;
      }
    } else {
      throw new ExtException(basename(__FILE__),"Input: $mysql_date ist kein gueltiges MySQL-Datum (YYYY-MM-DD)"
      ,__LINE__, __CLASS__ . "{}", __FUNCTION__. "()");
    }
  }
 
  function mysql2datetime($mysql_date_time,$short_t=false) {
    // $short_t: false = dd.mm.yyyy hh:mm:ii, true = dd.mm.yyyy hh:mm
    if($mysql_date_time!=NULL) {
      $this->text_split("/ /",$mysql_date_time);
      $this->text_split2("/-/",$this->SplitArray[0]);
      if($short_t==false) {
        return implode(".",array_reverse($this->SplitArray2))." ".$this->SplitArray[1];
      } else {
        return implode(".",array_reverse($this->SplitArray2))." ".substr($this->SplitArray[1],0,5);
      }
    } else {
      throw new ExtException(basename(__FILE__),"Input: $mysql_date_time ist kein gueltiges MySQL-Datum/Zeit (YYYY-MM-DD HH:mm:ii)"
      ,__LINE__, __CLASS__ . "{}", __FUNCTION__. "()");
    }
  }
 
  function date2mysql($normal_date,$short_y=false,$quote=true) {
    // $normal_date: d.m.Y!
    // Sshort_y : false = YYYY, true = YY
    // $quote : false = return-value, true = 'return-value'
    if($normal_date!=NULL) {
      $this->text_split("/\./",$normal_date);
      if($short_y==true) $this->SplitArray[2] = "20".$this->SplitArray[2];
      if($quote==true) {
        return "'".implode("-",array_reverse($this->SplitArray))."'";
      } else {
        return implode("-",array_reverse($this->SplitArray));      
      }
    } else {
      throw new ExtException(basename(__FILE__),"Input: $normal_date ist kein gueltiges Datum (dd.mm.YYYY)"
      ,__LINE__, __CLASS__ . "{}", __FUNCTION__. "()");
    }
  }
 
  function arab2roem($arabische_zahl) {
   // Quelle: http://www.roemische-ziffern.de/Roemische-Zahlen-PHP-berechnen.html
   $ar_r = array( "M","CM","D","CD","C","XC","L","XL","X","IX","V","IV","I");
   $ar_a = array(1000, 900,500, 400,100, 90,  50, 40,  10,   9,  5,   4,  1);

   for ($count=0; $count < count($ar_a); $count++) {
      while ($arabische_zahl >= $ar_a[$count]) {
         $roemische_zahl .= $ar_r[$count];
         $arabische_zahl -= $ar_a[$count];
      }
   }
   return $roemische_zahl;
  }
 
  // Dateiendung ermitteln
  public function dateiendung($dateiname) {
    $pathinfo = pathinfo($dateiname);
    if(!$pathinfo['extension']) {
      throw new ExtException(basename(__FILE__),"Datei: $dateiname hat keine Dateiendung!"
      ,__LINE__, __CLASS__ . "{}", __FUNCTION__. "()");
    } else {
      return $pathinfo['extension'];
    }  
  }

  function create_filename ($pre="") {
    // Erzeugen eines einmaligen Dateinamens mittels MicroTIME
    $new_filename = microtime();
    $this->text_split('/ /',$new_filename);
    $new_filename = $this->SplitArray[0] + $this->SplitArray[1];
    ($pre!="") ? $this->FileName = $this->RUml($pre)."_".$new_filename : $this->FileName = $new_filename;
  }
 
  function mailme($from, $to,$betreff,$body,$add_to="",$filename="") {
    // Alte Email-Funktion
    $this->sendEmail($to,$from,"",$betreff,$body,$filename,$add_to);
  }
 
  function sendEmail($to, $from, $from_name, $subject, $message, $files="", $head_add="") {
    /*
    $to         = receipient address
    $from       = name of sender
    $from_name  = sender address
    $subject    = Subject
    $body       = eMail Body
    $attach     = 1 File or an array of files(!)
    $head_add   = Additional Values for Head, ex. CC, BCc
    */
    // Working with the files
    if($files!="") {
        // Attachment exists
        if(!is_array($files)) { $files = array($files); }
               
        $attachment = array();
        foreach($files AS $key => $value) {
            if(is_int($key)) {
                $file = $value;
                $name = basename($file);
            } else {
                $file = $key;
                $name = basename($value);
            }
           
            $size = filesize($file);
            $data = file_get_contents($file);
            $type = mime_content_type($file);
           
            $attachment[] = array("name"=>$name, "size"=>$size, "type"=>$type, "data"=>$data, "file"=>$file);
        }
       
        $mime_boundary = md5(uniqid(microtime(), true));
    }
   
    // Encoding
    $encoding = mb_detect_encoding($message, "utf-8, iso-8859-1, cp-1252");
   
    // Header
    $header  = "From: \"$from_name\" <$from>\r\n";
    if($head_add!="") {
        $header .= $head_add."\r\n";
    }
   
    if(is_array($files)) {
        $header .= "Content-Type: multipart/mixed;\r\n";
        $header .= "    boundary=\"".$mime_boundary."\"\r\n";
        $header .= "MIME-Version: 1.0\r\n";
       
        $content  = "--".$mime_boundary."\r\n";
        $content .= "Content-Type: text/plain; charset=\"$encoding\"\r\n";
        $content .= "Content-Transfer-Encoding: quoted-printable\r\n\r\n";
        $content .= $message."\r\n";
       
        foreach($attachment AS $att) {
            $data = chunk_split(base64_encode($att['data']));
            $content .= "--".$mime_boundary."\r\n";
            $content .= "Content-Type: ".$att['type'].";\r\n";
            $content .= "       name=\"".$att['name']."\"\r\n";
            $content .= "Content-Description: ".$att['name']."\"\r\n";
            $content .= "Content-Disposition: attachment;\r\n";
            $content .= "       filename=\"".$att['name']."\n; size=".$att['size'].";\r\n";
            $content .= "Content-Transfer-Encoding: base64\r\n\r\n";
            $content .= $data."\r\n";        
        }    
       
        $content .= "--".$mime_boundary."--";        
    }  else {
        $content = $message;  
    }
   
    $result = mail($to, $subject, $content, $header);
    if($result) {
      print "<br /><b>eMail wurde erfolgreich versendet an $to</b>";
    } else {
      throw new ExtException(basename(__FILE__),"eMail von: $from konnte nicht an $to gesendet werden.<br />
         $subject\n<b>Text:</b> $message<br />Dateiname: <pre>".print_r($filename)."</pre><br />AddHead: $add_to",__LINE__, __CLASS__ . "{}", __FUNCTION__. "()");
    }  
  }  
 
 
 
  public function encrypt_decrypt($action, $string, $key = 'rh437AdZTStbG4sXmGkVAMzpbc2SLazyWgpQ25cwKHWZM9Tk57ZJzWfu4kvQXL2dwujLPuwAMjXQRAZJfgdXpWBVQNMZBfn243sF') {
     // initialization vector
     $iv = md5(md5($key));
     if( $action == 'encrypt' ) {
         $output = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, $iv);
         $output = base64_encode($output);
     } else if( $action == 'decrypt' ) {
         $output = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($string), MCRYPT_MODE_CBC, $iv);
         $output = trim($output);
         $output = $this->istr_replace("\0","",$output);
     }
     return $output;
  }
 
  public function deleteTree($dir) {
    $files = array_diff(scandir($dir), array('.','..'));
    foreach($files as $file) {
      (is_dir($dir/$file)) ? $this->deleteTree("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);  
  }
 
  public function bytesToSize($bytes, $precision = 2, $modus = 1024) {
    // precision = decimal positions
    // modus     = factor: 1024 or 1000  
    $kilobyte = $modus;
    $megabyte = $kilobyte * $modus;
    $gigabyte = $megabyte * $modus;
    $terabyte = $gigabyte * $modus;
   
    if (($bytes >= 0) && ($bytes < $kilobyte)) {
        return $bytes . ' B';
 
    } elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
        return round($bytes / $kilobyte, $precision) . ' KB';
 
    } elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
        return round($bytes / $megabyte, $precision) . ' MB';
 
    } elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
        return round($bytes / $gigabyte, $precision) . ' GB';
 
    } elseif ($bytes >= $terabyte) {
        return round($bytes / $terabyte, $precision) . ' TB';
    } else {
        return $bytes . ' B';
    }
  }
 
  public function feiertag($datum,$wochent=0, $return="tf",$arrOut=false) {
    // datum:   mysql-Datum: 2016-01-01 17:15:00 oder 2016-01-01
    // wochent: 0    = Kein Rückgabe von Wochentagen
    //          1    = Rückgabe von Wochentagen, wenn $return='name' und kein Feiertag
    //          2    = Rückgabe von SA,SO,WoTag, wenn $return='name' und kein Feiertag
    // return:  name = Gibt den Namen des Feiertages zurück
    //          tf   = Gibt true/false zurück
    //          id   = Gibt den Schluesselwert des Arrays zurück
    // arrOut:  Array's ausgeben, zwecks Doku: feiertag("2016-01-01",'tf',true);
 
    if($datum!="0000-00-00" && $datum!="0000-00-00 00:00:00") {
      $datum = strtotime($datum);
      $jahr  = date("Y",$datum);
      $tag   = date("m-d",$datum);
     
      // Wochentag
      if($wochent>=1) {
        if($wochent==1) {
          $wochentage = Array("Sonntag","Montag","Dienstag","Mittwoch","Donnerstag","Freitag","Samstag");
          $wtag = date("w",$datum);
          if($return=="name") { $wtag = $wochentage[$wtag]; }
        } else {
          $wochentage = Array("Sonntag","Wochentag","Samstag");
          $wtag = date("w",$datum);
          switch($wtag) {
            case 0:  ($return=="name") ? $wtag = $wochentage[0]: $wtag = 0; break;
            case 1: case 2: case 3: case 4:
            case 5:  ($return=="name") ? $wtag = $wochentage[1]: $wtag = 1; break;
            case 6:  ($return=="name") ? $wtag = $wochentage[2]: $wtag = 2; break;
            default: ($return=="name") ? $wtag = $wochentage[1]: $wtag = 1; break;
          }                  
        }
      }
     
      // Fixe Feiertage
      $feiertag_name = Array(10=>"Neujahr",11=>"Heilige Drei Koenige",12=>"Staatsfeiertag",13=>"Maria Himmelfahrt",
                             14=>"Nationalfeiertag",15=>"Allerheiligen",16=>"Maria Empfaengnis",
                             17=>"Heiliger Abend",18=>"Weihnachten",19=>"Stefanitag",20=>"Silvester");
      $feiertag_tag  = Array(10=>"01-01",11=>"01-06",12=>"05-01",13=>"08-15",14=>"10-26",15=>"11-01",16=>"12-08",
                             17=>"12-24",18=>"12-25",19=>"12-26",20=>"12-31");
      // Bewegliche Feiertage, basierend auf dem Osterdatum des Jahres
      $tage  = 60 * 60 * 24;
      //$oSonn = easter_date($jahr);
     
      // Ostern nach Gauss
      $a = $jahr % 19;                            // Formel nach Gauß  
      $b = $jahr % 4;                             // Werte für den Zeitraum
      $c = $jahr % 7;                             // 1900 - 2099
      $m = number_format(8 * number_format($jahr / 100) + 13) / 25 - 2;
      $s = number_format($jahr / 100) - number_format ($jahr / 400) - 2;
      $M = (15 + $s - $m) % 30;
      $N = (6 + $s) % 7;
      $d = ($M + 19 * $a) % 30;
     
      if($d == 29) {
        $D = 28;
      } elseif($d == 28 && $a >=11) {
        $D = 27;
      } else {
        $D = $d;
      }
     
      $e = (2 * $b + 4 * $c + 6 * $D + $N) % 7;
      $oSonn = mktime (0,0,0,3,21,$jahr) + (($D + $e + 1) * 86400);
     
      $feiertag_name[] = "Karfreitag";
      $feiertag_tag[]  = date("m-d",$oSonn - 2 * $tage);
      $feiertag_name[] = "Ostersonntag";
      $feiertag_tag[]  = date("m-d",$oSonn);
      $feiertag_name[] = "Ostermontag";
      $feiertag_tag[]  = date("m-d",$oSonn + 1 * $tage);
      $feiertag_name[] = "Christi Himmelfahrt";
      $feiertag_tag[]  = date("m-d",$oSonn + 39 * $tage);        
      $feiertag_name[] = "Pfingstsonntag";
      $feiertag_tag[]  = date("m-d",$oSonn + 49 * $tage);      
      $feiertag_name[] = "Pfingstmontag";
      $feiertag_tag[]  = date("m-d",$oSonn + 50 * $tage);    
      $feiertag_name[] = "Fronleichnam";
      $feiertag_tag[]  = date("m-d",$oSonn + 60 * $tage);
      $array_K = array_search($tag,$feiertag_tag);
     
      // Array ausgeben?
      if($arrOut==true) {
        if (is_array($wochentage)) { print_r($wochentage); }
        print_r($feiertag_name);
        print_r($feiertag_tag);
      }
     
      if($array_K>=0 && $array_K!==false) {
        switch($return){
          case "name":  return $feiertag_name[$array_K];  break;
          case "tf":    return true;                      break;
          case "id":    return $array_K;                  break;
        }
      } else {
        switch($return){
          case "name":  return ($wochent==0) ? "" : $wtag; break;
          case "tf":    return false; break;
          case "id":    return ($wochent==0) ? NULL : $wtag; break;
        }
      }
    } else {
      switch($return){
        case "name":  return ($wochent==0) ? "" : $wtag; break;
        case "tf":    return false; break;
        case "id":    return NULL;  break;
      }
    }
  }  
}
?>