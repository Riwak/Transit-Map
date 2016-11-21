<!-- 
Audouin d'Aboville
Copyright 2016-2017
http://ad-inc.fr
-->

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content=" Gest-Links - Your personal & secure links manager.">
    <meta name="author" content="AD-Riwak">
    
    <!-- Fav Icon -->
	<link rel="shortcut icon" href="images/logo.png">
	<link rel="apple-touch-icon" href="images/logo.png">
	<link rel="apple-touch-icon" sizes="114x114" href="images/logo.png">
	<link rel="apple-touch-icon" sizes="72x72" href="images/logo.png">
	<link rel="apple-touch-icon" sizes="144x144" href="images/logo.png">

    <title>Transit Map / Paris Subway </title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/3-col-portfolio.css" rel="stylesheet">
    <link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">

</head>

<body style="padding-top: 55px; background-image: URL(images/blank.jpg);">

    <!-- Navigation -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">Transit Map (Bêta1)</a>
            </div>
			
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1" style="float: right;">
                <ul class="nav navbar-nav">
                	<?php 
                	$heure = date("H:i");
                	?>
					<button type="submit" class="btn btn-default" name="" value="" type="button" style="margin-top: 8px;"><img src="images/Metro.png" width="20" height="20" style="padding: 1px 5px 4px 0px !important;"/> <?php echo $heure; ?></button>
                </ul>
            </div>
        </div>
    </nav>
    
    <?php
      
      $line = $_GET['line'];
      $dir = $_GET['dir'];
      
      if ($dir==2 || $dir==3)
      {
      	
      }
      else
      {
      	$dir=2;
      }
      
      $dir=htmlspecialchars(($dir));
      $line=htmlspecialchars(($line));
      
      $var_location = "[]";
  	  
      include("config.php");
   	  $base = mysql_connect (HOST, USER, PASS);
	  mysql_select_db (BDD, $base);
	  
	  $sql = 'SELECT * FROM Line WHERE id="'.$line.'"';
	  $req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
	  $info_line = mysql_fetch_array($req);
	  
	  $reponse = mysql_query("SELECT DISTINCT stop_id FROM stop_times WHERE metro=$line");
	  $i = 0;
					
	  while ($donnees = mysql_fetch_array($reponse))
	  {
			$i++;
			$sql = 'SELECT * FROM stops WHERE stop_id="'.$donnees[0].'"';
			$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
			$info_station = mysql_fetch_array($req);
			
			if ($i<=$info_line[1] && $dir==2)
			{	
        		$var_location = $var_location . ", ['station', " . $info_station[3] . ", " . $info_station[4] . "]";
        	}
        	else if ($i>$info_line[1] && $dir==3)
        	{
        		$var_location = $var_location . ", ['station', " . $info_station[3] . ", " . $info_station[4] . "]";
        	}
	  }
 	  if ($var_location == "[]") // Cas ou il n'y a rien en BDD
      {
       		$var_location = "['', 0,0]";
       		$last_coordinate = "0, 0";
      }
      else
      {
       	 if ($info_station[3]!="-180.0" && $info_station[4]!="-180.0")
         {
       		$last_coordinate = 48.8534100 . ", " .  2.3488000;
       	 }
       	 else
       	 {
       		$last_coordinate = "0, 0";
       	 }
       }
 				
    ?>

		<div id="map" style="height:82%; width:70%; position: absolute;"></div>
<script type="text/javascript">
    function initialize() {

        var locations = [
            <?=$var_location?>
        ];

        var mapOptions = {
            center: new google.maps.LatLng(<?=$last_coordinate?>),
            zoom: 13,
  			scaleControl: false,
 			scrollwheel: false,
        };
        var map = new google.maps.Map(document.getElementById('map'),
            mapOptions);

        var marker, i;

        for(i=0; i < locations.length; i++){
            marker = new google.maps.Marker({
            position: new google.maps.LatLng(locations[i][1], locations[i][2]),
            map: map
        });
    }
    google.maps.event.addDomListener(window, 'load', initialize);
}
</script>
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB6AI5klEZm7TqJabK3dc5-jINrCGG0bJs&signed_in=true&callback=initialize">
    </script>
    	
        
    	<div class="container" style="margin-left: 70%; width:30%; height:500px; overflow:scroll;">

    <?php

      if ($dir==2)
      {
      	echo "<a href='load_map.php?line=$line&dir=3' style='float: right; padding-top: 18px;'/><button type='button' class='btn btn-default'><i class='fa fa-exchange' aria-hidden='true'></i></button></a>";
  	  }
  	  else if ($dir==3)
  	  {
  	  	echo "<a href='load_map.php?line=$line&dir=2' style='float: right; padding-top: 18px;'/><button type='button' class='btn btn-default'><i class='fa fa-exchange' aria-hidden='true'></i></button></a>";
  	  }
  	  else
  	  {
  	  	echo "<a href='load_map.php?line=$line&dir=3' style='float: right; padding-top: 18px;'/><button type='button' class='btn btn-default'><i class='fa fa-exchange' aria-hidden='true'></i></button></a>";
  	  }
  	  
  	  echo "<a href='index.php' style='float: right; padding-top: 18px;'/><button type='button' class='btn btn-default' style='margin-right: 10px;'><i class='fa fa-chevron-left' aria-hidden='true'></i></button></a>";
  
      echo "<h2>Ligne $line</h2>";
      echo "<small> Dir : $info_line[$dir] </small>"; // ou [3];
      
      echo "<hr>";
	  
	  $reponse = mysql_query("SELECT DISTINCT stop_id FROM stop_times WHERE metro=$line");
	  $i = 0;
					
	  while ($donnees = mysql_fetch_array($reponse))
	  {
			$i++;
			
			$sql = 'SELECT * FROM stops WHERE stop_id="'.$donnees[0].'"';
			$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
			$info_station = mysql_fetch_array($req);
			
			if ($i<=$info_line[1] && $dir==2)
			{
				echo "<div class='panel panel-default'>";
        		echo "<div class='panel-heading'><a href='read_map.php?id=$donnees[0]&line=$line&dir=$dir'>$info_station[1]</a></div>";
        		echo "</div>";
        	}
        	else if ($i>$info_line[1] && $dir==3)
        	{
        		echo "<div class='panel panel-default'>";
        		echo "<div class='panel-heading'><a href='read_map.php?id=$donnees[0]&line=$line&dir=$dir'>$info_station[1]</a></div>";
        		echo "</div>";
        	}
	  }
      if ($i == 0)
 	  {
 		echo "Erreur ! Ligne incorrecte !";
 	  }
 				
    ?>
    
  		</div>
        
        <!-- Footer -->
        <nav class="navbar navbar-inverse navbar-fixed-bottom" role="navigation">
        <div class="container">
            <div class="navbar-footer">
        <hr>
            <div class="row">
                <div class="col-lg-12">
                    <p style="color: white;">Copyright &copy; 2016-2017 | Transit Map |
                     <small> Open Data RATP.</small><small><a href="" onclick="alert('Nous ne garantissons pas la disponibilité ni lintégrité des données, nous ne pourrons pas être tenus responsable en cas derreurs, omissions, suppressions, retards, défaillances.')" style="float: right; color: white;"> Précision des informations  </a></small> </p>
                </div>
            </div>
        </div>
        </div>
        </nav>
    </div>

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

</body>

</html>
