<?php

/**
 * Class s7_conversion
 */
class s7_phphelper
{
    /**
     * @param $strHex string with 32-bit Hex data
     * @return float
     */
    public static function hexToFloat32(string $strHex): float
    {
        $v = hexdec($strHex);
        $x = ($v & ((1 << 23) - 1)) + (1 << 23) * ($v >> 31 | 1);
        $exp = ($v >> 23 & 0xFF) - 127;
        return $x * pow(2, $exp - 23);
    }

    /**
     * @param $float float to convert to 32 bit hex string
     * @return string
     */
    public static function float32ToHex(float $float): string
    {
        return strrev(unpack('h*', pack('f', $float))[1]);
    }

    /**
     * @param $data string
     * @param $start int
     * @return int
     */
    public static function getS7_Int(string $data, int $start): int
    {
        return hexdec(bin2hex(substr($data,$start,4)));
    }

    /**
     * @param $data string
     * @param $start int
     * @return int
     */
    public static function getS7_DInt(string $data, int $start): int
    {
        return hexdec(bin2hex(substr($data,$start,8)));
    }

    /**
     * @param $data string
     * @param $start int
     * @return float
     */
    public static function getS7_Real(string $data, int $start)
    {
        return self::hexToFloat32(bin2hex(substr($data,$start,4)));
    }

    /**
     * @param $data string to prepare
     * @param $length int lenght for the send function
     */
    public static function sendData_Prepare(string &$data, int &$length): void
    {
        $data = "";
        $length = 0;
    }

    /**
     * @param $data string Prepared data string
     * @param $lenght int lenght for the send function
     * @param $float2add float
     */
    public static function sendDataAdd_S7_Real(string &$data, int &$lenght, float $float2add): void
    {
        $data .= self::float32ToHex($float2add);
        $lenght += 4;
    }

    /**
     * @param $data string Prepared data string
     * @param $length int
     * @param $number2add int
     * @throws Exception
     */
    public static function sendDataAdd_S7_Int(string &$data, int &$length, int $number2add): void
    {
        if ($number2add > (-32767 * -1) || $number2add < -32768) {
            throw new Exception("Given number '$number2add' is out of range for a 16Bit Siemens S7 INT");
        } else {
            $data .= str_pad(dechex($number2add), 4, '0', STR_PAD_LEFT);
            $length += 2;
        }
    }

    /**
     * @param $data string Prepared data string
     * @param $length int lenght for the send function
     * @param $number2add int
     */
    public static function sendDataAdd_S7_DInt(string &$data, int &$length, int $number2add): void
    {
        $data .= str_pad(dechex($number2add), 8, '0', STR_PAD_LEFT);
        $length += 4;
    }
}
