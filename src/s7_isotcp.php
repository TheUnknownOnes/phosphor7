<?php

/* Phosphor7 - Project
   Web: https://github.com/chaosben/phosphor7
   License: Apache License 2.0
*/

if (! defined("PHOSPHOR7_EXTERNAL_REQUIRE_MANAGEMENT")) {
  require_once("p7_ctypes.php");
  require_once("snap_msgsock.php");
}

define("isoTcpVersion",    	3);      // RFC 1006
define("isoTcpPort",    		102);    // RFC 1006
define("isoInvalidHandle",  0);
define("MaxTSAPLength",    	16);     // Max Lenght for Src and Dst TSAP
define("MaxIsoFragments",   64);     // Max fragments
define("IsoPayload_Size", 	4096);   // Iso telegram Buffer size

define("noError",    			0);

const errIsoMask               = 0x000F0000;
const errIsoBase               = 0x0000FFFF;

const errIsoConnect            = 0x00010000; // Connection error
const errIsoDisconnect         = 0x00020000; // Disconnect error
const errIsoInvalidPDU         = 0x00030000; // Bad format
const errIsoInvalidDataSize    = 0x00040000; // Bad Datasize passed to send/recv : buffer is invalid
const errIsoNullPointer    	= 0x00050000; // Null passed as pointer
const errIsoShortPacket    	= 0x00060000; // A short packet received
const errIsoTooManyFragments   = 0x00070000; // Too many packets without EoT flag
const errIsoPduOverflow    	= 0x00080000; // The sum of fragments data exceded maximum packet size
const errIsoSendPacket         = 0x00090000; // An error occurred during send
const errIsoRecvPacket         = 0x000A0000; // An error occurred during recv
const errIsoInvalidParams    	= 0x000B0000; // Invalid TSAP params
const errIsoResvd_1    	    = 0x000C0000; // Unassigned
const errIsoResvd_2    	    = 0x000D0000; // Unassigned
const errIsoResvd_3    	    = 0x000E0000; // Unassigned
const errIsoResvd_4    	    = 0x000F0000; // Unassigned

const ISO_OPT_TCP_NODELAY   	= 0x00000001; // Disable Nagle algorithm
const ISO_OPT_INSIDE_MTU    	= 0x00000002; // Max packet size < MTU ethernet card

// TPKT Header - ISO on TCP - RFC 1006 (4 bytes)
class TTPKT extends CStruct {
  const SIZE = CUChar::SIZE * 4;

  public function __construct() {
    $this->Members["Version"] = new CUChar();   // Always 3 for RFC 1006
    $this->Members["Reserved"] = new CUChar();  // 0
    $this->Members["HI_Lenght"] = new CUChar(); // High part of packet lenght (entire frame, payload and TPDU included)
    $this->Members["LO_Lenght"] = new CUChar(); // Low part of packet lenght (entire frame, payload and TPDU included)
  }
}                // Packet length : min 7 max 65535


class TCOPT_Params extends CStruct {
  const SIZE = CUChar::SIZE * 3 + CUChar::SIZE * 254;

  public function __construct() {
    $this->Members["PduSizeCode"] = new CUChar();
    $this->Members["PduSizeLen"] = new CUChar();
    $this->Members["PduSizeVal"] = new CUChar();
    $this->Members["TSAP"] = new CArray("CUChar", [245]); // We don't know in advance these fields....
  }
}

// PDU Type constants - ISO 8073, not all are mentioned in RFC 1006
// For our purposes we use only those labeled with **
// These constants contains 4 low bit order 0 (credit nibble)
//
//     $10 ED : Expedited Data
//     $20 EA : Expedited Data Ack
//     $40 UD : CLTP UD
//     $50 RJ : Reject
//     $70 AK : Ack data
// **  $80 DR : Disconnect request (note : S7 doesn't use it)
// **  $C0 DC : Disconnect confirm (note : S7 doesn't use it)
// **  $D0 CC : Connection confirm
// **  $E0 CR : Connection request
// **  $F0 DT : Data
//

