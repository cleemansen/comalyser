<?php
//Daten holen
$name = $_GET['name'];
$kickerinfos = getKickerInfoFromName($name);


printSession();

echo"<h2>Einzelanalyse von Spieler ".$name."</h2>";


echo"Gefundene Einträge: ".count($kickerinfos);




?>