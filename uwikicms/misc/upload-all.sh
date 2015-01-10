#!/bin/sh

# UWiKiCMS is a lightweight web content management system.
# Copyright (C) 2005, 2006, 2007, 2009, 2013, 2015 Christian Mauduit <ufoot@ufoot.org>
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

. ~/.passwd

DST=/var/www/test/uwikicms/_uwikicms
rm -rf $DST
CWD=`dirname $0`

for site in blog ufo valerie vap www; do
    echo "Uploading "$site"..."
    $CWD/configure-$site.ufoot.org.sh
    $CWD/install.sh
    ncftpput -u $UFOOT_ORG_FTP_USER -p $UFOOT_ORG_FTP_PASSWD -R $UFOOT_ORG_FTP_SERVER /$site $DST
done

for site in adele lise; do
    echo "Uploading "$site"..."
    $CWD/configure-$site.ufoot.org.sh
    $CWD/install.sh
    ncftpput -u $UFOOT_ORG_FTP_USER -p $UFOOT_ORG_FTP_PASSWD -R $UFOOT_ORG_FTP_SERVER /$site/prive $DST
done

echo "Uploading demo..."
$CWD/configure-demo.free.fr.sh
$CWD/install.sh
ncftpput -u $UFOOT_FREE_FTP_USER -p $UFOOT_FREE_FTP_PASSWD -R $UFOOT_FREE_FTP_SERVER /uwikicmsdemo $DST

$CWD/configure-www.test.sh
$CWD/install.sh




