<?php
class Controller_Main extends Controller { 

    function action_index() { 
        $this->view->generate('main_view.php', 'template_view.php'); 
        //echo '<h1>Main Controller</h1>';
    } 
    
    /*function action_phpinfo() { 
        $this->view->generate('phpinfo_view.php', 'template_view.php'); 
        //echo '<h1>Main Controller. Action=Index1</h1>';
    } */
}
?>