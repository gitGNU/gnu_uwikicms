<?php
/*
 UWiKiCMS is a lightweight web content management system.
 Copyright (C) 2005, 2006, 2007, 2009, 2013, 2015 Christian Mauduit <ufoot@ufoot.org>

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

class UWC_Imagebinary {
  var $data=null;
  var $path="";
  var $user_status=0;
  var $image_id=0;
  var $status=3;
  var $status_updated=false;

  function UWC_Imagebinary(&$data,$path,$user_status,$image_id) {
    $this->data=& $data;
    $this->path=$path;
    $this->user_status=$user_status;
    $this->image_id=$image_id;
  }

  function update_status_data() {
    if (!$this->status_updated) {
      $this->status_updated=true;
      $this->data->select_image_status_by_id($this->image_id);
      $row=$this->data->query_select_fetch_row();
      if ($row) {
	$this->status=(int) $row["image_status"];
      }
      $this->data->query_select_free();
    }
  }

  function get_status() {
    $this->update_status_data();
    return $this->status;
  }

  /*
   * Following functions are useless, but they're defined in case
   * there's some rights/auth error, so that PHP page can be
   * generated without any error...
   */
  function get_home() {
    return null;
  }

  function get_prev() {
    return null;
  }

  function get_next() {
    return null;
  }

  function get_up() {
    return null;
  }

  function get_parents() {
    return null;
  }

  function get_children() {
    return null;
  }

  function exists() {
    // Needs to be fixed
    return false;
  }

  function get_date_update() {
    return null;
  }
}

function uwc_imagebinary_view_full(&$data,$id) {
  $data->select_image_full_by_id($id);
  if ($row=$data->query_select_fetch_row()) {
    $string_full=$row["image_full"];
  }
  header('Content-Type: image/jpeg');
  
  //$image_full=imagecreatefromstring($string_full);
  //imagejpeg($image_full);
  echo $string_full;
}

function uwc_imagebinary_view_preview(&$data,$id) {
  $data->select_image_preview_by_id($id);
  if ($row=$data->query_select_fetch_row()) {
    $string_preview=$row["image_preview"];
  }
  header('Content-Type: image/jpeg');
  
  //$image_preview=imagecreatefromstring($string_preview);
  //imagejpeg($image_preview);
  echo $string_preview;
}

?>