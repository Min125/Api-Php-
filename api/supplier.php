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
		$sql = "SELECT * FROM suppliers";
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
					"name"=>$row["name"],
					"address" =>$row["address"],
					"phone_no" => $row["phone_no"]
				);
				array_push($categories_arr["data"], $category);
			}
		}
		http_response_code(200);
		echo json_encode($categories_arr);
	}

	function show($id) {
		global $pdo;
		$sql = 'SELECT * FROM suppliers where id = :id';
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
					"name"=>$row["name"],
					"address" =>$row["address"],
					"phone_no" => $row["phone_no"]
				);
				array_push($categories_arr["data"], $category);
			
			}
		}
		http_response_code(200);
		echo json_encode($categories_arr);
	}

	function store(){
		global $pdo;
		$name = $_POST['name'];
		$address = $_POST['address'];
		$phone_no = $_POST['phone_no'];

		if(!empty($name)){
			$sql = "SELECT * FROM suppliers where name = :name";
			$stmt = $pdo->prepare($sql);
			$stmt ->bindParam(':name',$name);
			$stmt->execute();
			if ($stmt->rowCount()){
				$response = array(
					'status' => '0',
					'status' => "That name is already added in database"
				);
			}else{
				$sql = "INSERT INTO suppliers(name,address,phone_no) VALUES (:name,:address,:phone_no)";
				$stmt = $pdo->prepare($sql);
				$stmt ->bindParam(':name',$name);
				$stmt ->bindParam(':address',$address);
				$stmt ->bindParam(':phone_no',$phone_no);
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
		$name = $_POST['name'];
		$address = $_POST['address'];
		$phone_no = $_POST['phone_no'];

		if (!empty($name)){

			$sql = "UPDATE suppliers SET name=:name,address=:address,phone_no=:phone_no WHERE id=:id";
			$stmt = $pdo->prepare($sql);
			$stmt -> bindParam(":name",$name);
			$stmt -> bindParam(":id",$id);
				$stmt ->bindParam(':address',$address);
				$stmt ->bindParam(':phone_no',$phone_no);
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

		$sql= "DELETE FROM suppliers WHERE id=:id";
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