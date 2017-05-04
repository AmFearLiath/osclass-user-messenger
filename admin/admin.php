<?php
if (!defined('OC_ADMIN') || OC_ADMIN!==true) exit('Access is not allowed.');

if (osc_plugin_check_update('user-messenger')) {
    $um_update = '<p>Update available, please do backup from your database and install the newest version</p>';
    osc_add_flash_error_message(__('<strong>Update available</strong> please do backup from your database and install the newest version', 'user-messenger'), 'admin');
}

?>
<div class="um_help">

    <h1><?php _e('User Messenger', 'user-messenger'); ?> v.<?php echo um_version ?></h1>

    <div class="tab_contents">
        
        <div class="um_tab" data-tab-title="Settings" style="display: none;">    
            <div class="um_header"> 
                <h1><?php _e('About', 'user-messenger'); ?></h1>                                                    
                <p><?php _e('This plugin adds a full-featured user messenger to your site. With this, your users can send messages to each other or start a chat. Also you can send system notifications to user.', 'jquery_wysiwyg_editor'); ?></p>
            </div>    
            <div class="um_header"> 
                <h1><?php _e('Settings', 'user-messenger'); ?></h1>                                                    
                <p><?php echo 'User: '.osc_total_users(); print_r(osc_user()); ?></p>
            </div>
            
                    
        </div>
        
        <div class="um_tab" data-tab-title="Help" style="display: none;">    
            <div class="um_header"> 
                <h1><?php _e('Help', 'user-messenger'); ?></h1>
            </div>
            <br /><br />            
            <div class="um_content">
            
                <h2 class="um_title"><?php _e('Installation', 'user-messenger'); ?></h2>
                <p><?php _e('To run this plugin, you have to modify some files from your theme. The here shown examples are based on the files for the theme osclasswizards.', 'user-messenger'); ?></p>
                <p><?php _e('But it should work in other templates too.', 'user-messenger'); ?></p>
                
                <hr />
                
                <h2 class="um_title"><?php _e('Modifications', 'user-messenger'); ?></h2>
                
                <h3 class="um_title"><?php _e('Notifications', 'user-messenger'); ?></h3>
                <p><?php _e('To show the Info menu on frontpage, add this line in the user menu <em>(e.g. header.php in your theme folder)</em>.', 'user-messenger'); ?></p>
                <pre>&lt;?php osc_run_hook('um_menu'); ?&gt;</pre>
                
                <br /><br /><br />
                
                <h3 class="um_title"><?php _e('Buttons', 'user-messenger'); ?></h3>
                <p><?php _e('Add this line in the sidebar of your item page <em>(item.php or item-sidebar.php in your theme folder)</em>, <strong>right before the contact form</strong>. This will add new buttons to send notifications or mails and change the appearance of the contact forms. Both contact forms now slide down, after click on the button.', 'user-messenger'); ?></p>
                <pre>&lt;?php osc_run_hook('um_before_contact'); ?&gt;</pre>
                <p><?php _e('It should looks like this.', 'user-messenger'); ?></p>
<pre>
&lt;ul id="error_list"&gt;&lt;/ul&gt;    
&lt;?php osc_run_hook('um_before_contact'); ?&gt;    
&lt;form action="&lt;?php echo osc_base_url(true); ?&gt;" method="post" name="contact_form" id="contact_form" .....
</pre>
                
                <br /><br /><br />
                
                <h3 class="um_title"><?php _e('Scripts', 'user-messenger'); ?></h3>
                <p><?php _e('Add this line in the sidebar of your item page <em>(item.php or item-sidebar.php in your theme folder)</em>, <strong>right after the contact form</strong>. This will add necessary scripts to handle the contact buttons.', 'user-messenger'); ?></p>
                <pre>&lt;?php osc_run_hook('um_after_contact'); ?&gt;</pre>
                <p><?php _e('It should looks like this.', 'user-messenger'); ?></p>
<pre>
&lt;/form&gt;    
&lt;?php osc_run_hook('um_after_contact'); ?&gt;    
&lt;?php ContactForm::js_validation(); ?&gt;
</pre>
                
                <br /><br /><br />
                
                <h3 class="um_title"><?php _e('Send notifications', 'user-messenger'); ?></h3>
                <p><?php _e('To send notifications in other scripts, you can use this code. Only you have to fill the needed parameter by yourself.', 'user-messenger'); ?></p>
<pre>
&lt;?php
    if (class_exists('um_f')) {
        um_f::newInstance()->_send(
            array(
                'id' => 'affected item id', 
                'from_user' => 'sending user id',
                'to_user' => 'receiving user id',
                'notification_title' => 'Message title',
                'notification_content' => 'Message content'
            )
        );
    }
?&gt;
</pre>          <p><?php _e('I\'ve created an account as system user and gave him manually id \'0\', so i can add this id to <em>from_user</em> and use this account as notification sending systemaccount.', 'user-messenger'); ?></p>
                    
            </div>
        </div>
    </div>
        
</div>