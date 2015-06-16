<?php
/*
	Stop Spammers Plugin 
	Options Setup Page for MU switch
	
*/
if (!defined('ABSPATH')) exit; // just in case

if(!current_user_can('manage_options')) {
	die('Access Denied');
}
?>

<div class="wrap">
  <h2>Stop Spammers Multisite Options</h2>
  <?php
$now=date('Y/m/d H:i:s',time() + ( get_option( 'gmt_offset' ) * 3600 ));
//$ip=kpg_get_ip();
$ip=$_SERVER['REMOTE_ADDR'];
$nonce='';
$muswitch=get_option('kpg_muswitch');
if (empty($muswitch)) $muswitch='N';
if (array_key_exists('kpg_stop_spammers_control',$_POST)) $nonce=$_POST['kpg_stop_spammers_control'];
if (wp_verify_nonce($nonce,'kpgstopspam_update')) { 
	if (array_key_exists('action',$_POST)) {
		if (array_key_exists('muswitch',$_POST)) $muswitch=trim(stripslashes($_POST['muswitch']));
		if (empty($muswitch)) $muswitch='N';
		if ($muswitch!='Y') $muswitch='N';
		update_option('kpg_muswitch',$muswitch);
		echo "<h2>Options Updated</h2>";

	} 	} else {
	// echo "no nonce<br/>";
}

$nonce=wp_create_nonce('kpgstopspam_update');

?>
  <form method="post" action="">
    <input type="hidden" name="kpg_stop_spammers_control" value="<?php echo $nonce;?>" />
    <input type="hidden" name="action" value="update mu settings" />
    <h4>Network Blog Option:</h4>
    <table align="center" cellspacing="1" style="background-color:#CCCCCC;font-size:.9em;">
      <tr bgcolor="white">
        <td width="20%" valign="top">Select how you want to control options in a networked blog environment:</td>
        <td valign="top"> Networked ON:
          <input name="muswitch" type="radio" value='Y'  <?php if ($muswitch=='Y') echo "checked=\"true\""; ?> />
          <br/>
          Networked OFF:
          <input name="muswitch" type="radio" value='N' <?php if ($muswitch!='Y') echo "checked=\"true\""; ?> />
        </td>
        <td valign="top"><p>If you are running WPMU and want to control options and history through the main log admin panel, select on. If you select OFF, each blog will have to configure the plugin separately, and each blog will have a separte history.. <br />
          </p></td>
      </tr>
    </table>
    <br/>
    <br/>
    <p class="submit">
      <input class="button-primary" value="Save Changes" type="submit" />
    </p>
  </form>
</div>
