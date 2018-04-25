<?php
include_once'../includes/dBconnect.php';
include_once 'function.php';

$selectionID = $_POST['getID'];

$query = "SELECT users.id, username, email, userFirstName, userLastName, attendance, residents, users, admin
  					FROM users 
  					INNER JOIN userinfo 
  					ON userinfo.id = users.id
            INNER JOIN userpermissions
            ON userpermissions.userID = users.id
            WHERE users.id = :selectionID
            Limit 1";
    $query_params = array(':selectionID' => $selectionID);  

try{     
  $stmt = $db->prepare($query);
  $stmt->bindParam(':selectionID', $selectionID);
  $result = $stmt->execute();   // Execute the prepared query. 
}catch(PDOException $ex) 
{ 
  die($GLOBALS['somethingWrong']); 
}

$row = $stmt->fetch();
$attendancePerm = $row['attendance'];
$residentsPerm = $row['residents'];
$userPerm = $row['users'];
$adminPerm = $row['admin'];

echo "ID: " . $row['id'] . "<br>" .
    $row['userFirstName'] . " " . $row['userLastName'] . "<br>" .
    "Email: " . $row['email'] . "<br><br>" .
    "Username: " . $row['username'] . "<br>";
echo "|";
echo $attendancePerm;
echo "|";
echo $residentsPerm;
echo "|";
echo $userPerm;
echo "|";
echo $adminPerm;

  $_POST['formEmployeeID'] = $row['id'];

