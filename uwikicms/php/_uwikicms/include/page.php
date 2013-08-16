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

/*
 * All these files are included only if it's actually required,
 * ie if we need to generate the page instead of simply getting
 * it from the cache.
 */
require("context.php");
require("format.php");
require("db.php");
require("sql.php");
require("data.php");
require("session.php");
require("auth.php");
require("tree.php");
require("content.php");
require("image.php");
require("imagebinary.php");
require("imagelist.php");
require("external.php");
require("phpwiki.php");
require("horde.php");
require("gallery.php");
require("rss.php");

define("UWC_PAGE_DESCRIPTION_SIZE",80);

class UWC_Page {
  var $context=false;
  var $conf=false;
  var $request=false;
  var $data=false;
  var $lang=false;
  var $auth=false;
  var $content=false;
  var $title="";
  var $img_odd=false;
  var $http_code=0;
  var $allinone_path="";

  function UWC_Page($mode) {
    /*
     * For later use in phpwiki internal code, when generating
     * HTML (links for images and internal pages).
     */
    $GLOBALS["uwc_page_phpwiki_hook"]=& $this;

    $this->conf=new UWC_Config();
    $this->request=new UWC_Request();
    $this->lang=new UWC_Lang();
    $this->data=new UWC_Data($this->conf);
    $this->auth=new UWC_Auth($this->conf,$this->data,$this->request);
    $this->context=new UWC_Context($mode,$this->conf,$this->data,$this->lang,$this->auth,$this->request);
    $this->lang->include_messages();

    if (preg_match("/image_page/",$this->context->get_type())) {
      $this->content=new UWC_Image($this->data,$this->context->get_path(),$this->context->get_lang(),$this->auth,$this->context->get_image_id());
    } elseif (preg_match("/image/",$this->context->get_type())) {
      $this->content=new UWC_Imagebinary($this->data,$this->context->get_path(),$this->get_user_status(),$this->context->get_image_id());
    } elseif ("gallery"==$this->context->get_type()) {
      $this->content=new UWC_Gallery($this->data,$this->context->get_path(),$this->context->get_lang(),$this->auth);
    } else {
      $this->content=new UWC_Content($this->data,$this->context->get_path(),$this->context->get_lang(),$this->auth);
    }

    /*
     * update_action couldn't be put in the context constructor
     * since it *needs* content to know wether it has rights on
     * the document. And one can't ask for context before content
     * either.
     */
    $this->context->update_action($this->content);

    /*
     * 403 and 404 for binary images need to fall back
     * on the corresponding HTML page.
     */
    if (($this->context->action=="404" || 
	 $this->context->action=="403") && 
	preg_match("/image_(preview|full)/",$this->context->get_type())) {
      $this->content=new UWC_Image($this->data,$this->context->get_path(),$this->context->get_lang(),$this->auth,$this->context->get_image_id());
      $this->context->update_action($this->content);      
    }

    $this->process();
  }

  function set_allinone_path($path) {
    /*
     * Ugly hack, to allow relative links to function during
     * all in one page generation, we must make the page object
     * to temporary believe its path is one if its children.
     * This way the hook functions (callbacks called by phpwiki
     * transform code) work properly.
     */
    $this->allinone_path=$path;
  }

  function get_path() {
    if ($this->allinone_path) {
      $path=$this->allinone_path;
    } else {
      $path=$this->context->get_path();
    }

    return $path;
  }

  function get_lang() {
    return $this->context->get_lang();
  }

  function get_title() {
    if (!$this->title) {
      switch ($this->context->action) {
      case "403":
      case "404":
      case "loginform":
      case "logout":
      case "createform":
      case "imagecreateform":
      case "clearcache":
      case "clearcacheform":
      case "renum":
      case "renumform":
      case "allinoneform":
      case "about":
	$title=$this->translate("title_".$this->context->action);
	break;
      case "updateform":
      case "deleteform":
      case "view":
      case "rss":
      case "external":
      case "imageupdateform":
      case "imagedeleteform":
      case "imageview":
      case "allinone":
	$title=uwc_format_text_to_html($this->content->get_title());
	break;
      case "tree":
	if ($this->context->is_home()) {
	  $title=$this->translate1("this_is_a_tree",uwc_format_text_to_html($this->content->get_title()));
	} else {
	  $title=$this->translate1("this_is_a_subtree",uwc_format_text_to_html($this->content->get_title()));
	}
	break;
      case "create":
      case "update":
      case "delete":
	$title=uwc_format_text_to_html(uwc_format_unescape_gpc($this->request->get_value("title")));
	break;
      case "imagecreate":
      case "imageupdate":
      case "imagedelete":
	$title=uwc_format_text_to_html(uwc_format_unescape_gpc($this->request->get_value("alt")));
	break;
      default:
	$title=$this->translate("no_title");
      }
    }

    if (!$title) {
      $title=$this->translate("no_title");
    }

    return $title;
  }

