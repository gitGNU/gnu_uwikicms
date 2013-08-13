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

class UWC_Data {
  var $db=false;
  var $sql=false;
  /*
   * Prefix is added to every "path" parameter with a line like
   * $path=$this->prefix.$path;
   * this enables the "subsite" feature of uwikicms, which one
   * of the reasons I made it for. When configured as a subsite,
   * a uwikicms instance has no idea of what's going on on
   * other sites...
   */
  var $prefix="";

  function UWC_Data(&$conf) {
    $this->db=new UWC_Db($conf);
    $this->sql=new UWC_Sql();
    $this->prefix=$conf->dbprefix;
  }

  function query($query) {
    $this->db->query($query);
  }

  function query_select($query) {
    return $this->db->query_select($query);
  }

  function query_select_num_rows($select_result=false) {
    return $this->db->query_select_num_rows($select_result);
  }

  function query_select_fetch_row($select_result=false) {
    return $this->db->query_select_fetch_row($select_result);
  }

  function query_select_free($select_result=false) {
    return $this->db->query_select_free($select_result);
  }

  function select_user_by_id($id) {
    $query=sprintf($this->sql->query["select_user_by_id"],
		   uwc_format_escape_sql($id));
    return $this->query_select($query);    
  }

  function select_user_by_id_and_passwd($id,$passwd) {
    $query=sprintf($this->sql->query["select_user_by_id_and_passwd"],
		   uwc_format_escape_sql($id),
		   uwc_format_escape_sql($passwd));
    return $this->query_select($query);    
  }

  function insert_content($path,$lang,$title,$author,$text,$status) {
    $path=$this->prefix.$path;
    $query=sprintf($this->sql->query["select_content_next_order"],
		   uwc_format_escape_sql($lang),
		   uwc_format_escape_sql($path));
    $this->query_select($query);
    $row=$this->query_select_fetch_row();
    $next_order=false;
    if ($row) {
      $next_order=$row["content_next_order"];
    }
    if (! $next_order) {
      $next_order="1";
    }
    $this->query_select_free();
    $query=sprintf($this->sql->query["insert_content"],
		   uwc_format_escape_sql($path),
		   uwc_format_escape_sql($lang),
		   uwc_format_escape_sql($title),
		   uwc_format_escape_sql($author),
		   uwc_format_escape_sql(uwc_format_fix_content_text($text)),
		   (int) uwc_format_escape_sql($status),
		   (int) uwc_format_escape_sql($next_order));
    $this->query($query);    
  }

  function update_content($path,$lang,$title,$author,$text,$status,$order) {
    /* 
     * Do not perform updae if title & text are NULL/empty, this should
     * prevent weird bugs (root page being resetted...) and anyway, if one
     * wants to get rid of content -> delete is safer...
     */
    if ($title || $text) {
      $path=$this->prefix.$path;
      $query=sprintf($this->sql->query["update_content"],
		     uwc_format_escape_sql($title),
		     uwc_format_escape_sql($author),
		     uwc_format_escape_sql(uwc_format_fix_content_text($text)),
		     (int) uwc_format_escape_sql($status),
		     (int) uwc_format_escape_sql($order),
		     uwc_format_escape_sql($path),
		     uwc_format_escape_sql($lang));
      $this->query($query);    
    }
  }

  function update_content_without_date_update($path,$lang,$title,$author,$text,$status,$order) {
    /* 
     * Do not perform updae if title & text are NULL/empty, this should
     * prevent weird bugs (root page being resetted...) and anyway, if one
     * wants to get rid of content -> delete is safer...
     */
    if ($title || $text) {
      $path=$this->prefix.$path;
      $query=sprintf($this->sql->query["update_content_without_date_update"],
		     uwc_format_escape_sql($title),
		     uwc_format_escape_sql($author),
		     uwc_format_escape_sql(uwc_format_fix_content_text($text)),
		     (int) uwc_format_escape_sql($status),
		     (int) uwc_format_escape_sql($order),
		     uwc_format_escape_sql($path),
		     uwc_format_escape_sql($lang));
      $this->query($query);    
    }
  }

  function delete_content_by_path_and_lang($path,$lang) {
    $path=$this->prefix.$path;
    $query=sprintf($this->sql->query["delete_content_by_path_and_lang"],
		   uwc_format_escape_sql($path),
		   uwc_format_escape_sql($lang));
    $this->query($query);    
  }

  function select_content_by_path_and_lang($path,$lang) {
    $path=$this->prefix.$path;
    $query=sprintf($this->sql->query["select_content_by_path_and_lang"],
		   uwc_format_escape_sql($path),
		   uwc_format_escape_sql($lang));
    return $this->query_select($query);    
  }

