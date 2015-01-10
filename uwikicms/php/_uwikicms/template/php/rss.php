<?php
/*
 UWiKiCMS is a lightweight web content management system.
 Copyright (C) 2005, 2006, 2007, 2009, 2013, 2015 Christian Mauduit <ufoot@ufoot.org>

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
uwc_rss_setlocale();
?>
<channel>
  <title><?php echo uwc_format_html_to_rss($this->get_title()); ?></title>
  <description><?php echo uwc_format_html_to_rss(sprintf($this->translate("rss_about"),$this->get_title())); ?></description>
  <lastBuildDate><?php echo date_format(date_create(), DateTime::RSS); ?></lastBuildDate>
  <link><?php echo $this->get_absolute_url_clean(); ?></link>
<?php if ($this->need_rss()) { 
   foreach ($this->get_rss() as $rss_item) { ?>
  <item>
     <title><?php echo uwc_format_escape_rss($rss_item["title"]); ?></title>
     <description><?php echo uwc_format_escape_rss($rss_item["text"]); ?> ...</description>
     <pubDate><?php echo uwc_rss_date_from_unix_timestamp($rss_item["date_update"]); ?></pubDate>
     <link><?php echo $this->make_absolute_url_clean($rss_item["path"]); ?></link>
  </item>
<?php } } ?>
</channel>
<?php
    echo "</rss>\n";
?>
