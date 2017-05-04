<?php

class um_f extends DAO {
    
    private static $instance;

    /**
    * Start new Instance
    * 
    */
    public static function newInstance() {
        if( !self::$instance instanceof self ) {
            self::$instance = new self ;
        }
        return self::$instance ;
    }
    
    /**
    * Class Construct
    * 
    */
    function __construct() {
        $this->_sect = 'plugin_user_messenger';
        $this->_table_messages = '`'.DB_TABLE_PREFIX.'t_user_messages`';
        
        parent::__construct();
    }

    /**
    * Install plugin
    * 
    * @param mixed $opts
    * @param mixed $dir
    */
    function _install($opts = '', $dir = '') {

        $file = osc_plugin_resource('/user-messenger/assets/create_table.sql');
        $sql = file_get_contents($file);
        
        if (!$this->dao->importSQL($sql)) {
            throw new Exception( "Error importSQL::spam_prot<br>".$file ) ;
        }

        if ($opts == '') { $opts = $this->_opt(); }        
        foreach ($opts AS $k => $v) {
            osc_set_preference($k, $v[0], $v[1], $v[2]);
        }

        return true;        
    }

    /**
    * Uninstall plugin
    * 
    */
    function _uninstall() {
        $pref = $this->_sect;                
        Preference::newInstance()->delete(array("s_section" => $pref));    
        $this->dao->query(sprintf('DROP TABLE %s', $this->_table_messages));    
    }

    /**
    * Returns preferences
    * 
    * @param mixed $opt
    */
    function _get($opt) {

        $pref = $this->_sect;
        return osc_get_preference($opt, $pref);
    }

    /**
    * Save preferences
    * 
    * @param mixed $opt
    * @param mixed $value
    * @param mixed $type
    */
    function _set($opt, $value, $type) {

        $pref = $this->_sect;
        return osc_set_preference($opt, $value, $pref, $tye);
    }

    /**
    * Options for plugin
    * 
    */
    function _opt() {

        $pref = $this->_sect;        
        $opts = array(
            'umh_...' => array('value', $pref, 'STRING'),
            'umh_...' => array('value', $pref, 'BOOLEAN')
        );
        return $opts;
    }
    
    /**
    * Insert data in table
    * 
    * @param mixed $table
    * @param mixed $set
    */
    function _insert($table, $set) {
        if ($this->dao->insert($this->_table_messages, $set)) {
            return true;
        }
        return false;
    }
    
    /**
    * Update data in table
    * 
    * @param mixed $table
    * @param mixed $set
    * @param mixed $where
    */
    function _update($table, $set, $where) {
        if ($this->dao->update($this->_table_messages, $set, $where)) {
            return true;
        }
        return false;
    }
    
    function _action($params) {                            
        switch ($params['um_do']) {
            case 'send_message':
                if ($this->_send($params)) {
                    osc_add_flash_ok_message(__('Your message was send succesfully.', 'user-messenger'));    
                } else {
                    osc_add_flash_error_message(__('Your message can not send.', 'user-messenger'));
                }               
                
                break;
            case 'send_notification':            
                $user = User::newInstance()->findByPrimaryKey($params['to_user']);
                View::newInstance()->_exportVariableToView('user', $user);
                
                $content = array();
                $content[] = array('{USER_NAME}', '{USER_MAIL}', '{USER_REGDATE}', '{USER_WEBSITE}', '{USER_PHONE_LAND}', '{USER_PHONE_MOBILE}', '{USER_COUNTRY}', '{USER_REGION}', '{USER_CITY}', '{USER_CITY_AREA}', '{USER_ADDRESS}', '{USER_ZIP}');        
                $content[] = array(osc_user_name(), osc_user_email(), osc_user_regdate(), osc_user_website(), osc_user_phone_land(), osc_user_phone_mobile, osc_user_country(), osc_user_region(), osc_user_city(), osc_user_city_area(), osc_user_address(), osc_user_zip());
            
                $params['notification_title'] = osc_mailBeauty($params['notification_title'], $content);
                $params['notification_content'] = osc_mailBeauty($params['notification_content'], $content);
                
                $this->_send($params);
                break;
            case 'reply':
                $this->dao->select('*');
                $this->dao->from($this->_table_messages);
                $this->dao->where('pk_message_id', $params['id']);
                $this->dao->orWhere('i_message_id', $params['id']);
                
                $result = $this->dao->get();
                if (!$result) { return false; }

                return $this->_showConversation($result->result(), $params['id']);
                break;                
            default:
                break;
        }         
    }

