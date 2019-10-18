<?php

if(!function_exists('getCategory')){
	function getCategory(){
		return [
			1 => 'GENERAL',
			2 => 'ISSUE'
		];
	}
}
if(!function_exists('getPriorityTask')){
	function getPriorityTask(){
	    return [
	        1 => 'LOW',
	        2 => 'NORMAL',
	        3 => 'HIGH',
	        4 => 'URGENT',
	        5 => 'IMMEDIATE'
	    ];
	}
}
if(!function_exists('getStatusTask')){

	 function getStatusTask(){
	    return [
	        1 => 'NEW',
	        2 =>'PROCESSING',
	        3 => 'DONE'
	    ];
	}
}

if(!function_exists('getFormService')){
    function getFormService(){
        return [
            1 => 'Google',
            2 => 'Website',
            3 => 'Facebook',
            4 => 'Domain'
        ];
    }
}

?>
