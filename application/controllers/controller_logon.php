<?php
class Controller_Logon
 extends Controller { 
    function action_index() { 
        $this->view->generate('logon_view.php', 'template_view.php'); 
    } 
}
?>