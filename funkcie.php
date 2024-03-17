<?php
session_start();
// Vypis produktov / /index
function vypis_prod(){
    $conn = new mysqli("localhost", "root", "", "cvic_pit");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
	}
    $sql = "SELECT * FROM product";
    $result = $conn->query($sql);

    if ($result !== false && $result->num_rows > 0) {
        echo '<div class="products-container">';
        while ($row = $result->fetch_assoc()) {
            echo '<div class="col-md-3">'; 
            echo '<a href="dany_prod.php?id=' . $row["id"] . '">';
            echo "<p>" . $row["name"] . "</p></a>";
            echo "<p>" . $row["description"] . "</p>";
            echo "</div>";
        }
        echo '</div>';
		echo '<div>';
		if(isset($_SESSION['name'])){
		echo '<a href = "add_prod.php">Add Product</a>';
		}
		echo '</div>';
		
    } else {
        echo "NO RESULTS !";
    }
    $conn->close();
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Vypis daneho produktu / /dany_prod
function vypis_daneho_produktu($id){
	$conn = new mysqli("localhost", "root", "", "cvic_pit");
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
		}
	$sql = "SELECT * FROM `product` WHERE `id` = ?";
	if ($stmt = $conn->prepare($sql)) {
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$result = $stmt->get_result();
		
		if ($result !== false && $result->num_rows > 0) {
			while($row = $result->fetch_assoc()){
				echo '<div class = "products-container">';
				echo '<p>Name : ' . $row["name"] . "</p>";
				echo '<p>Desc : ' . $row["description"] . "</p>";
				echo '<p>Price : ' . $row["price"] . " €</p>";	
				echo '<p>Price + DPH: ' . $row["price"]* 1.2 . " €</p>";
				if(isset($_SESSION['name'])){
					echo '<form method="post">
						<input type="hidden" name="prod_id" value="' . $row['id'] . '">
						<input type="hidden" name="prod_name" value="' . $row['name'] . '">
						<input type="hidden" name="prod_price" value="' . $row['price'] . '">
						<input type="submit" name="add_to_cart" value="Add to Cart">    
						</form>
						<br>';
					echo '<form method="post">
						<input type="hidden" name="prod_id" value="' . $row['id'] . '">
						<input type="submit" name="delete_prod" value="Delete Product">    
						</form>
						<br>';						
					echo '<a href = "modify_prod.php?id=' . $row["id"] . '">Modify Product</a>';
				}
			}
		}
		
		else {
			echo "Product ID out of bounds! NT NT";
		}	
		$stmt->close();
	}
 else {
	echo "STATEMENT FAILED!";
	}
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Login sablona / /login
function login_vypis(){
	?>
	<div class = "log_div">
	<form method="post">
		<label>Email:</label>
		<input type="text" name="email" placeholder="email" required>
		<br>
		<label>Password:</label>
		<input type = "password" name="password" placeholder = "password" required>
		<br>
		<input type="submit" name = "login" value="Login">
		<label>Ak si ešte niesi zaregistrovaný, registuj sa <a href = "registracia.php"> tu </a>!</label><br>
	</form>
	</div>
	<?php 
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Registracia sablona / /registracia
function registracia_vypis(){
	?>
	<div>
	<p>
	<form method = "POST">
	<label>Username: </label>
	<input type = "text" name = "rusername" required>
	<br>
	<label>Email: </label>
	<input type = "text" name = "remail" required>
		<br>
	<label>Password: </label>
	<input type = "password" name = "rpassword" required>
	<br>
	<input type = "submit" name = "registracia" value = "Registrovat">
	</div>
	<?php
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Email Valid 
function isValidEmail($email) {
	if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
		return true;
	}
	return 0;
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Nav sablona /
function nav(){
	?>
	<nav>
    <a href="index.php">Index</a>
	<a href="registracia.php">Register</a>
    <a href="login.php">Login</a>
	<a href= "kosik.php">Cart(<?php echo get_cart_total(); ?>)</a>
	</nav>
<?php	
}

function add_to_cart($product_id, $product_name, $product_price) {
    if (!isset($_COOKIE['cart'])) {
        $cart = array();
    } else {
        $cart = json_decode($_COOKIE['cart'], true);
    }
    if (isset($cart[$product_id])) {
          $cart[$product_id]['quantity']++;
    } 
	else {
        $cart[$product_id] = array(
            'name' => $product_name,
            'price' => $product_price,
            'quantity' => 1
        );
    }
	echo "<script type='text/javascript'>alert('ADDED TO CART !');</script>";
    setcookie('cart', json_encode($cart), time() + 3600, '/');
    header('Location: dany_prod.php?id=' . $product_id);
    exit();
}

function get_cart_total_price() {
    $total = 0;
    if (isset($_COOKIE['cart'])) {
        $cart = json_decode($_COOKIE['cart'], true);
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
    }
    return $total;
}
function get_cart_total() {
    $total = 0;
    if (isset($_COOKIE['cart'])) {
        $cart = json_decode($_COOKIE['cart'], true);
        foreach ($cart as $item) {
            $total += 1 * $item['quantity'];
        }
    }
    return $total;
}
function displayAllProductsInCart() {
    $conn = new mysqli("localhost", "root", "", "cvic_pit");
	if (isset($_COOKIE['cart'])) {
        $cart = json_decode($_COOKIE['cart'], true);
        if (!empty($cart)) {
            foreach ($cart as $product_id => $item) {
                $query = "SELECT * FROM product WHERE id = $product_id";
                $result = $conn->query($query);
                if ($result->num_rows > 0) {
                    $product = $result->fetch_assoc();
                    echo '<div>';
                    echo '<h2>' . $product['name'] . '</h2>';
                    echo '<p>Price: ' . $product['price'] . '€</p>';
					echo '<p>Price + DPH: ' . $product['price']* 1.2 . '€</p>';
                    echo '<p>Quantity: ' . $item['quantity'] . '</p>';
                    echo '<form method="post">';
                    echo '<input type="hidden" name="remove_from_cart" value="' . $product['id'] . '">';
                    echo '<input type="submit" value="Remove from Cart">';
                    echo '</form>';
                    echo '</div>';
                }
            }
        } else {
            if(isset($_SESSION['name'])){
				echo '<div>YOUR CART IS EMPTY !</div>';
				}
			else{
				echo '<div>NEED TO LOG IN!</div>';
	}
        }
    } else {
	if(isset($_SESSION['name'])){
        echo '<div>YOUR CART IS EMPTY !</div>';
		}
	else{
		echo '<div>NEED TO LOG IN!</div>';
	}
	
	}
}
function remove_from_cart($product_id) {
    if (isset($_COOKIE['cart'])) {
        $cart = json_decode($_COOKIE['cart'], true);
        if (isset($cart[$product_id])) {
            if ($cart[$product_id]['quantity'] > 1) {
                $cart[$product_id]['quantity']--;
            } else {
                unset($cart[$product_id]);
            }
            setcookie('cart', json_encode($cart), time() + 3600, '/');
        }
    }
    header('Location: kosik.php');
    exit();
}
function remove_cart(){
    setcookie('cart', '', time() - 3600, '/');
    header('Location: kosik.php');
    exit();
}


?>

