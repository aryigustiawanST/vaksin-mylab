<?php

class classRegistrasi {

    public function __construct()
    {
        // require_once 'classKoneksi.php';
		// $koneksi = new Koneksi();
		// $this->conn = $koneksi->connect();

    }

	public function insertData($nama, $nik, $tanggal_lahir, $no_hp, $alamat, $vaksin_dosis_satu, $table) {		
		
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $transaksi_id = '';
        for ($i = 0; $i < 15; $i++) {
            $transaksi_id .= $characters[rand(0, $charactersLength - 1)];
        }

        // echo $transaksi_id;die;

        $sql = "INSERT INTO $table SET 
				transaksi_id=:transaksi_id,
		        nama=:nama,
		        nik=:nik,
		        tanggal_lahir=:tanggal_lahir,
		        no_hp=:no_hp,
		        alamat=:alamat,
		        vaksin_dosis_satu=:vaksin_dosis_satu";
		$q = $this->conn->prepare($sql);
		$q->execute(array(':transaksi_id'=>$transaksi_id,':nama'=>$nama,':nik'=>$nik,':tanggal_lahir'=>$tanggal_lahir,':no_hp'=>$no_hp,':alamat'=>$alamat,':vaksin_dosis_satu'=>$vaksin_dosis_satu));

        header("location: completed?transaksi_id=".$transaksi_id);
        // return true;
	
    }

    public function showRegistrasi($table) {			

        $sql = "SELECT * FROM $table";
		$q = $this->conn->query($sql) or die("failed!");
		
		while($r = $q->fetch(PDO::FETCH_ASSOC)){
			$data[]=$r;
		}
		return $data;	
	
    }

