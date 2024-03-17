<?php
include "sablona.php";
?>

<body>
<?php
nav();
if (isset($_SESSION['name'])){
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
else{
	registracia_vypis();
}

// Odhlasenie
if (isset($_POST['odhlasenie'])) {
    session_unset();
    session_destroy();
	remove_cart();
    header("Refresh:0");
}


// Registracia
if (isset($_POST['registracia'])) {
	$email = $_POST['remail'];
	if (isset($_POST['rusername']) && isset($_POST['rpassword']) && isset($_POST['remail']) && isValidEmail($email)){
        $conn = new mysqli("localhost", "root", "", "cvic_pit");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $rusername = $_POST['rusername'];
        $rpassword = hash('sha512', $_POST['rpassword']);
        $remail = $_POST['remail'];
        $sql = "INSERT INTO user (`name`, `password_hash`, `email`) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sss", $rusername, $rpassword, $remail);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                echo "<br>";
                ?>
                <div>
				<p>Registration Passed !</p>
				<p><a href="login.php">[Click here to login!]</a></p>
               </div>
                <?php
            } else {
                echo "<h1>REGISTRATION FAILED !<h1>";
				echo "ERROR: " . $stmt->error; 
            }
            $stmt->close();
        } else {
            echo "STATEMENT FAILED!";
        }
        $conn->close();
    }
	else{
		if(!isValidEmail($email)){
			echo "<div>WRONG MAIL !</div>";
		}
	}
} 

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

?>

</body>