  function select_content_lang_by_path($path) {
    $path=$this->prefix.$path;
    $query=sprintf($this->sql->query["select_content_lang_by_path"],
		   uwc_format_escape_sql($path));
    return $this->query_select($query);    
  }

  function select_content_status_by_path_and_lang($path,$lang) {
    $path=$this->prefix.$path;
    $query=sprintf($this->sql->query["select_content_status_by_path_and_lang"],
		   uwc_format_escape_sql($path),
		   uwc_format_escape_sql($lang));
    return $this->query_select($query);    
  }

  function select_content_children_by_path_lang_and_status($path,$lang,$status) {
    $path=$this->prefix.$path;
    $query=sprintf($this->sql->query["select_content_children_by_path_lang_and_status"],
		   (int) strlen($this->prefix)+1,
		   uwc_format_escape_sql($path),
		   uwc_format_escape_sql($lang),
		   (int) uwc_format_escape_sql($status),
		   strlen($path)+2); // +1 because of SQL, another +1 for "/"
    return $this->query_select($query);    
  }

  function select_content_children_by_path_lang_status_and_domain_regex($path,$lang,$status,$domain_regex) {
    $path=$this->prefix.$path;
    $query=sprintf($this->sql->query["select_content_children_by_path_lang_status_and_domain_regex"],
		   (int) strlen($this->prefix)+1,
		   uwc_format_escape_sql($path),
		   uwc_format_escape_sql($lang),
		   (int) uwc_format_escape_sql($status),
		   uwc_format_escape_sql($domain_regex),
		   strlen($path)+2); // +1 because of SQL, another +1 for "/"
    return $this->query_select($query);    
  }

  function select_content_news_by_path_lang_and_status($path,$lang,$status,$news_max_nb,$news_max_age) {
    $path=$this->prefix.$path;
    $query=sprintf($this->sql->query["select_content_news_by_path_lang_and_status"],
		   (int) strlen($this->prefix)+1,
		   uwc_format_escape_sql($path),
		   uwc_format_escape_sql($lang),
		   (int) uwc_format_escape_sql($status),
		   (int) $news_max_age,
		   (int) $news_max_nb);
    return $this->query_select($query);    
  }

  function select_content_news_by_path_lang_status_and_domain_regex($path,$lang,$status,$domain_regex,$news_max_nb,$news_max_age) {
    $path=$this->prefix.$path;
    $query=sprintf($this->sql->query["select_content_news_by_path_lang_status_and_domain_regex"],
		   (int) strlen($this->prefix)+1,
		   uwc_format_escape_sql($path),
		   uwc_format_escape_sql($lang),
		   (int) uwc_format_escape_sql($status),
		   uwc_format_escape_sql($domain_regex),
		   (int) $news_max_age,
		   (int) $news_max_nb);
    return $this->query_select($query);    
  }

  function select_content_rss_by_path_and_lang($path,$lang,$rss_max_nb,$rss_max_age,$rss_max_len) {
    $path=$this->prefix.$path;
    $query=sprintf($this->sql->query["select_content_rss_by_path_and_lang"],
		   (int) strlen($this->prefix)+1,
		   (int) $rss_max_len,
		   uwc_format_escape_sql($path),
		   uwc_format_escape_sql($lang),
		   (int) $rss_max_age,
		   (int) $rss_max_nb);
    return $this->query_select($query);    
  }

  function select_content_tree_by_path_lang_and_status($path,$lang,$status) {
    $path=$this->prefix.$path;
    $query=sprintf($this->sql->query["select_content_tree_by_path_lang_and_status"],
		   (int) strlen($this->prefix)+1,
		   uwc_format_escape_sql($path),
		   uwc_format_escape_sql($lang),
		   (int) uwc_format_escape_sql($status));
    return $this->query_select($query);    
  }

  function select_content_tree_by_path_lang_status_and_domain_regex($path,$lang,$status,$domain_regex) {
    $path=$this->prefix.$path;
    $query=sprintf($this->sql->query["select_content_tree_by_path_lang_status_and_domain_regex"],
		   (int) strlen($this->prefix)+1,
		   uwc_format_escape_sql($path),
		   uwc_format_escape_sql($lang),
		   (int) uwc_format_escape_sql($status),
		   uwc_format_escape_sql($domain_regex));
    return $this->query_select($query);    
  }

  function select_content_title_by_path_lang_and_status($path,$lang,$status) {
    $path=$this->prefix.$path;
    $query=sprintf($this->sql->query["select_content_title_by_path_lang_and_status"],
		   uwc_format_escape_sql($path),
		   uwc_format_escape_sql($lang),
		   (int) uwc_format_escape_sql($status));
    return $this->query_select($query);
  }

