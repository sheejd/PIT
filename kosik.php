<?php
include "sablona.php";

?>
<body>
<?php
if (isset($_POST['remove_from_cart'])) {
    $product_id_to_remove = $_POST['remove_from_cart'];
    remove_from_cart($product_id_to_remove);
}



if(isset($_POST['remove_cart'])) {
    remove_cart();
    header('Location: index.php');
	exit(); 
}

nav();
echo displayAllProductsInCart();
if(get_cart_total() > 0){
	echo '<div><p>Total Price: ';
	echo get_cart_total_price();
	echo '€</p>';
	echo '<p>Total Price + DPH: ';
	echo get_cart_total_price() * 1.2;
	echo '€</p></div>';
	echo '
    <div>
    <form method="post">
    <input type="submit" name="remove_cart" value="Remove cart">
    </form>
    </div>';
}

?>


</body>
