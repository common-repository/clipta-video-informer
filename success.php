<?php
if (file_exists('tmp/info.dat')){
    unlink('tmp/info.dat');
}

foreach(glob('upload_pic/*') as $path){
    unlink($path);
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">  
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/> 
	<link rel="stylesheet" href="css/style.css" />     
</head> 
<body>
<p><strong>News added successfully!</strong></p>
<p>To modify or remove news items go to your <a href="http://info.clipta.com/partners_my_news_manage">Informer Partner Account</a></p>
</body> 
</html>