  function get_title_sql() {
    return $title=uwc_format_text_to_html($this->content->get_title());
  }

  function get_author() {
    return $this->content->get_author();
  }

  function get_copyright_holder() {
    $copyright_holder=$this->content->get_copyright_holder();

    if (!$copyright_holder) {
      $copyright_holder=$this->conf->copyright_holder;
    }

    return $copyright_holder;
  }

  function get_copyright_holder_with_mailto() {    
    $email=$this->content->get_email();
    if ($email) {
      $copyright_holder_with_mailto=sprintf("<a href=\"mailto:%s\">%s</a>",
					    $email,
					    $this->get_copyright_holder());
    } else {
      $copyright_holder_with_mailto=$this->get_copyright_holder();
    }

    return $copyright_holder_with_mailto;
  }

  function get_date_create() {
    return $this->content->get_date_create();
  }

  function get_date_update() {
    $date_update=$this->content->get_date_update();

    if (!$date_update) {
      $date_update=time();
    }

    return $date_update;
  }

  function get_text() {
    // phpwiki_to_html does all the fancy formatting
    return uwc_format_phpwiki_to_html($this->content->get_text());
  }

  function get_text_unformatted() {
    return uwc_format_text_to_html($this->content->get_text());
  }

  function get_input_width1() {
    return $this->context->get_input_width1();
  }

  function get_input_height1() {
    return $this->context->get_input_height1();
  }

  function get_input_width2() {
    return $this->context->get_input_width2();
  }

  function get_input_height2() {
    return $this->context->get_input_height2();
  }

  function get_arrow_width() {
    return $this->conf->arrow_width;
  }

  function get_arrow_height() {
    return $this->conf->arrow_height;
  }

  function get_lang_width() {
    return $this->conf->lang_width;
  }

  function get_lang_height() {
    return $this->conf->lang_height;
  }
 
  function get_rss_width() {
    return $this->conf->rss_width;
  }

  function get_rss_height() {
    return $this->conf->rss_height;
  }

  function get_news_max_nb() {
    return $this->conf->news_max_nb;
  }

  function get_news_max_age() {
    return $this->conf->news_max_age;
  }

  function get_rss_max_nb() {
    return $this->conf->rss_max_nb;
  }

  function get_rss_max_age() {
    return $this->conf->rss_max_age;
  }

  function get_rss_max_len() {
    return $this->conf->rss_max_len;
  }

  function get_status() {
    return $this->content->get_status();
  }

  function get_user_status() {
    return $this->auth->get_user_status();
  }

  function get_status_style() {
    switch ($this->content->get_status()) {
    case 0:
      $status_style="status0";
      break;
    case 1:
      $status_style="status1";
      break;
    case 2:
      $status_style="status2";
      break;
    case 3:
      $status_style="status3";
      break;
    default:
      $status_style="default";
      break;
    }

    return $status_style;
  }

  function get_rights() {
    switch ($this->content->get_status()) {
    case 0:
      $rights="rights_0";
      break;
    case 1:
      $rights="rights_1";
      break;
    case 2:
      $rights="rights_2";
      break;
    case 3:
      $rights="rights_3";
      break;
    default:
      $rights="rights_0";
      break;
    }

    switch ($this->context->action) {
    case "allinone":
      $rights_html=$this->translate("display_status_".$this->get_allinone_status());
      break;
    case "tree":
      $rights_html=$this->translate("display_status_".$this->get_user_status());
      break;
    default:
      $rights_html=$this->translate2($rights,strftime("%Y",$this->get_date_update()),$this->get_copyright_holder_with_mailto());
    }

    return $rights_html;
  }

