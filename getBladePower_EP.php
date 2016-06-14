<?php

require 'vendor/autoload.php';
use GuzzleHttp\Client;

$cookie = loginUCS();

$blade = null;
$flagmap = null;

for($i=0; $i<8; $i++)
{
  $blade[$i] = getBladeInfo($cookie,"sys/chassis-1/blade-".($i+1));
}

for($i=0; $i<8; $i++)
{
  echo("## $i  ##".PHP_EOL);
  echo($blade[$i]["dn"].PHP_EOL);
  echo($blade[$i]["name"].PHP_EOL);
  echo($blade[$i]["operPower"].PHP_EOL);
  if($blade[$i]["dn"] != null)
  {
    if($blade[$i]["operPower"]=="on")$flagmap[$i]=11;
    else $flagmap[$i]=10;
  }else{
    $flagmap[$i]=0;
  } 
}

print_r($flagmap);

createImage($flagmap,"EP_UCS_Status.jpg");

function loginUCS()
{
  $body = '<aaaLogin inName="admin" inPassword="admin" />';
  $res = getXML($body);
  $cookie = $res["@attributes"]["outCookie"];
  return($cookie);  
}

function getBladeInfo($cookie,$dn)
{
  $contents = '<configResolveDn cookie="'.$cookie.'" dn="'.$dn.'" inHierarchical="false"></configResolveDn>';
  $res = getXML($contents);
  $bladeinfo = get_object_vars($res['outConfig']);
  if($bladeinfo["computeBlade"] != null)
  {
    $bladeinfo = get_object_vars($bladeinfo["computeBlade"]);
    $bladeinfo = $bladeinfo["@attributes"];
  }else{
    $bladeinfo = null;
  }
  return($bladeinfo);
}

function getXML($contents)
{
  $client = new Client();
  $headers = ['User-Agent' => 'lwp-request/2.06', 'Content-Type' => 'application/x-www-form-urlencoded'];
  $response = $client->request('POST', 'http://10.44.142.219/nuova', [ 'body' => $contents ]);

  $body = $response->getBody();
  $remainingBytes = $body->getContents();
  $xml = simplexml_load_string($remainingBytes);
  $data = get_object_vars($xml);
  return($data);
}

function createImage($flagmap,$filename)
{
  $image_path = "/var/www/html/mgmtucs/img"; 
  $base_image = imagecreatefromjpeg($image_path.'/chassi.jpg');
  $blade_image = imagecreatefromjpeg($image_path.'/blade.jpg');

  $power_on = imagecolorallocate($base_image, 0, 255, 0);
  $power_off = imagecolorallocate($base_image, 255, 0, 0);
  $text_color = imagecolorallocate($base_image, 255, 255, 255);

  $fontpath = "/var/www/html/mgmtucs/font/ipaexg.ttf";

  date_default_timezone_set('Asia/Tokyo');
  $date = date("Y/m/d H:i:s");
  ImageTTFText($base_image, 16, 0, 90, 210, $text_color,$fontpath, $date);

  $map[0] = ["x" => "0", "y" => "0"];
  $map[1] = ["x" => "170", "y" => "0"];
  $map[2] = ["x" => "0", "y" => "45"];
  $map[3] = ["x" => "170", "y" => "45"];
  $map[4] = ["x" => "0", "y" => "90"];
  $map[5] = ["x" => "170", "y" => "90"];
  $map[6] = ["x" => "0", "y" => "135"];
  $map[7] = ["x" => "170", "y" => "135"];

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
  
  imagejpeg( $base_image, $image_path.'/'.$filename);

  //最後にメモリを開放する
  imagedestroy($base_image);
}

?>
