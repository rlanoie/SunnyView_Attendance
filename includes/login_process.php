

<?php 
include_once 'dBconnect.php';
include_once 'function.php';
include_once 'commonMsg.php';  
sec_session_start(); // Custom secure way of starting a PHP session.
  $GLOBALS['errorMsg']="";
  $loginError = "";
    // This if statement checks to determine whether the login form has been submitted 
    // If it has, then the login code is run, otherwise the form is displayed 
    if(!empty($_POST) And (isset($_POST['username'], $_POST['password']))) 
    {
      $username = $_POST['username'];
      /*$token = $_POST['token'];
      $processLogin = login($username, $token, $db);*/
      
      
      $token = md5(uniqid(rand(), true));
      $_SESSION['token'] = $token;
      
      $processLogin = login($username, $token, $db);
      
      if($processLogin=='true')
      {            
        header('Location: ../dashboard');
      } else if ($processLogin=='locked'){
        // Login failed 
        $GLOBALS['errorMsg'] = $usernamePassword;
        header('Location: ../login?login=error3');
      }else{
        // Login failed 
        $GLOBALS['errorMsg'] = $usernamePassword;
        header('Location: ../login?login=error1');
      }
    }else{
      
      $loginError=test_input($_GET["loginError"]);
      
      if($loginError == 'password'){
        header('Location: ../login?login=password');
      
      }else if($loginError == 'username'){
          header('Location: ../login?login=username');
      
      }else{
        header('Location: ../login');
      }
    }

?> 