  function need_rights() {
    return $this->content && ($this->context->get_action() == "view" || $this->context->get_action() == "external" || $this->context->get_action() == "imageview" || $this->context->get_action() == "tree" || $this->context->get_action() == "allinone");
  }

  function get_last_update() {
    return $this->translate1("last_update",$this->lang->format_date($this->get_date_update()));
  }

  function need_last_update() {
    return $this->get_date_update() != 0 && ($this->context->get_action() == "view" || $this->context->get_action() == "external" || $this->context->get_action() == "imageview");
  }

  function get_order() {
    return $this->content->get_order();
  }

  function get_children() {
    return $this->content->get_children();
  }

  function get_news() {
    return $this->content->get_news($this->get_news_max_nb(), $this->get_news_max_age());
  }

  function get_rss() {
    return $this->content->get_rss($this->get_rss_max_nb(), $this->get_rss_max_age(), $this->get_rss_max_len());
  }

  function get_allinone_status() {
    if ($this->request->has_key("status")) {
      $status=(int) $this->request->get_value("status");
    } else {
      $status=$this->get_user_status();
    }

    return $status;
  }

  function get_allinone_default_intro() {
    if ($this->context->is_home()) {
      $intro=$this->translate2("all_in_one_site",$this->content->get_title(),$this->today());
    } else {
      $intro=$this->translate2("all_in_one_subsite",$this->content->get_title(),$this->today());
    }

    return $intro;
  }

  function get_allinone_intro() {
    return uwc_format_text_to_html(uwc_format_unescape_gpc($this->request->get_value("intro")));
  }

  function get_tree() {
    return $this->content->get_tree($this->get_allinone_status());
  }

  function get_parents() {
    return $this->content->get_parents();
  }

  function get_home_path() {
    $home=$this->content->get_home();

    return $home["path"];
  }

  function get_home_title() {
    $home=$this->content->get_home();

    if ($home["title"]) {
      $home=uwc_format_text_to_html($home["title"]);
    } else {
      /*
       * When being on home page, parent data is unset,
       * so we just get our current value. Fixes #20032.
       */
      $home=$this->get_title();
    }

    if (! $home) {
      $home=$this->translate("no_title");
    }

    return $home;
  }

  function get_up_path() {
    $up=$this->content->get_up();

    return $up["path"];
  }

  function get_up_title() {
    $up=$this->content->get_up();

    if ($up["title"]) {
      $up=uwc_format_text_to_html($up["title"]);
    } else {
      $up=$this->translate("no_title");
    }

    return $up;
  }

  function get_next_path() {
    $next=$this->content->get_next();

    return $next["path"];
  }

  function get_next_title() {
    $next=$this->content->get_next();

    if ($next["title"]) {
      $next=uwc_format_text_to_html($next["title"]);
    } else {
      $next=$this->translate("no_title");
    }

    return $next;
  }

  function get_prev_path() {
    $prev=$this->content->get_prev();

    return $prev["path"];
  }

  function get_prev_title() {
    $prev=$this->content->get_prev();

    if ($prev["title"]) {
      $prev=uwc_format_text_to_html($prev["title"]);
    } else {
      $prev=$this->translate("no_title");
    }

    return $prev;
  }

  function get_prefix() {
    return $this->conf->prefix;
  }

  function get_htprefix() {
    return $this->conf->htprefix;
  }

  function get_domain_name() {
    /*
     * Obviously this domain name computing could be put
     * in the Config object itself. However we only need
     * domain name for Google searches, so it's useless
     * to bother moving it. Besides, putting this code here
     * will cause it not to be executed when page is cached.
     */
    preg_match("/^((http|https):\/\/)?([^\/\:]+)/i",$this->conf->siteurl, $matches);
    $domain_name = $matches[3];
    return $domain_name;
  }

  function get_absolute_url() {
    return $this->conf->siteurl.$this->make_url($this->get_path(),array("login"=>"","password"=>""));
  }

  function get_absolute_url_no_esc() {
    return $this->conf->siteurl.$this->make_url_no_esc($this->get_path(),array("login"=>"","password"=>""));
  }

  function get_absolute_url_clean() {
    return $this->conf->siteurl.$this->make_url($this->get_path(),array("login"=>"","password"=>"","session"=>"","action"=>""));
  }

