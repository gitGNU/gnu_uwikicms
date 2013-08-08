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
<form method="post" action="<? echo $this->get_update_url(); ?>">

<div id="edit" class="<? echo $this->get_status_style(); ?>">
<input type="hidden" name="oldpath" value="<? echo $this->get_path(); ?>" />

<dl>
<dt><? echo $this->translate("title"); ?></dt>
<dd><input type="text" size="<? echo $this->get_input_width1(); ?>" maxlength="255" name="title" value="<? echo $this->get_title_sql(); ?>" class="edit" /></dd>
<dt><? echo $this->translate("text"); ?></dt>
<dd><textarea cols="<? echo $this->get_input_width2(); ?>" rows="<? echo $this->get_input_height2(); ?>" name="text"><? echo $this->get_text_unformatted(); ?></textarea></dd>
<dt><? echo $this->translate("status"); ?></dt>
<dd><select name="status">
<? for ($i=0;$i<=$this->get_user_status();++$i) {
   if ($this->get_status()==$i) {
     ?><option value="<? echo $i; ?>" selected="selected"><? echo $this->translate("status_".$i); ?></option>
     <? } else {
     ?><option value="<? echo $i; ?>"><? echo $this->translate("status_".$i); ?></option>
     <? } 
   } ?>
</select></dd>
<dt><? echo $this->translate("path"); ?></dt>
<dd><input type="text" size="<? echo $this->get_input_width1(); ?>" maxlength="255" name="path" value="<? echo $this->get_path(); ?>" class="edit" /></dd>
<dt><? echo $this->translate("order"); ?></dt>
<dd><input type="text" size="5" maxlength="10" name="order" value="<? echo $this->get_order(); ?>" class="edit" /></dd>
<? foreach($this->get_images_list() as $id => $image_data) { ?>
    <dt>
      [&nbsp;img:<? echo $id; ?>&nbsp;] 
    </dt>
    <dd>
       <div class="image">
       <a href="<? echo $this->make_url(uwc_image_make_page_url($this->get_path(),$id)); ?>"><img src="<? echo $this->make_url(uwc_image_make_preview_url($this->get_path(),$id)); ?>" width="<? echo $image_data["preview_w"]; ?>" height="<? echo $image_data["preview_h"]; ?>" alt="<? echo uwc_format_text_to_html($image_data["alt"]); ?>" title="<? echo $this->translate4("filename_width_height_size",$image_data["filename"],$image_data["full_w"],$image_data["full_h"],uwc_format_readable_size($image_data["size"])); ?>" class="msiehackimage" /></a>       
       </div>
    </dd>
<? } ?>
</dl>
</div>

<div id="message" class="default">
<input type="submit" name="submit" value="<? echo $this->translate("update"); ?>" class="button" />
</div>

</form>


