<?php
	
	header("ACCESS-Control-Allow-Origin: *");
	header("Content-Type: application/json");

	require '../db_connect.php';

	$request_method = $_SERVER["REQUEST_METHOD"];

	switch ($request_method) {
		case 'GET':
			if (!empty($_GET['id'])) {
				$id = intval($_GET['id']);
				show($id);
			}else{
				index();
			}
			break;
		case 'POST':
			if(!empty($_GET['id'])){
				$id = intval($_GET['id']);
				update($id);
			}else{
				store();
			}
			break;
		case 'DELETE':
			$id = $_GET['id'];
			destory($id);
			break;
		
		default:
			header("HTTP/1.0 405 Method Not Allowed");
			$response = array(
				'status' => '0',
				'status_message' => "Method Not Allowed"
			);
			echo json_encode($response);
			break;
	}

	function index(){

		global $pdo;
		$sql = "SELECT * FROM orders";
		$stmt = $pdo->prepare($sql);
		$stmt -> execute();
		$rows = $stmt->fetchAll();

		$categories_arr = array();

		if ($stmt->rowCount()<=0) {
			$categories_arr["status"]=0;
			$categories_arr["status_mesage"]="Something went wrong";
		}else{

			$categories_arr["status"]=1;
			$categories_arr["status_mesage"]="200 OK";

			$categories_arr["data"]=array();

			foreach ($rows as $row) {
				$category = array(
					'id'=>$row["id"],
					"payment_method"=>$row["payment_method"],
					"order_date" =>$row["order_date"],
					"status" => $row["status"],
					"customer_id" => $row["customer_id"]
				);
				array_push($categories_arr["data"], $category);
			}
		}
		http_response_code(200);
		echo json_encode($categories_arr);
	}

	function show($id) {
		global $pdo;
		$sql = 'SELECT * FROM orders where id = :id';
		$stmt = $pdo->prepare($sql);
		$stmt ->bindParam(':id',$id);
		$stmt->execute();

		$rows = $stmt->fetchAll();

		$categories_arr = array();

		if ($stmt->rowCount() <= 0){
			$categories_arr["status"]=0;
			$categories_arr["status_message"]= "Something went wrong";
		}else{
			$categories_arr["status"]=1;
			$categories_arr["status_message"] = "200 OK";
			$categories_arr["data"] = array();

			foreach ($rows as $row) {
				$category = array(
					'id'=>$row["id"],
					"payment_method"=>$row["payment_method"],
					"order_date" =>$row["order_date"],
					"status" => $row["status"],
					"customer_id" => $row["customer_id"]
				);
				array_push($categories_arr["data"], $category);
			
			}
		}
		http_response_code(200);
		echo json_encode($categories_arr);
	}

	function store(){
		global $pdo;
		$id = $_POST['id'];
		$payment_method = $_POST['payment_method'];
		$order_date = $_POST['order_date'];
		$status = $_POST['status'];
		$customer_id = $_POST['customer_id'];

		if(!empty($payment_method)){
			$sql = "SELECT * FROM orders where id = :id";
			$stmt = $pdo->prepare($sql);
			$stmt ->bindParam(':id',$id);
			$stmt->execute();
			if ($stmt->rowCount()){
				$response = array(
					'status' => '0',
					'status' => "That name is already added in database"
				);
			}else{
				$sql = "INSERT INTO orders(payment_method,order_date,status,customer_id) VALUES (:payment_method,:order_date,:status,:customer_id)";
				$stmt = $pdo->prepare($sql);
				$stmt ->bindParam(':payment_method',$payment_method);
				$stmt ->bindParam(':order_date',$order_date);
				$stmt ->bindParam(':status',$status);
				$stmt ->bindParam(':customer_id',$customer_id);
				$stmt->execute();
				if ($stmt->rowCount()){
					$response = array(
						'status' => '1',
						'status' => "Categor is added successfully"
					);
				}else{
					$response = array(
						'status' => '0',
						'status_message' => "Category can't added tp database"
					);
				}
			}
		}else{
			$response = array(
						'status' => '0',
						'status_message' => "Category is required"
					);
		}
		echo json_encode($response);
	}

	function update($id){

		global $pdo;
		$payment_method = $_POST['payment_method'];
		$order_date = $_POST['order_date'];
		$status = $_POST['status'];
		$customer_id = $_POST['customer_id'];

		if (!empty($name)){

			$sql = "UPDATE orders SET payment_method=:payment_method,order_date=:order_date,status=:status,customer_id=:customer_id WHERE id=:id";
			$stmt = $pdo->prepare($sql);
			$stmt -> bindParam(":id",$id);
			$stmt ->bindParam(':payment_method',$payment_method);
			$stmt ->bindParam(':order_date',$order_date);
			$stmt ->bindParam(':status',$status);
			$stmt ->bindParam(':customer_id',$customer_id);
			$stmt -> execute();

			if($stmt->rowCount()){
				$response = array(
					'status' =>'1',
					'status_message' => 'Exiting Category is updated sucessfully'
				);
			}else{
				$response = array(
					'status' =>'0',
					'status_message' => 'Nothing changed and updated'
				);
			}
		}else{
			$response = array(
				'status' =>'0',
				'status_message' => 'Category name is required'
			);
		}
		echo json_encode($response);
	}

	function destory($id){
		global $pdo;

		$sql= "DELETE FROM orders WHERE id=:id";
		$stmt = $pdo->prepare($sql);
		$stmt-> bindParam(':id',$id);
		$stmt-> execute();
		if($stmt->rowCount()){
			$response = array(
				'status' =>'1',
				'status_message' => 'Exiting Category is deleted sucessfully'
			);
		}else{
			$response = array(
				'status' =>'0',
				'status_message' => 'Exiting Category cannot delete'
			);
		}
		
	echo json_encode($response);
}


?>