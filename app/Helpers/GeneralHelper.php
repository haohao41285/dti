<?php
namespace App\Helpers;


class GeneralHelper{
    public static function getCustomerStatus($status_id){
        // 1 is assigned, 2 is disabled, 3 is new arrivals,4 is serviced
        switch ($status_id) {
            case 1:
                $status_name = "Assigned";
                break;
            case 3:
                $status_name = "New Arrivals";
                break;
            case 4:
                $status_name = "Serviced";
                break;

            default:
                $status_name = "Disabled";
            break;
        }
        return $status_name;
    }
    public static function getCustomerStatusList(){
        return [
            1 => "Assigned",
            2 => "Disabled",
            3 => "New Arrivals",
            4 => "Serviced"
        ];
    }

    public static function getOrdersStatus(){
        return [
            'New',
            'Processing',
            'Done',
            'Fixing',
            'Cancel',
            'Notpayment',
        ];
    }
    public static function getFormOrder()
    {
        return [
            1 => 'Google',
            2 => 'Website',
            3 => 'Facebook',
            4 => 'Domain'
        ];
    }
    public static function getPriorityTask(){
        return [
            1 => 'LOW',
            2 => 'NORMAL',
            3 => 'HIGH',
            4 => 'URGENT',
            5 => 'IMMEDIATE'
        ];
    }
    public static function getStatusTask(){
        return [
            1 => 'NEW',
            2 =>'PROCESSING',
            3 => 'DONE'
        ];
    }
    public static function convertGender($gender){
        if($gender ==1)
                return "Male";
        elseif($gender ==2)
                return "Female";
        else return "Chirld";
    }

    public static function getIpAddress(){
        $ip = null;
        if ($_SERVER) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $ip = getenv('HTTP_X_FORWARDED_FOR');
            } elseif (getenv('HTTP_CLIENT_IP')) {
                $ip = getenv('HTTP_CLIENT_IP');
            } else {
                $ip = getenv('REMOTE_ADDR');
            }
        }
        return $ip;
    }

    public static function callAPI($arr){
        $data = array("name" => "Hagrid", "age" => "36");
        $data_string = json_encode($data);

        $curl = curl_init('http://example.api.com');

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
        );

        $result = curl_exec($curl);
        curl_close($curl);
    }
}
