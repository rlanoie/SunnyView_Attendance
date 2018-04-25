<?php
include_once 'session.php';

include_once 'commonMsg.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/Exception.php';
require '../PHPMailer/PHPMailer.php';
require '../PHPMailer/SMTP.php';
if (session_status() !== PHP_SESSION_ACTIVE) {sec_session_start();}



$errorMsg;
//GET DATE ----------------------------------------------------------------
		/*Returns the current date*/
			function getCurrentDate(){
				date_default_timezone_set('America/Los_Angeles');
				$now = date("Y-m-d");
				return $now;
			}
//GET TIMESTAMP----------------------------------------------------------------
		/*Returns the current date & time*/
			function getTimeStamp(){
				date_default_timezone_set('America/Los_Angeles');
				$now = date('Y-m-d G:i:s A');
				return $now;
			}
//ATTENDANCE STATUS UPDATE----------------------------------------------------------------
	/*Check if the attendance is still pending
		Return 'started', opened', closed', or 'no'
		*--No indicates that the attendance has not been started.--**/
		function attendanceStatus($qryDate, $db){	
			$filterdate = $qryDate;
			$query = "SELECT * 
								FROM `attendanceheader` 
								WHERE `date`= :date";
			$param[':date'] = $filterdate;
				try{     
					$stmt = $db->prepare($query);
					$result = $stmt->execute($param);   // Execute the prepared query. 
				}catch(PDOException $ex) { 
					die($GLOBALS['somethingWrong']); 
				}
			$row = $stmt->fetch();
			if($row){
				if($row['closed']!='0000-00-00'){
					return 'closed';
				}else{
					return 'started';
				}
			}else{
				return 'no';
			}
		}
//LOGIN ----------------------------------------------------------------
	/*Goes through the login credentials and all other requirements prior to 
		granting login privledges.
		RETURNS 'false' if account is locked
		RETURNS 'true' if login credentials are correct
		RETURNS DIE if database error*/
		function login($username, $token, $db) {
      //Get the credentials for the username
			$query = " SELECT id, username, password, salt, tempPassword, email, active 
									FROM users 
									WHERE username = :username 
									AND active = 1
									LIMIT 1"; 
      $query_params = array(':username' => $username);  
			
			try{ 
				$stmt = $db->prepare($query); 
				$stmt->bindParam(':username', $username);
				$result = $stmt->execute(); 
			} 
			catch(PDOException $ex){ 
				die($somethingWrong); 
			}  
			
      $login_ok = false; // This variable used to track successfull/unsuccessfull log-in  
     	$row = $stmt->fetch(); 
      $GLOBALS['errorMsg']=$GLOBALS['usernamePassword'];  //set global error to incorrect username/password msg.
		
			
      if($row) //Check if row exists.  If $row is false, then username is not correct. 
				if (checkbrute($row['id'], $db) == true){  // Account is locked 
					recordAttempt($row['id'], $db);        // record another failed attempt
					lockAccount($row['id'], $db);
          return 'locked';
				}else{ 
					// hash the submitted password so that it can be compared to the hashed version in the database. 
					$check_password = hash('sha256', $_POST['password'] . $row['salt']); 
          for($round = 0; $round < 65536; $round++) { 
						$check_password = hash('sha256', $check_password . $row['salt']); 
					} 
					
					// Using the password submitted by the user and the salt stored in the database, see if passwords match. 
					if($check_password === $row['password']) {
						$login_ok = true; 
						$_SESSION['postpassword'] = $check_password;
					} else{
						// Password is not correct record this attempt in the database
						recordAttempt($row['id'], $db);
						$login_ok = false; 
						return false;          
						}
					}
          // If the user logged in successfully, then send them to the dashboard page. 
					if($login_ok) { 
						//store users current browser information
						$user_browser = $_SERVER['HTTP_USER_AGENT'];
						
            $_SESSION['login_string'] = hash('sha512', $token. $user_browser . $row['id']);
            
            
						// store the $row array into the $_SESSION by and removing the salt and password values from it.  
						unset($row['salt']); 
						unset($row['password']); 
              
						//Store the user's data into the session at the index 'user'. 
						$_SESSION['user'] = $row;
						userPermissions($db);
            
						
						return 'true';
					} else { // Tell the user they failed 
            return 'false';
					} 
				}
