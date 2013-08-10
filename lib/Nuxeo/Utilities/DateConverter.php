<?php
namespace Nuxeo\Utilities;

/**
 * Class DateConverter
 * @package Nuxeo\Utilities
 */
class DateConverter
{

    /**
     * @param $date
     * @return null|string
     */
    public function phpToNuxeo($date)
    {
        try {
            $datetime = new \DateTime($date);
            $time = $datetime->format('Y-m-d');
        } catch (\Exception $e) {
            $time = null;
        }

        return $time;
    }

    /**
     * @param $date
     * @return \DateTime
     */
    public function nuxeoToPhp($date)
    {
        $newDate = explode('T', $date);
        $phpDate = new \DateTime($newDate[0]);
        return $phpDate;
    }

    /**
     * @param $date
     * @return string
     */
    public function inputToPhp($date)
    {
        /**
         * If given a date from user input and DateTime fails to parse it correctly,
         * then it must not be correct, thus we can safely exit.
         */
        try {
            $datetime = new \DateTime($date);
        } catch (\Exception $e) {
            echo 'date not correct';
            exit;
        }

        $phpDate = $datetime->format('Y-m-d');

        return $phpDate;
    }

    /**
     * @param $date
     * @return null|string
     */
    public function inputToNuxeo($date)
    {
        $phpDate     = $this->inputToPhp($date);
        $returnDate  = $this->phpToNuxeo($phpDate);

        return $returnDate;
    }
}
