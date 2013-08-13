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

class UWC_Gallery     {
  var $data=null;
  var $path="";
  var $lang="";
  var $auth=null;
  var $container=null;

  function UWC_Gallery(&$data,$path,$lang,&$auth) {
    $this->data=& $data;
    $this->path=$path;
    $this->lang=$lang;
    $this->auth=& $auth;
    $this->container=new UWC_Content($data,uwc_content_get_parent_path($path),$lang,$auth);
  }

  function get_user_status() {
    return $this->auth->get_user_status();
  }

  function get_status() {
    return $this->container->get_status();
  }

  function get_images_list() {
    return $this->container->get_images_list();
  }

  function get_up() {
    return array("path"=>$this->container->get_path(),"title"=>$this->container->get_title());
  }

  function get_home() {
    return $this->container->get_home();
  }

  function get_parents() {
    $container_parents=$this->container->get_parents();

    array_push($container_parents, $this->get_up());

    return $container_parents;
  }

  /*
   * Following functions are useless, but they're defined in case
   * there's some rights/auth error, so that PHP page can be
   * generated without any error...
   */
  function get_prev() {
    return null;
  }

  function get_next() {
    return null;
  }

  function get_children() {
    return null;
  }

  function exists() {
    return true;
  }

  function get_date_update() {
    return null;
  }
}

function uwc_gallery_xul_header() {
  header('Content-Type: application/vnd.mozilla.xul+xml');
}

?>