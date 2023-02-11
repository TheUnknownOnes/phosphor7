<?php

/* Phosphor7 - Project
   Web: https://github.com/chaosben/phosphor7
   License: Apache License 2.0
*/

if (! defined("PHOSPHOR7_EXTERNAL_REQUIRE_MANAGEMENT")) {
  require_once("s7_peer.php");
  require_once("p7_ctypes.php");
  require_once("s7_types.php");
  require_once("snap_sysutils.php");
}

const errCliMask                   = 0xFFF00000;
const errCliBase                   = 0x000FFFFF;

const errCliInvalidParams          = 0x00200000;
const errCliJobPending             = 0x00300000;
const errCliTooManyItems           = 0x00400000;
const errCliInvalidWordLen         = 0x00500000;
const errCliPartialDataWritten     = 0x00600000;
const errCliSizeOverPDU            = 0x00700000;
const errCliInvalidPlcAnswer       = 0x00800000;
const errCliAddressOutOfRange      = 0x00900000;
const errCliInvalidTransportSize   = 0x00A00000;
const errCliWriteDataSizeMismatch  = 0x00B00000;
const errCliItemNotAvailable       = 0x00C00000;
const errCliInvalidValue           = 0x00D00000;
const errCliCannotStartPLC         = 0x00E00000;
const errCliAlreadyRun             = 0x00F00000;
const errCliCannotStopPLC          = 0x01000000;
const errCliCannotCopyRamToRom     = 0x01100000;
const errCliCannotCompress         = 0x01200000;
const errCliAlreadyStop            = 0x01300000;
const errCliFunNotAvailable        = 0x01400000;
const errCliUploadSequenceFailed   = 0x01500000;
const errCliInvalidDataSizeRecvd   = 0x01600000;
const errCliInvalidBlockType       = 0x01700000;
const errCliInvalidBlockNumber     = 0x01800000;
const errCliInvalidBlockSize       = 0x01900000;
const errCliDownloadSequenceFailed = 0x01A00000;
const errCliInsertRefused          = 0x01B00000;
const errCliDeleteRefused          = 0x01C00000;
const errCliNeedPassword           = 0x01D00000;
const errCliInvalidPassword        = 0x01E00000;
const errCliNoPasswordToSetOrClear = 0x01F00000;
const errCliJobTimeout             = 0x02000000;
const errCliPartialDataRead        = 0x02100000;
const errCliBufferTooSmall         = 0x02200000;
const errCliFunctionRefused        = 0x02300000;
const errCliDestroying             = 0x02400000;
const errCliInvalidParamNumber     = 0x02500000;
const errCliCannotChangeParam      = 0x02600000;

const DeltaSecs = 441763200; // Seconds between 1970/1/1 (C time base) and 1984/1/1 (Siemens base)

// Read/Write Multivars
class TS7DataItem extends CStruct {
  const SIZE = CInt::SIZE * 6 + CPointer::SIZE;

  public function __construct() {
    $this->Members["Area"] = new CInt();
    $this->Members["WordLen"] = new CInt();
    $this->Members["Result"] = new CInt();
    $this->Members["DBNumber"] = new CInt();
    $this->Members["Start"] = new CInt();
    $this->Members["Amount"] = new CInt();
    $this->Members["pdata"] = new CPointer();
  }
}

class_alias("TS7DataItem", "PS7DataItem");

class TS7ResultItems extends CArray {
  const SIZE = CInt::SIZE * MaxVars;

  public function __construct() {
    parent::__construct("CInt", [MaxVars]);
  }
}

class_alias("TS7ResultItems", "PS7ResultItems");

class TS7BlocksList extends CStruct {
  const SIZE = CInt::SIZE * 7;

  public function __construct() {
    $this->Members["OBCount"] = new CInt();
    $this->Members["FBCount"] = new CInt();
    $this->Members["FCCount"] = new CInt();
    $this->Members["SFBCount"] = new CInt();
    $this->Members["SFCCount"] = new CInt();
    $this->Members["DBCount"] = new CInt();
    $this->Members["SDBCount"] = new CInt();
  }
}

class_alias("TS7BlocksList", "PS7BlocksList");

class TS7BlockInfo extends CStruct {
  const SIZE = CInt::SIZE * 10 + CChar::SIZE * 11 * 2 + CChar::SIZE * 9 * 3;

  public function __construct() {
    $this->Members["BlkType"] = new CInt();
    $this->Members["BlkNumber"] = new CInt();
    $this->Members["BlkLang"] = new CInt();
    $this->Members["BlkFlags"] = new CInt();
    $this->Members["MC7Size"] = new CInt();  // The real size in bytes
    $this->Members["LoadSize"] = new CInt();
    $this->Members["LocalData"] = new CInt();
    $this->Members["SBBLength"] = new CInt();
    $this->Members["CheckSum"] = new CInt();
    $this->Members["Version"] = new CInt();
    // Chars info
    $this->Members["CodeDate"] = new CArray("CChar", [11]);
    $this->Members["IntfDate"] = new CArray("CChar", [11]);
    $this->Members["Author"] = new CArray("CChar", [9]);
    $this->Members["Family"] = new CArray("CChar", [9]);
    $this->Members["Header"] = new CArray("CChar", [9]);
  }
}