// COTP Header for CONNECTION REQUEST/CONFIRM - DISCONNECT REQUEST/CONFIRM
class TCOTP_CO extends CStruct {
  const SIZE = CUChar::SIZE * 2 + CUShort::SIZE * 2 + CUChar::SIZE + TCOPT_Params::SIZE;

  public function __construct() {
    $this->Members["HLength"] = new CUChar(); // Header length : initialized to 6 (length without params - 1)
               // descending classes that add values in params field must update it.
    $this->Members["PDUType"] = new CUChar(); // 0xE0 Connection request
               // 0xD0 Connection confirm
               // 0x80 Disconnect request
               // 0xDC Disconnect confirm
    $this->Members["DstRef"] = new CUShort(); // Destination reference : Always 0x0000
    $this->Members["SrcRef"] = new CUShort(); // Source reference : Always 0x0000
    $this->Members["CO_R"] = new CUChar();  // If the telegram is used for Connection request/Confirm,
               // the meaning of this field is CLASS+OPTION :
               //   Class (High 4 bits) + Option (Low 4 bits)
               //   Class : Always 4 (0100) but is ignored in input (RFC States this)
               //   Option : Always 0, also this in ignored.
               // If the telegram is used for Disconnect request,
               // the meaning of this field is REASON :
               //    1     Congestion at TSAP
               //    2     Session entity not attached to TSAP
               //    3     Address unknown (at TCP connect time)
               //  128+0   Normal disconnect initiated by the session
               //          entity.
               //  128+1   Remote transport entity congestion at connect
               //          request time
               //  128+3   Connection negotiation failed
               //  128+5   Protocol Error
               //  128+8   Connection request refused on this network
               //          connection
    // Parameter data : depending on the protocol implementation.
    // ISO 8073 define several type of parameters, but RFC 1006 recognizes only
    // TSAP related parameters and PDU size.  See RFC 0983 for more details.
    $this->Members["Params"] = new TCOPT_Params();
    /* Other params not used here, list only for completeness
      ACK_TIME     	   = 0x85,  1000 0101 Acknowledge Time
      RES_ERROR    	   = 0x86,  1000 0110 Residual Error Rate
      PRIORITY           = 0x87,  1000 0111 Priority
      TRANSIT_DEL  	   = 0x88,  1000 1000 Transit Delay
      THROUGHPUT   	   = 0x89,  1000 1001 Throughput
      SEQ_NR       	   = 0x8A,  1000 1010 Subsequence Number (in AK)
      REASSIGNMENT 	   = 0x8B,  1000 1011 Reassignment Time
      FLOW_CNTL    	   = 0x8C,  1000 1100 Flow Control Confirmation (in AK)
      TPDU_SIZE    	   = 0xC0,  1100 0000 TPDU Size
      SRC_TSAP     	   = 0xC1,  1100 0001 TSAP-ID / calling TSAP ( in CR/CC )
      DST_TSAP     	   = 0xC2,  1100 0010 TSAP-ID / called TSAP
      CHECKSUM     	   = 0xC3,  1100 0011 Checksum
      VERSION_NR   	   = 0xC4,  1100 0100 Version Number
      PROTECTION   	   = 0xC5,  1100 0101 Protection Parameters (user defined)
      OPT_SEL            = 0xC6,  1100 0110 Additional Option Selection
      PROTO_CLASS  	   = 0xC7,  1100 0111 Alternative Protocol Classes
      PREF_MAX_TPDU_SIZE = 0xF0,  1111 0000
      INACTIVITY_TIMER   = 0xF2,  1111 0010
      ADDICC             = 0xe0   1110 0000 Additional Information on Connection Clearing
    */
  }
}
class_alias("TCOTP_CO", "PCOTP_CO");

// COTP Header for DATA EXCHANGE
class TCOTP_DT extends CStruct {
  const SIZE = CUChar::SIZE * 3;

  public function __construct() {
    $this->Members["HLength"] = new CUChar(); // Header length : 3 for this header
    $this->Members["PDUType"] = new CUChar(); // 0xF0 for this header
    $this->Members["EoT_Num"] = new CUChar(); // EOT (bit 7) + PDU Number (bits 0..6)
                // EOT = 1 -> End of Trasmission Packet (This packet is complete)
          // PDU Number : Always 0
  }
}

