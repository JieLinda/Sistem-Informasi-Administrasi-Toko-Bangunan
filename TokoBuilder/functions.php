<?php
require 'db.php';
function registrasi($data){
    global $con;
    
    $username = strtolower($data["username"]);
    $password = $data["password"];

    // cek username sudah ada atau belum
    $res = mysqli_query($con,"SELECT username FROM user WHERE username = '$username'");
    if ( mysqli_fetch_assoc($res) ){
        echo "<script>
        alert('Username sudah terdaftar!')
        </script>";
        
        return false;
    }

    // enkripsi password
    $password = password_hash($password, PASSWORD_DEFAULT);

    // add user ke database
    $stmt = $con->prepare("INSERT INTO user VALUES (?,?)");
    $stmt->bind_param("ss",$username,$password);

    if ($stmt->execute()){
        echo "<script>
        alert('User berhasil ditambahkan!');
        </script>";
        return mysqli_affected_rows($con);
        // header("Location:login.php");
        // exit;
    } else {
        echo "Error: " . $stmt->error;
        return false;
    }

    $stmt->close();
}

function hapuspro($id){
    global $con;
    $stmt = $con->prepare("DELETE FROM produk WHERE produk_id = ?");
    $stmt->bind_param("s",$id);
    
    if ($stmt->execute()){
        echo "<script>
        alert('Produk berhasil dihapus!');
        </script>"; 
        $stmt->close();
        return mysqli_affected_rows($con);
    } else {
        echo "Error: " . $stmt->error;
        $stmt->close();
        return false;
    }
}

function hapussup($id){
    global $con;
    $stmt = $con->prepare("DELETE FROM supplier WHERE supplier_id = ?");
    $stmt->bind_param("s",$id);
    
    if ($stmt->execute()){
        echo "<script>
        alert('Supplier berhasil dihapus!');
        </script>"; 
        $stmt->close();
        return mysqli_affected_rows($con);
    } else {
        echo "Error: " . $stmt->error;
        $stmt->close();
        return false;
    }
}

function updatepro($data,$id){

    global $con;

        $nama = $data["nama_produk"];
        $stok = $data["stok"];
        $harga = $data["harga_jual"];

        $stmt = $con->prepare("UPDATE produk SET nama_produk = ?, stok = ?, harga_jual = ? WHERE produk_id = ?");
        $stmt->bind_param("ssss",$nama,$stok,$harga,$id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Produk berhasil diupdate!";
            return mysqli_affected_rows($con);
        } else {
            $_SESSION['message'] = "Produk gagal diupdate!";
            return false;
        }
        header("Location: product.php");
        exit();

}

function updatesup($data,$id){

    global $con;

        $nama = $data["nama_supplier"];
        $notelp = $data["nomor_telepon"];

        $stmt = $con->prepare("UPDATE supplier SET nama_supplier = ?, nomor_telepon = ? WHERE supplier_id = ?");
        $stmt->bind_param("sss",$nama,$notelp,$id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Supplier berhasil diupdate!";
            return mysqli_affected_rows($con);
        } else {
            $_SESSION['message'] = "Supplier gagal diupdate!";
            return false;
        }
        header("Location: supplier.php");
        exit();

}
?>