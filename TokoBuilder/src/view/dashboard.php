<?php
    session_start();
    require '../logic/tools.php';
    if(!isset($_SESSION['username'])){
        header("Location: index.php");
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
    <script src='tailwind.config.js'></script>
    <style>
        li {
            cursor: pointer;
            pointer-events: auto;
        }
        li a{
            display: block;
        }
    </style>
</head>
<body>
    <!-- Header -->
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

  <!-- Content -->
   <div class="d-flex flex-col items-center gap-4 container mx-auto py-6">
        <h1 class="display-4 font-bold">Welcome, <?php echo $_SESSION['username']; ?></h1>
        <div class="row container-fluid h-100 bg-white rounded shadow mx-auto">
            <div id="hutang" class=" flex-fill col-4 p-4">
            <h1 class="display-5">Hutang Warning:</h1>
            <br>
                <?php foreach(query("select distinct h.debt_amount, s.supplier_name, h.deadline from warning_hutang w join hutang h on w.debt_id = h.debt_id join supplier s on h.supplier_id = s.supplier_id") as $warning) :?>
                    <div class="alert alert-danger" role="alert">
                        <h1 class="font-bold"><?php echo $warning['supplier_name']; ?></h1>
                        <p class="mb-0">Total Owed : <?php echo $warning['debt_amount']; ?></p>
                        <p class="mb-0">Deadline : <?php echo $warning['deadline']; ?></p>
                        <p class="mb-0">Overdue : <?php echo date_diff(date_create(date('Y-m-d')), date_create($warning['deadline'])) ->format('%R%a days');?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <div id="piutang" class=" flex-fill col-4 p-4">
                <h1 class="display-5">Piutang Warning:</h1>
                <br>
                    <?php foreach(query("SELECT distinct customer_name, receivable_amount, deadline FROM warning_piutang w join piutang p on w.receivable_id = p.receivable_id join customer c on p.customer_id = c.customer_id") as $warning) :?>
                        <div class="alert alert-danger" role="alert">
                            <h1 class="font-bold"><?php echo $warning['customer_name']; ?></h1>
                            <p class="mb-0">Total Owed : <?php echo $warning['receivable_amount']; ?></p>
                            <p class="mb-0">Deadline : <?php echo $warning['deadline']; ?></p>
                            <p class="mb-0">Overdue : <?php echo date_diff(date_create(date('Y-m-d')), date_create($warning['deadline'])) ->format('%R%a days');?></p>
                        </div>
                    <?php endforeach; ?>
            </div>
            <div id="stok" class="flex-fill col-4 items-center p-4">
                <h1 class="display-5">Stock Warning:</h1>
                <br>
                <?php foreach(query("SELECT distinct p.product_name, p.stock, p.min_stock FROM warning_stock w join produk p on w.product_id = p.product_id ") as $warning) :?>
                    <div class="alert alert-danger" role="alert">
                        <h1 class="font-bold"><?php echo $warning['product_name']; ?></h1>
                        <p class="mb-0">Current Stock : <?php echo $warning['stock']; ?></p>
                        <p class="mb-0">Minimum Stock : <?php echo $warning['min_stock']; ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
   </div>
</body>
</html>