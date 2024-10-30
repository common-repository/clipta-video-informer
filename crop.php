<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $targ_w = 176;
    $targ_h = 116;
    
    $src = 'upload_pic/original.png';
    $img_r = imagecreatefrompng($src);
    $dst_r = ImageCreateTrueColor( $targ_w, $targ_h );
    
    imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
        $targ_w,$targ_h,$_POST['w'],$_POST['h']);
    
    imagepng($dst_r, 'upload_pic/thumbnail.png');
}