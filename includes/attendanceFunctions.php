<?php
include_once '../includes/session.php';
include_once'../includes/dBconnect.php';
include_once 'function.php';
include_once '../includes/residentFunctions.php';
include_once 'commonMsg.php';
if (session_status() !== PHP_SESSION_ACTIVE) {sec_session_start();}

if($_SERVER["REQUEST_METHOD"]=="POST" AND isset($_POST["phpFunction"])  AND isset($_POST["method"])){
	if($_POST["phpFunction"] == 'attendanceFunctions'){
		echo $_POST["method"]();
	}
}

//$GLOBALS['countAttendance']=$GLOBALS['countAttendance']="";
date_default_timezone_set('America/Los_Angeles');
$today = date("Y-m-d");

$frmDate= "";
if (empty($_POST['frmDate'])) {
    $frmDate = $today;
} else {
	$frmDate = $_POST["frmDate"];
}

//GET ATTENDANCE DATE----------------------------------------------------------------
		/*Establish the date to use for attendance functions.
			Either the date on the form or default to the current date.
			RETURNS a date*/
			function getAttendanceDate(){
				if(isset($_POST['postDate'])){
					$thisDate = json_decode($_POST['postDate']);
				}else if(isset($_POST['frmDate'])){
					$thisDate = $_POST['frmDate'];
				}else{
					date_default_timezone_set('America/Los_Angeles');
					$thisDate = getCurrentDate();
				}
			return $thisDate;
			}
//-GET ATTENDANCE STATUS UPDATE---------------------------------------------------------------
		/*Check for the current status of the attendance for the 
			currently displayed date on the form
			RETURNS STATUS of attendance*/
			function getAttendanceStatusUpdate($date_in){
				$newDate = $date_in;
				//print($newDate);
				$db = $GLOBALS['connection'];
				$result = attendanceStatus($newDate, $db);
				return $result;
			}
			
			function returnAttendanceStatus(){
				$dateCheck;
				if(isset($_POST['attendanceDate'])){
					$dateCheck = $_POST['attendanceDate'];
					echo $json = json_encode(getAttendanceStatusUpdate($dateCheck));
			}else{
					$response = 'failed';
					echo $json = json_encode($response);
				}
			}
//GET ABSENCE BY DATE - returns query of absenceByDate
			function getAbsenceByDate(){
				$db = $GLOBALS['connection'];
				echo $json = json_encode(absenceByDate($db));
			}


//ATTENDANCE CODES -----------------------------------------------------------------
		/*Attendance Code All
			RETURNS all records from attendanceCode table*/
			function attendanceCode($db){
    		$query_UserFilter = "SELECT id, code, displayOptions
              						FROM attendanceCode 
              						ORDER BY code ASC"; 

	    	$paramaters[0]="";
  	  	$paramaters[1]="";
    		$queryResults = sqlQuery($query_UserFilter,$paramaters,$db);
				return $queryResults;
			}
		/*Attendance Code Display Filter
			Filters out attendance codes that are not being used.*/
			function attendanceCode_Dropdown($db){
    		$query_UserFilter = "SELECT id, code, displayOption
              						FROM attendanceCode 
													WHERE displayOption = 1
              						ORDER BY code ASC"; 

	    	$paramaters[0]="";
  	  	$paramaters[1]="";
    		$queryResults = sqlQuery($query_UserFilter,$paramaters,$db);
				return $queryResults;				
			}

//DATE CHANGE ----------------------------------------------------------------
		/*Processes data when the form date changes.  
			Gets the attendance list for the new date on the form.
			RETURNS OBJECT ARRAY; QUERY = the attendance list, STATUS = progress of the attendance list (started, in-progress, completed)*/
			function dateChange(){
				if(isset($_POST['frmDate'])){
					$newDate = json_decode($_POST['frmDate']);
				}else{
					echo 'failure';
				}
				$db = $GLOBALS['connection'];
				$results['query'] = attendanceList($newDate, $db);			
				$results['status'] = getAttendanceStatusUpdate($newDate);
				$json = json_encode($results);
				echo $json;
			}

//-ATTENDANCE START----------------------------------------------------------------
		/*insert new date into attendanceheader table.
			Catch error if date already exists
			RETURNS started = success
			RETURNS error 1062 = duplicate entries
			RETURNS die(message)*/
			function startAttendance(){
				if(isset($_POST['frmDate'])){
						$dateFilter = test_input($_POST['frmDate']);
				}else{
					return false;
				}
				$status = getAttendanceStatusUpdate($dateFilter);
				if ($status == 'started'){
					return 'duplicate';
				}else if ($status == 'closed'){
					return $status;
				}else{
					$query_attendanceDetail = "INSERT INTO `attendanceheader`(`date`) VALUES (:attendDate)";
					$param[':attendDate'] = $dateFilter;
					
					$db = $GLOBALS['connection'];
					try{
						// Prepare statement
  					$stmt = $db->prepare($query_attendanceDetail);
						$result=$stmt->execute($param);		
					}catch(PDOException $ex){
						//Ignore duplicate entry attempts		
						$dupErr = $stmt->errorInfo(); 
						if($dupErr[1]=="1062"){
							return('Attendance has already been started for today.');
						}else{
	     				return $GLOBALS['somethingWrong']; 				
						}
					}
					return('started');
				}
			}
