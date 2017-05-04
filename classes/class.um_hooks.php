<?php

class um_h {
        
    private static $instance;
    
    public static function newInstance() {
        if( !self::$instance instanceof self ) {
            self::$instance = new self ;
        }
        return self::$instance ;
    }
    
    public static function _admin_menu($dir) {
        echo '<h3><a href="#">' . __('User Messenger', 'user-messenger') . '</a></h3>
              <ul>
                <li><a href="' . osc_admin_render_plugin_url($dir.'admin/admin.php') . '">&raquo; ' . __('Settings', 'user-messenger') . '</a></li>
                <li><a href="' . osc_admin_render_plugin_url($dir.'admin/help.php') . '">&raquo; ' . __('Help', 'user-messenger') . '</a></li>
              </ul>';
    }

    public static function _admin_menu_init($dir) {
        osc_add_admin_menu_page( __('Toolbox', 'user-messenger'), osc_admin_render_plugin_url($dir.'admin/admin.php'), 'liath', 'administrator' );
        osc_add_admin_submenu_divider('liath', 'User Messenger', 'um_admin_divider', 'administrator');
        osc_add_admin_submenu_page('liath', __('Settings', 'user-messenger'), osc_admin_render_plugin_url($dir.'admin/admin.php'), 'um_admin_settings', 'administrator');
        osc_add_admin_submenu_page('liath', __('Help', 'user-messenger'), osc_admin_render_plugin_url($dir.'admin/help.php'), 'um_admin_help', 'administrator');
    }    

    public static function _style($dir) {
        osc_enqueue_style('user-messenger-styles', osc_plugin_url($dir.'/assets/css/user-messenger.css').'user-messenger.css');
        osc_enqueue_style('user-messenger-slider', osc_plugin_url($dir.'/assets/css/tabit.css').'tabit.css');
    }

    public static function _style_admin($dir) {
        osc_enqueue_style('user-messenger-styles-admin', osc_plugin_url($dir.'/assets/css/user-messenger-admin.css').'user-messenger-admin.css');
        osc_enqueue_style('user-messenger-styles-slider', osc_plugin_url($dir.'/assets/css/tabit.css').'tabit.css');
    }
    
    public static function _init($dir) {           
        if (osc_version() < 311) {
            osc_add_hook('header',       'umh_load', 10);
            osc_add_hook('admin_header', 'umh_load', 10);
        } else {                
            osc_register_script('user-messenger-main', osc_plugin_url($dir.'/assets/js/user-messenger.js').'user-messenger.js', array('jquery'));        
            osc_register_script('user-messenger-main', osc_plugin_url($dir.'/assets/js/plugin.tabit.js').'plugin.tabit.js', array('jquery'));        
            osc_enqueue_script('user-messenger-main');
            osc_add_hook('header',       'umh_load', 10);
            osc_add_hook('admin_header', 'umh_load', 10);
        }
        
    }

    public static function _load($version) {
        
        $pref = um_f::newInstance()->_sect;

        if ($version > 310) {
            echo '
            <script type="text/javascript">
                (function($) {
                    jQuery(document).ready(function() {
                        $(".um_tab").tabIt({
                            open_tab_animation : "fly",
                            animation_duration : 300 
                        });
                    });
                })(jQuery);                    
            </script>';                
        } else {
                
        }
    }
    
    public static function _menu() {
     
        $notifications = um_f::newInstance()->_check(osc_logged_user_id());
        $notif = '';
        if ($notifications > 0) {
            $notif = '('.$notifications.')';
        }
                      
        echo '<a href="'.osc_route_url('um_mailbox', array('um_do' => 'inbox')).'" title="'.__('Your notifications', 'user-messenger').'"><i class="lnr lnr-envelope"></i> '.sprintf(__('Notifications %s', 'user-messenger'), $notif).'</a>';
    }
    
