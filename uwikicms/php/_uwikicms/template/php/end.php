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
  </div>

  <div id="block4">
  <div id="actions" class="box admin">

    <?php if ($this->need_actions_focus()) { ?>
    <div id="actionsfocus"><?php echo $this->get_actions_focus(); ?></div>
    <?php } ?>

  <div id="actionslist">
  <ul>
  <?php if ($this->need_action_help()) { ?>
     <li><a href="<?php echo $this->get_help_url(); ?>"><?php echo $this->translate("action_help"); ?></a></li>
  <?php } ?>
  <?php if ($this->need_action_root()) { ?>     
    <li><a href="<?php echo $this->make_url(""); ?>"><?php echo $this->translate("action_root"); ?></a></li>
<?php } ?>	
    <li>
  <?php if ($this->get_user_id()) { ?>
  <a href="<?php echo $this->get_logout_url(); ?>"><?php
       echo sprintf($this->translate("action_logout"), $this->get_user_label()); 
       ?></a>
<?php
   } else {
     ?><a href="<?php echo $this->get_loginform_url(); ?>"><?php
       echo $this->translate("action_login");
       ?></a>
<?php } ?>
</li>
<?php if ($this->need_action_view()) { ?>
     <li><a href="<?php echo $this->get_view_url(); ?>"><?php echo $this->translate("action_view"); ?></a></li>
<?php } ?>
<?php if ($this->need_action_rss()) { ?>
     <li><a href="<?php echo $this->get_rss_url(); ?>"><?php echo $this->translate("action_rss"); ?></a></li>
<?php } ?>
<?php if ($this->need_action_tree()) { ?>
     <li><a href="<?php echo $this->get_tree_url(); ?>"><?php echo $this->translate("action_tree"); ?></a></li>
<?php } ?>
<?php if ($this->need_action_css_yes()) { ?>
     <li><a href="<?php echo $this->get_css_yes_url(); ?>"><?php echo $this->translate("action_css_yes"); ?></a></li>
<?php } ?>
<?php if ($this->need_action_css_no()) { ?>
     <li><a href="<?php echo $this->get_css_no_url(); ?>"><?php echo $this->translate("action_css_no"); ?></a></li>
<?php } ?>
<?php if ($this->need_action_gallery()) { ?>
     <li><a href="<?php echo $this->get_gallery_url(); ?>"><?php echo $this->translate("action_gallery"); ?></a></li>
<?php } ?>
<?php if ($this->need_action_allinone()) { ?>
     <li><a href="<?php echo $this->get_allinoneform_url(); ?>"><?php echo $this->translate("action_allinone"); ?></a></li>
<?php } ?>
<?php if ($this->need_action_update()) { ?>
     <li><a href="<?php echo $this->get_updateform_url(); ?>"><?php echo $this->translate("action_update"); ?></a></li>
<?php } ?>
<?php if ($this->need_action_imagenew()) { ?>
     <li><a href="<?php echo $this->get_imagenew_url(); ?>"><?php echo $this->translate("action_imagenew"); ?></a></li>
<?php } ?>
<?php if ($this->need_action_delete()) { ?>
     <li><a href="<?php echo $this->get_deleteform_url(); ?>"><?php echo $this->translate("action_delete"); ?></a></li>
<?php } ?>
<?php if ($this->need_action_translate()) { ?>
       <li><?php echo $this->translate("translate");?>
       <?php $first=true; ?>
     <?php foreach ($this->get_untranslated() as $lang) { ?>
       <?php if (!$first) { echo " | "; } $first=false; ?>
<a href="<?php echo $this->get_translate_url($lang); ?>"><?php echo $lang; ?></a>
<?php } ?>
       </li>
<?php } ?>
<?php if ($this->need_action_clearcache()) { ?>
     <li><a href="<?php echo $this->get_clearcacheform_url(); ?>"><?php echo $this->translate("action_clearcache"); ?></a></li>
<?php } ?>
  </ul>
<?php if ($this->need_action_new()) { ?>
  <form method="post" action="<?php echo $this->get_new_url(); ?>">
  <input type="hidden" name="path" value="<?php echo $this->get_path(); ?>" />
  <input type="text" name="page" size="8" maxlength="32" value="" class="edit" />
  <input type="submit" name="submit" value="<?php echo $this->translate("action_new"); ?>" class="button" />
  </form>
<?php } ?>
  </div>
  </div>

  </div>

  <div id="block5">

  <div id="infos" class="box system">
    <div id="credits">
    <?php echo $this->translate2("page_generated_by_uwikicms", $this->conf->version,$this->today()); ?>
    </div>
    <?php if ($this->need_rights()) { ?>
    <div id="rights">
    <?php echo $this->get_rights(); ?>
    </div>
    <?php } ?>
    <?php if ($this->need_last_update()) { ?>
    <div id="lastupdate">
    <?php echo $this->get_last_update(); ?>
    </div>
    <?php } ?>
    <?php if ($this->need_absolute_url()) { ?>
    <div id="absoluteurl">
      <?php echo $this->translate("source"); ?>
      <a href="<?php echo $this->get_absolute_url(); ?>"><?php echo $this->get_absolute_url_clean(); ?></a>
    </div>
    <?php } ?>
  </div>

  </div>

  </body>
</html>
