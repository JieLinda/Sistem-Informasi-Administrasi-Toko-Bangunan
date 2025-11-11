<?php

require '../functions.php';

require '../authen.php';

$username = $_SESSION["username"];

$keyword = $_GET["keyword"];

$query = "SELECT * FROM produk WHERE nama_produk LIKE '%$keyword%' AND username = '$username'";

$result = mysqli_query($con, $query);


?>
<table id="prodTab" class="w-full text-left" style="display: table;">
        <thead>
         <tr class="text-gray-600">
          <th class="py-2">No</th>
          <th class="py-2">Nama Produk</th>
          <th class="py-2">Stok</th>
          <th class="py-2">Harga Jual</th>
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
            <td class="py-2"><?php echo $row['nama_produk']; ?></td>
            <td class="py-2"><?php echo $row['stok']; ?></td>
            <td class="py-2"><?php echo $row['harga_jual']; ?></td>
            <td>
            <!-- Form untuk Update -->
            <a class="btn btn-primary" href="update_product.php?id=<?= $row["produk_id"]; ?>">Update</a>
            <?php $_SESSION["id"] = $row["produk_id"] ?>
            <!-- Form untuk Delete -->
             <a class="btn btn-danger" href="delete_product.php?id=<?= $row["produk_id"]; ?>" onclick="return confirm('Yakin?')">Delete</a>
        </td>
        </tr>
        <?php } ?>
        </tbody>
       </table>