class_alias("TS7BlockInfo", "PS7BlockInfo");

class TS7BlocksOfType extends CArray {
  const SIZE = CWord::SIZE * 0x2000;

  public function __construct() {
    parent::__construct("CWord", [0x2000]);
  }
}

class_alias("TS7BlocksOfType", "PS7BlocksOfType");

class TS7OrderCode extends CStruct {
  const SIZE = CChar::SIZE * 21 + CByte::SIZE * 3;

  public function __construct() {
    $this->Members["Code"] = new CArray("CChar", [21]); // Order Code
    $this->Members["V1"] = new CByte(); // Version V1.V2.V3
    $this->Members["V2"] = new CByte();
    $this->Members["V3"] = new CByte();
  }
}

class_alias("TS7OrderCode", "PS7OrderCode");

class TS7CpuInfo extends CStruct {
  const SIZE = CChar::SIZE * 33 + CChar::SIZE * 25 + CChar::SIZE * 25 + CChar::SIZE * 27 + CChar::SIZE * 25;

  public function __construct() {
    $this->Members["ModuleTypeName"] = new CArray("CChar", [33]);
    $this->Members["SerialNumber"] = new CArray("CChar", [25]);
    $this->Members["ASName"] = new CArray("CChar", [25]);
    $this->Members["Copyright"] = new CArray("CChar", [27]);
    $this->Members["ModuleName"] = new CArray("CChar", [25]);
  }
}

class_alias("TS7CpuInfo", "PS7CpuInfo");

class TS7CpInfo extends CStruct {
  const SIZE = CInt::SIZE * 4;

  public function __construct() {
    $this->Members["MaxPduLengt"] = new CInt();
    $this->Members["MaxConnections"] = new CInt();
    $this->Members["MaxMpiRate"] = new CInt();
    $this->Members["MaxBusRate"] = new CInt();
  }
}

class_alias("TS7CpInfo", "PS7CpInfo");

// See ยง33.1 of "System Software for S7-300/400 System and Standard Functions"
// and see SFC51 description too
class SZL_HEADER extends CStruct {
  const SIZE = CWord::SIZE * 2;

  public function __construct() {
    $this->Members["LENTHDR"] = new CWord();
    $this->Members["N_DR"] = new CWord();
  }
}

class_alias("SZL_HEADER", "PSZL_HEADER");

class TS7SZL extends CStruct {
  const SIZE = SZL_HEADER::SIZE + CByte::SIZE * (0x4000-4);

  public function __construct() {
    $this->Members["Header"] = new SZL_HEADER();
    $this->Members["Data"] = new CArray("CByte", [0x4000-4]);
  }
}

class_alias("TS7SZL", "PS7SZL");

// SZL List of available SZL IDs : same as SZL but List items are big-endian adjusted
class TS7SZLList extends CStruct {
  const SIZE = SZL_HEADER::SIZE + CWord::SIZE * (0x2000-2);

  public function __construct() {
    $this->Members["Header"] = new SZL_HEADER();
    $this->Members["List"] = new CArray("CWord", [0x2000-4]);
  }
}

class_alias("TS7SZLList", "PS7SZLList");

// See ยง33.19 of "System Software for S7-300/400 System and Standard Functions"
class TS7Protection extends CStruct {
  const SIZE = CWord::SIZE * 5;

  public function __construct() {
    $this->Members["sch_schal"] = new CWord();
    $this->Members["sch_par"] = new CWord();
    $this->Members["sch_rel"] = new CWord();
    $this->Members["bart_sch"] = new CWord();
    $this->Members["anl_sch"] = new CWord();
  }
}

class_alias("TS7Protection", "PS7Protection");

define("s7opNone", 0);
define("s7opReadArea", 1);
define("s7opWriteArea", 2);
define("s7opReadMultiVars", 3);
define("s7opWriteMultiVars", 4);
define("s7opDBGet", 5);
define("s7opUpload", 6);
define("s7opDownload", 7);
define("s7opDelete", 8);
define("s7opListBlocks", 9);
define("s7opAgBlockInfo", 10);
define("s7opListBlocksOfType", 11);
define("s7opReadSzlList", 12);
define("s7opReadSZL", 13);
define("s7opGetDateTime", 14);
define("s7opSetDateTime", 15);
define("s7opGetOrderCode", 16);
define("s7opGetCpuInfo", 17);
define("s7opGetCpInfo", 18);
define("s7opGetPlcStatus", 19);
define("s7opPlcHotStart", 20);
define("s7opPlcColdStart", 21);
define("s7opCopyRamToRom", 22);
define("s7opCompress", 23);
define("s7opPlcStop", 24);
define("s7opGetProtection", 25);
define("s7opSetPassword", 26);
define("s7opClearPassword", 27);
define("s7opDBFill", 28);

// Param Number (to use with setparam)

