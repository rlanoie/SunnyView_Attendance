<?php
include_once 'session.php';
include_once 'function.php';
include_once'../includes/dBconnect.php';


require_once "../random_compat-1.4.3/lib/random.php";
if (session_status() !== PHP_SESSION_ACTIVE) {sec_session_start();}
if($_SERVER["REQUEST_METHOD"]=="POST" AND isset($_POST["method"])){
	echo $_POST["method"]();
}

$frmID = $frmEmpLName = $frmEmpFName = "";
$frmIDRadio =$frmEmpLRadio = $frmEmpFRadio = "=";
$frmActiveRadio = "1";
$frmSortRadio = "0";
//values for Employee form filter fileds
if (isset($_POST['formFilterID'])) {
	$frmID = $_POST["formFilterID"];
}
if (isset($_POST['formFilterID_radio'])) {
	$frmIDRadio = $_POST["formFilterID_radio"];
}

//Employee Last Name
if (isset($_POST['formLastName'])) {
	$frmEmpLName = $_POST["formLastName"];
}
if (isset($_POST['formLastName_radio'])) {
	$frmEmpLRadio = $_POST["formLastName_radio"];
}

//Resident First Name
if (isset($_POST['formFirstName'])) {
	$frmEmpFName = $_POST["formFirstName"];
}
if (isset($_POST['formFirstName_radio'])) {
	$frmEmpFRadio = $_POST["formFirstName_radio"];
}

if (isset($_POST['active'])) {
	$frmActiveRadio = $_POST['active'];
}
if (isset($_POST['sort'])) {
	$frmSortRadio = $_POST['frmSort'];
}


		$input_Userinfo_Fields="";//userinfo Table
		$sqlUICount = 0; //track the number of fields for userinfo
		$input_Users_Fields="";//users table
		$sqlUCount = 0; //track the number of fields for userinfo
		$input_permission_Fields="";//users table
		$sqlPCount = 0; //track the number of fields for userinfo

		$sqlCount = 0;
		
		$sqlStatement="";

//--EMPLOYEE PROCESS ------------------------------------------------------*/
		/*add an employee to the tables*/
			function addEmployee(){
				$db = $GLOBALS['connection'];
        if(checkCredentials($db)==false){
          return 'credientalError';
        }
				return insertEmployee($db);
			}
		/*update an already existing employee in the tables*/
			function changeEmployee(){
				$db = $GLOBALS['connection'];
        if(checkCredentials($db)==false){
          return 'credientalError';
        }
				return updateEmployee($db);
			}
		/*Begin Transaction to add new employee data to the tables:
			user, userinfo, userpermission*/
			function insertEmployee($db){
				if(isset($_POST['submitModal_AddEmp'])){
					$db->beginTransaction();	
						$id = userTableInsert($db);
						userInfoInsert($id, $db);
						userpermissionInsert($id, $db);
					$db->commit();
					return 'Your changes have been recorded';
				}else{
					die('Invalid Entry');
				}
					
			}
		/*Begin Transaction to update employee data to the tables:
			user, userinfo, userpermission*/
			function updateEmployee($db){
					if(isset($_POST['submitModal_AddEmp'])){
						$sqlMethod = "INSERT INTO";
					}else{
						die('Invalid Entry');
					}
						$db->beginTransaction();	
							$id = userTableUpdate($db);
							userInfoUpdate($id, $db);
							userpermissionUpdate($id, $db);
						$db->commit();
					return 'Your changes have been recorded';
				}
		/*Deactivate Employee*/
			function deactivateEmployee(){
				$db = $GLOBALS['connection'];
        if(checkCredentials($db)==false){
          return 'credientalError';
        }
				userTableUpdate($db);
        return 'This user account has been deactivated';
			}
		/*Update the user's account settings*/
			function updateAccount(){
        $db = $GLOBALS['connection'];
        if(checkCredentials($db)==false){
          return 'credientalError';
        }
				$db = $GLOBALS['connection'];
				if(!empty($_POST) And (isset($_POST['username'], $_POST['password']))){
					$userID =  $_POST['userID'];
					$username = $_POST['username'];
					$results = getSalt($userID, $db);

					$oldPassword = hash('sha256', $_POST['password'] . $results['row'][0]['salt']);
					for($round = 0; $round < 65536; $round++){ 
           $oldPassword = hash('sha256', $oldPassword . $results['row'][0]['salt']); 
       		}
					
					if($results['row'][0]['password']==$oldPassword){
            
							$id = userTableUpdate($db);
							unset($results['row'][0]['salt']); 
							unset($results['row'][0]['password']); 
							if($id > 0){
								$_SESSION['user']['tempPassword'] = 0;
								//print($_SESSION['user']['tempPassword']);
								return true;
							}else{
								return false;
							}
					}else{
						return false;
					}
				}
			}

