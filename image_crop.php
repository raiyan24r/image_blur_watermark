<?php



$sentImage = 'http://localhost/image_crop/ss1.jpg';
$croppedimg = cropImage($sentImage);


?>

<img src="<?= $croppedimg; ?>" />


<?php
function cropImage($sentImage)
{



  $finalImage = imagecreatefromjpeg($sentImage);

  $im2 = imagecrop($finalImage, ['x' => 0, 'y' => imagesy($finalImage) * 0.1, 'width' => imagesx($finalImage), 'height' => imagesy($finalImage) * 0.9]);


  /*
  if (imagesy($finalImage) > 1.5 * imagesx($finalImage)) {
    $im2 = imagecrop($finalImage, ['x' => 0, 'y' => imagesy($finalImage) * 0.1, 'width' => imagesx($finalImage), 'height' => imagesy($finalImage) * 0.9]);
  } 

*/


  $img_name = 'converted' . rand(10, 100) . '.png';
  imagepng(
    $im2,
    $img_name
  );



  return  $img_name;
}
