 	<?php	
		//query employee list to populate employee dropdown search
/*		$sqlNameSearch = "SELECT EMPLOYEE_ID, LAST_NAME, FIRST_NAME, PHONE_NUMBER, DEPARTMENT_ID
		FROM  `hr_employees`
		ORDER BY LAST_NAME, FIRST_NAME ASC";	
		//echo $sqlNameSearch;

		$resNSearch = $conn->query($sqlNameSearch);


		//query department list to populate department dropdown search
		$sqlDepartment = "SELECT DEPARTMENT_ID, DEPARTMENT_NAME 
		FROM  `department`";
	
		$resDep = $conn->query($sqlDepartment);


		$varNID = $sql_ChBuild = "";
		if ($_SERVER["REQUEST_METHOD"] == "POST") 
		{
			
			if (isset($_POST['formEmployeeID']) AND $_POST['formEmployeeID'] != '')
			{

			}
				$varNID = ($_POST['newFirst']);																
				$sql_ChBuild = $varNID;
			echo $varNID;
			echo $sql_ChBuild;
			
			$sqlChanges = "INSERT INTO `directory`.`hr_change_request` (`EMPLOYEE_ID` ,`LAST` ,`FIRST` ,`PHONE` ,`DEPARTMENT_ID`) 
			VALUES ('$sql_ChBuild',  'test',  'test',  'test',  '1')";
			
			//mysql_select_db('test_db');
			//$result = $conn->query($sql);	
   		//$retval = mysql_query( $sqlChanges, $conn );
			$conn->query($sqlChanges);
				
		}

echo $varNID;*/