<?php
/*
 UWiKiCMS is a lightweight web content management system.
 Copyright (C) 2005, 2006, 2007, 2009, 2013, 2015, 2016 Christian Mauduit <ufoot@ufoot.org>

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
<form method="post" action="<?php echo $this->get_imagecreate_url(); ?>" enctype="multipart/form-data">

<div id="edit" class="<?php echo $this->get_status_style(); ?>">
<input type="hidden" name="path" value="<?php echo $this->get_path(); ?>" />
<dl>
<dt><?php echo $this->translate("alt"); ?></dt>
<dd><input type="text" size="<?php echo $this->get_input_width1(); ?>" maxlength="255" name="alt" value="" class="edit" /> </dd>
<dt><?php echo $this->translate("longdesc"); ?></dt>
<dd><textarea cols="<?php echo $this->get_input_width1(); ?>" rows="<?php echo $this->get_input_height1(); ?>" name="longdesc"></textarea></dd>
<dt><?php echo $this->translate("imagefile"); ?></dt>
<dd><input type="file" size="<?php echo $this->get_input_width1(); ?>" name="imagefile" value="" class="button" /> </dd>
</dl>
</div>

<div id="message" class="default">
<input type="submit" name="submit" value="<?php echo $this->translate("create"); ?>" class="button" />
</div>

</form>