  function make_absolute_url_clean($path) {
    return $this->conf->siteurl.$this->make_url($path,array("login"=>"","password"=>"","session"=>"","action"=>""));
  }

  function need_absolute_url() {
    return true;
  }

  function get_view_url() {
    return $this->make_url($this->get_path());
  }

  function get_tree_url() {
    return $this->make_url($this->get_path(),array("action"=>"tree"));
  }

  function get_rss_url() {
    return $this->make_url($this->get_path(),array("login"=>"","password"=>"","session"=>"","action"=>"rss"));
  }

  function get_css_yes_url() {
    return $this->make_url($this->get_path(),array("css"=>"yes"));
  }

  function get_css_no_url() {
    return $this->make_url($this->get_path(),array("css"=>"no"));
  }

  function get_new_url() {
    return $this->make_url("/_uwikicms/new.php",array("lang"=>$this->get_lang()));
  }

  function get_create_url() {
    return $this->make_url("/_uwikicms/create.php");
  }

  function get_updateform_url() {
    return $this->make_url($this->get_path(),array("action"=>"updateform"));
  }

  function get_deleteform_url() {
    return $this->make_url($this->get_path(),array("action"=>"deleteform"));
  }

  function get_update_url() {
    return $this->make_url("/_uwikicms/update.php");
  }

  function get_delete_url() {
    return $this->make_url("/_uwikicms/delete.php");
  }

  function get_loginform_url() {
    return $this->make_url($this->get_path(),array("action"=>"loginform"));
  }

  function get_login_url() {
    return $this->make_url("/_uwikicms/login.php",array("continue"=>$this->get_continue_url()));
  }

  function get_logout_url() {
    return $this->make_url($this->get_path(),array("action"=>"logout","continue"=>$this->get_path(),"session"=>""));
  }

  function get_continue_url() {
    return $this->context->get_continue_url();
  }

  function get_translate_url($lang) {
    return $this->make_url($this->get_path(),array("lang"=>$lang));
  }

  function get_clearcacheform_url() {
    return $this->make_url($this->get_path(),array("action"=>"clearcacheform"));
  }

  function get_clearcache_url() {
    return $this->make_url("/_uwikicms/clearcache.php",array("continue"=>$this->get_path()));
  }

  function get_renumform_url() {
    return $this->make_url($this->get_path(),array("action"=>"renumform"));
  }

  function get_renum_url() {
    return $this->make_url("/_uwikicms/renum.php",array("continue"=>$this->get_path()));
  }

  function get_help_url() {
    return "http://www.ufoot.org/uwikicms/doc/manual";
  }

  function get_allinoneform_url() {
    return $this->make_url($this->get_path(),array("action"=>"allinoneform"));
  }

  function get_allinone_url() {
    return $this->make_url("/_uwikicms/allinone.php");
  }

  function get_imagenew_url() {
    return $this->make_url(uwc_image_make_page_url($this->get_path(),uwc_image_get_next_id($this->data)),array("lang"=>$this->get_lang()));
  }

  function get_imagecreate_url() {
    return $this->make_url("/_uwikicms/imagecreate.php");
  }

  function get_imageupdate_url() {
    return $this->make_url("/_uwikicms/imageupdate.php");
  }

  function get_imagedelete_url() {
    return $this->make_url("/_uwikicms/imagedelete.php");
  }

  function get_image_preview_url() {
    return $this->make_url(uwc_image_make_preview_url(uwc_content_get_parent_path($this->get_path()),$this->context->get_image_id()));
  }

  function get_image_full_url() {
    return $this->make_url(uwc_image_make_full_url(uwc_content_get_parent_path($this->get_path()),$this->context->get_image_id()));
  }

  function get_image_preview_w() {
    return $this->content->get_preview_w();
  }

  function get_image_preview_h() {
    return $this->content->get_preview_h();
  }

  function get_image_full_w() {
    return $this->content->get_full_w();
  }

  function get_image_full_h() {
    return $this->content->get_full_h();
  }

  function get_image_fullscaled_w() {
    return $this->content->get_fullscaled_w();
  }

  function get_image_fullscaled_h() {
    return $this->content->get_fullscaled_h();
  }

  function get_gallery_url() {
    $path=(preg_match("/image/",$this->context->action)) ? $this->get_up_path() : $this->get_path();
    return $this->make_url($path."/gallery.xul");
  }

