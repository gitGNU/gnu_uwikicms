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

define("UWC_FORMAT_LONGDESC_CUT", 80);

function uwc_format_escape_sql($str) {
  $str=uwc_format_unescape_gpc($str);
  // trim all < 0x20 chars except \t (0x09) and \n (0x0A)
  $str=preg_replace("/([\\x00-\\x08]|[\\x0B-\\x1F])/","",$str);
  // tab -> 8 spaces
  $str = preg_replace('/\t/','        ', $str);
  // protect string
  $str = addslashes($str);

  return $str;
}

function uwc_format_escape_sql_bin($str) {
  // protect string
  $str = addslashes($str);

  return $str;
}

function uwc_format_unescape_gpc($str) {
  if (get_magic_quotes_gpc()) {
    /*
     * If magic_quote_gpc is on data have already been escaped...
     */
    $str=stripslashes($str);
  }

  return $str;
}

function uwc_format_text_to_html($str) {
  /*
   * This function can seem useless since it looks like
   * a plain call to htmlentities, however we use it in
   * case we want to change its behavior.
   */

  return uwc_format_htmlfriendly(htmlentities($str,ENT_COMPAT|ENT_XHTML,"ISO-8859-15"));
}

function uwc_format_html_to_xul($str) {
  return htmlspecialchars(html_entity_decode($str),ENT_COMPAT|ENT_XHTML,"ISO-8859-15");
}

function uwc_format_text_to_xul($str) {
  return uwc_format_html_to_xul(uwc_format_text_to_html($str));
}

function uwc_format_text_to_html_attribute($str) {
  /*
   * Usefull to format stuff for html attributes like
   * alt tags within images or html meta attributes
   */
  return uwc_format_text_to_html(preg_replace("/([\\x00-\\x1F])/"," ",$str));
}

function uwc_format_phpwiki_to_html($str) {
  global $html;
  global $pagehash;

  $html="";
  $pagehash=Array();
  $pagehash["content"]=explode("\n",$str);
  $pagehash["refs"]=Array();

  phpwiki_transform();

  return uwc_format_htmlfriendly($html);
}

function uwc_format_fix_content_text($str) {
  /*
   * Fixes the text fill when inserting/updating content.
   * The idea is that we filter the typed text, mostly for
   * inlined images, which we want to be separated from the rest.
   */
  $str=preg_replace("/\r/","",$str);
  $str=preg_replace("/\n*\s*\[\s*img\s*\:\s*(\d+)\s*\]\s*\n*/","\n\n[ img:\\1 ]\n\n",$str);

  return $str;
}

function uwc_format_readable_size($size) {
  if ($size<1000) {
    $readable_size=sprintf("%d ",$size);
  } elseif ($size<10000) {
    $readable_size=sprintf("%1.1f k",$size/1000);
  } elseif ($size<1000000) {
    $readable_size=sprintf("%d k",$size/1000);
  } elseif ($size<10000000) {
    $readable_size=sprintf("%1.1f M",$size/1000000);
  } else {
    $readable_size=sprintf("%d M",$size/1000000);
  }

  return $readable_size;
}

function uwc_format_cut_text($text,$limit) {
  $text_cut=substr($text,0,max($limit-3,1));
  if ($text_cut != $text) {
    $text_cut.="...";
  } else {
    $text_cut=$text;
  }
  return $text_cut;
}

function uwc_format_html_for_img_link_preview($url_page,$url_preview,$w,$h,$alt,$longdesc,$linkname,$odd) {
  /*
  switch ($linkname) {
  case "left":
  case "right":
  case "center":
    $style=$linkname;
    break;
  case "auto":
  default:
    $style=$odd ? "left" : "right";
    break;
  }
  */
  $style=$odd ? "odd" : "even";


  $alt=uwc_format_text_to_html_attribute($alt);
  //$longdesc=uwc_format_text_to_html(uwc_format_cut_text($longdesc,UWC_FORMAT_LONGDESC_CUT));
  $longdesc=uwc_format_text_to_html($longdesc);
  
  $html="<div class=\"image $style\"><div><a href=\"$url_page\"><img src=\"$url_preview\" width=\"$w\" height=\"$h\" alt=\"$alt\" class=\"msiehackimage\" /></a></div><div>$longdesc</div></div>\n";
  
  return $html;
}

function uwc_format_html_for_img_link_full($url_page,$url_preview,$w,$h,$alt,$longdesc,$linkname) {
  $alt=uwc_format_text_to_html_attribute($alt);
  //$longdesc=uwc_format_text_to_html(uwc_format_cut_text($longdesc,UWC_FORMAT_LONGDESC_CUT));
  $longdesc=uwc_format_text_to_html($longdesc);
  
  $html="<div class=\"image full\"><div><a href=\"$url_page\"><img src=\"$url_preview\" width=\"$w\" height=\"$h\" alt=\"$alt\" class=\"msiehackimage\" /></a></div><div>$longdesc</div></div>\n";
  
  return $html;
}

/*
 * Function found on http://www.php.net;
 * by mail at britlinks dot com
 * similar to cedric at shift-zone dot be's function, 
 * this 'cleans up' text from MS Word, 
 * and other non-alphanumeric characters 
 * to their valid [X]HTML counterparts
 */
function uwc_format_htmlfriendly($var){
   $chars = array(
       128 => '&#8364;',
       130 => '&#8218;',
       131 => '&#402;',
       132 => '&#8222;',
       133 => '&#8230;',
       134 => '&#8224;',
       135 => '&#8225;',
       136 => '&#710;',
       137 => '&#8240;',
       138 => '&#352;',
       139 => '&#8249;',
       140 => '&#338;',
       142 => '&#381;',
       145 => '&#8216;',
       146 => '&#8217;',
       147 => '&#8220;',
       148 => '&#8221;',
       149 => '&#8226;',
       150 => '&#8211;',
       151 => '&#8212;',
       152 => '&#732;',
       153 => '&#8482;',
       154 => '&#353;',
       155 => '&#8250;',
       156 => '&#339;',
       158 => '&#382;',
       159 => '&#376;');
   $var = str_replace(array_map('chr', array_keys($chars)), $chars, $var);

   return $var;
}

function uwc_format_escape_rss($var) {
  $var = strtr($var, 
	       array("&" => "&amp;",
		     ">" => "&gt;",
		     "<" => "&lt;",
		     '"' => "&quot;",
		     "'" => "&apos;",
		     "\r" => " ",
		     "\n" => " ",
		     "\t" => " "));

  return $var;
}

function uwc_format_no_markup($var) {
  $var = preg_replace("/\[([^\|\]]+)\|([^\|\]]+)\]/","\$1",$var);
  $var = preg_replace("/\[([^\|\]]+)\]/","",$var);
  $var = preg_replace("/^[\#\*\!]+/","",$var);
  $var = preg_replace("/[\r\n][\#\*\!]+/","",$var);
  $var = preg_replace("/[[:cntrl:][:space:]]+/"," ",$var);
  $var = preg_replace("/^ +/","",$var);
  $var = preg_replace("/ +\$/","",$var);

  return $var;
}

function uwc_format_html_to_rss($var) {
  $var = html_entity_decode($var,ENT_COMPAT|ENT_XHTML,"ISO-8859-15");
  $var = uwc_format_escape_rss($var);

  return $var;
}

?>
