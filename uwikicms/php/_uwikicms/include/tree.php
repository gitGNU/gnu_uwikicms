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

class UWC_Tree {
  var $title;
  var $path;
  var $subtrees;

  function UWC_Tree($title,$path) {
    $this->title=$title;
    $this->path=$path;
    $this->subtrees=array();
  }

  function add_subtree($title,$path) {
    $subtree=new UWC_Tree($title,$path);

    $p1=uwc_tree_explode_path($this->path);
    $p2=uwc_tree_explode_path($path);
    $c1=count($p1);
    $c2=count($p2);

    // assumes $p1 included in $p2
    if ($c1 < $c2-1) {
      $parent_tree=& $this->find_or_create_subtree(uwc_content_get_parent_path($subtree->get_path()));
      $parent_tree->add_subtree($title,$path);
    }

    if ($c1+1 == $c2) {
      array_push($this->subtrees, $subtree);      
    }
  }

  function &find_or_create_subtree($path) {
    $p1=uwc_tree_explode_path($this->path);
    $p2=uwc_tree_explode_path($path);
    $c1=count($p1);
    $c2=count($p2);    

    // assumes $p1 included in $p2
    if ($c1 < $c2-1) {
      $subtree=& $this->find_or_create_subtree(uwc_content_get_parent_path($path));
    }

    if ($c1+1 == $c2) {
      for ($i=0;$i < count($this->subtrees);++$i) {
	if ($this->subtrees[$i]->get_path() == $path) {
	  $subtree=& $this->subtrees[$i];
	}
      }
      if (!isset($subtree)) {
	$this->add_subtree("",$path);
	$subtree=& $this->find_or_create_subtree($path);
      }
    }

    return $subtree;
  }

  function &get_subtrees() {
    return $this->subtrees;
  }

  function get_title() {
    return $this->title;
  }

  function get_path() {
    return $this->path;
  }
}

function uwc_tree_explode_path($path) {
  $sp1=explode("/",$path);
  
  $sp2=array();
  foreach ($sp1 as $bc) {
    if ($bc) {
      array_push($sp2,$bc);
    }
  }
  
  return $sp2;
}

function uwc_tree_implode_path($sp) {
  $path="";
  foreach ($sp as $bc) {
    $path.="/".$bc;
    }
  
  return $path;
}


?>