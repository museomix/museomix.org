<?php
define('WPML_STICKY_LINKS_FOLDER', basename(WPML_STICKY_LINKS_PATH));


if(defined('WP_ADMIN') && defined('FORCE_SSL_ADMIN') && FORCE_SSL_ADMIN){
    define('WPML_STICKY_LINKS_URL', rtrim(str_replace('http://','https://', WP_PLUGIN_URL), '/') . '/' . WPML_STICKY_LINKS_FOLDER );
}else{
    define('WPML_STICKY_LINKS_URL', WP_PLUGIN_URL . '/' . WPML_STICKY_LINKS_FOLDER );
}