<?php
header( 'Content-Type:application/json' );

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'http://www.kmle.co.kr/livesearch.php?mobile=1&firefox=true&callback=?&q='.$_GET['q']);
curl_setopt($ch, CURLOPT_SSLVERSION,1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$data = curl_exec($ch);

curl_close($ch);

echo $data;