    function _check($id) {
        $this->dao->select('COUNT(i_to_user_id) AS count');
        $this->dao->from($this->_table_messages);
        $this->dao->where('i_message_read', '0');
        $this->dao->where('i_to_user_id', $id);
        
        $result = $this->dao->get();
        if (!$result) { return false; }
        
        $row = $result->row();

        return $row['count'];      
    }

    function _send($params) {
        $set = array(
            'i_from_user_id' => $params['from_user'],
            'i_to_user_id' => $params['to_user'],
            'i_item_id' => $params['id'],
            's_message_title' => $params['notification_title'],
            's_message_content' => $params['notification_content'],
        );
        
        if ($this->_insert($this->_table_messages, $set)) {
            return true;
        }
        return false;      
    }

    function _reply($params) {
        $set = array(
            'i_from_user_id' => $params['from_user'],
            'i_to_user_id' => $params['to_user'],
            'i_message_id' => $params['id'],
            's_message_content' => $params['notification_content'],
        );
        
        if ($this->_insert($this->_table_messages, $set)) {
            return true;
        }
        return false;      
    }

    function _delete($id) {
        if ($this->dao->delete($this->_table_messages, array('pk_message_id' => $id))) {
            return true;
        }
        return false;
    }
    
    function _read($action, $id = '') {        
        switch ($action) {
            case "inbox":                       
                $this->dao->select('*');
                $this->dao->from($this->_table_messages);
                $this->dao->where('i_to_user_id', osc_logged_user_id());
                $this->dao->where('i_message_id', '0');
                $this->dao->orderBy('dt_message_date', 'DESC');
                
                $result = $this->dao->get();
                if (!$result) { return false; }

                $messages = $this->_showMailbox($result->result());
                break;
            case "outbox":
                $this->dao->select('*');
                $this->dao->from($this->_table_messages);
                $this->dao->where('i_from_user_id', osc_logged_user_id());
                $this->dao->where('i_message_id', '0');
                $this->dao->orderBy('dt_message_date', 'DESC');
                
                $result = $this->dao->get();
                if (!$result) { return false; }
                
                $messages = $this->_showMailbox($result->result());
                break;
            case "conversation":
                $this->dao->from($this->_table_messages);
                $this->dao->set(array('i_message_read' => '1', 'dt_message_read' => date("Y-m-d H:i:s", time())));
                $this->dao->where('i_to_user_id', osc_logged_user_id());
                $this->dao->where('(`pk_message_id` = "' . $id . '" OR `i_message_id` = "' . $id . '" )');
                $this->dao->update();
                
                $this->dao->select('*');
                $this->dao->from($this->_table_messages);
                $this->dao->where('pk_message_id', $id);
                $this->dao->orWhere('i_message_id', $id);
                
                $result = $this->dao->get();
                if (!$result) { return false; }
                
                $messages = $this->_showConversation($result->result(), $id);
                break;
            default:
                break;
        }
        return $messages;      
    }
    
