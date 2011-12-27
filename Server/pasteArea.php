<?php
	//DB
    include('database.php');
    //Helpers
    include('functions.php');
    include("session.php");
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
    "http://www.w3.org/TR/html4/strict.dtd"
    >
<html lang="en">
<head>
    <title>Comunio Analyser 5000 - Paste Area</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
    
    <?php
    $db = connect_to_db();
    
    //Auslesen des eingefügten Textes
    $text = $_POST["pasteArea"];
    //Eingabe cleanen..
    if(isset($text))
        $text = str_replace("'", "_", $text);
    
    if(isset($text)){
        //String anhand der Zeilenumbrüche trennen
        $array = explode("\r\n", $text);    
    }
    
    ?>
    <table>
        <tbody>
    <?php
    //Alles ausgeben
    for($i=0; $i<count($array);$i++){
        
        //String anhand der Tabs trennen (für jede Zeile)
        $arrayCell = explode("\t",$array[$i]);
        $Name = $arrayCell[0];
        $Verein = $arrayCell[1];
        $Marktwert = $arrayCell[2];
        $Marktwert = str_replace(".","",$Marktwert);
        $Punkte = $arrayCell[3];
        $Position = $arrayCell[4];
        //$Richtpreis = $arrayCell[5];
        
        //gibts diesen spieler schon in der DB?
        $PID = getPIDFromName($Name);
		
        if($PID == null) {
			//Spieler noch nicht DB.
			$query = "INSERT INTO Comunio_Player (Name, Club, UID) VALUES ('".$Name."','".$Verein."',1)";
			//TODO
			echo "Hey, du warst einkaufen! Gib mir bitte noch den Einkaufspreis für ".$Name.". !TODO!<br>";
			$result = do_query($query);
			if(!$result)
				echo"ERRRROOOOOOR @Neuen Spieler einfügen: ".mysql_error()."<br>";
        }else{
			//echo"Spieler ".$Name." schon in DB mit PID=".$PID."<br>";
		}
        
        //PID holen anhand des Namens
        $PID = getPIDFromName($Name);
        
        /*
        * Spielerwerte in die DB eintragen (wird auf jeden Fall gemacht)
        */
    	$round = $_POST["Round"];
        $time = date("Y-m-d");
        //wurde für den heutigen Tag schon ein Martwert und Punkte für diesen Spieler eingetragen?
        //Spieltag auslesen + Datum
        $query = "SELECT * FROM Comunio_Values WHERE PID = ".$PID." AND TIME = '".$time."'";
        $result = do_query($query);
        if($result) {
        	if(mysql_num_rows($result) <= 0) {
        		//not yet
        		$query = "INSERT INTO Comunio_Values (Time, Round, Points, Price, PID) VALUES ('".$time."',".$round.",".$Punkte.",".$Marktwert.",".$PID.")";
        		$result = do_query($query);
        		if($result)
        			echo "Spieler ".$Name." up to date now!<br>";
        	}else if(mysql_num_rows($result) > 0) {
        		//echo "Spieler ".$Name." ist für heute (".$time.") schon eingetragen!<br>";
        	}
        }else{
        	echo "DB ERROR @Check if Player has values for today! ".mysql_error()."<br>";
        }
        
        //Zellen durchlaufen
        //PRINT
         echo "<tr>";
         for($j=0; $j<count($arrayCell); $j++) {
            echo "<td>";
            echo $arrayCell[$j];
            echo"</td>";
        }
        //Zeilenumbruch
        echo"</tr>";
        
    }
    
    
    
    //while($row=mysql_fetch_row($result)){
        
    //}

    ?>
        </tbody>
    </table>
    
  
    <div id="pasteArea">
        <h3>Comunio Tabelle hier einfügen:</h3>
        <form action="#" method="POST">
            <textarea name="pasteArea" cols="100" rows="20"></textarea><br>
            Spieltag: 
            <select name="Round">
               <?php
               for($i=1;$i<=34;$i++){
                echo "<option>$i</option>";
               }
               ?>              
		    </select>
            <input type="submit" value="Absenden">
        </form>
    </div>
</body>
</html>