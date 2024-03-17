<?php
include "sablona.php";
?>

<body>
<?php
nav();
if(isset($_SESSION['email']) && $_SESSION['password']){
	echo "<h3>USER : " . $_SESSION['name'] . "</h3>";
}
vypis_prod();
?>
</body>
<footer>Autor:  Adam Balala</footer>
</html>