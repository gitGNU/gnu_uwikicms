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
<div id="message" class="default">
<div>
<?php echo $this->translate("cache_cleared"); ?>.
</div>

<?php if ($this->need_continue_url()) { ?>
<div>
     <a href="<?php echo $this->make_url($this->get_continue_url()); ?>"><?php echo $this->translate("continue"); ?></a>
</div>
<?php } ?>
</div>