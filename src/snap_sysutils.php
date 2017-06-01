<?php

/* Phosphor7 - Project
   Web: https://github.com/chaosben/phosphor7
   License: Apache License 2.0
*/

//---------------------------------------------------------------------------
function SysGetTick() {
  return (int)(microtime(true) / 1000.0);
}
//---------------------------------------------------------------------------
function SysSleep($Delay_ms) {
	usleep((int)($Delay_ms * 1000.0));
}
//---------------------------------------------------------------------------
function DeltaTime(&$Elapsed) {
    $TheTime=SysGetTick();
    // Checks for rollover
    if ($TheTime < $Elapsed)
        $Elapsed=0;
    return $TheTime-$Elapsed;
}

?>
