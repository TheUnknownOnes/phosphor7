<?php

/**
 * Class s7_conversion
 */
class s7_phphelper
{
    /**
     * @param $strHex string with 32 bit Hex data
     * @return float
     */
    public static function hexToFloat32($strHex) {
        $v = hexdec($strHex);
        $x = ($v & ((1 << 23) - 1)) + (1 << 23) * ($v >> 31 | 1);
        $exp = ($v >> 23 & 0xFF) - 127;
        return $x * pow(2, $exp - 23);
    }

    /**
     * @param $float float to convert to 32 bit hex string
     * @return string
     */
    public static function float32ToHex($float) {
        return strrev(unpack('h*', pack('f', $float))[1]);
    }

    /**
     * @param $data
     * @param $start
     * @return int
     */
    public static function getS7_Int($data, $start) {
        return hexdec(bin2hex(substr($data,$start,4)));
    }

    /**
     * @param $data
     * @param $start
     * @return float|int
     */
    public static function getS7_DInt($data, $start) {
        return hexdec(bin2hex(substr($data,$start,8)));
    }

    /**
     * @param $data string
     * @param $start int
     * @return float
     */
    public static function getS7_Real($data, $start) {
        return self::hexToFloat32(bin2hex(substr($data,$start,4)));
    }

    /**
     * @param $data string to prepare
     * @param $lenght int lenght for the send function
     */
    public static function sendData_Prepare(&$data, &$lenght) {
        $data = "";
        $lenght = 0;
    }

    /**
     * @param $data string Prepared data string
     * @param $lenght int lenght for the send function
     * @param $float2add float
     */
    public static function sendDataAdd_S7_Real(&$data, &$lenght, $float2add) {
        $data .= self::float32ToHex($float2add);
        $lenght += 4;
    }

    /**
     * @param $data string Prepared data string
     * @param $lenght int lenght for the send function
     * @param $number2add int
     */
    public static function sendDataAdd_S7_Int(&$data, &$lenght, $number2add) {
        if ($number2add > (-32767 * -1) || $number2add < -32768) {
            throw new Exception("Given number '$number2add' is out of range for a 16Bit Siemens S7 INT");
        } else {
            $data .= str_pad(dechex($number2add), 4, '0', STR_PAD_LEFT);
            $lenght += 2;
        }
    }

    /**
     * @param $data string Prepared data string
     * @param $lenght int lenght for the send function
     * @param $number2add int
     */
    public static function sendDataAdd_S7_DInt(&$data, &$lenght, $number2add) {
        $data .= str_pad(dechex($number2add), 8, '0', STR_PAD_LEFT);
        $lenght += 4;
    }
}