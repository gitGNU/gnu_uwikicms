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

function uwc_external_build_real_path($prefix,$path,$ext) {
  return sprintf("%s/_external%s.%s",$prefix,$path,$ext);
}

function uwc_external_build_real_path_with_lang($prefix,$path,$lang,$ext) {
  return sprintf("%s/_external%s.%s.%s",$prefix,$path,$lang,$ext);
}

function uwc_external_exists(&$conf, $path, $lang) {
  $ext_found="";

  foreach (array("html","htm","php","php4","php3","txt") as $ext) {    
    if (file_exists(uwc_external_build_real_path_with_lang($conf->prefix,$path,$lang,$ext))) {
      $ext_found=sprintf("%s.%s",$lang,$ext);
      break;
    }
    if (file_exists(uwc_external_build_real_path($conf->prefix,$path,$ext))) {
      $ext_found=$ext;
      break;
    }
  }

  return $ext_found;
}

function uwc_external_include(&$page) {
  $ext=uwc_external_exists($page->conf, 
			   $page->get_path(),
			   $page->get_lang());

  /*
   * Include is stronger than a file read for it will actually
   * process PHP code if there's some. This is what we want.
   */
  include(uwc_external_build_real_path($page->conf->prefix,
				       $page->get_path(),
				       $ext));
}

?>