//LOGIN - CHECKBRUTE FORCE ----------------------------------------------------------------
	/*Check that the number of login attempts in the last 30 minutes are not greater
		than 3 to prevent someone attempting to guess the password
		Returns 'true' if account is locked
		Returns 'false' if account is unlocked.*/
		function checkbrute($user_id, $db) {
    	$now = getTimeStamp(); // Get timestamp of current time 
    	//print("Now  " . $now . "<br>");
    	// All login attempts are counted from the past 2 hours. 
	    $valid_attempts = date('Y-m-d H:i:s',strtotime('-30 minutes'));

  	  $query = "SELECT time 
    	          FROM `login_attempts` 
      	        WHERE user_id = :userID 
        	      AND time > '$valid_attempts'";
        try
				{  
          $stmt = $db->prepare($query); 
          $stmt->bindParam(':userID', $user_id); 
          $stmt->execute(); 
        } 
        catch(PDOException $ex) 
        { 
          $GLOBALS['errorMsg'] = $GLOBALS['somethingWrong'];
          die($GLOBALS['somethingWrong']); 
        }
        $row = $stmt->fetch(); 
        $count = $stmt->rowCount();
        // If there have been more than 3 failed logins lock the account.
		//	print($count);
        if ($count >= 2) //count starts at 0
        {
        	$GLOBALS['errorMsg'] = $GLOBALS['LockecAccount'];  
          return true;
        } else {
          return false;
        }
			}
	/*Record failed login attempts.
		Adds a failure to the login failure table for tracking.*/
		function recordAttempt($user_id, $db){
  		$now = date('Y-m-d G:i:s A');
  		$quearyInsert = "INSERT INTO login_attempts(user_id, time) VALUES ('$user_id', '$now')";
  		$db->query($quearyInsert);
		}
		function lockAccount($userID_in, $db){
			$param[':userID'] = $userID_in;
			$param[':locked'] = '1';
			$statementBuilder = 'Update `users` SET `locked` = :locked WHERE `id` = :userID';
			
			try{
				$stmt = $db->prepare($statementBuilder);
				$stmt->execute($param);
			}catch(PDOException $ex){
				$test = $stmt->errorInfo();
				console.log($test);
			}
		}
		function getSalt($userID_in, $db){
			$userID = $userID_in;
			$query ="SELECT password, salt
								FROM users 
								WHERE id = :userID";
			try{
					//$queryResults = sqlQuery($query, $query_params, $db);      
					$stmt = $db->prepare($query);
					$stmt->bindParam(':userID', $userID);
					$result = $stmt->execute();   // Execute the prepared query.    
				}catch(PDOException $ex) { 
					return $GLOBALS['somethingWrong']; 
				}
				$queryResults['row'] = $stmt->fetchAll();
				$queryResults['count'] = $stmt->rowCount();
				//print($queryResults['count']);
			return $queryResults;
		}

//LOGIN CHECK ----------------------------------------------------------------
function checkCredentials($db){
  $token = $_POST['token'];
  $userID = $_POST['userID'];
	
  return login_check($token, $userID, $db);
}
  /*Page access
  Checks if a user is logged in and grants access to a page otherwise redirects the user to the index page.*/
    function pageAccess($token, $userID, $db){
      if(isset($_SESSION['postpassword'])){
		    //$password = $_SESSION['postpassword'];
		    if(login_check($token, $userID, $db) == false) {
			   header('location:../index');
		    }
	    }else {
			  header('location:../index');
      }
    }
	/*Check if the user is currently logged in
		RETURNS 'true' if the user is logged in
		RETURNS 'false' if the user is not logged in*/
		function login_check($token, $user_ID, $db){

    	if (isset($_SESSION['user']['id'], $_SESSION['user']['username'], $_SESSION['login_string'])) 
    	{
				// Get the variables from the user login
				
				$username = $_SESSION['user']['username'];
				$login_String = $_SESSION['login_string'];
				
				//get the user's current info
				$user_browser = $_SERVER['HTTP_USER_AGENT'];
        
				//$submitted_Password = $password;
				//query the user
				$query ="SELECT password, salt
								FROM users 
								WHERE id = :userID
								LIMIT 1";
        	
				$query_params = array(':userID' => $user_ID);    
				try{
					//$queryResults = sqlQuery($query, $query_params, $db);      
					$stmt = $db->prepare($query);
					$stmt->bindParam(':userID', $user_ID);
					$result = $stmt->execute();   // Execute the prepared query.    
				
				}catch(PDOException $ex) { 
					die($GLOBALS['somethingWrong']); 
				}
				$row = $stmt->fetch(); 
				
				if($row){ //If row exists then user exists.
					//$current_loginString = hash('sha512', $submitted_Password . $user_browser);
          $current_loginString = hash('sha512', $token . $user_browser . $user_ID);
          //$current_loginString = hash('sha512', $token);
				
				  if ($current_loginString === $_SESSION['login_string']){
					  // Logged In!!!! 
					  return true;
				  } else { // Not logged in            
					  return false;
				  }
        }else{
          return false;
        } 
			}else{
        return false;
      }
		}