    function _showConversation($data, $id) {
        if (osc_is_web_user_logged_in()) {
            $messages = '';
            $this->dao->select('i_from_user_id, i_to_user_id');
            $this->dao->from($this->_table_messages);
            $this->dao->where('pk_message_id', $id);
            
            $result = $this->dao->get();
            if (!$result) { return false; }
            $main = $result->row();
            
            if ($main['i_from_user_id'] == osc_logged_user_id()) {
                $sender = User::newInstance()->findByPrimaryKey($main['i_from_user_id']);
                $receiver = User::newInstance()->findByPrimaryKey($main['i_to_user_id']);    
            } elseif ($main['i_to_user_id'] == osc_logged_user_id()) {
                $sender = User::newInstance()->findByPrimaryKey($main['i_to_user_id']);
                $receiver = User::newInstance()->findByPrimaryKey($main['i_from_user_id']);
            }        
                
            foreach($data AS $k => $v) {
                
                $read = ''; $logged = osc_logged_user_id();
                $from = $v['i_from_user_id'];
                $to   = $v['i_to_user_id'];
                
                if ($from == $logged) {
                    $class = ' outgoing';
                    $name = $sender['s_name'];
                    $date = $v['dt_message_date'];
                    $mess = $v['s_message_content'];
                    
                    if ($v['i_message_read'] == '1') {
                        $read = '<small>'.sprintf(__('Read on %s', 'user-messenger'), date('d.m.Y. H:i:s', strtotime($v['dt_message_read']))).'</small>';
                    }
                } elseif ($to == $logged) {
                    $class = ' ingoing';
                    $name = $receiver['s_name'];
                    $date = $v['dt_message_date'];
                    $mess = $v['s_message_content'];
                }
                
                if ($from == $logged || $to == $logged) {
                    $messages .= '
                                    <div id="um_con_messages_'.$v['pk_message_id'].'" class="um_con_message'.$class.'">
                                        <p class="um_con_meta">
                                            <span class="meta_info">'.$name.' '.__('on', 'user-messenger').' '.$date.'</span>
                                            '.($from == $logged ? '<span class="meta_delete" data-id="'.$v['pk_message_id'].'">x</span>' : '').'
                                        </p>        
                                        <p class="um_con_content">'.$mess.'</p>
                                        '.$read.'        
                                    </div>';
                }
            }    
        
            $return = '
                <div class="um_con_wrap" id="um_con_wrap">
                    <div class="um_conversation" id="um_conversation" style="display: none;">
                        <form id="um_form_conversation" name="um_conversation" action="'.osc_base_url(true).'?page=ajax&action=custom&ajaxfile='.osc_plugin_folder(dirname(__FILE__)).'views/messages.php&um_do=reply&um_redirect=conversation&um_id='.$id.'">
                            <input type="hidden" name="from_user" value="'.$sender['pk_i_id'].'" />
                            <input type="hidden" name="to_user" value="'.$receiver['pk_i_id'].'" />                
                            <input type="hidden" name="um_do" value="reply" />
                            <input type="hidden" name="id" value="'.$id.'" />
                            <div id="um_conversation_flow">                
                                '.$messages.'
                            </div>                    
                            <div class="clearfix"></div>
                            <div class="um_reply">
                                '.($sender['pk_i_id'] != '0' && $receiver['pk_i_id'] != '0' ? '
                                <span class="input"><input type="text" id="notification_content" name="notification_content" placeholder="'.__('Enter your answer', 'user-messenger').'" class="form-control" /></span>
                                <span class="button"><button class="btn btn-info" style="padding: 5px;" type="submit">'.__('Submit', 'user-messenger').'</button></span>
                                ' : '
                                <span class="input"><input type="text" id="notification_content" name="notification_content" placeholder="'.__('You cannot answere to this user', 'user-messenger').'" class="form-control" disabled="disabled" /></span>
                                <span class="button"><button class="btn btn-info" style="padding: 5px;" type="button" disabled="disabled">'.__('Submit', 'user-messenger').'</button></span>').'
                            </div>    
                        </form>
                        <script type="text/javascript">
                            $(document).ready(function() {
                                $(document).on("click", "span.meta_delete", function(event) {
                                    if (confirm("You really want to delete this message? Action cannot be undone!")) {
                                        var id = $(this).data("id");
                                        $.get("'.osc_base_url(true).'?page=ajax&action=custom&ajaxfile='.osc_plugin_folder(dirname(__FILE__)).'views/messages.php&um_do=delete&um_id="+id, function(response) {
                                            $("#um_con_messages_"+id).remove();    
                                        });
                                    }                                
                                });
                                $(document).on("submit", "form#um_form_conversation", function(event){
                                    event.preventDefault();
                                
                                    var form    = $(this),
                                        method  = form.attr("method"),
                                        action  = form.attr("action"),
                                        data    = form.serialize();                    
                                    
                                    $.ajax({
                                        url: action,
                                        type: method,
                                        data: data,
                                        cache: false,            
                                        success: function(data){                            
                                            var source = $("<div>"+data+"</div>");
                                                cont   = source.find("#um_conversation_flow").html();
                                            $("#um_conversation_flow").html(cont);
                                            $("#notification_content").val("");
                                            $("#um_conversation_flow").scrollTop($("#um_conversation_flow").prop("scrollHeight"));
                                        }
                                    });
                                });
                            });    
                        </script>
                    </div>    
                </div>';
                
            return $return;
        } else {
            return __('Please login in to show this conversation', 'user-messenger');    
        }   
    }
    
