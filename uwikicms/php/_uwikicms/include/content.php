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

class UWC_Content {
  var $data=null;
  var $updated=false;
  var $data_exists=false;
  var $children_updated=false;
  var $news_updated=false;
  var $rss_updated=false;
  var $tree_updated=false;
  var $parents_updated=false;
  var $next_updated=false;
  var $prev_updated=false;
  var $imagelist_updated=false;
  var $path="";
  var $lang="";
  var $auth=null;
  var $title="";
  var $author="";
  var $copyright_holder="";
  var $date_create=0;
  var $date_update=0;
  var $text="";
  var $status=3;
  var $order=0;

  function UWC_Content(&$data,$path,$lang,&$auth) {
    $this->data=& $data;
    $this->path=$path;
    $this->lang=$lang;
    $this->auth=& $auth;
  }

  function get_user_status() {
    return $this->auth->get_user_status();
  }

  function get_user_domain_regex() {
    return $this->auth->get_user_domain_regex();
  }

  function force_update_data() {
    $this->updated=true;
    $this->title="";
    $this->author="";
    $this->copyright_holder="";
    $this->email="";
    $this->date_create=0;
    $this->date_update=0;
    $this->text="";
    $this->status=3;
    $this->order=0;
    $this->data->select_content_by_path_and_lang($this->path,$this->lang);
    $row=$this->data->query_select_fetch_row();
    if ($row) {
      $this->data_exists=true;
      $this->title=$row["content_title"];
      $this->author=$row["user_label"];
      $this->copyright_holder=$row["user_copyright_holder"];
      $this->email=$row["user_email"];
      $this->date_create=(int) $row["content_date_create"];
      $this->date_update=(int) $row["content_date_update"];
      $this->text=$row["content_text"];
      $this->status=(int) ($row["content_status"]);
      $this->order=(int) ($row["content_order"]);      
    } else {
      /*
       * We try to get parent status, this is usefull when
       * creating sub-pages.
       */
      $this->data->select_content_status_by_path_and_lang(uwc_content_get_parent_path($this->path),$this->lang);
      $row=$this->data->query_select_fetch_row();
      if ($row) {
	$this->status=(int) ($row["content_status"]);
      }      
    }
    $this->data->query_select_free();
  }

  function update_data_uwikicms_page() {
    $this->updated=true;
    $this->update_parents_data();
    if (count($this->parents)) {
      $this->parents=array($this->parents[0]);
      $this->up=$this->parents[0];
    }
  }

  function update_data() {
    if (!$this->updated) {
      if (!uwc_contextutils_is_uwikicms_path($this->path)) {
	$this->force_update_data();
      } else {
	// not a content page!!
	$this->update_data_uwikicms_page();
      }
    }
  }

  function update_children_data() {
    if (!$this->children_updated) {
      $this->children_updated=true;
      $this->children=array();
      if ($this->get_user_status()==2) {
	$this->data->select_content_children_by_path_lang_status_and_domain_regex($this->path,$this->lang,$this->get_user_status(),$this->get_user_domain_regex());
      } else {
	$this->data->select_content_children_by_path_lang_and_status($this->path,$this->lang,$this->get_user_status());
      }
      while ($row=$this->data->query_select_fetch_row()) {
	array_push($this->children, 
		   array("path"=>$row["content_path"],
			 "title"=>$row["content_title"]));
      }
      $this->data->query_select_free();
    }
  }

  function update_news_data($news_max_nb, $news_max_age) {
    $this->update_children_data();
    if (!$this->news_updated) {
      $this->news_updated=true;
      $this->news=array();
      if ($this->get_user_status()==2) {
	$this->data->select_content_news_by_path_lang_status_and_domain_regex($this->path,$this->lang,$this->get_user_status(),$this->get_user_domain_regex(),$news_max_nb,$news_max_age);
      } else {
	$this->data->select_content_news_by_path_lang_and_status($this->path,$this->lang,$this->get_user_status(),$news_max_nb,$news_max_age);
      }
      while ($row=$this->data->query_select_fetch_row()) {
	$item = array("path"=>$row["content_path"],
		      "title"=>$row["content_title"]);
	if (!in_array($item, $this->children)) {
	  array_push($this->news, $item);
	}
      }
      $this->data->query_select_free();
    }
  }

