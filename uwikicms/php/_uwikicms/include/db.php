<?
/*
 UWiKiCMS is a lightweight web content management system.
 Copyright (C) 2005, 2006, 2007 Christian Mauduit <ufoot@ufoot.org>

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

class UWC_Db {
  var $conf=false;
  var $cnx=false;
  var $select_result=null;

  function UWC_Db(&$conf) {
    $this->conf=& $conf;
  }

  function connect() {
    $this->cnx=mysql_connect($this->conf->dbhost,
			     $this->conf->dbuser,
			     $this->conf->dbpasswd)
      or die("Unable to connect on MySQL server on \"".$this->conf->dbhost."\" : " . mysql_error());
    mysql_select_db($this->conf->dbname, $this->cnx)
      or die ("Unable to select MySQL database \"".$this->conf->dbname."\" : " . mysql_error());
  }

  function get_cnx() {
    if (! $this->cnx) {
      $this->connect();
    }
    return $this->cnx;
  }

  function query($query) {
    $result = mysql_query($query,$this->get_cnx())
      or die ("Unable to execute MySQL request \"".$query."\" : " . mysql_error());    
  }

  function query_select($query) {
    if ($this->select_result) {
      $this->query_select_free();
    }
    $this->select_result = mysql_query($query,$this->get_cnx())
      or die ("Unable to execute MySQL request \"".$query."\" : " . mysql_error());        
    return $this->select_result;
  }

  function query_select_num_rows($select_result=false) {
    if (! $select_result) {
      $select_result = $this->select_result;
    }
    if ($select_result) {
      $num_rows=mysql_num_rows($select_result);
    } else {
      $num_rows=0;
    }

    return $num_rows;
  }

  function query_select_fetch_row($select_result=false) {
    if (! $select_result) {
      $select_result = $this->select_result;
    }
    if ($select_result) {
      $row = mysql_fetch_array($select_result, MYSQL_ASSOC);
    } else {
      $row = false;
    }

    return $row;
  }

  function query_select_free($select_result=false) {
    if (! $select_result) {
      $select_result = $this->select_result;
    }
    if ($select_result == $this->select_result) {
      $this->select_result=false;
    }
    if ($select_result) {
      mysql_free_result($select_result);      
    }
  }
}
?>
