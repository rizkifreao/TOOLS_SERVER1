<?php
//echo "Cookie SPC_EC = ";
//$spcec = trim(fgets(STDIN));
echo "cropid = ";
$cropid = trim(fgets(STDIN));
echo "resourceid = ";
$resourceid = trim(fgets(STDIN));

$headers = array();
$headers[] = 'content-type: application/json;charset=UTF-8';
$headers[] = 'cookie: SPC_EC=85Hm+GOhZdi5MsMU4zsTGMfIhtNEFKvUlR4zV+PuMiAUdL/uSQJywx/EUo5Iu0IfHxQwsaazREOPwArW1NW+ItCsLprBnrKV8ajb2sc7h4kmfKR8R2cffrPTyojrjK8lGo0FanGEH62TbgqjvORfbYo18F2VHHNB3yrl3eo7oc8=';

$ulang = 0;
while ($ulang < 400){
$request = '{"cropId":'.$cropid.',"resourceId":'.$resourceid.'}';
$body = curl('https://games.shopee.co.id/farm/api/orchard/crop/water', $request, $headers);
//var_dump($body);
$air = json_decode($body[0]);
$status = $air->msg;
echo "".$status."\n";
for ($i= 120; $i>=0; $i--) {
        echo "\r";
        echo "Siram Berikutnya $i ";
        sleep(1);
    }
$ulang++;
}



function curl($url, $fields = null, $headers = null) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  if ($fields !== null) {
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
  }
  if ($headers !== null) {
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  }
  $result = curl_exec($ch);
  $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  return array($result, $httpcode);
  }