//--GET RESIDENT ------------------------------------------------------*/
			function getEmployeesActive($active_in, $db){
				$_POST['active'] = $active_in;
				return employeeList($db);
			}

			//get employee to mail too
			function getMailToUser_Reset(){
				$results = json_decode(getEmployees(), true);
				$count = $results['count'];
				
				$arrayResults = '';
				$email = '';
				$username = '';
				if($count == 1){
					$arrayResults = $results['row'];	
					$mailValues['sendEmail'] = $arrayResults[0]['email'];
					$mailValues['sendTo'] = $arrayResults[0]['userFirstName']. " ". $arrayResults[0]['userLastName'];
				}else{
					return false;
				}
				
				$return['mailValues'] = $mailValues;
				$return['arrayResults'] = $arrayResults;
				return $return;
			}
			//get username and email to user
			function getUsername(){
				$user = getMailToUser_Reset();
				$mailValues = $user['mailValues'];
				$arrayResults = $user['arrayResults'];
				$username = $arrayResults[0]['username'];
				
				$mailValues['body'] = 'Your username is: <b>'.$username.'</b>';
				$mailValues['altbody']  = 'Your username is:'.$username;
				sendMail($mailValues);
			}

			//return JSON employee list
			function getEmployees(){
				$db = $GLOBALS['connection'];
				$json = json_encode(employeeList($db));
				return $json;
			}
//EMPLOYEE PROCESSES ----------------------------------------------------------------
			function resetPassword(){
				$db = $GLOBALS['connection'];
				
				$user = getMailToUser_Reset();
				if($user == false){
					return false;
				}
				$mailValues = $user['mailValues'];
				$arrayResults = $user['arrayResults'];
				$_POST['frm_id'] = $arrayResults[0]['id'];
				$_POST['tempPassword'] = 1;
				
				if(isset($_POST['action']) AND ($_POST['action']!='')){
					$action = $_POST['action'];
					switch($action){
						case 'forgot':
							//generate temporary password
							$string = random_bytes (4);
							$tempP = bin2hex($string);
				
							$_POST['newPassword'] = $tempP;
							
							$temp = userTableUpdate($db);				
							$tempPassword = $tempP;
							
							$mailValues['body'] = 'Your temporary password is: <b>'.$tempPassword.'</b>';
							$mailValues['altbody']  = 'Your temporary password is:'.$tempPassword;							
							break;
						case 'locked':
							//generate temporary password
							$string = random_bytes (10);
							$tempPassword = bin2hex($string);
							
							$_POST['newPassword'] = $tempPassword;
							$temp = userTableUpdate($db);
							$mailValues['body'] = 'Your temporary password is: <b>'.$tempPassword.'</b>';
							$mailValues['altbody']  = 'Your temporary password is:'.$tempPassword;
							break;
						default:
							return false;
					}
				}else{
					return false;
				}
				sendMail($mailValues);
			}