class_alias("TCOTP_DT", "PCOTP_DT");

// Info part of a PDU, only common parts. We use it to check the consistence
// of a telegram regardless of it's nature (control or data).
class TIsoHeaderInfo extends CStruct {
  const SIZE = TTPKT::SIZE + CUChar::SIZE * 2;

  public function __construct() {
    $this->Members["TPKT"] = new TTPKT(); // TPKT Header
        // Common part of any COTP
    $this->Members["HLength"] = new CUChar(); // Header length : 3 for this header
    $this->Members["PDUType"] = new CUChar(); // Pdu type
  }
}
class_alias("TIsoHeaderInfo", "PIsoHeaderInfo");

// PDU Type consts (Code + Credit)
const pdu_type_CR    	= 0xE0;  // Connection request
const pdu_type_CC    	= 0xD0;  // Connection confirm
const pdu_type_DR    	= 0x80;  // Disconnect request
const pdu_type_DC    	= 0xC0;  // Disconnect confirm
const pdu_type_DT    	= 0xF0;  // Data transfer

const pdu_EoT    		= 0x80;  // End of Trasmission Packet (This packet is complete)

const DataHeaderSize  = TTPKT::SIZE+TCOTP_DT::SIZE;
const IsoFrameSize    = IsoPayload_Size+DataHeaderSize;

class TIsoControlPDU extends CStruct {
  const SIZE = TTPKT::SIZE + TCOTP_CO::SIZE;

  public function __construct() {
    $this->Members["TPKT"] = new TTPKT();     // TPKT Header
    $this->Members["COTP"] = new TCOTP_CO();  // COPT Header for CONNECTION stuffs
  }
}
class_alias("TIsoControlPDU", "PIsoControlPDU");

class TIsoPayload extends CArray {
  const SIZE = CUChar::SIZE * IsoPayload_Size;

  public function __construct() {
    parent::__construct("CUChar", [IsoPayload_Size]);
  }
}

class TIsoDataPDU extends CStruct {
  const SIZE = TTPKT::SIZE + TCOTP_DT::SIZE + TIsoPayload::SIZE;

  public function __construct() {
    $this->Members["TPKT"] = new TTPKT();           // TPKT Header
    $this->Members["COTP"] = new TCOTP_DT();        // COPT Header for DATA EXCHANGE
    $this->Members["Payload"] = new TIsoPayload();  // Payload
  }
}

class_alias("TIsoDataPDU", "PIsoDataPDU");
class_alias("TIsoPayload", "PIsoPayload");

abstract class TPDUKind {
  const pkConnectionRequest = 0;
  const pkDisconnectRequest = 1;
  const pkEmptyFragment = 2;
  const pkInvalidPDU = 3;
  const pkUnrecognizedType = 4;
  const pkValidData = 5;
}

function ErrIsoText($Error, $Msg) {

}

class TIsoTcpSocket extends TMsgSocket {

  private $FControlPDU; //TIsoControlPDU
  private $IsoMaxFragments; //int // max fragments allowed for an ISO telegram

	/**
	 *  Checks the PDU format
	 *  @param mixed pPDU
	 *  @param u_char PduTypeExpected
	 *  @return int
	 */
	private function CheckPDU($pPDU, $PduTypeExpected) {
    $Info = null; //PIsoHeaderInfo
    $Size = null; //int
    $this->ClrIsoError();
    if (! is_null($pPDU)) {
      $Info = PIsoHeaderInfo::cast($pPDU);
      $Size = $this->PDUSize($pPDU);
      // Performs check
      if (( $Size<7 ) || ( $Size>IsoPayload_Size ) ||  // Checks RFC 1006 header length
        ($Info->HLength<TCOTP_DT::SIZE-1 ) ||  // Checks ISO 8073 header length
        ( $Info->PDUType!=$PduTypeExpected))         // Checks PDU Type
          return $this->SetIsoError(errIsoInvalidPDU);
      else
        return noError;
    }
    else
      return $this->SetIsoError(errIsoNullPointer);
  }

