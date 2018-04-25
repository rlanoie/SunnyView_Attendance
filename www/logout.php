<?php 

   
require '../includes/session.php';
sec_session_start();
// Unset all session values 
$_SESSION = array();
 
// get session parameters 
$params = session_get_cookie_params();

// Delete the actual cookie. 
setcookie(session_name(),
        '', time() - 42000, 
        $params["path"], 
        $params["domain"], 
        $params["secure"], 
        $params["httponly"]);

session_destroy();
session_write_close();

// Redirect to main page
header('location:../index');
?>

 // First we execute our common code to connection to the database and start the session 
    require("../includes/session.php"); 
     
    // We remove the user's data from the session 
    unset($_SESSION['user']); 
     
    // We redirect them to the login page 
    header("Location: ../index.html"); 
    die("Redirecting to: ../index.html");

//