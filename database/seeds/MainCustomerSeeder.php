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
        $customer_status_arr = [];
        $team_arr = [];

        \DB::table('main_customer_template')->truncate();

    	for ($i=1; $i < 100; $i++) { 
    		$customer_arr[] = [
	        	'ct_salon_name' => 'Salon'.$i,
	        	'ct_contact_name' => 'A.'.$i,
	        	'ct_business_phone' => '123456789'.$i,
	        	'ct_cell_phone' => '123456789'.$i,
	        	'created_by' => 10,
	        	'updated_by' => 10
	        ];
            $customer_status_arr[$i] = 1;
    	}
        $customer_status_list = json_encode($customer_status_arr);

        for ($i=1; $i < 4; $i++) {
            $team_arr[] = [
                'team_name' => 'Team'.$i,
                'team_leader' => $i,
                'team_status' => 1,
                'team_customer_status' => $customer_status_list,
            ];
        }
    	\DB::table('main_customer_template')->insert($customer_arr);
        \DB::table('main_team')->insert($team_arr);

    }
}
