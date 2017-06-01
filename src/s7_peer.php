<?php

/* Phosphor7 - Project
   Web: https://github.com/chaosben/phosphor7
   License: Apache License 2.0
*/

if (! defined("PHOSPHOR7_EXTERNAL_REQUIRE_MANAGEMENT")) {
  require_once("s7_types.php");
  require_once("s7_isotcp.php");
}

const errPeerMask 	    = 0xFFF00000;
const errPeerBase       = 0x000FFFFF;
const errNegotiatingPDU = 0x00100000;

class TSnap7Peer extends TIsoTcpSocket {
  private $cntword; //word

  protected $Destroying; //bool
  protected $PDUH_out; //PS7ReqHeader

  /**
   *  @return word
   */
  protected function GetNextWord() {
    if ($this->cntword==0xFFFF)
        $this->cntword=0;
     return $this->cntword++;
  }

  /**
   *  @param int Error
   *  @return int
   */
  protected function SetError($Error) {
    if ($Error==0)
       $this->ClrError();
    else
       $this->LastError=$Error | $this->LastIsoError | $this->LastTcpError;
    return $Error;
  }

  /**
   *  @return int
   */
  protected function NegotiatePDULength() {
    $Result = $IsoSize = 0; //int
    $ReqNegotiate = null; //PReqFunNegotiateParams
    $ResNegotiate = null; //PResFunNegotiateParams
    $Answer = null; //PS7ResHeader23
    $this->ClrError();
    // Setup Pointers
    //ReqNegotiate = PReqFunNegotiateParams(pbyte(PDUH_out) + sizeof(TS7ReqHeader));
    $ReqNegotiate = new PReqFunNegotiateParams();
    // Header
    $this->PDUH_out->P        = 0x32;            // Always $32
    $this->PDUH_out->PDUType  = PduType_request; // $01
    $this->PDUH_out->AB_EX    = 0x0000;          // Always $0000
    $this->PDUH_out->Sequence = $this->GetNextWord();   // AutoInc
    $this->PDUH_out->ParLen   = $this->SwapWord(TReqFunNegotiateParams::SIZE); // 8 bytes
    $this->PDUH_out->DataLen  = 0x0000;
    // Params
    $ReqNegotiate->FunNegotiate = pduNegotiate;
    $ReqNegotiate->Unknown = 0x00;
    $ReqNegotiate->ParallelJobs_1 = 0x0100;
    $ReqNegotiate->ParallelJobs_2 = 0x0100;
    $ReqNegotiate->PDULength = $this->SwapWord($this->PDURequest);
    $IsoSize = TS7ReqHeader::SIZE + TReqFunNegotiateParams::SIZE;

    $Bytes = "";
    $this->PDUH_out->writeToBytes($Bytes);
    $ReqNegotiate->writeToBytes($Bytes);
    $this->PDU->Payload->readFromBytes($Bytes);

    $Buffer = null;
    $Result = $this->isoExchangeBuffer($Buffer, $IsoSize);
    if (($Result == 0) && ($IsoSize == TS7ResHeader23::SIZE + TResFunNegotiateParams::SIZE)) {
      // Setup pointers
      $Bytes = $this->PDU->Payload->getAsBytes();
      $Answer = new PS7ResHeader23();
      $Answer->readFromBytes($Bytes);
      $ResNegotiate = new PResFunNegotiateParams();
      $ResNegotiate->readFromBytes($Bytes, TS7ResHeader23::SIZE);
      if ( $Answer->Error != 0 )
        $Result = $this->SetError(errNegotiatingPDU);
      if ( $Result == 0 )
        $this->PDULength = $this->SwapWord($ResNegotiate->PDULength);
    }
    return $Result;
  }


  protected function ClrError() {
    $this->LastError=0;
    $this->LastIsoError=0;
    $this->LastTcpError=0;
  }

  public $LastError; //int
  public $PDULength; //int
  public $PDURequest; //int

  public function __construct() {
    parent::__construct();

    $this->PDUH_out= new PS7ReqHeader(); //(&PDU.Payload);
    $this->PDURequest=480; // Our request, FPDULength will contain the CPU answer
    $this->LastError=0;
    $this->cntword=0;
    $this->Destroying = false;
  }

  public function __destruct() {
    $this->Destroying = true;

    parent::__destruct();
  }

  public function PeerDisconnect() {
    $this->ClrError();
    $this->isoDisconnect(true);
  }

  /**
   *  @return int
   */
  public function PeerConnect() {
    $Result = null; //int

    $this->ClrError();
    $Result = $this->isoConnect();
    if ($Result == 0) {
      $Result = $this->NegotiatePDULength();
      if ($Result != 0)
        $this->PeerDisconnect();
    }
    return $Result;
  }
}


?>
