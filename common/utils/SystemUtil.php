<?php
/**
 * Created by IntelliJ IDEA.
 * User: vietpn
 * Date: 02/08/2016
 * Time: 11:30
 */

namespace common\utils;

use Yii;


class SystemUtil
{
    public static function isViettelSubscriber($msisdn)
    {
        //msisdn (viettel(097,098,0164....0169 | mobi(090,093,...0128) | vina (091, 094, 0125, 0127, 0129)
        //$pattern = '/^(84|0)(96|97|98|168|164|165|166|167|169)\d-\d{3}-\d{3}$/i'; // Viettel
        $pattern = '/^(84|0)(86|96|97|98|162|163|164|165|166|167|168|169)\d{7}$/i'; // Viettel
        if (preg_match($pattern, $msisdn)) {
            return true;
        } else {
            return false;
        }
    }

    public static function isVinaSubscriber($msisdn)
    {
        //vina (091, 094, 0125, 0127, 0129)
        $pattern = '/^(84|0)(91|94|123|125|127|129)\d{7}$/i'; // Vina pattern
        if (preg_match($pattern, $msisdn)) {
            return true;
        } else {
            return false;
        }
    }

    public static function isMobiSubscriber($msisdn)
    {
        //mobi(090,093,0121,122,124,0128)
        $pattern = '/^(84|0)(89|90|93|120|121|122|124|126|128)\d{7}$/i'; // Mobi pattern
        if (preg_match($pattern, $msisdn)) {
            return true;
        } else {
            return false;
        }
    }

    public static function getTelcoId($msisdn)
    {
        if (SystemUtil::isMobiSubscriber($msisdn)) {
            return 2;
        } else if (SystemUtil::isViettelSubscriber($msisdn)) {
            return 1;
        } else if (SystemUtil::isVinaSubscriber($msisdn)) {
            return 3;
        } else {
            return 0;
        }
    }

}