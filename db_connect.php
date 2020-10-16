<?php
	
	$server_name="localhost";
	$db_name="protein_house_os";
	$user="root";
	$password="";

	$pdo = new PDO("mysql:host=$server_name; dbname=$db_name",$user,$password);

	try {
		$conn = $pdo;
		$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		// echo "Connected";
	} catch (Exception $e) {
		echo "Fail connection";
	}
?>