<?php
/*
 UWiKiCMS is a lightweight web content management system.
 Copyright (C) 2005, 2006, 2007, 2009, 2013 Christian Mauduit <ufoot@ufoot.org>

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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php echo $this->get_lang(); ?>">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
    <meta http-equiv="Content-Language" content="<?php echo $this->get_lang(); ?>" />
    <title><?php echo $this->get_title(); ?></title>
    <meta name="Author" content="<?php echo $this->get_meta_author(); ?>" />
    <meta name="Description" content="<?php echo $this->get_meta_description(); ?>" />
    <meta name="Keywords" content="<?php echo $this->get_meta_keywords(); ?>" />
    <meta name="Copyright" content="<?php echo $this->get_meta_copyright(); ?>" />
    <meta name="Generator" content="UWiKiCMS" />
<?php if ($this->use_css()) { ?>
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->get_css_dir(); ?>/style.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->get_css_dir(); ?>/layout.css" />
    <link rel="stylesheet" type="text/css" media="print" href="<?php echo $this->get_css_dir(); ?>/print.css" />
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
<?php } ?>
  </head>
  <body>

    <div id="block1">

    <div id="title" class="default">
    <h1><?php echo $this->get_title(); ?></h1>
    </div>

    </div>

    <div id="block2">

    <div id="control" class="box menu">

    <?php if ($this->need_control_focus()) { ?>
    <div id="controlfocus"><?php echo $this->get_control_focus(); ?></div>
    <?php } ?>

    <?php if ($this->need_nav()) { ?>
    <div id="nav">
    <span class="textmode"> [ </span>

    <?php if ($this->need_home()) { ?>
    <a href="<?php echo $this->make_url($this->get_home_path()); ?>"><img src="<?php echo $this->get_images_dir(); ?>/home.png" width="<?php echo $this->get_arrow_width(); ?>" height="<?php echo $this->get_arrow_height(); ?>" alt="<?php echo sprintf($this->translate("home"),$this->get_home_title()); ?>" title="<?php echo $this->get_home_title(); ?>" class="msiehackbox" /></a>
    <?php } ?>

    <?php if ($this->need_prev()) { ?>
    <span class="textmode"> | </span><a href="<?php echo $this->make_url($this->get_prev_path()); ?>"><img src="<?php echo $this->get_images_dir(); ?>/arrow_left.png" width="<?php echo $this->get_arrow_width(); ?>" height="<?php echo $this->get_arrow_height(); ?>" alt="<?php echo sprintf($this->translate("prev"),$this->get_prev_title()); ?>" title="<?php echo $this->get_prev_title(); ?>" class="msiehackbox" /></a>
    <?php } ?>

    <?php if ($this->need_up()) { ?>
    <span class="textmode"> | </span><a href="<?php echo $this->make_url($this->get_up_path()); ?>"><img src="<?php echo $this->get_images_dir(); ?>/arrow_up.png" width="<?php echo $this->get_arrow_width(); ?>" height="<?php echo $this->get_arrow_height(); ?>" alt="<?php echo sprintf($this->translate("up"),$this->get_up_title()); ?>" title="<?php echo $this->get_up_title(); ?>" class="msiehackbox" /></a>
    <?php } ?>

    <?php if ($this->need_next()) { ?>
    <span class="textmode"> | </span><a href="<?php echo $this->make_url($this->get_next_path()); ?>"><img src="<?php echo $this->get_images_dir(); ?>/arrow_right.png" width="<?php echo $this->get_arrow_width(); ?>" height="<?php echo $this->get_arrow_height(); ?>" alt="<?php echo sprintf($this->translate("next"),$this->get_next_title()); ?>" title="<?php echo $this->get_next_title(); ?>" class="msiehackbox" /></a>
    <?php } ?>

    <?php if ($this->need_news()) { ?>
    <span class="textmode"> | </span><a href="<?php echo $this->get_rss_url(); ?>"><img src="<?php echo $this->get_images_dir(); ?>/rss.png" width="<?php echo $this->get_rss_width(); ?>" height="<?php echo $this->get_rss_height(); ?>" alt="<?php echo sprintf($this->translate("rss"),$this->get_title()); ?>" title="<?php echo sprintf($this->translate("rss_about"),$this->get_title()); ?>" class="msiehackbox" /></a>
    <?php } ?>

    <span class="textmode"> ] </span>
    </div>
    <?php } ?>

    <?php $langs=$this->get_translated();
if (count($langs)>0) { 
  $first=true; ?>    
    <div id="langs">
    <span class="textmode"> [ </span>
    <?php foreach ($langs as $lang) {
       if (!$first) { ?>
      <span class="textmode"> | </span>
    <?php }
      $first=false; ?>
      <a href="<?php echo $this->get_translate_url($lang); ?>"><img src="<?php echo $this->get_images_dir()."/flag_".$lang.".png"; ?>" width="<?php echo $this->get_lang_width(); ?>" height="<?php echo $this->get_lang_height(); ?>" alt="<?php echo $this->translate("view_lang_".$lang); ?>" title="<?php echo $this->translate("view_lang_".$lang); ?>" class="msiehackbox" /></a>
    <?php } ?>
    <span class="textmode"> ] </span>
    </div>
    <?php } ?>

    <?php if ($this->need_parents()) { ?>
    <div id="parents">
    <?php foreach ($this->get_parents() as $parent) { ?>
    <a href="<?php echo $this->make_url($parent["path"]); ?>">
<?php 
   if ($parent["title"]) {
     echo uwc_format_text_to_html($parent["title"]);
   } else {
     echo $this->translate("no_title");
   }
?>
    </a> &gt; 
    <?php } ?>
    <?php echo $this->get_title(); ?>
    </div>
    <?php } ?>

    <?php if ($this->need_children()) {?>
    <div id="children">
    <ul>
    <?php foreach ($this->get_children() as $child) { ?>
      <li> <a href="<?php echo $this->make_url($child["path"]);?>">
<?php 
   if ($child["title"]) {
     echo uwc_format_text_to_html($child["title"]);
   } else {
     echo $this->translate("no_title");
   }
?>
       </a></li>
    <?php } ?>
    </ul>
    </div>
    <?php } ?>

    <?php if ($this->need_news()) {?>
    <div id="news">
    <ul>
    <?php foreach ($this->get_news() as $news) { ?>
      <li> <a href="<?php echo $this->make_url($news["path"]);?>">
<?php 
   if ($news["title"]) {
     echo uwc_format_text_to_html($news["title"]);
   } else {
     echo $this->translate("no_title");
   }
?>
       </a></li>
    <?php } ?>
    </ul>
    </div>
    <?php } ?>

    <?php if ($this->need_google()) {?>
    <div id="google">
       <form action="http://www.google.fr/search" method="get" id="google_form">
       <p>
	    <input type="hidden" id="google_ie" name="ie" value="latin1" />
	    <input type="hidden" id="google_as_dt" name="as_dt" value="i" />
	    <input type="hidden" id="google_as_sitesearch" name="as_sitesearch" value="<?php echo $this->get_domain_name(); ?>" />
	    <input type="text" size="16" maxlength="192" id="google_q" name="q" />
	    <input type="submit" id="google_search" name="search" value="<?php echo $this->translate("google_search"); ?>" />
       </p>
        </form>
    </div> 
    <?php } ?>

    </div>

    </div>

    <div id="block3">