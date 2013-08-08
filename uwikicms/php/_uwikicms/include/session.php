<?
/*
 UWiKiCMS is a lightweight web content management system.
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

/*
 * Mcrypt is much more elegant and efficient than PHP coded
 * Blowfish, but my provider does not have mcrypt activated
 * in PHP. So I decided to use Horde's Blowfish, which works
 * nicely enough for me, and works anywhere.
 */
define("UWC_SESSION_USE_MCRYPT",false);

function uwc_session_encrypt($key,$iv,$user_id) {
  $session="";

  if ($user_id) {
    $session_array=Array();
    $session_array["user_id"]=$user_id;
    $session_array["time"]=time();
    $session_serial=gzdeflate(serialize($session_array));
    $iv_bin=pack("H*", $iv);
    if (UWC_SESSION_USE_MCRYPT) {
      $session = bin2hex(mcrypt_encrypt(MCRYPT_BLOWFISH, $key, $session_serial, MCRYPT_MODE_CBC, $iv_bin));
    } else {
      $cipher=new Horde_Cipher_Blowfish();
      $cipher->setBlockMode("CBC");
      $cipher->setIV($iv_bin);
      $cipher->setKey($key);
      $session = bin2hex($cipher->encrypt($session_serial));
    }
  }
  
  return $session;
}

function uwc_session_decrypt($key,$iv,$session,$session_lifetime) {
  $user_id="";
  
  if ($session) {
    $iv_bin=pack("H*", $iv);
    if (UWC_SESSION_USE_MCRYPT) {
      $session_serial = mcrypt_decrypt(MCRYPT_BLOWFISH, $key, pack("H*", $session), MCRYPT_MODE_CBC, $iv_bin);
    } else {
      $cipher=new Horde_Cipher_Blowfish();
      $cipher->setBlockMode("CBC");
      $cipher->setIV($iv_bin);
      $cipher->setKey($key);
      $session_serial=$cipher->decrypt(pack("H*",$session));
    }
    $session_array=unserialize(gzinflate($session_serial));
    if ($session_array["user_id"] && 
	time()-$session_array["time"]<$session_lifetime) {
      $user_id=$session_array["user_id"];
    }
  }
  
  return $user_id;
}

?>
