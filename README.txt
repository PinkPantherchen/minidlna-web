Installationsanleitung
**********************
Voraussetzung:
* Apache2
* PHP5<  (Getestet nur mit PHP5 - PHP8.1)
* PHP_SQLITE3
* mbstring
* mcrypt

Auf Ubuntu:
PHP81 apt-get install apache2 php8.1 php8.1-sqlite3 php8.1-mbstring sqlite3
      ACHTUNG: mcrypt muss händisch erstellt werden sonst gehn die JDOWNLOADER Dateien nicht
               Anleitung (Ubuntu): https://php.tutorials24x7.com/blog/how-to-install-mcrypt-for-php-8-on-ubuntu-20-04-lts
PHP8: apt-get install apache2 php8.0 php8.0-sqlite3 php8.0-mbstring sqlite3 php8.0-mcrypt
PHP<: apt-get install apache2 php php-sqlite3 php-mbstring sqlite3 php-mcrypt

Installation:
* Download minidlna.tar in ein Verzeichnis des Webservers
* Entpacken: tar -xvf minidlna.tar
* Berechtigungen anpassen:
  (NUR wenn Downloadmodul verwendet werden soll!!!)
  chmod 777 -R jd  
* Config-File anpassen (inc/config.inc.php)
  <snip>
  $path_to_db = {Absoluter Pfad zur Datenbank} (bsp.: "/var/lib/minidlna/files.db")
  $server_http = {Pfad zum Webinterface des MINIDLNA Servers} (bsp.: "http://minidlna:8200")
  $debug_sqls = {true = DB-Queries anzeigen, false = keine Ausgabe der DB-Queries} (default: false)
  $fdownload  = {true = Download der Files ermöglichen, false = kein Download} (default: false)
  $jdownload  = {true = DLC/CCF/RSDF-Dateien werden erzeugt für Bulk-Download} (default: false)
  </snip>
  
Quellen
*******
phpLiteAdmin: https://www.phpliteadmin.org/  

Bei erlaubtem (J-)Download sind folgende Dinge einzurichten
***********************************************************
Hier ein kleines Beispiel:

Videos/Musik/Bilder liegen in einem Verzeichnis
/FS1/{Videos/Musik/Bilder}
dann muss im Webserver-Root folgendes gemacht werden
a) cd /var/www/localhost/htdocs
b) ln -s /FS1
   .. nun können die Dateien heruntergeladen werden
c) zum Schutz empfiehlt es sich eine .htaccess anzulegen
   ein Beispiel dafür liegt im "files/" Verzeichnis
   - cp files/access.htaccess /FS1/.htaccess
   - cp files/access.minidlna /FS1/.minidlna
   - Zugriff für IP-Bereich 192.168.1.x 
   - Authentifizierung mit:
     User: meinNameIstHase
     Pass: meinPasswortIstGeheim
  
     TIPP: neue User/Passwörter erstellt man mit folgendem Befehl
          htpasswd -b /FS1/.minidlna {Benutzername} {Passwort}
     
     TIPP: Adressbereich für den Zugriff wird in der .htaccess 
          geändert, will man dies überhaupt nicht beschränken,
          dann einfach die Zeilen
          .. Apache 2.2 ..
          #Order deny,allow
          #deny from all
          #allow from 192.168.1
          .. Apache 2.4 ..
          #Require ip 192.168.1.0/24
          ..
          löschen oder mit # auskommentieren 

d) um das jdownloader-Modul zu nützen muss die Datei script/dlcapi.class.php
   bearbeitet werden:
   
   Zeile 64: hier den absoluten Pfad zum Medien-Verzeichnis über Web
             in meinem Beispiel: http://minidlna:8200 .. der Hostname ist srv1
   Zeile 66: hier de absolute Pfad zur Datei dlcapicache.txt
             in meinem Beispiel ist das /var/www/localhost/htdocs/minidlna/jd/dlcapicache.txt
             die genannte Datei muss les/schreibar sein für den Webserverbenutzer
             (chmod 777 dlcapicache.txt)
   .. damit funktionieren dann die DLC/CCF/RSDF-Dateien zb. im JDownloader (intern natürlich)
   
   TIP: Soll das ganze von extern funktionieren, muss der Webserver von aussen zugänglich sein:
        /var/www/localhost/htdocs/minidlna/script/dlcapi.class.php
        ..
        Zeile 64:    const dlc_content_generator_url         = 'http://minidlna:8200';
        Zeile 66:    const dlc_cache_keys_filename           = '/var/www/localhost/htdocs/minidlna/jd/dlcapicache.txt';
        ..
        /var/www/localhost/htdocs/FS1/.htaccess
        .. Apache 2.2 ..
        #Order deny,allow
        #deny from all
        #allow from 192.168.1
        .. Apache 2.4
        #Require ip 192.168.1.0/24
        ..
        
        .. dann kriegt man die Daten auch extern runter, muss sich aber anmelden, soll der
        Download ohne anmeldung erfolgen können, dann einfach die .htaccess in .htaccess_old umbenennen
        oder löschen (die .passwd könnte dann auch gelöscht werden)       
                
