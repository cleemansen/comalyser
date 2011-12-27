<?php
function connect_to_db() {
    $mydb = mysql_connect( "wp1054551.wp083.webpack.hosteurope.de" , "dbu1054551" , "kick2go" );
    
    if(!$mydb)
        die ('Konnte keine Verbindung zur Datenbank herstellen');
    
    //echo "mydb == ".$mydb."<br>";
        
    $funktioniert = mysql_select_db ( "db1054551-kick2go");
    //echo "DB IS OPEN: ".$funktioniert."<br>";
    return $mydb;
}


function close_db(){
    echo "DB IS CLOSED<br>";
    mysql_close ();

}


function do_query($query){
        
    $res = mysql_query($query);
    
    if(mysql_errno() != 1062 && mysql_errno() != 0) {
        echo $query."<br>";
        echo "<span class='error'>".mysql_errno(). ": " .mysql_error() . "</span><br>";
    }
    
    return $res;
    
}

function db_free($result){
    echo "DB is now free!!<br>";
    mysql_free_result($result);

}

?>