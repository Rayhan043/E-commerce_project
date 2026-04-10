<?php
if (isset($_GET['id'])) {
    header("Location: /E-comm/admin/add-product.php?id=" . $_GET['id']);
} else {
    header("Location: /E-comm/admin/products.php");
}
exit;
?>