	/**
	 *  Receives the next fragment
	 *  @param mixed From
	 *  @param int Max
	 *  @param int Size
	 *  @param bool EoT
	 *  @return int
	 */
	private function isoRecvFragment(&$From, $Max, &$Size, &$EoT) {
    $DataLength = null; //int

    $Size =0;
    $EoT =false;
    $PDUType = null; //byte
    $this->ClrIsoError();
    // header is received always from beginning
    $Bytes = "";
    $this->RecvPacket($Bytes, DataHeaderSize); // TPKT + COPT_DT
    if ($this->LastTcpError==0) {
      $this->PDU->readFromBytes($Bytes);
      $PDUType=$this->PDU->COTP->PDUType;
      switch ($PDUType) {
        case pdu_type_CR:
        case pdu_type_DR:
          $EoT=true;
          break;
        case pdu_type_DT:
          $EoT = ($this->PDU->COTP->EoT_Num & 0x80) == 0x80;  // EoT flag
          break;
        default:
          return $this->SetIsoError(errIsoInvalidPDU);
      }

      $DataLength = $this->PDUSize($this->PDU) - DataHeaderSize;
      if ($this->CheckPDU($this->PDU, $PDUType)!=0)
        return $this->LastIsoError;
      // Checks for data presence
      if ($DataLength>0) { // payload present
        // Check if the data fits in the buffer
        if($DataLength<=$Max) {
          $this->RecvPacket($From, $DataLength);
          if ($this->LastTcpError!=0)
            return $this->SetIsoError(errIsoRecvPacket);
          else
            $Size =$DataLength;
        }
        else
          return $this->SetIsoError(errIsoPduOverflow);
      }
    }
    else
      return $this->SetIsoError(errIsoRecvPacket);

    return $this->LastIsoError;
  }

	protected $PDU; //TIsoDataPDU

	/**
	 *  @param int $Error
	 *  @return int
	 */
	protected function SetIsoError($Error) {
    $this->LastIsoError = $Error | $this->LastTcpError;
    return $this->LastIsoError;
  }

	/**
	 *  Builds the control PDU starting from address properties
	 *  @return int
	 */
	protected function BuildControlPDU() {
    $ParLen = $IsoLen = null; //int

    $this->ClrIsoError();
    $this->FControlPDU->COTP->Params->PduSizeCode=0xC0; // code that identifies TPDU size
    $this->FControlPDU->COTP->Params->PduSizeLen =0x01; // 1 byte this field
    switch($this->IsoPDUSize)
    {
      case 128:
        $this->FControlPDU->COTP->Params->PduSizeVal =0x07;
        break;
      case 256:
        $this->FControlPDU->COTP->Params->PduSizeVal =0x08;
        break;
      case 512:
        $this->FControlPDU->COTP->Params->PduSizeVal =0x09;
        break;
      case 1024:
        $this->FControlPDU->COTP->Params->PduSizeVal =0x0A;
        break;
      case 2048:
        $this->FControlPDU->COTP->Params->PduSizeVal =0x0B;
        break;
      case 4096:
        $this->FControlPDU->COTP->Params->PduSizeVal =0x0C;
        break;
      case 8192:
        $this->FControlPDU->COTP->Params->PduSizeVal =0x0D;
        break;
      default:
        $this->FControlPDU->COTP->Params->PduSizeVal =0x0B;  // Our Default
    };
    // Build TSAPs
    $this->FControlPDU->COTP->Params->TSAP[0]=0xC1;   // code that identifies source TSAP
    $this->FControlPDU->COTP->Params->TSAP[1]=2;      // source TSAP Len
    $this->FControlPDU->COTP->Params->TSAP[2]=($this->SrcTSap>>8) & 0xFF; // HI part
    $this->FControlPDU->COTP->Params->TSAP[3]=$this->SrcTSap & 0xFF; // LO part

    $this->FControlPDU->COTP->Params->TSAP[4]=0xC2; // code that identifies dest TSAP
    $this->FControlPDU->COTP->Params->TSAP[5]=2;    // dest TSAP Len
    $this->FControlPDU->COTP->Params->TSAP[6]=($this->DstTSap>>8) & 0xFF; // HI part
    $this->FControlPDU->COTP->Params->TSAP[7]=$this->DstTSap & 0xFF; // LO part

    // Params length
    $ParLen=11; // 2 Src TSAP (Code+field Len)      +
                // 2 Src TSAP len                   +
                // 2 Dst TSAP (Code+field Len)      +
                // 2 Src TSAP len                   +
                // 3 PDU size (Code+field Len+Val)  = 11
    // Telegram length
    $IsoLen=TTPKT::SIZE+ // TPKT Header
            7 +          // COTP Header Size without params
            $ParLen;     // COTP params

    $this->FControlPDU->TPKT->Version  =isoTcpVersion;
    $this->FControlPDU->TPKT->Reserved =0;
    $this->FControlPDU->TPKT->HI_Lenght=0;  // Connection Telegram size cannot exced 255 bytes, so
                                            // this field is always 0
    $this->FControlPDU->TPKT->LO_Lenght=$IsoLen;

    $this->FControlPDU->COTP->HLength  =$ParLen + 6;    // <-- 6 = 7 - 1 (COTP Header size - 1)
    $this->FControlPDU->COTP->PDUType  =pdu_type_CR;    // Connection Request
    $this->FControlPDU->COTP->DstRef   =$this->DstRef;  // Destination reference
    $this->FControlPDU->COTP->SrcRef   =$this->SrcRef;  // Source reference
    $this->FControlPDU->COTP->CO_R     =0x00;           // Class + Option : RFC0983 states that it must be always 0x40
                                                        // but for some equipment (S7) must be 0 in disaccord of specifications !!!
    return noError;
  }

