<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $max_width = 450;
    
    $upload_dir = 'upload_pic';
    if(!is_dir($upload_dir)){
        mkdir($upload_dir, 0777);
    }
    chmod($upload_dir, 0777);
    
    $filename = basename($_FILES['image']['name']);
    $file_ext = strtolower(substr($filename, strrpos($filename, '.') + 1));
    $large_image_location = 'upload_pic/original.' . $file_ext;
    
    $userfile_name = $_FILES['image']['name'];
    $userfile_tmp  = $_FILES['image']['tmp_name'];
    $userfile_size = $_FILES['image']['size'];
    $userfile_type = $_FILES['image']['type'];
    
    move_uploaded_file($userfile_tmp, $large_image_location);
    chmod($large_image_location, 0777);
    
    $width = getWidth($large_image_location);
    $height = getHeight($large_image_location);
    //Scale the image if it is greater than the width set above
    if ($width > $max_width){
        $scale = $max_width/$width;
        $uploaded = resizeImage($large_image_location,$width,$height,$scale);
    }else{
        $scale = 1;
        $uploaded = resizeImage($large_image_location,$width,$height,$scale);
    }
    
    echo "success|" . getWidth('upload_pic/original.png') . "|" . getHeight('upload_pic/original.png');
}

function resizeImage($image,$width,$height,$scale) {
    $image_data = getimagesize($image);
    $imageType = image_type_to_mime_type($image_data[2]);
    $newImageWidth = ceil($width * $scale);
    $newImageHeight = ceil($height * $scale);
    $newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
    switch($imageType) {
        case "image/gif":
            $source=imagecreatefromgif($image); 
            break;
        case "image/pjpeg":
        case "image/jpeg":
        case "image/jpg":
            $source=imagecreatefromjpeg($image); 
            break;
        case "image/png":
        case "image/x-png":
            $source=imagecreatefrompng($image); 
            break;
    }
    imagecopyresampled($newImage,$source,0,0,0,0,$newImageWidth,$newImageHeight,$width,$height);
    
    imagepng($newImage,'upload_pic/original.png');
}


function getHeight($image) {
    $size = getimagesize($image);
    $height = $size[1];
    return $height;
}

function getWidth($image) {
    $size = getimagesize($image);
    $width = $size[0];
    return $width;
}