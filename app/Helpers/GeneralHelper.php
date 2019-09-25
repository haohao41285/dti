<?php
namespace App\Helpers;

/**
 * ImagesHelper class
 */
class GeneralHelper{
    
   public static function unicodeVietNamese($str){
        $unicodes = array (
                'a' =>'á|à|ạ|ả|ã|ă|ắ|ằ|ặ|ẳ|ẵ|â|ấ|ầ|ậ|ẩ|ẫ',
                'A'	=>'Á|À|Ạ|Ả|Ã|Ă|Ắ|Ằ|Ặ|Ẳ|Ẵ|Â|Ấ|Ầ|Ậ|Ẩ|Ẫ',
                'o' =>'ó|ò|ọ|ỏ|õ|ô|ố|ồ|ộ|ổ|ỗ|ơ|ớ|ờ|ợ|ở|ỡ',
                'O'	=>'Ó|Ò|Ọ|Ỏ|Õ|Ô|Ố|Ồ|Ộ|Ổ|Ỗ|Ơ|Ớ|Ờ|Ợ|Ở|Ỡ',
                'e' =>'é|è|ẹ|ẻ|ẽ|ê|ế|ề|ệ|ể|ễ',
                'E'	=>'É|È|Ẹ|Ẻ|Ẽ|Ê|Ế|Ề|Ệ|Ể|Ễ',
                'u' =>'ú|ù|ụ|ủ|ũ|ư|ứ|ừ|ự|ử|ữ',
                'U'	=>'Ú|Ù|Ụ|Ủ|Ũ|Ư|Ứ|Ừ|Ự|Ử|Ữ',
                'i' =>'í|ì|ị|ỉ|ĩ',
                'I'	=>'Í|Ì|Ị|Ỉ|Ĩ',
                'y' =>'ý|ỳ|ỵ|ỷ|ỹ',
                'Y'	=>'Ý|Ỳ|Ỵ|Ỷ|Ỹ',
                'd' =>'đ',
                'D' =>'Đ',
        );
        foreach($unicodes as $ascii=>$unicode){
                $str = preg_replace("/({$unicode})/",$ascii,$str);
        }
        return $str;
    }
    public static function stripUnicode($str){
         if(!$str) return false;
         $unicode=array(
            'a'=>'á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ',
            'A'=>'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ằ|Ẳ|Ẵ|Ặ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'd'=>'đ',
            'D'=>'Đ',
            'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'i'=>'í|ì|ỉ|ĩ|ị',
            'I'=>'Í|Ì|Ỉ|Ĩ|Ị',
            'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'O'=>'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'y'=>'ý|ỳ|ỷ|ỹ|ỵ',
            'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
         );
         foreach($unicode as $khongdau=>$codau)
         {
            $arr=explode("|",$codau);
            $str=str_replace($arr,$khongdau,$str);
         }
         return $str;
    }
    public static function changeTitle($str){
        $str=trim($str);
        if($str=="") return "";
        $str=str_replace('"','',$str);
        $str=str_replace("'",'',$str);
        $str=self::stripUnicode($str);
        $str=mb_convert_case($str,MB_CASE_LOWER,'utf-8');
        //MB_CASSE_UPPER / MB_CASE_TITLE/ MB_CASE_LOWER
        $str=str_replace(' ','-',$str);
        return $str;

    }
    public static function getCustomerStatus($status_id){
        // 1 is assigned, 2 is disabled, 3 is new arrivals,4 is serviced  
        switch ($status_id) {
            case 1:
                $status_name = "Assigned";
                break;
            case 3:
                $status_name = "Arrivals";
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
            3 => "Arrivals",
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
}