	/**
	 *  Calcs the PDU size
	 *  @param mixed pPDU
	 *  @return int
	 */
	protected function PDUSize($pPDU) {
    $Info = PIsoHeaderInfo::cast($pPDU);
    return $Info->TPKT->HI_Lenght*256+$Info->TPKT->LO_Lenght;
  }

	/**
	 * Parses the connection request PDU to extract TSAP and PDU size info
	 *  @param TIsoControlPDU PDU
	 */
	protected function IsoParsePDU(TIsoControlPDU $PDU) {
    // Currently we accept a connection with any kind of src/dst tsap
    // Override to implement special filters.
  }

	/**
	 *  Confirms the connection, override this method for special pourpose
	 *  By default it checks the PDU format and resend it changing the pdu type
	 *  @param u_char PDUType
	 *  @return int
	 */
	protected function IsoConfirmConnection($PDUType) {
    $CPDU = PIsoControlPDU::cast($this->PDU);
    $TempRef = null; //u_short

    $this->ClrIsoError();
    $this->PDU->COTP->PDUType=$PDUType;
    // Exchange SrcRef<->DstRef, not strictly needed by COTP 8073 but S7PLC as client needs it.
    $TempRef=$CPDU->COTP->DstRef;
    $CPDU->COTP->DstRef=$CPDU->COTP->SrcRef;
    $CPDU->COTP->SrcRef=0x0100;//TempRef;

    return SendPacket($this->PDU->getAsBytes(),PDUSize($this->PDU));
  }

  protected function ClrIsoError() {
    $this->LastIsoError=0;
    $this->LastTcpError=0;
  }

	/**
	 *  @param int $Size
	 */
	protected function FragmentSkipped($Size) {
    // override for log purpose
  }

	public $SrcTSap;   //word  // Source TSAP
	public $DstTSap;    //word  // Destination TSAP
	public $SrcRef;     //word  // Source Reference
	public $DstRef;     //word  // Destination Reference
	public $IsoPDUSize;    //int
	public $LastIsoError;  //int

	//--------------------------------------------------------------------------

	public function __construct() {
    parent::__construct();

    $this->FControlPDU = new TIsoControlPDU();
    $this->PDU = new TIsoDataPDU();

    $this->RecvTimeout = 3000; // Some old equipments are a bit slow to answer....
    $this->RemotePort  = isoTcpPort;
    // These fields should be $0000 and in any case RFC says that they are not considered.
    // But some equipment...need a non zero value for the source reference.
    $this->DstRef = 0x0000;
    $this->SrcRef = 0x0100;
    // PDU size requested
    $this->IsoPDUSize =1024;
    $this->IsoMaxFragments=MaxIsoFragments;
    $this->LastIsoError=0;
  }

