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

function uwc_lang_exists($lang_id) {
  $langs=array('en'=>'en_US.ISO-8859-1',
	       'fr'=>'fr_FR.ISO-8859-1',
	       'de'=>'de_DE.ISO-8859-1',
	       'es'=>'es_ES.ISO-8859-1',
	       'da'=>'da_DK.ISO-8859-1');
  
  return array_key_exists($lang_id, $langs);
}

class UWC_Lang {
  var $lang_id="en";
  var $lang="en-US";
  var $charset="iso-8859-1";
  var $locale="en_US.ISO-8859-1";
  var $messages=array();
  var $langs=array('en'=>'en_US.ISO-8859-1',
		     'fr'=>'fr_FR.ISO-8859-1',
		     'de'=>'de_DE.ISO-8859-1',
		     'es'=>'es_ES.ISO-8859-1',
		     'da'=>'da_DK.ISO-8859-1');

  function get_lang_id() {
    return $this->lang_id;
  }

  function get_lang() {
    return $this->lang;
  }

  function get_charset() {
    return $this->charset;
  }

  function get_locale() {
    return $this->locale;
  }

  function get_message($msg_id) {
    return $this->messages[$msg_id];
  }

  function set_lang_id($lang_id) {
    if (gettype($lang_id)!="string" or !uwc_lang_exists($lang_id)) {
      $lang_id="en";
    }

    $curgtlang=$this->langs[$lang_id];
    $gtparts=@preg_split("/\./",$curgtlang);
    $tmp=strtolower($gtparts[0]);
    $this->lang=preg_replace("/\_/", "-", $tmp);
    $this->charset=$gtparts[1];
    $this->lang_id=substr($this->lang,0,2);
    $this->locale=$curgtlang;
    setlocale (LC_ALL, $this->locale);
    $GLOBALS["LANG"]=$this->locale;
  }

  function include_messages() {
    /*
     * We include the right language file, which
     * will define the "messages" array.
     */
    $langxx=preg_replace("/(.*)\.php/","\$1".$this->get_lang_id().".php",__FILE__);
    if (file_exists($langxx)) {
      include $langxx;
    } else {
      // by default we use English
      include "langen.php";
    }
  }

  function set_accepted_lang($langs) {
    $gettextlangs=array();
    if (! count($langs)) {
      $langs=array_keys($this->langs);
    }
    foreach ($langs as $lang) {
      if (uwc_lang_exists($lang)) {
	array_push($gettextlangs,$this->langs[$lang]);
      }
    }
    $lang_id=uwc_lang_al2gt($gettextlangs);
    $this->set_lang_id($lang_id);
  }

  function format_date($timestamp) {
    switch ($this->lang_id) {
    case "fr":
      $date=strftime("%A %d %B %Y",$timestamp);
      break;
    default:
      // en
      $date=strftime("%a %b %d %Y",$timestamp);
      break;      
    }      
    
    return $date;
  }
}

/*
 * Following code is ripped GPL'ed code from Wouter Verhels,
 * got method al2gt from the same source.
 *
 * accept-to-gettext.inc -- convert information in 'Accept-*' headers to
 * gettext language identifiers.
 * Copyright (c) 2003, Wouter Verhelst <wouter@debian.org>
 */

function uwc_lang_find_match($curlscore,$curcscore,$curgtlang,$langval,$charval,
			     $gtlang)
{
  if($curlscore < $langval) {
    $curlscore=$langval;
    $curcscore=$charval;
    $curgtlang=$gtlang;
  } else if ($curlscore == $langval) {
    if($curcscore < $charval) {
      $curcscore=$charval;
      $curgtlang=$gtlang;
    }
  }
  return array($curlscore, $curcscore, $curgtlang);
}