  function update_rss_data($rss_max_nb,$rss_max_age,$rss_max_len) {
    if (!$this->rss_updated) {
      $this->rss_updated=true;
      $this->rss=array();
      $this->data->select_content_rss_by_path_and_lang($this->path,$this->lang,$rss_max_nb,$rss_max_age,$rss_max_len);
      while ($row=$this->data->query_select_fetch_row()) {
	array_push($this->rss, 
		   array("path"=>$row["content_path"],
			 "title"=>$row["content_title"],
			 "text"=>$row["content_text"],
			 "date_update"=>$row["content_date_update"]));
      }
      $this->data->query_select_free();
    }
  }

  function update_tree_data($status) {
    if (!$this->tree_updated) {
      $this->tree_updated=true;
      $this->tree=array();
      if ($status>$this->get_user_status()) {
	$status=$this->get_user_status();
      }
      if ($status==2) {
	$this->data->select_content_tree_by_path_lang_status_and_domain_regex($this->path,$this->lang,$status,$this->get_user_domain_regex());
      } else {
	$this->data->select_content_tree_by_path_lang_and_status($this->path,$this->lang,$status);
      }
      while ($row=$this->data->query_select_fetch_row()) {
	$this->tree[$row["content_path"]]=
	  array("title"=>$row["content_title"],
		"order"=>$row["content_order"],
		"status"=>$row["content_status"]);
      }
      $this->data->query_select_free();

      $this->tree_sort();
      $this->tree_struct();
    }
  }

  function tree_sort() {
    // carefull, tree_sort changes the tree structure
    $order2=1;
    $sorted_tree=array();
    foreach ($this->tree as $key=>$row) {
      $sp=uwc_tree_explode_path($key);
      $path="";
      $order_ascii="";
      foreach ($sp as $bc) {
	$path.="/".$bc;
	$order=isset($this->tree[$path]) ? $this->tree[$path]["order"] : 999999999;
	if (isset($this->tree[$path])) {
	  $order=$this->tree[$path]["order"];
	} else {
	  $order=999999999;
	  $this->tree[$path]=Array();
	  $this->tree[$path]["order"]=$order;
	}
	if (!isset($this->tree[$path]["order2"])) {
	  /*
	   * Order2 is here to make the difference between 2 folders
	   * which would have the same order (which is always possible).
	   * Without this hack subtrees from these 2 folders could
	   * be mixed...
	   */
	  $this->tree[$path]["order2"]=$order2++;
	}
	$order2=$this->tree[$path]["order2"];
	$order_ascii.=sprintf("/%09d-%09d",$order,$order2);
      }      
      $sorted_tree[$order_ascii]=$this->tree[$key];
      $sorted_tree[$order_ascii]["path"]=$key;
    }

    ksort($sorted_tree);

    $this->tree=&$sorted_tree;
  }

  function tree_struct() {
    // carefull, tree_struct changes the tree structure
    $structured_tree=new UWC_Tree($this->get_title(),$this->path);

    foreach ($this->tree as $key=>$row) {
      $structured_tree->add_subtree($row["title"],$row["path"]);
    }

    $this->tree=&$structured_tree;
  }

  function update_parents_data() {
    if (!$this->parents_updated) {
      $this->parents_updated=true;
      $this->parents=array();
      //data for the "up" link is collected here
      $this->up=false;
      $this->home=false;
      $path=$this->path;
      $old_path="";
      //array_push($this->parents,array("path"=>$path,"title"=>$this->get_title()));
      while ($path!="" && $path!=$old_path) {
	$old_path=$path;
	$path=uwc_content_get_parent_path($path);
	if ($this->get_user_status()==2) {
	  $this->data->select_content_title_by_path_lang_status_and_domain_regex($path,$this->lang,$this->get_user_status(),$this->get_user_domain_regex());
	} else {
	  $this->data->select_content_title_by_path_lang_and_status($path,$this->lang,$this->get_user_status());
	}
	if ($row=$this->data->query_select_fetch_row()) {
	  $title=$row["content_title"];
	} else {
	  $title=""; // we may miss a parent from db, this can be normal
	}
	$this->data->query_select_free();
	array_push($this->parents,array("path"=>$path,"title"=>$title));
	if (!$this->up) {
	  $this->up=array("path"=>$path,"title"=>$title);
	}
	if ($path=="") {
	  $this->home=array("path"=>$path,"title"=>$title);
	}
      }
      $this->parents=array_reverse($this->parents);
    }
  }

