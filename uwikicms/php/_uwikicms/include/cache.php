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

/*
 * Required files for cache to function, other files are included
 * only when a page is actually generated.
 */
require("config.php");
require("request.php");
require("lang.php");
require("contextutils.php");

class UWC_Cache {
  var $mode="";
  var $conf=null;
  var $request=null;
  var $path="";
  var $lang="";
  var $action="";
  var $cache_action="";
  var $lang_forced=false;
  var $file="";
  var $http_code=0;

  function UWC_Cache($mode) {
    $this->mode=$mode;

    $this->conf=new UWC_Config();
    $this->request=new UWC_Request();
    $this->lang=new UWC_Lang();

    $this->update_path();
    $this->update_action();
    $this->update_lang();

    $this->process();
  }

  function update_path() {
    /*
     * Exactly the same function as in the context object but:
     * 1) context object can't be instanciated here for it needs data
     * 2) rewriting this with parameters and no calls to members is ugly too
     * So in this case I judge code copy/pasting the right solution.
     */
    if ($this->request->has_key("path")) {
      $this->path=$this->request->get_value("path");
    } else {
      $a=explode("?",substr($this->request->get_request_uri(),strlen($this->conf->htprefix)));
      $this->path=$a[0];
    }
    $this->path=uwc_contextutils_fix_path($this->path);
  }

  function update_action() {
    $this->cache_action="nocache";
    $action=$this->request->get_value("action");

    if (((!$action) ||
	 ($action=="view") ||
	 ($action=="tree")) && 
	(!$this->request->has_key("session")) &&
	(!$this->request->has_key("login")) &&
	(!$this->request->has_key("css")) &&
	(!preg_match($this->conf->nocache_regex,$this->path))) {
      if (uwc_contextutils_is_image_page_path($this->path)) {
	$this->cache_action="cache";
      } elseif (uwc_contextutils_is_image_preview_path($this->path) ||
		uwc_contextutils_is_image_full_path($this->path)) {
	$this->cache_action="imagecache";
      } elseif (uwc_contextutils_is_gallery_path($this->path)) {
	$this->cache_action="xulcache";
      } elseif (uwc_contextutils_is_uwikicms_path($this->path)) {
	$this->cache_action="nocache";
      } else {
	$this->cache_action="cache";
      }
    }

    switch ($action) {
    case "tree":
      $this->action="tree";
      break;
    default:
      $this->action="view";
      break;
    }
  }

  function update_lang() {
    $this->lang->set_accepted_lang(array());
    if ($this->request->has_key("lang")) {
      $this->lang_forced=true;
      $this->lang->set_lang_id($this->request->get_value("lang"));
    }
  }

  function process() {
    switch ($this->cache_action) {
    case "cache":
    case "imagecache":
    case "xulcache":
      /*
       * It's important go get here without including page.php
       * since it's not really needed and can only improve performances.
       */
      $this->process_cache();
      break;
    default:
      require("page.php");
      new UWC_Page($this->mode);
      break;
    }
  }

  function process_cache() {
    $found=false;

    foreach (array(200,403,404) as $http_code) {
      $this->update_file($http_code);
      if (file_exists($this->file) && time()-filemtime($this->file)<$this->conf->cache_time) {
	$this->http_code=$http_code;
	$found=true;
	break;
      }
    }

    if ($found) {
      uwc_contextutils_send_header_http_code($this->http_code);
      if ($this->http_code==200 && $this->is_image()) {
	header('Content-Type: image/jpeg');
      } elseif ($this->http_code==200 && $this->is_xul()) {
	header('Content-Type: application/vnd.mozilla.xul+xml');
      } else {
	header("Content-Language: ".$this->lang->get_lang_id());
	header("Content-Type: text/html; charset=ISO-8859-1");
      }

      echo file_get_contents($this->file);
    } else {
      require("page.php");
      if (ob_start()) {
	$page=new UWC_Page($this->mode);
	$this->update_file($page->get_http_code());
	$this->write_to_disk();
	ob_end_flush();
      } else {
	new UWC_Page($this->mode);
      }
    }
  }

  function is_image() {
    return $this->cache_action=="imagecache";
  }

  function is_xul() {
    return $this->cache_action=="xulcache";
  }

  function update_file($http_code) {
    if ($this->is_image() || $this->is_xul()) {
      $this->file=sprintf("%s/_uwikicms/cache/%s%s",
			  $this->conf->prefix,
			  $http_code,
			  preg_replace("/\\//",",",$this->path));
    } else {
      $this->file=sprintf("%s/_uwikicms/cache/%s%s-%s-%s.%s.html",
			  $this->conf->prefix,
			  $http_code,
			  preg_replace("/\\//",",",$this->path),
			  $this->action,
			  $this->lang_forced ? "i18n" : "default",
			  $this->lang->get_lang_id());
    }
  }

  function write_to_disk() {
    $content=ob_get_contents();
    $size=strlen($content);

    /*
     * We delete the file, if it existed it is however useless,
     * otherwise we wouldn't generate a new version...
     */
    if (file_exists($this->file)) {
      unlink($this->file);
    }

    if ($size>=$this->conf->cache_minsize &&
	$size<=$this->conf->cache_maxsize) {
      if ($f=fopen($this->file,"w")) {
	/*
	 * Note that passing maxsize should
	 * have the effect of ignoring magic_quotes_runtime
	 * which is basically what we want.
	 */
	if (fwrite($f,$content,$this->conf->cache_maxsize)!=$size) {
	  /*
	   * Problem writing the file, in doubt it's safer to
	   * delete the file rather than letting a wrecked
	   * cache file arround...
	   */
	  unlink($this->file);
	}
	fclose($f);
      }
    }
  }
}

function uwc_cache_purge($conf,$cache_time=0) {
  $cache_dir=sprintf("%s/_uwikicms/cache",$conf->prefix);
  $time=time();
  $last_purge_file=sprintf("%s/last_purge_time.txt",$cache_dir);
  $last_purge_time=filemtime($last_purge_file);
  if ($time-$last_purge_time>$cache_time) {
    if ($handle = opendir($cache_dir)) {
      while ($file = readdir($handle)) {
	if (preg_match("/^\\d{3}/",$file)) {
	  /*
	   * Note the cache_time*2, in fact we delete files only
	   * when there are really really old to avoid deleting
	   * a file when it's just been loaded by another page.
	   * Leaving the file arround for some time does not
	   * harm since it won't be used anyway.
	   */
	  if ($time-filemtime($cache_dir."/".$file)>$cache_time*2) {
	    unlink(sprintf("%s/%s",$cache_dir,$file));
	  }
	}
      }
      closedir($handle);
      if ($f=fopen($last_purge_file,"w")) {
	fwrite($f,sprintf("%d\n",$time));
	fclose($f);
      }
    }
  }
}

function uwc_cache_cron($conf) {
  uwc_cache_purge($conf,$conf->cache_time);
}

?>
