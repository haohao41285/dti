<?php
namespace App\Traits;

trait StatisticsTrait
{
    private static function getByMonth($date){
        $startDate = getStartMonthByDate($date);
        $endDate = getEndMonthByDate($date);
        
        return self::getBetween2Date($startDate,$endDate);
    }

    private static function getByYear($date){
        $startDate = format_year($date)."-01-01";
        $endDate = format_year($date)."-12-31";

        return self::getBetween2Date($startDate,$endDate);
    }

    private static function getByQuarterly($date, $valueQuarter){
        switch ($valueQuarter) {
            case 'first':
                $startDate = getStartMonthByDate(format_year($date)."-01-01");
                $endDate = getEndMonthByDate(format_year($date)."-03-01");
                break;
            case 'second':
                $startDate = getStartMonthByDate(format_year($date)."-04-01");
                $endDate = getEndMonthByDate(format_year($date)."-06-01");
                break;
            case 'third':
                $startDate = getStartMonthByDate(format_year($date)."-07-01");
                $endDate = getEndMonthByDate(format_year($date)."-09-01");
                break;
            case 'fourth':
                $startDate = getStartMonthByDate(format_year($date)."-10-01");
                $endDate = getEndMonthByDate(format_year($date)."-12-01");
                break;
        }
        
        return self::getBetween2Date($startDate,$endDate);
    }

    private static function getByDate($date){
        $startDate = $date;
        $endDate =  $date;
        
        return self::getBetween2Date($startDate,$endDate);
    }
}