// Low level : change them to experiment new connections, their defaults normally work well
const pc_iso_SendTimeout   = 6;
const pc_iso_RecvTimeout   = 7;
const pc_iso_ConnTimeout   = 8;
const pc_iso_SrcRef        = 1;
const pc_iso_DstRef        = 2;
const pc_iso_SrcTSAP       = 3;
const pc_iso_DstTSAP       = 4;
const pc_iso_IsoPduSize    = 5;

// Client Connection Type
const CONNTYPE_PG         = 0x01;  // Connect to the PLC as a PG
const CONNTYPE_OP         = 0x02;  // Connect to the PLC as an OP
const CONNTYPE_BASIC      = 0x03;  // Basic connection


// Internal struct for operations
// Commands are not executed directly in the function such as "DBRead(...",
// but this struct is filled and then PerformOperation() is called.
// This allow us to implement async function very easily.

class TSnap7Job {
  public $Op = 0;         // Operation Code
  public $Result = 0;     // Operation result
  public $Pending= false; // A Job is pending
  public $Time = 0;       // Job Execution time
  // Read/Write
  public $Area = 0;       // Also used for Block type and Block of type
  public $Number = 0;     // Used for DB Number, Block number
  public $Start = 0;      // Offset start
  public $WordLen = 0;    // Word length
  // SZL
  public $ID = 0;         // SZL ID
  public $Index = 0;      // SZL Index
  // ptr info
  public $pData = 0;      // User data pointer
  public $Amount = 0;     // Items amount/Size in input
  public $pAmount = 0;    // Items amount/Size in output
  // Generic
  public $IParam = 0;     // Used for full upload and CopyRamToRom extended timeout
}

