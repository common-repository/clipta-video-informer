<?php

//http://info.clipta.com/wordpress_add_news
$errorString = '';
$generic_error_message = 'News cannot be added this time, please try again later';

if (isset($_GET['errors']) && $_GET['errors'] != '') {
    $data = file_get_contents('tmp/info.dat');
    $dataArray = unserialize($data);
    
    $errors = explode('|', $_GET['errors']);
    
    $errorString = '<ul class="error">';
    foreach($errors as $error){
        switch ($error) {
            case 'database':
            case 'news_url3':
            case 'news_url4':
            case 'news_image5':
            case 'news_image8':
            case 'news_image10':
            case 'news_image11':
            case 'news_image12':
            case 'news_image13':
            case 'news_image14':
                $errorString .= '<li>' . $generic_error_message . '</li>';
                break;
            case 'login':
                $errorString .= '<li>Please check your login and password for info.clipta.com in your website configuration and try to publish again. </li>';
                break;
            case 'news_title':
                $errorString .= '<li>Title is not valid</li>';
                break;
            case 'news_description':
                $errorString .= '<li>Description is not valid</li>';
                break;
            case 'news_url1':
                $errorString .= '<li>News URL is not valid</li>';
                break;
            case 'news_url2':
                $errorString .= '<li>News URL is valid but cannot be validated</li>';
                break;
            case 'news_category':
                $errorString .= '<li>Please choose category</li>';
                break;
            case 'news_has_video':
                $errorString .= '<li>Please confirm that news has video</li>';
                break;
            case 'news_image1':
            case 'news_image2':
            case 'news_image3':
            case 'news_image4':
            case 'news_image6':
            case 'news_image7':
            case 'news_image9':
                $errorString .= '<li>Please upload another image</li>';
                break;
            default:
                break;
        }
    }
    $errorString .= '</ul>';
    
} else {
    $dataArray = array(
        'partner_login'     => urldecode( isset($_GET['l']) && $_GET['l'] != '' ? $_GET['l'] : ''),
        'partner_password'  => urldecode( isset($_GET['p']) && $_GET['p'] != '' ? $_GET['p'] : ''),
        'news_title'        => urldecode( isset($_GET['t']) && $_GET['t'] != '' ? $_GET['t'] : ''),
        'news_description'  => urldecode( isset($_GET['c']) && $_GET['c'] != '' ? $_GET['c'] : ''),
        'news_category'     => '',
        'news_url'          => urldecode( isset($_GET['u']) && $_GET['u'] != '' ? $_GET['u'] : ''),
        'news_image'        => 'images/blank.png',
        'news_has_video'    => (int)$_GET['v'],
    );
}