//-ATTENDANCE CLOSE----------------------------------------------------------------
		/*Closes the attendance for the date specified
			Returns - passes the return value of closeAttendance back to the call.*/
			function closeThisDay(){
				if(isset($_POST['frmDate'])){
						$newDate = test_input($_POST['frmDate']);
					}else{
						return false;
					}
				$status = getAttendanceStatusUpdate($newDate);
				if($status=='started'){
					
					$db = $GLOBALS['connection'];
					return closeAttendance($newDate, $db);
				}
			}
			/*insert new date into attendanceheader table.
			Catch error if date already exists
			Returns 'closed' if successful
			Returns error 1062 if duplicate entry
			Returns DIE if database failure*/
			function closeAttendance($closeDate, $db){
				//need to get attendance status first
				date_default_timezone_set('America/Los_Angeles');
				$currentDate = date("Y-m-d");
				$userID = $_POST['userid'];
				$closingDate  = $closeDate; 
	
				$query_attendanceDetail = "UPDATE `attendanceheader` 
																	SET `closed`= :currDate, `closedUserID`= :userID 
																	WHERE `date`= :closingDate";

				$query_params1 = array(':closingDate' => $closingDate); 
				$query_params2 = array(':userID' => $userID); 
				$query_params3 = array(':currDate' => $currentDate); 
				try{
					// Prepare statement
  				$stmt = $db->prepare($query_attendanceDetail);
					$stmt->bindParam(':closingDate', $closingDate);
					$stmt->bindParam(':userID', $userID);
					$stmt->bindParam(':currDate', $currentDate);
					$result=$stmt->execute();
		
				}catch(PDOException $ex){
					//Ignore duplicate entry attempts		
					$dupErr = $stmt->errorInfo(); 
		
					if($dupErr[1]=="1062"){
						return($dupErr[1]);
					}else{
						$GLOBALS['errorMsg'] = $GLOBALS['somethingWrong'];
     				die($GLOBALS['somethingWrong']); 				
					}
				}
				return('closed');
			}