  function select_content_title_by_path_lang_status_and_domain_regex($path,$lang,$status,$domain_regex) {
    $path=$this->prefix.$path;
    $query=sprintf($this->sql->query["select_content_title_by_path_lang_status_and_domain_regex"],
		   uwc_format_escape_sql($path),
		   uwc_format_escape_sql($lang),
		   (int) uwc_format_escape_sql($status),
		   uwc_format_escape_sql($domain_regex));
    return $this->query_select($query);
  }

  function select_content_prev_by_path_lang_and_status($path,$lang,$status,$order) {
    $path=uwc_content_get_parent_path($path);
    // Important to add prefix *after* getting parent
    $path=$this->prefix.$path;
    $query=sprintf($this->sql->query["select_content_prev_by_path_lang_and_status"],
		   (int) strlen($this->prefix)+1,
		   uwc_format_escape_sql($path),
		   uwc_format_escape_sql($lang),
		   (int) uwc_format_escape_sql($status),
		   strlen($path)+2, // +1 because of SQL, another +1 for "/"
		   (int) uwc_format_escape_sql($order));
    return $this->query_select($query);    
  }

  function select_content_prev_by_path_lang_status_and_domain_regex($path,$lang,$status,$domain_regex,$order) {
    $path=uwc_content_get_parent_path($path);
    // Important to add prefix *after* getting parent
    $path=$this->prefix.$path;
    $query=sprintf($this->sql->query["select_content_prev_by_path_lang_status_and_domain_regex"],
		   (int) strlen($this->prefix)+1,
		   uwc_format_escape_sql($path),
		   uwc_format_escape_sql($lang),
		   (int) uwc_format_escape_sql($status),
		   uwc_format_escape_sql($domain_regex),
		   strlen($path)+2, // +1 because of SQL, another +1 for "/"
		   (int) uwc_format_escape_sql($order));
    return $this->query_select($query);    
  }

  function select_content_next_by_path_lang_and_status($path,$lang,$status,$order) {
    $path=uwc_content_get_parent_path($path);
    // Important to add prefix *after* getting parent
    $path=$this->prefix.$path;
    $query=sprintf($this->sql->query["select_content_next_by_path_lang_and_status"],
		   (int) strlen($this->prefix)+1,
		   uwc_format_escape_sql($path),
		   uwc_format_escape_sql($lang),
		   (int) uwc_format_escape_sql($status),
		   strlen($path)+2, // +1 because of SQL, another +1 for "/"
		   (int) uwc_format_escape_sql($order));
    return $this->query_select($query);    
  }

  function select_content_next_by_path_lang_status_and_domain_regex($path,$lang,$status,$domain_regex,$order) {
    $path=uwc_content_get_parent_path($path);
    // Important to add prefix *after* getting parent
    $path=$this->prefix.$path;
    $query=sprintf($this->sql->query["select_content_next_by_path_lang_status_and_domain_regex"],
		   (int) strlen($this->prefix)+1,
		   uwc_format_escape_sql($path),
		   uwc_format_escape_sql($lang),
		   (int) uwc_format_escape_sql($status),
		   uwc_format_escape_sql($domain_regex),
		   strlen($path)+2, // +1 because of SQL, another +1 for "/"
		   (int) uwc_format_escape_sql($order));
    return $this->query_select($query);    
  }

  function select_image_next_id() {
    $query=sprintf($this->sql->query["select_image_next_id"]);
    return $this->query_select($query);    
  }

  function insert_image($id,$path,$imagefile,$filename) {
    $path=$this->prefix.$path;
    $sql_data=uwc_image_prepare_sql($imagefile);
    $query=sprintf($this->sql->query["insert_image"],
		   (int) uwc_format_escape_sql($id),
		   uwc_format_escape_sql($path),
		   uwc_format_escape_sql_bin($sql_data["preview"]),
		   (int) uwc_format_escape_sql($sql_data["preview_w"]),
		   (int) uwc_format_escape_sql($sql_data["preview_h"]),
		   uwc_format_escape_sql_bin($sql_data["full"]),
		   (int) uwc_format_escape_sql($sql_data["full_w"]),
		   (int) uwc_format_escape_sql($sql_data["full_h"]),
		   uwc_format_escape_sql($filename));
    $this->query($query);    
  }

  function insert_legend($id,$lang,$alt,$longdesc) {
    $query=sprintf($this->sql->query["insert_legend"],
		   (int) uwc_format_escape_sql($id),
		   uwc_format_escape_sql($lang),
		   uwc_format_escape_sql($alt),
		   uwc_format_escape_sql($longdesc));
    $this->query($query);        
  }

  function select_legend_lang_by_id($id) {
    $query=sprintf($this->sql->query["select_legend_lang_by_id"],
		   (int) uwc_format_escape_sql($id));
    return $this->query_select($query);    
  }
  
