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
    
     <meta http-Equiv="Cache-Control" Content="no-cache" />
    <meta http-Equiv="Pragma" Content="no-cache" />
    <meta http-Equiv="Expires" Content="0" />
    
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
                
            </div>
        </div>
    </nav>
    
    <?php
   	 
   	  $heure = date("H:i");
      $line = $_GET['line'];
      $dir = $_GET['dir'];
      $id = $_GET['id'];
    
      $dir=htmlspecialchars(($dir));
      $line=htmlspecialchars(($line));
      $id=htmlspecialchars(($id));
      
      $var_location = "[]";
  	  
      include("config.php");
   	  $base = mysql_connect (HOST, USER, PASS);
	  mysql_select_db (BDD, $base);
	  
	  $sql = 'SELECT * FROM Line WHERE id="'.$line.'"';
	  $req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
	  $info_line = mysql_fetch_array($req);
	  
	  $sql = 'SELECT * FROM stops WHERE stop_id="'.$id.'"';
	  $req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
	  $info_station2 = mysql_fetch_array($req);
	  
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

      if ($info_station[3]!="-180.0" && $info_station[4]!="-180.0")
      {
       		$last_coordinate = $info_station2[3] . ", " .  $info_station2[4];
      }
      else
      {
       		$last_coordinate = "0, 0";
      }
      
      $jsonString = file_get_contents('static/js/config.js');
	  $data = json_decode($jsonString, true);
	  
	  $data['center.x'] = $info_station2[4];
 	  $data['center.y'] = $info_station2[3];

	  $newJsonString = json_encode($data);
	  file_put_contents('static/js/config.js', $newJsonString);
       
    ?>

		<iframe src="vasile-api.php?<?php echo time();?>"  style="height:82%; width:70%; position: absolute;"></iframe>
    	
        <!-- Page Content -->
    	<div class="container" style="margin-left: 70%; width:30%;">

    <?php

      if ($dir==2)
      {
      	echo "<a href='read_map.php?id=$id&line=$line&dir=3' style='float: right; padding-top: 18px;'/><button type='button' class='btn btn-default'><i class='fa fa-exchange' aria-hidden='true'></i></button></a>";
  	  }
  	  else if ($dir==3)
  	  {
  	  	echo "<a href='read_map.php?id=$id&line=$line&dir=2' style='float: right; padding-top: 18px;'/><button type='button' class='btn btn-default'><i class='fa fa-exchange' aria-hidden='true'></i></button></a>";
  	  }
  	  else
  	  {
  	  	echo "<a href='read_map.php?id=$id&line=$line&dir=3' style='float: right; padding-top: 18px;'/><button type='button' class='btn btn-default'><i class='fa fa-exchange' aria-hidden='true'></i></button></a>";
  	  }
  	  
  	  echo "<a href='load_map.php?line=$line&dir=$dir' style='float: right; padding-top: 18px;'/><button type='button' class='btn btn-default' style='margin-right: 10px;'><i class='fa fa-chevron-left' aria-hidden='true'></i></button></a>";
  
      echo "<h2>Ligne $line</h2>";
      echo "<small> Dir : $info_line[$dir] </small>"; // ou [3];
      
      echo "<hr>";
      
      ?>
      
      <div class="panel panel-default">
        <div class="panel-heading"><a href=""><?php echo $info_station2[1];?> </a></div>
      </div>
     
     <center>Prochains passages </center>
     
        <hr>
     
     <!-- SELECT DISTINCT * FROM stop_times WHERE stop_id=1644 AND metro=6 AND (arrival_time BETWEEN "12:00" AND "12:15") ORDER BY `stop_times`.`arrival_time` ASC LIMIT 5 -->
     
     <?php
     
	 $endTime = strtotime("+15 minutes", strtotime($heure));
	 $endTime = date('H:i', $endTime);
     
     $reponse = mysql_query('SELECT DISTINCT * FROM stop_times WHERE stop_id="'.$id.'" AND metro="'.$line.'" AND (arrival_time BETWEEN "'.$heure.'" AND "'.$endTime.'") ORDER BY stop_times.arrival_time ASC LIMIT 3');
	 $i = 0;
					
	 while ($donnees = mysql_fetch_array($reponse))
	 {
	 	$i++;
	 	
	 	$datetime1 = new DateTime($heure);
		$datetime2 = new DateTime($donnees[0]);

	 	$dteDiff  = $datetime2->diff($datetime1);
	 	$display= $dteDiff->format("%H:%I");
	 	
	 	if ($display=="00:00")
	 	{
	 		$display = "A Quai!";
	 	}
	 	
	 	echo "<i class='fa fa-exchange' aria-hidden='true'></i> $display</br>";
	 }
	 if ($i==0)
	 {
	 	echo "Plus de métro de prévu.";
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
    <script src="static/js/config.js?<?php echo time();?>"></script>

</body>

</html>
