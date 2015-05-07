<?php
//*******************************************************
function startsWith($haystack, $needle) {
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
}
//*******************************************************
//*
$ftp = array(
	1 => array("server" => "xx.xxx.xx.xxx", "user" => "xxxxx", "pwd" => "xxxx", "path" => "xxxxx"),
	2 => array("server" => "", "user" => "", "pwd" => "", "path" => ""));
//*
$ftpmax = 1; // mettre le nombre total de serveurs ftp du tableau $ftp
$numftp = 1;
$prefix = "";
$numimg = 1;
$histoimg= array();
if (isset($_GET['numftp'])) {
	if ($_GET['numftp'] <= $ftpmax && $_GET['numftp'] > 0) {
		$numftp = $_GET['numftp']; 
	}
}
if (isset($_GET['prefix'])) {
	$prefix = $_GET['prefix']; 
}
if (isset($_GET['numimg'])) {
	$numimg = $_GET['numimg']; 
}
$conn_id = ftp_connect($ftp[$numftp]["server"]);
ftp_pasv($conn_id, true);
$login_result = ftp_login($conn_id, $ftp[$numftp]["user"], $ftp[$numftp]["pwd"]);
if ((!$conn_id) || (!$login_result)) {
	echo "<p> Connexion au serveur ".$ftp[$numftp]["server"]." pour l'utilisateur ".$ftp[$numftp]["user"]." KO !";
	exit;
}
if ($ftp[$numftp]["path"] != "") {
	ftp_chdir($conn_id, $ftp[$numftp]["path"]);
}
$listfile = ftp_nlist($conn_id, ".");
// tri les noms de fichier par ordre décroissant (implique qu'il y ait un timestamp dans le nom pour restituer la dernière connue)
arsort($listfile, SORT_NATURAL | SORT_FLAG_CASE);
// choisi la dernière capture avec le préfixe correspondant dans le nom
$idxhisto = 0;
foreach ($listfile as $key => $val) {
    if ($prefix != "") {
		if (startsWith($val, $prefix) == true) {
			$idxhisto++;
			$histoimg[$idxhisto] = $key;
			if ($idxhisto == $numimg) {
				break;
			}
		}
    } else {
    	$idxhisto++;
		$histoimg[$idxhisto] = $key;
		if ($idxhisto == $numimg) {
			break;
		}
    }
}
if ($idxhisto < $numimg) {
	$numimg = $idxhisto;
}
$dest = imagecreatetruecolor(640, 480);
imagecolorallocate($dest, 255, 255, 255);
if ($numimg > 1 AND $numimg <= 4) {
	$sizesrcx = 318;
	$sizesrcy = 238;
	
} else {
	$sizesrcx = 640;
	$sizesrcy = 480;
	$numimg = 1;
}
$destquad = imagecreatetruecolor($sizesrcx, $sizesrcy);
imagecolorallocate($destquad, 255, 255, 255);
for ($i = 1; $i <= $numimg; $i++) {
	$namefile = $listfile[$histoimg[$i]];
	ob_start();
	$size = ftp_size($conn_id, $namefile);
	$result = ftp_get($conn_id, "php://output", $namefile, FTP_BINARY);
	$data = ob_get_contents();
	ob_end_clean();
	$src = imagecreatefromstring($data);
	imagecopyresampled($destquad, $src, 0, 0, 0, 0, $sizesrcx, $sizesrcy, 640, 480);
	if ($numimg == 1 OR $i == 1) {
		imagecopy ($dest , $destquad , 1 , 1 , 0 , 0 , $sizesrcx , $sizesrcy);
	} else if ($i == 2) {
		imagecopy ($dest , $destquad , 321 , 1 , 0 , 0 , $sizesrcx , $sizesrcy);
	} else if ($i == 3) {
		imagecopy ($dest , $destquad , 1 , 241 , 0 , 0 , $sizesrcx , $sizesrcy);
	} else if ($i == 4) {
		imagecopy ($dest , $destquad , 321 , 241 , 0 , 0 , $sizesrcx , $sizesrcy);
	}
}
ftp_close($conn_id);
header("Content-Type: image/jpeg");
imagejpeg($dest, NULL, 100);
exit;
?>