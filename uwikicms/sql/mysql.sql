-- UWiKiCMS is a lightweight web content management system.
-- Copyright (C) 2005, 2006, 2007, 2009, 2013 Christian Mauduit <ufoot@ufoot.org>
--
-- This program is free software; you can redistribute it and/or
-- modify it under the terms of the GNU General Public License as
-- published by the Free Software Foundation; either version 2 of
-- the License, or (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public
-- License along with this program; if not, write to the Free
-- Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston,
-- MA  02110-1301  USA

-- This script must be ran manually on MySQL to create all the tables

DROP TABLE IF EXISTS uwikicms_content;
CREATE TABLE uwikicms_content (
	content_path VARCHAR(255) NOT NULL,
	content_lang CHAR(2) NOT NULL,
	content_title VARCHAR(255) NOT NULL,
	content_author VARCHAR(15) NOT NULL,
	content_date_create DATETIME NOT NULL,
	content_date_update DATETIME NOT NULL,
	content_text LONGTEXT NOT NULL,
	content_status INT NOT NULL DEFAULT 3,
	content_order INT NOT NULL,
	PRIMARY KEY (content_path, content_lang)
);

CREATE INDEX uwikicms_content_idx_path ON uwikicms_content (content_path);

CREATE INDEX uwikicms_content_idx_lang ON uwikicms_content (content_lang);

CREATE INDEX uwikicms_content_idx_status ON uwikicms_content (content_status);

CREATE INDEX uwikicms_content_idx_order ON uwikicms_content (content_order);

CREATE INDEX uwikicms_content_idx_path_status ON uwikicms_content (content_path,content_status);

DROP TABLE IF EXISTS uwikicms_user;
CREATE TABLE uwikicms_user (
	user_id VARCHAR(15) NOT NULL,
	user_passwd CHAR(32) NOT NULL,
	user_label VARCHAR(255) NOT NULL,
	user_copyright_holder VARCHAR(255) NOT NULL,
	user_email VARCHAR(255) NOT NULL,
	user_status INT NOT NULL DEFAULT 0,
	user_domain_regex VARCHAR(255) NOT NULL DEFAULT '.*',
	PRIMARY KEY (user_id)
);

-- You'll also need to create an administration user, with commands
-- like:
INSERT INTO uwikicms_user (user_id,user_passwd,user_label,user_copyright_holder,user_email,user_status,user_domain_regex) VALUES ('admin',MD5('admin'),'Administrator','X','admin@mysite.com',3,'.*');
INSERT INTO uwikicms_user (user_id,user_passwd,user_label,user_copyright_holder,user_email,user_status,user_domain_regex) VALUES ('contrib',MD5('contrib'),'Contributor','Y','contrib@mysite.com',2,'^\\/contrib');
INSERT INTO uwikicms_user (user_id,user_passwd,user_label,user_copyright_holder,user_email,user_status,user_domain_regex) VALUES ('visit',MD5('visit'),'Visitor','Nobody','',1,'.*');
-- Note that you'll need to change these defaults to suit your needs.
-- The "status" field has the following meaning:
-- 0: no privilege (visitor)
-- 1: identified, user can read all status 0 and 1 documents
-- 2: contributor, user can read and create status 0,1 and 2 documents
-- 3: administrator

DROP TABLE IF EXISTS uwikicms_image;
CREATE TABLE uwikicms_image (
	image_id INT NOT NULL AUTO_INCREMENT,
	image_container_path VARCHAR(255) NOT NULL,
	image_preview BLOB NOT NULL,
	image_preview_w INT NOT NULL,
	image_preview_h INT NOT NULL,
	image_full LONGBLOB NOT NULL,
	image_full_w INT NOT NULL,
	image_full_h INT NOT NULL,
	image_filename VARCHAR(63) NOT NULL,
	PRIMARY KEY (image_id)
);

CREATE INDEX uwikicms_image_idx_container_path ON uwikicms_image (image_container_path);

DROP TABLE IF EXISTS uwikicms_legend;
CREATE TABLE uwikicms_legend (
	legend_image_id INT NOT NULL AUTO_INCREMENT,
	legend_lang CHAR(2) NOT NULL,
	legend_alt VARCHAR(255) NOT NULL,
	legend_longdesc TEXT NOT NULL,
	PRIMARY KEY (legend_image_id,legend_lang)
);

CREATE INDEX uwikicms_legend_idx_image_id ON uwikicms_legend (legend_image_id);

CREATE INDEX uwikicms_legend_idx_lang ON uwikicms_legend (legend_lang);


