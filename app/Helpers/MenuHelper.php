<?php
namespace App\Helpers;

class MenuHelper{

	public static function getMenuList(){
		return [
		    ['text' => 'Dashboard', 'icon'=>'fas fa-tachometer-alt', 'link' => 'dashboard'],
		    ['text' => 'Customers', 'icon'=>'fas fa-users', 'link' => 'customer','childrens' => [
		        ['text' => 'New Customers', 'link'=> 'customer/customers'],
		        ['text' => 'Merchants', 'link'=> 'customer/merchants'],
		        ['text' => 'Create New Customer', 'link'=> 'customer/add'],
		    ]],
		    ['text' => 'Marketing', 'icon'=>'fas fa-lightbulb', 'link' => 'marketing','childrens' => [
		        ['text' => 'Send SMS', 'link'=> 'marketing/sendsms'],
		        ['text' => 'Tracking History', 'link'=> 'marketing/tracking-history'],        
		    ]],
		    ['text' => 'DataSetup', 'icon'=>'fas fa-database', 'link' => 'datasetup','childrens' => [
		        ['text' => 'Combo', 'link'=> 'datasetup/combos'],
		        ['text' => 'Services', 'link'=> 'datasetup/services'],        
		        ['text' => 'Service Details', 'link'=> 'datasetup/servicedetails'],   
		        ['text' => 'Themes', 'link'=> 'datasetup/themes'],        
		        ['text' => 'Licenses', 'link'=> 'datasetup/licenses'],        
		    ]],
		    ['text' => 'Statistic', 'icon'=>'fas fa-chart-bar', 'link' => 'statistic','childrens' => [
		        ['text' => 'Seller', 'link'=> 'statistic/seller'],
		        ['text' => 'POS', 'link'=> 'statistic/pos'],        
		        ['text' => 'Website', 'link'=> 'statistic/website'],        
		    ]],
		     ['text' => 'IT Tools', 'icon'=>'fas fa-toolbox', 'link' => 'tools','childrens' => [
		        ['text' => 'Clone Website', 'link'=> 'tools/clonewebsite'],
		        ['text' => 'Update Website', 'link'=> 'tools/updatewebsite'],
		    ]],
		    ['text' => 'Users', 'icon'=>'fas fa-user-cog', 'link' => 'user','childrens' => [
		        ['text' => 'Users', 'link'=> 'user/list'],
		        ['text' => 'Roles', 'link'=> 'user/roles'],      
		    ]],    
		    ['text' => 'Settings', 'icon'=>'fas fa-cog', 'link' => 'setting','childrens' => [
		        ['text' => 'Setup Team', 'link'=> 'setting/setup-team'],
		        ['text' => 'Setup Team Type', 'link'=> 'setting/setup-team-type'],
		        ['text' => 'Setup Service', 'link'=> 'setting/setup-service'],
		        ['text' => 'Setup Template SMS', 'link'=> 'setting/setup-template-sms'],	  
		    ]],
		    ['text' => 'Recent Logs', 'icon'=>'fas fa-list-alt', 'link' => 'recentlog'],
		];
	}
}

 ?>