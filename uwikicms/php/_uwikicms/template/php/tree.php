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
?>
<div id="tree" class="default">
<?php
function display_tree($tree,&$page) {
   //  if ($tree->get_path()!=$page->get_path()) {
    echo "<a href=\"";
    echo $page->make_url($tree->get_path());
    echo "\">";
    if ($tree->get_title()) {
      echo uwc_format_text_to_html($tree->get_title());
    } else {
      echo $page->translate("no_title");
    }
    echo "</a>";
    //  } else {
    //    // We are on this very same page, we disable the link
    //    echo $page->get_title();    
    //  }
  $subtrees=& $tree->get_subtrees();
  if (count($subtrees)) {
    echo "\n<ul>\n";
    foreach ($subtrees as $subtree) {
      echo "<li>";
      display_tree($subtree,$page);
      echo "</li>\n";
    }
    echo "</ul>\n";
  }									     
}

display_tree($this->get_tree(),$this);
?>

</div>
