<?php
/*
Plugin Name: Clipta Video Informer
Plugin URI: http://info.clipta.com
Description: Promote your content by submitting your news and posts with video to <a href="http://info.clipta.com" target="_blank">info.clipta.com</a>
Version: 1.0
Author: Clipta team
Author URI: http://info.clipta.com/
*/


if (!class_exists("CliptaVideoInformer")) {
    class CliptaVideoInformer {
        
        var $adminOptionsName = "CliptaVideoInformer_AdminOptions";
        
        /**
         * constructor
         */
        function CliptaVideoInformer() {
            
        }
        
        
        /**
         * initialization
         */
        function init() {
            $this->getAdminOptions();
        }
        
        
        /**
         * Returns an array of admin options
         */
        function getAdminOptions() {
            /* default options */
            $options = array(
                'cliptaEmail'     => '',
                'cliptaPassword'  => '', 
                );
            
            /* get current options */
            $currentOptions = get_option($this->adminOptionsName);
            
            if (!empty($currentOptions)) {
                foreach ($currentOptions as $k => $v)
                    $options[$k] = $v;
            }
            
            /* set current options */
            update_option($this->adminOptionsName, $options);
            
            /* return current options */
            return $options;
        }
        
        
        function printPublishButton() {
            $cvnOptions = $this->getAdminOptions();
            
            global $post;
            
            $website_url    = get_bloginfo('wpurl');
            $post_id        = $post->ID;
            //$postInfo       = get_post($post_id); 
            $post_title     = urlencode($post->post_title);
            $post_content   = urlencode(substr(strip_tags($post->post_content), 0, 300));
            $post_name      = urlencode($post->guid);
            
            $post_has_video = strrpos($post->post_content, 'object ') !== FALSE ? 1 : 0;
            
            $partner_login      = urlencode($cvnOptions['cliptaEmail']);
            $partner_password   = urlencode($cvnOptions['cliptaPassword']);
            
            $url   = '/wp-content/plugins/clipta-video-informer/add-news.php';
            $url  .= '?l=' . $partner_login;
            $url  .= '&amp;p=' . $partner_password;
            $url  .= '&amp;t=' . $post_title;
            $url  .= '&amp;c=' . $post_content;
            $url  .= '&amp;u=' . $post_name;
            $url  .= '&amp;v=' . $post_has_video;
            $url  .= '&amp;w=' . $website_url;
            $url  .= '&amp;TB_iframe=true';

?>
<input type="button" value="Publish to info.clipta.com" title="Publish to info.clipta.com" alt="<?php echo $url; ?>" class="thickbox button-primary" />
<?php
        }
        
        
        /**
         * Prints out the admin page
         */
        function printAdminPage() {
                    $cvnOptions = $this->getAdminOptions();
                                        
                    if (isset($_POST['update_CliptaVideoInformerSettings'])) { 
                        if (isset($_POST['cliptaEmail'])) {
                            $cvnOptions['cliptaEmail'] = $_POST['cliptaEmail'];
                        }
                        if (isset($_POST['cliptaPassword'])) {
                            $cvnOptions['cliptaPassword'] = $_POST['cliptaPassword'];
                        }
                        update_option($this->adminOptionsName, $cvnOptions);
                        
                        ?>
<div class="updated"><p><strong><?php _e("Settings Updated.", "CliptaVideoNews");?></strong></p></div>
                    <?php
                    } ?>
<div class="wrap">
    <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
        <h2>Clipta Video Informer</h2>
        
        <h3>Your login on http://info.clipta.com</h3>
        <input type="text" name="cliptaEmail" value="<?php _e($cvnOptions['cliptaEmail'], 'CliptaVideoInformer') ?>" />
        
        <h3>Your password on http://info.clipta.com</h3>
        <input type="text" name="cliptaPassword" value="<?php _e($cvnOptions['cliptaPassword'], 'CliptaVideoInformer') ?>" />
        
        <div class="submit">
            <input type="submit" name="update_CliptaVideoInformerSettings" value="<?php _e('Update Settings', 'CliptaVideoInformer') ?>" />
        </div>

        <p>Not a partner yet? <a href="http://info.clipta.com/partners_registration" target="_blank">Click here to register.</a></p>
    </form>
 </div>
                    <?php
                }/* End function printAdminPage */
    
    }/* End Class CliptaVideoInformer */
}




if (class_exists("CliptaVideoInformer")) {
    $modelCliptaVideoInformer = new CliptaVideoInformer();
}


/* Initialize the admin panel */
if (!function_exists("CliptaVideoInformer_ap")) {
    function CliptaVideoInformer_ap() {
        global $modelCliptaVideoInformer;
        if (!isset($modelCliptaVideoInformer)) {
            return;
        }
        if (function_exists('add_options_page')) {
            add_options_page('Clipta Video Informer', 'Clipta Video Informer', 9, basename(__FILE__), array(&$modelCliptaVideoInformer, 'printAdminPage'));
        }
    }
}


/* Actions and Filters */
if (isset($modelCliptaVideoInformer)) {
    //Actions
    add_action('admin_menu', 'CliptaVideoInformer_ap');
    add_action('activate_clipta-video-informer/clipta-video-informer.php',  array(&$modelCliptaVideoInformer, 'init'));
    
    add_action('edit_form_advanced',   array(&$modelCliptaVideoInformer, 'printPublishButton'));
}

?>
