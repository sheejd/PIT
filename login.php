<?php
include "sablona.php";

nav();


// Ked je prihlaseny/ne
if(isset($_SESSION['email']) and isset($_SESSION['password'])){
    echo "<div>USER : " . $_SESSION['name'];
    ?>
    <form method="POST">
        <br>
        <input type="submit" name="odhlasenie" value="Logout">
    </form>
    <p><a href="index.php">Back to shop !</a></p>
    </div>
    <?php
} 
else {
	login_vypis();
}


// Prihlasenie 
if(isset($_POST['login'])){
	if(isset($_POST['email']) and isset($_POST['password'])){
		$conn = new mysqli("localhost", "root", "", "cvic_pit");
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$email = $_POST['email'];
		$password = hash('sha512', $_POST['password']);
		$sql = "SELECT * FROM user WHERE email = ? AND password_hash = ?";
		$stmt = $conn->prepare($sql);

		if ($stmt) {
			$stmt->bind_param("ss", $email, $password);
			$stmt->execute();
			$result = $stmt->get_result();
			if ($result->num_rows > 0) {
				echo "S";
				while($row = $result->fetch_assoc()){
				$_SESSION['email'] = $email;
				$_SESSION['password'] = $password;
				$_SESSION['name'] = $row['name'];
				header("Refresh:0");
				} 
			}
			else {
				echo "<div>WRONG LOGIN !</div>";
			}
			$stmt->close();
		} 
		else {
			echo "STATEMENT FAILED !";
		}
    $conn->close();
	}
};
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////





////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Odhlasenie
if (isset($_POST['odhlasenie'])) {
    session_unset();
    session_destroy();
	remove_cart();
    header("Refresh:0");
}
?>
</body>
