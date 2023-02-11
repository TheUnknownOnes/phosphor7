<?php

/* Phosphor7 - Project
   Web: https://github.com/chaosben/phosphor7
   License: Apache License 2.0
*/

if (! defined("PHOSPHOR7_EXTERNAL_REQUIRE_MANAGEMENT")) {
  require_once("s7_micro_client.php");
}

//see http://snap7.sourceforge.net/siemens_dataformat.html

class S7 {
  private static function BCDtoByte($B) {
    return (($B >> 4) * 10) + ($B & 0x0F);
  }

  private static function ByteToBCD($Value) {
    return ((intval($Value / 10) << 4) | ($Value % 10));
  }

  //Get/Set the bit at Pos.Bit

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @param int Bit
   *  @return bool
   */
  public static function GetBitAt($Buffer, $Pos, $Bit) {
      if ($Bit < 0) $Bit = 0;
      if ($Bit > 7) $Bit = 7;
      return (ord($Buffer[$Pos]) & (2**$Bit)) != 0;
  }

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @param int Bit
   *  @param bool Value
   */
  public static function SetBitAt(&$Buffer, $Pos, $Bit, $Value) {
    $Buffer = str_pad($Buffer, $Pos + 1, "\x00");

    if ($Bit < 0) $Bit = 0;
    if ($Bit > 7) $Bit = 7;

    $Byte = chr(($Value)?(ord($Buffer[$Pos]) | 2**$Bit):(ord($Buffer[$Pos]) & ~(2**$Bit)));
    $Buffer = substr_replace($Buffer, $Byte, $Pos);
  }

  //Get/Set 8 bit signed value (S7 SInt) -128..127

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @return int
   */
  public static function GetSIntAt($Buffer, $Pos) {
    return unpack("c", $Buffer[$Pos])[1];
  }

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @param int Value
   */
  public static function SetSIntAt(&$Buffer, $Pos, $Value) {
    $Buffer = str_pad($Buffer, $Pos, "\x00");
    $Buffer = substr_replace($Buffer, pack("c", $Value), $Pos);
  }


  //Get/Set 16 bit signed value (S7 int) -32768..32767

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @return int
   */
  public static function GetIntAt($Buffer, $Pos) {
      return unpack("s", pack("S", unpack("n", substr($Buffer, $Pos, 2))[1]))[1];
  }

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @param int Value
   */
  public static function SetIntAt(&$Buffer, $Pos, $Value) {
    $Buffer = str_pad($Buffer, $Pos, "\x00");
    $Buffer = substr_replace($Buffer, pack("n", unpack("S", pack("s", $Value))[1]), $Pos);
  }


  //Get/Set 32 bit signed value (S7 DInt) -2147483648..2147483647

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @return int
   */
  public static function GetDIntAt($Buffer, $Pos) {
    return unpack("l", pack("L", unpack("N", substr($Buffer, $Pos, 4))[1]))[1];
  }

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @param int Value
   */
  public static function SetDIntAt(&$Buffer, $Pos, $Value) {
    $Buffer = str_pad($Buffer, $Pos, "\x00");
    $Buffer = substr_replace($Buffer, pack("N", unpack("L", pack("l", $Value))[1]), $Pos);
  }


  //Get/Set 64 bit signed value (S7 LInt) -9223372036854775808..9223372036854775807

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @return int
   */
  public static function GetLIntAt($Buffer, $Pos) {
    return unpack("q", pack("Q", unpack("J", substr($Buffer, $Pos, 8))[1]))[1];
  }

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @param int Value
   */
  public static function SetLIntAt(&$Buffer, $Pos, $Value) {
    $Buffer = str_pad($Buffer, $Pos, "\x00");
    $Buffer = substr_replace($Buffer, pack("J", unpack("Q", pack("q", $Value))[1]), $Pos);
  }


  //Get/Set 8 bit unsigned value (S7 USInt) 0..255

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @return int
   */
  public static function GetUSIntAt($Buffer, $Pos) {
    return unpack("C", $Buffer[$Pos])[1];
  }

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @param int Value
   */
  public static function SetUSIntAt(&$Buffer, $Pos, $Value) {
    $Buffer = str_pad($Buffer, $Pos, "\x00");
    $Buffer = substr_replace($Buffer, pack("C", $Value), $Pos);
  }