    public function getById($id) {

        header('Content-type: text/html; charset=utf-8');
        $url = "https://api.mylab.co.id/api/v1/login";
        $User_Agent = 'Mozilla/5.0 (Windows NT 6.1; rv:60.0) Gecko/20100101 Firefox/60.0';

        $request_headers[] = 'Contect-Type:application/json';
        $request_headers[] = 'Content-type: application/json';
        
        $dataToken = array (
            'email' => 'dev@mylab.co.id',
            'password' => 'Tebet!!2018',
        );

        $data_json = json_encode($dataToken);
		$request_headers[] = 'Content-Length: ' . strlen($data_json);

        $ch = curl_init($url);
        // Set the url      
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_USERAGENT, $User_Agent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");

        // Execute
        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $data = json_decode($result, true);

        if ($code == 200) {
			
			$result = json_decode($result, true);
            $array = array($result);
            $token = $array[0]['data']['token']; 
			$orgid = $array[0]['data']['org'][0]['id'];

			$dataPeserta = array (
				'token' => $token,
			);

			header('Content-type: text/html; charset=utf-8');
			$url = "https://api.mylab.co.id/api/v1/operational/orders/".$_POST['order_id'];
			$User_Agent = 'Mozilla/5.0 (Windows NT 6.1; rv:60.0) Gecko/20100101 Firefox/60.0';
			
			$data_json_peserta = json_encode($dataPeserta);
			$request_headers_m[] = 'Contect-Type:application/json';
			$request_headers_m[] = 'Content-type: application/json';  
			$request_headers_m[] = 'Authorization: Bearer '.$token;
			$request_headers_m[] = 'org-id: '.$orgid;

                $ch = curl_init($url);
                
                curl_setopt($ch, CURLOPT_URL, $url );
                curl_setopt($ch, CURLOPT_USERAGENT, $User_Agent);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers_m);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json_peserta);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_ENCODING, "");
                
                $result_peserta = curl_exec($ch);
                $codeM = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                $data_peserta = json_decode($result_peserta, true);
                $peserta = $data_peserta['data'];

                if($codeM == 200) {
                    // print_r($data_peserta);
                    // die;
                    echo '<a href="print_pdf?order_id='.$_POST['order_id'].'" target="_blank" class="btn btn-block btn-primary" id="btn-cetak">Cetak Skrining</a>
                    <h4 align="center">KARTU KENDALI PELAYANAN VAKSINASI COVID-19</h4><br>

                    <table class="table table-bordered">
                        <tr style="background: #00b3d1">
                            <td colspan="2"><strong>VERIFIKASI DATA IDENTITAS</strong></td>
                            <td>Paraf Petugas</td>
                        </tr>
                        <tr>
                            <td><strong>Nama</strong></td>
                            <td>: '. $peserta['order_detail'][0]['full_name'] .'</td>
                            <td rowspan="5"></td>
                        </tr>
                        <tr>
                            <td><strong>NIK</strong></td>
                            <td>: '. $peserta['order_detail'][0]['personal_id'] .'</td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Lahir</strong></td>
                            <td>: '. $peserta['order_detail'][0]['dob'] .'</td>
                        </tr>
                        <tr>
                            <td><strong>No. HP</strong></td>
                            <td>: '. $peserta['order_detail'][0]['phone'] .'</td>
                        </tr>
                        <tr>
                            <td><strong>Alamat</strong></td>
                            <td>: '. $peserta['order_detail'][0]['address'] .'</td>
                        </tr>
                        <tr>
                            <td><strong>Vaksin yang diberikan pada dosis 1</strong></td>
                            <td>: '. $peserta['order_detail'][0]['test_name'] .'</td>
                            <td></td>
                        </tr>                
                    </table>
                    
                    <table class="table table-bordered">
                        <tr style="background: #00b3d1;text-align:center">
                            <td colspan="5"><strong>SKRINING</strong></td>
                        </tr>
                        <tr style="text-align:center">
                            <td>No.</td>
                            <td width="40%">Pemeriksaan</td>
                            <td colspan="2" width="20%">Hasil</td>
                            <td>Tindak Lanjut</td>
                        </tr>
                
                        <tr>
                            <td>1.</td>
                            <td>Suhu</td>
                            <td></td>
                            <td></td>
                            <td>Suhu > 37,5 &deg;C <strong>vaksinasi ditunda</strong> sampai sasaran sembuh</td>
                        </tr>
                
                        <tr>
                            <td>2.</td>
                            <td>Tekanan Darah</td>
                            <td></td>
                            <td></td>
                            <td>
                                Jika tekanan darah <strong> >180/110 mmHg </strong> pengukuran tekanan darah diulang 5 (lima) sampai 10 (sepuluh) menit kemudian Jika masih tinggi maka <strong>vaksinasi ditunda</strong> sampai terkontrol
                            </td>
                        </tr>
                
                        
                        <tr>
                            <td>1</td>
                            <td><strong>Pertanyaan untuk vaksinasi ke-1</strong><br>Apakah Anda memiliki riwayat alergi berat seperti sesak napas, bengkak dan urtikaria seluruh badan atau reaksi berat lainnya karena vaksin?</td>
                            <td></td>
                            <td></td>
                            <td>Jika Ya : vaksinasi diberikan di Rumah Sakit atau tidak diberikan lagi untuk vaksinasi ke-2</td>
                        <tr>
                        
                        <tr>
                            <td>2</td>
                            <td><strong>Pertanyaan untuk vaksinasi ke-2</strong><br>Apakah Anda memiliki riwayat alergi berat atau mengalami gejala sesak napas, bengkak dan uktikaria seluruh badan setelah divaksinasi COVID-19 sebelumnya?</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        <tr>
                        
                        <tr>
                            <td>3</td>
                            <td>Apakah Anda sedang hamil atau menyusui?</td>
                            <td></td>
                            <td></td>
                            <td>Jika sedang hamil vaksinasi ditunda sampai melahirkan.</td>
                        <tr>
                        
                        <tr>
                            <td>4</td>
                            <td>Apakah Anda mengidap penyakit autoimun seperti asma, lupus.</td>
                            <td></td>
                            <td></td>
                            <td>Jika Ya, maka vaksinasi ditunda jika sedang dalam kondisi akut atau  belum terkendali</td>
                        <tr>
                        
                        <tr>
                            <td>5</td>
                            <td>Apakah Anda sedang mendapat pengobatan immunosupressant seperti kortikosteroid dan kemoterapi?</td>
                            <td></td>
                            <td></td>
                            <td>Jika Ya: vaksinasi ditunda dan dirujuk
                        </td>
                        <tr>
                        
                        <tr>
                            <td>6</td>
                            <td>Apakah Anda memiliki penyakit jantung berat dalam keadaan sesak?</td>
                            <td></td>
                            <td></td>
                            <td>Jika Ya: vaksinasi ditunda dan dirujuk</td>
                        <tr>
                
                        <tr>
                            <td colspan="5"><i>Pertanyaan Nomor 7 dilanjutkan apabila terdapat penilaian kelemahan fisik pada sasaran vaksinasi.</i></td>
                        <tr>
                        
                        <tr>
                            <td>7</td>
                            <td>Pertanyaan tambahan bagi sasaran lansia (≥60 tahun):<br>
                                1.	Apakah Anda mengalami kesulitan untuk naik 10 anak tangga?<br>
                                2.	Apakah Anda sering merasa kelelahan?<br>
                                3.	Apakah Anda memiliki paling sedikit 5 dari 11 penyakit (Hipertensi, diabetes, kanker, penyakit paru kronis, serangan jantung, gagal jantung kongestif, nyeri dada, asma, nyeri sendi, stroke dan penyakit ginjal)?<br>
                                4.	Apakah Anda mengalami kesulitan berjalan kira-kira 100 sampai 200 meter?<br>
                                Apakah Anda mengalami penurunan berat badan yang bermakna dalam setahun terakhir?
                            </td>
                            <td></td>
                            <td></td>
                            <td>Jika terdapat 3 atau lebih jawaban Ya maka vaksin tidak dapat diberikan</td>
                        <tr>
                        
                        <tr>
                            <td colspan="4">
                                <strong>
                                Hasil Skrining<br>
                                <img style="width: 20px;height: 20px;border 5px solid black"> LANJUT VAKSIN<br>
                                <img style="width: 20px;height: 20px;border 5px solid black"> TUNDA<br>
                                <img style="width: 20px;height: 20px;border 5px solid black"> TIDAK DIBERIKAN<br>
                                </strong>
                            </td>
                            <td>
                                Paraf Petugas:
                            </td>
                        </tr>
                        
                        <tr style="background: #00b3d1;">
                            <td colspan="5"><strong>HASIL VAKSINASI</strong></td>
                        </tr>
                
                        <tr>
                            <td colspan="2">
                                Jenis Vaksin:<br>
                                No. Batch:<br>
                                Tanggal Vaksinasi:<br>
                                Jam Vaksinasi:<br>
                            </td>
                            <td colspan="2"></td>
                            <td>
                                Paraf Petugas:
                            </td>
                        </tr>
                
                        <tr style="background: #00b3d1;">
                            <td colspan="5"><strong>HASIL OBSERVASI</strong></td>
                        </tr>
                
                        <tr>
                            <td colspan="4">
                                <img style="width: 20px;height: 20px;border 5px solid black"> Tanpa Keluhan<br>
                                <img style="width: 20px;height: 20px;border 5px solid black"> Ada Keluhan, Sebutkan keluhan jika ada<br>                
                            </td>
                            <td>
                                Paraf Petugas:
                            </td>
                        </tr>
                
                    </table>';

                } else {

                    echo "<strong><h3>Maaf, data sudah pernah di cetak!</h3></strong>";

                }

        } else {
            echo 'error ' . $code;
        }

		// print_r($data);
		// die;
    
    }
    
}