<?php
class Controller_Chats extends Controller { 
    function action_index() { 
        $this->view->generate('chats_view.php', 'template_view.php'); 
    } 
    function action_get() { 
        $this->view->simple('chats_view.php'); 
    } 
}
?>