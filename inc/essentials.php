<?php
    date_default_timezone_set('America/Port-au-Prince');
    define('SITE_URL', 'http://localhost/magiccitymanager');
    define('SEND_API_KEY', "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");

    function adminLogin(){
        session_start();
        if(!(isset($_SESSION['adminLogin']) && $_SESSION['adminLogin']==true)){
            header("Location: index.php");
            exit;
        }
    }

    function redirect($url){
        header("Location: $url");
        exit;
    }
?>