  function get_image_size() {
    return uwc_format_readable_size($this->content->get_size());
  }

  function get_image_filename() {
    return uwc_format_text_to_html($this->content->get_filename());
  }

  function get_legend_alt() {
    return uwc_format_text_to_html_attribute($this->content->get_alt());
  }

  function get_legend_longdesc() {
    return uwc_format_text_to_html($this->content->get_longdesc());
  }

  function get_images_list() {
    return $this->content->get_images_list();
  }

  function get_css_dir() {
    // we don't use make_url here since we don't want any parameter
    return $this->conf->htprefix.$this->conf->css_dir;
  }

  function get_images_dir() {
    // we don't use make_url here since we don't want any parameter
    return $this->conf->htprefix.$this->conf->images_dir;
  }

  function get_js_dir() {
    // we don't use make_url here since we don't want any parameter
    return $this->conf->htprefix."/_uwikicms/template/js";
  }

  function make_url_no_esc($path,$params=false) {
    if (!$path) {
      /*
       * Very important, on some versions/config of Apache,
       * using http://mysite.com or http://mysite.com/uwikicms
       * without a trailing / causes an additionnal http redirect
       * or something alike, which causes GET parameters to
       * be lost, therefore it's important to add the "/"
       * for the root page. Other pages are not affected.
       */
      $path="/";
    }
    $url=$this->conf->htprefix.$path;
    $delim=(preg_match("/\?/",$path)) ? "&": "?";

    if (!$params) {
      $params=array();
    }

    foreach (array_merge($this->context->next_request,$params) as $k=>$v) {
      if ($v || $k=="continue") {
	$url.=$delim.urlencode($k)."=".urlencode($v);
	$delim="&";
      }
    }

    return $url;
  }

  function make_url($path,$params=false) {
    return htmlentities($this->make_url_no_esc($path,$params),ENT_COMPAT|ENT_XHTML,"ISO-8859-15");
  }

  function make_img_link($image_id,$linkname) { 
    $path=$this->get_path();

    $image=new UWC_Image($this->data,$path,$this->context->get_lang(),$this->auth,$image_id);
    $url_page=$this->make_url(uwc_image_make_page_url($path,$image_id));

    if ($image->get_status()>0) {
      $make_url_array=array();
    } else {
      /*
       * If image status is 0, there's no point in trying to get
       * the image with a "&session=XYZ" parameter, ie try to
       * load it unidentified. Doing so would inhibit cache features
       * and impair performance, therefore we load them as an
       * anonymous user.
       */
      $make_url_array=array("session"=>"");
    }

    if ($this->context->action=="allinone") {      
      $url_full=$this->make_url(uwc_image_make_full_url($path,$image_id),$make_url_array);
      $full_w=$image->get_full_w();
      $full_h=$image->get_full_h();
      $coef_w=((float) $full_w)/$this->context->get_allinone_max_size();
      $coef_h=((float) $full_h)/$this->context->get_allinone_max_size();
      $coef=max($coef_w,$coef_h);
      if ($coef>1) {
	$full_w=ceil($full_w/$coef);
	$full_h=ceil($full_h/$coef);
      }
      $html=uwc_format_html_for_img_link_full($url_page,$url_full,$full_w,$full_h,$image->get_alt(),$image->get_longdesc(),$linkname);
    } else {
      $url_preview=$this->make_url(uwc_image_make_preview_url($path,$image_id),$make_url_array);
      $html=uwc_format_html_for_img_link_preview($url_page,$url_preview,$image->get_preview_w(),$image->get_preview_h(),$image->get_alt(),$image->get_longdesc(),$linkname,$this->img_odd);
      
      $this->img_odd=!$this->img_odd;
    }

    return $html;
  }

