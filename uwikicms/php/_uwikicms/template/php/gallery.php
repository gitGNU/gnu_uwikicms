<?
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
<?
echo "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>\n";
echo "<?xml-stylesheet href=\"chrome://global/skin/\" type=\"text/css\"?>\n";
?>
<window
    id="uwc_gallery"
    orient="horizontal"
    xmlns:html="http://www.w3.org/1999/xhtml"
    xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">
<script type="application/x-javascript" src="<? echo $this->get_js_dir(); ?>/gallery.js"/>
<box orient="vertical">
<html:p>
<? foreach ($this->get_parents() as $parent) { ?>
<html:a href="<? echo $this->make_url($parent["path"]); ?>">
<? 
   if ($parent["title"]) {
     echo uwc_format_text_to_xul($parent["title"]);
   } else {
     echo $this->translate_xul("no_title");
   }
?>
</html:a> &gt; 
<? } ?>
<? echo $this->translate_xul("gallery_title"); ?>
</html:p>
<box orient="horizontal">
<button
    id="uwc_gallery_prev"
    image="<? echo $this->get_images_dir(); ?>/arrow_left.png"
    label="<? echo $this->translate_xul("gallery_prev");?>" 
    onclick="uwc_gallery_prev_image();" />
<button
    id="uwc_gallery_next"
    image="<? echo $this->get_images_dir(); ?>/arrow_right.png"
    label="<? echo $this->translate_xul("gallery_next");?>"
    onclick="uwc_gallery_next_image();" />
</box>
<box orient="horizontal">
<? 
$images_list=$this->get_images_list();
?>
<? if (count($images_list)) { ?>
<tabbox id="uwc_gallery_tabbox">
  <tabs>
<? $n=1; ?>
<? foreach($images_list as $id => $image_data) { ?>
    <tab label="<? echo $n; ?>" />
<?   $n++; ?>
<? } ?>
  </tabs>
  <tabpanels>
<? foreach($images_list as $id => $image_data) { ?>
    <tabpanel orient="horizontal">
      <box orient="vertical">
        <box orient="horizontal" style='overflow: auto;'>
          <image src="<? echo $this->make_url(uwc_image_make_full_url($this->get_up_path(),$id)); ?>" width="<? echo $image_data["fullscaled_w"]; ?>" height="<? echo $image_data["fullscaled_h"]; ?>" />
        </box>
        <html:p>
          <html:a href="<? echo $this->make_url(uwc_image_make_page_url($this->get_up_path(),$id)); ?>"><? echo uwc_format_text_to_xul($image_data["alt"]); ?></html:a>
        </html:p>
        <html:p>
          <? echo uwc_format_text_to_xul($image_data["longdesc"]); ?>
        </html:p>
      </box>
    </tabpanel>
<? } ?>
  </tabpanels>
</tabbox>
<? } else { ?>
  <html:p>
    <html:a href="<? echo $this->make_url($this->get_up_path()); ?>"><? echo $this->translate_xul("gallery_empty"); ?></html:a>
  </html:p>
<? } ?>
</box>
  <html:p>
    <? echo uwc_format_html_to_xul($this->translate2("xul_generated_by_uwikicms", $this->conf->version,$this->today())); ?>
  </html:p>
</box>
</window>