//USER TABLE QUERIES----------------------------------------------------------------
	//--INSERT INTO USER TABLE ------------------------------------------------------*/
		/*Builds insert query for user table
			RETURNS 'success'
			RETURNS error message
			RETURNS die if database error*/		
			function userTableInsert($db){
				$sqlCount = 1;
				$insertBuilder= $insertValues=$statementBuilder='';
				$id="";
				if(isset($_POST['newUsername']) AND $_POST['newUsername'] != ''){
					$param[':newUsername'] = test_input($_POST['newUsername']);
					$insertBuilder = $insertBuilder.qryBuildCount("`username`", $sqlCount);
					$insertValues = $insertValues.qryBuildCount(":newUsername", $sqlCount);
					$sqlCount++;
				}else{
					die ('missing username');
				}
				if(isset($_POST['newPassword']) AND ($_POST['newPassword']!='')){
					$salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647)); 
					$password = hash('sha256', test_input($_POST['newPassword']) . $salt);
					for($round = 0; $round < 65536; $round++){ 
						$password = hash('sha256', $password . $salt); 
       		}
					$param[':newPassword'] = $password;
					$insertBuilder = $insertBuilder.qryBuildCount("`password`", $sqlCount);
					$insertValues = $insertValues.qryBuildCount(":newPassword", $sqlCount);
					$sqlCount++;
				
					$param[':newSalt'] = $salt;
					$insertBuilder = $insertBuilder.qryBuildCount("`salt`", $sqlCount);
					$insertValues = $insertValues.qryBuildCount(":newSalt", $sqlCount);
					$sqlCount++;
				
					$param[':tempPassword'] = test_input($_POST['tempPassword']);	
					$insertBuilder = $insertBuilder.qryBuildCount("`tempPassword`", $sqlCount);
					$insertValues = $insertValues.qryBuildCount(":tempPassword", $sqlCount);
					$sqlCount++;
					
				}else{
					die ('missing password');
				}
				if(isset($_POST['active']) AND ($_POST['active']!='')){
					$param[':active'] = test_input($_POST['active']);
					$insertBuilder = $insertBuilder.qryBuildCount("`active`", $sqlCount);
					$insertValues = $insertValues.qryBuildCount(":active", $sqlCount);
				}else{
					die('Uh, Oh! Something went wrong');
				}

				if(isset($_POST['newEmail']) AND ($_POST['newEmail'] != '')){
					$param[':newEmail'] = test_input($_POST['newEmail']);
					$insertBuilder = $insertBuilder.qryBuildCount("`email`", $sqlCount);
					$insertValues = $insertValues.qryBuildCount(":newEmail", $sqlCount);
					$sqlCount++;
				}
				if (($insertValues!='') AND ($insertBuilder!='')){
					$statementBuilder = 'INSERT INTO '.' `users`('.$insertBuilder.') VALUES ('.$insertValues.')';
				}else{
					die ('missing required content');
				}

				try{
					$stmt = $db->prepare($statementBuilder);	
					$stmt->execute($param);
					$id = $db->lastInsertId();
				}catch(PDOException $ex) {
					$db->rollBack();
					$test = $stmt->errorInfo();
					//Ignore duplicate entry attempts
					if($test[1]!="1062"){
						$GLOBALS['errorMsg'] = $GLOBALS['somethingWrong'];
    	 	  	die($GLOBALS['somethingWrong']); 	
					}else{
						die ($test[1] . " Username already exists");
					}	 
				}
				return $id;	
			}
	//--UPDATE USER TABLE ------------------------------------------------------*/
		/*Builds u[date query for user table
			RETURNS 'success'
			RETURNS error message
			RETURNS die if database error*/	
			function userTableUpdate($db){
				$sqlCount = 1;
				$updateBuilder = "";
				$id = $_POST['frm_id'];
				
				if(isset($_POST['newUsername']) AND $_POST['newUsername'] != ''){
					$param[':newUsername'] = test_input($_POST['newUsername']);
					$updateBuilder = $updateBuilder.qryBuildCount("`username` = :newUsername", $sqlCount);
					$sqlCount++;
				}
				if(isset($_POST['newPassword']) AND ($_POST['newPassword']!='')){
					$salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647)); 
					$password = hash('sha256', test_input($_POST['newPassword']) . $salt);
					for($round = 0; $round < 65536; $round++){ 
          	$password = hash('sha256', $password . $salt); 
      		}
					$param[':newPassword'] = $password;
					$updateBuilder = $updateBuilder.qryBuildCount("`password` = :newPassword", $sqlCount);
					$sqlCount++;
					
					$param[':newSalt'] = $salt;
					$updateBuilder = $updateBuilder.qryBuildCount("`salt` = :newSalt", $sqlCount);
					$sqlCount++;
					
					if(isset($_POST['tempPassword']) AND ($_POST['tempPassword'] !='')){
						$param[':tempPassword'] = test_input($_POST['tempPassword']);	
						$updateBuilder = $updateBuilder.qryBuildCount("`tempPassword` = :tempPassword", $sqlCount);
						$sqlCount++;
						
						$param[':tempExpiration'] = getTimeStamp();	
						$updateBuilder = $updateBuilder.qryBuildCount("`tempExpiration` = :tempExpiration", $sqlCount);
						$sqlCount++;
					}
				}
				if(isset($_POST['newEmail']) AND ($_POST['newEmail'] != '')){
					$param[':newEmail'] = test_input($_POST['newEmail']);
					$updateBuilder = $updateBuilder.qryBuildCount("`email` = :newEmail", $sqlCount);
					$sqlCount++;
				}
				if(isset($_POST['active']) AND ($_POST['active'] != '')){
					$param[':active'] = test_input($_POST['active']);
					$updateBuilder = $updateBuilder.qryBuildCount("`active` = :active", $sqlCount);
					$sqlCount++;
					if($_POST['active'] == 0){
						$param[':deactivateDate'] = test_input($_POST['deactivateDate']);
						$updateBuilder = $updateBuilder.qryBuildCount("`deactivateDate` = :deactivateDate", $sqlCount);
						$sqlCount++;
					}else if($_POST['active'] > 1){
						die ('Uh, Oh! Something went wrong.');
					}
				}
				
				if($updateBuilder!=''){
					
					$param[':id'] = $id;
					$statementBuilder = 'UPDATE `users` SET '.$updateBuilder.' WHERE `id` = :id';
					
					try{
						$stmt = $db->prepare($statementBuilder);
						$stmt->execute($param);
					}catch(PDOException $ex) {
						$db->rollBack();
						$test = $stmt->errorInfo();
						if($test[1]!="1062"){
							$GLOBALS['errorMsg'] = $GLOBALS['somethingWrong'];
     		  		die($GLOBALS['somethingWrong']); 	
						}else{
              die ($test[1] . " Constraint violation");
						}	 
					}
				}
				return $id;
			}