  function make_page_link($rel_path,$linkname) { 
    /*
     * Ugly piece of code, needs heavy rewritting but will
     * probably stay as is for ages... It's too late, getting tired...
     */
    $path=$this->get_path();
    $tmp_path=$rel_path;
    $disp_path=".";
    while (preg_match("#^([^\\/]+)\\/(.*)\$#",$tmp_path,$match)) {
      if ($match[1]==".") {
	// just skip ./
      } elseif ($match[1]=="..") {
	$path=uwc_content_get_parent_path($path);
	if ($disp_path != ".") {
	  $disp_path=uwc_content_get_parent_path($disp_path);
	} else {
	  $disp_path.="/".$match[1];
	}
      } else {
	$path.="/".$match[1];
	$disp_path.="/".$match[1];
      }
      $tmp_path=$match[2];
    }
    $path.="/".$tmp_path;
    $disp_path.="/".$tmp_path;
    $disp_path=substr($disp_path,2);
    if (!$disp_path) {
      $disp_path="./";
    }
    if (!$linkname) {
      $linkname=$disp_path;
    }
    $path=$this->make_url($path);
    $html="<a href=\"$path\">$linkname</a>\n";

    return $html;
  }

  function make_lang_link($lang,$linkname) { 
    if (!$linkname) {
      $linkname=$this->translate("view_lang_".$lang);
    }
    $html=sprintf("<a href=\"%s\">%s</a>\n",
		  $this->get_translate_url($lang),
		  $linkname);
    return $html;
  }

  function get_user_id() {
    return $this->auth->get_user_id();
  }

  function get_user_label() {
    return $this->auth->get_user_label();
  }

  function get_meta_author() {
    return $this->get_author();
  }

  function get_meta_description() {
    return $this->get_home_title();
  }

  function get_meta_keywords() {
    $keywords=$this->get_author();
    $keywords.=", ".$this->get_copyright_holder();

    foreach ($this->get_parents() as $parent) {
      if ($parent["title"]) {
	$keywords.=", ".uwc_format_text_to_html($parent["title"]);
      }	
    }

    $keywords.=", ".$this->get_title();

    return $keywords;
  }

  function get_meta_copyright() {
    return $this->translate2("meta_copyright",strftime("%Y",$this->get_date_update()),$this->get_copyright_holder());
  }

  function today() {
    return $this->lang->format_date((time()));
  }

  function translate($msg_id) {
    return $this->lang->get_message($msg_id);
  }

  function translate_xul($msg_id) {
    return uwc_format_html_to_xul($this->translate($msg_id));
  }

  function translate1($msg_id,$par1) {
    return sprintf($this->lang->get_message($msg_id),$par1);
  }

  function translate2($msg_id,$par1,$par2) {
    return sprintf($this->lang->get_message($msg_id),$par1,$par2);
  }

  function translate3($msg_id,$par1,$par2,$par3) {
    return sprintf($this->lang->get_message($msg_id),$par1,$par2,$par3);
  }

  function translate4($msg_id,$par1,$par2,$par3,$par4) {
    return sprintf($this->lang->get_message($msg_id),$par1,$par2,$par3,$par4);
  }

  function get_translated() {
    $langs=array();

    foreach ($this->context->get_data_langs() as $lang) {
      if ($lang!=$this->get_lang()) {
	array_push($langs,$lang);
      }
    }

    asort($langs);

    return $langs;
  }

  function get_untranslated() {
    $langs=array();
    $data_langs=array_flip($this->context->get_data_langs());

    foreach (array_keys($this->lang->langs) as $lang) {
      if ($lang!=$this->get_lang() && !array_key_exists($lang,$data_langs)) {
	array_push($langs,$lang);
      }
    }

    asort($langs);

    return $langs;
  }

  function viewable() {
    return ($this->content->exists() && $this->context->action!="delete" && $this->auth->can_view($this->get_path(),$this->content->get_status()));
  }

  function editable() {
    return ($this->content->exists() && $this->context->action!="delete" && $this->auth->can_edit($this->get_path(),$this->content->get_status()));
  }

  function need_action_root() {
    return $this->get_path()!="";
  }

  function need_action_view() {
    return $this->context->action!="view" && $this->context->action!="external" && $this->context->action!="imageview" && $this->viewable();
  }

  function need_action_rss() {
    return $this->need_rss();
  }

  function need_action_tree() {
    return $this->context->action!="tree" && $this->viewable() && $this->need_children();
  }

  function need_action_css_no() {
    return $this->context->use_css && !$this->need_action_view();
  }

  function need_action_css_yes() {
    return !$this->context->use_css && !$this->need_action_view();
  }

  function need_action_update() {
    return $this->context->action!="updateform" && $this->editable();
  }

  function need_action_delete() {
    return $this->context->action!="deleteform" && $this->editable();
  }