//-ATTENDANCE TAKING----------------------------------------------------------------
		/*Add a new person to the attendance list then gets all relevant fields to be added to the 
			#Result div
			ECHO'S JSON_ENCODE attendee array' or error message*/
			function addAttendee(){
				$db = $GLOBALS['connection'];
        if(checkCredentials($db)==false){
          return 'credentialError';
        }
				$fobExists = filteredQuery($db);//lookup resident ID
  			/*check if a resident is assigned to the current key fob #
			  If not return an error message.*/
		  	if($fobExists['count'] == 1){
	  			$residentID = $fobExists['row'][0]['id'];
	 				/*Set $_POST['formResID'] to id retrieved when checking 
					if key fob is assigned to a current resident.  
  				This will be used when inerting a new attendance record.*/
				  $_POST['formResID'] = $residentID;  
			  	$attendeeAdded = insertAttendee($db);
						
	  	  	if($attendeeAdded=='true'){
		   			$attendee['array'] = getAttendee($db);
		  			$attendee['message'] = 'success';
	  				$json = json_encode($attendee);
  					echo $json;
			  	}else{
			 			$status['message'] = $attendeeAdded;
		  			$json = json_encode($status);
	  				echo $json;
  				}
			  }else{
  				$status['message'] = 'fob does not exist';
	  			$json = json_encode($status);
		  		echo $json;
			 	}
			}
		/*Used to add verified absences to the roll call list.*/
			function absenceApproval(){
				$db = $GLOBALS['connection'];
        if(checkCredentials($db)==false){
          return 'credentialError';
        }
				$data = $_POST['approved'];//array of absences for the current date.
				$date = test_input($_POST['frmDate']);
				//check if attendance for this date is started.
				$status = attendanceStatus($date, $db);
				if($status == 'no'){
					die ('Unable to verify absence!  Attendance has not been started.  Please start the attendance and try again. ');	
				} else if ($status == 'started'){
					
					//for each resident that is confirmed absent loop through array to add them to the attendance roster.
					
					for($x = 0; $x < count($data); $x++){
						$_POST['frmDate'] = $date;
						$_POST['userID'] = test_input($data[$x]['formUserID']);
						$_POST['formResID'] = test_input($data[$x]['residentID']);
						$_POST['formCode'] = test_input($data[$x]['reasonID']);
						
						$attendeeAdded = insertAttendee($db);
            
						if($attendeeAdded=='true'){
							$attendee = getAttendee($db);
              if($attendee['count'] == 0){
                return 'Error';
              }else{
                $return['row'][$x] = $attendee['row'][0];  
              }
						}
					}
					
					$json = json_encode($return);
					echo $json;
					
				} else if ($status == 'closed'){
					die ('Attendance closed!  Unable to make changes to this date');
				}else{
					die ($GLOBALS['somethingWrong']);	
				}
			}
		/*Add a new person to the attendance list.
			RETURNS true = successful.
			RETURNS error 1062 = duplicate entries
			RETURNS die(message)*/
			function insertAttendee($db){  //FORMERLY addAttendance
				
				$timeStamp = getTimeStamp();
				//NEW VARIABLES
				//$frmDate = $timeStamp;//default is today's date
				if (isset($_POST['frmDate']) AND $_POST['frmDate'] != ''){
					$param[':frmDate'] = test_input($_POST['frmDate']);
				}else{
					return $GLOBALS['somethingWrong'];
				}
				$param[':timestamp'] = $timeStamp;
				
				if (isset($_POST['userID']) AND $_POST['userID'] != ''){
					$param[':userID'] = test_input($_POST['userID']);
				}else{
					return $GLOBALS['somethingWrong'];
				}
				if (isset($_POST['formResID']) AND $_POST['formResID'] != ''){
					$param[':resID'] = test_input($_POST['formResID']);
				}else{
					return $GLOBALS['somethingWrong'];
				}
				
				if (isset($_POST['formCode']) AND $_POST['formCode'] != ''){
					$param[':code'] = test_input($_POST['formCode']);
				}else{
					return $GLOBALS['somethingWrong'];
				}
				$query_attendanceUpdate = "INSERT INTO `attendance` (`date`, `timestamp`, `UserID`, `ResID`, `code`)
																	 VALUES(:frmDate, :timestamp, :userID, :resID, :code)";	
				
				try{
	  	  	$stmt = $db->prepare($query_attendanceUpdate);
					$stmt->execute($param);
				}catch(PDOException $ex) { 
					
					$test = $stmt->errorInfo();
					if($test[1]=="1452"){//attendance not started
						return 'You must click start before you can record the attendance!'; 	
					}elseif($test[1]!="1062"){
						$GLOBALS['errorMsg'] = $GLOBALS['somethingWrong'];
     	  		return 'Duplicate';
					}else{
						return $GLOBALS['somethingWrong'];
					}
     		}
			return true;
		}
		/*Get a single resident.  Returns a single resident attendance information for the specified date.
			RETURNS array for a single attendee on a specified date*/
			function getAttendee($db){
				if(isset($_POST['frmDate'])){
					$frmDate = test_input($_POST['frmDate']);
					$param[':date'] = $frmDate;
				}else{
					return $GLOBALS['somethingWrong'];
				}
				 
				
				if (isset($_POST['formResID']) AND $_POST['formResID'] != ''){
					$param[':resID'] = test_input($_POST['formResID']);
				}else{
					return $GLOBALS['somethingWrong'];
				}	
				
				$queryAttendanceRecords = "SELECT attendance.date, timestamp, userFirstName, userLastName, ResFName, ResLName, ResID, attendanceCode.code,
						CONCAT(ResLName, ', ', ResFName) AS resident,
						CONCAT(userFirstName, ' ', userLastName) AS employee
						FROM attendance
						INNER JOIN userinfo ON userinfo.id = attendance.UserID
						INNER JOIN residents ON residents.id = attendance.ResID
						INNER JOIN attendanceCode ON attendanceCode.id = attendance.code
						WHERE date = :date AND ResID = :resID";
				try{
					$stmt = $db->prepare($queryAttendanceRecords);
					$result = $stmt->execute($param);   // Execute the prepared query.    
				}catch(PDOException $ex) { 
					die($GLOBALS['somethingWrong']); 
				}
				$queryResults['row'] = $stmt->fetchAll();
				$queryResults['count'] = $stmt->rowCount();
				return $queryResults;
			}
		/*Returns an array of all the attendees for the date posted*/
			function getAttendanceList(){
				$db = $GLOBALS['connection'];
				$filterDate = test_input($_POST['attendanceDate']);
				echo json_encode(attendanceList($filterDate, $db));
				//return attendanceList($filterDate, $db);
			}
		/*Creates a list of residents who have been 
			accounted for from the date selected
			Returns array of all residents accounted for.*/
			function attendanceList($filterDate, $db){
				$queryAttendanceRecords = "SELECT attendance.date, timestamp, userFirstName, userLastName, ResFName, ResLName, ResID, attendanceCode.code,
						CONCAT(ResLName, ', ', ResFName) AS resident,
						CONCAT(userFirstName, ' ', userLastName) AS employee
						FROM attendance
						INNER JOIN userinfo ON userinfo.id = attendance.UserID
						INNER JOIN residents ON residents.id = attendance.ResID
						INNER JOIN attendanceCode ON attendanceCode.id = attendance.code
						WHERE date = :date
						ORDER BY timestamp ASC";
				
 					try{
						$stmt = $db->prepare($queryAttendanceRecords);
						$stmt->bindParam(':date', $filterDate);
						$result = $stmt->execute();   // Execute the prepared query.    
						}catch(PDOException $ex) 
						{ 
							die($GLOBALS['somethingWrong']); 
						}
				$queryResults['row']= $stmt->fetchAll();
				$queryResults['count'] = $stmt->rowCount();
				return $queryResults;
			}
			function getDropdownResidents($date_in, $db){
				$_POST['frmDate'] = $date_in;
				return attendanceMissing($db);
			}
		/*Gets and returns an array of all residents who are missing from the attendance roster*/
			function getMissingResidents(){
				$db = $GLOBALS['connection'];
				echo json_encode(attendanceMissing($db));
			}
		/*Creates an array of all the residents who are missing from the absence roster for the date specified
			Does not include residents who are deactivated prior to the specified date 
			or activated after the specified date*/
			function attendanceMissing($db){
				if(isset($_POST['frmDate'])){
					$filterDate = test_input($_POST['frmDate']);
				}
				
					$query = "SELECT  `id` , CONCAT( ResLName, ', ',ResFName ) AS resident
										FROM  `residents` 
										WHERE deactivateDate >= :filterDate1 
										AND CreatedOn <= :filterDate4
										AND NOT EXISTS (SELECT * FROM  `attendance` 
																			WHERE attendance.date =  :filterDate2
																			AND attendance.ResID = residents.id)
																			
										Or deactivateDate IS NULL AND NOT EXISTS (SELECT * FROM  `attendance` 
																															WHERE attendance.date =  :filterDate3
																															AND attendance.ResID = residents.id)
										AND CreatedOn <= :filterDate5
										ORDER BY ResLName, ResFName ASC ";
				
				
				try{
					$stmt = $db->prepare($query);
					$stmt->bindParam(':filterDate1', $filterDate);
					$stmt->bindParam(':filterDate2', $filterDate);
					$stmt->bindParam(':filterDate3', $filterDate);
					$stmt->bindParam(':filterDate4', $filterDate);
					$stmt->bindParam(':filterDate5', $filterDate);
					$result = $stmt->execute();
				}catch(PDOException $ex) {
					print($ex);
					die($GLOBALS['somethingWrong']); 
				}
					$queryResults['row'] = $stmt->fetchAll();
					$queryResults['count'] = $stmt->rowCount();
					return $queryResults;
			}
		