  function update_next_data() {
    if (!$this->next_updated) {
      $this->next_updated=true;
      $this->next=null;
      if ($this->path != "") {
	if ($this->get_user_status()==2) {
	  $this->data->select_content_next_by_path_lang_status_and_domain_regex($this->path,$this->lang,$this->get_user_status(),$this->get_user_domain_regex(),$this->get_order());
	} else {
	  $this->data->select_content_next_by_path_lang_and_status($this->path,$this->lang,$this->get_user_status(),$this->get_order());
	}
	if ($row=$this->data->query_select_fetch_row()) {
	  $this->next=array("path"=>$row["content_path"],"title"=>$row["content_title"]);
	}
	$this->data->query_select_free();
      }
    }
  }

  function update_prev_data() {
    if (!$this->prev_updated) {
      $this->prev_updated=true;
      $this->prev=null;
      if ($this->path != "") {
	if ($this->get_user_status()==2) {
	  $this->data->select_content_prev_by_path_lang_status_and_domain_regex($this->path,$this->lang,$this->get_user_status(),$this->get_user_domain_regex(),$this->get_order());
	} else {
	  $this->data->select_content_prev_by_path_lang_and_status($this->path,$this->lang,$this->get_user_status(),$this->get_order());
	}
	if ($row=$this->data->query_select_fetch_row()) {
	  $this->prev=array("path"=>$row["content_path"],"title"=>$row["content_title"]);
	}
	$this->data->query_select_free();
      }
    }
  }

  function update_imagelist_data() {
    if (!$this->imagelist_updated) {
      $this->imagelist_updated=true;
      $this->imagelist=new UWC_Imagelist($this->data, $this->path, $this->lang);
    }
  }

  function exists() {
    $this->update_data();
    return $this->data_exists;
  }

  function get_path() {
    return $this->path;
  }

  function get_title() {
    $this->update_data();
    return $this->title;
  }

  function get_author() {
    $this->update_data();
    return $this->author;
  }

  function get_copyright_holder() {
    $this->update_data();
    return $this->copyright_holder;
  }

  function get_email() {
    $this->update_data();
    return $this->email;
  }

  function get_date_create() {
    $this->update_data();
    return $this->date_create;
  }

  function get_date_update() {
    $this->update_data();
    return $this->date_update;
  }

  function get_text() {
    $this->update_data();
    return $this->text;
  }

  function get_status() {
    $this->update_data();
    return $this->status;
  }

  function get_order() {
    $this->update_data();
    return $this->order;
  }

  function get_children() {
    $this->update_children_data();
    return $this->children;    
  }

  function get_news($news_max_nb, $news_max_age) {
    $this->update_news_data($news_max_nb, $news_max_age);
    return $this->news;    
  }

  function get_rss($rss_max_nb, $rss_max_age, $rss_max_len) {
    $this->update_rss_data($rss_max_nb, $rss_max_age, $rss_max_len);
    return $this->rss;    
  }

  function get_tree($status) {
    $this->update_tree_data($status);
    return $this->tree;    
  }

  function get_parents() {
    $this->update_parents_data();
    return $this->parents;    
  }

  function get_home() {
    $this->update_parents_data();
    return $this->home;    
  }

  function get_up() {
    $this->update_parents_data();
    return $this->up;    
  }

  function get_next() {
    $this->update_next_data();
    return $this->next;
  }

  function get_prev() {
    $this->update_prev_data();
    return $this->prev;
  }

  function get_images_list() {
    $this->update_imagelist_data();
    if ($this->get_user_status()>=$this->get_status()) {
      return $this->imagelist->get_list();
    } else {
      return Array();
    }
  }
}

function uwc_content_get_parent_path($path) {
  if (preg_match("/(.*)\/[^\/]+$/",$path,$matches)) {
    $parent=$matches[1];
  } else {
    $parent="";
  }
  
  return $parent;
}


?>
