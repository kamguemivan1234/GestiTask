<?php
// app/Model/Notification.php - VERSION CORRIGÉE

function get_all_my_notifications($conn, $id){
	$sql = "SELECT *, NOW() as server_time FROM notifications WHERE recipient=? AND deleted_at IS NULL ORDER BY date DESC, id DESC";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$id]);

	if($stmt->rowCount() > 0){
		$notifications = $stmt->fetchAll();
	}else $notifications = 0;

	return $notifications;
}

function count_notification($conn, $id){
	$sql = "SELECT id FROM notifications WHERE recipient=? AND is_read=0 AND deleted_at IS NULL";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$id]);

	return $stmt->rowCount();
}

function insert_notification($conn, $data){
	$sql = "INSERT INTO notifications (message, recipient, type, date, created_at) VALUES(?,?,?,CURDATE(),NOW())";
	$stmt = $conn->prepare($sql);
	$stmt->execute($data);
}

function notification_make_read($conn, $recipient_id, $notification_id){
	$sql = "UPDATE notifications SET is_read=1, read_at=NOW() WHERE id=? AND recipient=? AND deleted_at IS NULL";
	$stmt = $conn->prepare($sql);
	$result = $stmt->execute([$notification_id, $recipient_id]);
	return $result && $stmt->rowCount() > 0;
}

function delete_notification($conn, $recipient_id, $notification_id){
	$sql = "UPDATE notifications SET deleted_at=NOW() WHERE id=? AND recipient=? AND deleted_at IS NULL";
	$stmt = $conn->prepare($sql);
	$result = $stmt->execute([$notification_id, $recipient_id]);
	return $result && $stmt->rowCount() > 0;
}

function get_notification_details($conn, $notification_id, $recipient_id){
	$sql = "SELECT *, NOW() as server_time FROM notifications WHERE id=? AND recipient=? AND deleted_at IS NULL";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$notification_id, $recipient_id]);
	
	if($stmt->rowCount() > 0){
		return $stmt->fetch();
	}
	return false;
}

function get_deleted_notifications($conn, $id){
	$sql = "SELECT *, NOW() as server_time FROM notifications WHERE recipient=? AND deleted_at IS NOT NULL ORDER BY deleted_at DESC";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$id]);

	if($stmt->rowCount() > 0){
		return $stmt->fetchAll();
	}
	return [];
}
?>