//UERS PERMISSIONS----------------------------------------------------------------
	/*Checks the current user's permissions
		Sets the SESSION variable[permission] as an array*/
		function userPermissions($db){
  		$id = $_SESSION['user']['id'];
  
  		$query = "SELECT users.id, attendance, residents, admin, users
  						FROM users 
  						INNER JOIN userpermissions
          	  ON userpermissions.userID = users.id
            	WHERE users.id = :id
	            Limit 1";
  	  $query_params = array(':id' => $id);
  		print($query);
    	try{
      	$stmt = $db->prepare($query);
      	$stmt->bindParam(':id', $id);
      	$result = $stmt->execute();   // Execute the prepared query. 
    	}catch(PDOException $ex) 
    	{ 
      	die($GLOBALS['somethingWrong']); 
    	}
    	$row = $stmt->fetch();
    	$_SESSION['permissions'] = $row;
		}
//SQL WHERE GENERATOR ----------------------------------------------------------------
	/*Adds the appropriate values if the option 'like' is selected and if multiple fields are
		included in the sql statement
		RETURNS SQL STATEMENT*/
		function sqlWhereBuilder($frmInput, $Radio, $dbField){
	  	global $sql_SearchCriteria;
						
		  $wildCard="";
			$sql_SearchCriteria++;
			if($Radio==" LIKE "){	
		  	$wildCard="%";
	  	}else{
				$wildCard="";									
			}
			if($sql_SearchCriteria > 1)
			{//field = input
				return " AND $dbField$Radio'$wildCard$frmInput$wildCard'";
			}else{
				$sql_Field = "$dbField";
	  		return "$dbField$Radio'$wildCard$frmInput$wildCard'";
			}
		}
//SQL STATEMENT BUILDER----------------------------------------------------------------
		/*Keep track of the number of fields in the sql statement for comma placement*/
			function qryWhereStatement($conjunction ,$field, $symbol, $value, $sequentialCount){
				
				if($conjunction ==''){
					$conjunction='AND';
				}
				if($sequentialCount > 0){
					return " $conjunction $field $symbol $value";
				}else{
					return "$field $symbol $value";					
				}	
			}

//SQL STATEMENT BUILDER----------------------------------------------------------------
		/*Keep track of the number of fields in the sql statement for comma placement*/
			function qryBuildCount($value, $sqlCount){
				if($sqlCount > 1){
					return ", $value";
				}else{
					return "$value";					
				}	
			}
//TEST INPUT----------------------------------------------------------------
		/*Checks that the input is not malicous
			RETURNS the tested input*/
			function test_input($data) {
  			$data = trim($data); //remove white space
	  		$data = stripslashes($data); 
  			$data = htmlspecialchars($data);
	  		return $data;
			}



function sqlQuery($query, $Paramaters, $db){
  try{    
    $stmt = $db->prepare($query);
    $stmt->bindParam($Paramaters[0],$Paramaters[1]);
    
    $result = $stmt->execute();   // Execute the prepared query.    
  }catch(PDOException $ex) 
  { 
    echo $ex->getMessage();
    //die($GLOBALS['somethingWrong']); 
  }
   // If the user exists get variables from result.
  $row = $stmt->fetchAll(); 
  $queryResults ['row'] = $row;
  $queryResults ['stmt'] = $stmt;
  
 return $queryResults;
}

			/*Send an email to the specified recipients*/
			function sendMail($array_in){
				$mail = new PHPMailer(true);
				$mail->isSMTP();
				$mail->Host = 'smtp.office365.com';
				$mail->Port       = 587;
				$mail->SMTPSecure = 'tls';
				$mail->SMTPAuth   = true;
				$mail->Username = 'admin@generositylogic.com';
				$mail->Password = 'WaoeJeff17!';
				$mail->SetFrom('admin@generositylogic.com', 'GenLog Solutions');
				$mail->addReplyTo('admin@generositylogic.com', 'GenLog Solutions');
				$mail->XMailer = 'password reset';
				$mail->addAddress($array_in['sendEmail'], $array_in['sendTo']);
					//$mail->SMTPDebug  = 3;
					//$mail->Debugoutput = function($str, $level) {echo "debug level $level; message: $str";}; //$mail->Debugoutput = 'echo';
				$mail->IsHTML(true);
				$mail->Subject = 'Password Reset';
				$mail->Body    = $array_in['body'];
				$mail->AltBody = $array_in['altbody'];

				if(!$mail->send()) {
  	  		print 'Failure';
			    print 'Mailer Error: ' . $mail->ErrorInfo;
				} else {
		    	print 'Success';
				}
				
					
			}




  



?>