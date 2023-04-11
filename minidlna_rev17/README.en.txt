Installation Instructions
*************************
Prequisits
* Apache2
* PHP5< (Tested with PHP5 - PHP8.1)
* PHP_SQLITE3
* mbstring
* mcrypt
 
On Ubuntu:
PHP81 apt-get install apache2 php8.1 php8.1-sqlite3 php8.1-mbstring sqlite3
	ATTENTION: mcrypt has to be installed manually otherwise JDOWNLOADER files could not generated
                 HowTo for Ubuntu: https://php.tutorials24x7.com/blog/how-to-install-mcrypt-for-php-8-on-ubuntu-20-04-lts
PHP8: apt-get install apache2 php8.0 php8.0-sqlite3 php8.0-mbstring sqlite3 php8.0-mcrypt
PHP<: apt-get install apache2 php php-sqlite3 php-mbstring sqlite3 php-mcrypt

Installation:
* Download minidlna.tar into a directory on your webserver
* Extract: tar -xvf minidlna.tar
* Adjust userrights: 
  (ONLY if you want to use the download-module!!!)
  chmod 777 -R jd  
* Adjust the Config-File 
  <snip>
  $path_to_db = {Absolute path to database} (ex.: "/var/lib/minidlna/files.db");
  $server_http = {Path to the Webinterfaace of your MINIDLNA Server} (ex.: "http://minidlna:8200")
  $debug_sqls = {true = show DB-Queries, false = no DB-Queries-Output} (default: false)
  $fdownload  = {true = make it possible to download files, false = no download} (default: false)
  $jdownload  = {true = generate DLC/CCF/RSDF-files for bulk-download} (default: false)
  </snip>

Externals
*********
phpLiteAdmin: https://www.phpliteadmin.org/  

If Download or JDownload is allowed, something have to be configured
********************************************************************
Here a small example:

Videos/Music/Picture are in one directory with subdirectories
/FS1/{Videos/Musik/Bilder}
then you have to do the following in your Webserver-Root-Directory
a) cd /var/www/localhost/htdocs
b) ln -s /FS1
   .. now the Files are downloadable
c) for protection it might be no bad idea to create a .htaccess file
   an example therefore is in the "files/" Directory
   - cp files/access.htaccess /FS1/.htaccess
   - cp files/access.minidlna /FS1/.minidlna
   - Access only for the IP-Range 192.168.1.x 
   - Authentification with:
     User: meinNameIstHase
     Pass: meinPasswortIstGeheim
  
     TIP: new User/Passwords could be created by using the following command
          htpasswd -b /FS1/.minidlna {Username} {Password}
     
     TIP: the IP-Range for access can be changed in the .htaccess,
          if you dont restrict the access to a IP-Range, just comment out
          the following lines with # or delete them
        .. Apache 2.2 ..
        #Order deny,allow
        #deny from all
        #allow from 192.168.1
        .. Apache 2.4
        #Require ip 192.168.1.0/24
        ..

d) if you want to use the jdownloader-module, you have to edit the file
   "script/dlcapi.class.php"
   
   Line 64:  put the absolute base path of you Server in, in my case
             http://minidlna:8200 .. minidlna is the serverhost, always without final /
   Line 66:  the absolut path to the dlcapicache.txt which maybe fully rightable
             to the webserver (chmod 777 jd/dlcapicache.txt)
             in my case
             /var/www/localhost/htdocs/minidlna/jd/dlcapicache.txt
   .. now the DLC/CCF/RSDF-Files are working in JDownloader (internal only because of restriction)
   
   TIP: If you need also external access to this files, just change the following:
        /var/www/localhost/htdocs/minidlna/script/dlcapi.class.php
        ..
        Line 64:  const dlc_content_generator_url   = 'http://minidlna:8200';
        Line 66:  const dlc_cache_keys_filenmae     = '/var/www/localhost/htdocs/minidlna/jd/dlcapicache.txt';
        ..
        /var/www/localhost/htdocs/FS1/.htacces
        .. Apache 2.2 ..
        #Order deny,allow
        #deny from all
        #allow from 192.168.1
        .. Apache 2.4
        #Require ip 192.168.1.0/24
        ..
        
        .. than you can download the files also from external, but you have to logon .. JDownloader
           will ask you for the credentials. If you dont want a login-prompt, just rename the
           .htaccess to .htaccess_old or delete the file (in this case the .passwd could also be deleted)