  //Get/Set 16 bit unsigned value (S7 UInt) 0..65535

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @return int
   */
  public static function GetUIntAt($Buffer, $Pos) {
    return unpack("n", substr($Buffer, $Pos, 2))[1];
  }

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @param int Value
   */
  public static function SetUIntAt(&$Buffer, $Pos, $Value) {
    $Buffer = str_pad($Buffer, $Pos, "\x00");
    $Buffer = substr_replace($Buffer, pack("n", $Value), $Pos);
  }


  //Get/Set 32 bit unsigned value (S7 UDInt) 0..4294967296

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @return int
   */
  public static function GetUDIntAt($Buffer, $Pos) {
    return unpack("N", substr($Buffer, $Pos, 4))[1];
  }

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @param int Value
   */
  public static function SetUDIntAt(&$Buffer, $Pos, $Value) {
    $Buffer = str_pad($Buffer, $Pos, "\x00");
    $Buffer = substr_replace($Buffer, pack("N", $Value), $Pos);
  }


  //Get/Set 64 bit unsigned value (S7 ULint) 0..18446744073709551616

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @return int
   */
  public static function GetULIntAt($Buffer, $Pos) {
    return unpack("J", substr($Buffer, $Pos, 8))[1];
  }

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @param int Value
   */
  public static function SetULintAt(&$Buffer, $Pos, $Value) {
    $Buffer = str_pad($Buffer, $Pos, "\x00");
    $Buffer = substr_replace($Buffer, pack("J", $Value), $Pos);
  }


  // Get/Set 8 bit word (S7 Byte) 16#00..16#FF

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @return int
   */
  public static function GetByteAt($Buffer, $Pos) {
      return self::GetUSIntAt($Buffer, $Pos);
  }

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @param int Value
   */
  public static function SetByteAt(&$Buffer, $Pos, $Value) {
    self::setUSIntAt($Buffer, $Pos, $Value);
  }


  //Get/Set 16 bit word (S7 Word) 16#0000..16#FFFF

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @return int
   */
  public static function GetWordAt($Buffer, $Pos) {
    return self::GetUIntAt($Buffer, $Pos);
  }

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @param int Value
   */
  public static function SetWordAt(&$Buffer, $Pos, $Value) {
    self::SetUIntAt($Buffer, $Pos, $Value);
  }


  //Get/Set 32 bit word (S7 DWord) 16#00000000..16#FFFFFFFF

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @return int
   */
  public static function GetDWordAt($Buffer, $Pos) {
    return self::GetUDIntAt($Buffer, $Pos);
  }

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @param int Value
   */
  public static function SetDWordAt(&$Buffer, $Pos, $Value) {
    self::SetUDIntAt($Buffer, $Pos, $Value);
  }


  //Get/Set 64 bit word (S7 LWord) 16#0000000000000000..16#FFFFFFFFFFFFFFFF

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @return int
   */
  public static function GetLWordAt($Buffer, $Pos) {
    return self::GetULIntAt($Buffer, $Pos);
  }

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @param int Value
   */
  public static function SetLWordAt(&$Buffer, $Pos, $Value) {
    self::SetULintAt($Buffer, $Pos, $Value);
  }


  //Get/Set 32 bit floating point number (S7 Real) (Range of Single)

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @return float
   */
  public static function GetRealAt($Buffer, $Pos) {
    return unpack("f", pack("L", self::GetUDIntAt($Buffer, $Pos)))[1];
  }

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @param float Value
   */
  public static function SetRealAt(&$Buffer, $Pos, $Value) {
    self::SetUDIntAt($Buffer, $Pos, unpack("L", pack("f", $Value))[1]);
  }


  //Get/Set 64 bit floating point number (S7 LReal) (Range of Double)

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @return float
   */
  public static function GetLRealAt($Buffer, $Pos) {
    return unpack("d", pack("Q", self::GetULIntAt($Buffer, $Pos)))[1];
  }

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @param float Value
   */
  public static function SetLRealAt(&$Buffer, $Pos, $Value) {
    self::SetULIntAt($Buffer, $Pos, unpack("Q", pack("d", $Value))[1]);
  }


