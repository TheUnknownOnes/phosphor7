<?php

function addSrc($Name, $Target) {
  echo "Adding " . basename($Name) . PHP_EOL;
  fputs($Target, PHP_EOL);
  $File = fopen($Name, "r");
  try {
    while (! feof($File)) {
      fwrite($Target, fread($File, 102400));
    }
  }
  finally {
    fclose($File);
  }
}

$SrcDir = realpath(dirname(__FILE__) . "/../src");
$DistDir = realpath(dirname(__FILE__) . "/../dist/one_src_file");
$TargetFile = "$DistDir/phosphor7.php";



$Target = fopen($TargetFile, "w");
try {
  fputs($Target,'<?php define("PHOSPHOR7_EXTERNAL_REQUIRE_MANAGEMENT", 1); ?>');

  addSrc("$SrcDir/p7_global.php", $Target);
  addSrc("$SrcDir/p7_ctypes.php", $Target);
  addSrc("$SrcDir/p7_errno.php", $Target);
  addSrc("$SrcDir/p7_netinet_in.php", $Target);
  addSrc("$SrcDir/snap_sysutils.php", $Target);
  addSrc("$SrcDir/snap_msgsock.php", $Target);
  addSrc("$SrcDir/s7_isotcp.php", $Target);
  addSrc("$SrcDir/s7_types.php", $Target);
  addSrc("$SrcDir/s7_peer.php", $Target);
  addSrc("$SrcDir/s7_micro_client.php", $Target);
  addSrc("$SrcDir/phosphor7.php", $Target);
}
finally {
  fclose($Target);
  echo "Done!" . PHP_EOL;
}

?>
