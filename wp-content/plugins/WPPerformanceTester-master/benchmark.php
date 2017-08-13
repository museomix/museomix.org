<?php

/**
 * PHP Script to benchmark PHP and MySQL-Server
 *
 * inspired by / thanks to:
 * - www.php-benchmark-script.com  (Alessandro Torrisi)
 * - www.webdesign-informatik.de
 *
 * @author odan
 * @version 2014.02.23
 * @license MIT
 *
 */
// -----------------------------------------------------------------------------
// Setup
// -----------------------------------------------------------------------------
set_time_limit(120); // 2 minutes

$arr_cfg = array();

// optional: mysql performance test
//$arr_cfg['db.host'] = DB_HOST;
//$arr_cfg['db.user'] = DB_USER;
//$arr_cfg['db.pw'] = DB_PASSWORD;
//$arr_cfg['db.name'] = DB_NAME;

// -----------------------------------------------------------------------------
// Benchmark functions
// -----------------------------------------------------------------------------

function test_benchmark($arr_cfg)
{

    $time_start = microtime(true);

    $arr_return = array();
    $arr_return['version'] = '1.0';
    $arr_return['sysinfo']['time'] = date("Y-m-d H:i:s");
    $arr_return['sysinfo']['php_version'] = PHP_VERSION;
    $arr_return['sysinfo']['platform'] = PHP_OS;
    $arr_return['sysinfo']['server_name'] = $_SERVER['SERVER_NAME'];
    $arr_return['sysinfo']['server_addr'] = $_SERVER['SERVER_ADDR'];

    test_math($arr_return);

    test_string($arr_return);

    test_loops($arr_return);

    test_ifelse($arr_return);

    if (isset($arr_cfg['db.host'])) {
        test_mysql($arr_return, $arr_cfg);
    }

    $arr_return['total'] = timer_diff($time_start);

    return $arr_return;
}

function test_math(&$arr_return, $count = 99999)
{
    $time_start = microtime(true);

    $mathFunctions = array("abs", "acos", "asin", "atan", "bindec", "floor", "exp", "sin", "tan", "pi", "is_finite", "is_nan", "sqrt");
    for ($i = 0; $i < $count; $i++) {
        foreach ($mathFunctions as $function) {
            $r = call_user_func_array($function, array($i));
        }
    }

    $arr_return['benchmark']['math'] = timer_diff($time_start);
}

function test_string(&$arr_return, $count = 99999)
{
    $time_start = microtime(true);
    $stringFunctions = array("addslashes", "chunk_split", "metaphone", "strip_tags", "md5", "sha1", "strtoupper", "strtolower", "strrev", "strlen", "soundex", "ord");

    $string = 'the quick brown fox jumps over the lazy dog';
    for ($i = 0; $i < $count; $i++) {
        foreach ($stringFunctions as $function) {
            $r = call_user_func_array($function, array($string));
        }
    }
    $arr_return['benchmark']['string'] = timer_diff($time_start);
}

function test_loops(&$arr_return, $count = 999999)
{
    $time_start = microtime(true);
    for ($i = 0; $i < $count; ++$i)
        ;
    $i = 0;
    while ($i < $count) {
        ++$i;
    }

    $arr_return['benchmark']['loops'] = timer_diff($time_start);
}

function test_ifelse(&$arr_return, $count = 999999)
{
    $time_start = microtime(true);
    for ($i = 0; $i < $count; $i++) {
        if ($i == -1) {

        } elseif ($i == -2) {

        } else if ($i == -3) {

        }
    }
    $arr_return['benchmark']['ifelse'] = timer_diff($time_start);
}

function test_mysql(&$arr_return, $arr_cfg)
{

    $time_start = microtime(true);

    //parse out port number if exists
    $port = 3306;//default
    if(stripos($arr_cfg['db.host'],':')){
        $port = substr($arr_cfg['db.host'], stripos($arr_cfg['db.host'],':')+1);
        $arr_cfg['db.host'] = substr($arr_cfg['db.host'], 0, stripos($arr_cfg['db.host'],':'));
    }

    $link = mysqli_connect($arr_cfg['db.host'], $arr_cfg['db.user'], $arr_cfg['db.pw'], $arr_cfg['db.name'], $port);
    $arr_return['benchmark']['mysql_connect'] = timer_diff($time_start);

    // //$arr_return['sysinfo']['mysql_version'] = '';


    $arr_return['benchmark']['mysql_select_db'] = timer_diff($time_start);

    $result = mysqli_query($link, 'SELECT VERSION() as version;');
    $arr_row = mysqli_fetch_assoc($result);
    $arr_return['sysinfo']['mysql_version'] = $arr_row['version'];
    $arr_return['benchmark']['mysql_query_version'] = timer_diff($time_start);

    $query = "SELECT BENCHMARK(1000000,ENCODE('hello',RAND()));";
    $result = mysqli_query($link, $query);
    $arr_return['benchmark']['mysql_query_benchmark'] = timer_diff($time_start);

    mysqli_close($link);

    $arr_return['benchmark']['mysql_total'] = timer_diff($time_start);

    return $arr_return;
}

function test_wordpress(){


    //create dummy text to insert into database
    $dummytextseed = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque sollicitudin iaculis libero id pellentesque. Donec sodales nunc id lorem rutrum molestie. Duis ac ornare diam. In hac habitasse platea dictumst. Donec nec mi ipsum. Aenean dictum imperdiet erat, at lacinia mi ultrices ut. Phasellus quis nibh ornare, pulvinar dui sit amet, venenatis arcu. Suspendisse eget vehicula ligula, et placerat sapien. Cras enim erat, scelerisque sit amet tellus vel, tempor venenatis risus. In ultricies tristique ante, eu lobortis leo. Cras ullamcorper eleifend libero, quis sollicitudin massa venenatis a. Vestibulum sed pellentesque urna, nec consectetur nulla. Vestibulum sodales purus metus, non scelerisque.";
    $dummytext = "";
    for($x=0; $x<100; $x++){
        $dummytext .= str_shuffle($dummytextseed);
    }

    //start timing wordpress mysql functions
    $time_start = microtime(true);
    global $wpdb;
    $table = $wpdb->prefix . 'options';
    $optionname = 'wpperformancetesterbenchmark_';
    $count = 250;
    for($x=0; $x<$count;$x++){
        //insert
        $data = array('option_name' => $optionname . $x, 'option_value' => $dummytext);
        $wpdb->insert($table, $data);
        //select
        $select = "SELECT option_value FROM $table WHERE option_name='$optionname" . $x . "'";
        $wpdb->get_var($select);
        //update
        $data = array('option_value' => $dummytextseed);
        $where =  array('option_name' => $optionname . $x);
        $wpdb->update($table, $data, $where);
        //delete
        $where = array('option_name'=>$optionname.$x);
        $wpdb->delete($table,$where);    
    }

    $time = timer_diff($time_start);
    $queries = ($count * 4) / $time;
    return array('time'=>$time,'queries'=>$queries);     
}


function timer_diff($time_start)
{
    return number_format(microtime(true) - $time_start, 3);
}

function array_to_html($my_array)
{
    $strReturn = '';
    if (is_array($my_array)) {
        $strReturn .= '<table>';
        foreach ($my_array as $k => $v) {
            $strReturn .= "\n<tr><td style=\"vertical-align:top;\">";
            $strReturn .= '<strong>' . htmlentities($k) . "</strong></td><td>";
            $strReturn .= array_to_html($v);
            $strReturn .= "</td></tr>";
        }
        $strReturn .= "\n</table>";
    } else {
        $strReturn = htmlentities($my_array);
    }
    return $strReturn;
}