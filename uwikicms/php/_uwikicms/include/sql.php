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

class UWC_Sql {
  var $query=Array(
		   "select_user_by_id"=>"SELECT user_id, user_label, user_copyright_holder, user_email, user_status, user_domain_regex FROM uwikicms_user WHERE user_id='%s'",
		   "select_user_by_id_and_passwd"=>"SELECT user_id, user_label, user_copyright_holder, user_email, user_status, user_domain_regex FROM uwikicms_user WHERE user_id='%s' AND user_passwd=MD5('%s')",
		   "insert_content"=>"INSERT INTO uwikicms_content SET content_path='%s', content_lang='%s', content_title='%s', content_author='%s', content_date_create=NOW(), content_date_update=NOW(), content_text='%s', content_status=%d, content_order=%d",
		   "update_content"=>"UPDATE uwikicms_content SET content_title='%s', content_author='%s', content_date_update=NOW(), content_text='%s', content_status=%d, content_order=%d WHERE content_path='%s' AND content_lang='%s'",
		   "update_content_without_date_update"=>"UPDATE uwikicms_content SET content_title='%s', content_author='%s', content_text='%s', content_status=%d, content_order=%d WHERE content_path='%s' AND content_lang='%s'",
		   "delete_content_by_path_and_lang"=>"DELETE FROM uwikicms_content WHERE content_path='%s' AND content_lang='%s'",
		   "select_content_by_path_and_lang"=>"SELECT content_title, UNIX_TIMESTAMP(content_date_create) AS content_date_create, UNIX_TIMESTAMP(content_date_update) AS content_date_update, content_text, content_status, content_order, user_label, user_copyright_holder, user_email FROM uwikicms_content, uwikicms_user WHERE content_path='%s' AND content_lang='%s' AND content_author=user_id",
		   "select_content_lang_by_path"=>"SELECT content_lang FROM uwikicms_content WHERE content_path='%s'",
		   "select_content_status_by_path_and_lang"=>"SELECT content_status FROM uwikicms_content WHERE content_path='%s' AND content_lang='%s'",
		   "select_content_next_order"=>"SELECT MAX(content_order)+10 AS content_next_order FROM uwikicms_content",
		   "select_content_children_by_path_lang_and_status"=>"SELECT SUBSTRING(content_path,%d) AS content_path, content_title FROM uwikicms_content WHERE uwikicms_content.content_path LIKE '%s/%%' AND content_lang='%s' AND content_status<=%d AND INSTR(SUBSTRING(uwikicms_content.content_path,%d),'/')=0 ORDER BY content_order",
		   "select_content_children_by_path_lang_status_and_domain_regex"=>"SELECT SUBSTRING(content_path,%d) AS content_path, content_title FROM uwikicms_content WHERE uwikicms_content.content_path LIKE '%s/%%' AND content_lang='%s' AND (content_status<%d OR uwikicms_content.content_path REGEXP '%s') AND INSTR(SUBSTRING(uwikicms_content.content_path,%d),'/')=0 ORDER BY content_order",
		   "select_content_news_by_path_lang_and_status"=>"SELECT SUBSTRING(content_path,%d) AS content_path, content_title FROM uwikicms_content WHERE uwikicms_content.content_path LIKE '%s/%%' AND content_lang='%s' AND content_status<=%d AND content_date_update > NOW() - INTERVAL %d day ORDER BY content_date_update DESC LIMIT %d",
		   "select_content_news_by_path_lang_status_and_domain_regex"=>"SELECT SUBSTRING(content_path,%d) AS content_path, content_title FROM uwikicms_content WHERE uwikicms_content.content_path LIKE '%s/%%' AND content_lang='%s' AND (content_status<%d OR uwikicms_content.content_path REGEXP '%s') AND content_date_update > NOW() - INTERVAL %d day ORDER BY content_date_update DESC LIMIT %d",
		   "select_content_rss_by_path_and_lang"=>"SELECT SUBSTRING(content_path,%d) AS content_path, content_title, SUBSTRING(content_text,1,%d) AS content_text, UNIX_TIMESTAMP(content_date_update) AS content_date_update FROM uwikicms_content WHERE uwikicms_content.content_path LIKE '%s/%%' AND content_lang='%s' AND content_status<=0 AND content_date_update > NOW() - INTERVAL %d day ORDER BY content_date_update DESC LIMIT %d",
		   "select_content_tree_by_path_lang_and_status"=>"SELECT SUBSTRING(content_path,%d) AS content_path, content_title, content_order, content_status FROM uwikicms_content WHERE uwikicms_content.content_path LIKE '%s/%%' AND content_lang='%s' AND content_status<=%d ORDER BY uwikicms_content.content_path",
		   "select_content_tree_by_path_lang_status_and_domain_regex"=>"SELECT SUBSTRING(content_path,%d) AS content_path, content_title, content_order, content_status FROM uwikicms_content WHERE uwikicms_content.content_path LIKE '%s/%%' AND content_lang='%s' AND (content_status<%d OR uwikicms_content.content_path REGEXP '%s') ORDER BY uwikicms_content.content_path",
		   "select_content_title_by_path_lang_and_status"=>"SELECT content_title FROM uwikicms_content WHERE content_path='%s' AND content_lang='%s' AND content_status<=%d",
		   "select_content_title_by_path_lang_status_and_domain_regex"=>"SELECT content_title FROM uwikicms_content WHERE content_path='%s' AND content_lang='%s' AND (content_status<%d OR uwikicms_content.content_path REGEXP '%s')",
		   "select_content_next_by_path_lang_and_status"=>"SELECT SUBSTRING(content_path,%d) AS content_path, content_title FROM uwikicms_content WHERE uwikicms_content.content_path LIKE '%s/%%' AND content_lang='%s' AND content_status<=%d AND INSTR(SUBSTRING(uwikicms_content.content_path,%d),'/')=0 AND content_order>%s ORDER BY content_order LIMIT 1",
		   "select_content_next_by_path_lang_status_and_domain_regex"=>"SELECT SUBSTRING(content_path,%d) AS content_path, content_title FROM uwikicms_content WHERE uwikicms_content.content_path LIKE '%s/%%' AND content_lang='%s' AND (content_status<%d OR uwikicms_content.content_path REGEXP '%s') AND INSTR(SUBSTRING(uwikicms_content.content_path,%d),'/')=0 AND content_order>%s ORDER BY content_order LIMIT 1",
		   "select_content_prev_by_path_lang_and_status"=>"SELECT SUBSTRING(content_path,%d) AS content_path, content_title FROM uwikicms_content WHERE uwikicms_content.content_path LIKE '%s/%%' AND content_lang='%s' AND content_status<=%d AND INSTR(SUBSTRING(uwikicms_content.content_path,%d),'/')=0 AND content_order<%s ORDER BY content_order DESC LIMIT 1",
		   "select_content_prev_by_path_lang_status_and_domain_regex"=>"SELECT SUBSTRING(content_path,%d) AS content_path, content_title FROM uwikicms_content WHERE uwikicms_content.content_path LIKE '%s/%%' AND content_lang='%s' AND (content_status<%d OR uwikicms_content.content_path REGEXP '%s') AND INSTR(SUBSTRING(uwikicms_content.content_path,%d),'/')=0 AND content_order<%s ORDER BY content_order DESC LIMIT 1",
		   "select_image_next_id"=>"SELECT MAX(image_id)+1 AS image_id FROM uwikicms_image",
		   "insert_image"=>"INSERT INTO uwikicms_image SET image_id=%d, image_container_path='%s', image_preview='%s', image_preview_w=%d, image_preview_h=%d, image_full='%s', image_full_w=%d, image_full_h=%d, image_filename='%s'",
		   "insert_legend"=>"INSERT INTO uwikicms_legend SET legend_image_id=%d, legend_lang='%s', legend_alt='%s', legend_longdesc='%s'",
		   "select_legend_lang_by_id"=>"SELECT legend_lang FROM uwikicms_legend WHERE legend_image_id=%d",
		   "select_image_full_by_id"=>"SELECT image_full FROM uwikicms_image WHERE image_id=%d",
		   "select_image_preview_by_id"=>"SELECT image_preview FROM uwikicms_image WHERE image_id=%d",
		   "select_image_status_by_id"=>"SELECT MIN(content_status) AS image_status FROM uwikicms_content, uwikicms_image WHERE content_path=image_container_path AND image_id=%d",
		   "select_image_by_id"=>"SELECT image_container_path, image_preview_w, image_preview_h, image_full_w, image_full_h, LENGTH(image_full) AS image_size, image_filename FROM uwikicms_image WHERE image_id=%d",
		   "select_legend_by_id_and_lang"=>"SELECT legend_alt, legend_longdesc FROM uwikicms_legend WHERE legend_image_id=%d AND legend_lang='%s'",
		   "select_image_by_container_path"=>"SELECT image_id, image_preview_w, image_preview_h, image_full_w, image_full_h, LENGTH(image_full) AS image_size, image_filename FROM uwikicms_image WHERE image_container_path='%s'",
		   "select_legend_by_container_path_and_lang"=>"SELECT legend_image_id, legend_alt, legend_longdesc FROM uwikicms_legend, uwikicms_image WHERE image_id=legend_image_id AND image_container_path='%s' AND legend_lang='%s'",
		   "select_legend_next_by_container_path_lang_and_id"=>"SELECT image_id, legend_alt FROM uwikicms_legend, uwikicms_image WHERE image_id=legend_image_id AND image_container_path='%s' AND legend_lang='%s' AND legend_image_id>%d ORDER by legend_image_id LIMIT 1",
		   "select_legend_prev_by_container_path_lang_and_id"=>"SELECT image_id, legend_alt FROM uwikicms_legend, uwikicms_image WHERE image_id=legend_image_id AND image_container_path='%s' AND legend_lang='%s' AND legend_image_id<%d ORDER by legend_image_id DESC LIMIT 1",
		   "update_image"=>"UPDATE uwikicms_image SET image_preview='%s', image_preview_w=%d, image_preview_h=%d, image_full='%s', image_full_w=%d, image_full_h=%d, image_filename='%s' WHERE image_id=%d",
		   "update_legend"=>"UPDATE uwikicms_legend SET legend_alt='%s', legend_longdesc='%s' WHERE legend_image_id=%d AND legend_lang='%s'",
		   "delete_image_by_id"=>"DELETE FROM uwikicms_image WHERE image_id=%d",
		   "delete_legend_by_image_id"=>"DELETE FROM uwikicms_legend WHERE legend_image_id=%d",
		   "move_content"=>"UPDATE uwikicms_content SET content_path=CONCAT('%s',SUBSTRING(content_path,%d)) WHERE content_path LIKE '%s%%'",
		   "move_image"=>"UPDATE uwikicms_image SET image_container_path=CONCAT('%s',SUBSTRING(image_container_path,%d)) WHERE image_container_path LIKE '%s%%'",
		   "select_content_for_renum"=>"SELECT content_path, content_lang, content_order FROM uwikicms_content ORDER BY content_order, content_path, content_lang",
		   "update_content_for_renum"=>"UPDATE uwikicms_content SET content_order=%d WHERE content_path='%s' AND content_lang='%s'",

		   ""=>""
		   );
}
?>
