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
<form method="post" action="<? echo $this->get_imagedelete_url(); ?>">

<input type="hidden" name="path" value="<? echo $this->get_path(); ?>" />
<input type="hidden" name="alt" value="<? echo $this->get_legend_alt(); ?>" /> 

<div id="message" class="default">
<div>
<? echo $this->translate("confirm_delete_image"); ?>
<div>

<div>
<input type="submit" name="submit" value="<? echo $this->translate("delete"); ?>" class="button" />
</div>
</div>

</form>

<div id="content" class="<? echo $this->get_status_style(); ?>">

<div class="image">
<a href="<? echo $this->get_image_full_url(); ?>"><img src="<? echo $this->get_image_preview_url(); ?>" width="<? echo $this->get_image_preview_w(); ?>" height="<? echo $this->get_image_preview_h(); ?>" alt="<? echo $this->get_legend_alt(); ?>" title="<? echo $this->translate4("filename_width_height_size",$this->get_image_filename(),$this->get_image_full_w(),$this->get_image_full_h(),$this->get_image_size()); ?>" class="msiehackimage" /></a>
</div>

<div>
<? echo $this->get_legend_longdesc(); ?>
</div>

</div>

