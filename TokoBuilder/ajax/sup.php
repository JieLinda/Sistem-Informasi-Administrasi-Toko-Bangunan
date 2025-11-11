<?php

require '../authen.php';

require '../functions.php';

$keyword = $_GET["keyword"];

$username = $_SESSION["username"];

$query = "SELECT * FROM supplier WHERE nama_supplier LIKE '%$keyword%' AND username = '$username'";

$result = mysqli_query($con, $query);


?>

<table id="supplierTable" class="w-full text-left" style="display: table;">
        <thead>
         <tr class="text-gray-600">
          <th class="py-2">No</th>
          <th class="py-2">Nama Supplier</th>
          <th class="py-2">Nomor Telepon</th>
          <th class="py-2">Action</th>
         </tr>
        </thead>
        <tbody class="text-gray-700">
        <?php 
        $counter = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            
        ?>
        <tr class="border-b">
            <td class="py-2"><?php echo $counter++; ?></td>
            <td class="py-2"><?php echo $row['nama_supplier']; ?></td>
            <td class="py-2"><?php echo $row['nomor_telepon']; ?></td>
            <td>
            <!-- Form untuk Update -->
            <a class="btn btn-primary" href="update_supplier.php?id=<?= $row["supplier_id"]; ?>">Update</a>
            <?php $_SESSION["id"] = $row["supplier_id"] ?>
            <!-- Form untuk Delete -->
            <a class="btn btn-danger" href="delete_supplier.php?id=<?= $row["supplier_id"]; ?>" onclick="return confirm('Yakin?')">Delete</a>
        </td>
        </tr>
        <?php } ?>
        </tbody>
       </table>