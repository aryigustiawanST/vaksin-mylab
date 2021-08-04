<?php 
    error_reporting(0);
    include_once('layouts/head.php') 
?>

<script type="text/javascript" src="public/instascan.min.js"></script>
<script type="text/javascript" src="public/adapter.min.js"></script>
<script type="text/javascript" src="public/vue.min.js"></script>

<style>
    @media print {
        @page {
            margin-top: 20;
            margin-bottom: 10;
        }
        #btn-cetak {
            display: none;
        }
        #barcodescan {
            display: none;
        }
    }
</style>

<?php
    function user_autoloader($classRegistrasi){
        include_once("class/".$classRegistrasi.".php");    
    }
    spl_autoload_register('user_autoloader');
    $obj=new classRegistrasi;
    // extract($obj->getById($_REQUEST['order_id'],"tag_registrasi"));        
?>

<div class='col-md-12' id="barcodescan">
    <form method="post" action="print.php" target="_blank">       
        <table class="table">
            <tr align="center">
                <td>
                    <video id="preview" width="250px"></video>
                </td>
            </tr>
            <tr align="center">
                <td>
                    <div class="btn-group btn-group-toggle mb-5" data-toggle="buttons">
                        <label class="btn btn-primary active">
                            <input type="radio" name="options" value="1" autocomplete="off" checked> Front Camera
                        </label>
                        <label class="btn btn-secondary">
                            <input type="radio" name="options" value="2" autocomplete="off"> Back Camera
                        </label>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="text" class="form-control" id="order_id" name="order_id" value="" placeholder="Masukkan QR Barcode">
                </td>
            </tr>
            <tr>
                <td>
                    <button class="btn btn-block btn-success" type="submit" name="cari">Cetak</button>    
                </td>
            </tr>
        </table>

        <script>
            // let scanner = new Instascan.Scanner({ video: document.getElementById('preview')});
            // Instascan.Camera.getCameras().then(function(cameras){
            //     if(cameras.length > 0 ){
            //         scanner.start(cameras[0]);
            //     } else{
            //         alert('No cameras found');
            //     }

            // }).catch(function(e) {
            //     console.error(e);
            // });

            // scanner.addListener('scan',function(c){
            //     document.getElementById('order_id').value=c;
            // });

            var scanner = new Instascan.Scanner({ video: document.getElementById('preview'), scanPeriod: 5, mirror: false });
            scanner.addListener('scan',function(c){
                // alert(content);
                document.getElementById('order_id').value=c;
                //window.location.href=content;
            });
            
            Instascan.Camera.getCameras().then(function (cameras){
                if(cameras.length>0){
                    scanner.start(cameras[0]);
                    $('[name="options"]').on('change',function(){
                        if($(this).val()==1){
                            if(cameras[0]!=""){
                                scanner.start(cameras[0]);
                            }else{
                                alert('No Front camera found!');
                            }
                        }else if($(this).val()==2){
                            if(cameras[1]!=""){
                                scanner.start(cameras[1]);
                            }else{
                                alert('No Back camera found!');
                            }
                        }
                    });
                }else{
                    console.error('No cameras found.');
                    alert('No cameras found.');
                }
            }).catch(function(e){
                console.error(e);
                alert(e);
            });
        </script>
    </form>
</div>

<div class='col-md-12' style="font-size: 14px;">
    
    <?php 
        if(isset($_REQUEST['cari'])) {
            extract($obj->getById($_REQUEST['order_id']));   
        } 
    ?>
</div>

    <script>
        function printpage() {
            // var barcodescan = document.getElementById("barcodescan");
            var printBtn = document.getElementById("btn-cetak");
            // barcodescan.style.visibility = 'hidden';
            // printBtn.style.visibility = 'hidden';
            window.print()
            // printButton.style.visibility = 'visible';
            // skriningBtn.style.visibility = 'visible';
        }
    </script>

<?php include_once('layouts/footer.php') ?>