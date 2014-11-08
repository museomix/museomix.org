<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
  include 'include.php';
?>
<html lang="fr">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <META HTTP-EQUIV="Refresh" CONTENT="300; URL=http://www.museomix.org/telepresence/index.php">
    <title>Mur de téléprésence</title>
    <link rel="stylesheet" type="text/css" href="/wp-content/themes/museomix-design-2/style.css">
    <style>
          * {
        font-family: 'museomix', Helvetica, Arial, sans-serif;
        font-size: 14px;
        margin: 0px; 
        padding: 0px;
      }

      html, body {
        width:100%;
        margin: 0;
        background-color: #000;
		background-image:none;
		color:#FFF;
		font-family : "museomix", Helvetica, Arial, sans serif;
      }
	h1 {
		font-size:20px;
	}
     #contenu{
        
     }

      .fenetre {
        position: relative;
        display:inline-block;
        margin: 15px 0 40px 15px;
		width:40%;
      }

      iframe {
        width: 100%;
        height: 220px;
        margin-top: 4px;
      }

     .footer, .place {
        margin: 0px auto;
        width: 100%;
        text-align: center;
     }
     
     .place {
      padding: 0px 0;
      color: #fff;
      text-align: left;
      margin-top: 4px;
     }

     .heure{
      color:#FFF;
      padding: 2px 7px 0px 7px
     }

     #quebec{ display: block; float: left; background-color: #c52f1c;}
     #shropshire{ display: block; float: left; background-color: #0f7b34;}
     #nantes{ display: block; float: left; background-color: #e30b7b;}
     #paris{ display: block; float: left; background-color: #075594;}
     #lens{ display: block; float: left; background-color: #62398e;}
     #grenoble{ display: block; float: left; background-color: #6f523b;}


    </style>
    <script>
      function startTime()
      {
      var today=new Date();
      var h=today.getHours();
      var h1=today.getHours()-1;
      var h2=today.getHours()-6;
      var m=today.getMinutes();
      var s=today.getSeconds();
      // add a zero in front of numbers<10
      m=checkTime(m);
      s=checkTime(s);
      document.getElementById('saint-etienne').innerHTML=h+":"+m;
      document.getElementById('derby').innerHTML=h1+":"+m;
      document.getElementById('leman').innerHTML=h+":"+m;

      t=setTimeout(function(){startTime()},500);
      }

      function checkTime(i)
      {
      if (i<10)
        {
        i="0" + i;
        }
      return i;
      }
    </script>
  </head>
    <body onload="startTime()">
    <div id="contenu" class="container" style="width:99%;margin: 5px auto;">
		<h1>Suivez Museomix 2014 en live</h1>
		<?php
		$i = 1;
		foreach($videos as $video) {
		$original = new DateTime("now");
$timezoneName = timezone_name_from_abbr("", $video[3]*3600, false);
$modified = $original->setTimezone(new DateTimezone($timezoneName));

		?>
			<div class="fenetre">
				<div class="place"><?php echo $video[0]; ?> (<span id="<?php echo $video[1]; ?>" class="heure"><?php echo $modified->format('H:i'); ?></span>)</div>
				<iframe src="<?php echo $url1.$video[2].$url2; ?>" frameborder="0" allowfullscreen="no"></iframe>    
			</div>
		<?php
		$i++;} ?>
			<div class="fenetre">
				<div class="place">Arles</div>
				<iframe src="http://new.livestream.com/accounts/4598415/events/3470029/player?width=560&height=315&autoPlay=true&mute=false" width="560" height="315" frameborder="0" scrolling="no"> </iframe>
			</div>
   </div> 
  </body>
</html>