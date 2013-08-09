<?php
/*
 UWiKiCMS is a lightweight web content management system.
 Copyright (C) 2005, 2006, 2007, 2013 Christian Mauduit <ufoot@ufoot.org>

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

define("UWC_CONTEXTUTILS_UWIKICMS_PATH","/\\/_uwikicms/");
define("UWC_CONTEXTUTILS_IMAGE_PAGE_PATH","/img\\-(\\d+)$/");
define("UWC_CONTEXTUTILS_IMAGE_PREVIEW_PATH","/img\\-(\\d+)\\-preview\\.jpeg$/");
define("UWC_CONTEXTUTILS_IMAGE_FULL_PATH","/img\\-(\\d+)\\.jpeg$/");
define("UWC_CONTEXTUTILS_GALLERY_PATH","/gallery\\.xul$/");

function uwc_contextutils_is_uwikicms_path($path) {
  return preg_match(UWC_CONTEXTUTILS_UWIKICMS_PATH,$path);
}

function uwc_contextutils_is_image_page_path($path) {
  return preg_match(UWC_CONTEXTUTILS_IMAGE_PAGE_PATH,$path);
}

function uwc_contextutils_is_image_preview_path($path) {
  return preg_match(UWC_CONTEXTUTILS_IMAGE_PREVIEW_PATH,$path);
}

function uwc_contextutils_is_image_full_path($path) {
  return preg_match(UWC_CONTEXTUTILS_IMAGE_FULL_PATH,$path);
}

function uwc_contextutils_is_gallery_path($path) {
  return preg_match(UWC_CONTEXTUTILS_GALLERY_PATH,$path);
}

function uwc_contextutils_fix_path($path) {
  // Make sure we've a heading /
  $path="/".$path;
  // Get rid of common extensions and index.* pages
  $path=preg_replace("/(\\/index|)(\\.html|\\.htm|\\.php|\\.php3|\\.php4|\\.jsp|\\.asp)$/","",$path);
  // Get rid of useless buggy chars
  $path=preg_replace("/[^a-z|A-Z|\\d|\\-|\\_|\\.|\\/]/","",$path);
  // Case insensitive path
  $path=strtolower($path);
  // Transform // into /
  $path=preg_replace("/\/+/","/",$path);
  // Get rid of trailing /
  $path=rtrim($path,"/");
  // Maxlength=255
  $path=substr($path,0,255);

  return $path;
}

function uwc_contextutils_send_header_http_code($http_code) {
  switch ((int) $http_code) {
  case 403:
      header("HTTP/1.0 403 Forbidden");
      break;
  case 404:
      header("HTTP/1.0 404 Not Found");
      break;
  default:
      header("HTTP/1.0 200 OK");
  }
}
?>
