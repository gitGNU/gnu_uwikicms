<?
/*
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

/*
 * Prepares SQL queries from a .uwc file containing a page content.
 */

function sql4uwc_make_query($path,$lang,$file) {
  $content=file_get_contents($file);
  $query=sprintf("UPDATE uwikicms_content SET content_text='%s' WHERE content_path='%s' AND content_lang='%s'",
		 addslashes($content),
		 addslashes($path),
		 addslashes($lang));
  
  return $query;
}

function sql4uwc_syntax() {
?>
syntax: php sql4uwc.php <path> <lang> <file>
<?
}

if ($HTTP_SERVER_VARS['argv'][3]) {
  echo sql4uwc_make_query($HTTP_SERVER_VARS['argv'][1],
			  $HTTP_SERVER_VARS['argv'][2],
			  $HTTP_SERVER_VARS['argv'][3]);
} else {
  sql4uwc_syntax();
}

?>
