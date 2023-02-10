<?php

$buffer = null;
$length = null;

require_once("../vendor/phosphor7/src/phosphor7.php");
require_once("../vendor/phosphor7/src/s7_phphelper.php");

$c = new TSnap7MicroClient();

$c->ConnectTo("192.168.7.5", 0, 0);

s7_phphelper::sendData_Prepare($buffer,$length);
s7_phphelper::sendDataAdd_S7_Real($buffer, $length, 333.5);
s7_phphelper::sendDataAdd_S7_Int($buffer,$length, 524);
s7_phphelper::sendDataAdd_S7_DInt($buffer, $length, 442342446);

echo "----Send Data----\n";
echo "Data REAL: 333.5\n";
echo "Data INT : 524\n";
echo "Data DINT: 442342446\n";

echo "Send(RAW): ". $buffer ."\n";

$c->DBWrite(999, 0, $length, $buffer);

$c->Disconnect();
