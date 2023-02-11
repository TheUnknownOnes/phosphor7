<?php

require_once("../src/phosphor7.php");

$c = new TSnap7MicroClient();

$c->ConnectTo("10.11.6.2", 0, 0);

$buffer = "";
$c->DBRead(7, 0, 12, $buffer); //reads first 12 bytes of DB07
var_dump(bin2hex($buffer));

$c->Disconnect();
