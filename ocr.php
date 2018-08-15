<?php

error_reporting(0);
date_default_timezone_set("Asia/Jakarta");

if(!$_POST['checkbox'] && $_POST['kirim'] == 1){
  echo "<script type=\"text/javascript\">alert(\"MAKE SURE YOU'VE CLICK THE CHECKBOX\");</script>";
  echo '<script>setTimeout(function(){ window.location.href = "javascript:history.go(-1)"; }, 1);</script>';
}elseif($_POST['kirim'] == 1){
  function curl($url, $strPost=0)
  {
  	global $config;
  	$ch = curl_init();
  	curl_setopt($ch, CURLOPT_URL, $url);
  	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  	curl_setopt($ch, CURLOPT_HEADER, false);

    $headers = array();
    $headers[] = "Origin: https://ocr.space";
    $headers[] = "Accept-Encoding: gzip, deflate, br";
    $headers[] = "Accept-Language: en-US,en;q=0.9";
    $headers[] = "User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36";
    $headers[] = "Content-Type: application/x-www-form-urlencoded";
    $headers[] = "Accept: application/json, text/javascript, */*; q=0.01";
    $headers[] = "Referer: https://ocr.space/";
    $headers[] = "Authority: api.ocr.space";
    $headers[] = "Apikey: webocr5";
  	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  	if(isset($strPost))
  	{
  	     curl_setopt($ch, CURLOPT_POST, 1);
  	     curl_setopt($ch, CURLOPT_POSTFIELDS, $strPost);
  	}
  	$a = curl_exec($ch);
  	curl_close($ch);

  	return $a;
  }
  function getStr($string, $start, $end){
      $string = ' ' . $string;
      $ini = strpos($string, $start);
      if ($ini == 0) return '';
      $ini += strlen($start);
      $len = strpos($string, $end, $ini) - $ini;
      return substr($string, $ini, $len);
  }
  $addPost = http_build_query(array("url" => $_POST['url'], "language" => "eng", "isOverlayRequired" => "true", "FileType" => ".Auto", "IsCreateSearchablePDF" => "false", "isSearchablePdfHideTextLayer" => "true", "scale" => "true", "detectOrientation" => "true"));
  $addUrl = curl("https://api.ocr.space/parse/image", $addPost);
  $page = str_replace(array( '\r', '\n' ),'',$addUrl);
  $hasil = getStr($page,'"ParsedText":"','"');
  if(isset($hasil)){
    $fp = fopen("history-ocr.html", "a");
    fputs($fp,
      'URL Foto: <a href="'.$_POST['url'].'" onclick="window.open(this.href, \'mywin\',\'left=20,top=20,width=500,height=500,toolbar=1,resizable=0\'); return false;" ><font color="blue">'.$_POST['url'].'</font></a><br>Hasil: '.$hasil.'<br>~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~<br>'
    );
    echo '<a href="ocr.php"><font color="blue"><img src="http://www.clker.com/cliparts/j/6/l/f/Z/v/back-button-png-hi.png" width="100" height="45"></font></a><br /><br />';
    echo 'URL Foto: <a href="'.$_POST['url'].'" onclick="window.open(this.href, \'mywin\',\'left=20,top=20,width=500,height=500,toolbar=1,resizable=0\'); return false;" ><font color="blue">'.$_POST['url'].'</font></a><br/><br/>';
    echo 'Hasil:<br /><textarea name="hasil" rows="8" cols="80">'.$hasil.'</textarea><br /><br />';
    echo 'Tampilan Gambar:<br /><img src="'.$_POST['url'].'">';
  }elseif(!isset($hasil)){
    echo '<img src="http://approotz.com/wp-content/themes/approot/images/preloader.gif" height="200"><br>';
    echo '<meta http-equiv="refresh" content="2">';
  }elseif(empty($page) && ($page == null)) {
    echo '<script type="text/javascript">alert("WAIT! CHANGE COOKIE");</script>';
    echo '<img src="http://approotz.com/wp-content/themes/approot/images/preloader.gif" height="200"><br>';
    echo '<meta http-equiv="refresh" content="5">';
  }else{
    echo 'lapor admin';
  }
}else{
  // NEXT WITH CONFIRM
	echo
	'
	<form action="ocr.php" method="post">
    <input type="hidden" name="kirim" value="1"/>
		URL Foto:<br /><textarea name="url" rows="2" cols="50"></textarea><br>
    Harap centang ceklis disamping <input type="checkbox" name="checkbox" value="1">
		<input type="submit" value="Submit">
	</form>

  <p>
    <i>Mohon dimaklumi jika teks dari gambar tidak sesuai, karena kita semua manusia tak pernah luput dari kesalahan</i><br />
    <b>USAHAKAN TEKS DARI FOTO/GAMBAR HARUS JELAS DAN RAPIH</b><br />
    to form an API it would be nice to get permission first, I would love to hear that<br />
  	Click on the submit button, and the input will be sent to a page on the server called "/ocr.php".
  </p>';
}
