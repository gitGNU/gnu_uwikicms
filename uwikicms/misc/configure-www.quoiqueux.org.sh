#!/bin/sh

# UWiKiCMS is a lightweight web content management system.
# Copyright (C) 2005, 2006, 2007, 2013 Christian Mauduit <ufoot@ufoot.org>
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

# All the connexion parameters are in an external file
. ~/.passwd

for c in ./configure ../configure; do
	if [ -x $c ] ; then
		CMD=$c
	fi
done

$CMD \
--release \
--siteurl="http://www.quoiqueux.org" \
--htprefix="/infos" \
--dbprefix="" \
--dbhost="$QUOIQUEUX_ORG_DBHOST" \
--dbname="$QUOIQUEUX_ORG_DBNAME" \
--dbuser="$QUOIQUEUX_ORG_DBUSER" \
--dbpasswd="$QUOIQUEUX_ORG_DBPASSWD" \
--css_dir="/_uwikicms/template/css/quoiqueux"


