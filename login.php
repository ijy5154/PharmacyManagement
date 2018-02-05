<?php
session_start();
require 'db.connect.php';

try {
    $id = trim($_POST['ID']);
	$pass = $_POST['Password'];

	if( empty($id) || empty($pass) )	{
		throw new PDOException('아이디/비밀번호를 입력해주세요');
	}

    $db = new DB();

	// 아이디, 비밀번호 확인
	$sql = 'SELECT `User`, `Company`, `UserID`, `UserName`, `Password`=PASSWORD(:passwd) AS `Confirm`, `Master`, `Date_Entry`
			FROM `TB-UserInfo`
			WHERE `UserID`=:id';
	$stmt = $db->prepare($sql);
	$stmt->bindParam(':id', $id, PDO::PARAM_STR);
	$stmt->bindParam(':passwd', $pass, PDO::PARAM_STR);
	$stmt->execute();
	$member = $stmt->fetch(PDO::FETCH_ASSOC);
	$stmt->closeCursor();

	if( empty($member))	{
		throw new PDOException('해당 아이디가 존재하지 않습니다.');
	}
	if(! $member['Confirm'] )	{
		throw new PDOException('비밀번호가 일치하지 않습니다.');
	}

    $_SESSION['User'] = $member['User'];
    $_SESSION['User_Name'] = $member['UserName'];
    $_SESSION['Company'] = $member['Company'];
    $_SESSION['Master'] = $member['Master'];

    $return['success'] = true;
} catch (Exception $e) {
    $return['success'] = false;
    $return['reason'] = $e->getMessage();
}

echo json_encode($return);