function uwc_lang_al2gt($gettextlangs) {
  /* default to "everything is acceptable", as RFC2616 specifies */
  $acceptLang=(($_SERVER["HTTP_ACCEPT_LANGUAGE"] == '') ? '*' :
	       $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
  $acceptChar=(($_SERVER["HTTP_ACCEPT_CHARSET"] == '') ? '*' :
	       $_SERVER["HTTP_ACCEPT_CHARSET"]);
  $alparts=@preg_split("/,/",$acceptLang);
  $acparts=@preg_split("/,/",$acceptChar);
      
  /* Parse the contents of the Accept-Language header.*/
  foreach($alparts as $part) {
    $part=trim($part);
    if(preg_match("/;/", $part)) {
      $lang=@preg_split("/;/",$part);
      $score=@preg_split("/=/",$lang[1]);
      $alscores[$lang[0]]=$score[1];
    } else {
      $alscores[$part]=1;
    }
  }
      
  /* Do the same for the Accept-Charset header. */

  /* RFC2616: ``If no "*" is present in an Accept-Charset field, then
   * all character sets not explicitly mentioned get a quality value of
   * 0, except for ISO-8859-1, which gets a quality value of 1 if not
   * explicitly mentioned.''
   * 
   * Making it 2 for the time being, so that we
   * can distinguish between "not specified" and "specified as 1" later
   * on. */
  $acscores["ISO-8859-1"]=2;

  foreach($acparts as $part) {
    $part=trim($part);
    if(preg_match("/;/", $part)) {
      $cs=@preg_split("/;/",$part);
      $score=@preg_split("/=/",$cs[1]);
      $acscores[strtoupper($cs[0])]=$score[1];
    } else {
      $acscores[strtoupper($part)]=1;
    }
  }
  if($acscores["ISO-8859-1"]==2) {
    $acscores["ISO-8859-1"]=(isset($acscores["*"])?$acscores["*"]:1);
  }

  /* 
   * Loop through the available languages/encodings, and pick the one
   * with the highest score, excluding the ones with a charset the user
   * did not include.
   */
  $curlscore=0;
  $curcscore=0;
  $curgtlang=NULL;
  foreach($gettextlangs as $gtlang) {

    $tmp1=preg_replace("/\_/","-",$gtlang);
    $tmp2=@preg_split("/\./",$tmp1);
    $allang=strtolower($tmp2[0]);
    $gtcs=strtoupper($tmp2[1]);
    $noct=@preg_split("/-/",$allang);

    $testvals=array(
		    array(array_key_exists($allang, $alscores) ? $alscores[$allang] : 0, $acscores[$gtcs]),
		    array(array_key_exists($noct[0], $alscores) ? $alscores[$noct[0]] : 0, $acscores[$gtcs]),
		    array(array_key_exists($allang, $alscores) ? $alscores[$allang] : 0, $acscores["*"]),
		    array(array_key_exists($noct[0], $alscores) ? $alscores[$noct[0]] : 0, $acscores["*"]),
		    array(array_key_exists("*", $alscores) ? $alscores["*"] : 0, $acscores[$gtcs]),
		    array(array_key_exists("*", $alscores) ? $alscores["*"] : 0, $acscores["*"]));

    $found=FALSE;
    foreach($testvals as $tval) {
      if(!$found && isset($tval[0]) && isset($tval[1])) {
	$arr=uwc_lang_find_match($curlscore, $curcscore, $curgtlang, $tval[0],
				 $tval[1], $gtlang);
	$curlscore=$arr[0];
	$curcscore=$arr[1];
	$curgtlang=$arr[2];
	$found=TRUE;
      }
    }
  }

  /* We must re-parse the gettext-string now, since we may have found it
   * through a "*" qualifier.*/
  
  //$gtparts=@preg_split("/\./",$curgtlang);
  //$tmp=strtolower($gtparts[0]);
  //$this->lang=preg_replace("/\_/", "-", $tmp);
  //$this->charset=$gtparts[1];
  //$this->lang_id=substr($this->lang,0,2);
  //$this->locale=$curgtlang;
  //
  //  header("Content-Language: $lang");
  //  header("Content-Type: $mime; charset=$charset");
  //
  //  return $curgtlang;

  return substr($curgtlang,0,2);
}
?>
