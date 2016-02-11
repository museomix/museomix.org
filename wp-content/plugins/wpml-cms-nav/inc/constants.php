<?php

define('WPML_CMS_NAV_PLUGIN_FOLDER', basename(WPML_CMS_NAV_PLUGIN_PATH));

//if(defined('WP_ADMIN') && (defined('FORCE_SSL_ADMIN') && FORCE_SSL_ADMIN)){
if((defined('FORCE_SSL_ADMIN') && FORCE_SSL_ADMIN) || is_ssl()){
    define('WPML_CMS_NAV_PLUGIN_URL', rtrim(str_replace('http://','https://', WP_PLUGIN_URL), '/') . '/' . WPML_CMS_NAV_PLUGIN_FOLDER );
}else{
    define('WPML_CMS_NAV_PLUGIN_URL', WP_PLUGIN_URL . '/' . WPML_CMS_NAV_PLUGIN_FOLDER );
}

define('WPML_CMS_NAV_CACHE_EXPIRE', '1 HOUR');

if(!defined('PHP_EOL')){ define ('PHP_EOL',"\r\n"); }
