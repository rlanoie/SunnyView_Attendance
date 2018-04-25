<?php
include_once '../includes/session.php';
include_once 'function.php';
require '../includes/dBconnect.php';

if (session_status() !== PHP_SESSION_ACTIVE) {sec_session_start();}



if($_SERVER["REQUEST_METHOD"]=="POST" AND isset($_POST["phpFunction"]) AND isset($_POST["method"])){
	if($_POST["phpFunction"]=='residentFunctions'){//used to distinguish between other pages.
		echo $_POST["method"]();	
	}
}


//Variables used to populate the form to filter the resident list.
$frmID= $frmResLName = $frmResFName = $frmResHID="";
$frmIDRadio = $frmLRadio = $frmFRadio = $frmHIDRadio="=";
$frmActiveRadio="1";
$frmSortRadio="0";
//Resident ID
if (isset($_POST['formFilterID'])) {
	$frmID = $_POST["formFilterID"];
}
if (isset($_POST['formFilterID_radio'])) {
	$frmIDRadio = $_POST["formFilterID_radio"];
}
//Resident Key Fob
if (isset($_POST['formFilterHID'])) {
	$frmResHID = $_POST["formFilterHID"];
}
if (isset($_POST['formFilterHID_radio'])) {
	$frmHIDRadio = $_POST["formFilterHID_radio"];
}
//Resident Last Name
if (isset($_POST['formLastName'])) {
	$frmResLName = $_POST["formLastName"];
}
if (isset($_POST['formLastName_radio'])) {
	$frmLRadio = $_POST["formLastName_radio"];
}
//Resident First Name
if (isset($_POST['formFirstName'])) {
	$frmResFName = $_POST["formFirstName"];
}
if (isset($_POST['formFirstName_radio'])) {
	$frmFRadio = $_POST["formFirstName_radio"];
}
if (isset($_POST['active'])) {
	$frmActiveRadio = $_POST['active'];
}
if (isset($_POST['sort'])) {
	$frmSortRadio = $_POST['sort'];
}
//RESIDENT PROCESSES (FILTERED)----------------------------------------------------------------

		/*Add a new resident*/
			function addResident(){
        $db = $GLOBALS['connection'];
        if(checkCredentials($db)==false){
          return 'credientalError';
        }
				echo insertResident($db);
			}
		/*update an already existing resident in the tables*/
			function updateResident(){
        $db = $db;
        //$db = $GLOBALS['connection'];
        if(checkCredentials($db)==false){
          return 'credientalError';
        }
				$db->beginTransaction();
				$duplicateCheck = keyFobList($db);//check if key fob exists
				if ($duplicateCheck['count']  >= 1){
					$db->rollBack();
					$returnMsg = "duplicate";
					return $returnMsg;
				}
				residentTableUpdate($db);
				$db->commit();
				return 'This resident has been updated';          
			}
		/*Begin Transaction to add new employee data to the tables:
			user, userinfo, userpermission*/
			function insertResident($db){
				if(isset($_POST['modalAddResident'])){
					$db->beginTransaction();
						$duplicateCheck = keyFobList($db);//check if key fob exists
						if ($duplicateCheck['count']  >= 1){
							$db->rollBack();
							$returnMsg = "duplicate";
							return $returnMsg;
						}
						residentTableInsert($db);
					$db->commit();
					$returnMsg = "success";
					echo $returnMsg;
				}else{
					die('Invalid Entry');
				}
			}
		/*Deactivate Resident*/
			function deactivateResident(){
				$db = $GLOBALS['connection'];
        if(checkCredentials($db)==false){
          return 'credientalError';
        }
				residentTableUpdate($db);
        return 'This user account has been deactivated';
			}
	//--GET RESIDENT ------------------------------------------------------*/
		/**/
			function getResident(){
				$db = $GLOBALS['connection'];
				$json = json_encode(filteredQuery($db));
				echo $json;
			}
			function getActiveResidents($active_in, $db){
				$_POST['active'] = $active_in;
				return filteredQuery($db);
			}
	
