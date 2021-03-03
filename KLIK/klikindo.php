<?php

$headers = array();
$headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:82.0) Gecko/20100101 Firefox/82.0';
$headers[] = 'Accept-Language: en-US,en;q=0.5';
$headers[] = 'Content-Type: application/json';

echo " KLIK Account Creator \n";
regis:
echo " NOMER: ";
$nomer = trim(fgets(STDIN));



	$data = file_get_contents("https://wirkel.com/data.php?qty=1&domain=tinta.co.id");
	$datas = json_decode($data);
	$nama = $datas->result[0]->firstname;
	$nama2 = $datas->result[0]->lastname;

	$reg = curl('https://account-api-v1.klikindomaret.com/api/PreRegistration/SendOTPSMS?NoHP='.$nomer, null, $headers);
	if (strpos($reg[1], '"Message":"Kode OTP berhasil dikirim ke nomor telepon Anda."')) {
		echo " OTP: ";
		$otp = trim(fgets(STDIN));
		$reg2 = curl('https://account-api-v1.klikindomaret.com/api/PreRegistration/ValidationOTPCodeSMS?NoHP='.$nomer.'&otpCode='.$otp, null, $headers);
		if (strpos($reg2[1], '"Message":"Verifikasi berhasil dilakukan."')) {
			echo "PROSES DAFTAR ... \n";
			$reg3 = curl('https://account-api-v1.klikindomaret.com/api/Customer/Registration?districtID=2483&mfp_id=1', '{"nomor":"","isVaildPhoneNo":false,"messageError":"","Mobile":"'.$nomer.'","Email":null,"FName":"'.$nama.'","LName":"'.$nama2.'","Password":"sayaganteng","ConfirmPassword":"sayaganteng","IsConfirmed":true,"valueDate":"","isLoading":false,"ID":"00000000-0000-0000-0000-000000000000","IPAddress":"192.168.56.131","IsSubscribed":0,"IsNewsLetterSubscriber":0,"AllowSMS":false,"LastUpdate":"0001-01-01T00:00:00","DateOfBirth":"1993-03-'.rand(01, 30).'T00:00:00.000Z","Gender":"Wanita","DateOfBirthStringFormatted":"1993-03-'.rand(01, 30).'","TypePushEmail":0,"IsUpload":false,"IsActivated":false,"MobileVerified":true,"DateOfBirthExists":"0001-01-01T00:00:00","OTPValidationExpired":false,"IsFromOtherSystem":false,"OTPCount":0,"OTPAvailable":0,"IsNewAccount":true,"Origin":"Registrasi Website"}', $headers);
			if (strpos($reg3[1], '"Message":"Success"')) {
				echo "SUKSES DAFTAR : $nomer | sayaganteng \n";
				echo "BUAT LAGI? (y/n): ";
				$yn = trim(fgets(STDIN));
				if ($yn == 'y') goto regis;
				} else {
		die($reg3[1]);

			}
			} else {
		die($reg2[1]);
	}
	} else {
		die($reg[1]);
	}


function curl($url,$post,$headers,$follow=false,$method=null)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if ($follow == true) curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		if ($method !== null) curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		if ($headers !== null) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		if ($post !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		$result = curl_exec($ch);
		$header = substr($result, 0, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
		$body = substr($result, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
		preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $result, $matches);
		$cookies = array();
		foreach($matches[1] as $item) {
		  parse_str($item, $cookie);
		  $cookies = array_merge($cookies, $cookie);
		}
		return array (
		$header,
		$body,
		$cookies
		);
	}

function get_between($string, $start, $end) 
    {
        $string = " ".$string;
        $ini = strpos($string,$start);
        if ($ini == 0) return "";
        $ini += strlen($start);
        $len = strpos($string,$end,$ini) - $ini;
        return substr($string,$ini,$len);
    }

function remove_space($var) {
    $new = str_replace("\n", "", $var);
    $new = str_replace("\t", "", $new);
    $new = str_replace(" ", "", $new);
    return $new;
}

function color($color = "default" , $text)
    {
        $arrayColor = array(
            'red'       => '1;31',
            'green'     => '1;32',
            'yellow'    => '1;33',
            'blue'      => '1;34',
        );  
        return "\033[".$arrayColor[$color]."m".$text."\033[0m";
    }

function save($data, $file) 
	{
		$handle = fopen($file, 'a+');
		fwrite($handle, $data);
		fclose($handle);
	}
