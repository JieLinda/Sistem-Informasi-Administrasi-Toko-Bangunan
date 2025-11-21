<?php
require '../logic/watchdog.php';
session_start();
if(!isset($_SESSION['username'])){
  header("Location: index.php");
}
if(isset($_POST['add_hutang'])){
  $supplier = $_POST['selected_supplier_id'];
  $total = $_POST['total'];
  $due_date = $_POST['due_date'];
  $sql = "INSERT INTO hutang (supplier_id, debt_amount, deadline) VALUES ($supplier, $total, '$due_date')";
  query_no_return($sql);
  checkHutang();
}
if(isset($_POST['delete_hutang'])){
  $debt_id = $_POST['delete_id'];
  $sql = "DELETE FROM hutang WHERE debt_id = $debt_id";
  query_no_return($sql);
}
if(isset($_POST['change_hutang'])){
  $supplier= $_POST['selected_supplier_id'];
  $id = $_POST['selected_hutang_id'];
  $hutang_total = $_POST['total'];
  $due_date = $_POST['due_date'];
  query_no_return("UPDATE hutang SET supplier_id = $supplier, debt_amount = $hutang_total, deadline = '$due_date' WHERE debt_id = $id");
  checkHutang();
}
$suppliers = query("SELECT * FROM supplier");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hutang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src='tailwind.config.js'></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
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

  <!-- Main Form -->
  <main class="container mx-auto bg-white p-6 rounded shadow w-3/4 mt-4">
    <h2 class="text-2xl font-bold mb-4">Add Hutang</h2>
    <form action="" method="post" onsubmit="return validateSupplierSelection()">
        <div class="mb-4">
            <label for="supplier" class="block text-gray-700 font-bold mb-2">Supplier:</label>
            <input type="hidden" name="selected_supplier_id" id="selected_supplier_id_main" required>
            <button class="btn bg-purple-700 hover:bg-purple-500 text-white dropdown-toggle supplier_selector" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                Select Supplier
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
              <?php foreach ($suppliers as $supplier){?>
                <li><a class="dropdown-item main-dropdown" href="#" data-value="<?php echo $supplier['supplier_id']; ?>" data-name="<?php echo $supplier['supplier_name']; ?>"><?php echo $supplier['supplier_name']; ?></a></li>
              <?php } ?>
            </ul>
        </div>
        <div class="mb-4">
            <label for="total" class="block text-gray-700 font-bold mb-2">Total:</label>
            <div class="input-group">
                <span class="input-group-text" id='basic-addon1'>Rp</span>
                <input type="number" name="total" id="total" class="form-control w-full p-2 " required aria-describedby="basic-addon1">
            </div>
        </div>
        <div class="mb-4">
            <label for="due_date" class="block text-gray-700 font-bold mb-2">Due Date:</label>
            <input type="date" name="due_date" id="due_date" class="w-full p-2 form-control" required>
        </div>
        <button type="submit" name="add_hutang" class="bg-purple-700 hover:bg-purple-500 text-white px-4 py-2 rounded">Add Hutang</button>
    </form>
  </main>
  <br>
  <table class="table-auto mx-auto bg-white rounded shadow w-3/4">
      <thead class="bg-gray-200">
        <tr>
          <th class="px-4 py-2 text-left">ID</th>
          <th class="px-4 py-2 text-left">Supplier</th>
          <th class="px-4 py-2 text-left">Total</th>
          <th class="px-4 py-2 text-left">Due Date</th>
          <th class="px-4 py-2 text-left">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $result = query("SELECT * FROM hutang join supplier on hutang.supplier_id = supplier.supplier_id");
        foreach ($result as $row) {
          echo "<tr>
                  <td class='border px-4 py-2'>{$row['debt_id']}</td>
                  <td class='border px-4 py-2'>{$row['supplier_name']}</td>
                  <td class='border px-4 py-2'>{$row['debt_amount']}</td>
                  <td class='border px-4 py-2'>{$row['deadline']}</td>
                  <td class='hidden'>{$row['supplier_id']}</td>
                  <td class='border px-4 py-2'>
                    <div class='flex space-x-2'>
                    <button 
                        onclick=\"openModal('{$row['debt_id']}', '{$row['supplier_id']}', '{$row['debt_amount']}', '{$row['deadline']}', '{$row['supplier_name']}')
                       \"
                        class='bg-green-500 hover:bg-green-800 text-white px-4 py-2 rounded' id='editButton'>Edit
                    </button>
                      <form action='' method='post'>
                        <input type='hidden' name='delete_id' value='{$row['debt_id']}' />
                        <button type='submit' name='delete_hutang' class='bg-red-500 hover:bg-red-800 text-white px-4 py-2 rounded'>Delete</button>
                      </form>
                    </div>
                  </td>
                </tr>";
        }
        ?>  
      </tbody>
  </table>
  <br>

  <!-- Edit Modal -->
  <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded shadow-lg w-96">
      <h2 class="text-xl font-bold mb-4">Edit Hutang</h2>
      <form action="" method="post">
        <div class="mb-4">
            <input type="hidden" name="selected_supplier_id" id="selected_supplier_id_edit">
            <input type="hidden" name="selected_hutang_id" id="selected_hutang_id_edit">
            <label for="supplier" class="block text-gray-700 font-bold mb-2">Supplier:</label>
            <button class="btn bg-purple-700 hover:bg-purple-500 text-white dropdown-toggle supplier_selector" type="button" id="edit-dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                Select Supplier
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
              <?php foreach ($suppliers as $supplier){?>
                <li><a class="dropdown-item edit-dropdown" href="#" data-value="<?php echo $supplier['supplier_id']; ?>" data-name="<?php echo $supplier['supplier_name']; ?>"><?php echo $supplier['supplier_name']; ?></a></li>
              <?php } ?>
            </ul>
        </div>
        <div class="mb-4">
            <label for="total" class="block text-gray-700 font-bold mb-2">Total:</label>
            <div class="input-group">
                <span class="input-group-text" id='basic-addon1'>Rp</span>
                <input type="number" name="total" id="total_edit" class="form-control w-full p-2 " required aria-describedby="basic-addon1">
            </div>
        </div>
        <div class="mb-4">
            <label for="due_date" class="block text-gray-700 font-bold mb-2">Due Date:</label>
            <input type="date" name="due_date" id="due_date_edit" class="w-full p-2 form-control" required>
        </div>
        <div class="flex justify-end space-x-4">
          <button class="bg-gray-500 text-white px-4 py-2 rounded" onclick="closeModal()">Cancel</button>
          <button type="submit" name="change_hutang" class="bg-purple-700 hover:bg-purple-500 text-white px-4 py-2 rounded">Change Hutang</button>
        </div>
    </form>
    </div>
    <script>
      function openModal(hutang_id, supplier_id, total, due_date, supplier_name){
        document.getElementById('selected_hutang_id_edit').value = hutang_id;
        document.getElementById('editModal').classList.remove('hidden');
        document.getElementById('total_edit').value = total;
        document.getElementById('due_date_edit').value = due_date;
        document.getElementById('selected_supplier_id_edit').value = supplier_id;
        document.getElementById('edit-dropdown-toggle').textContent = supplier_name;
        console.log(document.getElementById('selected_supplier_id_edit').value);
        console.log(document.getElementById('total_edit').value);
        console.log(due_date);
        console.log(document.getElementById('selected_hutang_id_edit').value);
      };
      function closeModal(){
        document.getElementById('editModal').classList.add('hidden');
      };
      $('.main-dropdown').click(function(e) {
        let supplierId = $(this).attr('data-value');
        document.getElementById('selected_supplier_id_main').value = supplierId;
        console.log(supplierId);
        $('.supplier_selector').text($(this).attr('data-name'));
      });
      $('.edit-dropdown').click(function(e) {
        let supplierId = $(this).attr('data-value');
        document.getElementById('selected_supplier_id_edit').value = supplierId;
        $('.supplier_selector').text($(this).attr('data-name'));
      });

      function validateSupplierSelection() {
        const selectedSupplierId = document.getElementById('selected_supplier_id_main').value;
        if (!selectedSupplierId) {
          alert('Please select a supplier before submitting the form.');
          return false; // Prevent form submission
        }
        return true; // Allow form submission
      }
  </script>
</body>
</html>