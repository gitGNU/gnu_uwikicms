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
<?php
echo "<?xml version=\"1.0\" encoding=\"iso-8859-15\"?>\n";
echo "<rss version=\"2.0\">\n";
?>
<channel>
  <title><?php echo $this->get_title(); ?></title>
  <description><?php echo sprintf($this->translate("rss_about"),$this->get_title()); ?></description>
  <lastBuildDate><?php echo date_format(date_create(), DateTime::RSS); ?></lastBuildDate>
  <link><?php echo $this->get_path(); ?></link>
<?php if ($this->need_news()) { 
   foreach ($this->get_news() as $news) { ?>
  <item>
       <title><?php echo uwc_format_text_to_html($news["title"]); ?></title>
     <description>...</description>
     <pubDate>...</pubDate>
     <link>...</link>
  </item>
<?php } } ?>
</channel>
<?php
    echo "</rss>\n";
?>
