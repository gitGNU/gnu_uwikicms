#!/bin/sh

# UWiKiCMS is a lightweight web content management system.
# Copyright (C) 2005, 2006, 2007 Christian Mauduit <ufoot@ufoot.org>
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License as
# published by the Free Software Foundation; either version 2 of
# the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public
# License along with this program; if not, write to the Free
# Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston,
# MA  02110-1301  USA

# This is a script I use to install and test the software
# locally in my dev environment.

rm-temp-files.sh

for d in ./php ../php; do
	if [ -d $d ] ; then
		SRC=$d
	fi
done
DST="/var/www/test/uwikicms"

rsync --archive --delete --delete-excluded --exclude="*.arch-ids*" --exclude="*{arch}*" --exclude="*CVS*" --exclude="*-dist" $SRC/* $DST
rsync --archive $SRC/.htaccess $DST
find $DST -type d -exec chmod 755 "{}" \;
find $DST -type f -exec chmod 644 "{}" \;

chown -R ufoot:www-data $DST
find $DST -type f -exec chmod ug+rw "{}" \;
find $DST -type d -exec chmod ug+rwx "{}" \;