//USERINFO QUERIES----------------------------------------------------------------
	//--INSERT INTO USERINFO TABLE ------------------------------------------------------*/
		/*Builds insert query for userinfo table
			RETURNS 'success'
			RETURNS error message
			RETURNS die if database error*/		
			function userInfoInsert($id, $db){
				$sqlCount = 1;
				$insertBuilder= $insertValues=$statementBuilder='';
				//add id
				if($id){
					$param[':id'] = $id;
					$insertBuilder = qryBuildCount("`id`", $sqlCount);
					$insertValues = qryBuildCount(":id", $sqlCount);
					$sqlCount++;
				}else{
					die ('missing required content');
				}
				if(isset($_POST['newFirstName']) AND $_POST['newFirstName'] != ''){
					$param[':newFirst'] = test_input($_POST['newFirstName']);
					$insertBuilder = $insertBuilder.qryBuildCount("`userFirstName`", $sqlCount);
					$insertValues = $insertValues.qryBuildCount(":newFirst", $sqlCount);
					$sqlCount++;
				}else{
					die ('missing First Name');
				}
				if(isset($_POST['newLastName']) AND ($_POST['newLastName'] != '')){
					$param[':newLast'] = test_input($_POST['newLastName']);
					$insertBuilder = $insertBuilder.qryBuildCount("`userLastName`", $sqlCount);
					$insertValues = $insertValues.qryBuildCount(":newLast", $sqlCount);
					$sqlCount++;
				}
			
				if (($insertValues!='') AND ($insertBuilder!='')){
					$statementBuilder = "INSERT INTO ".' `userinfo`('.$insertBuilder.') VALUES ('.$insertValues.')';
				}else{
					die ('missing required content');
				}

				try{
					$stmt = $db->prepare($statementBuilder);
					$stmt->execute($param);
				}catch(PDOException $ex) {
					$db->rollBack();
					$test = $stmt->errorInfo();
					//Ignore duplicate entry attempts
					if($test[1]!="1062"){
						$GLOBALS['errorMsg'] = $GLOBALS['somethingWrong'];
	     	  	die($GLOBALS['somethingWrong']); 	
					}else{
						die ($test[1] . " Constraint violation");
					}	 
				}
				return 'success';
			}

	//--UPDATE INTO USERINFO TABLE ------------------------------------------------------*/
		/*Builds update query for userinfo table
			RETURNS error message
			RETURNS die if database error*/		
			function userInfoUpdate($id, $db){
				$sqlCount = 1;
				$updateBuilder ="";
				
				if(isset($_POST['newFirst']) AND $_POST['newFirst'] != ''){
					$param[':userFirst'] = test_input($_POST['newFirst']);
					$updateBuilder = qryBuildCount("`userFirstName` = :userFirst", $sqlCount);
					$sqlCount++;
					}
			
				if (isset($_POST['newLast']) AND $_POST['newLast'] != ''){
					$param[':userLast'] = test_input($_POST['newLast']);
					$updateBuilder = $updateBuilder.qryBuildCount("`userLastName` = :userLast", $sqlCount);
					$sqlCount++;
				}
				
				if($updateBuilder!=''){
					$param[':id'] = $id;
					$statementBuilder = 'UPDATE `userinfo` SET '.$updateBuilder.' WHERE `id` = :id';
					
					try{
						$stmt = $db->prepare($statementBuilder);
						$stmt->execute($param);
					}catch(PDOException $ex) {
						echo $ex;
						$db->rollBack();
						$test = $stmt->errorInfo();
						if($test[1]!="1062"){
							$GLOBALS['errorMsg'] = $GLOBALS['somethingWrong'];
	     	  		die($GLOBALS['somethingWrong']); 	
						}else{
							die ($test[1] . " Constraint violation");
						}	 
					}	
				}

			}
