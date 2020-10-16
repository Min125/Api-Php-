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
		$sql = "SELECT * FROM order_details";
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
					"product_id"=>$row["product_id"],
					"qty" =>$row["qty"],
					"size" => $row["size"],
					"status" => $row["status"]
				);
				array_push($categories_arr["data"], $category);
			}
		}
		http_response_code(200);
		echo json_encode($categories_arr);
	}

	function show($id) {
		global $pdo;
		$sql = 'SELECT * FROM order_details where id = :id';
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
					"product_id"=>$row["product_id"],
					"qty" =>$row["qty"],
					"size" => $row["size"],
					"status" => $row["status"]
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
		$product_id = $_POST['product_id'];
		$qty = $_POST['qty'];
		$size = $_POST['size'];
		$status = $_POST['status'];

		if(!empty($product_id)){
			$sql = "SELECT * FROM order_details where id = :id";
			$stmt = $pdo->prepare($sql);
			$stmt ->bindParam(':id',$id);
			$stmt->execute();
			if ($stmt->rowCount()){
				$response = array(
					'status' => '0',
					'status' => "That name is already added in database"
				);
			}else{
				$sql = "INSERT INTO order_details(product_id,qty,size,status) VALUES (:product_id,:qty,:size,:status)";
				$stmt = $pdo->prepare($sql);
				$stmt ->bindParam(':product_id',$product_id);
				$stmt ->bindParam(':qty',$qty);
				$stmt ->bindParam(':size',$size);
				$stmt ->bindParam(':status',$status);
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
		$product_id = $_POST['product_id'];
		$qty = $_POST['qty'];
		$size = $_POST['size'];
		$status = $_POST['status'];

		if (!empty($name)){

			$sql = "UPDATE order_details SET product_id=:product_id,qty=:qty,size=:size,status=:status WHERE id=:id";
			$stmt = $pdo->prepare($sql);
			$stmt ->bindParam(':product_id',$product_id);
			$stmt ->bindParam(':qty',$qty);
			$stmt ->bindParam(':size',$size);
			$stmt ->bindParam(':status',$status);
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

		$sql= "DELETE FROM order_details WHERE id=:id";
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