  function need_action_clearcache() {
    return $this->context->action!="clearcacheform" && $this->context->action!="clearcache" && $this->auth->can_admin();
  }

  function need_action_renum() {
    return $this->context->action!="renumform" && $this->context->action!="renum" && $this->auth->can_admin() && (!$this->conf->dbprefix) && (!$this->get_path());
  }

  function need_action_help() {
    return $this->get_user_status()>=2;
  }

  function need_action_allinone() {
    return ($this->context->action=="view" || $this->context->action=="tree" || $this->context->action=="external") && $this->auth->can_admin() && $this->viewable();
  }

  function need_action_new() {
    return $this->context->action!="createform" && $this->context->action!="delete" && $this->context->get_type()=="text" && $this->editable();
  }

  function need_action_imagenew() {
    return (!preg_match("/image/",$this->context->action)) && $this->editable();
  }

  function need_action_translate() {
    return count($this->get_untranslated())>0 && $this->editable();
  }

  function need_action_gallery() {
    return (($this->context->action=="view" || $this->context->action=="external") && count($this->get_images_list())>=2) || $this->context->action=="imageview";
  }

  function need_continue_url() {
    //return $this->context->get_continue_url() ? true : false;
    return true;
  }

  function need_parents() {
    return count($this->get_parents()) ? true : false;
  }

  function need_children() {
    return count($this->get_children()) ? true : false;
  }

  function need_news() {
    return count($this->get_news()) ? true : false;
  }

  function need_rss() {
    return count($this->get_rss()) ? true : false;
  }

  function need_nav() {
    return $this->need_home() || $this->need_up() || $this->need_next() || $this->need_prev() || $this->need_news();
  }

  function need_home() {
    return $this->content->get_home() ? true : false;
  }

  function need_up() {
    return $this->content->get_up() ? true : false;
  }

  function need_next() {
    return $this->content->get_next() ? true : false;
  }

  function need_prev() {
    return $this->content->get_prev() ? true : false;
  }

  function need_google() {
    return $this->content->get_status()==0;
  }

  function include_external() {
    uwc_external_include($this);
  }

  function need_control_focus() {
    return $this->conf->control_focus ? true : false;
  }

  function get_control_focus() {
    return $this->conf->control_focus;
  }

  function need_actions_focus() {
    return $this->conf->actions_focus ? true : false;
  }

  function get_actions_focus() {
    return $this->conf->actions_focus;
  }

  function set_http_code($http_code) {
    $this->http_code=(int) $http_code;
    uwc_contextutils_send_header_http_code($this->get_http_code());
  }

  function get_http_code() {
    switch ($this->http_code) {
    case 403:
    case 404:
      $http_code=$this->http_code;
      break;
    default:
      $http_code=200;
    }
    return $http_code;
  }

  function use_css() {
    return $this->context->use_css;
  }

