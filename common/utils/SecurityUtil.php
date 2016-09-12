<?php
namespace common\utils;

use Yii;

class SecurityUtil
{
    public static function getTransactionGDC()
    {
        return sha1(microtime(true) . uniqid(rand(), true));
    }

    /**
     * Encrypt Encrypt data send to pay365
     * @param $key_seed
     * @param $input
     * @return string
     */
    public static function encryptData($key_seed, $input)
    {
        $input = trim($input);
        $block = mcrypt_get_block_size('tripledes', 'ecb');
        $len = strlen($input);
        $padding = $block - ($len % $block);
        $input .= str_repeat(chr($padding), $padding);

        // generate a 24 byte key from the md5 of the seed
        $key = substr(md5($key_seed), 0, 24);
        $iv_size = mcrypt_get_iv_size(MCRYPT_TRIPLEDES, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

        // encrypt
        $encrypted_data = mcrypt_encrypt(MCRYPT_TRIPLEDES, $key, $input, MCRYPT_MODE_ECB, $iv);

        // clean up output and return base64 encoded
        return base64_encode($encrypted_data);
    }

    /**
     * Decrypt data receive from 365
     * @param $input
     * @param $key_seed
     * @return string
     */
    public static function decryptData($input, $key_seed)
    {
        $input = base64_decode($input);
        $key = substr(md5($key_seed), 0, 24);
        $text = mcrypt_decrypt(MCRYPT_TRIPLEDES, $key, $input, MCRYPT_MODE_ECB, '12345678');
        $block = mcrypt_get_block_size('tripledes', 'ecb');
        $packing = ord($text{strlen($text) - 1});
        if ($packing and ($packing < $block)) {
            for ($P = strlen($text) - 1; $P >= strlen($text) - $packing; $P--) {
                if (ord($text{$P}) != $packing) {
                    $packing = 0;
                }
            }
        }
        $text = substr($text, 0, strlen($text) - $packing);
        return $text;
    }

    /**
     * cache total buying
     * @param null $amount
     * @return mixed
     */
    public static function totalBuying($amount = null)
    {
        $date = date('Y-m-d');
        $key = 'total_buying_' . $date;
        $total_buying = Yii::$app->cache->get($key);
        if ($total_buying === false) {
            $card_buying = Yii::$app->db->createCommand("SELECT sum(amount) as total_charging FROM card_buying where date_created like '" . $date . "%' and response_code = 1");
            $topup_buying = Yii::$app->db->createCommand("SELECT sum(amount) as total_charging FROM topup_buying where date_created like '" . $date . "%' and response_code = 1");
            $res_card = empty($card_buying->queryAll()[0]['total_charging']) ? 0 : $card_buying->queryAll()[0]['total_charging'];
            $res_topup = empty($topup_buying->queryAll()[0]['total_charging']) ? 0 : $topup_buying->queryAll()[0]['total_charging'];
            $total_buying = $res_card + $res_topup;
            if (!empty($amount) && is_numeric($amount)) {
                $total_buying += $amount;
            }
            Yii::$app->cache->set($key, empty($total_buying) ? 0 : $total_buying, Yii::$app->params['time_cache']);
        } else {
            if (!empty($amount) && is_numeric($amount)) {
                $total_buying += $amount;
                Yii::$app->cache->set($key, empty($total_buying) ? 0 : $total_buying, Yii::$app->params['time_cache']);
            }
        }

        return empty($total_buying) ? 0 : $total_buying;
    }

    /**
     * Khóa thẻ lỗi 30' nếu nạp quá 3 lần lỗi
     * @param $username
     * @param $card_code
     * @param $card_serial
     * @return bool
     */
    public static function limitTimeCharging($username, $card_code, $card_serial)
    {
        $key = $username . '_' . $card_code . '_' . $card_serial . '_Card_Charging';
        $charging = Yii::$app->cache->get($key);

        if ($charging === false || $charging < Yii::$app->params['limit_time_charging']) {
            return true;
        }

        return false;
    }

    /**
     * đếm số lần thẻ lỗi
     * @param $username
     * @param $card_code
     * @param $card_serial
     */
    public static function countLimitTimeCharging($username, $card_code, $card_serial)
    {
        $key = $username . '_' . $card_code . '_' . $card_serial . '_Card_Charging';
        $charging = Yii::$app->cache->get($key);

        if ($charging === false) {
            Yii::$app->cache->set($key, 1, Yii::$app->params['time_cache_charging']);
        }
        $charging += 1;
        Yii::$app->cache->set($key, $charging, Yii::$app->params['time_cache_charging']);
    }
}