    public static function _user_menu() {
    
        $section = Rewrite::newInstance()->get_section();
        $notifications = um_f::newInstance()->_check(osc_logged_user_id());
        $notif = '';
        if ($notifications > 0) {
            $notif = '('.$notifications.')';
        }
        echo '
            <li'.($section == 'Mailbox' ? ' class="active"' : '').'>
                <a href="'.osc_route_url('um_mailbox', array('um_do' => 'inbox')).'" title="'.__('Your notifications', 'user-messenger').'"><i class="fa fa-envelope-o"></i> '.sprintf(__('My Notifications %s', 'user-messenger'), $notif).'</a>
            </li>
        ';              
    }
    
    public static function _manage_user($options, $users) {
        $id = $users['pk_i_id'];
        $options[] = '<a href="'.osc_route_admin_url('um_notify', array('um_id' => $id)).'" class="toggle_notification" data-id="'.$id.'">'.__('Send notification', 'user-messenger').'</a>';
        return $options;    
    }
    
    public static function _before_contact() {           
        echo '
            <div class="um_contact_wrapper">
                <a class="um_contact_button btn btn-info" id="um_send_mail" style="width: 45%; padding: 5px; float: left;"><span>'.__('Send email', 'user-messenger').'</span></a>
                <a class="um_contact_button btn btn-info" id="um_send_pm" style="width: 45%; padding: 5px; float: right;"><span>'.__('Send notification', 'user-messenger').'</span></a>
                <div style="clear: both;"></div>
            </div>
            <form action="'.osc_base_url(true).'" method="post" name="notification_form" id="notification_form" style="display: none;">
                <input type="hidden" name="page" value="item" />
                <input type="hidden" name="id" value="'.osc_item_id().'" />
                <input type="hidden" name="from_user" value="'.osc_logged_user_id().'" />
                <input type="hidden" name="to_user" value="'.osc_item_user_id().'" />
                <input type="hidden" name="um_do" value="send_message" />
                
                <div class="form-group">
                    <label class="control-label" for="message">
                        '.__('Message', 'user-messenger').'
                    </label>
                    <div class="controls textarea">
                        <input type="text" name="notification_title" id="notification_title" maxlength="100" />
                        <textarea id="notification_content" name="notification_content" rows="10" placeholder="'.__('Enter your message here...', 'user-messenger').'"></textarea>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-info">'.__("Send", 'user-messenger').'</button>                
            </form>
        ';
    }
    
    public static function _after_contact() {           
        echo '
        <script type="text/javascript">
            $(document).on("ready", function(){
                $("input#notification_title").attr("placeholder", "'.__('Title', 'user-messenger').'");
                $("#um_send_mail").on("click", function(event){
                    event.preventDefault();
                    if ($("form[name=notification_form]").is(":visible")) {
                        $("form[name=notification_form]").slideToggle("slow", function(){
                            $("form[name=contact_form]").slideToggle("slow");
                        });
                    } else {
                        $("form[name=contact_form]").slideToggle("slow");
                    }                
                });
                $("#um_send_pm").on("click", function(event){
                    event.preventDefault();
                    
                    if ($("form[name=contact_form]").is(":visible")) {
                        $("form[name=contact_form]").slideToggle("slow", function(){
                            $("form[name=notification_form]").slideToggle("slow");
                        });
                    } else {
                        $("form[name=notification_form]").slideToggle("slow");
                    }
                });            
            });
        </script>';
    }
    
    public static function _activate_item($id) {
        
        $item  = Item::newInstance()->findByPrimaryKey($id);
        View::newInstance()->_exportVariableToView('item', $item);
        
        $link   = '<a href="'.osc_item_url().'" >'.osc_item_url().'</a>';
        
        $content = array();
        $content[] = array('{TO_NAME}', '{LINK}', '{PAGE_TITLE}');        
        $content[] = array(osc_item_contact_name(), $link, osc_page_title());
        
        $title = __('Your ad was activated', 'vote-and-review');
        $body  = __('Hello {TO_NAME},','vote-and-review').'<br />
               '.__('We have reviewed and activated your ad now. You can view them here.','vote-and-review').'<br /><br />{LINK}<br /><br />
               '.__('Best regards','vote-and-review').'<br />{PAGE_TITLE}';
                
        $body = osc_mailBeauty($body, $content);
        
        um_f::newInstance()->_send(
            array( 
                'id' => osc_item_id(),
                'from_user' => '0', 
                'to_user' => osc_item_user_id(), 
                'notification_title' => $title, 
                'notification_content' => $body                        
            )
        );    
    }
    
