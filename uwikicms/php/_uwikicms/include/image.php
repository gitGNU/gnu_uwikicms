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

define("UWC_IMAGE_PAGE_URL","%s/img-%06d");
define("UWC_IMAGE_PREVIEW_URL","%s/img-%06d-preview.jpeg");
define("UWC_IMAGE_FULL_URL","%s/img-%06d.jpeg");

define("UWC_IMAGE_TMP_PATH",sprintf("%s/../tmp",dirname(__FILE__)));
define("UWC_IMAGE_PREVIEW_SURFACE",160*120);
define("UWC_IMAGE_FULLSCALED_SURFACE",640*480);
define("UWC_IMAGE_FULLSCALED_LIMIT",801*601);

class UWC_Image {
  var $data=null;
  var $path="";
  var $lang="";
  var $auth=null;
  var $image_id=0;
  var $status=3;
  var $next=null;
  var $prev=null;
  var $container=null;
  var $updated=false;
  var $status_updated=false;
  var $next_updated=false;
  var $prev_updated=false;
  var $image_exists=false;
  var $legend_exists=false;
  var $alt="";
  var $longdesc="";
  var $preview_w=0;
  var $preview_h=0;
  var $full_w=0;
  var $full_h=0;
  var $fullscaled_w=0;
  var $fullscaled_h=0;
  var $size=0;
  var $filename="";

  function UWC_Image(&$data,$path,$lang,&$auth,$image_id) {
    $this->data=& $data;
    $this->path=$path;
    $this->lang=$lang;
    $this->auth=& $auth;
    $this->image_id=$image_id;

    $this->container=new UWC_Content($data,uwc_content_get_parent_path($path),$lang,$this->auth);
  }

  function force_update_data() {
    $this->updated=true;
    $this->data->select_image_by_id($this->image_id);
    $row=$this->data->query_select_fetch_row();
    if ($row) {
      $this->image_exists=true;
      $this->preview_w=(int) $row["image_preview_w"];
      $this->preview_h=(int) $row["image_preview_h"];
      $this->full_w=(int) $row["image_full_w"];
      $this->full_h=(int) $row["image_full_h"];
      $this->size=(int) $row["image_size"];
      $this->filename=$row["image_filename"];
      $this->fullscaled_w=$this->full_w;
      $this->fullscaled_h=$this->full_h;
      uwc_image_calc_fullscaled($this->fullscaled_w,$this->fullscaled_h);
    }
    $this->data->query_select_free();
    $this->data->select_legend_by_id_and_lang($this->image_id,$this->lang);
    $row=$this->data->query_select_fetch_row();
    if ($row) {
      $this->legend_exists=true;
      $this->alt=$row["legend_alt"];
      $this->longdesc=$row["legend_longdesc"];
    }
    $this->data->query_select_free();
  }