//-ABSENCE RECORDING----------------------------------------------------------------
		/*process a new add request for an absence on the absence page.
			Checks the add request against of pre-recorded absences to verify request does
			not overlap with previously documented absences.
			SUCCESS - Returns SUCCESS and the results of all the absences.
			FAILURE - Returns DUPLICATE Error and an array of all conflicting absences for the specified person.*/
			function addNewAbsence(){
				$db = $GLOBALS['connection'];
        if(checkCredentials($db)==false){
          return 'credentialError';
        }
				$duplicateCheck = getResidentAbsence($db);
				if($duplicateCheck['count']>0){
					$duplicateCheck['error'] = "duplicate";
					$json = json_encode($duplicateCheck);
					echo $json;
				}else{
					$verifySuccess = insertAbsenceRecord($db);
          //print($verifySuccess);
					if($verifySuccess == 1){
						$results = defaultAbsenceQuery($db);
						$results['error'] = "success";
						$json = json_encode($results);
						echo $json;
					}
				}
			}
			function getUpdateAbsence(){
				$db = $GLOBALS['connection'];
        if(checkCredentials($db)==false){
          return 'credentialError';
        }
				$duplicateCheck = getResidentAbsence($db);				
				if($duplicateCheck['count']>0){
					$duplicateCheck['error'] = "duplicate";
					$json = json_encode($duplicateCheck);
					echo $json;
				}else{
					$verifySuccess = updateAbsence($db);
						if($verifySuccess == 1){
							$results = defaultAbsenceQuery($db);
							$results['error'] = "success";
							$json = json_encode($results);
							echo $json;
						}
				}
			}
			function getDeleteAbsence(){
				$db = $GLOBALS['connection'];
				if(checkCredentials($db)==false){
          return 'credentialError';
        }
				$verifySuccess = updateAbsence($db);
				//print($verifySuccess);
				if($verifySuccess == 1){
					$results = defaultAbsenceQuery($db);
					$json = json_encode($results);
							echo $json;
				}
			}
		/*Get a single resident's absent entries based off starting and ending dates
			Used to check for absence duplications.*/
			function getResidentAbsence($db){
				$queryBuilder="";
				$qryUpdate_NewDate_LeavingComparision1 ="";
				$qryUpdate_NewDate_LeavingComparision2 = "";
				$qryUpdate_NewDate_LeavingComparision3 = "";
				$qryUpdate_NewDate_LeavingComparision4 = "";
				$qryUpdate_NewDate_LeavingComparision5 = "";
				$qryUpdate_NewDate_LeavingComparision6 = "";
				$qryUpdate_NewDate_LeavingComparision7 = "";
				$qryUpdate_NewDate_LeavingComparision8 ="";
			
				
				if (isset($_POST['residentID']) AND $_POST['residentID'] != ''){
					$resID = test_input($_POST['residentID']);
				}								

				//ABSENCE TABLE VALUES
				if (isset($_POST['formBeginning']) AND $_POST['formBeginning'] != ''){
					$newLeaving = test_input($_POST['formBeginning']);
					$param[':resID1'] = $resID;
					$param[':newDateL1'] = $newLeaving;
					
					$param[':resID2'] = $resID;
					$param[':newDateL2'] = $newLeaving;
					
					$param[':resID3'] = $resID;
					$param[':newDateL3'] = $newLeaving;
					$param[':newDateL4'] = $newLeaving;
					
					if(isset($_POST['formOldBeginning']) AND ($_POST['formOldBeginning'] != '')){
						$oldBeginning = test_input($_POST['formOldBeginning']);
						$param[':oldBeginning1'] = $oldBeginning;
						$param[':oldBeginning2'] = $oldBeginning;
						$param[':oldBeginning10'] = $oldBeginning;
						$qryUpdate_NewDate_LeavingComparision1 = " AND Leaving <> :oldBeginning1";
						$qryUpdate_NewDate_LeavingComparision2 = " AND Leaving <> :oldBeginning2";
						$qryUpdate_NewDate_LeavingComparision8 = " AND Leaving <> :oldBeginning10";
					}
					
					/*Return the query results for any records exist that do not match the following rules:
						-NewLeaving date cannot equal another leaving date for the same person unless it is for an absence that is being updated.
						-NewLeaving date cannot equal another returning date for the same person
						-NewLeaving date cannot fall between another leaving and returning date for the same person unless it is for an absence that is being updated.*/
					$queryBuilder  = "resID = :resID1 AND Leaving = :newDateL1 AND deletedDate = '0000-00-00 00:00:00' $qryUpdate_NewDate_LeavingComparision1
														OR resID = :resID2 AND Returning = :newDateL2 AND deletedDate = '0000-00-00 00:00:00' $qryUpdate_NewDate_LeavingComparision8
														OR resID = :resID3 AND Leaving <= :newDateL3 AND Returning >= :newDateL4 AND deletedDate = '0000-00-00 00:00:00' $qryUpdate_NewDate_LeavingComparision2";
					
				}
				
				
				/*-Cannot equal another leaving date*/
				if (isset($_POST['formReturning']) AND $_POST['formReturning'] != ''){
					$newReturning = test_input($_POST['formReturning']);
					$param[':resID4'] = $resID;
					$param[':newDateR1'] = $newReturning;
					
					$param[':resID5'] = $resID;
					$param[':newDateR2'] = $newReturning;
					
					$param[':resID6'] = $resID;
					$param[':newDateR3'] = $newReturning;
					$param[':newDateR4'] = $newReturning;
					
					$param[':resID7'] = $resID;
					$param[':newDateL5'] = $newLeaving;
					$param[':newDateR5'] = $newReturning;
					
					$param[':resID8'] = $resID;
					$param[':newDateL6'] = $newLeaving;
					$param[':newDateR6'] = $newReturning;
					
					if(isset($_POST['formOldBeginning']) AND ($_POST['formOldBeginning'] != '')){
						$oldBeginning = test_input($_POST['formOldBeginning']);
						$param[':oldBeginning3'] = $oldBeginning;
						$qryUpdate_NewDate_LeavingComparision3 = " AND Leaving <> :oldBeginning3";
						
						$param[':oldBeginning4'] = $oldBeginning;
						$qryUpdate_NewDate_LeavingComparision4 = " AND Leaving <> :oldBeginning4";
						
						$param[':oldBeginning5'] = $oldBeginning;
						$qryUpdate_NewDate_LeavingComparision5 = " AND Leaving <> :oldBeginning5";
						
						$param[':oldBeginning6'] = $oldBeginning;
						$qryUpdate_NewDate_LeavingComparision6 = " AND Leaving <> :oldBeginning6";
						
						$param[':oldBeginning7'] = $oldBeginning;
						$qryUpdate_NewDate_LeavingComparision7 = " AND Leaving <> :oldBeginning7";
					}
					
					/*Return the query results for any records exist that do not match the following rules:
						The same person cannot have another record that falls outside of these rules: 
						-A new leaving date cannot equal another returning date
						-A new Returning date cannot equal another returning date
						
						-A new returning date cannot fall between a date range for another absence.
						-Another absence's dates range cannot fall inside of the date range for the new absence
						-Another absence's leaving date cannot fall between a newLeaving date and a newReturning date (unless this is for an absence update)*/
					
					$queryBuilder  = $queryBuilder." OR resID = :resID4 AND Leaving = :newDateR1 AND deletedDate = '0000-00-00 00:00:00' $qryUpdate_NewDate_LeavingComparision3
																					OR resID = :resID5 AND Returning = :newDateR2 AND deletedDate = '0000-00-00 00:00:00' $qryUpdate_NewDate_LeavingComparision4
																					OR resID = :resID6 AND Leaving <= :newDateR3 AND Returning >= :newDateR4 AND deletedDate = '0000-00-00 00:00:00' $qryUpdate_NewDate_LeavingComparision5
																					OR resID = :resID7 AND Leaving >= :newDateL5 AND Returning <= :newDateR5 AND deletedDate = '0000-00-00 00:00:00' $qryUpdate_NewDate_LeavingComparision6
																					OR resID = :resID8 AND Leaving >= :newDateL6 AND Leaving <= :newDateR6 AND deletedDate = '0000-00-00 00:00:00' $qryUpdate_NewDate_LeavingComparision7";
						
				}
				//print($queryBuilder);
				
				$statementBuilder = "SELECT `Leaving`, `Returning`, `reasonID`, `UserID`, `resID`, `ResFName`, `ResLName`,`Code`, absence.lastUpdate, absence.updatedBy,
																CONCAT(ResLName, ', ', ResFName) AS resident,
																CONCAT(createdUser.userFirstName, ' ', createdUser.userLastName) AS employee,
																CONCAT(updator.userFirstName, ' ', updator.userLastName) AS updator
																FROM absence
																INNER JOIN userinfo AS createdUser ON createdUser.id = absence.UserID
																LEFT JOIN userinfo AS updator ON updator.id = absence.UserID
																INNER JOIN residents
																ON residents.id = absence.resID
																INNER JOIN attendanceCode
																ON attendanceCode.id = absence.reasonID
																WHERE 
																	$queryBuilder
																ORDER BY Leaving, ResLName, ResFName ASC";
				
				try{
					$stmt = $db->prepare($statementBuilder);	
					$stmt->execute($param);
				}catch(PDOException $ex) {
	     	  	return $GLOBALS['somethingWrong']; 	
				}
				$results['row'] = $stmt->fetchAll();
				$results['count'] = $stmt->rowCount();
				return $results;
			}
		/*Records a new absence*/
			function insertAbsenceRecord($db){
				$sqlCount = 1; //track the number of fields for userinfo
				//fields used to create the query statement
				$sql_Fields;
				$sql_Values; 
					
					if (isset($_POST['residentID']) AND $_POST['residentID'] != ''){
						$resid = test_input($_POST['residentID']);
						$param[':resID'] = $resid;
						$sql_Fields = qryBuildCount("`resID`",$sqlCount);
						$sql_Values = qryBuildCount(":resID",$sqlCount); 
						$sqlCount++;
					}else{
						return false;
					}
					//ABSENCE TABLE VALUES
					if (isset($_POST['formBeginning']) AND $_POST['formBeginning'] != ''){
						$beginning = test_input($_POST['formBeginning']);
						$param[':leaving'] = $beginning;
						$sql_Fields = $sql_Fields.qryBuildCount("`Leaving`",$sqlCount);
						$sql_Values = $sql_Values.qryBuildCount(":leaving",$sqlCount);
						$sqlCount++;
					}else{
						return false;
					}
					if (isset($_POST['formReturning']) AND $_POST['formReturning'] != ''){
						$returning = test_input($_POST['formReturning']);
						$param[':returning'] = $returning;
						$sql_Fields = $sql_Fields.qryBuildCount("`Returning`",$sqlCount);
						$sql_Values = $sql_Values.qryBuildCount(":returning",$sqlCount);
						$sqlCount++;
					}
					if (isset($_POST['reasonID']) AND $_POST['reasonID'] != ''){
						$reason = test_input($_POST['reasonID']);
						$param[':reasonID'] = $reason;
						$sql_Fields = $sql_Fields.qryBuildCount("`reasonID`",$sqlCount);
						$sql_Values = $sql_Values.qryBuildCount(":reasonID",$sqlCount);
						$sqlCount++;
					}else{
						return false;
					}
					if (isset($_POST['userID']) AND $_POST['userID'] != ''){
						$userID = test_input($_POST['userID']);
						$param[':userID'] = $userID;
						$sql_Fields = $sql_Fields.qryBuildCount("`userID`",$sqlCount);
						$sql_Values = $sql_Values.qryBuildCount(":userID",$sqlCount);
						$sqlCount++;
					}else{
						return false;
					}
					$param[':createdOn'] = getTimeStamp();
					$sql_Fields = $sql_Fields.qryBuildCount("`createdOn`",$sqlCount);
					$sql_Values = $sql_Values.qryBuildCount(":createdOn",$sqlCount);
					
					$statementBuilder = "INSERT INTO `absence` ($sql_Fields) VALUES($sql_Values)";
					
					try{
						// Prepare statement
		  	  	$stmt = $db->prepare($statementBuilder);
						
						// execute the query
						$stmt->execute($param);
						}catch(PDOException $ex) { 
							$test = $stmt->errorInfo();
							//Ignore duplicate entry attempts
						print($ex);
							if($test[1]=="1452"){
								die('You must click start before you can record the attendance!'); 	
							}elseif($test[1]!="1062"){
								$GLOBALS['errorMsg'] = $GLOBALS['somethingWrong'];
     	  				return $GLOBALS['somethingWrong']; 	
							}else{
								print($test[1]);
							}
						}
						return true;
					}
					
			function updateAbsence($db){
				$sqlCount = 1; //track the number of fields for userinfo
				//fields used to create the query statement
				$sql_Fields="";
				
				if (isset($_POST['residentID']) AND $_POST['residentID'] != ''){
						$resid = test_input($_POST['residentID']);
						$param[':resID'] = $resid;
						$queryEqual = "`resID`= :resID";
					}else{
						return false;
					}
				if(isset($_POST['PKLeaving']) AND $_POST['PKLeaving'] != ''){
					$param[':PKLeaving'] = test_input($_POST['PKLeaving']);
					$queryEqual = $queryEqual." AND `Leaving` = :PKLeaving";
				}
				
				if (isset($_POST['formBeginning']) AND $_POST['formBeginning'] != ''){
					$beginning = test_input($_POST['formBeginning']);
					$param[':leaving'] = $beginning;
					$sql_Fields = qryBuildCount("`Leaving` = :leaving",$sqlCount);
					$sqlCount++;
				}
				
				//Set Returning Date to Null if no input exists.  Add a value for all changes.
				if (isset($_POST['formReturning']) AND $_POST['formReturning'] != ''){
					$returning = test_input($_POST['formReturning']);
					$param[':returning'] = $returning;
					$sql_Fields = $sql_Fields.qryBuildCount("`Returning` = :returning",$sqlCount);
					$sqlCount++;
				}
						
				
				if (isset($_POST['reasonID']) AND $_POST['reasonID'] != ''){
						$reason = test_input($_POST['reasonID']);
						$param[':reasonID'] = $reason;
						$sql_Fields = $sql_Fields.qryBuildCount("`reasonID` = :reasonID",$sqlCount);
						$sqlCount++;
				}
				if (isset($_POST['userID']) AND $_POST['userID'] != ''){
					$userID = test_input($_POST['userID']);
					$param[':updatedBy'] = $userID;
					$sql_Fields = $sql_Fields.qryBuildCount("`updatedBy` = :updatedBy",$sqlCount);
					$sqlCount++;
				}else{
					return false;
				}
				if (isset($_POST['formActive']) AND $_POST['formActive'] != ''){
					$active = test_input($_POST['formActive']);
					
					$param[':active'] = $active;
					$sql_Fields = $sql_Fields.qryBuildCount("`Active` = :active",$sqlCount);
					$sqlCount++;
					
					if($active == 0){
						$currentTime =  getTimeStamp();
						$param[':deletedDate'] = $currentTime;
						$sql_Fields = $sql_Fields.qryBuildCount("`deletedDate` = :deletedDate",$sqlCount);
						$sqlCount++;						
					}
				}
					$param[':PK_deletedDate'] = '0000-00-00 00:00:00';
					$queryEqual = $queryEqual." AND `deletedDate` = :PK_deletedDate";

				
					$param[':lastUpdate'] = getTimeStamp();
					$sql_Fields = $sql_Fields.qryBuildCount("`lastUpdate` = :lastUpdate",$sqlCount);
				
				
					$sqlQuery = "UPDATE `absence` SET $sql_Fields WHERE $queryEqual";
				
					try{
						// Prepare statement
		  	  	$stmt = $db->prepare($sqlQuery);
						
						// execute the query
						$stmt->execute($param);
						}catch(PDOException $ex) { 
							$test = $stmt->errorInfo();
							//Ignore duplicate entry attempts
						print($ex);
							if($test[1]=="1452"){
								return 'You must click start before you can record the attendance!'; 	
							}elseif($test[1]!="1062"){
								$GLOBALS['errorMsg'] = $GLOBALS['somethingWrong'];
     	  				return $GLOBALS['somethingWrong']; 	
							}else{
								return($test[1]);
							}
						}
					return true;
				}
		/*Absence query - lists all current and future absences.*/
			function defaultAbsenceQuery($db){
				
				$filterDate = getAttendanceDate();
	
				$queryAbsenceRecords = "SELECT `Leaving`, `Returning`, `reasonID`, `UserID`, `resID`, `ResFName`, `ResLName`,`Code`, absence.createdOn,  absence.lastUpdate, absence.updatedBy,
																CONCAT(ResLName, ', ', ResFName) AS resident,
																CONCAT(createdUser.userFirstName, ' ', createdUser.userLastName) AS employee,
																CONCAT(updator.userFirstName, ' ', updator.userLastName) AS updator
																FROM absence
																INNER JOIN userinfo AS createdUser ON createdUser.id = absence.UserID
																LEFT JOIN userinfo AS updator ON updator.id = absence.updatedBy
																INNER JOIN residents
																ON residents.id = absence.resID
																INNER JOIN attendanceCode
																ON attendanceCode.id = absence.reasonID
																WHERE Leaving >= :leaving AND Code != :present AND deletedDate = '0000-00-00 00:00:00'
																OR Returning >= :returning AND deletedDate = '0000-00-00 00:00:00'
																ORDER BY Leaving, ResLName, ResFName ASC"; 
				try{
					$stmt = $db->prepare($queryAbsenceRecords);
					$stmt->bindParam(':present', $filterDate);
					$stmt->bindParam(':leaving', $filterDate);
					$stmt->bindParam(':returning', $filterDate);
					$result = $stmt->execute();   // Execute the prepared query.    
					}catch(PDOException $ex) 
					{ print($ex);
						die($GLOBALS['somethingWrong']); 
					
					}
					$queryResults['row'] = $stmt->fetchAll();
					$queryResults['count'] = $stmt->rowCount();
				//	$GLOBALS['countAbsence'] = $stmt->rowCount();
			
					return $queryResults;
			}
		/*ABSENCE LIST BY DATE - Generate a list of absences that fall on a specified date.
			RETURN query results*/	
			function absenceByDate($db){
				 
				if(isset($_POST['frmDate'])){
					$filterDate = test_input($_POST['frmDate']);
				}
				
				$queryAbsenceRecords = "SELECT `Leaving`, `Returning`, `reasonID`, `UserID`, `resID`, `userFirstName`, `userLastName`,`ResFName`, `ResLName`,`Code`, deletedDate,
																CONCAT(ResFName, ' ', ResLName) AS resident,
																CONCAT(userFirstName, ' ', userLastName) AS employee
																FROM absence
																INNER JOIN userinfo ON userinfo.id = absence.UserID
																INNER JOIN residents ON residents.id = absence.resID
																INNER JOIN attendanceCode ON attendanceCode.id = absence.reasonID
																WHERE Leaving = :filterDateL1 AND deletedDate = '0000-00-00 00:00:00' AND NOT EXISTS (SELECT * FROM attendance WHERE attendance.ResID = absence.resID AND attendance.date = :notDateFilter1 AND code <> 2)
																OR Returning = :filterDateR1 AND deletedDate = '0000-00-00 00:00:00' AND NOT EXISTS (SELECT * FROM attendance WHERE attendance.ResID = absence.resID AND attendance.date = :notDateFilter2 AND code <> 2)
																OR Leaving < :filterDateL2 AND Returning > :filterDateR2 AND deletedDate = '0000-00-00 00:00:00' AND NOT EXISTS (SELECT * FROM attendance WHERE attendance.ResID = absence.resID AND attendance.date = :notDateFilter3 AND code <> 2)
																ORDER BY Leaving, ResLName, ResFName ASC"; 
				
				try{
					$stmt = $db->prepare($queryAbsenceRecords);
					$stmt->bindParam(':filterDateL1', $filterDate);
					$stmt->bindParam(':filterDateL2', $filterDate);
					$stmt->bindParam(':filterDateR1', $filterDate);
					$stmt->bindParam(':filterDateR2', $filterDate);
					
					$stmt->bindParam(':notDateFilter1', $filterDate);
					$stmt->bindParam(':notDateFilter2', $filterDate);
					$stmt->bindParam(':notDateFilter3', $filterDate);
					
					
					$result = $stmt->execute();   // Execute the prepared query.    
					}catch(PDOException $ex) 
					{ print($ex);
						die($GLOBALS['somethingWrong']); 
					
					}
					$queryResults['row'] = $stmt->fetchAll();
					$queryResults['count'] = $stmt->rowCount();
					return $queryResults;
			}
		
			