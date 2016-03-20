<?php
/*
 UWiKiCMS is a lightweight web content management system.
 Copyright (C) 2005, 2006, 2007, 2009, 2013, 2015, 2016 Christian Mauduit <ufoot@ufoot.org>

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

define("UWC_CONTEXT_ALLINONE_MAX_SIZE",480);

class UWC_Context {
  var $mode="";
  var $conf=false;
  var $data=false;
  var $lang=false;
  var $auth=false;
  var $request=false;
  var $path="";
  var $type="";
  var $image_id="";
  var $action="";
  var $next_request=array();
  var $continue_url="";
  var $data_langs=array();
  var $input_width1=80;
  var $input_height1=25;
  var $input_width2=80;
  var $input_height2=25;
  var $use_css=true;  

  function UWC_Context($mode,&$conf,&$data,&$lang,&$auth,&$request) {
    $this->mode=& $mode;
    $this->conf=& $conf;
    $this->data=& $data;
    $this->lang=& $lang;
    $this->auth=& $auth;
    $this->request=& $request;

    $this->update_path();
    $this->update_type();
    $this->update_lang();
    $this->update_css();
    $this->update_continue_url();
    $this->update_session();
    $this->update_gui();
  }

  function update_data_langs() {
    $tmp_langs=array();
    if (preg_match("/image_page/",$this->type)) {
      $this->data->select_legend_lang_by_id($this->get_image_id());
      while ($row=$this->data->query_select_fetch_row()) {
	array_push($tmp_langs,$row["legend_lang"]);
      }
      $this->data->query_select_free();
    } elseif (preg_match("/image/",$this->type)) {
      $this->data->select_image_by_id($this->get_image_id());
      if ($row=$this->data->query_select_fetch_row()) {
	// Binary image (jpeg), no need to translate -> we have all languages
	$tmp_langs=array_keys($this->lang->langs);
      } else {
	// No image in database -> no language will raise 404
	$tmp_langs=array();
      }
      $this->data->query_select_free();
    } elseif ("gallery" == $this->type) {
      // Gallery allways available, all languages available
	$tmp_langs=array_keys($this->lang->langs);      
    } else {
      $this->data->select_content_lang_by_path($this->path);
      while ($row=$this->data->query_select_fetch_row()) {
	array_push($tmp_langs,$row["content_lang"]);
      }
      $this->data->query_select_free();
    }
    /*
     * Now we sort the langs to match our "prefered"
     * order
     */
    $this->data_langs=array();
    foreach (array_keys($this->lang->langs) as $lang_id) {
      if (count(array_intersect(array($lang_id),$tmp_langs))) {
	array_push($this->data_langs,$lang_id);
      }
    }
 }
  
  function update_lang() {
    $this->update_data_langs();

    if ($this->request->has_key("lang")) {
      $this->lang->set_lang_id($this->request->get_value("lang"));
      $this->next_request["lang"]=$this->request->get_value("lang");
    } else {
      $this->lang->set_accepted_lang($this->data_langs);
    }

    /*
     * If we there's no page in the requested lang, 
     * and we have no chance to ever edit a page,
     * and there's a page in another language
     * then we use another lang.
     */
    if ((!count(array_intersect(array($this->lang->get_lang_id()), 
				$this->data_langs))) &&
	($this->auth->get_user_status()<=1) &&
	(count($this->data_langs))) {
      $this->lang->set_lang_id($this->data_langs[0]);	
    }
  }

  function update_path() {
    if ($this->request->has_key("path")) {
      $this->path=$this->request->get_value("path");
    } else {
      $a=explode("?",substr($this->request->get_request_uri(),strlen($this->conf->htprefix)));
      $this->path=$a[0];
    }
    $this->path=uwc_contextutils_fix_path($this->path);
  }

  function update_type() {
    $this->type="text";

    if (preg_match(UWC_CONTEXTUTILS_UWIKICMS_PATH,$this->path)) {
      $this->type="uwikicms";
    } elseif (preg_match(UWC_CONTEXTUTILS_IMAGE_PAGE_PATH,$this->path,$match)) {
      $this->type="image_page";
      $this->image_id=(int) $match[1];
    } elseif (preg_match(UWC_CONTEXTUTILS_IMAGE_PREVIEW_PATH,$this->path,$match)) {
      $this->type="image_preview";
      $this->image_id=(int) $match[1];
    } elseif (preg_match(UWC_CONTEXTUTILS_IMAGE_FULL_PATH,$this->path,$match)) {
      $this->type="image_full";
      $this->image_id=(int) $match[1];
    } elseif (preg_match(UWC_CONTEXTUTILS_GALLERY_PATH,$this->path,$match)) {
      $this->type="gallery";
    }
  }

  function update_css() {
    if ($this->request->has_key("css") && $this->request->get_value("css")=="no") {
      $this->next_request["css"]="no";
      $this->use_css=false;
    } else {
      $this->use_css=true;
    }
  }

  function update_continue_url () {
    if ($this->request->has_key("continue")) {
      $this->continue_url=$this->request->get_value("continue");
    } else {
      $this->continue_url=substr($this->request->get_request_uri(),strlen($this->conf->htprefix));
    }
    if (uwc_contextutils_is_uwikicms_path($this->continue_url)) {
      $this->continue_url="";
    }
  }

  function update_session() {
    if ($this->auth->session) {
      $this->next_request["session"]=$this->auth->session;
    }
  }

  function delete_session() {
    unset($this->next_request["session"]);
  }

  function update_action(&$content) {
    switch ($this->request->get_value("action")) {
    case "loginform":
    case "logout":
    case "about":
      $this->action=$this->request->get_value("action");
      break;
    default:
    switch ($this->mode) {
    case "create":
    case "update": 
    case "delete":
    case "imagecreate":
    case "imageupdate": 
    case "imagedelete":
      if ($this->auth->can_edit($this->get_path(),$content->get_status())) {
	$this->action=$this->mode;
      } else {	
	$this->action="loginform";
      }
      break;
    case "clearcache":
    case "renum":
    case "allinone":
      if ($this->auth->can_admin()) {
	$this->action=$this->mode;
      } else {	
	$this->action="loginform";
      }
      break;
    case "new":
      $this->path=uwc_contextutils_fix_path($this->get_path()."/".$this->request->get_value("page"));
      $this->action=$this->mode;
      break;
    case "about":
      $this->action=$this->mode;
      break;
    default:
      if (count(array_intersect(array($this->lang->get_lang_id()), 
				$this->data_langs))) {
	if ($this->auth->can_view($this->get_path(),$content->get_status())) {
	  switch ($this->type) {	    
	  case "image_page":
	    $this->action="imageview";
	    break;
	  case "image_preview":
	    $this->action="imageviewpreview";
	    break;
	  case "image_full":
	    $this->action="imageviewfull";
	    break;
	  case "gallery":
	    $this->action="gallery";
	    break;
	  default:
	    $this->action="view";
	  }
	} else {
	  $this->action="403";
	}
      } else {
	if ($this->auth->can_edit($this->get_path(),$content->get_status())) {
	  switch ($this->type) {
	  case "image_page":
	    if (!count($this->data_langs)) {
	      $this->action="imagecreateform";
	    } else {
	      $this->action="imageupdateform";
	    }
	    break;
	  default:
	    $this->action="createform";
	  }
	} else {
	  $this->action="404";
	}
      }
    }

    if ($this->auth->can_view($this->get_path(),$content->get_status()) &&
	$this->request->has_key("action")) {
      switch ($this->request->get_value("action")) {
      case "tree":
      case "rss":
	$this->action=$this->request->get_value("action");
	break;
      }
    }

    if ($this->auth->can_edit($this->get_path(),$content->get_status()) &&
	$this->request->has_key("action")) {
      switch ($this->request->get_value("action")) {
      case "updateform":
      case "deleteform":
	if ($this->type=="image_page") {
	  $this->action="image".$this->request->get_value("action");
	} else {
	  $this->action=$this->request->get_value("action");
	}
	break;
      }
    }      

    if ($this->auth->can_admin() && $this->request->has_key("action")) {
      switch ($this->request->get_value("action")) {
      case "clearcacheform":
      case "renumform":
      case "allinoneform":
	  $this->action=$this->request->get_value("action");
	  break;
      }	
    }
    
    if ($this->action=="view" &&
	uwc_external_exists($this->conf, $this->get_path(), $this->get_lang())) {
      $this->action="external";
    }
    }
  }

  function update_gui() {
    if (preg_match("/(links|lynx)/i",$_SERVER["HTTP_USER_AGENT"])) {
      /*
       * No use to have great height/width when in console mode,
       * at least that's my opinion and habit.
       */
      $this->input_width1=40;
      $this->input_height1=4;
      $this->input_width2=50;
      $this->input_height2=10;
    } elseif (preg_match("/(msie)/i",$_SERVER["HTTP_USER_AGENT"])) {
      /*
       * Not that I think than IE can't handle high res, but my father
       * uses UWiKiCMS on a laptop with a small screen and AFAIK IE
       * doesn't have the fancy CRTL +/- feature that changes fonts
       * on the fly so reducing width/height a bit makes things
       * easier for him.
       */
      $this->input_width1=50;
      $this->input_height1=5;
      $this->input_width2=60;
      $this->input_height2=20;
    } else {
      /*
       * Everyone else uses default settings, which are not
       * *that* "big", any decent interface should cope with that.
       */
      $this->input_width1=60;
      $this->input_height1=6;
      $this->input_width2=80;
      $this->input_height2=25;
    }
  }

  function get_lang() {
    return $this->lang->get_lang_id();
  }  

  function get_data_langs() {
    return $this->data_langs;
  }

  function get_path() {
    return $this->path;
  }  

  function get_type() {
    return $this->type;
  }

  function get_image_id() {
    return $this->image_id;
  }

  function get_action() {
    return $this->action;
  }  

  function is_home() {
    return $this->path=="" || $this->path=="/";
  }

  function get_continue_url() {
    return $this->continue_url;
  }

  function get_input_width1() {
    return $this->input_width1;
  }

  function get_input_height1() {
    return $this->input_height1;
  }

  function get_input_width2() {
    return $this->input_width2;
  }

  function get_input_height2() {
    return $this->input_height2;
  }

  function get_allinone_max_size() {
    if ($this->request->has_key("max_size")) {
      $max_size=(int) $this->request->get_value("max_size");
    } else {
      $max_size=UWC_CONTEXT_ALLINONE_MAX_SIZE;
    }

    if (!$max_size) {
      $max_size=UWC_CONTEXT_ALLINONE_MAX_SIZE;      
    }

    return $max_size;
  }
}

?>