	public function __destruct() {
    parent::__destruct();
  }

	// HIGH Level functions (work on payload hiding the underlying protocol)
	/**
	 *  Connects with a peer, the connection PDU is automatically built starting from address scheme (see below)
	 *  @return int
	 */
	public function isoConnect() {
    $TmpControlPDU = null; //pbyte
    $ControlPDU = null; //PIsoControlPDU
    $Length = null; //u_int
    $Result = null; //int

    // Build the default connection telegram
    $this->BuildControlPDU();
    $ControlPDU = $this->FControlPDU;

    // Checks the format
    $Result =$this->CheckPDU($ControlPDU, pdu_type_CR);
    if ($Result!=0)
      return $Result;

    $Result =$this->SckConnect();
    if ($Result==noError)
    {
      // Calcs the length
      $Length =$this->PDUSize($ControlPDU);
      // Send connection telegram
      $this->SendPacket($ControlPDU->getAsBytes(), $Length);
      if ($this->LastTcpError==0)
      {
        $TmpControlPDU = $ControlPDU;
        // Receives TPKT header (4 bytes)
        $BufferHeader = "";
        $this->RecvPacket($BufferHeader, TTPKT::SIZE);
        if ($this->LastTcpError==0)
        {
          $TmpControlPDU->readFromBytes($BufferHeader);
          // Calc the packet length
          $Length =$this->PDUSize($TmpControlPDU);
          // Check if it fits in the buffer and if it's greater then TTPKT size
          if (($Length<=TIsoControlPDU::SIZE) && ($Length>TTPKT::SIZE)) {
            // Points to COTP
            //TmpControlPDU+=sizeof(TTPKT);
            $Length -= TTPKT::SIZE;
            // Receives remainin bytes 4 bytes after
            $BufferBody = "";
            $this->RecvPacket($BufferBody, $Length);
            if ($this->LastTcpError==0) {
              $TmpControlPDU->readFromBytes($BufferHeader . $BufferBody);
              // Finally checks the Connection Confirm telegram
              $Result =$this->CheckPDU($this->FControlPDU, pdu_type_CC);
              if ($Result!=0)
                $this->LastIsoError=$Result;
            }
            else
              $Result =$this->SetIsoError(errIsoRecvPacket);
          }
          else
            $Result =$this->SetIsoError(errIsoInvalidPDU);
        }
        else
          $Result =$this->SetIsoError(errIsoRecvPacket);
        // Flush buffer
        if ($Result!=0)
          $this->Purge();
      }
      else
        $Result =$this->SetIsoError(errIsoSendPacket);

      if ($Result!=0)
        $this->SckDisconnect();
    }
    return $Result;
  }

	/**
	 *  Disconnects from a peer, if OnlyTCP = true, only a TCP disconnect is performed,
	 *  otherwise a disconnect PDU is built and send.
	 *  @param bool $OnlyTCP
	 *  @return int
	 */
	public function isoDisconnect($OnlyTCP) {
    $Result = null; //int

    $this->ClrIsoError();
    if ($this->Connected)
      $this->Purge(); // Flush pending
    $this->LastIsoError=0;
    // OnlyTCP true -> Disconnect Request telegram is not required : only TCP disconnection
    if (! $OnlyTCP)
    {
      // if we are connected -> we have a valid connection telegram
      if ($this->Connected)
        $this->FControlPDU->COTP->PDUType =pdu_type_DR;
      // Checks the format
      $Result =$this->CheckPDU($this->FControlPDU, pdu_type_DR);
      if ($Result!=0)
        return $Result;
      // Sends Disconnect request
      $this->SendPacket($this->FControlPDU->getAsBytes(), $this->PDUSize($this->FControlPDU));
      if ($this->LastTcpError!=0)
      {
        $Result =$this->SetIsoError(errIsoSendPacket);
        return $Result;
      }
    }
    // TCP disconnect
    $this->SckDisconnect();
    if ($this->LastTcpError!=0)
      $Result =$this->SetIsoError(errIsoDisconnect);
    else
      $Result =0;

    return $Result;
  }

