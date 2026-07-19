<?php
require_once dirname(__DIR__, 2) . '/config/bootstrap.php';
return [
    'driver'=>'smtp','host'=>(string)beyond_config('smtp.host',''),'port'=>(int)beyond_config('smtp.port',465),
    'encryption'=>(string)beyond_config('smtp.secure','ssl'),'username'=>(string)beyond_config('smtp.user',''),
    'password'=>(string)beyond_config('smtp.pass',''),'from_email'=>(string)beyond_config('smtp.from',''),
    'from_name'=>'Beyond ID','reply_to'=>(string)beyond_config('smtp.reply_to',''),
];
