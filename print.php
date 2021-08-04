<?php
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

					require("fpdf/fpdf.php");
					$pdf = new FPDF('P','mm','A4');

					class PDF extends FPDF{
						function Footer(){
							$this->SetY(-15);
							$this->SetFont('Arial','',6);
							$this->SetTextColor(55,55,55);
							// $this->Cell(0,10,'MyLab 2021 ',0,0,'C');
							$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'R');
						}
					}

					$pdf = new PDF();
					$pdf->AliasNbPages();

					// function user_autoloader($classRegistrasi){
					//     include_once("class/".$classRegistrasi.".php");    
					// }

					// spl_autoload_register('user_autoloader');
					// $obj=new classRegistrasi;

					$pdf->AddPage();

					// $pdf->SetFont("Arial","","6");
					// $pdf->SetTextColor(55,55,55);
					// $pdf->Cell(0,3,"SKRINING MY LAB",0,1,"R");
					// $pdf->Ln(8);

					$pdf->Image('public/mylab.png',10,10,20);
					$pdf->Ln(16);

					$pdf->SetFont("Arial","B","10");
					$pdf->SetFillColor(255,255,255);
					$pdf->SetTextColor(0,0,0);
					$pdf->SetDrawColor(0,0,0);
					$pdf->Cell(190,5,"KARTU KENDALI PELAYANAN VAKSINASI COVID-19","",1,"C",true);
					$pdf->Ln(6);

					// extract($obj->getById($_REQUEST['order_id']));                               
					
					$pdf->SetFont("Arial","","7");
					$pdf->Cell(170,4,"	VERIFIKASI DATA IDENTITAS",1,0,"L",true);	
					$pdf->Cell(20,4, 'Paraf petugas:',1,1,"L",true);

					// START FROM API					
					// if(!empty($peserta['order_detail'][0]['idcard_url'])) {
					// 	$pdf->Cell(30,20, $pdf->Image($peserta['order_detail'][0]['idcard_url'], 12, 42, 25), "TL", 0, 'L', false );
					// } else {
						// $pdf->Cell(30,5,"","TL",0,"L",true);
					// }
					$pdf->Cell(30,5,"Nama",1,0,"L",true);	
					$pdf->Cell(140,5, $peserta['order_detail'][0]['full_name'],1,0,"L",true);
					$pdf->Cell(20,5, '',"LRT",1,"",true);

					// $pdf->Cell(30,5,"","L",0,"L",false);
					$pdf->Cell(30,5,"NIK",1,0,"L",true);	
					$pdf->Cell(140,5, $peserta['order_detail'][0]['personal_id'],1,0,"L",true);
					$pdf->Cell(20,5, '',"LR",1,"",true);

					// $pdf->Cell(30,5,"","L",0,"L",false);
					$pdf->Cell(30,5,"Tanggal Lahir",1,0,"L",true);	
					$pdf->Cell(140,5, $peserta['order_detail'][0]['dob'],1,0,"L",true);
					$pdf->Cell(20,5, '',"LR",1,"",true);

					// $pdf->Cell(30,5,"","L",0,"L",false);
					$pdf->Cell(30,5,"No. HP",1,0,"L",true);	
					$pdf->Cell(140,5, $peserta['order_detail'][0]['phone'],1,0,"L",true);
					$pdf->Cell(20,5, '',"LR",1,"",true);

					// $pdf->Cell(30,5,"","L",0,"L",false);
					$pdf->Cell(30,5,"Alamat","LRT",0,"TL",true);	
					$pdf->Cell(140,5, $peserta['order_detail'][0]['address'],"LRT",0,"TL",true);
					$pdf->Cell(20,5, '',"LR",1,"",true);

					// $pdf->Cell(30,5,"","LR",0,"L",false);
					// $pdf->Cell(30,5,"","LR",0,"TL",true);	
					// $pdf->Cell(140,5, "","LR",0,"TL",true);
					// $pdf->Cell(20,5, '',"LR",1,"",true);

					// $pdf->Cell(30,5,"","LR",0,"L",false);
					// $pdf->Cell(30,5,"","LR",0,"TL",true);	
					// $pdf->Cell(140,5, "","LR",0,"TL",true);
					// $pdf->Cell(20,5, '',"LR",1,"",true);

					// $pdf->Cell(30,7,"","LR",0,"L",false);
					// $pdf->Cell(30,7,"","LR",0,"TL",true);	
					// $pdf->Cell(140,7, "","LR",0,"TL",true);
					// $pdf->Cell(20,7, '',"LR",1,"",true);

					// $pdf->Cell(30,5,"","LB",0,"L",false);
					$pdf->Cell(30,5,"Vaksin yang diberikan",1,0,"L",true);	
					$pdf->Cell(140,5, $peserta['order_detail'][0]['test_name'],1,0,"L",true);
					$pdf->Cell(20,5, '',1,1,"",true);
					// END FROM API

					$pdf->Ln(6);	

					$pdf->Cell(190,4,"SKRINING",1,1,"C",true);

					$pdf->SetFillColor(255,255,255);
					$pdf->Cell(8,4,"No.",1,0,"L",true);	
					$pdf->Cell(90,4, 'Pemeriksaan',1,0,"L",true);
					$pdf->Cell(20,4, 'Hasil',1,0,"C",true);
					$pdf->Cell(72,4, 'Tindak Lanjut',1,1,"",true);

					$pdf->Cell(8,4,"1.",1,0,"L",true);	
					$pdf->Cell(90,4, 'Suhu',1,0,"L",true);
					$pdf->Cell(10,4, '',1,0,"C",true);
					$pdf->Cell(10,4, '',1,0,"C",true);
					$pdf->Cell(72,4, 'Suhu > 37,5 C vaksinasi ditunda sampai sasaran sembuh',1,1,"",true);

					$pdf->Cell(8,4,"2.","LR",0,"L",true);	
					$pdf->Cell(90,4, 'Tekanan Darah',"LR",0,"L",true);
					$pdf->Cell(10,4, '',"LR",0,"C",true);
					$pdf->Cell(10,4, '',"LR",0,"C",true);
					$pdf->Cell(72,4, 'Jika tekanan darah >180/110 mmHg pengukuran tekanan darah',"LR",1,"",true);

					$pdf->Cell(8,4,"","LR",0,"L",true);	
					$pdf->Cell(90,4, '',"LR",0,"L",true);
					$pdf->Cell(10,4, '',"LR",0,"C",true);
					$pdf->Cell(10,4, '',"LR",0,"C",true);
					$pdf->Cell(72,4, 'diulang 5 (lima) sampai 10 (sepuluh) menit kemudian Jika masih',"LR",1,"",true);

					$pdf->Cell(8,4,"","LRB",0,"L",true);	
					$pdf->Cell(90,4, '',"LRB",0,"L",true);
					$pdf->Cell(10,4, '',"LRB",0,"C",true);
					$pdf->Cell(10,4, '',"LRB",0,"C",true);
					$pdf->Cell(72,4, 'tinggi maka vaksinasi ditunda sampai terkontrol',"LRB",1,"",true);

					$pdf->Cell(8,4,"","LRBT",0,"L",true);	
					$pdf->Cell(90,4, 'Pertanyaan',"LRBT",0,"L",true);
					$pdf->Cell(10,4, 'Ya',"LRBT",0,"C",true);
					$pdf->Cell(10,4, 'Tidak',"LRBT",0,"C",true);
					$pdf->Cell(72,4, '',"LRBT",1,"",true);

					$pdf->Cell(8,4,"1.","LR",0,"L",true);	
					$pdf->Cell(90,4, 'Pertanyaan untuk vaksinasi ke-1',"LR",0,"L",true);
					$pdf->Cell(10,4, '',"LR",0,"C",true);
					$pdf->Cell(10,4, '',"LR",0,"C",true);
					$pdf->Cell(72,4, 'Jika Ya: vaksinasi diberikan di Rumah Sakit ',"LR",1,"",true);

					$pdf->Cell(8,4,"","LR",0,"L",true);	
					$pdf->Cell(90,4, 'Apakah Anda memiliki riwayat alergi berat seperti sesak napas, bengkak dan',"LR",0,"L",true);
					$pdf->Cell(10,4, '',"LR",0,"C",true);
					$pdf->Cell(10,4, '',"LR",0,"C",true);
					$pdf->Cell(72,4, '',"LR",1,"",true);

					$pdf->Cell(8,4,"","LRB",0,"L",true);	
					$pdf->Cell(90,4, 'urtikaria seluruh badan atau reaksi berat lainnya karena vaksin?',"LRB",0,"L",true);
					$pdf->Cell(10,4, '',"LRB",0,"C",true);
					$pdf->Cell(10,4, '',"LRB",0,"C",true);
					$pdf->Cell(72,4, '',"LRB",1,"",true);

					$pdf->Cell(8,4,"","LR",0,"L",true);	
					$pdf->Cell(90,4, 'Pertanyaan untuk vaksinasi ke-2',"LR",0,"L",true);
					$pdf->Cell(10,4, '',"LR",0,"C",true);
					$pdf->Cell(10,4, '',"LR",0,"C",true);
					$pdf->Cell(72,4, 'Jika Ya: merupakan kontraindikasi untuk vaksinasi ke-2',"LR",1,"",true);

					$pdf->Cell(8,4,"","LR",0,"L",true);	
					$pdf->Cell(90,4, 'Apakah Anda memiliki riwayat alergi berat setelah divaksinasi COVID-19',"LR",0,"L",true);
					$pdf->Cell(10,4, '',"LR",0,"C",true);
					$pdf->Cell(10,4, '',"LR",0,"C",true);
					$pdf->Cell(72,4, '',"LR",1,"",true);

					$pdf->Cell(8,4,"","LRB",0,"L",true);	
					$pdf->Cell(90,4, 'sebelumnya?',"LRB",0,"L",true);
					$pdf->Cell(10,4, '',"LRB",0,"C",true);
					$pdf->Cell(10,4, '',"LRB",0,"C",true);
					$pdf->Cell(72,4, '',"LRB",1,"",true);

					$pdf->Cell(8,4,"2.","LRB",0,"L",true);	
					$pdf->Cell(90,4, 'Apakah Anda sedang hamil?',"LRB",0,"L",true);
					$pdf->Cell(10,4, '',"LRB",0,"C",true);
					$pdf->Cell(10,4, '',"LRB",0,"C",true);
					$pdf->Cell(72,4, 'Jika sedang hamil vaksinasi ditunda sampai melahirkan',"LRB",1,"",true);

					$pdf->Cell(8,4,"3.","LR",0,"L",true);	
					$pdf->Cell(90,4, 'Apakah Anda mengidap penyakit autoimun seperti asma, lupus.',"LR",0,"L",true);
					$pdf->Cell(10,4, '',"LR",0,"C",true);
					$pdf->Cell(10,4, '',"LR",0,"C",true);
					$pdf->Cell(72,4, 'Jika Ya, maka vaksinasi ditunda jika sedang dalam kondisi akut ',"LR",1,"",true);

					$pdf->Cell(8,4,"","LRB",0,"L",true);	
					$pdf->Cell(90,4, '',"LRB",0,"L",true);
					$pdf->Cell(10,4, '',"LRB",0,"C",true);
					$pdf->Cell(10,4, '',"LRB",0,"C",true);
					$pdf->Cell(72,4, 'atau  belum terkendali',"LRB",1,"",true);

					$pdf->Cell(8,4,"4.","LR",0,"L",true);	
					$pdf->Cell(90,4, 'Apakah Anda sedang mendapat pengobatan untuk gangguan pembekuan darah,',"LR",0,"L",true);
					$pdf->Cell(10,4, '',"LR",0,"C",true);
					$pdf->Cell(10,4, '',"LR",0,"C",true);
					$pdf->Cell(72,4, 'Jika Ya: vaksinasi ditunda dan dirujuk',"LR",1,"",true);

					$pdf->Cell(8,4,"","LRB",0,"L",true);	
					$pdf->Cell(90,4, 'kelainan darah, defisiensi imun dan penerima produk darah/transfusi?',"LRB",0,"L",true);
					$pdf->Cell(10,4, '',"LRB",0,"C",true);
					$pdf->Cell(10,4, '',"LRB",0,"C",true);
					$pdf->Cell(72,4, '',"LRB",1,"",true);

					$pdf->Cell(8,4,"5.","LR",0,"L",true);	
					$pdf->Cell(90,4, 'Apakah Anda sedang mendapat pengobatan immunosupressant seperti',"LR",0,"L",true);
					$pdf->Cell(10,4, '',"LR",0,"C",true);
					$pdf->Cell(10,4, '',"LR",0,"C",true);
					$pdf->Cell(72,4, 'Jika Ya: vaksinasi ditunda dan dirujuk',"LR",1,"",true);

					$pdf->Cell(8,4,"","LRB",0,"L",true);	
					$pdf->Cell(90,4, 'kortikosteroid dan kemoterapi?',"LRB",0,"L",true);
					$pdf->Cell(10,4, '',"LRB",0,"C",true);
					$pdf->Cell(10,4, '',"LRB",0,"C",true);
					$pdf->Cell(72,4, '',"LRB",1,"",true);

					$pdf->Cell(8,4,"6.","LRB",0,"L",true);	
					$pdf->Cell(90,4, 'Apakah Anda memiliki penyakit jantung berat dalam keadaan sesak?',"LRB",0,"L",true);
					$pdf->Cell(10,4, '',"LRB",0,"C",true);
					$pdf->Cell(10,4, '',"LRB",0,"C",true);
					$pdf->Cell(72,4, 'Jika Ya: vaksinasi ditunda dan dirujuk',"LRB",1,"",true);

					$pdf->Cell(190,4,"Pertanyaan Nomor 7 dilanjutkan apabila terdapat penilaian kelemahan fisik pada sasaran vaksinasi.",1,1,"L",true);

					$pdf->Cell(8,4,"7.","LRT",0,"L",true);	
					$pdf->Cell(90,4, 'Pertanyaan tambahan bagi sasaran lansia (>60 tahun):',"LRT",0,"L",true);
					$pdf->Cell(10,4, '',"LRT",0,"C",true);
					$pdf->Cell(10,4, '',"LRT",0,"C",true);
					$pdf->Cell(72,4, 'Jika terdapat 3 atau lebih jawaban Ya maka vaksin tidak',"LRT",1,"",true);

					$pdf->Cell(8,4,"","LR",0,"L",true);	
					$pdf->Cell(90,4, '1. Apakah Anda mengalami kesulitan untuk naik 10 anak tangga?',"LR",0,"L",true);
					$pdf->Cell(10,4, '',"LR",0,"C",true);
					$pdf->Cell(10,4, '',"LR",0,"C",true);
					$pdf->Cell(72,4, 'dapat diberikan',"LR",1,"",true);

					$pdf->Cell(8,4,"","LR",0,"L",true);	
					$pdf->Cell(90,4, '2. Apakah Anda sering merasa kelelahan?',"LR",0,"L",true);
					$pdf->Cell(10,4, '',"LR",0,"C",true);
					$pdf->Cell(10,4, '',"LR",0,"C",true);
					$pdf->Cell(72,4, '',"LR",1,"",true);

					$pdf->Cell(8,4,"","LR",0,"L",true);	
					$pdf->Cell(90,4, '3. Apakah Anda memiliki paling sedikit 5 dari 11 penyakit (Hipertensi, diabetes',"LR",0,"L",true);
					$pdf->Cell(10,4, '',"LR",0,"C",true);
					$pdf->Cell(10,4, '',"LR",0,"C",true);
					$pdf->Cell(72,4, '',"LR",1,"",true);

					$pdf->Cell(8,4,"","LR",0,"L",true);	
					$pdf->Cell(90,4, '    kanker, penyakit paru kronis, serangan jantung, gagal jantung kongestif, nyeri',"LR",0,"L",true);
					$pdf->Cell(10,4, '',"LR",0,"C",true);
					$pdf->Cell(10,4, '',"LR",0,"C",true);
					$pdf->Cell(72,4, '',"LR",1,"",true);

					$pdf->Cell(8,4,"","LR",0,"L",true);	
					$pdf->Cell(90,4, '    dada, asma, nyeri sendi, stroke dan penyakit ginjal)?',"LR",0,"L",true);
					$pdf->Cell(10,4, '',"LR",0,"C",true);
					$pdf->Cell(10,4, '',"LR",0,"C",true);
					$pdf->Cell(72,4, '',"LR",1,"",true);

					$pdf->Cell(8,4,"","LR",0,"L",true);	
					$pdf->Cell(90,4, '4. Apakah Anda mengalami kesulitan berjalan kira-kira 100 sampai 200 meter?',"LR",0,"L",true);
					$pdf->Cell(10,4, '',"LR",0,"C",true);
					$pdf->Cell(10,4, '',"LR",0,"C",true);
					$pdf->Cell(72,4, '',"LR",1,"",true);

					$pdf->Cell(8,4,"","LR",0,"L",true);	
					$pdf->Cell(90,4, 'Apakah Anda mengalami penurunan berat badan yang bermakna dalam',"LR",0,"L",true);
					$pdf->Cell(10,4, '',"LR",0,"C",true);
					$pdf->Cell(10,4, '',"LR",0,"C",true);
					$pdf->Cell(72,4, '',"LR",1,"",true);

					$pdf->Cell(8,4,"","LRB",0,"L",true);	
					$pdf->Cell(90,4, 'setahun terakhir?',"LRB",0,"L",true);
					$pdf->Cell(10,4, '',"LRB",0,"C",true);
					$pdf->Cell(10,4, '',"LRB",0,"C",true);
					$pdf->Cell(72,4, '',"LRB",1,"",true);

					$pdf->Cell(118,4,"HASIL SKRINING:","LRT",0,"L",true);	
					$pdf->Cell(72,4, 'Paraf Petugas:',"LRT",1,"",true);

					$pdf->Cell(5,4,"","LRBT",0,"L",true);
					$pdf->Cell(113,4,"LANJUT VAKSIN","LR",0,"L",true);	
					$pdf->Cell(72,4, '',"LR",1,"",true);

					$pdf->Cell(5,4,"","LRBT",0,"L",true);
					$pdf->Cell(113,4,"TUNDA","LR",0,"L",true);	
					$pdf->Cell(72,4, '',"LR",1,"",true);

					$pdf->Cell(5,4,"","LRBT",0,"L",true);
					$pdf->Cell(113,4,"TIDAK DIBERIKAN","LRB",0,"L",true);	
					$pdf->Cell(72,4, '',"LRB",1,"",true);

					$pdf->SetFillColor(255,255,255);
					$pdf->Cell(190,4,"HASIL VAKSINASI",1,1,"C",true);
					
					$pdf->SetFillColor(255,255,255);
					$pdf->Cell(118,4,"Jenis Vaksin:","LRT",0,"L",true);	
					$pdf->Cell(72,4, 'Paraf Petugas:',"LRT",1,"",true);

					$pdf->Cell(118,4,"No. Batch:","LR",0,"L",true);	
					$pdf->Cell(72,4, '',"LR",1,"",true);

					$pdf->Cell(118,4,"Tanggal Vaksinasi:","LR",0,"L",true);	
					$pdf->Cell(72,4, '',"LR",1,"",true);

					$pdf->Cell(118,4,"Jam Vaksinasi:","LRB",0,"L",true);	
					$pdf->Cell(72,4, '',"LRB",1,"",true);

					$pdf->SetFillColor(255,255,255);
					$pdf->Cell(190,4,"HASIL OBSERVASI",1,1,"C",true);

					$pdf->SetFillColor(255,255,255);
					$pdf->Cell(5,4,"","LRBT",0,"L",true);
					$pdf->Cell(113,4,"Tanpa Keluhan","LRT",0,"L",true);	
					$pdf->Cell(72,4, 'Paraf Petugas:',"LRT",1,"",true);

					$pdf->Cell(5,4,"","LRBT",0,"L",true);
					$pdf->Cell(113,4,"Ada Keluhan, Sebutkan keluhan jika ada....","LR",0,"L",true);	
					$pdf->Cell(72,4, '',"LR",1,"",true);

					$pdf->Cell(5,4,"","LT",0,"L",true);
					$pdf->Cell(113,4,"","R",0,"L",true);	
					$pdf->Cell(72,4, '',"LR",1,"",true);

					$pdf->Cell(5,4,"","LB",0,"L",true);
					$pdf->Cell(113,4,"","RB",0,"L",true);	
					$pdf->Cell(72,4, '',"LRB",1,"",true);

					$pdf->AddPage();
					$pdf->Image($peserta['order_detail'][0]['idcard_url'],15,20,85);

					$pdf->Output();
				
				} else {
		
					echo "<script>alert('Maaf, Data sudah pernah di cetak!!');</script>";
					echo "<script>window.close();</script>";

				}
		} else {			
			echo 'error ' . $code;	
		}

?>