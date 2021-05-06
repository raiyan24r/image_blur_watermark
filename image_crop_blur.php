<?php


$screenshot = 'http://localhost/image_crop/ss3.jpg'; // screenshot directory
$logo = 'http://localhost/image_crop/sta.png'; // logo/stamp directory


$im = blurAndPlaceLogo($screenshot, $logo); //call function blurAndPlaceLogo with parameters screenshot and logo



header('Content-Type: image/png');
imagepng($im);



function blurAndPlaceLogo($screenshot, $logo)
{



    $sentImage = $screenshot;
    $blurredimg = blurImage($sentImage);

    $im = $blurredimg;

    $stamp = resizeLogo($logo, imagesx($im));



    $im = imagecopymerge_alpha($im, $stamp, imagesx($im) * .1, imagesy($im) * .4, 0, 0, imagesx($stamp), imagesy($stamp), 30);


     $img_name = 'testimonial' . rand(10, 100) . '.png';
   // $img_name = 'output.png';
    imagepng($im, $img_name);

    return $im;
}


function resizeLogo($image, $ss_width)
{


    $imageSize = getImageSize($image);
    $imageWidth = $imageSize[0];
    $imageHeight = $imageSize[1];

    $DESIRED_WIDTH = $ss_width * 0.8;
    $proportionalHeight = round(($DESIRED_WIDTH * $imageHeight) / $imageWidth);

    $originalImage = imagecreatefrompng($image);
    // $originalImage = imagecreatefrompng($image);

    $resizedImage = imageCreateTrueColor($DESIRED_WIDTH, $proportionalHeight);
    // imagealphablending($originalImage, false);
    // imagesavealpha($originalImage, true);
    imagealphablending($resizedImage, false);
    imagesavealpha($resizedImage, true);
    imageCopyResampled($resizedImage, $originalImage, 0, 0, 0, 0, $DESIRED_WIDTH + 1, $proportionalHeight + 1, $imageWidth, $imageHeight);



    return $resizedImage;
}


function blurImage($sentImage)
{



    $finalImage = imagecreatefromjpeg($sentImage);

    $img2 = imagecreatetruecolor(imagesx($finalImage), imagesy($finalImage) * 0.1); // create img2 for selection


    if (imagesy($finalImage) >= 2 * imagesx($finalImage)) {
        imagecopy($img2, $finalImage, 0, 0, 0, 0, imagesx($finalImage), imagesy($finalImage) * 0.1);
        //echo "11";
    } else {
        // echo "122221";
        imagecopy($img2, $finalImage, 0, 0, 0, 0, imagesx($finalImage), imagesy($finalImage) * 0.8);
    }




    for ($x = 1; $x <= 70; $x++) {
        imagefilter($img2, IMG_FILTER_GAUSSIAN_BLUR);
    }


    if (imagesy($finalImage) >= 2 * imagesx($finalImage)) {
        imagecopymerge($finalImage, $img2, 0, 0, 0, 0, imagesx($finalImage), imagesy($finalImage) * 0.1, 100);
    } else {
        
        imagecopymerge($finalImage, $img2, 0, 0, 0, 0, imagesx($finalImage), imagesy($finalImage) * 0.08, 100);
    }



    return  $finalImage;
}

function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct)
{
    // creating a cut resource
    $cut = imagecreatetruecolor($src_w, $src_h);

    // copying relevant section from background to the cut resource
    imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);

    // copying relevant section from watermark to the cut resource
    imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);

    // insert cut resource to destination image
    imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct);

    return $dst_im;
}
