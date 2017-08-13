<?php
/**
 * Plugin Name: SecuPress COOKIEHASH
 * Description: Change the default COOKIEHASH constant value to prevent easy guessing.
 * Version: 1.0
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Copyright 2012-2016 SecuPress
 */

define( 'COOKIEHASH', md5( __FILE__ . '!Vd%iwGcaKqht9^wGI#5(IdpMrKZAT)u7YQy!YlCLZFe&KTNzJHdiQ*!0JrwUPn$' ) );
