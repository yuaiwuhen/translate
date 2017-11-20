<?php
/**
 * Created by PhpStorm.
 * User: zk
 * Date: 2017/11/20
 * Time: 10:50
 */
// Include Composer autoloader if not already done.
include 'vendor/autoload.php';

// Parse pdf file and build necessary objects.
$parser = new \Smalot\PdfParser\Parser();
$uploaddir = './pdf/';
$uploadfile = $uploaddir . basename(time() . '_' . $_FILES['pdf']['name']);
//$uploadfile = './pdf/1511146908_Complement in cancer  Review-- 2014.pdf';
echo '<pre>';
if (move_uploaded_file($_FILES['pdf']['tmp_name'], $uploadfile)) {
    echo "File is valid, and was successfully uploaded.\n";
} else {
    echo "Possible file upload attack!\n";
}
$pdf    = $parser->parseFile($uploadfile);

$text = $pdf->getText();
$total = strlen($text);
$text_arr = [];
if($total > 3000){
    $len = 0;
    while(true){
        $temp = substr($text,$len,3000);
        $pos = strrpos($temp, '.');
        $temp = substr($temp,0, $pos);
        $text_arr[] = $temp;
        $len += $pos;
        if(strlen(substr($text, $len))<=3000){
            break;
        }
    }
}
$last = '';
if($text_arr){
    foreach ($text_arr as $item) {
        $res_text = query($item);
        $last .= $res_text;
    }
}else{
    $res_text = query($item);
    $last .= $res_text;
}

$myfile = fopen($uploadfile.'.text', "w") or die("Unable to open file!");
fwrite($myfile, $last);
fclose($myfile);
echo "<a href='" . $uploadfile . ".text' download='file'>点我下载(请用word打开,切勿使用记事本)</a>";die;


function query($text){
    $key = '7213821ed7445a5b';
    $salt = rand(1,10);
    $q = $text;
    $q = substr($q,0,2000);
    $seret = 'ViQdS9KmYHrA1SLsaxxIhx3QQjx29kHV';
    $sign = strtoupper(md5($key.$q.$salt.$seret));
    $q = urlencode($q);
    $url = "http://openapi.youdao.com/api?q=$q&from=EN&to=zh_CHS&appKey=$key&salt=$salt&sign=";
    $url = $url . $sign;
    $res = file_get_contents($url);
    $res = json_decode($res, true);
    echo $res['translation'][0];
    return $res['translation'][0];
}
