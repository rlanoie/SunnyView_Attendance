<?php
include_once '../config.php';
    $host = HOST;
    $dbname = DATABASE;
    // UTF-8 is a character encoding scheme 
    $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'); 
     
    //opens a connection to your database using the PDO library 
if(empty($db)){

    try 
    { 
       $db = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", USER, PASSWORD, $options); 
       $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
       $db->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
       $GLOBALS['connection'] = $db;
    } 
    catch(PDOException $ex) 
    { 
        die("Failed to connect to the database: " . $ex->getMessage()); 
    } 

    //configures PDO to throw an exception if an error is encounterd.
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
     
    // Configures PDO to return database rows from your database using an associative array.
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); 
     
    // This block of code is used to undo magic quotes.  
    if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) 
    { 
        function undo_magic_quotes_gpc(&$array) 
        { 
            foreach($array as &$value) 
            { 
                if(is_array($value)) 
                { 
                    undo_magic_quotes_gpc($value); 
                } 
                else 
                { 
                    $value = stripslashes($value); 
                } 
            } 
        } 
     
        undo_magic_quotes_gpc($_POST); 
        undo_magic_quotes_gpc($_GET); 
        undo_magic_quotes_gpc($_COOKIE); 
    } 
     
    // Content is encoded using UTF-8 
    header('Content-Type: text/html; charset=utf-8'); 
}

?>