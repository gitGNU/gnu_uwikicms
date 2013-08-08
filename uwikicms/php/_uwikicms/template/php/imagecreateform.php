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
<form method="post" action="<? echo $this->get_imagecreate_url(); ?>" enctype="multipart/form-data">

<div id="edit" class="<? echo $this->get_status_style(); ?>">
<input type="hidden" name="path" value="<? echo $this->get_path(); ?>" />
<dl>
<dt><? echo $this->translate("alt"); ?></dt>
<dd><input type="text" size="<? echo $this->get_input_width1(); ?>" maxlength="255" name="alt" value="" class="edit" /> </dd>
<dt><? echo $this->translate("longdesc"); ?></dt>
<dd><textarea cols="<? echo $this->get_input_width1(); ?>" rows="<? echo $this->get_input_height1(); ?>" name="longdesc"></textarea></dd>
<dt><? echo $this->translate("imagefile"); ?></dt>
<dd><input type="file" size="<? echo $this->get_input_width1(); ?>" name="imagefile" value="" class="button" /> </dd>
</dl>
</div>

<div id="message" class="default">
<input type="submit" name="submit" value="<? echo $this->translate("create"); ?>" class="button" />
</div>

</form>


