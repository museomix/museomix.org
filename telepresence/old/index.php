<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
  include 'include.php';
?>
<html lang="fr">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <META HTTP-EQUIV="Refresh" CONTENT="300; URL=http://museomix.rezopole.net/telepresence/index.php">
    <title>Mur de téléprésence</title>
    <!--
    <link rel="stylesheet" type="text/css" href="style.css">
    <script type="text/javascript" src="script.js"></script>
    -->
    <style>
          * {
        font-family: 'Geneva', Helvetica, Arial, sans-serif;
        font-size: 14px;
        margin: 0px; 
        padding: 0px;
      }

      html, body {
        width:100%;
        margin: 0;
        background-color: #000;
      }

     #contenu{
        position:absolute;
        height:600px; 
        width:1280px;
        margin:-300px 0px 0px -630px;
        top: 50%; 
        left: 50%;
     }

      .fenetre {
        position: relative;
        float: left;
        width: 405px;
        height: 251px;
        margin: 15px 0 40px 15px;
      }

      iframe {
        width: 390px;
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
      color: #000;
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
      document.getElementById('heure1').innerHTML=h2+":"+m+":"+s;
      document.getElementById('heure2').innerHTML=h1+":"+m+":"+s;
      document.getElementById('heure3').innerHTML=h+":"+m+":"+s;
      document.getElementById('heure4').innerHTML=h+":"+m+":"+s;
      document.getElementById('heure5').innerHTML=h+":"+m+":"+s;
      document.getElementById('heure6').innerHTML=h+":"+m+":"+s;

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
    <div id="contenu">

      <div class="fenetre">
        <div id="quebec"><div id="heure1" class="heure">08:17</div></div><br>
        <div class="place"><img src="images/quebec.gif"></div>
        <iframe src="<?php echo $url1.$qb.$url2; ?>" frameborder="0" allowfullscreen="no"></iframe>    
      </div>

      <div class="fenetre">
        <div id="shropshire"><div id="heure2" class="heure">13:17</div></div><br>
        <div class="place"><img src="images/shropshire.gif"></div>        
        <iframe src="<?php echo $url1.$en.$url2; ?>" frameborder="0" allowfullscreen="no"></iframe>
        </div>
      <div class="fenetre">
        <div id="nantes"><div id="heure3" class="heure">14:17</div></div><br>
        <div class="place"><img src="images/nantes.gif"></div>        
        <iframe src="<?php echo $url1.$na.$url2; ?>" frameborder="0" allowfullscreen="no"></iframe>    
        </div>
      <div class="fenetre">
        <div id="paris"><div id="heure4" class="heure">14:17</div></div><br>
        <div class="place"><img src="images/paris.gif"></div>       
        <iframe src="<?php echo $url1.$pa.$url2; ?>" frameborder="0" allowfullscreen="no"></iframe>
        </div>
      <div class="fenetre">
        <div id="lens"><div id="heure5" class="heure">14:17</div></div><br>
        <div class="place"><img src="images/lens.gif"></div>
        <iframe src="<?php echo $url1.$le.$url2; ?>" frameborder="0" allowfullscreen="no"></iframe>
      </div>
      <div class="fenetre">
        <div id="grenoble"><div id="heure6" class="heure">14:17</div></div><br>
        <div class="place"><img src="images/grenoble.gif"></div>        
        <iframe src="<?php echo $url1.$gr.$url2; ?>" frameborder="0" allowfullscreen="no"></iframe>    
      </div>
<!--6IZeD6Q4lSg-->
   </div> 
  </body>
</html>