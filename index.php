<?php
/*
Plugin Name: User Messenger
Plugin URI: http://amfearliath.tk/osclass-user-messenger
Description: An User Messenger for private messaging
Version: 1.1.0
Author: Liath
Author URI: http://amfearliath.tk
Short Name: user_messenger
Plugin update URI: user-messenger

Changelog:
1.0.0 - first published
1.1.0 - changed to DAO Method, some Bugfixes
*/

require_once('classes/class.um_functions.php');
require_once('classes/class.um_hooks.php');

define('um_file', dirname(__FILE__));
define('um_version', '1.1.0');

if (Params::getParam('um_do')) {
    if (Params::getParamsAsArray()) {
        $params = Params::getParamsAsArray();
        um_f::newInstance()->_action($params);     
    }           
}

osc_register_plugin(osc_plugin_path(__FILE__), 'umf_install');
osc_add_hook(osc_plugin_path(__FILE__) . '_uninstall', 'umf_uninstall');
osc_add_hook(osc_plugin_path(__FILE__) . '_configure', 'umf_configuration');

osc_add_hook('header', 'umh_style');
osc_add_hook('admin_header', 'umh_style_admin');

osc_add_hook('init', 'umh_init');
osc_add_hook('admin_init', 'umh_init');
        
if (osc_is_web_user_logged_in()) {       
    osc_add_hook('um_menu','umh_menu');
    osc_add_hook('um_user_menu','umh_user_menu');
}

osc_add_hook('actions_manage_users', 'umh_manage_user');

osc_add_hook('um_before_contact','umh_before_contact');
osc_add_hook('um_after_contact','umh_after_contact');

osc_add_hook('activate_item', 'umh_activate_item');
osc_add_hook('deactivate_item', 'umh_deactivate_item');
osc_add_hook('delete_item', 'umh_delete_item');
osc_add_hook('add_comment', 'umh_add_comment');
osc_add_hook('item_premium_on', 'umh_premium_on');
osc_add_hook('item_premium_off', 'umh_premium_off');
osc_add_hook('item_spam_on', 'umh_spam_on');
osc_add_hook('item_spam_off', 'umh_spam_off');

if(osc_version() < 311) {
    osc_add_hook('admin_menu', 'umh_admin_menu');
} else {
    osc_add_hook('admin_menu_init', 'umh_admin_menu_init');
}

osc_add_route('um_mailbox', 'messages/(.+)', 'messages/{um_do}', osc_plugin_folder(__FILE__).'views/messages.php', false, 'custom', 'Mailbox', __('My messages', 'user-messenger'));
osc_add_route('um_notify', 'notification/(.+)', 'notification/{um_id}', osc_plugin_folder(__FILE__).'views/notification.php', false, 'custom', 'Notification', __('Send notification', 'user-messenger'));

function umf_install() {
    um_f::newInstance()->_install('', um_file);
}

function umf_uninstall() {
    um_f::newInstance()->_uninstall();
}

function umf_configuration() {
    osc_admin_render_plugin(osc_plugin_path(um_file).'/admin/admin.php');
}

function umh_load() {
    echo um_h::_load(osc_version());
}

function umh_style() {
    echo um_h::_style(osc_plugin_path(um_file));
}

function umh_style_admin() {
    echo um_h::_style_admin(osc_plugin_path(um_file));
}

function umh_init() {           
    um_h::_init(um_file);
}

function umh_admin_menu() {
    echo um_h::_admin_menu(osc_plugin_folder(__FILE__));
}

function umh_admin_menu_init() {
    echo um_h::_admin_menu_init(osc_plugin_folder(__FILE__));    
}    

function umh_menu() {           
    um_h::_menu();
}    

function umh_user_menu() {           
    um_h::_user_menu();
}

function umh_manage_user($options, $users) {           
    return um_h::_manage_user($options, $users);
}

function umh_manage_user_popup() {           
    echo um_h::_manage_user_popup();
}

function umh_before_contact() {           
    um_h::_before_contact();
}

function umh_after_contact() {           
    um_h::_after_contact();
}

function umh_activate_item($id) {
    um_h::_activate_item($id);
}

function umh_deactivate_item($id) {
    um_h::_deactivate_item($id);
}

function umh_delete_item($id) {
    um_h::_delete_item($id);
}

function umh_add_comment($item) {
    um_h::_add_comment($item);
}

function umh_premium_on($id) {
    um_h::_premium_on($id);
}

function umh_premium_off($id) {
    um_h::_premium_off($id);
}

function umh_spam_on($id) {
    um_h::_spam_on($id);
}

function umh_spam_off($id) {
    um_h::_spam_off($id);
}                      
?>