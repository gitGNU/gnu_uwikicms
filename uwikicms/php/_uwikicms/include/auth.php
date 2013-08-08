<?php
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

class UWC_Auth {
  var $conf;
  var $data;
  var $request;
  var $updated=false;
  var $user_id="";
  var $user_passwd="";
  var $user_label="";
  var $user_copyright_holder="";
  var $user_status=0;
  var $user_domain_regex="//";
  var $session="";

  function UWC_Auth(&$conf, &$data, &$request) {
    $this->conf=& $conf;
    $this->data=& $data;
    $this->request=& $request;
    $this->update_auth();
  }

  function update_auth() {
    if (!$this->updated) {
      $this->updated=true;
      $tmp_id=$this->request->get_value("login");
      $passwd=$this->request->get_value("password");;
      if ($tmp_id && $passwd) {
	$this->data->select_user_by_id_and_passwd($tmp_id,$passwd);
	$row=$this->data->query_select_fetch_row();
	if ($row &&
	    $row["user_id"]) {
	  $this->user_id=$tmp_id;
	  $this->user_passwd=$passwd;
	  $this->user_label=$row["user_label"];
	  $this->user_copyright_holder=$row["user_copyright_holder"];
	  $this->user_status=(int) $row["user_status"];
	  $this->user_domain_regex=$row["user_domain_regex"];
	}
      } elseif ($this->request->has_key("session")) {
	$tmp_id=uwc_session_decrypt($this->conf->mcrypt_key,$this->conf->mcrypt_iv,$this->request->get_value("session"),$this->conf->session_lifetime);
	if ($tmp_id) {
	  $this->data->select_user_by_id($tmp_id);
	  $row=$this->data->query_select_fetch_row();
	  if ($row &&
	      $row["user_id"]) {
	    $this->user_id=$tmp_id;
	    $this->user_label=$row["user_label"];
	    $this->user_copyright_holder=$row["user_copyright_holder"];
	    $this->user_status=(int) $row["user_status"];
	    $this->user_domain_regex=$row["user_domain_regex"];
	  }
	}
      }
      $this->session=uwc_session_encrypt($this->conf->mcrypt_key,$this->conf->mcrypt_iv,$this->user_id);
    }
  }

  function is_anonymous() {
    $this->update_auth();
    return (!$this->user_id);
  }

  function get_user_id() {
    $this->update_auth();
    return $this->user_id;
  }

  function get_user_label() {
    $this->update_auth();
    return $this->user_label;
  }

  function get_user_copyright_holder() {
    $this->update_auth();
    return $this->user_copyright_holder;
  }

  function get_user_status() {
    $this->update_auth();
    return $this->user_status;
  }

  function get_user_domain_regex() {
    $this->update_auth();
    return $this->user_domain_regex;
  }

  function can_view($path,$content_status=3) {
    $this->update_auth();
    return (($content_status <= $this->user_status  &&
	     $content_status <= 1) ||
	    $this->can_edit($path,$content_status));
  }

  function can_edit($path,$content_status=3) {
    $this->update_auth();
    /*
     * Note that we add heading and trailing "/" to regex
     * here, for it's needed without the "/" in MySQL.
     */
    return (($this->user_status == 2 &&
	     $this->content_status <=2 &&
	     preg_match("/".$this->user_domain_regex."/",$this->conf->dbprefix.$path)) ||
	    $this->user_status == 3);
  }

  function can_admin() {
    return ($this->user_status>=2);
  }

  function clear_user() {
    $this->updated=true;
    $this->user_id="";
    $this->user_passwd="";
    $this->user_label="";
    $this->user_copyright_holder="";
    $this->user_status=0;
    $this->user_domain_regex="//";
    $this->session="";
  }
}

?>