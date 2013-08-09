<?php
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

echo "Running configure.php\n";

$ROOT_HTACCESS="php/.htaccess";
$INCLUDE_HTACCESS="php/_uwikicms/include/.htaccess";
$TEMPLATE_HTACCESS="php/_uwikicms/template/php/.htaccess";
$TMP_HTACCESS="php/_uwikicms/tmp/.htaccess";
$SECRET_HTACCESS="php/_uwikicms/secret/.htaccess";
$UWIKICMS_CONFIG_PHP="php/_uwikicms/include/config.php";
$PHPWIKI_CONFIG_PHP="php/_uwikicms/include/phpwiki/config.php";

function read_file($filename) {
  $handle = fopen ($filename, "r");
  $contents="";
  if ($size=filesize ($filename)) {
    while (!feof($handle)) {
      $contents .= fread ($handle, 1024);
    }
  }
  fclose($handle);

  return $contents;
}

function write_file($filename,$content) {
  $handle = fopen ($filename, "w");
  fwrite ($handle, $content);
  fflush ($handle);
  fclose($handle);
}

function copy_from_dist($filename) {
  $content = read_file($filename."-dist");
  write_file($filename, $content);
  printf("Copy %s\n", $filename);
}

function echo_file($filename) {
  echo read_file($filename);
}

function preg_replace_file($pattern, $replacement, $filename) {
  $contents=read_file($filename);
  $contents_replaced=preg_replace($pattern, $replacement, $contents);

  if ($contents != $contents_replaced) {
    write_file($filename,$contents_replaced);
    printf("Setup %s (pattern=%s)\n", $filename, $pattern);
  }
}

class Options {
  var $argv=Array();
  var $help=false;
  var $values=Array("htprefix"=>"/uwikicms",
		    "dbprefix"=>"",
		    "siteurl"=>"http://localhost",
		    "dbhost"=>"server",
		    "dbname"=>"mydb",
		    "dbuser"=>"user",
		    "dbpasswd"=>"x",
		    "images_dir"=>"/_uwikicms/template/images/default",
		    "css_dir"=>"/_uwikicms/template/css/default",
		    "copyright_holder"=>"Christian Mauduit");
  var $bools=Array("debug"=>true);

  function Options ($argv) {
    $this->argv=$argv;
  }

  function process_bool_option($option_name, &$option_value) {
    foreach ($this->argv as $arg) {
      if ($arg == "--".$option_name) {
	$option_value=true;
      }
    }
  }

  function process_value_option($option_name, &$option_value) {
    foreach ($this->argv as $arg) {
      if (preg_match("/^--".$option_name."=(.*)$/", $arg, $matches)) {
	$option_value=$matches[1];
      }
    }
  }

  function process_options() {
    $this->process_bool_option("help", $this->help);
    foreach (array_keys($this->values) as $key) {
      $this->process_value_option($key, $this->values[$key]);
    }
    srand((double)microtime()*1000003);
    $this->values["mcrypt_key"]=md5(getcwd().serialize($this->values).rand());
    $iv_bin=mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_CBC), MCRYPT_DEV_RANDOM);
    $this->values["mcrypt_iv"]=bin2hex($iv_bin);
    $this->process_bool_option("debug", $this->bools["debug"]);
    $this->process_bool_option("release", $release);
    if ($release) {
      $this->bools["debug"]=false;
    }
  }

  function print_value_option($option_name, $option_value) {
    printf("option->%s = %s\n",$option_name,$option_value);
  }

  function print_help() {
    echo "Echoing README file:\n";
    echo_file("README");
  }

  function print_options() {
    foreach ($this->values as $key=>$value) {
      $this->print_value_option($key, $value);
    }
    foreach ($this->bools as $key=>$value) {
      $this->print_value_option($key, $value ? "true" : "false");
    }
  }

  function setup_options() {
    global $ROOT_HTACCESS;
    global $INCLUDE_HTACCESS;
    global $TEMPLATE_HTACCESS;
    global $TMP_HTACCESS;
    global $SECRET_HTACCESS;
    global $UWIKICMS_CONFIG_PHP;
    global $PHPWIKI_CONFIG_PHP;

    copy_from_dist($ROOT_HTACCESS);
    foreach (Array("403","404") as $err) {
      preg_replace_file("/^ErrorDocument\\s+".$err."\\s+.*$/m","ErrorDocument ".$err." ".$this->values["htprefix"]."/_uwikicms/".$err.".php",$ROOT_HTACCESS);
    }
    copy_from_dist($INCLUDE_HTACCESS);
    copy_from_dist($TEMPLATE_HTACCESS);
    copy_from_dist($TMP_HTACCESS);
    copy_from_dist($SECRET_HTACCESS);
    copy_from_dist($UWIKICMS_CONFIG_PHP);
    foreach ($this->values as $key=>$value) {
      preg_replace_file("/\\$".$key."\\s*\=.*$/m","\\$".$key." = \"".$value."\"; // setup by ./configure",$UWIKICMS_CONFIG_PHP);
    }
    foreach ($this->bools as $key=>$value) {
      preg_replace_file("/\\$".$key."\\s*\=.*$/m","\\$".$key." = ".($value ? "true" : "false")."; // setup by ./configure",$UWIKICMS_CONFIG_PHP);
    }
    if ($this->bools["debug"]) {
      preg_replace_file("/error_reporting\\s*\\(.*\\).*$/m","error_reporting(E_ALL ^ E_NOTICE); // setup by ./configure",$UWIKICMS_CONFIG_PHP);
    } else {
      preg_replace_file("/error_reporting\\s*\\(.*\\).*$/m","error_reporting(0); // setup by ./configure",$UWIKICMS_CONFIG_PHP);
    }
    copy_from_dist($PHPWIKI_CONFIG_PHP);    
  }
}
 
function configure($argv) {
  $options=new Options($argv);
  $options->process_options();
  if ($options->help) {
    $options->print_help();
  } else {
    $options->print_options();
    $options->setup_options();
  }
}

configure($argv);

?>
