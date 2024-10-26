<?php
class Default_Index_ControllerMiddleware {

    function indexAction(){ return;
        echo __FILE__.'<br>';
        echo __CLASS__.'<br>';
        echo __FUNCTION__.'<br>';
		echo 'Default index action middleware controller!';
    }
   
}
