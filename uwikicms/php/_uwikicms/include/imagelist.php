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

class UWC_Imagelist {
  var $data=null;
  var $path="";
  var $lang="";
  var $user_status=0;
  var $update=false;
  var $list=null;

  function UWC_Imagelist(&$data,$path,$lang) {
    $this->data=& $data;
    $this->path=$path;
    $this->lang=$lang;
  }

  function update_data() {
    if (!$this->updated) {
      $this->updated=true;
      $this->list=array();
      $this->data->select_image_by_container_path($this->path);
      while ($row=$this->data->query_select_fetch_row()) {
	$id=(int) $row["image_id"];
	$preview_w=(int) $row["image_preview_w"];
	$preview_h=(int) $row["image_preview_h"];
	$full_w=(int) $row["image_full_w"];
	$full_h=(int) $row["image_full_h"];
	$size=(int) $row["image_size"];
	$filename=$row["image_filename"];
	$fullscaled_w=$full_w;
	$fullscaled_h=$full_h;
	uwc_image_calc_fullscaled($fullscaled_w,$fullscaled_h);
	$this->list[$id]=array("preview_w"=>$preview_w,
			       "preview_h"=>$preview_h,
			       "full_w"=>$full_w,
			       "full_h"=>$full_h,
			       "size"=>$size,
			       "filename"=>$filename,
			       "fullscaled_w"=>$fullscaled_w,
			       "fullscaled_h"=>$fullscaled_h,
			       "alt"=>"",
			       "longdesc"=>"");
      }
      $this->data->query_select_free();
      /*
       * Using a separate request to get the legend, using
       * LEFT JOINS or other tweaks would be even more complicated
       * IMHO.
       */
      $this->data->select_legend_by_container_path_and_lang($this->path,$this->lang);
      while ($row=$this->data->query_select_fetch_row()) {
	$id=(int) $row["legend_image_id"];
	$alt=$row["legend_alt"];
	$longdesc=$row["legend_longdesc"];
	$this->list[$id]["alt"]=$alt;
	$this->list[$id]["longdesc"]=$longdesc;
      }
      $this->data->query_select_free();
    }
  }

  function get_list() {
    $this->update_data();
    return $this->list;
  }
}

?>