//USERPERMISSION QUERIES----------------------------------------------------------------
	//--INSERT INTO USERPERMISSION TABLE ------------------------------------------------------*/
			/*For each checkbox check if the box is checked then assign the appropriate sql query.
			For fields that have multiple checkboxes first check if the user is granted write permission, 
			if not check if the user has been granted read permission.  If neither are true enter 'none' 
			as the sql input value
			Builds insert query for userpermission table
			RETURNS 'success'
			RETURNS error message
			RETURNS die if database error*/	
			function userpermissionInsert($id, $db){
				$sqlCount = 1;
				$insertBuilder= $insertValues=$statementBuilder='';
				$defaultInput = 'none';
				//add id
				if($id){
					$param[':userId'] = $id;
					$insertBuilder = qryBuildCount("`userId`", $sqlCount);
					$insertValues = qryBuildCount(":userId", $sqlCount);
					$sqlCount++;
				}else{
					die ('missing required content');
				}
				
				//ATTENDANCE PERMISSION
				if(isset($_POST['attendance']) AND $_POST['attendance'] != ''){
					$param[':attendance'] = test_input($_POST['attendance']);
				}else{
					$param[':attendance'] = $defaultInput;
				}
				$insertBuilder = $insertBuilder.qryBuildCount("`attendance`", $sqlCount);
				$insertValues = $insertValues.qryBuildCount(":attendance", $sqlCount);
				$sqlCount++;
				
				//RESIDENT PERMISSION
				if(isset($_POST['writeResidents']) AND ($_POST['writeResidents']  == 'write')){
					$param[':residents'] = test_input($_POST['writeResidents']);
				}elseif(isset($_POST['readResident']) AND $_POST['readResident'] =='read'){
					$param[':residents'] = test_input($_POST['readResident']);
				}else{
					$param[':residents'] = $defaultInput;
				}
				$insertBuilder = $insertBuilder.qryBuildCount("`residents`", $sqlCount);
				$insertValues = $insertValues.qryBuildCount(":residents", $sqlCount);
				$sqlCount++;
				
				
				//ADMIN PERMISSION
				if(isset($_POST['writeadmin']) AND ($_POST['writeadmin']  =='write')){
					$param[':admin'] = test_input($_POST['writeadmin']);
				}else{
					$param[':admin'] = $defaultInput;
				}
				$insertBuilder = $insertBuilder.qryBuildCount("`admin`", $sqlCount);
				$insertValues = $insertValues.qryBuildCount(":admin", $sqlCount);
				$sqlCount++;			
				
				//USERS PERMISSION
				if(isset($_POST['writeUsers']) AND ($_POST['writeUsers']  =='write')){
					$param[':users'] = test_input($_POST['writeUsers']);
				}elseif(isset($_POST['readUsers']) AND $_POST['readUsers'] =='read'){
					$param[':users'] = test_input($_POST['readUsers']);
				}else{
					$param[':users'] = $defaultInput;
				}
				$insertBuilder = $insertBuilder.qryBuildCount("`users`", $sqlCount);
				$insertValues = $insertValues.qryBuildCount(":users", $sqlCount);
				$sqlCount++;
				
				if (($insertValues!='') AND ($insertBuilder!='')){
					$statementBuilder = "INSERT INTO ".' `userpermissions`('.$insertBuilder.') VALUES ('.$insertValues.')';
				}else{
					die ('missing required content');
				}

			try{
				$stmt = $db->prepare($statementBuilder);
				$stmt->execute($param);
			}catch(PDOException $ex) {
				$db->rollBack();
				$test = $stmt->errorInfo();
				//Ignore duplicate entry attempts
					if($test[1]!="1062"){
						$GLOBALS['errorMsg'] = $GLOBALS['somethingWrong'];
	     	  	die($GLOBALS['somethingWrong']); 	
					}else{
						die ($test[1] . " Constraint violation");
					}	 
			}
			return 'success';
		}

			function userpermissionUpdate($id, $db){
				$sqlCount = 1;
				$updateBuilder ="";
				$defaultInput = "none";
				//ATTENDANCE PERMISSION
				if(isset($_POST['attendance']) AND $_POST['attendance'] != ''){
					$param[':attendance'] = test_input($_POST['attendance']);
				}else{
					$param[':attendance'] = $defaultInput;
				}
				$updateBuilder = qryBuildCount("`attendance` = :attendance", $sqlCount);
				$sqlCount++;
				
				//RESIDENT PERMISSION
				if(isset($_POST['writeResidents']) AND $_POST['writeResidents'] =='write'){
					$param[':residents'] = test_input($_POST['writeResidents']);
				}elseif(isset($_POST['readResident']) AND $_POST['readResident'] =='read'){
					$param[':residents']  = test_input($_POST['readResident']);
				}else{
					$param[':residents']  = $defaultInput;
				}
				$updateBuilder = $updateBuilder.qryBuildCount("`residents` = :residents", $sqlCount);
				$sqlCount++;
				
				//ADMIN PERMISSION
				if(isset($_POST['writeadmin']) AND $_POST['writeadmin'] =='write'){
					$param[':admin'] = test_input($_POST['writeadmin']);
				}else{
					$param[':admin']   = $defaultInput;
				}
				$updateBuilder = $updateBuilder.qryBuildCount("`admin` = :admin", $sqlCount);
				$sqlCount++;
				
				//USERS PERMISSION
				if(isset($_POST['writeUsers']) AND $_POST['writeUsers'] =='write'){
					$param[':users'] = test_input($_POST['writeUsers']);
				}elseif(isset($_POST['readUsers']) AND $_POST['readUsers'] =='read'){
					$param[':users'] = test_input($_POST['readUsers']);
				}else{
					$param[':users']   = $defaultInput;
				}
				$updateBuilder = $updateBuilder.qryBuildCount("`users` = :users", $sqlCount);
				$sqlCount++;	
					
				if($updateBuilder!=''){
					$param[':id'] = $id;
					$statementBuilder = 'UPDATE `userpermissions` SET '.$updateBuilder.' WHERE `userID` = :id';
					try{
						$stmt = $db->prepare($statementBuilder);
						$stmt->execute($param);
					}catch(PDOException $ex) {
						//$db->rollBack();
						$test = $stmt->errorInfo();
						//Ignore duplicate entry attempts
						if($test[1]!="1062"){
						$GLOBALS['errorMsg'] = $GLOBALS['somethingWrong'];
	     	  	die($GLOBALS['somethingWrong']); 	
					}else{
							die ($test[1] . " Constraint violation");
						}	 
					}
				}
			}