    public static function _deactivate_item($id) {
        
        $item  = Item::newInstance()->findByPrimaryKey($id);
        View::newInstance()->_exportVariableToView('item', $item);
        
        $link   = '<a href="'.osc_contact_url().'" >'.osc_contact_url().'</a>';
        
        $content = array();
        $content[] = array('{TO_NAME}', '{LINK}', '{PAGE_TITLE}');        
        $content[] = array(osc_item_contact_name(), $link, osc_page_title());
        
        $title = __('Your ad was deactivated', 'vote-and-review');
        $body  = __('Hello {TO_NAME},','vote-and-review').'<br />
               '.__('We have deactivated your ad. If you think that this was a mistake, feel free to contact our support here.','vote-and-review').'<br /><br />{LINK}<br /><br />
               '.__('Best regards','vote-and-review').'<br />{PAGE_TITLE}';
                
        $body = osc_mailBeauty($body, $content);
        
        um_f::newInstance()->_send(
            array( 
                'id' => osc_item_id(),
                'from_user' => '0', 
                'to_user' => osc_item_user_id(), 
                'notification_title' => $title, 
                'notification_content' => $body                        
            )
        );        
    }
    
    public static function _delete_item($id) {
        
        $item  = Item::newInstance()->findByPrimaryKey($id);
        View::newInstance()->_exportVariableToView('item', $item);
        
        $link   = '<a href="'.osc_contact_url().'" >'.osc_contact_url().'</a>';
        
        $content = array();
        $content[] = array('{TO_NAME}', '{LINK}', '{PAGE_TITLE}');        
        $content[] = array(osc_item_contact_name(), $link, osc_page_title());
        
        $title = __('Your ad was deleted', 'vote-and-review');
        $body  = __('Hello {TO_NAME},','vote-and-review').'<br />
               '.__('We have deleted your ad. To know the reason, feel free to contact our support here.','vote-and-review').'<br /><br />{LINK}<br /><br />
               '.__('Best regards','vote-and-review').'<br />{PAGE_TITLE}';
                
        $body = osc_mailBeauty($body, $content);
        
        um_f::newInstance()->_send(
            array( 
                'id' => osc_item_id(),
                'from_user' => '0', 
                'to_user' => osc_item_user_id(), 
                'notification_title' => $title, 
                'notification_content' => $body                        
            )
        );        
    }
    
    public static function _add_comment($item) {
        
        $link   = '<a href="'.osc_item_url().'" >'.osc_item_url().'</a>';
        
        $content = array();
        $content[] = array('{TO_NAME}', '{LINK}', '{PAGE_TITLE}');        
        $content[] = array(osc_item_contact_name(), $link, osc_page_title());
        
        $title = __('Your ad was commented', 'vote-and-review');
        $body  = __('Hello {TO_NAME},','vote-and-review').'<br />
               '.__('There is a new comment on your ad, you can check it here.','vote-and-review').'<br /><br />{LINK}<br /><br />
               '.__('Best regards','vote-and-review').'<br />{PAGE_TITLE}';
                
        $body = osc_mailBeauty($body, $content);
        
        um_f::newInstance()->_send(
            array( 
                'id' => osc_item_id(),
                'from_user' => '0', 
                'to_user' => osc_item_user_id(), 
                'notification_title' => $title, 
                'notification_content' => $body                        
            )
        );        
    } 
    
