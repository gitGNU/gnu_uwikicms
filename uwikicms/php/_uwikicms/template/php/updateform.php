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
?>
<form method="post" action="<?php echo $this->get_update_url(); ?>">

<div id="edit" class="<?php echo $this->get_status_style(); ?>">
<input type="hidden" name="oldpath" value="<?php echo $this->get_path(); ?>" />

<dl>
<dt><?php echo $this->translate("title"); ?></dt>
<dd><input type="text" size="<?php echo $this->get_input_width1(); ?>" maxlength="255" name="title" value="<?php echo $this->get_title_sql(); ?>" class="edit" /></dd>
<dt><?php echo $this->translate("text"); ?></dt>
<dd><textarea cols="<?php echo $this->get_input_width2(); ?>" rows="<?php echo $this->get_input_height2(); ?>" name="text"><?php echo $this->get_text_unformatted(); ?></textarea></dd>
<dt><?php echo $this->translate("status"); ?></dt>
<dd><select name="status">
<?php for ($i=0;$i<=$this->get_user_status();++$i) {
   if ($this->get_status()==$i) {
     ?><option value="<?php echo $i; ?>" selected="selected"><?php echo $this->translate("status_".$i); ?></option>
     <?php } else {
     ?><option value="<?php echo $i; ?>"><?php echo $this->translate("status_".$i); ?></option>
     <?php } 
   } ?>
</select></dd>
<dt><?php echo $this->translate("path"); ?></dt>
<dd><input type="text" size="<?php echo $this->get_input_width1(); ?>" maxlength="255" name="path" value="<?php echo $this->get_path(); ?>" class="edit" /></dd>
<dt><?php echo $this->translate("order"); ?></dt>
<dd><input type="text" size="5" maxlength="10" name="order" value="<?php echo $this->get_order(); ?>" class="edit" /></dd>
<dt><?php echo $this->translate("do_date_update"); ?></dt>
<dd><input type="checkbox" name="do_date_update" value="1" checked="checked" class="edit" /></dd>
<?php foreach($this->get_images_list() as $id => $image_data) { ?>
    <dt>
      [&nbsp;img:<?php echo $id; ?>&nbsp;] 
    </dt>
    <dd>
       <div class="image">
       <a href="<?php echo $this->make_url(uwc_image_make_page_url($this->get_path(),$id)); ?>"><img src="<?php echo $this->make_url(uwc_image_make_preview_url($this->get_path(),$id)); ?>" width="<?php echo $image_data["preview_w"]; ?>" height="<?php echo $image_data["preview_h"]; ?>" alt="<?php echo uwc_format_text_to_html($image_data["alt"]); ?>" title="<?php echo $this->translate4("filename_width_height_size",$image_data["filename"],$image_data["full_w"],$image_data["full_h"],uwc_format_readable_size($image_data["size"])); ?>" class="msiehackimage" /></a>       
       </div>
    </dd>
<?php } ?>
</dl>
</div>

<div id="message" class="default">
<input type="submit" name="submit" value="<?php echo $this->translate("update"); ?>" class="button" />
</div>

</form>