    function _hasUnread($id, $user) {
        $this->dao->select('*');
        $this->dao->from($this->_table_messages);
        $this->dao->where('(`i_message_read` = "0" AND `i_to_user_id` = "' . $user . '" )');
        $this->dao->where('(`pk_message_id` = "' . $id . '" OR `i_message_id` = "' . $id . '" )');

        $result = $this->dao->get();
        if ($result->numRows() == 0) { 
            return false; 
        }
        return true;
    }
    
    function _showMailbox($data) {
        $messages = '';
        if (osc_is_web_user_logged_in()) {    
            foreach($data AS $k => $v) {
                
                $sender = User::newInstance()->findByPrimaryKey($v['i_from_user_id']);
                $receiver = User::newInstance()->findByPrimaryKey($v['i_to_user_id']);
                $read = '';
                
                if ($this->_hasUnread($v['pk_message_id'], osc_logged_user_id())) { $read = ' style="font-weight: bold;"'; }
                    
                $messages .= '
                        <ul id="um_'.$v['pk_message_id'].'" class="um_message" data-id="'.$v['pk_message_id'].'">
                            <li class="um_date">
                                <i class="fa fa-calendar"></i> '.date("d.m.Y", strtotime($v['dt_message_date'])).'<br />
                                <i class="fa fa-clock-o"></i> '.date("H:i:s", strtotime($v['dt_message_date'])).'
                            </li>
                            <li class="um_title" id="um_title_'.$v['pk_message_id'].'"'.$read.'>'.$v['s_message_title'].'</li>
                            <li class="um_names">'.
                                ($v['i_to_user_id'] == osc_logged_user_id() ? $sender['s_name'] : $receiver['s_name']).'
                            </li>
                            <li class="um_delete"><a class="um_delete_item" data-id="'.$v['pk_message_id'].'">Delete</a></li>
                        </ul>';
            }
            
            $return = '
                <div class="um_mailbox_wrap" id="um_mailbox_wrap">
                    <div class="um_table" id="um_table">
                        <ul>
                            <li class="um_table_header">
                                <ul>
                                    <li class="um_date">'.__('Date', 'user-messenger').'</li>
                                    <li class="um_title">'.__('Title', 'user-messenger').'</li>
                                    <li class="um_names">'.($v['i_to_user_id'] == osc_logged_user_id() ? __('From', 'user-messenger') : __('To', 'user-messenger')).'</li>
                                    <li class="um_delete"></li>
                                </ul>
                            </li>
                            <li class="um_table_data" id="um_load_conv">
                                '.$messages.'
                            </li>
                        </ul>
                    </div>
                </div>';
        
            return $return;
        } else {
            return __('Please login in to show your messages', 'user-messenger');
        }   
    }
    
    function r($var){
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    }
    function d($var){
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
    }           
}

?>