$site_url = urldecode( isset($_GET['w']) && $_GET['w'] != '' ? $_GET['w'] : 'http://' . $_SERVER['HTTP_HOST']);

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">  
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    <link rel="stylesheet" href="css/style.css" type="text/css" media="all" /> 
    <link rel="stylesheet" href="css/jquery.Jcrop.css" type="text/css" media="all" /> 
    <link rel="stylesheet" href="css/thickbox.css" type="text/css" media="screen" />
    
    
    <script type="text/javascript" src="js/jquery-1.3.1.min.js"></script>
    <script type="text/javascript" src="js/jquery.validate.min.js"></script>
    <script type="text/javascript" src="js/jquery.Jcrop.min.js"></script>
    <script type="text/javascript" src="js/ajaxupload.3.4.js"></script>
    <script type="text/javascript" src="js/thickbox-compressed.js"></script>
    <script type="text/javascript" src="js/jquery.form.js"></script>
    
    <script type="text/javascript">
        /*<![CDATA[*/
        $(document).ready(function() {
            var api;
            
            api = $.Jcrop('#cropbox');
            
            function updateCoords(c){
                $('#x').val(c.x);
                $('#y').val(c.y);
                $('#w').val(c.w);
                $('#h').val(c.h);
            };
            
            
            function checkCoords(){
                if (parseInt($('#w').val())) return true;
                alert('Please select a crop region then press submit.');
                return false;
            };
            
            
            $("#add-news-form").submit(function() {
                $('#add-news-form').ajaxSubmit({url: 'save-info.php'});
                return true; 
            });
            
            
            $("#save_thumbnail").click(function() {
                $('#loader').show();
                $('#progress').html('Please wait, saving thumbnail...');
                $('#uploaded_image').hide();
                $('#crop-form').ajaxSubmit(function() {
                    $('#news_image').attr('val', 'upload_pic/thumbnail.png');
                    $('#thumbnail_image').attr('src', 'upload_pic/thumbnail.png?rnd='+Math.round(Math.random(0)*1000));
                    $('#news_image').attr('value', '<?php echo $site_url; ?>/wp-content/plugins/clipta-video-informer/upload_pic/thumbnail.png?rnd='+Math.round(Math.random(0)*1000));
                    $('#loading-image-menu').show();
                    $('#loader').hide();
                    $('#progress').html('');
                    //api.destroy();
                    $('#cropbox').Jcrop().destroy();
                }); 
            });
            
            
            new Ajax_upload($('#add_picture'), {
                action: 'upload.php',
                name: 'image',
                onSubmit : function(file , ext){
                    if (ext && /^(jpg|png|jpeg|gif)$/.test(ext)){
                        $('#loader').show();
                        $('#progress').html('Please wait, uploading image...');
                    } else {
                        $('#progress').html('<p class="error">Only images allowed! (jpg, jpeg, png, gif)</p><br />');
                        return false;
                    }
                },
                onComplete : function(file, response){
                    response = unescape(response);
                    var response = response.split("|");
                    var responseType = response[0];
                    var resWidth = response[1];
                    var resHeight = response[2];
                    $('#cropbox').attr({ 
                        src: 'upload_pic/original.png?rnd='+Math.round(Math.random(0)*1000),
                        width: resWidth,
                        height: resHeight
                    });
                    $('#loader').hide();
                    $('#progress').html('');
                    $('#uploaded_image').show();
                    
                    api.destroy();
                    api = $.Jcrop('#cropbox',{
                        bgColor:     'black',
                        bgOpacity:   .4,
                        setSelect:   [ 0, 0, 176, 116 ],
                        onSelect:    updateCoords,
                        aspectRatio: 176 / 116
                    });
                }
            });
            
            
            $('#button_from_url').click(function() {
                var u = $('#image-from-url').val();
                $('#loader').show();
                $('#progress').html('Please wait, uploading image...');
                $.ajax({
                    type: 'POST',
                    url: 'upload-from-url.php',
                    data: 'u='+u,
                    cache: false,
                    success: function(response){
                        response = unescape(response);
                        var response = response.split("|");
                        var responseType = response[0];
                        var resWidth = response[1];
                        var resHeight = response[2];
                        if(responseType=="success"){
                            $('#cropbox').attr({ 
                                src: 'upload_pic/original.png?rnd='+Math.round(Math.random(0)*1000),
                                width: resWidth,
                                height: resHeight
                            });
                            $('#loader').hide();
                            $('#progress').html('');
                            $('#uploaded_image').show();
                            api.destroy();
                            api = $.Jcrop('#cropbox',{
                                bgColor:     'black',
                                bgOpacity:   .4,
                                setSelect:   [ 0, 0, 176, 116 ],
                                onSelect:    updateCoords,
                                aspectRatio: 176 / 116
                            });
                        }else{
                            $('#loader').hide();
                            $('#progress').html('<p class="error">Please try another url</p>');
                        }
                    }
                });
            });
            
        });
        /*]]>*/
    </script> 

    <script type="text/javascript">
    $(document).ready(function() {
        $("#add-news-form").validate({
            rules: {
                news_title: {
                    required: true,
                    minlength: 5,
                    maxlength: 100,
                    },
                news_description: {
                    required: true,
                    minlength: 20,
                    maxlength: 300,
                    },
                news_category: "required",
                news_url: {
                    required: true,
                    url: true
                },
                news_image: "required",
                news_has_video: "required"
            },
            messages: {
                news_title: {
                    required: "Please enter a title (5-100 characters)",
                    minlength: "Title is too short (min 5 characters)",
                    maxlength: "Title is too long (max 100 characters)",
                    },
                news_description: {
                    required: "Please enter a short description (20-300 characters)",
                    minlength: "Description is too short (min 20 characters)",
                    maxlength: "Description is too long (max 300 characters)",
                    },
                news_category: "Please select the news category",
                news_url: {
                    required: "Please enter the news URL",
                    url: "Please enter a valid URL"
                },
                news_image: "Please add picture",
                news_has_video: "You need to confirm"
            }
        });
    });
    </script>
    
    <script src="http://www.google.com/jsapi" type="text/javascript"></script>
    <script language="Javascript" type="text/javascript">
    //<![CDATA[
    google.load('search', '1');
    
    function OnLoad() {
        // Create a search control
        var searchControl = new google.search.SearchControl();
        options = new google.search.SearcherOptions();
        options.setExpandMode(google.search.SearchControl.EXPAND_MODE_OPEN);
        searchControl.addSearcher(new google.search.ImageSearch(), options);
        // tell the searcher to draw itself and tell it where to attach
        searchControl.draw(document.getElementById("searchcontrol"));
        // execute an inital search
        searchControl.execute("<?php echo $dataArray['news_title']; ?>");
    }
    google.setOnLoadCallback(OnLoad);
    //]]>
    </script>
