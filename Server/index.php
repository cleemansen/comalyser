<?php
header("link rel='stylesheet' type='text/css' href='css/home_grid.css'");
session_start();

include("functions.php");

include("database.php");
connect_to_db();

include('crawler.php');

date_default_timezone_set('Europe/Berlin');

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

$requirements = Array();

$get = $_GET['site'];
switch($get) {
    case null:
        $requirements['extJS'] = false;
        printHtmlHeader('Comalyser5000', $requirements);
        include('welcome.php');
        break;
    case 'home':
        $requirements['extJS'] = true;
        $requirements['home_grid'] = true;
        $requirements['home_change'] = false;
        printHtmlHeader('Home', $requirements);
        include('home.php');
        break;
    case 'synchronice':
        $requirements['extJS'] = false;
        printHtmlHeader('Sync', $requirements);
        include('synchronice.php');
        break;
    case 'signup':
        $requirements['extJS'] = false;
        printHtmlHeader('SignUp', $requirements);
        include('signup.php');
        break;
    case 'connect_comunio':
        $requirements['extJS'] = false;
        printHtmlHeader('Connect to Comunio', $requirements);
        include('connect_comunio.php');
        break;
    case 'kicker':
        $requirements['extJS'] = false;
        printHtmlHeader('Kicker', $requirements);
        include('kicker.php');
        break;
    case 'logout':
        $requirements['extJS'] = false;
        printHtmlHeader('Comalyser5000', $requirements);
        session_unset();
        //session_destroy();
        $_SESSION=array();
        include('welcome.php');
        break;
    default:
        $requirements['extJS'] = false;
        printHtmlHeader('Comalyser5000', $requirements);
        include('welcome.php');
        break;
}

printHtmlFooter();
?>