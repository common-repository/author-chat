<?php

/* Author Chat Process v2.0.3 */

if (!function_exists('array_column')) {

    function array_column(array $input, $columnKey, $indexKey = null) {
        $array = array();
        foreach ($input as $value) {
            if (!isset($value[$columnKey])) {
                trigger_error("Key \"$columnKey\" does not exist in array");
                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            } else {
                if (!isset($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not exist in array");
                    return false;
                }
                if (!is_scalar($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not contain scalar value");
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
    }

}

if (!function_exists('wp_verify_nonce') ) {
    require_once( ABSPATH . 'wp-includes/pluggable.php' );
}

if (isset($_POST['function']) && (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'ajax-nonce') != false)) {
    global $wpdb;
    $author_chat_table = $wpdb->prefix . 'author_chat';
    $author_chat_room_participants_table = $wpdb->prefix . 'author_chat_room_participants';
    $wp_usermeta = $wpdb->prefix . 'usermeta';
    $function = sanitize_text_field( $_POST['function'] );
    
    if (isset($_POST['room_pressed_button_id'])) {
        $room_pressed_button_id = strip_tags(sanitize_text_field($_POST['room_pressed_button_id']));
    } else {
        $room_pressed_button_id = '0';
    }
    
    if (isset($_POST['user_time_zone'])) {
        $user_time_zone = strip_tags(sanitize_text_field($_POST['user_time_zone']));
    } else {
        $user_time_zone = '0';
    }
    
    $result = array();

    switch ($function) {
        case( 'getState' ):
            $result = $wpdb->get_var("SELECT COUNT(*) FROM $author_chat_table");
            break;

        case( 'send' ):
            $user_id = strip_tags(sanitize_text_field($_POST['user_id']));
            $nickname = strip_tags(sanitize_text_field($_POST['nickname']));
            $message = strip_tags(sanitize_text_field($_POST['message']));
            $date = date('Y-m-d H:i:s');
            if (( $message ) != '\n') {                
                //$newDate = strtotime("$date +{$user_time_zone} hours");
                //$date = date('Y-m-d,H:i:s', $newDate);
                
                $result = array(
                    'user_id' => $user_id,
                    'nickname' => $nickname,
                    'content' => $message,
                    'chat_room_id' => $room_pressed_button_id,
                    'date' => $date
                );
                
                $wpdb->insert($author_chat_table, $result, array('%d', '%s', '%s', '%d', '%s'));
                
                acppauthorchat_sendFCMNotification($nickname, $message, ["room_id" => $room_pressed_button_id], "AAAAl54tLgs:APA91bFZaptLBvPyNRvReX8WYwKNMRqgt4Mx109sbteaL13t6gx6OOASiA_REWO5g9zjy1jZxqRD_X7BglRMFG3imdr4uUhJ9i94HWK3dciFbEbKi7ErD7nuKaxlFo6dVGAKrcdPjiAd");
            }
            break;

        case( 'update' ):
            $lines = $wpdb->get_results("SELECT id, user_id, nickname, content, chat_room_id, date FROM $author_chat_table ORDER BY id ASC", ARRAY_A);
            $text = array();
            foreach ($lines as $line) {
                if ($line['chat_room_id'] == $room_pressed_button_id) { // Show only main chat room conversation
                    $text[] = $line;
                }
            }
            
            $date = array_column($text, 'date');
            array_walk_recursive($date, function( &$element ) use(&$user_time_zone) {
                $element = strtotime("$element +{$user_time_zone} hours");
                $element = date('Y-m-d,H:i:s', $element);
            });
            $result = array(
                'id' => array_column($text, 'id'),
                'uid' => array_column($text, 'user_id'),
                'nick' => array_column($text, 'nickname'),
                'msg' => array_column($text, 'content'),
                'date' => $date
            );
            break;

        case( 'initiate' ):
            $lines = $wpdb->get_results("SELECT id, user_id, nickname, content, chat_room_id, date FROM $author_chat_table ORDER BY id ASC", ARRAY_A);
            $text = array();
            foreach ($lines as $line) {
                if ($line['chat_room_id'] == $room_pressed_button_id) { // Show only main chat room conversation
                    $text[] = $line;
                }
            }
            $date = array_column($text, 'date');
            array_walk_recursive($date, function( &$element ) use(&$user_time_zone) {
                $element = strtotime("$element +{$user_time_zone} hours");
                $element = date('Y-m-d,H:i:s', $element);
            });
            $result = array(
                'id' => array_column($text, 'id'),
                'uid' => array_column($text, 'user_id'),
                'nick' => array_column($text, 'nickname'),
                'msg' => array_column($text, 'content'),
                'date' => $date
            );
            break;
            
        case( 'addRoom' ):
            //Remove rooms without conversations before adding another one
            $wpdb->query("
                DELETE acrp
                FROM $author_chat_room_participants_table acrp
                LEFT JOIN $author_chat_table ac ON ac.chat_room_id = acrp.chat_room_id
                WHERE ac.chat_room_id IS NULL
		");

            $user_id = sanitize_text_field(strip_tags(filter_var($_POST['user_id'], FILTER_SANITIZE_NUMBER_INT)));
            $room_id = sanitize_text_field(strip_tags(filter_var($_POST['room_id'], FILTER_SANITIZE_NUMBER_INT)));

            $result = array(
                'user_id' => $user_id,
                'chat_room_id' => $room_id
            );

            $wpdb->insert($author_chat_room_participants_table, $result, array('%d', '%d'));
            break;

        case( 'getRoomsForUser' ):
            $user_id = sanitize_text_field(strip_tags(filter_var($_POST['user_id'], FILTER_SANITIZE_NUMBER_INT)));
            
            $lines = $wpdb->get_results("SELECT user_id, chat_room_id FROM $author_chat_room_participants_table WHERE user_id = $user_id", ARRAY_A);
                        
            $text = array();
            foreach ($lines as $line) {
                    $text[] = $line;
            }

            $result = array(
                'chat_room_id' => array_column($text, 'chat_room_id')
            );
            break;
            
        case( 'searchUser' ):
            if (isset($_POST['search_user'])) {
                $user_name = sanitize_text_field(strip_tags(filter_var($_POST['search_user'], FILTER_SANITIZE_STRING, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_ENCODE_HIGH))));

                $lines = $wpdb->get_results("SELECT user_id, meta_value FROM $wp_usermeta WHERE meta_value LIKE '%$user_name%' AND meta_key = 'nickname'", ARRAY_A);

                $text = array();
                foreach ($lines as $line) {
                    $text[] = $line;
                }

                $result = array(
                    'user_id' => array_column($text, 'user_id'),
                    'nickname' => array_column($text, 'meta_value')
                );
            }
            break;
            
        case( 'addUser' ):
            $user_id = sanitize_text_field(strip_tags(filter_var($_POST['user_id'], FILTER_SANITIZE_NUMBER_INT)));
            $room_id = sanitize_text_field(strip_tags(filter_var($_POST['room_id'], FILTER_SANITIZE_NUMBER_INT)));
            
            $duplicate_check = $wpdb->get_results("SELECT user_id FROM $author_chat_room_participants_table WHERE user_id = $user_id AND chat_room_id = $room_id", ARRAY_A);
            
            if (count($duplicate_check) === 0) {
                $result = array(
                    'user_id' => $user_id,
                    'chat_room_id' => $room_id
                );

                $wpdb->insert($author_chat_room_participants_table, $result, array('%d', '%d'));
            }

            break;
        
        case( 'getUsersForRoom' ):
            $room_id = sanitize_text_field(strip_tags(filter_var($_POST['room_id'], FILTER_SANITIZE_NUMBER_INT)));

            //$lines = $wpdb->get_results("SELECT DISTINCT user_id FROM $author_chat_room_participants_table WHERE chat_room_id = $room_id", ARRAY_A);
            
            $lines = $wpdb->get_results("
                SELECT acrp.user_id, um.meta_value 
                FROM $author_chat_room_participants_table acrp 
                INNER JOIN $wp_usermeta um 
                ON acrp.user_id = um.user_id 
                WHERE acrp.chat_room_id = $room_id 
                AND um.meta_key = 'nickname'
                ", ARRAY_A);

                $text = array();
                foreach ($lines as $line) {
                    $text[] = $line;
                }

                $result = array(
                    'user_id' => array_column($text, 'user_id'),
                    'nickname' => array_column($text, 'meta_value')
                );

            break;
            
		case( 'removeUser' ):
            $user_id = sanitize_text_field(strip_tags(filter_var($_POST['user_id'], FILTER_SANITIZE_NUMBER_INT)));
            $room_id = sanitize_text_field(strip_tags(filter_var($_POST['room_id'], FILTER_SANITIZE_NUMBER_INT)));

            $result = array(
                'user_id' => $user_id,
                'chat_room_id' => $room_id
            );

            $wpdb->delete($author_chat_room_participants_table, $result, array('%d', '%d'));

            break;
        
        case( 'whoIsChannelOwner' ):
            $room_id = sanitize_text_field(strip_tags(filter_var($_POST['room_id'], FILTER_SANITIZE_NUMBER_INT)));

            $lines = $wpdb->get_results("SELECT DISTINCT user_id FROM $author_chat_room_participants_table WHERE id IN(SELECT MIN(id) from $author_chat_room_participants_table WHERE chat_room_id = $room_id) AND chat_room_id = $room_id", ARRAY_A);

            $text = array();
            foreach ($lines as $line) {
                $text[] = $line;
            }

            $result = array(
                'user_id' => array_column($text, 'user_id'),
            );

            break;
    }
    echo wp_send_json($result);
}

function acppauthorchat_sendFCMNotification($title = "", $body = "", $customData = [], $serverKey = ""){    
    $topic = $_SERVER['SERVER_NAME'];
    
    if($serverKey != ""){
        ini_set("allow_url_fopen", "On");
        $data = array (
            "to" => "/topics/$topic",
            "notification" => array(
                "body" => sanitize_text_field($body),
                "title" => $title,
            ),
            "data" => $customData
        );

        $args = array(
			'body' => json_encode($data),
			'headers'=> array(
						'Content-Type' => 'application/json',
						'Authorization' => 'key=' . $serverKey
			)
        );
		
        $result = wp_remote_post ( "https://fcm.googleapis.com/fcm/send", $args );
        return json_decode( $result );
    }
    return false;
}

?>