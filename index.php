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
    <meta name="description" content=" Transit Map - Where is my Subway ?">
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

<body>

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

    <!-- Page Content -->
    <div class="container">
    
    <center><h1 class="page-header" style=" font-weight: bold;">Où est le prochain Métro ?
                    
                </h1>
                
                </hr>
                
                 <form action="load_map.php" method="get" class="form-signin" role="form" enctype="multipart/form-data">
					<SELECT name="line" size="1" class="form-control" autofocus required style="width: 290px; height: 43px;">
					
					<?php
                    include("config.php");

                    try {
                        $pdo = new PDO(
                            "mysql:host=" . HOST . ";dbname=" . BDD . ";charset=utf8",
                            USER,
                            PASS,
                            [
                                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_NUM
                            ]
                        );

                        $reponse = $pdo->query("SELECT * FROM Line");

                        while ($donnees = $reponse->fetch())
                        {
                            echo "<option value=\"" . htmlspecialchars($donnees[0]) . "\">";
                            echo "Ligne " . htmlspecialchars($donnees[0]) . " - ";
                            echo htmlspecialchars($donnees[2]) . " / " . htmlspecialchars($donnees[3]);
                            echo "</option>";
                        }

                    } catch (PDOException $e) {
                        die("Erreur de connexion à la base de données.");
                    }
                    ?>
					
					</SELECT>
					</br>
					<button type="submit" class="btn btn-default" type="button">Recherche</button>
				</form>
				
				
				</br>
				</hr>
				</br>
				<a href="images/metro-paris.gif"/><img src="images/metro-paris.gif" width="290" height="290" style="border-radius: 25px;border: 2px solid #95a5a6;"/></a>
				
				</center>
        
        
        
        <!-- Footer -->
        <nav class="navbar navbar-inverse navbar-fixed-bottom" role="navigation">
        <div class="container">
            <div class="navbar-footer">
        <hr>
            <div class="row">
                <div class="col-lg-12">
                    <p style="color: white;">Copyright &copy; 2016-2017 | Transit Map / Paris Subway / V 0.1</p>
                    <!-- - <small> Open Data RATP.</small><small><a href="" onclick="alert('Nous ne garantissons pas la disponibilité ni lintégrité des données, nous ne pourrons pas être tenus responsable en cas derreurs, omissions, suppressions, retards, défaillances.')" style="float: right; color: white;"> Précision des informations  </a></small> !-->
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
