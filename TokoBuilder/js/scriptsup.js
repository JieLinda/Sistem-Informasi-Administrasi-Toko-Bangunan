// ambil elemen yg dibutuhkan
var keyword = document.getElementById('keyword');
var consup = document.getElementById('consup');

keyword.addEventListener('keyup', function(){
    
    // buat objek ajax
    var xhr = new XMLHttpRequest();

    // cek kesiapan ajax
    xhr.onreadystatechange = function() {
        if ( xhr.readyState == 4 && xhr.status == 200 ){
            consup.innerHTML = xhr.responseText;
        }
    }

    xhr.open('GET', 'ajax/sup.php?keyword=' + keyword.value, true);
    xhr.send();
});