  //Get/Set DateTime (S7 DATE_AND_TIME)

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @return DateTime
   */
  public static function GetDateTimeAt($Buffer, $Pos) {
    $Year = $Month = $Day = $Hour = $Min = $Sec = $MSec = 0;

    $Year = self::BCDtoByte(ord($Buffer[$Pos]));
    if ($Year < 90)
      $Year += 2000;
    else
      $Year += 1900;

    $Month = self::BCDtoByte(ord($Buffer[$Pos + 1]));
    $Day = self::BCDtoByte(ord($Buffer[$Pos + 2]));
    $Hour = self::BCDtoByte(ord($Buffer[$Pos + 3]));
    $Min = self::BCDtoByte(ord($Buffer[$Pos + 4]));
    $Sec = self::BCDtoByte(ord($Buffer[$Pos + 5]));
    $MSec = (self::BCDtoByte(ord($Buffer[$Pos + 6])) * 10) + intval(self::BCDtoByte(ord($Buffer[$Pos + 7])) / 10);

    return DateTime::createFromFormat("YmdGisu", sprintf("%04d%02d%02d%02d%02d%02d%d", $Year, $Month, $Day, $Hour, $Min, $Sec, $MSec * 1000));
  }

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @param DateTime Value
   */
  public static function SetDateTimeAt(&$Buffer, $Pos, DateTime $Value) {
    $Buffer = str_pad($Buffer, $Pos, "\x00");

    $Year = (int)$Value->format("Y");
    $Month = (int)$Value->format("n");
    $Day = (int)$Value->format("j");
    $Hour = (int)$Value->format("G");
    $Min = (int)$Value->format("i");
    $Sec = (int)$Value->format("s");
    $Dow = (int)$Value->format("w") + 1;
    // MSecH = First two digits of miliseconds
    $MsecH = intval(($Value->format("u") / 1000) / 10);
    // MSecL = Last digit of miliseconds
    $MsecL = intval($Value->format("u") / 1000) % 10;
    if ($Year > 1999)
      $Year -= 2000;

    $Buffer = substr_replace($Buffer, pack("C8", self::ByteToBCD($Year),
                                                 self::ByteToBCD($Month),
                                                 self::ByteToBCD($Day),
                                                 self::ByteToBCD($Hour),
                                                 self::ByteToBCD($Min),
                                                 self::ByteToBCD($Sec),
                                                 self::ByteToBCD($MsecH),
                                                 self::ByteToBCD($MsecL * 10 + $Dow)), $Pos);
  }


  //Get/Set DATE (S7 DATE)

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @return DateTime
   */
  public static function GetDateAt($Buffer, $Pos) {
    return DateTime::createFromFormat("Y#m#d", "1990-01-01")->add(new DateInterval(sprintf("P%dD", self::GetIntAt($Buffer, $Pos))));
  }

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @param DateTime Value
   */
  public static function SetDateAt(&$Buffer, $Pos, DateTime $Value) {
    self::SetIntAt($Buffer, $Pos, DateTime::createFromFormat("Y#m#d", "1990-01-01")->diff($Value)->days);
  }


  //Get/Set TOD (S7 TIME_OF_DAY)

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @return DateTime
   */
  public static function GetTODAt($Buffer, $Pos) {
    $Val = self::GetDIntAt($Buffer, $Pos);
    $Sec = intval($Val / 1000);
    $USec = ($Val % 1000) * 1000;
    return DateTime::createFromFormat("U.u", "{$Sec}.{$USec}");
  }

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @param DateTime Value
   */
  public static function SetTODAt(&$Buffer, $Pos, DateTime $Value) {
    $Val = (($Value->format("G") * 60 * 60) +
            ($Value->format("i") * 60) +
            ($Value->format("s")) * 1000 +
           intval($Value->format("u") / 1000));

    self::SetDIntAt($Buffer, $Pos, $Val);
  }


  //Get/Set LTOD (S7 1500 LONG TIME_OF_DAY)

  /**
   *  @param String Buffer
   *  @param int Pos
   *  @return DateTime
   */
  public static function GetLTODAt($Buffer, $Pos) {
    //S71500 Tick = 1 ns

    $Val = self::GetLIntAt($Buffer, $Pos);
    $Sec = intval($Val / (10**9));
    $USec = ($Val % (10**9)) / 1000;
    return DateTime::createFromFormat("U.u", "{$Sec}.{$USec}");
  }

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @param DateTime Value
   */
  public static function SetLTODAt(&$Buffer, $Pos, DateTime $Value) {
    $Val = (($Value->format("G") * 60 * 60) +
            ($Value->format("i") * 60) +
            ($Value->format("s") * 10**9) +
           intval($Value->format("u") * 1000));
    self::SetLIntAt($Buffer, $Pos, $Val);
  }