Administratives Interface
*************************
Ein neues Feature is das Administrative Interface, über welches man die Haupteinstellungen
per Web machen kann, allerdings ist es u.U. empfehlenswert dieses zu schützen, daher liegen dieses
Script in einem Unterordner namens /admin

Um da Verzeichnis zu schützen braucht man lediglich zwei Dateien erzeugen:
a) .htpasswd
   htpasswd -bc admin/.htpasswd {Username} {Passwort}
b) .htaccess
   <--- snip ---->
   AuthUserFile .htpasswd
   AuthName "FS1 - Administrativer Bereich (only internal)"
   AuthType Basic

  <Limit GET POST>
   Order deny,allow
   Deny from all
   Allow from 192.168.1
   require valid-user
  </Limit>

  ab Apache 2.4
  <Limit GET POST>
    Require ip 192.168.1.0/24
  </Limit>
    
  <--- /snip ---->
  
  Hier gilt das gleich wie ab Zeile 32 dieser Anleitung ;)
       
Styles
******
Um ein neues Style zu installieren:
* Erstelle ein Verzeichnis im Ordner "css"
* Erstelle in dem neuen Verzeichnis eine Datei: "ui.dynatree.css"
(Default)
default = Default-Style (Old Windows)
vista   = Vista-Style (Vista Windows)
none    = Keine Bilder (normale Auflistung ohne Symbole)

Sprachen
********
Alle Sprachen befinden sich im Ordner "lng"
* ?.lng = Sprachdatei
* ?.jpg = Sprachbild (24x12px [BxH])

Sprachkürzel [2stellig]
(Default)
de.lng  = Deutsch
en.lng  = Englisch
es.lng  = Spanisch
fr.lng  = Französisch
it.lng  = Italienisch  

TIPS
****
Bei Zugriffsfehlern auf die files.db des minidlna einen HardLink (NICHT Softlink) setzen:

01. im Webverzeichnis: ln /var/lib/minidlna/files.db .
02. Config-File-Anpassen (inc/config.inc.php)
    <snip>
    $path_to_db = {Absoluter Pfad zur Datenbank} (bsp.: "/var/www/localhost/htdocs/minidlna/files.db");
    </snip>

Allgemeine Information (Quellen)
********************************
MINIDLNA:  http://minidlna.sourceforge.net/			[Version: 1.2.1]
SQLiteWeb: https://sourceforge.net/projects/sqlitemanager/	[Version: 1.2.4]
DLC-API:   http://jdownloader.org/knowledge/wiki/linkprotection/container/dlcapi [Version: 1.0.1]
jQuerTree: https://www.abeautifulsite.net/jquery-file-tree [Version 1.0.1]
DynaTree:  http://wwwendt.de/tech/dynatree/doc/dynatree-doc.html [Version 1.2.4]

Installation von MINIDLNA:
(Binary Methode)
01. Laden Sie die Datei minidlna-1.2.1_static.tar.gz herunter
    (In meinem Archiv, Verzeichnis files liegt die Originaldatei auch!)
02. Entpacken Sie die Datei: tar -zxvf minidlna-1.2.1_static.tar.gz -C /
    .. weiter mit 07.
(Source Methode)
01. Laden Sie die Datei minidlna-1.2.1.tar.gz herunter zb. nach /tmp
    (In meinem Archiv, Verzeichnis files liegt die Originaldatei auch!)
02. Entpacken Sie die Datei: tar -zxvf minidlna-1.2.1.tar.gz
03. cd minidlna-1.2.1
04. ./configure
05. make
06. make install
    .. weiter mit Schritt 07.
(Gemeinesam gehts weiter)
07. Bearbeiten Sie die Datei /etc/minidlna.conf nach Ihren Wünschen
    (Ein Beispiel für meineDatei finden Sie in files/minidlna.example)
08. Kopieren Sie folgende Dateien aus meinem Archiv:
    cp files/minidlna.init.script /etc/init.d/minidlna
    cp files/minidlna.init.conf /etc/conf.d/minidlna
09. Unter Gentoo zb.
    rc-update add minidlna default
10. MiniDLNA-Server starten
    /etc/init.d/minidlna start

10. Ohne init-Script, Server starten
    /usr/sbin/minidlnad -c /etc/minidlna.conf

    Um die Datenbank neu oder das erstemal zugenerieren starten Sie wie folgt:
    /usr/sbin/minidlnad -R -c /etc/minidlna.conf

(SQLiteWeb, DLCApi, jQueryTree und DynaTree ist in diesem Archiv bereits vorhanden)

Zum Testen
**********
Wenn Sie den DLC-Text testen wollen
01. Besuchen Sie http://dcrypt.it/
02. Registerkarte [Paste]
03. Kopieren Sie den Text in das Textfeld und [Submit]
04. Nun erhalten Sie die Liste der Link, testen Sie diese im Webbrowser


