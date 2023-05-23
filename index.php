<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

function create_guid() {
	$microTime = microtime();
	list($a_dec, $a_sec) = explode(" ", $microTime);

	$dec_hex = dechex($a_dec * 1000000);
	$sec_hex = dechex($a_sec);

	ensure_length($dec_hex, 5);
	ensure_length($sec_hex, 6);

	$guid = "";
	$guid .= $dec_hex;
	$guid .= create_guid_section(3);
	$guid .= '-';
	$guid .= create_guid_section(4);
	$guid .= '-';
	$guid .= create_guid_section(4);
	$guid .= '-';
	$guid .= create_guid_section(4);
	$guid .= '-';
	$guid .= $sec_hex;
	$guid .= create_guid_section(6);

	return $guid;
}

function create_guid_section($characters) {
	$return = "";
	for ($i = 0; $i < $characters; $i++) {
		$return .= dechex(mt_rand(0, 15));
	}
	return $return;
}

function ensure_length(&$string, $length) {
	$strlen = strlen($string);
	if ($strlen < $length) {
		$string = str_pad($string, $length, "0");
	}
	else if ($strlen > $length) {
		$string = substr($string, 0, $length);
	}
}

$user = 'user';
$pass = '1234';
//Подключался на рабочем компьютере, параметры подключения нужно править на свои
$dbh = new PDO('mysql:host=172.16.0.8;dbname=crm', $user, $pass);

if( isset($_GET) && !empty($_GET)){
    $guid = substr($_SERVER["REQUEST_URI"], 1);
    $sql_req = "SELECT name FROM `Impasse` WHERE id = '".$guid."'";
    $sql_res = $dbh->query($sql_req)->fetchAll(PDO::FETCH_NAMED)[0]['name'];
    if(!empty($sql_res)) {
        header("Location: $sql_res");
    } else {
        echo "Wrong link";
    }
    
    exit;
}

if( isset($_POST['url'])){
    $url = $_POST['url'];
    $guid = create_guid();
    $sth = "INSERT INTO `Impasse`(`id`, `name`) VALUES ('".$guid."', '".$url."')";
    $sth_res = $dbh->query($sth)->fetchAll(PDO::FETCH_NAMED);
    echo $guid;
    exit;
}

?>

<html lang='en'>
<head>
  <title>Linkerd</title>
  <meta charset='utf-8'>
  <meta name='viewport' content='width=device-width, initial-scale=1'>
  <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
  <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js'></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script>
    function shorten(){
         var url = $('#url').val();
         $.ajax({
            type: 'post',
            data: {url: url},
            success: function(guid){
               $('#shorten').html('Your link: <a href="' + url + '" target=_blank>https://crm5.siz37.ru/Linkerd.php?' + guid + '</a>');
            }
         });
    };
   </script>
 
</head>
<body>
    <div class='container mt-3'>
        <h1>Linkerd</h1>
        <form>
            <div class='mb-3 mt-3'>
                <input id='url' type='url' class='form-control' id='link' size='20' placeholder='Your link'>
            </div>
        </form> 
        <input type="button" class='btn btn-primary' onclick='shorten()' value="Shorten!">
    </div>
    <div  class='container mt-3' id='shorten'></div>
</body>
</html>