  function update_data() {
    if (!$this->updated) {
      $this->force_update_data();
    }
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

  function update_next_data() {
    if (!$this->next_updated) {
      $this->next_updated=true;
      $this->data->select_legend_next_by_container_path_lang_and_id($this->container->get_path(),$this->lang,$this->image_id);
      if ($row=$this->data->query_select_fetch_row()) {
	$this->next=array("path"=>uwc_image_make_page_url($this->container->get_path(),
							  $row["image_id"]),
			  "title"=>$row["legend_alt"]);
      }
    }
  }

  function update_prev_data() {
    if (!$this->prev_updated) {
      $this->prev_updated=true;
      $this->data->select_legend_prev_by_container_path_lang_and_id($this->container->get_path(),$this->lang,$this->image_id);
      if ($row=$this->data->query_select_fetch_row()) {
	$this->prev=array("path"=>uwc_image_make_page_url($this->container->get_path(),
							  $row["image_id"]),
			  "title"=>$row["legend_alt"]);
      }
    }
  }

  function get_status() {
    $this->update_status_data();
    return $this->status;
  }

  function get_home() {
    return $this->container->get_home();
  }

  function get_prev() {
    $this->update_prev_data();
    return $this->prev;
  }

  function get_next() {
    $this->update_next_data();
    return $this->next;
  }

  function get_up() {
    return Array("path"=>$this->container->get_path(),
		 "title"=>$this->container->get_title());
  }

  function get_parents() {
    $container_parents=$this->container->get_parents();

    array_push($container_parents, $this->get_up());

    return $container_parents;
  }

  function get_children() {
    return null;
  }

  function exists() {
    return $this->image_exists();
  }

  function image_exists() {
    $this->update_data();
    return $this->image_exists;
  }

  function legend_exists() {
    $this->update_data();
    return $this->legend_exists;
  }

  function get_date_update() {
    return null;
  }

  function get_title() {
    return $this->get_alt();
  }

  function get_author() {
    return $this->container->get_author();
  }

  function get_copyright_holder() {
    return $this->container->get_copyright_holder();
  }

  function get_email() {
    return $this->container->get_email();
  }

  function get_text() {
    return $this->get_longdesc();
  }

  function get_alt () {
    $this->update_data();
    return $this->alt;
  }

  function get_longdesc () {
    $this->update_data();
    return $this->longdesc;
  }

  function get_preview_w () {
    $this->update_data();
    return $this->preview_w;
  }

  function get_preview_h () {
    $this->update_data();
    return $this->preview_h;
  }

  function get_full_w () {
    $this->update_data();
    return $this->full_w;
  }

  function get_full_h () {
    $this->update_data();
    return $this->full_h;
  }

  function get_fullscaled_w () {
    $this->update_data();
    return $this->fullscaled_w;
  }

  function get_fullscaled_h () {
    $this->update_data();
    return $this->fullscaled_h;
  }

  function get_size () {
    $this->update_data();
    return $this->size;
  }

  function get_filename() {
    $this->update_data();
    return $this->filename;
  }
}

function uwc_image_make_page_url($path,$id) {
  return sprintf(UWC_IMAGE_PAGE_URL,$path,$id);
}

function uwc_image_make_preview_url($path,$id) {
  return sprintf(UWC_IMAGE_PREVIEW_URL,$path,$id);
}

function uwc_image_make_full_url($path,$id) {
  return sprintf(UWC_IMAGE_FULL_URL,$path,$id);
}

function uwc_image_calc_fullscaled(& $width, & $height) {
  if ($width*$height>UWC_IMAGE_FULLSCALED_LIMIT) {
    $coef=sqrt(((float) ($width*$height))/(UWC_IMAGE_FULLSCALED_SURFACE));
    $width=ceil($width/$coef);
    $height=ceil($height/$coef);
  }
}

function uwc_image_get_next_id(&$data) {
  $data->select_image_next_id();
  if ($row=$data->query_select_fetch_row()) {
    $id=(int) $row["image_id"];
  }
  $data->query_select_free();

  if (! ($id>0)) {
    $id=1;
  }

  return $id;
}

/*
 * Inspired from code on php.net:
 * Exemple 1. Exemple de gestion d'erreur lors de la création d'image (gracieusement offert par vic@zymsys.com )
 */
//function loadjpeg($imagefile) {
function uwc_image_loadimage($imagefile) {
  $im = @imagecreatefromjpeg($imagefile); /* Tentative d'ouverture */
  if (!$im) {
    $im = @imagecreatefrompng($imagefile);
  }
  if (!$im) {
    $im = @imagecreatefromgif($imagefile);
  }
  if (!$im) { /* Vérification */
    $im = imagecreate(150, 30); /* Création d'une image blanche */
    $bgc = imagecolorallocate($im, 255, 255, 255);
    $tc  = imagecolorallocate($im, 0, 0, 0);
    imagefilledrectangle($im, 0, 0, 200, 20, $bgc);
    // Affichage d'un message d'erreur
    imagestring($im, 1, 5, 5, "Can't open $imgname", $tc);
  }
  return $im;
}

function uwc_image_tmp_jpeg_filename() {
  return sprintf("%s/_tmpimg%d-%d.jpeg",UWC_IMAGE_TMP_PATH,rand(),microtime());
}

function uwc_image_check_file($imagefile) {
  $im = @imagecreatefromjpeg($imagefile);
  if (!$im) {
    $im = @imagecreatefrompng($imagefile);
  }
  if (!$im) {
    $im = @imagecreatefromgif($imagefile);
  }
  return !!$im;
}

function uwc_image_prepare_sql($imagefile) {
  $image_full=uwc_image_loadimage($imagefile);
  $tmpfile=uwc_image_tmp_jpeg_filename();
  imageinterlace($image_full);
  $full_w=imagesx($image_full);
  $full_h=imagesy($image_full);
  imagejpeg($image_full,$tmpfile);
  $sql_data_full=array('full_w'=>$full_w,
		       'full_h'=>$full_h,
		       'full'=>file_get_contents($tmpfile));
  unlink($tmpfile);
  $coef=sqrt(((float) ($full_w*$full_h))/(UWC_IMAGE_PREVIEW_SURFACE));
  if ($coef<1.) {
    $coef=1.;
  }
  $preview_w=ceil($full_w/$coef);
  $preview_h=ceil($full_h/$coef);
  $image_preview = imagecreatetruecolor($preview_w, $preview_h);
  imagecopyresampled($image_preview, $image_full, 0,0,0,0, $preview_w, $preview_h, $full_w, $full_h); 
  imageinterlace($image_preview);
  imagejpeg($image_preview,$tmpfile);
  $sql_data_preview=array('preview_w'=>$preview_w,
			  'preview_h'=>$preview_h,
			  'preview'=>file_get_contents($tmpfile));
  unlink($tmpfile);
  return array_merge($sql_data_full,$sql_data_preview);
}

?>
