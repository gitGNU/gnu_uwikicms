<?php
/*
 UWiKiCMS is a lightweight web content management system.
 Copyright (C) 2005, 2006, 2007 Christian Mauduit <ufoot@ufoot.org>

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
<div id="content">

<div class="default">
<p>
<?php echo $this->get_allinone_intro(); ?>
</p>
</div>

<?php
function display_all_in_one($tree,&$page) {
  $content=new UWC_Content($page->data,$tree->get_path(),$page->context->get_lang(),$page->auth);

  $title=$content->get_title();
  if ($title || $content->get_text()) {
    if ($title) {
      $title=uwc_format_text_to_html($title);
    } else {
      $title=$page->translate("no_title");
    }

    echo "<div class=\"status".$content->get_status()."\">\n";
    echo "<h1><a href=\"".$page->make_url($content->get_path())."\">".$title."</a></h1>\n";
    
    $orig_path=$page->get_path();
    $page->set_allinone_path($content->get_path());
    echo uwc_format_phpwiki_to_html($content->get_text());
    $page->set_allinone_path($orig_path);
    
    echo "</div>\n\n";
  }

  $subtrees=& $tree->get_subtrees();
  if (count($subtrees)) {
    echo "\n\n";
    foreach ($subtrees as $subtree) {
      display_all_in_one($subtree,$page);
    }
  }									     
}

display_all_in_one($this->get_tree(),$this);
?>

</div>
