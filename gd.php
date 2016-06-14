<?php

$base_image = imagecreatefromjpeg('img/chassi.jpg');
$blade_image = imagecreatefromjpeg('img/blade.jpg');

$power_on = imagecolorallocate($base_image, 0, 255, 0);
$power_off = imagecolorallocate($base_image, 255, 0, 0);
$text_color = imagecolorallocate($base_image, 255, 255, 255);

$fontpath = "/var/www/html/mgmtucs/font/ipaexg.ttf";

date_default_timezone_set('Asia/Tokyo');
$date = date("Y/m/d H:i:s"); 
ImageTTFText($base_image, 16, 0, 90, 210, $text_color,$fontpath, $date);

$map[0] = ["x" => "0", "y" => "0"];
$map[2] = ["x" => "0", "y" => "45"];
$map[4] = ["x" => "0", "y" => "90"];
$map[6] = ["x" => "0", "y" => "135"];
$map[1] = ["x" => "170", "y" => "0"];
$map[3] = ["x" => "170", "y" => "45"];
$map[5] = ["x" => "170", "y" => "90"];
$map[7] = ["x" => "170", "y" => "135"];

$flagmap[0] = 10;
$flagmap[1] = 11;
$flagmap[2] = 0;
$flagmap[3] = 0;
$flagmap[4] = 10;
$flagmap[5] = 11;
$flagmap[6] = 0;
$flagmap[7] = 0;

$position_x = 25;
$position_y = 3;

for($idx=0; $idx<8; $idx++)
{
  $pos_x = $position_x+$map[$idx]["x"];
  $pos_y = $position_y+$map[$idx]["y"];
  if($flagmap[$idx]==10)
  {
    imagecopy($base_image, $blade_image, $pos_x, $pos_y, 0, 0, 172, 44);
    imagefilledarc( $base_image, $pos_x+90, $pos_y+20, 30, 30, 0, 360, $power_off, IMG_ARC_PIE );
  }else if($flagmap[$idx]==11){
    imagecopy($base_image, $blade_image, $pos_x, $pos_y, 0, 0, 172, 44);
    imagefilledarc( $base_image, $pos_x+90, $pos_y+20, 30, 30, 0, 360, $power_on, IMG_ARC_PIE );
  }else if($flagmap[$idx]==0){
  }
}
imagejpeg( $base_image, 'img/status.jpg');

//最後にメモリを開放する
imagedestroy($base_image);


?>
