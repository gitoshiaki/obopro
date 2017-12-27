<?php

/**
 * Enviroment Variable Loader for SAKURA
 */

$path = "{$_SERVER['DOCUMENT_ROOT']}/../env_vars/{$_SERVER['HTTP_HOST']}";
if ( 'support@sakura.ad.jp' == $_SERVER['SERVER_ADMIN'] && file_exists($path) ){

    $_DB = array_merge($_SERVER, parse_ini_file($path));

    define('DATABASE_NAME','waseda-ad_obopro');
    define('DATABASE_USER', $_DB["DB_USER"] );
    define('DATABASE_PASSWORD', $_DB["DB_PASS"] );
    define('DATABASE_HOST', $_DB["DB_HOST"] );

} else {

  // 接続データベース情報(本番)
  define('DATABASE_NAME','obopro_next');
  define('DATABASE_USER','root');
  define('DATABASE_PASSWORD','root');
  define('DATABASE_HOST','127.0.0.1:8889');

}


define('PDO_DSN','mysql:dbname=' . DATABASE_NAME .';host=' . DATABASE_HOST . '; charset=utf8');

?>
