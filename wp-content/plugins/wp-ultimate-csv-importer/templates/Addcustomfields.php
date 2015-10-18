<?php
	if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly
	$filename = isset($_POST['filename']) ? $_POST['filename'] : '';
	$count = isset($_POST['corecount']) ? $_POST['corecount'] : '';
        $impobj = new WPImporter_includes_helper();
	$getrec = $impobj->csv_file_data($filename);
	$csvheaders = $impobj->headers;
        $returndata = "<table><tr><td><input type='text' name='coremapping$count' id = 'coremapping$count'/></td>";
	$returndata .= "<td class='left_align'> <select name='coretextbox$count' id='coretextbox$count' class='uiButton'>";
	$returndata .= "<option id = 'select'>-- Select --</option>";
	foreach($csvheaders as $headerkey => $headervalue){
		$returndata .= "<option value = 'textbox$headerkey'>$headervalue</option>";
	}
	$returndata .= "</select></td>";
	$returndata .= "<td></td><td></td></tr></table>";
        print_r($returndata);die;
?>