  /**
   *  Sends a buffer a valid header is created
   *  @param mixed Data
   *  @param int Size
   *  @return int
   */
  public function isoSendBuffer($Data, $Size) {
    $Result = null; //int
    $IsoSize = null; //u_int

    $this->ClrIsoError();
    // Total Size = Size + Header Size
    $IsoSize =$Size+DataHeaderSize;
    // Checks the length
    if (($IsoSize>0) && ($IsoSize<=IsoFrameSize)) {
      // Builds the header
      $Result =0;
      // TPKT
      $this->PDU->TPKT->Version  = isoTcpVersion;
      $this->PDU->TPKT->Reserved = 0;
      $this->PDU->TPKT->HI_Lenght= ($IsoSize>> 8) & 0xFF;
      $this->PDU->TPKT->LO_Lenght= $IsoSize & 0xFF;
      // COPT
      $this->PDU->COTP->HLength   =TCOTP_DT::SIZE-1;
      $this->PDU->COTP->PDUType   =pdu_type_DT;
      $this->PDU->COTP->EoT_Num   =pdu_EoT;
      // Fill payload
      if (! is_null($Data)) // Data=null ==> use internal buffer PDU.Payload
        $this->PDU->Payload->readFromBytes($Data);
      // Send over TCP/IP
      $this->SendPacket($this->PDU->getAsBytes(), $IsoSize);

      if ($this->LastTcpError!=0)
        $Result =$this->SetIsoError(errIsoSendPacket);
    }
    else
      $Result =$this->SetIsoError(errIsoInvalidDataSize );
    return $Result;
  }

  /**
   *  Receives a buffer
   *  @param mixed Data
   *  @param int Size
   *  @return int
   */
  public function isoRecvBuffer(&$Data, &$Size) {
    $Result = null; //int

    $this->ClrIsoError();
    $Size =0;
    $Result =$this->isoRecvPDU($this->PDU);
    if ($Result==0) {
      $Size =$this->PDUSize($this->PDU)-DataHeaderSize;
      if (! is_null($Data))  // Data=NULL ==> a child will consume directly PDU.Payload
        $Data = $this->PDU->Payload->getAsBytes();
    }
    return $Result;
  }

  /**
   *  Exchange cycle send->receive
   *  @param mixed Data
   *  @param int Size
   *  @return int
   */
  public function isoExchangeBuffer(&$Data, &$Size) {
    $Result = null; //int

    $this->ClrIsoError();
    $Result =$this->isoSendBuffer($Data, $Size);
    if ($Result==0)
      $Result =$this->isoRecvBuffer($Data, $Size);
    return $Result;
  }

	/**
	 *  A PDU is ready (at least its header) to be read
	 *  @return bool
	 */
	public function IsoPDUReady() {
    $this->ClrIsoError();
	  return $this->PacketReady(TCOTP_DT::SIZE);
  }

	/**
	 *  Same as isoSendBuffer, but the entire PDU has to be provided (in any case a check is performed)
	 *  @param PIsoDataPDU Data
	 *  @return int
	 */
	public function isoSendPDU($Data) {
    $Result = null; //int

    $this->ClrIsoError();
    $Result=$this->CheckPDU($Data,pdu_type_DT);
    if ($Result==0)
    {
      $this->SendPacket($Data->getAsBytes(),$this->PDUSize($Data));
      if ($this->LastTcpError!=0)
        $Result=$this->SetIsoError(errIsoSendPacket);
    }
      return $Result;
  }