    public static function _premium_on($id) {
        
        $item  = Item::newInstance()->findByPrimaryKey($id);
        View::newInstance()->_exportVariableToView('item', $item);
        
        $link   = '<a href="'.osc_item_url().'" >'.osc_item_url().'</a>';
        
        $content = array();
        $content[] = array('{TO_NAME}', '{LINK}', '{PAGE_TITLE}');        
        $content[] = array(osc_item_contact_name(), $link, osc_page_title());
        
        $title = __('Your ad is now premium', 'vote-and-review');
        $body  = __('Hello {TO_NAME},','vote-and-review').'<br />
               '.__('We have marked your ad as premium. You can view them here.','vote-and-review').'<br /><br />{LINK}<br /><br />
               '.__('Best regards','vote-and-review').'<br />{PAGE_TITLE}';
                
        $body = osc_mailBeauty($body, $content);
        
        um_f::newInstance()->_send(
            array( 
                'id' => osc_item_id(),
                'from_user' => '0', 
                'to_user' => osc_item_user_id(), 
                'notification_title' => $title, 
                'notification_content' => $body                        
            )
        );        
    } 
    
    public static function _premium_off($id) {
        
        $item  = Item::newInstance()->findByPrimaryKey($id);
        View::newInstance()->_exportVariableToView('item', $item);
        
        $link   = '<a href="'.osc_item_url().'" >'.osc_item_url().'</a>';
        
        $content = array();
        $content[] = array('{TO_NAME}', '{LINK}', '{PAGE_TITLE}');        
        $content[] = array(osc_item_contact_name(), $link, osc_page_title());
        
        $title = __('Your ad is no longer premium', 'vote-and-review');
        $body  = __('Hello {TO_NAME},','vote-and-review').'<br />
               '.__('Your ad is no longer marked as premium. If you want you can renew the premium status.','vote-and-review').'<br /><br />{LINK}<br /><br />
               '.__('Best regards','vote-and-review').'<br />{PAGE_TITLE}';
                
        $body = osc_mailBeauty($body, $content);
        
        um_f::newInstance()->_send(
            array( 
                'id' => osc_item_id(),
                'from_user' => '0', 
                'to_user' => osc_item_user_id(), 
                'notification_title' => $title, 
                'notification_content' => $body                        
            )
        );   
                
    } 
    
    public static function _spam_on($id) {
        
        $item  = Item::newInstance()->findByPrimaryKey($id);
        View::newInstance()->_exportVariableToView('item', $item);
        
        $link   = '<a href="'.osc_contact_url().'" >'.osc_contact_url().'</a>';
        
        $content = array();
        $content[] = array('{TO_NAME}', '{LINK}', '{PAGE_TITLE}');        
        $content[] = array(osc_item_contact_name(), $link, osc_page_title());
        
        $title = __('Your ad was marked as spam', 'vote-and-review');
        $body  = __('Hello {TO_NAME},','vote-and-review').'<br />
               '.__('We have marked your ad as spam. To know the reason, feel free to contact our support here.','vote-and-review').'<br /><br />{LINK}<br /><br />
               '.__('Best regards','vote-and-review').'<br />{PAGE_TITLE}';
                
        $body = osc_mailBeauty($body, $content);
        
        um_f::newInstance()->_send(
            array( 
                'id' => osc_item_id(),
                'from_user' => '0', 
                'to_user' => osc_item_user_id(), 
                'notification_title' => $title, 
                'notification_content' => $body                        
            )
        );
        
                
    } 
    
    public static function _spam_off($id) {
        
        $item  = Item::newInstance()->findByPrimaryKey($id);
        View::newInstance()->_exportVariableToView('item', $item);
        
        $link   = '<a href="'.osc_contact_url().'" >'.osc_contact_url().'</a>';
        
        $content = array();
        $content[] = array('{TO_NAME}', '{LINK}', '{PAGE_TITLE}');        
        $content[] = array(osc_item_contact_name(), $link, osc_page_title());
        
        $title = __('Your ad is no longer marked as spam', 'vote-and-review');
        $body  = __('Hello {TO_NAME},','vote-and-review').'<br />
               '.__('Your ad is no longer marked as spam. You can view then here.','vote-and-review').'<br /><br />{LINK}<br /><br />
               '.__('Best regards','vote-and-review').'<br />{PAGE_TITLE}';
                
        $body = osc_mailBeauty($body, $content);
        
        um_f::newInstance()->_send(
            array( 
                'id' => osc_item_id(),
                'from_user' => '0', 
                'to_user' => osc_item_user_id(), 
                'notification_title' => $title, 
                'notification_content' => $body                        
            )
        );
        
                
    }    
    
}

?>