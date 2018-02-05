<?php
session_start();
require 'db.connect.php';

try {
    if (!in_array($_POST['method'], array('Add', 'Modify', 'Del'))) {
        throw new Exception('Invalid Connection');
    }

    $db = new DB();

    if($_POST['method'] != 'Del'){
		if($_POST['start_date'] > $_POST['end_date']){
			throw new Exception('입고일자를 확인해주세요.');
		}
    }

	if($_POST['method'] == 'Add'){
		$sql = 'INSERT INTO `TB-MedicineInfo`(`User`, `Company`, `Medicine_Name`, `Description`, `Start_Date`, `End_Date`, `Alarm`) VALUES(:User, :Company, :Medicine_Name, :Description, :Start_Date, :End_Date, :Alarm)';
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':User', $_SESSION['User']);
        $stmt->bindParam(':Company', $_SESSION['Company']);
		$stmt->bindParam(':Medicine_Name', $_POST['medicine_name']);
		$stmt->bindParam(':Description', $_POST['description']);
		$stmt->bindParam(':Start_Date', $_POST['start_date']);
		$stmt->bindParam(':End_Date', $_POST['end_date']);
        $stmt->bindParam(':Alarm', $_POST['alarm']);
		$stmt->execute();
	}elseif($_POST['method'] == 'Modify'){
		$sql = 'UPDATE `TB-MedicineInfo` 
                SET `User`=:User, `Medicine_Name`=:Medicine_Name, `Description`=:Description, `Start_Date`=:Start_Date, `End_Date`=:End_Date, `Alarm`=:Alarm
                WHERE `Info`=:Info';
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':Info', $_POST['info']);
		$stmt->bindParam(':User', $_SESSION['User']);
		$stmt->bindParam(':Medicine_Name', $_POST['medicine_name']);
		$stmt->bindParam(':Description', $_POST['description']);
		$stmt->bindParam(':Start_Date', $_POST['start_date']);
		$stmt->bindParam(':End_Date', $_POST['end_date']);
        $stmt->bindParam(':Alarm', $_POST['alarm']);
		$stmt->execute();
	}else{
		$sql = 'DELETE FROM `TB-MedicineInfo` WHERE `Info`=:Info';
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':Info', $_POST['info']);
		$stmt->execute();
	}

    $return['success'] = true;
} catch (Exception $e) {
    $return['success'] = false;
    $return['reason'] = $e->getMessage();
}

echo json_encode($return);