  //---------------------------------------------------------------------------
  // Fragments Recv schema
  //------------------------------------------------------------------------------
  //
  //         packet 1                 packet 2                 packet 3
  // +--------+------------+  +--------+------------+  +--------+------------+
  // | HEADER | FRAGMENT 1 |  | HEADER | FRAGMENT 2 |  | HEADER | FRAGMENT 3 |
  // +--------+------------+  +--------+------------+  +--------+------------+
  //                |                         |                        |
  //                |             +-----------+                        |
  //                |             |                                    |
  //                |             |           +------------------------+
  //                |             |           |      (Packet 3 has EoT Flag set)
  //                V             V           V
  // +--------+------------+------------+------------+
  // | HEADER | FRAGMENT 1 : FRAGMENT 2 : FRAGMENT 3 |
  // +--------+------------+------------+------------+
  //     ^
  //     |
  //     +-- A new header is built with updated info
  //
  //------------------------------------------------------------------------------
  /**
   *  Same as isoRecvBuffer, but it returns the entire PDU, automatically enques the fragments
   *  @param PIsoDataPDU Data
   *  @return int
   */
  public function isoRecvPDU($Data) {
    $Result = null; //int
    $Size = null; //int
    $pData = null; //pbyte
    $max = null; //int
    $Offset = null; //int
    $Received = null; //int
    $NumParts = null; //int
    $Complete = null; //bool

    $NumParts =1;
    $Offset =0;
    $Complete =false;
    $this->ClrIsoError();
    $pData = "";
    do {
      $max =IsoPayload_Size-$Offset; // Maximum packet allowed
      if ($max>0) {
        $Buffer = "";
        $Result =$this->isoRecvFragment($Buffer, $max, $Received, $Complete);
        if ($Result == 0)
          $pData .= $Buffer;
        if(($Result==0) &&  (!$Complete))
        {
          ++$NumParts;
          $Offset += $Received;
          if ($NumParts>$this->IsoMaxFragments)
            $Result =$this->SetIsoError(errIsoTooManyFragments);
        }
      }
      else
        $Result =$this->SetIsoError(errIsoTooManyFragments);
    } while ((!$Complete) && ($Result==0));


    if ($Result==0)
    {
      $this->PDU->Payload->readFromBytes($pData);
      // Add to offset the header size
      $Size =$Offset+$Received+DataHeaderSize;
      // Adjust header
      $this->PDU->TPKT->HI_Lenght =($Size>>8) & 0xFF;
      $this->PDU->TPKT->LO_Lenght =$Size & 0xFF;
      // Copies data if target is not the local PDU
      if ($Data!==$this->PDU)
        memcpy($Data, $this->PDU, $Size);
    }
    else
      if ($this->LastTcpError!=WSAECONNRESET)
        $this->Purge();
    return $Result;
  }

	/**
	 *  Same as isoExchangeBuffer, but the entire PDU has to be provided (in any case a check is performed)
	 *  @param PIsoDataPDU Data
	 *  @return int
	 */
	public function isoExchangePDU($Data) {
    $Result = null;
    $this->ClrIsoError();
    $Result=$this->isoSendPDU($Data);
    if ($Result==0)
      $Result=$this->isoRecvPDU($Data);
    return $Result;
  }

	/**
	 *  Peeks an header info to know which kind of telegram is incoming
	 *  @param mixed pPDU
	 *  @param TPDUKind PduKind
	 */
	public function IsoPeek($pPDU, &$PduKind) {
    $Info = null; //PIsoHeaderInfo
    $IsoLen = null; //u_int

    $Info=PIsoHeaderInfo::cast($pPDU);
    $IsoLen=$this->PDUSize($Info);

    // Check for empty fragment : size of PDU = size of header and nothing else
    if ($IsoLen==DataHeaderSize ) {
        // We don't need to check the EoT flag since the PDU is empty....
        $PduKind=TPDUKind::pkEmptyFragment;
        return;
    }
    // Check for invalid packet : size of PDU < size of header
    if ($IsoLen<DataHeaderSize ) {
        $PduKind=TPDUKind::pkInvalidPDU;
        return;
    }
    // Here IsoLen>DataHeaderSize : check the PDUType
    switch ($Info->PDUType) {
        case pdu_type_CR:
            $PduKind=TPDUKind::pkConnectionRequest;
            break;
        case pdu_type_DR:
            $PduKind=TPDUKind::pkDisconnectRequest;
            break;
        case pdu_type_DT:
            $PduKind=TPDUKind::pkValidData;
            break;
        default:
            $PduKind=TPDUKind::pkUnrecognizedType;
    }
  }
}

?>
