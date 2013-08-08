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
<form method="post" action="<? echo $this->get_allinone_url(); ?>">

<input type="hidden" name="path" value="<? echo $this->get_path(); ?>" />

<div id="message" class="default">
<div>
<? echo $this->translate("confirm_all_in_one"); ?>
</div>

<div>
<dl>
<dt><? echo $this->translate("allinone_status"); ?></dt>
<dd>
<select name="status">
<? for ($i=0;$i<=$this->get_user_status();++$i) {
   if ($this->get_status()==$i) {
     ?><option value="<? echo $i; ?>" selected="selected"><? echo $this->translate("status_".$i); ?></option>
     <? } else {
     ?><option value="<? echo $i; ?>"><? echo $this->translate("status_".$i); ?></option>
     <? } 
   } ?>
</select>
</dd>
<dt><? echo $this->translate("allinone_intro"); ?></dt>
<dd><textarea cols="<? echo $this->get_input_width1(); ?>" rows="<? echo $this->get_input_height1(); ?>" name="intro"><? echo $this->get_allinone_default_intro(); ?></textarea></dd>
<dt><? echo $this->translate("allinone_max_size"); ?></dt>
<dd><input type="edit" name="max_size" size="5" maxlength="5" value="<? echo UWC_CONTEXT_ALLINONE_MAX_SIZE; ?>" class="edit" /></dd>
</dl>
</div>

<div>
<input type="submit" name="submit" value="<? echo $this->translate("all_in_one"); ?>" class="button" />
</div>
</div>

</form>

