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
<?php
echo "<?phpxml version=\"1.0\" encoding=\"ISO-8859-1\" ?>\n";
echo "<?phpxml-stylesheet href=\"chrome://global/skin/\" type=\"text/css\"?>\n";
?>
<window
    id="uwc_gallery"
    orient="horizontal"
    xmlns:html="http://www.w3.org/1999/xhtml"
    xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">
<script type="application/x-javascript" src="<?php echo $this->get_js_dir(); ?>/gallery.js"/>
<box orient="vertical">
<html:p>
<?php foreach ($this->get_parents() as $parent) { ?>
<html:a href="<?php echo $this->make_url($parent["path"]); ?>">
<?php 
   if ($parent["title"]) {
     echo uwc_format_text_to_xul($parent["title"]);
   } else {
     echo $this->translate_xul("no_title");
   }
?>
</html:a> &gt; 
<?php } ?>
<?php echo $this->translate_xul("gallery_title"); ?>
</html:p>
<box orient="horizontal">
<button
    id="uwc_gallery_prev"
    image="<?php echo $this->get_images_dir(); ?>/arrow_left.png"
    label="<?php echo $this->translate_xul("gallery_prev");?>" 
    onclick="uwc_gallery_prev_image();" />
<button
    id="uwc_gallery_next"
    image="<?php echo $this->get_images_dir(); ?>/arrow_right.png"
    label="<?php echo $this->translate_xul("gallery_next");?>"
    onclick="uwc_gallery_next_image();" />
</box>
<box orient="horizontal">
<?php 
$images_list=$this->get_images_list();
?>
<?php if (count($images_list)) { ?>
<tabbox id="uwc_gallery_tabbox">
  <tabs>
<?php $n=1; ?>
<?php foreach($images_list as $id => $image_data) { ?>
    <tab label="<?php echo $n; ?>" />
<?php   $n++; ?>
<?php } ?>
  </tabs>
  <tabpanels>
<?php foreach($images_list as $id => $image_data) { ?>
    <tabpanel orient="horizontal">
      <box orient="vertical">
        <box orient="horizontal" style='overflow: auto;'>
          <image src="<?php echo $this->make_url(uwc_image_make_full_url($this->get_up_path(),$id)); ?>" width="<?php echo $image_data["fullscaled_w"]; ?>" height="<?php echo $image_data["fullscaled_h"]; ?>" />
        </box>
        <html:p>
          <html:a href="<?php echo $this->make_url(uwc_image_make_page_url($this->get_up_path(),$id)); ?>"><?php echo uwc_format_text_to_xul($image_data["alt"]); ?></html:a>
        </html:p>
        <html:p>
          <?php echo uwc_format_text_to_xul($image_data["longdesc"]); ?>
        </html:p>
      </box>
    </tabpanel>
<?php } ?>
  </tabpanels>
</tabbox>
<?php } else { ?>
  <html:p>
    <html:a href="<?php echo $this->make_url($this->get_up_path()); ?>"><?php echo $this->translate_xul("gallery_empty"); ?></html:a>
  </html:p>
<?php } ?>
</box>
  <html:p>
    <?php echo uwc_format_html_to_xul($this->translate2("xul_generated_by_uwikicms", $this->conf->version,$this->today())); ?>
  </html:p>
</box>
</window>
