<?php
session_start();
require 'db.connect.php';

try {
    if (!is_numeric($_POST['ID'])) {
        throw new Exception('Invalid Connection');
    }

    $db = new DB();

    $sql = 'SELECT 
                `Info`, `User`, `UserName`, `Medicine_Name`, `Description`, DATE_FORMAT(`Start_Date`, "%Y-%m-%d") AS `Start_Date`, DATE_FORMAT(`End_Date`, "%Y-%m-%d") AS `End_Date`, `Alarm`
            FROM `TB-MedicineInfo` 
            LEFT JOIN `TB-UserInfo` USING(`User`)
            WHERE 
                `Info` = :Info';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':Info', $_POST['ID']);
    $stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (empty($row)) {
        throw new Exception('Info Not Found.');
    }

    $return['author'] = false;

    if($_SESSION['User'] == $row['User'] || $_SESSION['Master'] == 'Y'){
    	$return['author'] = true;
    }

    $return['success'] = true;
    $return['id'] = $row['Info'];
    $return['medicine_name'] = $row['Medicine_Name'];
    $return['user_name'] = $row['UserName'];
    $return['description'] = $row['Description'];
    $return['start_date'] = $row['Start_Date'];
    $return['end_date'] = $row['End_Date'];
    $return['alarm'] = $row['Alarm'];
    
} catch (Exception $e) {
    $return['success'] = false;
    $return['reason'] = $e->getMessage();
}

echo json_encode($return);
