<?php

use Illuminate\Database\Seeder;

class MainCustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$customer_arr = [];

    	for ($i=1; $i < 100; $i++) { 
    		$customer_arr[] = [
	        	'ct_salon_name' => 'Salon'.$i,
	        	'ct_contact_name' => 'A.'.$i,
	        	'ct_business_phone' => '123456789'.$i,
	        	'ct_cell_phone' => '123456789'.$i,
	        	'ct_status' => 1,
	        	'created_by' => 10,
	        	'updated_by' => 10
	        ];
    	}
    	\DB::table('main_customer_template')->insert($customer_arr);
    }
}
