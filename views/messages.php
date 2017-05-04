<?php 
require_once($_SERVER['DOCUMENT_ROOT'].'/oc-load.php');
require_once(osc_plugins_path().'user-messenger/classes/class.um_functions.php');

$args = Params::getParamsAsArray(); $id = '';
if (isset($args['um_id'])) { $id = $args['um_id']; }
if ($args['um_do'] == 'reply') {
    $messages = um_f::newInstance()->_reply($args);
    $messages = um_f::newInstance()->_read($args['um_redirect'], $id);    
} elseif ($args['um_do'] == 'delete') {
    $messages = um_f::newInstance()->_delete($id);    
} else {
    $messages = um_f::newInstance()->_read($args['um_do'], $id);    
}

    
?> 
<div class="um_cc_wrapper">
    <div class="widget-header" style="margin: 0 0 10px 0;">
        <h3><?php echo sprintf(__('Welcome %s', 'user-messenger'), osc_logged_user_name()); ?></h3>
    </div>
    <div class="um_filter">
        <div class="um_mailbox" id="um_mailbox">
            <a href="<?php echo osc_route_url('um_mailbox', array('um_do' => 'inbox')); ?>" data-id="inbox" class="btn btn-info" style="padding: 5px;"><?php echo __('Inbox', 'user-messenger'); ?></a>    
            <a href="<?php echo osc_route_url('um_mailbox', array('um_do' => 'outbox')); ?>" data-id="outbox" class="btn btn-info" style="padding: 5px;"><?php echo __('Outbox', 'user-messenger'); ?></a>    
            <div id="um_back" data-back="<?php echo $args['um_do']; ?>" class="btn btn-info" style="padding: 5px; display: none;"><a style="color: #fff;"><?php echo __('Back', 'user-messenger'); ?></a></div>
        </div>
        <div class="um_search" id="um_search">
            <input type="text" name="um_search" id="um_search" class="form-control" />
        </div>
    </div>
    
    <div class="clearfix"></div>
    
    <div id="um_messages">
        <?php echo $messages; ?>
    </div>  
    
    <div class="clearfix"></div>
    
        

</div>
<script type="text/javascript">
    $(document).on("ready", function(){
        
        $("input#um_search").attr('placeholder', '<?php _e('Search...', 'user-messenger'); ?>');
        
        $(".um_delete_item").on("click", function(event) {
            event.preventDefault();
            if (confirm("Do you really want to delete this Conversation? This action cannot be undone!")) {
                var id = $(this).data("id"),
                    file = '<?php echo osc_plugin_folder(__FILE__).'messages.php'; ?>',
                    path = '<?php echo osc_base_url(true); ?>';
                    
                $.get(path+"?page=ajax&action=custom&ajaxfile="+file+"&um_do=delete&um_id="+id, function (response) {
                    $("ul#um_"+id).remove();    
                });
            }            
        });
                
        $("#um_load_conv ul li.um_date, #um_load_conv ul li.um_title, #um_load_conv ul li.um_names").on("click", function() {                    
            var id = $(this).parent("ul").data("id"),
                file = '<?php echo osc_plugin_folder(__FILE__).'messages.php'; ?>',
                path = '<?php echo osc_base_url(true); ?>';                
                            
            $("#um_table").slideToggle("fast", function(){
                $("#um_title_"+id).removeAttr("style");                                
                $("#um_search").fadeToggle("fast", function(){
                    $("#um_back").fadeToggle("fast");
                });                
                $.get(path+"?page=ajax&action=custom&ajaxfile="+file+"&um_do=conversation&um_id="+id, function (response) {                                                    
                    var source = $('<div>' + response + '</div>');                        
                        data = source.find('#um_con_wrap').html();
                    $("#um_conversation").remove();            
                    $("#um_messages").append(data);
                    $("#um_conversation_flow").css({
                        "max-height": "85vh",
                        "overflow": "auto"
                    });
                    $("#um_conversation").slideToggle("fast");
                    $("#um_conversation_flow").scrollTop($("#um_conversation_flow").prop("scrollHeight"));
                });                
            });
                                
        });
        
        $("#um_back").on("click", function() {
            $("#um_conversation").slideToggle("slow", function(){                
                $("#um_back").fadeToggle("slow", function(){
                    $("#um_search").fadeToggle("slow");
                });
                $("#um_table").slideToggle("slow");
            });        
        });            
    });    
</script>
