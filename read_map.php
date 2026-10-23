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
    <meta name="description" content="Gest-Links - Your personal & secure links manager.">
    <meta name="author" content="AD-Riwak">

    <meta http-equiv="Cache-Control" content="no-cache">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <!-- Fav Icon -->
    <link rel="shortcut icon" href="images/logo.png">
    <link rel="apple-touch-icon" href="images/logo.png">
    <link rel="apple-touch-icon" sizes="114x114" href="images/logo.png">
    <link rel="apple-touch-icon" sizes="72x72" href="images/logo.png">
    <link rel="apple-touch-icon" sizes="144x144" href="images/logo.png">

    <title>Transit Map / Paris Subway</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/3-col-portfolio.css" rel="stylesheet">

    <link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">

</head>

<body style="padding-top: 55px; background-image: url(images/blank.jpg);">

    <!-- Navigation -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">

        <div class="container">

            <div class="navbar-header">

                <button type="button"
                        class="navbar-toggle"
                        data-toggle="collapse"
                        data-target="#bs-example-navbar-collapse-1">

                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>

                </button>

                <a class="navbar-brand" href="index.php">
                    Transit Map (Bêta1)
                </a>

            </div>

            <div class="collapse navbar-collapse"
                 id="bs-example-navbar-collapse-1"
                 style="float: right;">
            </div>

        </div>

    </nav>


<?php

/* -----------------------------------------------------------
   PARAMÈTRES
----------------------------------------------------------- */

$heure = date("H:i");

$line = isset($_GET['line']) ? (int) $_GET['line'] : 0;
$dir  = isset($_GET['dir'])  ? (int) $_GET['dir']  : 2;
$id   = isset($_GET['id'])   ? $_GET['id'] : '';


/* Direction autorisée uniquement : 2 ou 3 */

if ($dir != 2 && $dir != 3) {
    $dir = 2;
}


/* -----------------------------------------------------------
   CONNEXION BDD
----------------------------------------------------------- */

include("config.php");

try {

    $pdo = new PDO(
        "mysql:host=" . HOST . ";dbname=" . BDD . ";charset=utf8",
        USER,
        PASS,
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_NUM
        )
    );

} catch (PDOException $e) {

    die("Erreur de connexion à la base de données.");

}


/* -----------------------------------------------------------
   INFORMATIONS SUR LA LIGNE
----------------------------------------------------------- */

$stmt = $pdo->prepare(
    "SELECT *
     FROM Line
     WHERE id = ?"
);

$stmt->execute(array($line));

$info_line = $stmt->fetch();

if (!$info_line) {
    die("Ligne incorrecte.");
}


/* -----------------------------------------------------------
   INFORMATIONS SUR LA STATION
----------------------------------------------------------- */

$stmt = $pdo->prepare(
    "SELECT *
     FROM stops
     WHERE stop_id = ?"
);

$stmt->execute(array($id));

$info_station2 = $stmt->fetch();

if (!$info_station2) {
    die("Station incorrecte.");
}


/* -----------------------------------------------------------
   COORDONNÉES DES STATIONS DE LA LIGNE
----------------------------------------------------------- */

$var_location = "[]";

$stmt = $pdo->prepare(
    "SELECT DISTINCT stop_id
     FROM stop_times
     WHERE metro = ?"
);

$stmt->execute(array($line));

$i = 0;
$last_station = null;

while ($donnees = $stmt->fetch()) {

    $i++;

    $stmt_station = $pdo->prepare(
        "SELECT *
         FROM stops
         WHERE stop_id = ?"
    );

    $stmt_station->execute(array($donnees[0]));

    $info_station = $stmt_station->fetch();

    if (!$info_station) {
        continue;
    }

    $last_station = $info_station;


    if ($i <= $info_line[1] && $dir == 2) {

        $var_location .= ", ['station', "
            . (float) $info_station[3]
            . ", "
            . (float) $info_station[4]
            . "]";

    } elseif ($i > $info_line[1] && $dir == 3) {

        $var_location .= ", ['station', "
            . (float) $info_station[3]
            . ", "
            . (float) $info_station[4]
            . "]";

    }

}


/* -----------------------------------------------------------
   CENTRE DE LA CARTE
----------------------------------------------------------- */

if (
    $info_station2[3] != "-180.0" &&
    $info_station2[4] != "-180.0"
) {

    $last_coordinate =
        (float) $info_station2[3]
        . ", "
        . (float) $info_station2[4];

} else {

    $last_coordinate = "0, 0";

}


/* -----------------------------------------------------------
   MISE À JOUR DE CONFIG.JS
----------------------------------------------------------- */

$jsonString = file_get_contents('static/js/config.js');

