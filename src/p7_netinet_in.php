<?php

/* Phosphor7 - Project
   Web: https://github.com/chaosben/phosphor7
   License: Apache License 2.0
*/

if (! defined("PHOSPHOR7_EXTERNAL_REQUIRE_MANAGEMENT")) {
  require_once("p7_ctypes.php");
}

class_alias("uint16_t", "in_port_t");

class_alias("uint32_t", "in_addr_t");

class in_addr extends CStruct {
  public function __construct() {
    $this->Members["s_addr"] = new in_addr_t();
  }
}

class sockaddr_in extends CStruct {
  public function __construct() {
    $this->Members["sin_family"] = new int16_t();             /* Port number.  */
    $this->Members["sin_port"] = new in_port_t();             /* Port number.  */
    $this->Members["sin_addr"] = new in_addr();               /* Internet address.  */
    $this->Members["sin_zero"] = new CArray("uint8_t", [8]);  // Padding
  }
}

?>
