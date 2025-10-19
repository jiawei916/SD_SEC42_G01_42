<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "salesdatabase"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

$sellerID = $_POST['sellerID'];
$productID = $_POST['productID'];
$customerID = $_POST['customerID'];
$quantity = $_POST['quantity'];
$saleDate = date('Y-m-d');
$sql_price = "SELECT price FROM products WHERE productID = '$productID'";

$result = $conn->query($sql_price);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $price = $row['price'];

    $total = $price * $quantity;

    $sql_insert = "INSERT INTO sales (saleDate, quantity, total, sellerID, productID, customerID)
                   VALUES ('$saleDate', '$quantity', '$total', '$sellerID', '$productID', '$customerID')";

    if ($conn->query($sql_insert) === TRUE) {
        echo "New sales record saved successfully!";
    } else {
        echo "Error: " . $sql_insert . "<br>" . $conn->error;
    }
} else {
    echo "Invalid product selected.";
}
}

$conn->close();
?>
<!DOCTYPE html>
<html>
    <body>
        <form method="POST" action="temporary.php">
            <label>Seller ID:</label><input type="text" name="sellerID"><br>
            <label>Product ID:</label><input type="text" name="productID"><br>
            <label>Customer ID:</label><input type="text" name="customerID"><br>
            <label>Quantity:</label><input type="number" name="quantity"><br>
            <input type="submit" value="Save">
        </form>
    </body>
</html>