if ($jsonString !== false) {

    $data = json_decode($jsonString, true);

    if (is_array($data)) {

        $data['center.x'] = $info_station2[4];
        $data['center.y'] = $info_station2[3];

        $newJsonString = json_encode($data);

        file_put_contents(
            'static/js/config.js',
            $newJsonString
        );
    }
}

?>


    <!-- MAP -->

    <iframe
        src="vasile-api.php?<?php echo time(); ?>"
        style="height:82%; width:70%; position:absolute;">
    </iframe>


    <!-- Page Content -->

    <div class="container"
         style="margin-left:70%; width:30%;">

<?php

/* -----------------------------------------------------------
   BOUTON CHANGEMENT DE DIRECTION
----------------------------------------------------------- */

if ($dir == 2) {

    echo "<a href='read_map.php?id="
        . urlencode($id)
        . "&line="
        . urlencode($line)
        . "&dir=3'
        style='float:right; padding-top:18px;'>
        <button type='button' class='btn btn-default'>
            <i class='fa fa-exchange' aria-hidden='true'></i>
        </button>
        </a>";

} else {

    echo "<a href='read_map.php?id="
        . urlencode($id)
        . "&line="
        . urlencode($line)
        . "&dir=2'
        style='float:right; padding-top:18px;'>
        <button type='button' class='btn btn-default'>
            <i class='fa fa-exchange' aria-hidden='true'></i>
        </button>
        </a>";

}


/* -----------------------------------------------------------
   BOUTON RETOUR
----------------------------------------------------------- */

echo "<a href='load_map.php?line="
    . urlencode($line)
    . "&dir="
    . urlencode($dir)
    . "'
    style='float:right; padding-top:18px;'>

    <button type='button'
            class='btn btn-default'
            style='margin-right:10px;'>

        <i class='fa fa-chevron-left'
           aria-hidden='true'></i>

    </button>

    </a>";


/* -----------------------------------------------------------
   TITRE
----------------------------------------------------------- */

echo "<h2>Ligne "
    . htmlspecialchars($line, ENT_QUOTES, 'UTF-8')
    . "</h2>";

echo "<small>Dir : "
    . htmlspecialchars(
        $info_line[$dir],
        ENT_QUOTES,
        'UTF-8'
    )
    . "</small>";

echo "<hr>";

?>


        <!-- STATION -->

        <div class="panel panel-default">

            <div class="panel-heading">

                <a href="">
                    <?php
                    echo htmlspecialchars(
                        $info_station2[1],
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </a>

            </div>

        </div>


        <center>Prochains passages</center>

        <hr>


<?php

/* -----------------------------------------------------------
   PROCHAINS PASSAGES
----------------------------------------------------------- */

$endTime = strtotime(
    "+15 minutes",
    strtotime($heure)
);

$endTime = date('H:i', $endTime);


/*
 * On utilise maintenant une requête préparée
 * au lieu de construire directement la requête SQL.
 */

$stmt = $pdo->prepare(
    "SELECT DISTINCT *
     FROM stop_times
     WHERE stop_id = ?
       AND metro = ?
       AND arrival_time BETWEEN ? AND ?
     ORDER BY arrival_time ASC
     LIMIT 3"
);

$stmt->execute(
    array(
        $id,
        $line,
        $heure,
        $endTime
    )
);


$i = 0;

while ($donnees = $stmt->fetch()) {

    $i++;


    $datetime1 = new DateTime($heure);
    $datetime2 = new DateTime($donnees[0]);


    $dteDiff = $datetime2->diff($datetime1);

    $display = $dteDiff->format("%H:%I");


    if ($display == "00:00") {

        $display = "A Quai!";

    }


    echo "<i class='fa fa-exchange'
              aria-hidden='true'></i> "
        . htmlspecialchars(
            $display,
            ENT_QUOTES,
            'UTF-8'
        )
        . "<br>";

}


if ($i == 0) {

    echo "Plus de métro de prévu.";

}

?>


    </div>


    <!-- Footer -->

    <nav class="navbar navbar-inverse navbar-fixed-bottom"
         role="navigation">

        <div class="container">

            <div class="navbar-footer">

                <hr>

                <div class="row">

                    <div class="col-lg-12">

                        <p style="color:white;">

                            Copyright &copy; 2016-2017 |
                            Transit Map |

                            <small>
                                Open Data RATP.
                            </small>

                            <small>

                                <a href="#"
                                   onclick="alert('Nous ne garantissons pas la disponibilité ni lintégrité des données, nous ne pourrons pas être tenus responsable en cas derreurs, omissions, suppressions, retards, défaillances.')"
                                   style="float:right; color:white;">

                                    Précision des informations

                                </a>

                            </small>

                        </p>

                    </div>

                </div>

            </div>

        </div>

    </nav>


    <!-- jQuery -->

    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->

    <script src="js/bootstrap.min.js"></script>

    <!-- Configuration -->

    <script src="static/js/config.js?<?php echo time(); ?>"></script>

</body>

</html>