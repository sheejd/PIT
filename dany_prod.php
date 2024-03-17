<?php
include "sablona.php";
?>
<body>
<?php
nav();
vypis_daneho_produktu($_GET['id']);
if (isset($_POST['add_to_cart'])) {
    $product_id = $_GET['id'];
	echo $product_id;
	$conn = new mysqli("localhost", "root", "", "cvic_pit");
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$sql = "SELECT * FROM product WHERE id = ?";
		$stmt = $conn->prepare($sql);

		if ($stmt) {
			$stmt->bind_param("i",$product_id);
			$stmt->execute();
			$result = $stmt->get_result();
			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()){
				$product_name = $row['name'];
				$product_price = $row['price'];
				echo $product_price . " " . $product_name;
				}
			}
		}
		
	add_to_cart($product_id,$product_name,$product_price);	
}

if (isset($_POST['delete_prod'])) {
	$conn = new mysqli("localhost", "root", "", "cvic_pit");
	if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
	$product_id = $_POST['prod_id'];
	$sql = "DELETE FROM product WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
	if ($stmt->execute()) {
        echo "<br>PRODUCT DELETED!";
		header('Location: index.php');
    } else {
        echo "ERROR : " . $stmt->error;
    }
    $stmt->close();
}

?>
</body>