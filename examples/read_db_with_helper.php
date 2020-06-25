<pre>
<?php

$buffer = null;
$lenght = null;

require_once("../vendor/phosphor7/src/phosphor7.php");
require_once("../vendor/phosphor7/src/s7_phphelper.php");

$c = new TSnap7MicroClient();

$c->ConnectTo("192.168.7.5", 0, 0);

$c->DBRead(999, 0, 10, $buffer);

echo "----Read Data----\n";
echo "Data(RAW): ".bin2hex(substr($buffer,0,10))."\n";
$float = s7_phphelper::getS7_Real($buffer,0);
echo "Data REAL: ".$float."\n";
$int   = s7_phphelper::getS7_Int($buffer,4);
echo "Data INT:  ".$int."\n";
$dint   = s7_phphelper::getS7_DInt($buffer,6);
echo "Data INT:  ".$dint."\n";

$c->Disconnect();

?>
</pre>