Administrative Interface
************************
A new feature i implemented, an administrativ interface, over which you could change the settings
for the minilan-web. But it might be not a bad idea to secure this part, there for the script is
in an own directory called "/admin". If you want to secure this script you only have to generate 
two files in this directory:

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

  since Apache 2.4
  <Limit GET POST>
    Require ip 192.168.1.0/24
  </Limit>
  <--- /snip ---->
  
  There for the same rules or description is interessting which i made beginning with row 32 of this docu.

Styles
******
If you want to install a new style (for the Tree-View)
* Create a directory in the folder "css"
* Create in the new directory a file called: "ui.dynatree.css"
(Default)
default = Default-Style (Old Windows)
vista   = Vista-Style (Vista Windows)
none    = No Pictures (normal listing without symbols)

Languages
*********
All the languagefiles are located in the folder "lng"
* ?.lng = Languagefile
* ?.jpg = Languagepicture (24x12px [BxH])

Language (Syntax of Filename: 2 alphanumeric-digit)
(Default)
de.lng  = german
en.lng  = english
es.lng  = spanish
fr.lng  = french
it.lng  = italian 

TIPS
****
If you get an error while accessing the files.db of the minidlna via Web, set a Hardlink (NOT Softlink):

01. in your webaccessable folder: ln /var/lib/minidlna/files.db .
02. correct the Config-File (inc/config.inc.php)
    <snip>
    $path_to_db = {Absolute path to database} (ex.: "/var/www/localhost/htdocs/minidlna/files.db");
    </snip>

Generall Information (Sources)
********************************
MINIDLNA:  http://minidlna.sourceforge.net/                     [Version: 1.2.1]
SQLiteWeb: https://sourceforge.net/projects/sqlitemanager/      [Version: 1.2.4]
DLC-API:   http://jdownloader.org/knowledge/wiki/linkprotection/container/dlcapi [Version: 1.0.1]
jQuerTree: https://www.abeautifulsite.net/jquery-file-tree [Version 1.0.1]
DynaTree:  http://wwwendt.de/tech/dynatree/doc/dynatree-doc.html [Version 1.2.4]

Installation of MINIDLNA:
(Binary method)
01. Download the file minidlna-1.2.1_static.tar.gz 
    (In my Archiv, Directory files, you will find this original file also)
02. Extract the file: tar -zxvf minidlna-1.2.1_static.tar.gz -C /
    .. move to Step 07.
(Source method)
01. Download the file minidlna-1.2.1.tar.gz to ex. /tmp
02. Extract the the file: tar -zxvf minidlna-1.2.1.tar.gz
03. Move into the new folder: cd minidlna-1.2.1
04. ./configure
05. make
06. make install
    .. move to Step 07.
(Cooperative steps)
07. Edit the file /etc/minidlna.conf up to your wishes 
    (example for mine: files/minidlna.example)
08. Copy the following files out of my archiv:
    cp files/minidlna.init.script /etc/init.d/minidlna
    cp files/minidlna.init.conf /etc/conf.d/minidlna
09. Under Gentoo ex.
    rc-update add minidlna default
10. Start the MiniDLNA-Server 
    /etc/init.d/minidlna start

10. Without init-Script, start the Server
    /usr/sbin/minidlnad -c /etc/minidlna.conf

    To generate the database new or the first time:
    /usr/sbin/minidlnad -R -c /etc/minidlna.conf

(SQLiteWeb, DLCApi, jQueryTree and DynaTree are already in my archive available)

For Testing
***********
If you want to decrypt the dlc-file-string visit
01. http://dcrypt.it/
02. Paste .. paste the string
03. [Submit]
04. Then you will get the Links, try them in Browser if they are working 