</head> 
<body>
 
<form id="add-news-form" action="http://info.clipta.com/wordpress_add_news" method="post"> 

<input type="hidden" name="partner_login" value="<?php echo $dataArray['partner_login']; ?>" />
<input type="hidden" name="partner_password" value="<?php echo $dataArray['partner_password']; ?>" />
<input type="hidden" name="response_to" value="<?php echo $site_url; ?>/wp-content/plugins/clipta-video-informer/success.php" />
<input type="hidden" name="error_response_to" value="<?php echo $site_url; ?>/wp-content/plugins/clipta-video-informer/add-news.php" />
<?php echo $errorString; ?>
<table cellspacing="0" cellpadding="0"> 
    <tr> 
        <th><label for="news_title">Title:</label></th> 
        <td> 
            <input type="text" id="news_title" name="news_title" value="<?php echo $dataArray['news_title']; ?>" style="width:100%;" /> 
        </td> 
    </tr> 
    <tr> 
        <th><label for="news_description">Description:</label></th> 
        <td> 
            <textarea id="news_description" name="news_description" rows="5" style="width:100%;"><?php echo $dataArray['news_description']; ?></textarea> 
        </td> 
    </tr> 
    <tr> 
        <th>Category:</th> 
        <td>
            <table cellspacing="0" cellpadding="0" id="category-table"> 
                <tr> 
                    <td>
                        <input type="radio" name="news_category" value="17" <?php echo '17' == $dataArray['news_category'] ? 'checked="checked"' : ''; ?> />Anime
                    </td> 
                    <td> 
                        <input type="radio" name="news_category" value="1" <?php echo '1' == $dataArray['news_category'] ? 'checked="checked"' : ''; ?> />Auto
                    </td> 
                    <td> 
                        <input type="radio" name="news_category" value="2" <?php echo '2' == $dataArray['news_category'] ? 'checked="checked"' : ''; ?> />Celebrities
                    </td> 
                </tr>
                <tr> 
                    <td> 
                        <input type="radio" name="news_category" value="3" <?php echo '3' == $dataArray['news_category'] ? 'checked="checked"' : ''; ?> />Comedy
                    </td> 
                    <td> 
                        <input type="radio" name="news_category" value="4" <?php echo '4' == $dataArray['news_category'] ? 'checked="checked"' : ''; ?> />Cooking
                    </td> 
                    <td> 
                        <input type="radio" name="news_category" value="5" <?php echo '5' == $dataArray['news_category'] ? 'checked="checked"' : ''; ?> />Educational
                    </td> 
                </tr>
                <tr> 
                    <td> 
                        <input type="radio" name="news_category" value="6" <?php echo '6' == $dataArray['news_category'] ? 'checked="checked"' : ''; ?> />Extreme
                    </td> 
                    <td> 
                        <input type="radio" name="news_category" value="7" <?php echo '7' == $dataArray['news_category'] ? 'checked="checked"' : ''; ?> />Fashion
                    </td> 
                    <td> 
                        <input type="radio" name="news_category" value="8" <?php echo '8' == $dataArray['news_category'] ? 'checked="checked"' : ''; ?> />Gadget &amp; Technology
                    </td> 
                </tr>
                <tr> 
                    <td> 
                        <input type="radio" name="news_category" value="9" <?php echo '9' == $dataArray['news_category'] ? 'checked="checked"' : ''; ?> />Gaming
                    </td> 
                    <td> 
                        <input type="radio" name="news_category" value="20" <?php echo '20' == $dataArray['news_category'] ? 'checked="checked"' : ''; ?> />Metal Music
                    </td> 
                    <td> 
                        <input type="radio" name="news_category" value="10" <?php echo '10' == $dataArray['news_category'] ? 'checked="checked"' : ''; ?> />Music
                    </td> 
                    </tr>
                <tr> 
                    <td> 
                        <input type="radio" name="news_category" value="11" <?php echo '11' == $dataArray['news_category'] ? 'checked="checked"' : ''; ?> />News and Politics
                    </td> 
                    <td> 
                        <input type="radio" name="news_category" value="12" <?php echo '12' == $dataArray['news_category'] ? 'checked="checked"' : ''; ?> />Others
                    </td> 
                    <td> 
                        <input type="radio" name="news_category" value="13" <?php echo '13' == $dataArray['news_category'] ? 'checked="checked"' : ''; ?> />Pets &amp; Animals
                    </td> 
                </tr>
                <tr> 
                    <td> 
                        <input type="radio" name="news_category" value="14" <?php echo '14' == $dataArray['news_category'] ? 'checked="checked"' : ''; ?> />Sports
                    </td> 
                    <td> 
                        <input type="radio" name="news_category" value="15" <?php echo '15' == $dataArray['news_category'] ? 'checked="checked"' : ''; ?> />Travel
                    </td> 
                    <td> 
                        <input type="radio" name="news_category" value="16" <?php echo '16' == $dataArray['news_category'] ? 'checked="checked"' : ''; ?> />TV/Movies
                    </td> 
                </tr> 
            </table>
            <label for="news_category" class="error">Please select the news category</label>
        </td> 
    </tr> 
    <tr> 
        <th><label for="news_url">URL:</label></th> 
        <td> 
            <input type="text" id="news_url" name="news_url" value="<?php echo $dataArray['news_url']; ?>" style="width:100%;" /> 
        </td> 
    </tr> 
    <tr> 
        <th>Picture:</th> 
        <td>
            <img id="thumbnail_image" src="<?php echo $dataArray['news_image']; ?>" alt="" /><br /><br />
            <input type="hidden" id="news_image" name="news_image" value="<?php echo 'images/blank.png' != $dataArray['news_image'] ? $dataArray['news_image'] : ''; ?>" />
            

            <div id="loading-image-block">
                <span id="loader" style="display:none;"><img src="images/loader.gif" alt="Loading..."/></span> <span id="progress"></span>
                
                <div id="loading-image-menu">
                    <p>Upload from disk:</p>
                    <p><input id="add_picture" type="button" value="Add Picture" /></p>
                    <p style="clear: both;">or</p>
                    <p>Insert picture URL (<a  href="#TB_inline?height=550&width=400&inlineId=searchcontrol" class="thickbox">Use google to search image</a>):</p>
                    <p><input type="text" id="image-from-url" value="" /> <input id="button_from_url" type="button" value="Insert" /></p>
                </div>
                
                <div id="uploaded_image">
                    <p><img src="" width="450" height="450" id="cropbox" /></p>
                    <br />
                    <p><input id="save_thumbnail" type="button" value="Save thumbnail" /></p>
                </div>
            </div>
        </td> 
    </tr> 
    <tr> 
        <th>Confirm that your<br />news has video:</th> 
        <td style="vertical-align: middle;">
            <input type="checkbox" id="news_has_video" name="news_has_video" value="1" <?php echo $dataArray['news_has_video'] ? 'checked="checked"' : ''; ?> /><br />
            <label for="news_has_video" class="error">You need to confirm</label>
        </td> 
    </tr> 
    <tr> 
        <th>&nbsp;</th>
        <td>
            <input type="submit" name="publish" value="Publish" />
        </td> 
    </tr> 
</table> 
</form>

<form id="crop-form" action="crop.php" method="post">
    <input type="hidden" id="x" name="x" value="1" />
    <input type="hidden" id="y" name="y" value="1" />
    <input type="hidden" id="w" name="w" value="176" />
    <input type="hidden" id="h" name="h" value="116" />
</form>

<div style="display: none;">
    <div id="searchcontrol">Loading</div>
</div>



</body> 
</html>