#[AllowDynamicProperties] class TSnap7MicroClient extends TSnap7Peer {
  /**
   *  @param word SiemensTime
   *  @param string PTime
   */
  private function FillTime($SiemensTime, &$PTime) {
    // SiemensTime -> number of seconds after 1/1/1984
    // This is not S7 date and time but is used only internally for block info
    $TheDate = ($SiemensTime * 86400)+ DeltaSecs;
    $timeinfo = getdate($TheDate);
    $PTime = strftime("%Y/%m/%d", $timeinfo[0]);
  }

  /**
   *  @param byte B
   *  @return byte
   */
  private function BCDtoByte($B) {
    return (($B >> 4) * 10) + ($B & 0x0F);
  }

  /**
   *  @param word Value
   *  @return byte
   */
  private function WordToBCD($Value) {
    return (intval($Value / 10) << 4) | ($Value % 10);
  }

  /**
   *  @return int
   */
  private function opReadArea() {
    $ReqParams = null; //PReqFunReadParams
    $ResParams = null; //PResFunReadParams
    $Answer = null; //PS7ResHeader23
    $ResData = null; //PResFunReadItem
    $RPSize = null; //word // ReqParams size
    $WordSize = null; //int
    $Offset = null; //uintptr_t
    $Target = ""; //original pbyte, here it buffers the incoming data
    $Address = null; //int
    $IsoSize = null; //int
    $Start = null; //int
    $MaxElements = null; //int  // Max elements that we can transfer in a PDU
    $Elements = null; //word // Num of elements that we are asking for this telegram
    $TotElements = null; //int  // Total elements requested
    $Size = null; //int
    $Result = null; //int

    $WordSize=$this->DataSizeByte($this->Job->WordLen); // The size in bytes of an element that we are asking for
    if ($WordSize==0)
      return errCliInvalidWordLen;
    // First check : params bounds
    if (($this->Job->Number<0) || ($this->Job->Number>65535) || ($this->Job->Start<0) || ($this->Job->Amount<1))
      return errCliInvalidParams;
    // Second check : transport size
    if (($this->Job->WordLen==S7WLBit) && ($this->Job->Amount>1))
      return errCliInvalidTransportSize;
    // Request Params size
    $RPSize    =TReqFunReadItem::SIZE+2; // 1 item + FunRead + ItemsCount
    // Setup pointers (note : PDUH_out and PDU.Payload are the same pointer)
    //ReqParams =PReqFunReadParams(pbyte(PDUH_out)+sizeof(TS7ReqHeader));
    $ReqParams = new PReqFunReadParams();
    //Answer    =PS7ResHeader23(&PDU.Payload);
    //ResParams =PResFunReadParams(pbyte(Answer)+ResHeaderSize23);
    //ResData   =PResFunReadItem(pbyte(ResParams)+sizeof(TResFunReadParams));
    // Each packet cannot exceed the PDU length (in bytes) negotiated, and moreover
    // we must ensure to transfer a "finite" number of item per PDU
    $MaxElements=($this->PDULength-TS7ResHeader23::SIZE-TResFunReadParams::SIZE-4) / $WordSize;
    $TotElements=$this->Job->Amount;
    $Start      =$this->Job->Start;
    $Offset     =0;
    $Result     =0;
    while (($TotElements>0) && ($Result==0)) {
      $NumElements=$TotElements;
      if ($NumElements>$MaxElements)
        $NumElements=$MaxElements;

      //Target=pbyte(Job.pData)+Offset;
      //----------------------------------------------- Read next slice-----
      $this->PDUH_out->P = 0x32;                          // Always 0x32
      $this->PDUH_out->PDUType = PduType_request;         // 0x01
      $this->PDUH_out->AB_EX = 0x0000;                    // Always 0x0000
      $this->PDUH_out->Sequence = $this->GetNextWord();   // AutoInc
      $this->PDUH_out->ParLen = $this->SwapWord($RPSize);  // 14 bytes params
      $this->PDUH_out->DataLen = 0x0000;                  // No data

      $ReqParams->FunRead = pduFuncRead;      // 0x04
      $ReqParams->ItemsCount = 1;
      $ReqParams->Items[0]->ItemHead[0] = 0x12;
      $ReqParams->Items[0]->ItemHead[1] = 0x0A;
      $ReqParams->Items[0]->ItemHead[2] = 0x10;
      $ReqParams->Items[0]->TransportSize = $this->Job->WordLen;
      $ReqParams->Items[0]->Length = $this->SwapWord($NumElements);
      $ReqParams->Items[0]->Area = $this->Job->Area;
      if ($this->Job->Area==S7AreaDB)
        $ReqParams->Items[0]->DBNumber = $this->SwapWord($this->Job->Number);
      else
        $ReqParams->Items[0]->DBNumber = 0x0000;
      // Adjusts the offset
      if (($this->Job->WordLen==S7WLBit) || ($this->Job->WordLen==S7WLCounter) || ($this->Job->WordLen==S7WLTimer))
        $Address = $Start;
      else
        $Address = $Start*8;

      $ReqParams->Items[0]->Address[2] = $Address & 0x000000FF;
      $Address = $Address >> 8;
      $ReqParams->Items[0]->Address[1] = $Address & 0x000000FF;
      $Address = $Address >> 8;
      $ReqParams->Items[0]->Address[0] = $Address & 0x000000FF;

      $IsoSize = TS7ReqHeader::SIZE+$RPSize;
      $Buffer = null;
      $Bytes = "";
      $this->PDUH_out->writeToBytes($Bytes);
      $ReqParams->writeToBytes($Bytes);
      $this->PDU->Payload->readFromBytes($Bytes);
      $Result = $this->isoExchangeBuffer($Buffer,$IsoSize);

      // Get Data
      if ($Result==0) {  // 1St level Iso
        $Bytes = $this->PDU->Payload->getAsBytes();
        $Answer = new PS7ResHeader23();
        $Answer->readFromBytes($Bytes);
        $ResParams = new PResFunReadParams();
        $ResParams->readFromBytes($Bytes, TS7ResHeader23::SIZE);
        $ResData = new PResFunReadItem();
        $ResData->readFromBytes($Bytes, TS7ResHeader23::SIZE + TResFunReadParams::SIZE);
        $Size = 0;
        // Item level error
        if ($ResData->ReturnCode==0xFF) // <-- 0xFF means Result OK
        {
          // Calcs data size in bytes
          $Size = $this->SwapWord($ResData->DataLength);
          // Adjust Size in accord of TransportSize
          if (($ResData->TransportSize != TS_ResOctet) && ($ResData->TransportSize != TS_ResReal) && ($ResData->TransportSize != TS_ResBit))
            $Size = $Size >> 3;
          $this->Job->pData .= substr($ResData->Data->getAsBytes(), 0, $Size);
        }
        else
          $Result = $this->CpuError($ResData->ReturnCode);
       $Offset+=$Size;
      }
      //--------------------------------------------------------------------
      $TotElements -= $NumElements;
      $Start += $NumElements*$WordSize;
    }

    return $Result;
  }

    private function opWriteArea()
    {
        $ReqParams = null; // PReqFunWriteParams
        $ReqData = null; // PReqFunWriteDataItem // only 1 item for WriteArea Function
        $ResParams = null;// PResFunWrite
        $Answer = null; // PS7ResHeader23
        $RPSize = null; // word // $ReqParams size
        $RHSize = null; // word // Request headers size
        $First = true; // bool  = true
        $Address = null; // int
        $IsoSize = null; // int
        $WordSize = null; // int
        $Size = null; // word
        $Offset = 0; // uintptr_t
        $Start = null; // int // where we are starting from for this telegram
        $MaxElements = null; // int // Max elements that we can transfer in a PDU
        $NumElements = null; // word // Num of elements that we are asking for this telegram
        $TotElements = null;// int // Total elements requested
        $Result = null; // int

        $WordSize = $this->DataSizeByte($this->Job->WordLen); // The size in bytes of an element that we are pushing
        if ($WordSize == 0)
            return errCliInvalidWordLen;
        // First check : params bounds
        if (($this->Job->Number < 0) || ($this->Job->Number > 65535) || ($this->Job->Start < 0) || ($this->Job->Amount < 1))
            return errCliInvalidParams;
        // Second check : transport size
        if (($this->Job->WordLen == S7WLBit) && ($this->Job->Amount > 1))
            return errCliInvalidTransportSize;

        $RHSize = TS7ReqHeader::SIZE +        // Request header
                    2 +                       // FunWrite+ItemCount (of TReqFunWriteParams)
                    TReqFunWriteItem::SIZE +  // 1 item reference
                    4;                        // ReturnCode+TransportSize+DataLength
        $RPSize = TReqFunWriteItem::SIZE + 2;

        // Setup pointers (note : PDUH_out and PDU.Payload are the same pointer)
        $ReqParams = new PReqFunWriteParams();
        $ReqData = new PReqFunWriteDataItem(); // 2 = FunWrite+ItemsCount

        // Each packet cannot exceed the PDU length (in bytes) negotiated, and moreover
        // we must ensure to transfer a "finite" number of item per PDU
        $MaxElements = ($this->PDULength - $RHSize) / $WordSize;
        $TotElements = $this->Job->Amount;
        $Start = $this->Job->Start;

        while (($TotElements > 0) && ($Result == 0)) {
            $NumElements = $TotElements;
            if ($NumElements > $MaxElements)
                $NumElements = $MaxElements;

            //$Source=pbyte($this->Job->pData)+$Offset;

            $Size = $NumElements * $WordSize;
            $this->PDUH_out->P = 0x32;                    // Always 0x32
            $this->PDUH_out->PDUType = PduType_request;   // 0x01
            $this->PDUH_out->AB_EX = 0x0000;              // Always 0x0000
            $this->PDUH_out->Sequence = $this->GetNextWord();    // AutoInc
            $this->PDUH_out->ParLen = $this->SwapWord($RPSize); // 14 bytes params
            $this->PDUH_out->DataLen = $this->SwapWord($Size + 4);

            $ReqParams->FunWrite = pduFuncWrite;    // 0x05
            $ReqParams->ItemsCount = 1;
            $ReqParams->Items[0]->ItemHead[0] = 0x12;
            $ReqParams->Items[0]->ItemHead[1] = 0x0A;
            $ReqParams->Items[0]->ItemHead[2] = 0x10;
            $ReqParams->Items[0]->TransportSize = $this->Job->WordLen;
            $ReqParams->Items[0]->Length = $this->SwapWord($NumElements);
            $ReqParams->Items[0]->Area = $this->Job->Area;
            if ($this->Job->Area == S7AreaDB)
                $ReqParams->Items[0]->DBNumber = $this->SwapWord($this->Job->Number);
            else
                $ReqParams->Items[0]->DBNumber = 0x0000;


            // Adjusts the offset
            if (($this->Job->WordLen == S7WLBit) || ($this->Job->WordLen == S7WLCounter) || ($this->Job->WordLen == S7WLTimer))
                $Address = $Start;
            else
                $Address = $Start * 8;

            $ReqParams->Items[0]->Address[2] = $Address & 0x000000FF;
            $Address = $Address >> 8;
            $ReqParams->Items[0]->Address[1] = $Address & 0x000000FF;
            $Address = $Address >> 8;
            $ReqParams->Items[0]->Address[0] = $Address & 0x000000FF;

            $ReqData->ReturnCode = 0x00;

            switch ($this->Job->WordLen) {
                case S7WLBit:
                    $ReqData->TransportSize = TS_ResBit;
                    break;
                case S7WLInt:
                case S7WLDInt:
                    $ReqData->TransportSize = TS_ResInt;
                    break;
                case S7WLReal:
                    $ReqData->TransportSize = TS_ResReal;
                    break;
                case S7WLChar   :
                case S7WLCounter:
                case S7WLTimer:
                    $ReqData->TransportSize = TS_ResOctet;
                    break;
                default:
                    $ReqData->TransportSize = TS_ResByte;
                    break;
            };

            if (($ReqData->TransportSize != TS_ResOctet) && ($ReqData->TransportSize != TS_ResReal) && ($ReqData->TransportSize != TS_ResBit))
                $ReqData->DataLength = $this->SwapWord($Size * 8);
            else
                $ReqData->DataLength = $this->SwapWord($Size);

            // This BELOW might need improvement !!
            $data = $this->Job->pData;

            $data = str_split($data,2);

            foreach ($data as $key => $byte) {
                $data[$key] = hexdec($byte);
            }
            $ReqData->Data = $data;
            // This ABOVE might need improvement !!


            $IsoSize = $RHSize + $Size;
            $Buffer = null;
            $Bytes = "";

            // preparedata
            $this->PDUH_out->writeToBytes($Bytes);
            $ReqParams->writeToBytes($Bytes);
            $ReqData->writeToBytes($Bytes, 24);

            // send data
            $this->PDU->Payload->readFromBytes($Bytes);
            $Result = $this->isoExchangeBuffer($Buffer, $IsoSize);

            // receive data
            if ($Result == 0) {  // 1St level Iso
                $Bytes = $this->PDU->Payload->getAsBytes();
                $Answer = new PS7ResHeader23();
                $Answer->readFromBytes($Bytes);
                $ResParams = new PResFunReadParams();
                $ResParams->readFromBytes($Bytes, TS7ResHeader23::SIZE);
                $ResData = new PResFunReadItem();
                $ResData->readFromBytes($Bytes, TS7ResHeader23::SIZE + TResFunReadParams::SIZE);
                $Size = 0;
                // Item level error
                if ($ResData->ReturnCode == 0xFF) { // <-- 0xFF means Result OK
                    // Calcs data size in bytes
                    $Size = $this->SwapWord($ResData->DataLength);
                    // Adjust Size in accord of TransportSize
                    if (($ResData->TransportSize != TS_ResOctet) && ($ResData->TransportSize != TS_ResReal) && ($ResData->TransportSize != TS_ResBit))
                        $Size = $Size >> 3;
                    $this->Job->pData .= substr($ResData->Data->getAsBytes(), 0, $Size);
                } else
                    $Result = $this->CpuError($ResData->ReturnCode);
                $Offset += $Size;
                $First = false;
                //--------------------------------------------------------------------
                $TotElements -= $NumElements;
                $Start += $NumElements * $WordSize;
            }
            return $Result;
        }
    }

  //TODO: private int opReadMultiVars();
  //TODO: private int opWriteMultiVars();
  //TODO: private int opListBlocks();
  //TODO: private int opListBlocksOfType();
  //TODO: private int opAgBlockInfo();
  //TODO: private int opDBGet();
  //TODO: private int opDBFill();
  //TODO: private int opUpload();
  //TODO: private int opDownload();
  //TODO: private int opDelete();
  //TODO: private int opReadSZL();
  //TODO: private int opReadSZLList();
  //TODO: private int opGetDateTime();
  //TODO: private int opSetDateTime();
  //TODO: private int opGetOrderCode();
  //TODO: private int opGetCpuInfo();
  //TODO: private int opGetCpInfo();
  //TODO: private int opGetPlcStatus();
  //TODO: private int opPlcStop();
  //TODO: private int opPlcHotStart();
  //TODO: private int opPlcColdStart();
  //TODO: private int opCopyRamToRom();
  //TODO: private int opCompress();
  //TODO: private int opGetProtection();
  //TODO: private int opSetPassword();
  //TODO: private int opClearPassword();

  /**
   *  @param int Error
   *  @return int
   */
  private function CpuError(int $Error){
    switch($Error) {
      case 0                          : return 0;
      case Code7AddressOutOfRange     : return errCliAddressOutOfRange;
      case Code7InvalidTransportSize  : return errCliInvalidTransportSize;
      case Code7WriteDataSizeMismatch : return errCliWriteDataSizeMismatch;
      case Code7ResItemNotAvailable   :
      case Code7ResItemNotAvailable1  : return errCliItemNotAvailable;
      case Code7DataOverPDU           : return errCliSizeOverPDU;
      case Code7InvalidValue          : return errCliInvalidValue;
      case Code7FunNotAvailable       : return errCliFunNotAvailable;
      case Code7NeedPassword          : return errCliNeedPassword;
      case Code7InvalidPassword       : return errCliInvalidPassword;
      case Code7NoPasswordToSet       :
      case Code7NoPasswordToClear     : return errCliNoPasswordToSetOrClear;
      default:
        return errCliFunctionRefused;
    }
  }

  //TODO: private longword DWordAt(void * P);
  //TODO: private int CheckBlock(int BlockType, int BlockNum,  void *pBlock,  int Size);
  //TODO: private int SubBlockToBlock(int SBB);
  //
  //TODO: protected word ConnectionType;
  //TODO: protected longword JobStart;
  protected $Job; //TSnap7Job

  /**
   *  @param int WordLength
   *  @return int
   */
  protected function DataSizeByte($WordLength) {
    switch ($WordLength){
      case S7WLBit     : return 1;  // S7 sends 1 byte per bit
      case S7WLByte    : return 1;
      case S7WLChar    : return 1;
      case S7WLWord    : return 2;
      case S7WLDWord   : return 4;
      case S7WLInt     : return 2;
      case S7WLDInt    : return 4;
      case S7WLReal    : return 4;
      case S7WLCounter : return 2;
      case S7WLTimer   : return 2;
      default          : return 0;
     }
  }

  //TODO: protected int opSize; // last operation size

  /**
   *  @return int
   */
  protected function PerformOperation() {
    $this->ClrError();
    $Operation=$this->Job->Op;
    switch($Operation) {
        case s7opNone:
             $this->Job->Result=errCliInvalidParams;
             break;
        case s7opReadArea:
             $this->Job->Result=$this->opReadArea();
             break;
        case s7opWriteArea:
             $this->Job->Result=$this->opWriteArea();
             break;
        case s7opReadMultiVars:
             $this->Job->Result=$this->opReadMultiVars();
             break;
        case s7opWriteMultiVars:
             $this->Job->Result=$this->opWriteMultiVars();
             break;
        case s7opDBGet:
             $this->Job->Result=$this->opDBGet();
             break;
        case s7opDBFill:
             $this->Job->Result=$this->opDBFill();
             break;
        case s7opUpload:
             $this->Job->Result=$this->opUpload();
             break;
        case s7opDownload:
             $this->Job->Result=$this->opDownload();
             break;
        case s7opDelete:
             $this->Job->Result=$this->opDelete();
             break;
        case s7opListBlocks:
             $this->Job->Result=$this->opListBlocks();
             break;
        case s7opAgBlockInfo:
             $this->Job->Result=$this->opAgBlockInfo();
             break;
        case s7opListBlocksOfType:
             $this->Job->Result=$this->opListBlocksOfType();
             break;
        case s7opReadSzlList:
             $this->Job->Result=$this->opReadSZLList();
             break;
        case s7opReadSZL:
             $this->Job->Result=$this->opReadSZL();
             break;
        case s7opGetDateTime:
             $this->Job->Result=$this->opGetDateTime();
             break;
        case s7opSetDateTime:
             $this->Job->Result=$this->opSetDateTime();
             break;
        case s7opGetOrderCode:
             $this->Job->Result=$this->opGetOrderCode();
             break;
        case s7opGetCpuInfo:
             $this->Job->Result=$this->opGetCpuInfo();
             break;
        case s7opGetCpInfo:
             $this->Job->Result=$this->opGetCpInfo();
             break;
        case s7opGetPlcStatus:
             $this->Job->Result=$this->opGetPlcStatus();
             break;
        case s7opPlcHotStart:
             $this->Job->Result=$this->opPlcHotStart();
             break;
        case s7opPlcColdStart:
             $this->Job->Result=$this->opPlcColdStart();
             break;
        case s7opCopyRamToRom:
             $this->Job->Result=$this->opCopyRamToRom();
             break;
        case s7opCompress:
             $this->Job->Result=$this->opCompress();
             break;
        case s7opPlcStop:
             $this->Job->Result=$this->opPlcStop();
             break;
        case s7opGetProtection:
             $this->Job->Result=$this->opGetProtection();
             break;
        case s7opSetPassword:
             $this->Job->Result=$this->opSetPassword();
             break;
        case s7opClearPassword:
             $this->Job->Result=$this->opClearPassword();
             break;
    }
    $this->Job->Time =SysGetTick()-$this->JobStart;
    $this->Job->Pending=false;
    return $this->SetError($this->Job->Result);
  }

  //TODO: public TS7Buffer opData;

	public function __construct() {
    parent::__construct();

    $this->Job = new TSnap7Job();

    $this->SrcRef =0x0100; // RFC0983 states that SrcRef and DetRef should be 0
			// and, in any case, they are ignored.
			// S7 instead requires a number != 0
			// Libnodave uses 0x0100
			// S7Manager uses 0x0D00
      // TIA Portal V12 uses 0x1D00
			// WinCC     uses 0x0300
			// Seems that every non zero value is good enough...
    $this->DstRef  =0x0000;
    $this->SrcTSap =0x0100;
    $this->DstTSap =0x0000; // It's filled by connection functions
    $this->ConnectionType = CONNTYPE_PG; // Default connection type
  }

  public function __destruct() {
    $this->Destroying = true;
    parent::__destruct();
  }

  /**
   *  @param bool DoReconnect
   *  @return int
   */
  public function Reset($DoReconnect) {
    $this->Job->Pending=false;
    if ($DoReconnect) {
      $this->Disconnect();
        return $this->Connect();
    }
    else
      return 0;
  }

  /**
   *  @param string RemAddress
   *  @param word LocalTSAP
   *  @param word RemoteTsap
   */
  public function SetConnectionParams($RemAddress, $LocalTSAP, $RemoteTSAP) {
    $this->SrcTSap = $LocalTSAP;
    $this->DstTSap = $RemoteTSAP;
    $this->RemoteAddress = $RemAddress;
  }

  //TODO: public void SetConnectionType(word ConnType);

	/**
	 *  @param string RemAddress
	 *  @param int Rack
	 *  @param int Slot
	 *  @return int
	 */
    public function ConnectTo($RemAddress, $Rack, $Slot) {
        $RemoteTSAP = ($this->ConnectionType<<8)+($Rack*0x20)+$Slot;
        $this->SetConnectionParams($RemAddress, $this->SrcTSap, $RemoteTSAP);
        return $this->Connect();
    }

  /**
   *  @return int
   */
  public function Connect() {
    $this->JobStart=SysGetTick();
    $Result = $this->PeerConnect();
    $this->Job->Time=SysGetTick()-$this->JobStart;
    var_dump($Result);
    return $Result;
  }

	/**
	 *  @return int
	 */
	public function Disconnect() {
    $this->JobStart=SysGetTick();
    $this->PeerDisconnect();
    $this->Job->Time=SysGetTick()-$this->JobStart;
    $this->Job->Pending=false;
    return 0;
  }

	//TODO: public int GetParam(int ParamNumber, void *pValue);
	//TODO: public int SetParam(int ParamNumber, void *pValue);

  // Fundamental Data I/O functions

  /**
   *  @param int Area
   *  @param int DBNumber
   *  @param int Start
   *  @param int Amount
   *  @param int WordLen
   *  @param CVariable pUsrData
   *  @return int
   */
  public function ReadArea($Area, $DBNumber, $Start, $Amount, $WordLen, &$pUsrData) {
    if (! $this->Job->Pending) {
      $this->Job->Pending  = true;
      $this->Job->Op       = s7opReadArea;
      $this->Job->Area     = $Area;
      $this->Job->Number   = $DBNumber;
      $this->Job->Start    = $Start;
      $this->Job->Amount   = $Amount;
      $this->Job->WordLen  = $WordLen;
      $this->Job->pData    = &$pUsrData;
      $this->JobStart     = SysGetTick();
      return $this->PerformOperation();
    }
    else
      return $this->SetError(errCliJobPending);
  }

  public function WriteArea($Area, $DBNumber, $Start, $Amount, $WordLen, &$pUsrData){
      if (! $this->Job->Pending) {
          $this->Job->Pending  = true;
          $this->Job->Op       = s7opWriteArea;
          $this->Job->Area     = $Area;
          $this->Job->Number   = $DBNumber;
          $this->Job->Start    = $Start;
          $this->Job->Amount   = $Amount;
          $this->Job->WordLen  = $WordLen;
          $this->Job->pData    = &$pUsrData;
          $this->JobStart     = SysGetTick();
          return $this->PerformOperation();
      }
      else
          return $this->SetError(errCliJobPending);
  }
  //TODO: public int ReadMultiVars(PS7DataItem Item, int ItemsCount);
  //TODO: public int WriteMultiVars(PS7DataItem Item, int ItemsCount);
  // Data I/O Helper functions

  /**
   *  @param int DBNumber
   *  @param int Start
   *  @param int Size
   *  @param CVariable pUsrData
   *  @return int
   */
  public function DBRead($DBNumber, $Start, $Size, &$pUsrData) {
    return $this->ReadArea(S7AreaDB, $DBNumber, $Start, $Size, S7WLByte, $pUsrData);
  }

    /**
     *  @param int DBNumber
     *  @param int Start
     *  @param int Size
     *  @param CVariable pUsrData
     *  @return int
     */
    public function DBWrite($DBNumber, $Start, $Size, &$pUsrData) {
        return $this->WriteArea(S7AreaDB, $DBNumber, $Start, $Size, S7WLByte, $pUsrData);
    }
	
  //TODO: public int MBRead(int Start, int Size, void * pUsrData);
  //TODO: public int MBWrite(int Start, int Size, void * pUsrData);
  //TODO: public int EBRead(int Start, int Size, void * pUsrData);
  //TODO: public int EBWrite(int Start, int Size, void * pUsrData);
  //TODO: public int ABRead(int Start, int Size, void * pUsrData);
  //TODO: public int ABWrite(int Start, int Size, void * pUsrData);
  //TODO: public int TMRead(int Start, int Amount, void * pUsrData);
  //TODO: public int TMWrite(int Start, int Amount, void * pUsrData);
  //TODO: public int CTRead(int Start, int Amount, void * pUsrData);
  //TODO: public int CTWrite(int Start, int Amount, void * pUsrData);
  // Directory functions
  //TODO: public int ListBlocks(PS7BlocksList pUsrData);
  //TODO: public int GetAgBlockInfo(int BlockType, int BlockNum, PS7BlockInfo pUsrData);
  //TODO: public int GetPgBlockInfo(void * pBlock, PS7BlockInfo pUsrData, int Size);
  //TODO: public int ListBlocksOfType(int BlockType, TS7BlocksOfType *pUsrData, int & ItemsCount);
  // Blocks functions
  //TODO: public int Upload(int BlockType, int BlockNum, void * pUsrData, int & Size);
  //TODO: public int FullUpload(int BlockType, int BlockNum, void * pUsrData, int & Size);
  //TODO: public int Download(int BlockNum, void * pUsrData, int Size);
  //TODO: public int Delete(int BlockType, int BlockNum);
  //TODO: public int DBGet(int DBNumber, void * pUsrData, int & Size);
  //TODO: public int DBFill(int DBNumber, int FillChar);
  // Date/Time functions
  //TODO: public int GetPlcDateTime(tm &DateTime);
  //TODO: public int SetPlcDateTime(tm * DateTime);
  //TODO: public int SetPlcSystemDateTime();
  // System Info functions
  //TODO: public int GetOrderCode(PS7OrderCode pUsrData);
  //TODO: public int GetCpuInfo(PS7CpuInfo pUsrData);
  //TODO: public int GetCpInfo(PS7CpInfo pUsrData);
  //TODO: public int ReadSZL(int ID, int Index, PS7SZL pUsrData, int &Size);
  //TODO: public int ReadSZLList(PS7SZLList pUsrData, int &ItemsCount);
  // Control functions
  //TODO: public int PlcHotStart();
  //TODO: public int PlcColdStart();
  //TODO: public int PlcStop();
  //TODO: public int CopyRamToRom(int Timeout);
  //TODO: public int Compress(int Timeout);
  //TODO: public int GetPlcStatus(int &Status);
  // Security functions
  //TODO: public int GetProtection(PS7Protection pUsrData);
  //TODO: public int SetSessionPassword(char *Password);
  //TODO: public int ClearSessionPassword();
  // Properties
  //TODO: public bool Busy(){ return Job.Pending; };
  //TODO: public int Time(){ return int(Job.Time);}
}

class_alias("TSnap7MicroClient", "PSnap7MicroClient");

?>
