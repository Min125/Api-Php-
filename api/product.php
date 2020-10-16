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
		$sql = "SELECT * FROM products";
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
					"price" =>$row["price"],
					"waiting_time" => $row["waiting_time"],
					"image"=>$row["image"],
					"category_id" =>$row["category_id"],
					"supplier_id" => $row["supplier_id"]
				);
				array_push($categories_arr["data"], $category);
			}
		}
		http_response_code(200);
		echo json_encode($categories_arr);
	}

	function show($id) {
		global $pdo;
		$sql = 'SELECT * FROM products where id = :id';
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
		$price = $_POST['price'];
		$waiting_time = $_POST['waiting_time'];
		$category_id = $_POST['category_id'];
		$supplier_id = $_POST['supplier_id'];
		$image = $_FILES['image'];

		$source_dir = "../image/";
		$file_path= $source_dir.$image['name'];
		$image_file = "/image/".$image['name'];

		move_uploaded_file($image['tmp_name'],$file_path);

		if(!empty($name) && !empty($file_path)){
			$sql = "SELECT * FROM products where name = :name";
			$stmt = $pdo->prepare($sql);
			$stmt ->bindParam(':name',$name);
			$stmt->execute();
			if ($stmt->rowCount()){
				$response = array(
					'status' => '0',
					'status' => "That name is already added in database"
				);
			}else{
				$sql = "INSERT INTO products(name,price,waiting_time,category_id,supplier_id,image) VALUES (:name,:price,:waiting_time,:category_id,:supplier_id,:image)";
				$stmt = $pdo->prepare($sql);
				$stmt ->bindParam(':name',$name);
				$stmt ->bindParam(':image',$image_file);
				$stmt ->bindParam(':price',$price);
				$stmt ->bindParam(':waiting_time',$waiting_time);
				$stmt ->bindParam(':category_id',$category_id);
				$stmt ->bindParam(':supplier_id',$supplier_id);
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
		$price = $_POST['price'];
		$waiting_time = $_POST['waiting_time'];
		$category_id = $_POST['category_id'];
		$supplier_id = $_POST['supplier_id'];

		$image_sql= "SELECT * FROM products where id=:id";
		$image_stmt = $pdo->prepare($image_sql);
		$image_stmt -> bindParam(":id",$id);
		$image_stmt -> execute();

		$image_row = $image_stmt->fetch(PDO::FETCH_ASSOC);
		$old_image = $image_row['image'];

		$old_image = '..'.$old_image;
		// var_dump($old_image);die();
		
		
		if (isset($_FILES['new_image'])){
			$new_image = $_FILES['new_image'];
			$file_path = '../image/'.$new_image['name'];
			move_uploaded_file($new_image['tmp_name'],$file_path);
			
			unlink($old_image);
			$image = '/image/'.$new_image['name'];
			// var_dump($new_image);die();
		}else{
			$image = $image_row['image'];
		}

		if (!empty($name)){

			$sql = "UPDATE products SET name=:name, image=:image, price=:price, waiting_time=:waiting_time, category_id=:category_id, supplier_id=:supplier_id WHERE id=:id";
			$stmt = $pdo->prepare($sql);
			$stmt -> bindParam(":name",$name);
			$stmt -> bindParam(":id",$id);
			$stmt -> bindParam(":image",$image);
			$stmt ->bindParam(':price',$price);
			$stmt ->bindParam(':waiting_time',$waiting_time);
			$stmt ->bindParam(':category_id',$category_id);
			$stmt ->bindParam(':supplier_id',$supplier_id);
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

		$sql= "DELETE FROM products WHERE id=:id";
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