<?php


$screenshot = 'http://localhost/image_crop/ss1.jpg';
$logo = 'http://localhost/image_crop/logo.jpg';


$im = blurLogoImage($screenshot, $logo);



header('Content-Type: image/jpg');
imagejpeg($im);



function blurLogoImage($screenshot, $logo)
{



    $sentImage = $screenshot;
    $blurredimg = blurImage($sentImage);

    $im = $blurredimg;

    $stamp = resizeLogo($logo, imagesx($im));
    imagecopymerge($im, $stamp, imagesx($im) * .1, imagesy($im) * .4, 0, 0, imagesx($stamp), imagesy($stamp), 30);
    $img_name = 'testimonial' . rand(10, 100) . '.png';
    imagepng($im, $img_name);

    return $im;
}


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
    //imageJPEG($resizedImage, "save.jpg");


    return $resizedImage;

    // imageDestroy($originalImage);
    // imageDestroy($resizedImage);
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
        // echo "122221";
        imagecopymerge($finalImage, $img2, 0, 0, 0, 0, imagesx($finalImage), imagesy($finalImage) * 0.08, 100);
    }

    // $im2 = imagecrop($finalImage, ['x' => 0, 'y' => imagesy($finalImage) * 0.1, 'width' => imagesx($finalImage), 'height' => imagesy($finalImage) * 0.9]);
    // $img_name = "ss_cropped.png";
    // imagepng(
    //     $finalImage,
    //     $img_name
    // );



    return  $finalImage;
}
