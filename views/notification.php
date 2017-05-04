<?php 
$userID = Params::getParam('um_id');
$user  = User::newInstance()->findByPrimaryKey($userID);
View::newInstance()->_exportVariableToView('user', $user);
?> 
<div class="user_notification">
     <h2><?php  _e('Send notification to:', 'user-messenger'); ?> <?php echo osc_user_name(); ?></h2>        
    <p><?php  _e('You can use following placeholder:', 'user-messenger'); ?></p>
    <small>
        <?php echo (osc_user_name() ? '<div class="shortcode">{USER_NAME}</div><div class="value">'.osc_user_name().'</div><br />' : ''); ?>
        <?php echo (osc_user_email() ? '<div class="shortcode">{USER_MAIL}</div><div class="value">'.osc_user_email().'</div><br />' : ''); ?>
        <?php echo (osc_user_regdate() ? '<div class="shortcode">{USER_REGDATE}</div><div class="value">'.osc_user_regdate().'</div><br />' : ''); ?>
        <?php echo (osc_user_website() ? '<div class="shortcode">{USER_WEBSITE}</div><div class="value">'.osc_user_website().'</div><br />' : ''); ?>
        <?php echo (osc_user_phone_land() ? '<div class="shortcode">{USER_PHONE_LAND}</div><div class="value">'.osc_user_phone_land().'</div><br />' : ''); ?>
        <?php echo (osc_user_phone_mobile() ? '<div class="shortcode">{USER_PHONE_MOBILE}</div><div class="value">'.osc_user_phone_mobile().'</div><br />' : ''); ?>
        <?php echo (osc_user_country() ? '<div class="shortcode">{USER_COUNTRY}</div><div class="value">'.osc_user_country().'</div><br />' : ''); ?>
        <?php echo (osc_user_region() ? '<div class="shortcode">{USER_REGION}</div><div class="value">'.osc_user_region().'</div><br />' : ''); ?>
        <?php echo (osc_user_city() ? '<div class="shortcode">{USER_CITY}</div><div class="value">'.osc_user_city().'</div><br />' : ''); ?>
        <?php echo (osc_user_city_area() ? '<div class="shortcode">{USER_CITY_AREA}</div><div class="value">'.osc_user_city_area().'</div><br />' : ''); ?>
        <?php echo (osc_user_address() ? '<div class="shortcode">{USER_ADDRESS}</div><div class="value">'.osc_user_address().'</div><br />' : ''); ?>
        <?php echo (osc_user_zip() ? '<div class="shortcode">{USER_ZIP}</div><div class="value">'.osc_user_zip().'</div><br />' : ''); ?>
    </small>
     <p></p>
     <br /><hr /><br />
     <form id="send_notification" action="<?php echo osc_admin_base_url(true).'?page=users'; ?>" method="post">
        <input type="hidden" name="from_user" value="0" />
        <input type="hidden" name="to_user" value="<?php echo $userID; ?>" />
        <input type="hidden" name="id" value="" />
        <input type="hidden" name="page" value="users" />
        <input type="hidden" name="um_do" value="send_notification" />
        <h4>Title</h4>        
        <input type="text" name="notification_title" placeholder="<?php  _e('Enter the title here...', 'user-messenger'); ?>" />
        <h4>Message</h4>
        <textarea name="notification_content" placeholder="<?php  _e('Enter your message here...', 'user-messenger'); ?>"></textarea>
        <button type="submit" class="btn btn-red"><?php _e('Send', 'user-messenger'); ?></button>
    </form>
</div>