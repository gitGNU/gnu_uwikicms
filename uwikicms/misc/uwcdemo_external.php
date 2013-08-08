<?
/*
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

# Deletes database content on a regular basis

#define(UWCDEMO_DELETE_DELAY,43200); # 12 hours
define(UWCDEMO_DELETE_DELAY,300); # for tests
function uwcdemo_delete_test($filename,&$data) {
  $time=time();
  if ((!file_exists($filename)) || $time-filemtime($filename)>UWCDEMO_DELETE_DELAY) {
    $data->query("DELETE FROM uwikicms_content WHERE content_path LIKE '/test%%'");
    $data->query("DELETE FROM uwikicms_image");
    $data->query("DELETE FROM uwikicms_legend");
    if ($f=fopen($filename,"w")) {
      fwrite($f,sprintf("%d\n",$time));
      fclose($f);
    }
  }
}

//echo dirname(__FILE__);
uwcdemo_delete_test(dirname(__FILE__)."/delete.txt",$page->data);

?>
