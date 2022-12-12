<?php
class Controller_Logoff
 extends Controller { 
    function action_index() { 
        session_start();
        $user = new User();
        $user->logoff();
        
        unset($_SESSION['oauth']);
        unset($_SESSION['token']);
        unset($_SESSION['user_id']);

        //echo $_SESSION['loged'].'<br>';    
        header("Location: /"); 

    } 
}
?>