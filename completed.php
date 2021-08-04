<?php include_once('layouts/head.php') ?>

<?php
    function user_autoloader($classRegistrasi){
    include_once("class/".$classRegistrasi.".php");    
    }
    spl_autoload_register('user_autoloader');
    $obj=new classRegistrasi;

    extract($obj->getById($_REQUEST['transaksi_id'],"tag_registrasi"));
?>

<div class='col-md-12'>
    <table class="table">
        <tr>
            <td align="center">
                <img src="https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl=<?php echo $transaksi_id; ?>&choe=UTF-8" title="NIK" /><br>
                <strong><?php echo $nama; ?><br>(NIK: <?php echo $nik; ?>)</strong> 
            </td>
        </tr>
        <tr>
            <td align="center">
                Terima Kasih, Anda sudah berhasil mendaftar vaksinasi. Screenshot QR Barcode ini sebagai bukti Anda sudah mendaftar.
            </td>
        </tr>
        <tr>
            <td align="center">
                <a class="btn btn-warning" href="index">Kembali ke halaman Pendaftaran</a>
            </td>
        </tr>
    </table>
</div>

<?php include_once('layouts/footer.php') ?>