  function process() {
    switch ($this->context->action) {
    case "logout":
      $this->auth->clear_user();
      $this->context->delete_session();
      break;
    }

    if ($this->context->request->has_key("password")) {
      /*
       * If there's a password in the request, we replace
       * it on the fly with a crypted session.
       */
      header("Location: ".$this->get_absolute_url_no_esc());
      return;
    }

    switch ($this->context->action) {
    case "new":
      /* Path is automatically changed within the context object */
      header("Location: ".$this->get_absolute_url_no_esc());
      return;
    case "403":
      $this->set_http_code(403);
      break;
    case "404":
      $this->set_http_code(404);
      break;
    default:
      $this->set_http_code(200);
    }

    switch ($this->context->action) {
    case "create":
      $this->data->insert_content($this->get_path(),
				  $this->get_lang(),
				  $this->request->get_value("title"),
				  $this->get_user_id(),
				  $this->request->get_value("text"),
				  $this->request->get_value("status"));
      $this->content->force_update_data();
      break;
    case "update":
      $oldpath=uwc_contextutils_fix_path($this->request->get_value("oldpath"));
      if ($oldpath!=$this->get_path()) {
	/*
	 * It's important to move things before the actual update.
	 */
	$this->data->move_content($oldpath,$this->get_path());
	$this->data->move_image($oldpath,$this->get_path());
      }
      if ($this->request->get_value("do_date_update")) {
	$this->data->update_content($this->get_path(),
				    $this->get_lang(),
				    $this->request->get_value("title"),
				    $this->get_user_id(),
				    $this->request->get_value("text"),
				    $this->request->get_value("status"),
				    $this->request->get_value("order"));
      } else {
	$this->data->update_content_without_date_update($this->get_path(),
							$this->get_lang(),
							$this->request->get_value("title"),
							$this->get_user_id(),
							$this->request->get_value("text"),
							$this->request->get_value("status"),
							$this->request->get_value("order"));
      }
      $this->content->force_update_data();
      break;
    case "delete":
      $this->data->delete_content_by_path_and_lang($this->get_path(),
						   $this->get_lang());
      break;
    case "imagecreate":
      $this->data->insert_image($this->context->get_image_id(),
				uwc_content_get_parent_path($this->get_path()),
				uwc_request_get_file_tmp("imagefile"),
				uwc_request_get_file_remote("imagefile"));
      $this->data->insert_legend($this->context->get_image_id(),
				 $this->get_lang(),
				 $this->request->get_value("alt"),
				 $this->request->get_value("longdesc"));
      $this->content->force_update_data();
      break;
    case "imageupdate":
      $imagefile=uwc_request_get_file_tmp("imagefile");
      if (uwc_image_check_file($imagefile)) {
	$this->data->update_image($this->context->get_image_id(),
				  $imagefile,
				  uwc_request_get_file_remote("imagefile"));
      }
      if ($this->content->legend_exists()) {
	$this->data->update_legend($this->context->get_image_id(),
				   $this->get_lang(),
				   $this->request->get_value("alt"),
				   $this->request->get_value("longdesc"));
      } else {
	$this->data->insert_legend($this->context->get_image_id(),
				   $this->get_lang(),
				   $this->request->get_value("alt"),
				   $this->request->get_value("longdesc"));
      }
      $this->content->force_update_data();
      break;
    case "imagedelete":
      $this->data->delete_image_by_id($this->context->get_image_id());
      $this->data->delete_legend_by_image_id($this->context->get_image_id());      
      break;
    case "clearcache":
      uwc_cache_purge($this->conf,0);
      break;
    case "renum":
      $this->content->renum();
      break;
    }

    switch ($this->context->action) {
    case "imageviewpreview":
      uwc_imagebinary_view_preview($this->data,$this->context->get_image_id());
      break;
    case "imageviewfull":
      uwc_imagebinary_view_full($this->data,$this->context->get_image_id());
      break;
    case "gallery":
      uwc_gallery_xul_header();
      include $this->get_prefix()."/_uwikicms/template/php/".$this->context->action.".php";
      break;
    case "rss":
      uwc_rss_xml_header();
      include $this->get_prefix()."/_uwikicms/template/php/".$this->context->action.".php";
      break;
    default:
      header("Content-Language: ".$this->get_lang());
      header("Content-Type: text/html; charset=ISO-8859-1");

      /*
       * General case, we use the template system.
       */
      include $this->get_prefix()."/_uwikicms/template/php/begin.php";
      
      switch ($this->context->action) {
	/*
	 * There's a switch here to filter the "allowed" templates
	 * and avoid security problems through forged action fields
	 */
      case "403":
      case "404":
      case "loginform":
      case "logout":
      case "view":
      case "external":
      case "tree":
      case "updateform":
      case "createform":
      case "deleteform":
      case "update":
      case "create":
      case "delete":
      case "imageview":
      case "imagecreateform":
      case "imageupdateform":
      case "imagedeleteform":
      case "imagecreate":
      case "imageupdate":
      case "imagedelete":
      case "clearcacheform":
      case "clearcache":
      case "renumform":
      case "renum":
      case "allinoneform":
      case "allinone":
      case "about":
	include $this->get_prefix()."/_uwikicms/template/php/".$this->context->action.".php";
      break;
      default:
	include $this->get_prefix()."/_uwikicms/template/php/about.php";
      }
      include $this->get_prefix()."/_uwikicms/template/php/end.php";

      /*
       * This is a trick we use to trigger cache cleanup. Cache cron
       * will check wether it has been executed recently, and if
       * not it will actually clear the cache. Otherwise nothing
       * is done and the performance impact is minimal. This way
       * one does not need to have cron access on the server.
       */
      uwc_cache_cron($this->conf);
    }
  }
}

?>