//RESIDENT TABLE QUERIES----------------------------------------------------------------
	//--INSERT INTO RESIDENT TABLE ------------------------------------------------------*/
		/*Builds insert query for user table
			RETURNS 'success'
			RETURNS error message
			RETURNS die if database error*/		
			function residentTableInsert($db){
				$sqlCount = 1;
				$insertBuilder= $insertValues=$statementBuilder='';
				$id="";
				if(isset($_POST['newFirst']) AND $_POST['newFirst'] != ''){
					$param[':newFirst'] = $_POST['newFirst'];
					$insertBuilder = $insertBuilder.qryBuildCount("`ResFName`", $sqlCount);
					$insertValues = $insertValues.qryBuildCount(":newFirst", $sqlCount);
					$sqlCount++;
				}else{
					die ('Missing First Name!');
				}
				if(isset($_POST['newLast']) AND $_POST['newLast'] != ''){
					$param[':newLast'] = $_POST['newLast'];
					$insertBuilder = $insertBuilder.qryBuildCount("`ResLName`", $sqlCount);
					$insertValues = $insertValues.qryBuildCount(":newLast", $sqlCount);
					$sqlCount++;
				}else{
					die ('Missing Last Name!');
				}
				if(isset($_POST['newRFID']) AND $_POST['newRFID'] != ''){
					$param[':newRFID'] = $_POST['newRFID'];
					$insertBuilder = $insertBuilder.qryBuildCount("`HID`", $sqlCount);
					$insertValues = $insertValues.qryBuildCount(":newRFID", $sqlCount);
					$sqlCount++;
				}else{
					die ('Missing RFID!');
				}
				if(isset($_POST['newRoom']) AND $_POST['newRoom'] != ''){
					$param[':newRoom'] = $_POST['newRoom'];
					$insertBuilder = $insertBuilder.qryBuildCount("`roomNumber`", $sqlCount);
					$insertValues = $insertValues.qryBuildCount(":newRoom", $sqlCount);
					$sqlCount++;
				}
				if(isset($_POST['active']) AND ($_POST['active']!='')){
					$param[':active'] = $_POST['active'];	
					$insertBuilder = $insertBuilder.qryBuildCount("`active`", $sqlCount);
					$insertValues = $insertValues.qryBuildCount(":active", $sqlCount);
					$sqlCount++;
				}else{
					die('Uh, Oh! Something went wrong');
				}
				//user adding resident
				if(isset($_POST['userID']) AND ($_POST['userID']!='')){
					$param[':createdBy'] = $_POST['userID'];
					$insertBuilder =  $insertBuilder.qryBuildCount("`createdBy`", $sqlCount);
					$insertValues = $insertValues.qryBuildCount(":createdBy", $sqlCount);	
					$param[':updatedBy'] = $_POST['userID'];
					$insertBuilder =  $insertBuilder.qryBuildCount("`updatedBy`", $sqlCount);
					$insertValues = $insertValues.qryBuildCount(":updatedBy", $sqlCount);
					$sqlCount++;
				}else{
					die ('A fatal error has occurred!  Please log out and try again.');
				}
				$timestamp = getTimeStamp();
					$param[':createdOn'] = $timestamp;
					$insertBuilder =  $insertBuilder.qryBuildCount("`createdOn`", $sqlCount);
					$insertValues = $insertValues.qryBuildCount(":createdOn", $sqlCount);
					$sqlCount++;
					$param[':lastUpdate'] = $timestamp;
					$insertBuilder =  $insertBuilder.qryBuildCount("`lastUpdate`", $sqlCount);
					$insertValues = $insertValues.qryBuildCount(":lastUpdate", $sqlCount);
					$sqlCount++;
				if (($insertValues!='') AND ($insertBuilder!='')){				
					$statementBuilder = 'INSERT INTO '.' `residents`('.$insertBuilder.') VALUES ('.$insertValues.')';
					
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
						die ($test[1] . "resident already exists");
					}	 
				}
			}
	//--UPDATE RESIDENT TABLE ------------------------------------------------------*/
		/*Make changes to a current resident0*/	
			function residentTableUpdate($db){
				$sqlCount = 1;
				$updateBuilder = "";
				$id = $_POST['frm_id'];
				if(isset($_POST['newFirst']) AND $_POST['newFirst'] != ''){
					$param[':newFirst'] = $_POST['newFirst'];
					$updateBuilder = $updateBuilder.qryBuildCount("`ResFName` = :newFirst", $sqlCount);
					$sqlCount++;
				}
				if(isset($_POST['newLast']) AND ($_POST['newLast'] != '')){
					$param[':newLast'] = $_POST['newLast'];
					$updateBuilder = $updateBuilder.qryBuildCount("`ResLName` = :newLast", $sqlCount);
					$sqlCount++;
				}
				if(isset($_POST['newRFID']) AND ($_POST['newRFID'] != '')){
					$param[':newRFID'] = $_POST['newRFID'];
					$updateBuilder = $updateBuilder.qryBuildCount("`HID` = :newRFID", $sqlCount);
					$sqlCount++;
				}
				if(isset($_POST['newRoom']) AND $_POST['newRoom'] != ''){
					$param[':newRoom'] = $_POST['newRoom'];
					$updateBuilder = $updateBuilder.qryBuildCount("`roomNumber` = :newRoom", $sqlCount);
					$sqlCount++;
				}
        //user adding resident
				if(isset($_POST['userID']) AND ($_POST['userID']!='')){
					$param[':updatedBy'] = $_POST['userID'];
          $updateBuilder = $updateBuilder.qryBuildCount("`updatedBy` = :updatedBy", $sqlCount);
          $sqlCount++;
          $timestamp = getTimeStamp();
					$param[':lastUpdate'] = $timestamp;
					$updateBuilder =  $updateBuilder.qryBuildCount("`lastUpdate` = :lastUpdate", $sqlCount);
					$sqlCount++;
				}else{
					die ('A fatal error has occurred!  Please log out and try again.');
				}
				if(isset($_POST['active']) AND ($_POST['active'] != '')){
					$param[':active'] = $_POST['active'];
					$updateBuilder = $updateBuilder.qryBuildCount("`active` = :active", $sqlCount);
					$sqlCount++;
					if($_POST['active'] == 0){						
							$param[':deactivateDate'] = getTimeStamp();
							$updateBuilder = $updateBuilder.qryBuildCount("`deactivateDate` = :deactivateDate", $sqlCount);
						  $sqlCount++;
						if(isset($_POST['deactivatedBy']) AND ($_POST['deactivatedBy'] != '')){
							$param[':deactivatedBy'] = $_POST['deactivatedBy'];
							$updateBuilder = $updateBuilder.qryBuildCount("`deactivatedBy` = :deactivatedBy", $sqlCount);
							$sqlCount++;
					  }else{
						  die ('Uh, Oh! Something went wrong.');
					  }
					}else if($_POST['active'] > 1){
						die ('Uh, Oh! Something went wrong.');
					}
				}
				
				if($updateBuilder!=''){
					$param[':id'] = $id;

					$statementBuilder = 'UPDATE `residents` SET '.$updateBuilder.' WHERE `id` = :id';
          
					try{
						$stmt = $db->prepare($statementBuilder);
						$stmt->execute($param);
					}catch(PDOException $ex) {
						//$db->rollBack();
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
//RESIDENT LIST (ALL) ----------------------------------------------------------------
		/*Default queary when the employee page is loaded.
			Runs a query to retrieve all of the residents.
			RETURNS query of ALL residents*/
			function residentListALL($db){
    		$statementBuilder = "SELECT id, ResFName, ResLName
        							      FROM residents 
              							ORDER BY ResLName, ResFName ASC"; 
				try{
					$stmt = $db->prepare($statementBuilder);	
					$stmt->execute();
				}catch(PDOException $ex) {
					$db->rollBack();
					$test = $stmt->errorInfo();
					//Ignore duplicate entry attempts
					if($test[1]!="1062"){
						$GLOBALS['errorMsg'] = $GLOBALS['somethingWrong'];
     	  		die($GLOBALS['somethingWrong']); 	
					}else{
						die ($test[1] . "resident already exists");
					}	
				}
				$queryResults['row']= $stmt->fetchAll();
				$queryResults['count'] = $stmt->rowCount();
				
				return $queryResults;
			}
//RESIDENT LIST (FILTERED)----------------------------------------------------------------
		/*Filter the query of residents based on filter inputs
			RETURNS query of residents*/
			function filteredQuery($db){
				$sql_SearchCriteria=0;
				$sql_Search1 = "";
				$sql_Search2 = "";
				$field ="";
				$sqlCount = 0;
				$param = array();
				
					if (isset($_POST['formFilterID']) AND $_POST['formFilterID'] != ''){
						$frmID = test_input($_POST['formFilterID']);	
						$frmIDRadio = $_POST['formFilterID_radio'];
						$field = "residents.id";
						$sql_Search1 = qryWhereStatement('', $field, $frmIDRadio, ':formID1', $sqlCount);
						if($frmIDRadio == " LIKE "){	
							$frmID = "%".$frmID."%";
	  				}
						
						$bind1 = array();
						array_push($bind1, ':formID1', $frmID);
						array_push($param, $bind1);						
						
						if(isset($_POST['filterActiveDate'])){
							$sql_Search2 = qryWhereStatement('', $field, $frmIDRadio, ':formID2', $sqlCount);
							
							$bind2 = array();
							array_push($bind2, ':formID2', $frmID);
							array_push($param, $bind2);
						}
						$sqlCount++;
					}		
				
					if (isset($_POST['formFilterHID']) AND $_POST['formFilterHID'] != ''){
						$frmHID = test_input($_POST['formFilterHID']);	
						$frmHIDRadio = $_POST['formFilterHID_radio'];
						$field = "HID";
						$sql_Search1 = $sql_Search1.qryWhereStatement('', $field, $frmHIDRadio, ':HID1', $sqlCount);
						if($frmHIDRadio == " LIKE "){	
							$frmHID = "%".$frmHID."%";
	  				}
						
						$bind1 = array();
						array_push($bind1, ':HID1', $frmHID);
						array_push($param, $bind1);
						
						if(isset($_POST['filterActiveDate'])){
							$sql_Search2 = $sql_Search2.qryWhereStatement('', $field, $frmHIDRadio, ':HID2', $sqlCount);
						
							$bind2 = array();
							array_push($bind2, ':HID2', $frmHID);
							array_push($param, $bind2);
						}
						$sqlCount++;
					}	
					if (isset($_POST['formLastName']) AND $_POST['formLastName'] != ''){	
						$frmResLName = test_input($_POST['formLastName']);
						$frmLRadio = $_POST['formLastName_radio'];
						$field = "ResLName";
						$sql_Search1 = $sql_Search1.qryWhereStatement('', $field, $frmLRadio, ':lastName1', $sqlCount);
						if($frmLRadio == " LIKE "){	
							$frmResLName = "%".$frmResLName."%";
	  				}
						$bind1 = array();
						array_push($bind1, ':lastName1', $frmResLName);
						array_push($param, $bind1);
						
						if(isset($_POST['filterActiveDate'])){
							$sql_Search2 = $sql_Search2.qryWhereStatement('', $field, $frmLRadio, ':lastName2', $sqlCount);
							
							$bind2 = array();
							array_push($bind2, ':lastName2', $frmResLName);
							array_push($param, $bind2);
						}
						$sqlCount++;
					}
					if (isset($_POST['formFirstName']) AND $_POST['formFirstName'] != ''){
						$frmResFName = test_input($_POST['formFirstName']);
						$frmFRadio = $_POST["formFirstName_radio"];
						$field = "ResFName";
						$sql_Search1 = $sql_Search1.qryWhereStatement('', $field, $frmFRadio, ':firstName1', $sqlCount);
						if($frmFRadio == " LIKE "){	
							$frmResFName = "%".$frmResFName."%";
	  				}
						$bind1 = array();
						array_push($bind1, ':firstName1', $frmResFName);
						array_push($param, $bind1);
						
						if(isset($_POST['filterActiveDate'])){
							$sql_Search2 = $sql_Search2.qryWhereStatement('', $field, $frmFRadio, ':firstName2', $sqlCount);
							$bind2 = array();
							array_push($bind2, ':firstName2', $frmResFName);
							array_push($param, $bind2);
						}
						$sqlCount++;
					}
					if (isset($_POST['active']) AND $_POST['active'] != ''){
						$frmActive = test_input($_POST['active']);
						$symbol = "=";
						$sql_Search1 = $sql_Search1.qryWhereStatement('', 'active', $symbol, ':active1', $sqlCount);
						
						$bind1 = array();
						array_push($bind1, ':active1', $frmActive);
						array_push($param, $bind1);
						
						if(isset($_POST['filterActiveDate'])){
							$sql_Search2 = $sql_Search2.qryWhereStatement('', 'active', $symbol, ':active2', $sqlCount);
							
							$bind2 = array();
							array_push($bind2, ':active2', $frmActive);
							array_push($param, $bind2);
						}
						$sqlCount++;
					}
					//Include inactive residents if their deactive date is the same as the date specified
					if(isset($_POST['filterActiveDate']) AND $_POST['filterActiveDate'] != ''){
						$frmFilterDate = test_input($_POST['filterActiveDate']);
						$sql_Search1 = $sql_Search1 . qryWhereStatement('', 'deactivateDate', ' >= ', ':filterDate1', $sqlCount);

						$bind1 = array();
						array_push($bind1, ":filterDate1", $frmFilterDate);
						array_push($param, $bind1);
						
						$sql_Search2 = $sql_Search2 . qryWhereStatement('', 'deactivateDate', 'IS', 'NULL', $sqlCount);
						$sql_Search1 = $sql_Search1 . ' OR '. $sql_Search2; 
					}
					$whereClause = "";
					if($sql_Search1 !=""){
						$whereClause = "Where ";
					}
					$sortBy = "ORDER BY ResLName, ResFName ASC";
					if(isset($_POST['frmSort']) AND $_POST['frmSort']!=''){
						$frmSort = test_input($_POST['frmSort']);
						$sortBy = sortSelection($frmSort);						
					}
				
						$query_UserFilter = "SELECT residents.id, ResFName, ResLName, roomNumber, CreatedOn, createdBy, lastUpdate, updatedBy, active,
																	deactivateDate, deactivatedBy, 
																	CONCAT( ResFName,  ', ', ResLName ) AS resident, 
																	CONCAT( createdUser.userFirstName,  ', ', createdUser.userLastName ) AS creator, 
																	CONCAT( updator.userFirstName,  ', ', updator.userLastName ) AS updator,
																	CONCAT( deletor.userFirstName,  ', ', deletor.userLastName ) AS deletor
																	FROM residents
																	INNER JOIN userinfo AS createdUser ON createdUser.id = createdBy
																	INNER JOIN userinfo AS updator ON updator.id = updatedBy
																	LEFT JOIN userinfo AS deletor ON deletor.id = deactivatedBy
																	$whereClause $sql_Search1 
																	$sortBy";		

					try{
						$stmt = $db->prepare($query_UserFilter);	
						$arrlength = count($param);
						if($sql_Search1 !=""){
							$paramlength = count($param);
							for($x = 0; $x < $paramlength; $x++) {
								$stmt->bindValue($param[$x][0], $param[$x][1]);	
							}
						}
						$stmt->execute();

					}catch(PDOException $ex) {
						print $ex;
						//Ignore duplicate entry attempts
     	  		return $GLOBALS['somethingWrong']; 		
					}
					$queryResults['row']= $stmt->fetchAll();
					$queryResults['count'] = $stmt->rowCount();
					
					return $queryResults;
	  		
			}

function sortSelection($option){
	switch($option){
		case 0: //A-Z Last, First
			return "ORDER BY ResLName, ResFName ASC";
			break;
		case 1: //Z-A Last, First
    	return "ORDER BY ResLName DESC, ResFName DESC";
			break;
		case 2:
			return "ORDER BY roomNumber ASC";
			break;
	}
}
		/*Search the list of active residents for the current key fob number*/
			function keyFobList($db){
				$sql_SearchCriteria=0;
				$sql_Search = "";
				$field ="";
				
				if (isset($_POST['newRFID']) AND $_POST['newRFID'] != ''){
					$param[':HID'] = test_input($_POST['newRFID']);
					$sql_Search = "HID = :HID";
					
					$param[':active'] = $_POST['active'];
					$sql_Search = $sql_Search . ' AND active = :active ';
					$query_UserFilter = "SELECT HID, active FROM residents WHERE $sql_Search";
			
				try{
						$stmt = $db->prepare($query_UserFilter);	
						
						$stmt->execute($param);
					}catch(PDOException $ex) {
						$test = $stmt->errorInfo();
						//Ignore duplicate entry attempts
						if($test[1]!="1062"){
							$GLOBALS['errorMsg'] = $GLOBALS['somethingWrong'];
     	  			die($GLOBALS['somethingWrong']); 	
						}else{
							die ($test[1] . "resident already exists");
						}	
					}
					$queryResults['row']= $stmt->fetchAll();
					$queryResults['count'] = $stmt->rowCount();
					
					return $queryResults;
				}
			}
		