  function select_image_full_by_id($id) {
    $query=sprintf($this->sql->query["select_image_full_by_id"],
		   (int) uwc_format_escape_sql($id));
    return $this->query_select($query);    
  }

  function select_image_preview_by_id($id) {
    $query=sprintf($this->sql->query["select_image_preview_by_id"],
		   (int) uwc_format_escape_sql($id));
    return $this->query_select($query);    
  }

  function select_image_status_by_id($id) {
    $query=sprintf($this->sql->query["select_image_status_by_id"],
		   (int) uwc_format_escape_sql($id));
    return $this->query_select($query);        
  }

  function select_image_by_id($id) {
    $query=sprintf($this->sql->query["select_image_by_id"],
		   (int) uwc_format_escape_sql($id));
    return $this->query_select($query);        
  }

  function select_legend_by_id_and_lang($id,$lang) {
    $query=sprintf($this->sql->query["select_legend_by_id_and_lang"],
		   (int) uwc_format_escape_sql($id),
		   uwc_format_escape_sql($lang));
    return $this->query_select($query);        
  }

  function select_image_by_container_path($path) {
    $path=$this->prefix.$path;
    $query=sprintf($this->sql->query["select_image_by_container_path"],
		   uwc_format_escape_sql($path));
    return $this->query_select($query);        
  }

  function select_legend_by_container_path_and_lang($path,$lang) {
    $path=$this->prefix.$path;
    $query=sprintf($this->sql->query["select_legend_by_container_path_and_lang"],
		   uwc_format_escape_sql($path),
		   uwc_format_escape_sql($lang));
    return $this->query_select($query);        
  }

  function select_legend_next_by_container_path_lang_and_id($path,$lang,$id) {
    $path=$this->prefix.$path;
    $query=sprintf($this->sql->query["select_legend_next_by_container_path_lang_and_id"],
		   uwc_format_escape_sql($path),
		   uwc_format_escape_sql($lang),
		   (int) uwc_format_escape_sql($id));
    return $this->query_select($query);        
  }

  function select_legend_prev_by_container_path_lang_and_id($path,$lang,$id) {
    $path=$this->prefix.$path;
    $query=sprintf($this->sql->query["select_legend_prev_by_container_path_lang_and_id"],
		   uwc_format_escape_sql($path),
		   uwc_format_escape_sql($lang),
		   (int) uwc_format_escape_sql($id));
    return $this->query_select($query);        
  }

  function update_image($id,$imagefile,$filename) {
    $sql_data=uwc_image_prepare_sql($imagefile);
    $query=sprintf($this->sql->query["update_image"],
		   uwc_format_escape_sql_bin($sql_data["preview"]),
		   (int) uwc_format_escape_sql($sql_data["preview_w"]),
		   (int) uwc_format_escape_sql($sql_data["preview_h"]),
		   uwc_format_escape_sql_bin($sql_data["full"]),
		   (int) uwc_format_escape_sql($sql_data["full_w"]),
		   (int) uwc_format_escape_sql($sql_data["full_h"]),
		   uwc_format_escape_sql($filename),
		   (int) uwc_format_escape_sql($id));
    $this->query($query);    
  }

  function update_legend($id,$lang,$alt,$longdesc) {
    $query=sprintf($this->sql->query["update_legend"],
		   uwc_format_escape_sql($alt),
		   uwc_format_escape_sql($longdesc),
		   (int) uwc_format_escape_sql($id),
		   uwc_format_escape_sql($lang));
    $this->query($query);        
  }

  function delete_image_by_id($id) {
    $query=sprintf($this->sql->query["delete_image_by_id"],
		   (int) uwc_format_escape_sql($id));
    $this->query($query);        
  }

  function delete_legend_by_image_id($id) {
    $query=sprintf($this->sql->query["delete_legend_by_image_id"],
		   (int) uwc_format_escape_sql($id));
    $this->query($query);        
  }

  function move_content($oldpath,$path) {
    $oldpath=$this->prefix.$oldpath;
    $path=$this->prefix.$path;
    $query=sprintf($this->sql->query["move_content"],
		   uwc_format_escape_sql($path),
		   (int) strlen($oldpath)+1,
		   uwc_format_escape_sql($oldpath));
    $this->query($query);
  }

  function move_image($oldpath,$path) {
    $oldpath=$this->prefix.$oldpath;
    $path=$this->prefix.$path;
    $query=sprintf($this->sql->query["move_image"],
		   uwc_format_escape_sql($path),
		   (int) strlen($oldpath)+1,
		   uwc_format_escape_sql($oldpath));
    $this->query($query);
  }
}

?>
