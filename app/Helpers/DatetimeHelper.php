<?php
use Carbon\Carbon;

if (!function_exists('format_date')) {
    function format_date($date)
    {
        return Carbon::parse($date)->format('m/d/Y') ;
    }
}
if (!function_exists('format_date_m_d')) {
    function format_date_m_d($date)
    {
        return Carbon::parse($date)->format('m/d') ;
    }
}

if (!function_exists('format_date_d_m_y')) {
    function format_date_d_m_y($date)
    {
        return Carbon::parse($date)->format('d-m-Y') ;
    }
}

if (!function_exists('format_date_db')) {
    function format_date_db($date)
    {
        return Carbon::parse($date)->format('Y-m-d') ;
    }
}

if (!function_exists('format_datetime')) {
    function format_datetime($date)
    {
        return Carbon::parse($date)->format('m/d/Y g:i A') ;
    }
}

if (!function_exists('format_time24h')) {
    function format_time24h($time)
    {
        return Carbon::parse($time)->format('H:i') ;
    }
}

if (!function_exists('format_dayMonth')){
    function format_dayMonth($date)
    {
        return Carbon::parse($date)->format('d');
    }
}

if (!function_exists('format_dayWeek')){
    function format_dayWeek($date)
    {   
        $weekMap = [
            0 => 'SUN',
            1 => 'MON',
            2 => 'TUE',
            3 => 'WED',
            4 => 'THU',
            5 => 'FRI',
            6 => 'SAT',
        ];
        return $weekMap[Carbon::parse($date)->dayOfWeek]; //ex:MON
    }
}

if (!function_exists('format_month')){
    function format_month($date){
        return Carbon::parse($date)->format('M');
    }
}

if (!function_exists('format_year')){
    function format_year($date){
        return Carbon::parse($date)->format('Y');
    }
}

if (!function_exists('gettime_by_datetime')) {
    function gettime_by_datetime($date)
    {
        return Carbon::parse($date)->format('g:i A') ;
    }
}

if (!function_exists('get_nowDate')) {
    function get_nowDate($format ='')
    {
        if($format=="")
        {
            return Carbon::now() ;
        }else{
            return Carbon::now()->format($format);
        }
        
    }
}
if (!function_exists('get_startofMonth')) {
    function get_startOfMonth()
    {
        return Carbon::today()->startOfMonth() ;
    }
}
if (!function_exists('get_endOfMonth')) {
    function get_endOfMonth()
    {
        return Carbon::today()->endOfMonth() ;
    }
}

if (!function_exists('getStartMonthByDate')) {
    function getStartMonthByDate($date)
    {
        return Carbon::parse($date)->startOfMonth() ;
    }
}

if (!function_exists('getEndMonthByDate')) {
    function getEndMonthByDate($date)
    {
        return Carbon::parse($date)->endOfMonth() ;
    }
}



?>