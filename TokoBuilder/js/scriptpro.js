// ambil elemen yg dibutuhkan
var keyword = document.getElementById('keyword');
var conpro = document.getElementById('conpro');

keyword.addEventListener('keyup', function(){
    
    // buat objek ajax
    var xhr = new XMLHttpRequest();

    // cek kesiapan ajax
    xhr.onreadystatechange = function() {
        if ( xhr.readyState == 4 && xhr.status == 200 ){
            conpro.innerHTML = xhr.responseText;
        }
    }

    xhr.open('GET', 'ajax/pro.php?keyword=' + keyword.value, true);
    xhr.send();
});