<?php
session_start();

$random_str = substr(md5(rand()), 0, 6);
$_SESSION['captcha'] = $random_str;

$image = imagecreatetruecolor(200, 50);
$background_color = imagecolorallocate($image, 255, 255, 255);
imagefilledrectangle($image, 0, 0, 200, 50, $background_color);

for ($i = 0; $i < 200; $i++) {
    $noise_color = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
    imagesetpixel($image, rand(0, 200), rand(0, 50), $noise_color);
}

for ($i = 0; $i < 5; $i++) {
    $line_color = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
    imageline($image, rand(0, 200), rand(0, 50), rand(0, 200), rand(0, 50), $line_color);
}

$text_color = imagecolorallocate($image, rand(0, 150), rand(0, 150), rand(0, 150));
imagettftext($image, 20, 0, 30, 35, $text_color, './lib/Roboto-Black.ttf', $random_str);

header('Content-type: image/png');
imagepng($image);
imagedestroy($image);
?>
