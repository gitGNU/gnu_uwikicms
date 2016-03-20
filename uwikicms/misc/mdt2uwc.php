<?
/*
 Copyright (C) 2005, 2006, 2007, 2009, 2013, 2015, 2016 Christian Mauduit <ufoot@ufoot.org>

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License as
 published by the Free Software Foundation; either version 2 of
 the License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public
 License along with this program; if not, write to the Free
 Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston,
 MA  02110-1301  USA
*/

/*
 * I wrote this script to import data from my old MadThought journal
 * into uwikicms.
 */

require(dirname(__FILE__)."/../php/_uwikicms/include/config.php");
require(dirname(__FILE__)."/../php/_uwikicms/include/db.php");
require(dirname(__FILE__)."/../php/_uwikicms/include/contextutils.php");

define(MDT2UWC_MDT_CAT,2);
define(MDT2UWC_DBPREFIX2,"/perso/journal");
define(MDT2UWC_AUTHOR,"ufoot");

class MDT2UWC_Record {
  var $mdt_title="";
  var $mdt_type=0;
  var $mdt_timestamp=0;
  var $mdt_text="";

  function MDT2UWC_Record($mdt_title,$mdt_type,$mdt_timestamp,$mdt_text) {
    $this->set_mdt_title($mdt_title);
    $this->set_mdt_type($mdt_type);
    $this->set_mdt_timestamp($mdt_timestamp);
    $this->set_mdt_text($mdt_text);
  }

  function set_mdt_title($mdt_title) {
    $this->mdt_title=stripslashes($mdt_title);
  }

  function set_mdt_type($mdt_type) {
    $this->mdt_type=$mdt_type;
  }

  function set_mdt_timestamp($mdt_timestamp) {
    $this->mdt_timestamp=$mdt_timestamp;
  }

  function set_mdt_text($mdt_text) {
    $this->mdt_text=stripslashes($mdt_text);
  }

  function get_uwc_title() {
    return $this->mdt_title;
  }

  function get_uwc_timestamp() {
    return $this->mdt_timestamp;
  }

  function get_uwc_text() {
    $date=ucfirst(strftime("%A %d %B %Y",$this->mdt_timestamp));
    return $date."\n\n".$this->mdt_text;
  }

  function get_uwc_path() {
    $path=sprintf("/%s/%s",strftime("%Y",$this->mdt_timestamp),$this->mdt_title);

    $path=uwc_contextutils_fix_path($path);

    return $path;
  }

  function get_uwc_status() {
    return $this->mdt_type ? 2 : 0;
  }
}

function mdt2uwc_migrate() {
  setlocale(LC_ALL, "fr_FR");

  $conf=new UWC_Config();
  $db=new UWC_Db($conf);

  $query=sprintf("SELECT Etitle, UNIX_TIMESTAMP(Edate) AS Etimestamp, typeID, Etext FROM j_entries WHERE catID=%d ORDER BY Edate",MDT2UWC_MDT_CAT);
  $db->query_select($query);
  $order=1;
  while ($row=$db->query_select_fetch_row()) {
    $record=new MDT2UWC_Record($row["Etitle"],(int) $row["typeID"],(int) $row["Etimestamp"],$row["Etext"]);
    echo sprintf("%s: %s\n",$record->get_uwc_path(),$record->get_uwc_title());
    $query=sprintf("INSERT INTO uwikicms_content SET content_path='%s%s%s', content_lang='fr', content_title='%s', content_author='%s', content_date_create=NOW(), content_date_update=NOW(), content_text='%s', content_status=%d, content_order=%d",
		   $conf->dbprefix,
		   MDT2UWC_DBPREFIX2,
		   $record->get_uwc_path(),
		   addslashes($record->get_uwc_title()),
		   MDT2UWC_AUTHOR,
		   addslashes($record->get_uwc_text()),
		   $record->get_uwc_status(),
		   $order);
    #echo $query.";\n";
    $db->query($query);
    $order++;
  }

}

/*
 * Commented the following line by default to avoid importng
 * random data if launching the script when testing...
 */
//mdt2uwc_migrate(); 

?>