  //GET/SET LDT (S7 1500 Long Date and Time)
  /**
   *  @param string Buffer
   *  @param int Pos
   *  @return DateTime
   */
  public static function GetLDTAt($Buffer, $Pos) {
    return self::GetLTODAt($Buffer, $Pos);
  }

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @param DateTime Value
   *  @return mixed
   */
//  public static function SetLDTAt(&$Buffer, $Pos, DateTime $Value) {
//    $Days = DateTime::createFromFormat("!", "")->diff($Value)->days;
//    $Val = ($Days * 24 * 60 * 60 +
//            $Value->format("G") * 60 * 60 +
//            $Value->format("i") * 60 +
//            $Value->format("s")) * 10**9 +
//           intval($Value->format("u") * 1000);
//      self::SetLIntAt($Buffer, $Pos, ($Value.$Ticks-$bias) * 100);  // << TODO define $Ticks and $bias
//  }


  //Get/Set DTL (S71200/1500 Date and Time)
  //Thanks to Johan Cardoen for GetDTLAt

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @return DateTime
   */
  public static function GetDTLAt($Buffer, $Pos) {
    $Year = self::GetUIntAt($Buffer, $Pos);
    $Month = self::GetUSIntAt($Buffer, $Pos + 2);
    $Day = self::GetUSIntAt($Buffer, $Pos + 3);
    //$Dow = self::GetUSIntAt($Buffer, $Pos + 4);
    $Hour = self::GetUSIntAt($Buffer, $Pos + 5);
    $Min = self::GetUSIntAt($Buffer, $Pos + 6);
    $Sec = self::GetUSIntAt($Buffer, $Pos + 7);
    $USec = self::GetUDIntAt($Buffer, $Pos + 8)/1000;

    return DateTime::createFromFormat("YmdGisu", sprintf("%04d%02d%02d%02d%02d%02d%d", $Year, $Month, $Day, $Hour, $Min, $Sec, $USec));
  }

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @param DateTime Value
   */
  public static function SetDTLAt(&$Buffer, $Pos, DateTime $Value) {
    self::SetUIntAt($Buffer, $Pos, (int)$Value->format("Y"));
    self::SetUSIntAt($Buffer, $Pos + 2, (int)$Value->format("n"));
    self::SetUSIntAt($Buffer, $Pos + 3, (int)$Value->format("j"));
    self::SetUSIntAt($Buffer, $Pos + 4, (int)$Value->format("w") + 1);
    self::SetUSIntAt($Buffer, $Pos + 5, (int)$Value->format("G"));
    self::SetUSIntAt($Buffer, $Pos + 6, (int)$Value->format("i"));
    self::SetUSIntAt($Buffer, $Pos + 7, (int)$Value->format("s"));
    self::SetUDIntAt($Buffer, $Pos + 8, (int)$Value->format("u") * 1000);
  }


  //Get/Set String (S7 String)
  //Thanks to Pablo Agirre

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @return string
   */
  public static function GetStringAt($Buffer, $Pos) {
      $size = self::GetUSIntAt($Buffer, $Pos + 1);
      return implode("", unpack("A*", substr($Buffer, $Pos + 2, $size)));
  }

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @param int MaxLen
   *  @param int Value
   */
  public static function SetStringAt(&$Buffer, $Pos, $MaxLen, $Value) {
    self::SetUSIntAt($Buffer, $Pos, $MaxLen);
    self::SetUSIntAt($Buffer, $Pos + 1, strlen($Value));
    $Buffer = substr_replace($Buffer, pack("A*", $Value), $Pos + 2);
  }


  //Get/Set Array of char (S7 ARRAY OF CHARS)

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @param int Size
   *  @return string
   */
  public static function GetCharsAt($Buffer, $Pos, $Size) {
      return implode("", unpack("A*", substr($Buffer, $Pos, $Size)));
  }

  /**
   *  @param string Buffer
   *  @param int Pos
   *  @param string Value
   */
  public static function SetCharsAt(&$Buffer, $Pos, $Value) {
    $Buffer = str_pad($Buffer, $Pos, "\x00");
    $Buffer = substr_replace($Buffer, pack("A*", $Value), $Pos);
  }

}

?>
