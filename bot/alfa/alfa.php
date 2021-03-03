<?php
//alfagift
function urut($panjang)
{
    $karakter= 'abcdefghijklmnopqrstuvwxyz123456789';
    $string = '';
    for ($i = 0; $i < $panjang; $i++) {
  $pos = rand(0, strlen($karakter)-1);
  $string .= $karakter{$pos};
    }
    return $string;
}
function nama()
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://ninjaname.horseridersupply.com/indonesian_name.php");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		$ex = curl_exec($ch);
		preg_match_all('~(&bull; (.*?)<br/>&bull; )~', $ex, $name);
		return $name[2][mt_rand(0, 14) ];
	}
function acak($panjang)
{
    $karakter= '123456789';
    $string = '';
    for ($i = 0; $i < $panjang; $i++) {
  $pos = rand(0, strlen($karakter)-1);
  $string .= $karakter{$pos};
    }
    return $string;
}
function acaka($panjang)
{
    $karakter= 'ABCDEFGHIJKLMNOPQRSTUVWXY123456789';
    $string = '';
    for ($i = 0; $i < $panjang; $i++) {
  $pos = rand(0, strlen($karakter)-1);
  $string .= $karakter{$pos};
    }
    return $string;
}
function curl($url, $header, $mode="get", $data=0)
	{
	if ($mode == "get" || $mode == "Get" || $mode == "GET")
		{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
                curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
		$result = curl_exec($ch);
		}
	elseif ($mode == "post" || $mode == "Post" || $mode == "POST")
		{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
		curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		$result = curl_exec($ch);
		}
	else
		{
		$result = "Not define";
		}
	return $result;
	}
	
$latitude = array(
"-6.201027",
"-7.161367",
"5.548290",
"-8.086410",
" -6.574958",
"-6.347891");
$longatitude = array(
"106.816666",
"113.482498",
"95.323753",
"111.713127",
"110.670525",
"106.741158");
$random_latitude=$latitude[mt_rand(0,sizeof($latitude)-1)];
$random_longatitude=$longatitude[mt_rand(0,sizeof($longatitude)-1)];


$depicemodel = array(
"Xiaomi Redmi Note 3",
"Xiaomi Redmi Note 3 Pro",
"Xiaomi Redmi Note 9",
"Xiaomi Redmi Note 9 Pro",
"Xiaomi Redmi Note 8",
"Xiaomi Redmi Note 5A",
"Xiaomi Redmi Note 5",
"Xiaomi Redmi Note 8 Pro",
"OPPO A15",
"OPPO Reno4",
"OPPO A3s",
"OPPO A53",
"OPPO A37",
"OPPO A33",
"OPPO A93",
"OPPO A92s",
"Vivo Y12",
"Vivo Y21",
"Vivo Y30i",
"Vivo Y20s",
"Vivo Y91",
"Vivo Y1s");
$random_depice=$depicemodel[mt_rand(0,sizeof($depicemodel)-1)];
$sg1 = acaka(2);
$sg2 = acaka(2);
$sg3 = acaka(2);
$sg4 = acaka(2);
$sg5 = acaka(2);
$sg6 = acaka(2);
$sg7 = acaka(2);
$sg8 = acaka(2);
$sg9 = acaka(2);
$sg10 = acaka(2);
$sg11 = acaka(2);
$sg12 = acaka(2);
$sg13 = acaka(2);
$sg14 = acaka(2);
$sg15 = acaka(2);
$sg16 = acaka(2);
$sg17 = acaka(2);
$sg18 = acaka(2);
$sg19 = acaka(2);
$sg20 = acaka(2);
$signatures = $sg1.":".$sg2.":".$sg3.":".$sg4.":".$sg5.":".$sg6.":".$sg7.":".$sg8.":".$sg9.":".$sg10.":".$sg11.":".$sg12.":".$sg13.":".$sg14.":".$sg15.":".$sg16.":".$sg17.":".$sg18.":".$sg19.":".$sg20;
$trxids = acak(10);
$kode1 = urut(8);
$kode2 = urut(4);
$kode3 = urut(4);
$kode4 = urut(4);
$kode5 = urut(12);
$bound = $kode1."-".$kode2."-".$kode3."-".$kode4."-".$kode5;
$header[] = "Host: api.alfagift.id";
$header[] = "accept: application/json";
$header[] = "accept-language: id";
$header[] = "trxid: ".$trxids;
$header[] = "versionname: 4.0.21";
$header[] = "versionnumber: 405100";
$header[] = "devicetype: Android";
$header[] = "devicemodel: ".$random_depice;
$header[] = "packagename: com.alfamart.alfagift";
$header[] = "signature: ".$signatures;
$header[] = "latitude: ".$random_latitude;
$header[] = "longitude: ".$random_longatitude;
$header[] = "deviceid: ".$bound;
$header[] = "token:";
$header[] = "id:";
$header[] = "content-type: application/json; charset=UTF-8";
$header[] = "user-agent: okhttp/3.14.4";

echo "==========================\n";
echo "Tools = AUTO REG ALFAGIFT\n";
echo "Tools by ＪＯＳＳＫＩ\n";
echo "==========================\n";
$url = "https://api.alfagift.id/v1/otp/request";
$noim = readline("Masukkan No Hp => ");
$data = '{"action":"REGISTER","mobileNumber":"'.$noim.'","type":0';
$res = curl($url,$header,"post",$data);
echo $res."\n";
$url = "https://api.alfagift.id/v1/otp/verify";
$otpc = readline("Masukkan OTPmu => ");
$data = '{"action":"REGISTER","mobileNumber":"'.$noim.'","otpCode":"'.$otpc.'","type":0}';
$res = curl($url,$header,"post",$data);
echo $res."\n";
$url = "https://mapi.alfagift.id/v1/account/member/create";
$data = '{"address":"","birthDate":"1991-10-11","debug":false,"deviceId":"%rnd1-7977-40cd-aa41-%rnd2%rnd3","email":"%maiden_name%rnd4@gmail.com","firstName":"","fullName":"%maiden_name","gender":"F","lastName":"","latitude":0,"longitude":0,"maritalStatus":"M","password":"Adeksayang1","phone":"%nomor","postCode":"","registerPonta":true,"token":"%token"}';







?>