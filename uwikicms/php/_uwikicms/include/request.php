<?php
/*
 UWiKiCMS is a lightweight web content management system.
 Copyright (C) 2005, 2006, 2007, 2009, 2013 Christian Mauduit <ufoot@ufoot.org>

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

class UWC_Request {
  var $request_uri="";
  var $query_string="";
  var $request=array();

  function UWC_Request() {
    $this->request_uri=uwc_request_get_from_redirect("REQUEST_URI");
    /*
     * For some reason request_uri is not what I expect with
     * my provider's environnement. Maybe because of Apache 1.3
     * instead of 2.0 when testing.
     */
    if (uwc_contextutils_is_uwikicms_path($this->request_uri)) {
      $this->request_uri=uwc_request_get_from_redirect("URL");
    }
    // Remove trailing index.php, needed by my provider
    if (preg_match("/(.*)\\/index\\.php/", $this->request_uri, $match)) {
      $this->request_uri=$match[1];
    }
    $this->query_string=uwc_request_get_from_redirect("QUERY_STRING");
    
    $this->update_request();
  }

  function update_request() {
    parse_str($this->query_string,$this->request);

    $this->request=array_merge($_REQUEST, $this->request);
  }

  function has_key($key) {
    return array_key_exists($key,$this->request);
  }

  function get_value($key) {
    if ($this->has_key($key)) {
      return $this->request[$key];
    } else {
      return "";
    }
  }

  function get_request_uri() {
    return $this->request_uri;
  }
}

function uwc_request_get_from_redirect($key) {
  if (array_key_exists("REDIRECT_".$key,$_SERVER)) {
    $value=$_SERVER["REDIRECT_".$key];
  } elseif (array_key_exists($key,$_SERVER)) {
    $value=$_SERVER[$key];
  } else {
    $value="";
  }
  return $value;
}

function uwc_request_get_file_tmp($name) {
  return $_FILES[$name]['tmp_name'];
}

function uwc_request_get_file_remote($name) {
  return $_FILES[$name]['name'];
}

?>