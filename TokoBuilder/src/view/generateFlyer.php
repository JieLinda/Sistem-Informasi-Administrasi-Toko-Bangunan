<?php 
    require '../logic/tools.php';
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "SELECT * FROM produk";
    $result = $conn->query($sql);


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['options'])) {
            $selectedOptions = $_POST['options'];
        }

        if(isset($_POST['discount'])) {
            $discount = $_POST['discount'];
            
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function validateForm() {
            const checkboxes = document.querySelectorAll('input[name="options[]"]');
            const checkboxSelected = Array.from(checkboxes).some(checkbox => checkbox.checked);

            
            const radios = document.querySelectorAll('input[name="discount"]');
            const radioSelected = Array.from(radios).some(radio => radio.checked);

            if (!checkboxSelected) {
                alert("Please select at least one product.");
                return false;
            }

            if (!radioSelected) {
                alert("Please select a discount option.");
                return false;
            }

            return true; 
        }
    </script>
</head>
<body>
<header class="bg-purple-700 text-white py-3 px-6">
    <div class="container mx-auto flex items-center justify-between">
    <a href="headerManager/dashboard.php" class="hover:underline font-bold">Sistem Informasi</a>
      <nav class="flex space-x-6">
      <a href="headerManager/generate.php" class="hover:underline">Flyer/Catalog</a>
        <a href="headerManager/restock.php" class="hover:underline">Restock</a>
        <a href="headerManager/customers.php" class="hover:underline">Customers</a>
        <a href="headerManager/sales.php" class="hover:underline">Sales</a>
        <a href="headerManager/products.php" class="hover:underline">Products</a>
        <a href="headerManager/suppliers.php" class="hover:underline">Suppliers</a>
        <a class="hover:underline dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Finance
            <span class="caret"></span></a>
        <ul class="dropdown-menu">
            <li class="dropdown-item"><a href="headerManager/hutang.php">Hutang</a></li>
            <li class="dropdown-item"><a href="headerManager/piutang.php">Piutang</a></li>
            <li class="dropdown-item"><a href="headerManager/pendapatan.php">Pendapatan</a></li>
        </ul>
        <a href="../logout.php" class="hover:underline">Logout</a>
      </nav>
    </div>
  </header>

    <div id="main-container" class="flex justify-center mt-5">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h2 class="text-2xl font-bold mb-4">Select Options</h2>
            <form method="POST" action="flyerGeneration.php" onsubmit="return validateForm()">
                <?php while($row = $result->fetch_assoc()):  ?>
                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="options[]" value="<?= $row["product_id"] ?>" class="form-checkbox text-indigo-600">
                        <span class="ml-2"><?= $row["product_name"] ?></span>
                    </label>
                </div>
                <?php endwhile; ?>

                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="discount"  value="diskon" class="form-radio text-indigo-600">
                        <span class="ml-2">Diskon 5%</span>
                    </label>
                </div>

                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="discount" value="none" class="form-radio text-indigo-600">
                        <span class="ml-2">None</span>
                    </label>
                </div>
                <button type="submit" action="" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-500">
                    Submit
                </button>
            </form>
        </div>
    </div>
    

</body>
</html>