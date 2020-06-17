<?php

function hello()
{
    return "Hello Word !";
}
if (function_exists("is_admin")){
    function is_admin()
    {
        if (Auth::check()) {
            if (Auth::user()->__get("role") == \App\User::ADMIN_ROLE) {
                return true;
            }
        }
        return false;}
}

if(!function_exists("format_money")){
    function format_money($money){
        return "$".number_format($money,2);
    }
}

if(!function_exists("notify")){
    function notify($channel,$event,$data){
        $options = array(
            'cluster' => 'ap1',
            'useTLS' => true
        );
        $pusher = new Pusher\Pusher(
            '80f25d86e9a019d30740',
            '2b5a7af47eb1521ab3e0',
            '1020626',
            $options
        );
        $pusher->trigger($channel, $event, $data);
    }
}
