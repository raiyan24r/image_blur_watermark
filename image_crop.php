<?php



$sentImage = 'http://localhost/image_crop/ss1.jpg';
$croppedimg = cropImage($sentImage);

$im = imagecreatefrompng("http://localhost/image_crop/" . $croppedimg);

$stamp = resizeLogo('http://localhost/image_crop/logo.jpg', imagesx($im));

$marge_right = 10;
$marge_bottom = 10;
$sx = imagesx($stamp);
$sy = imagesy($stamp);


imagecopymerge($im, $stamp, imagesx($im) * .1, imagesy($im) / 2, 0, 0, imagesx($stamp), imagesy($stamp), 30);

imagepng($im, 'photo_stamp.png');


?>

<img src="<?= 'photo_stamp.png'; ?>" />


<?php






function resizeLogo($image, $ss_width)
{


  $imageSize = getImageSize($image);
  $imageWidth = $imageSize[0];
  $imageHeight = $imageSize[1];

  $DESIRED_WIDTH = $ss_width * 0.7;
  $proportionalHeight = round(($DESIRED_WIDTH * $imageHeight) / $imageWidth);

  $originalImage = imagecreatefromjpeg($image);

  $resizedImage = imageCreateTrueColor($DESIRED_WIDTH, $proportionalHeight);

  imageCopyResampled($resizedImage, $originalImage, 0, 0, 0, 0, $DESIRED_WIDTH + 1, $proportionalHeight + 1, $imageWidth, $imageHeight);
  imageJPEG($resizedImage, "save.jpg");


  return $resizedImage;
  // imageDestroy($originalImage);
  // imageDestroy($resizedImage);
}




function cropImage($sentImage)
{



  $finalImage = imagecreatefromjpeg($sentImage);

  $im2 = imagecrop($finalImage, ['x' => 0, 'y' => imagesy($finalImage) * 0.1, 'width' => imagesx($finalImage), 'height' => imagesy($finalImage) * 0.9]);


  /*
  if (imagesy($finalImage) > 1.5 * imagesx($finalImage)) {
    $im2 = imagecrop($finalImage, ['x' => 0, 'y' => imagesy($finalImage) * 0.1, 'width' => imagesx($finalImage), 'height' => imagesy($finalImage) * 0.9]);
  } 

*/


  //$img_name = 'converted' . rand(10, 100) . '.png';
  $img_name = "ss_cropped.png";
  imagepng(
    $im2,
    $img_name
  );



  return  $img_name;
}