//EMPLOYEE LISTS ----------------------------------------------------------------
		/*(ACTIVE) - Returns all active employees*/
			function employeeListAll($db){
	    //Default queary when the employee page is loaded.
  	  	$statementBuilder = "SELECT users.id, username, email, active, userFirstName, userLastName
														FROM users 
														INNER JOIN userinfo 
														ON users.id = userinfo.id
														WHERE active = 1
														ORDER BY userLastName, userFirstName ASC"; 
				try{
					$stmt = $db->prepare($statementBuilder);	
					$stmt->execute();
				}catch(PDOException $ex) {
					$test = $stmt->errorInfo();
				}
				$queryResults['row']= $stmt->fetchAll();
				$queryResults['count'] = $stmt->rowCount();
				return $queryResults;
			}
//USER LIST (FILTERED)----------------------------------------------------------------
		/*Filter the query of users based on inputs from th form
			RETURNS query of users*/
			function employeeList($db){
				//search variables
				$sql_SearchCriteria=0;
				$sql_Search = $field="";
				//Result variables
				$sqlCount = 0;
				$param = array();
					if (isset($_POST['formFilterID']) AND $_POST['formFilterID'] != ''){
						$frmEmpID = test_input($_POST['formFilterID']);	
						$frmIDRadio = $_POST['formFilterID_radio'];
						$field = "users.id";
						$sql_Search = qryWhereStatement('', $field, $frmIDRadio, ':userID1', $sqlCount);
						if($frmIDRadio== " LIKE "){
							$frmEmpID = "%".$frmEmpID."%";
						}
						$bind1 = array();
						array_push($bind1, ":userID1", $frmEmpID);
						array_push($param, $bind1);
						$sqlCount++;
					}
				
					if (isset($_POST['formLastName']) AND $_POST['formLastName'] != ''){	 
						$frmEmpLName = test_input($_POST['formLastName']);
						$frmEmpLRadio = $_POST['formLastName_radio'];
						$field = "userLastName";
						$sql_Search = $sql_Search . qryWhereStatement('', $field, $frmEmpLRadio, ':userLastName1', $sqlCount);
						if($frmEmpLRadio == " LIKE "){
							$frmEmpLName = "%".$frmEmpLName."%";
						}
						$bind1 = array();
						array_push($bind1, ":userLastName1", $frmEmpLName);
						array_push($param, $bind1);
						$sqlCount++;
					}
					if (isset($_POST['formFirstName']) AND $_POST['formFirstName'] != ''){
						$frmEmpFName = test_input($_POST['formFirstName']);
						$frmEmpFRadio = $_POST["formFirstName_radio"];
						$field = "userFirstName";
						$sql_Search = $sql_Search . qryWhereStatement('', $field, $frmEmpFRadio, ':userFirstName1', $sqlCount);
						if($frmEmpFRadio == " LIKE "){
							$frmEmpFName = "%".$frmEmpFName."%";
						}
						$bind1 = array();
						array_push($bind1, ":userFirstName1", $frmEmpFName);
						array_push($param, $bind1);
						$sqlCount++;
					}						 
					if (isset($_POST['active']) AND $_POST['active'] != ''){
						$frmActive = test_input($_POST['active']);
						$field = "active";
						$sql_Search = $sql_Search . qryWhereStatement('', $field, '=', ':active1', $sqlCount);
						$bind1 = array();
						array_push($bind1, ":active1", $frmActive);
						array_push($param, $bind1);
						$sqlCount++;
					}
					//added april 3, 2018
					if (isset($_POST['username']) AND $_POST['username'] != ''){
						$frmUsername = test_input($_POST['username']);
						$field = "username";
						$sql_Search = $sql_Search . qryWhereStatement('', $field, '=', ':username1', $sqlCount);
						$bind1 = array();
						array_push($bind1, ":username1", $frmUsername);
						array_push($param, $bind1);
						$sqlCount++;
					}
					if (isset($_POST['email']) AND $_POST['email'] != ''){
						$frmEmail = test_input($_POST['email']);
						$field = "email";
						$sql_Search = $sql_Search . qryWhereStatement('', $field, '=', ':email1', $sqlCount);
						$bind1 = array();
						array_push($bind1, ":email1", $frmEmail);
						array_push($param, $bind1);
						$sqlCount++;
					}
					
				
					//add the userpermissions table to the query results
					$permissoinSelect = "";
					$permissionJoin = "";
					if (isset($_POST['userPermission']) AND $_POST['userPermission'] !=''){
						$permissionTable = test_input($_POST['userPermission']);
						if($permissionTable == true){
							$permissoinSelect = "attendance, residents, admin, users,";							
							$permissionJoin = "INNER JOIN userpermissions ON users.id = userpermissions.userID";
						}
					}
				
					$whereClause = "";
					if($sql_Search !=""){
						$whereClause = "Where ";
					}
					$sortBy = sortSelection("0");
				
					if(isset($_POST['frmSort']) AND $_POST['frmSort']!=''){
						$frmSort = test_input($_POST['frmSort']);
						$sortBy = sortSelection($frmSort);						
					}

				
    			$statementBuilder = "SELECT users.id, username, email, active, userFirstName, userLastName, $permissoinSelect
															CONCAT(userLastName,  ', ', userFirstName) AS employee
															FROM users 
															INNER JOIN userinfo ON users.id = userinfo.id
															$permissionJoin
															$whereClause $sql_Search
															$sortBy";	
			//	print($statementBuilder);
    			try{
						$stmt = $db->prepare($statementBuilder);	
						if($sql_Search != ""){
							$paramLength = count($param);
							for($x = 0; $x < $paramLength; $x++){
								$stmt->bindValue($param[$x][0], $param[$x][1]);
							}
						}
						$stmt->execute();
					}catch(PDOException $ex) {
						$test = $stmt->errorInfo();
					}
					$queryResults['row']= $stmt->fetchAll();
					$queryResults['count'] = $stmt->rowCount();
				return $queryResults;
			}
		
			function sortSelection ($option){
				switch($option){
					case 0: //A-Z Last, First
						return "ORDER BY userLastName ASC, userFirstName ASC";
						break;
					case 1: //Z-A Last, First
    				return "ORDER BY userLastName DESC, userFirstName DESC";
						break;
				}
			}
