<?php

/* Phosphor7 - Project
   Web: https://github.com/chaosben/phosphor7
   License: Apache License 2.0
*/

if (! defined("PHOSPHOR7_EXTERNAL_REQUIRE_MANAGEMENT")) {
  require_once("p7_ctypes.php");
  require_once("s7_isotcp.php");
}

  // Area ID
const S7AreaPE   =	0x81;
const S7AreaPA   =	0x82;
const S7AreaMK   =	0x83;
const S7AreaDB   =	0x84;
const S7AreaCT   =	0x1C;
const S7AreaTM   =	0x1D;

const MaxVars     = 20;

const S7WLBit     = 0x01;
const S7WLByte    = 0x02;
const S7WLChar    = 0x03;
const S7WLWord    = 0x04;
const S7WLInt     = 0x05;
const S7WLDWord   = 0x06;
const S7WLDInt    = 0x07;
const S7WLReal    = 0x08;
const S7WLCounter = 0x1C;
const S7WLTimer   = 0x1D;

  // Block type
const Block_OB   = 0x38;
const Block_DB   = 0x41;
const Block_SDB  = 0x42;
const Block_FC   = 0x43;
const Block_SFC  = 0x44;
const Block_FB   = 0x45;
const Block_SFB  = 0x46;

  // Sub Block Type
const SubBlk_OB  = 0x08;
const SubBlk_DB  = 0x0A;
const SubBlk_SDB = 0x0B;
const SubBlk_FC  = 0x0C;
const SubBlk_SFC = 0x0D;
const SubBlk_FB  = 0x0E;
const SubBlk_SFB = 0x0F;

  // Block languages
const BlockLangAWL       = 0x01;
const BlockLangKOP       = 0x02;
const BlockLangFUP       = 0x03;
const BlockLangSCL       = 0x04;
const BlockLangDB        = 0x05;
const BlockLangGRAPH     = 0x06;

  // CPU status
const S7CpuStatusUnknown = 0x00;
const S7CpuStatusRun     = 0x08;
const S7CpuStatusStop    = 0x04;

const evcSnap7Base           = 0x00008000;
// S7 Server Event Code
const evcPDUincoming  	     = 0x00010000;
const evcDataRead            = 0x00020000;
const evcDataWrite    	     = 0x00040000;
const evcNegotiatePDU        = 0x00080000;
const evcReadSZL             = 0x00100000;
const evcClock               = 0x00200000;
const evcUpload              = 0x00400000;
const evcDownload            = 0x00800000;
const evcDirectory           = 0x01000000;
const evcSecurity            = 0x02000000;
const evcControl             = 0x04000000;
const evcReserved_08000000   = 0x08000000;
const evcReserved_10000000   = 0x10000000;
const evcReserved_20000000   = 0x20000000;
const evcReserved_40000000   = 0x40000000;
const evcReserved_80000000   = 0x80000000;
// Event SubCodes
const evsUnknown                 = 0x0000;
const evsStartUpload             = 0x0001;
const evsStartDownload           = 0x0001;
const evsGetBlockList            = 0x0001;
const evsStartListBoT            = 0x0002;
const evsListBoT                 = 0x0003;
const evsGetBlockInfo            = 0x0004;
const evsGetClock                = 0x0001;
const evsSetClock                = 0x0002;
const evsSetPassword             = 0x0001;
const evsClrPassword             = 0x0002;
// Event Result
const evrNoError                 = 0;
const evrFragmentRejected        = 0x0001;
const evrMalformedPDU            = 0x0002;
const evrSparseBytes             = 0x0003;
const evrCannotHandlePDU         = 0x0004;
const evrNotImplemented          = 0x0005;
const evrErrException            = 0x0006;
const evrErrAreaNotFound         = 0x0007;
const evrErrOutOfRange           = 0x0008;
const evrErrOverPDU              = 0x0009;
const evrErrTransportSize        = 0x000A;
const evrInvalidGroupUData       = 0x000B;
const evrInvalidSZL              = 0x000C;
const evrDataSizeMismatch        = 0x000D;
const evrCannotUpload            = 0x000E;
const evrCannotDownload          = 0x000F;
const evrUploadInvalidID         = 0x0010;
const evrResNotFound             = 0x0011;

  // Async mode
const amPolling   = 0;
const amEvent     = 1;
const amCallBack  = 2;

//------------------------------------------------------------------------------
//                                  PARAMS LIST
// Notes for Local/Remote Port
//   If the local port for a server and remote port for a client is != 102 they
//   will be *no more compatible with S7 IsoTCP*
//   A good reason to change them could be inside a debug session under Unix.
//   Increasing the port over 1024 avoids the need of be root.
//   Obviously you need to work with the couple Snap7Client/Snap7Server and change
//   both, or, use iptable and nat the port.
//------------------------------------------------------------------------------
const p_u16_LocalPort  	    = 1;
const p_u16_RemotePort 	    = 2;
const p_i32_PingTimeout	    = 3;
const p_i32_SendTimeout     = 4;
const p_i32_RecvTimeout     = 5;
const p_i32_WorkInterval    = 6;
const p_u16_SrcRef          = 7;
const p_u16_DstRef          = 8;
const p_u16_SrcTSap         = 9;
const p_i32_PDURequest      = 10;
const p_i32_MaxClients      = 11;
const p_i32_BSendTimeout    = 12;
const p_i32_BRecvTimeout    = 13;
const p_u32_RecoveryTime    = 14;
const p_u32_KeepAliveTime   = 15;

// Bool param is passed as int32_t : 0->false, 1->true
// String param (only set) is passed as pointer
/*
typedef int16_t   *Pint16_t;
typedef uint16_t  *Puint16_t;
typedef int32_t   *Pint32_t;
typedef uint32_t  *Puint32_t;
typedef int64_t   *Pint64_t;
typedef uint64_t  *Puint64_t;
typedef uintptr_t *Puintptr_t;*/

//-----------------------------------------------------------------------------
//                               INTERNALS CONSTANTS
//------------------------------------------------------------------------------

const DBMaxName = 0xFFFF; // max number (name) of DB

const errS7Mask         = 0xFFF00000;
const errS7Base         = 0x000FFFFF;
const errS7notConnected = errS7Base+0x0001; // Client not connected
const errS7InvalidMode  = errS7Base+0x0002; // Requested a connection to...
const errS7InvalidPDUin = errS7Base+0x0003; // Malformed input PDU

// S7 outcoming Error code
const Code7Ok                      = 0x0000;
const Code7AddressOutOfRange       = 0x0005;
const Code7InvalidTransportSize    = 0x0006;
const Code7WriteDataSizeMismatch   = 0x0007;
const Code7ResItemNotAvailable   	= 0x000A;
const Code7ResItemNotAvailable1    = 0xD209;
const Code7InvalidValue   	        = 0xDC01;
const Code7NeedPassword            = 0xD241;
const Code7InvalidPassword         = 0xD602;
const Code7NoPasswordToClear   	= 0xD604;
const Code7NoPasswordToSet         = 0xD605;
const Code7FunNotAvailable         = 0x8104;
const Code7DataOverPDU             = 0x8500;

// Result transport size
const TS_ResBit   = 0x03;
const TS_ResByte  = 0x04;
const TS_ResInt   = 0x05;
const TS_ResReal  = 0x07;
const TS_ResOctet = 0x09;

// Client Job status (lib internals, not S7)
const JobComplete  = 0;
const JobPending   = 1;

// Control codes
const CodeControlUnknown   = 0;
const CodeControlColdStart = 1;      // Cold start
const CodeControlWarmStart = 2;      // Warm start
const CodeControlStop      = 3;      // Stop
const CodeControlCompress  = 4;      // Compress
const CodeControlCpyRamRom = 5;      // Copy Ram to Rom
const CodeControlInsDel    = 6;      // Insert in working ram the block downloaded
					  // Delete from working ram the block selected
// PDU Type
const PduType_request      = 1;      // family request
const PduType_response     = 3;      // family response
const PduType_userdata     = 7;      // family user data

// PDU Functions
const pduResponse    	= 0x02;   // Response (when error)
const pduFuncRead    	= 0x04;   // Read area
const pduFuncWrite   	= 0x05;   // Write area
const pduNegotiate   	= 0xF0;   // Negotiate PDU length
const pduStart         = 0x28;   // CPU start
const pduStop          = 0x29;   // CPU stop
const pduStartUpload   = 0x1D;   // Start Upload
const pduUpload        = 0x1E;   // Upload
const pduEndUpload     = 0x1F;   // EndUpload
const pduReqDownload   = 0x1A;   // Start Download request
const pduDownload      = 0x1B;   // Download request
const pduDownloadEnded = 0x1C;   // Download end request
const pduControl   	= 0x28;   // Control (insert/delete..)

// PDU SubFunctions
const SFun_ListAll   	= 0x01;   // List all blocks
const SFun_ListBoT   	= 0x02;   // List Blocks of type
const SFun_BlkInfo   	= 0x03;   // Get Block info
const SFun_ReadSZL   	= 0x01;   // Read SZL
const SFun_ReadClock   = 0x01;   // Read Clock (Date and Time)
const SFun_SetClock  	= 0x02;   // Set Clock (Date and Time)
const SFun_EnterPwd    = 0x01;   // Enter password    for this session
const SFun_CancelPwd   = 0x02;   // Cancel password    for this session
const SFun_Insert   	= 0x50;   // Insert block
const SFun_Delete   	= 0x42;   // Delete block

class_alias("tm", "PTimeStruct");

//==============================================================================
//                                   HEADERS
//==============================================================================
#pragma pack(1)

// Tag Struct
class TS7Tag extends CStruct {
  const SIZE = CInt::SIZE * 5;

  public function __construct() {
    $this->Members["Area"] = new CInt();
    $this->Members["DBNumber"] = new CInt();
    $this->Members["Start"] = new CInt();
    $this->Members["Size"] = new CInt();
    $this->Members["WordLen"] = new CInt();

  }
}
class_alias("TS7Tag", "PS7Tag");

// Incoming header, it will be mapped onto IsoPDU payload
class TS7ReqHeader extends CStruct {
  const SIZE = CByte::SIZE * 2 + CWord::SIZE * 4;

  public function __construct() {
    $this->Members["P"] = new CByte();       // Telegram ID, always 32
    $this->Members["PDUType"] = new CByte(); // Header type 1 or 7
    $this->Members["AB_EX"] = new CWord();   // AB currently unknown, maybe it can be used for long numbers.
    $this->Members["Sequence"] = new CWord();// Message ID. This can be used to make sure a received answer
    $this->Members["ParLen"] = new CWord();  // Length of parameters which follow this header
    $this->Members["DataLen"] = new CWord(); // Length of data which follow the parameters
  }
}

class_alias("TS7ReqHeader", "PS7ReqHeader");

// Outcoming 12 bytes header , response for Request type 1
class TS7ResHeader23 extends CStruct{
  const SIZE = CByte::SIZE * 2 + CWord::SIZE * 5;

  public function __construct() {
    $this->Members["P"] = new CByte();       // Telegram ID, always 32
    $this->Members["PDUType"] = new CByte(); // Header type 2 or 3
    $this->Members["AB_EX"] = new CWord();   // AB currently unknown, maybe it can be used for long numbers.
    $this->Members["Sequence"] = new CWord();// Message ID. This can be used to make sure a received answer
    $this->Members["ParLen"] = new CWord();  // Length of parameters which follow this header
    $this->Members["DataLen"] = new CWord(); // Length of data which follow the parameters
    $this->Members["Error"] = new CWord();   // Error code
  }
}

class_alias("TS7ResHeader23", "PS7ResHeader23");

// Outcoming 10 bytes header , response for Request type 7
class TS7ResHeader17 extends CStruct {
  const SIZE = CByte::SIZE * 2 + CWord::SIZE * 4;

  public function __construct() {
    $this->Members["P"] = new CByte();       // Telegram ID, always 32
    $this->Members["PDUType"] = new CByte(); // Header type 1 or 7
    $this->Members["AB_EX"] = new CWord();   // AB currently unknown, maybe it can be used for long numbers.
    $this->Members["Sequence"] = new CWord();// Message ID. This can be used to make sure a received answer
    $this->Members["ParLen"] = new CWord();  // Length of parameters which follow this header
    $this->Members["DataLen"] = new CWord(); // Length of data which follow the parameters
  }
}

class_alias("TS7ResHeader17", "PS7ResHeader17");

// Outcoming 10 bytes header , response for Request type 8 (server control)
class TS7ResHeader8 extends CStruct {
  const SIZE = CByte::SIZE * 2 + CWord::SIZE * 4;

  public function __construct() {
    $this->Members["P"] = new CByte();       // Telegram ID, always 32
    $this->Members["PDUType"] = new CByte(); // Header type 8
    $this->Members["AB_EX"] = new CWord();   // Zero
    $this->Members["Sequence"] = new CWord();// Message ID. This can be used to make sure a received answer
    $this->Members["DataLen"] = new CWord(); // Length of data which follow this header
    $this->Members["Error"] = new CWord();   // Error code
  }
}

class_alias("TS7ResHeader8", "PS7ResHeader8");

// Outcoming answer buffer header type 2 or header type 3
class TS7Answer23 extends CStruct {
  const SIZE = TS7ResHeader23::SIZE + CByte::SIZE * (IsoPayload_Size - TS7ResHeader23::SIZE);

  public function __construct() {
    $this->Members["Header"] = new TS7ResHeader23();
    $this->Members["ResData"] = new CArray("CByte", [IsoPayload_Size - TS7ResHeader23::SIZE]);
  }
};

class_alias("TS7Answer23", "PS7Answer23");

// Outcoming buffer header type 1 or header type 7
class TS7Answer17 extends CStruct {
  const SIZE = TS7ResHeader17::SIZE + CByte::SIZE * (IsoPayload_Size - TS7ResHeader17::SIZE);

  public function __construct() {
    $this->Members["Header"] = new TS7ResHeader17();
    $this->Members["ResData"] = new CArray("CByte", [IsoPayload_Size - TS7ResHeader17::SIZE]);
  }
};

class_alias("TS7Answer17", "PS7Answer17");

class TTimeBuffer extends CArray {
  const SIZE = CByte::SIZE * 9;

  public function __construct() {
    parent::__construct("CByte", [8]);
  }
}

class_alias("TTimeBuffer", "PTimeBuffer");

class TS7Time extends CStruct {
  const SIZE = CByte::SIZE * 8;

  public function __construct() {
    $this->Members["bcd_year"] = new CByte();
    $this->Members["bcd_mon"] = new CByte();
    $this->Members["bcd_day"] = new CByte();
    $this->Members["bcd_hour"] = new CByte();
    $this->Members["bcd_min"] = new CByte();
    $this->Members["bcd_sec"] = new CByte();
    $this->Members["bcd_himsec"] = new CByte();
    $this->Members["bcd_dow"] = new CByte();
  }
}

class_alias("TS7Time", "PS7Time");

class TS7Buffer extends CArray {
  const SIZE = CByte::SIZE * 65536;

  public function __construct() {
    parent::__construct("CByte", [65536]);
  }
}

class_alias("TS7Buffer", "PS7Buffer");

const ReqHeaderSize   = TS7ReqHeader::SIZE;
const ResHeaderSize23 = TS7ResHeader23::SIZE;
const ResHeaderSize17 = TS7ResHeader17::SIZE;

// Most used request type parameters record
class TReqFunTypedParams extends CStruct {
  const SIZE = CByte::SIZE * 3 + CByte::SIZE * 5;

  public function __construct() {
    $this->Members["Head"] = new CArray("CByte", [3]);  // 0x00 0x01 0x12
    $this->Members["Plen"] = new CByte();              // par len 0x04
    $this->Members["Uk"] = new CByte();                // unknown
    $this->Members["Tg"] = new CByte();                // type and group  (4 bits type and 4 bits group)
    $this->Members["SubFun"] = new CByte();            // subfunction
    $this->Members["Seq"] = new CByte();               // sequence
  }
}

//==============================================================================
//                            FUNCTION NEGOTIATE
//==============================================================================
class TReqFunNegotiateParams extends CStruct {
  const SIZE = CByte::SIZE * 2 + CWord::SIZE * 3;

  public function __construct() {
    $this->Members["FunNegotiate"] = new CByte();
    $this->Members["Unknown"] = new CByte();
    $this->Members["ParallelJobs_1"] = new CWord();
    $this->Members["ParallelJobs_2"] = new CWord();
    $this->Members["PDULength"] = new CWord();
  }
}

class_alias("TReqFunNegotiateParams", "PReqFunNegotiateParams");

class TResFunNegotiateParams extends CStruct {
  const SIZE = CByte::SIZE * 2 + CWord::SIZE * 3;

  public function __construct() {
    $this->Members["FunNegotiate"] = new CByte();
    $this->Members["Unknown"] = new CByte();
    $this->Members["ParallelJobs_1"] = new CWord();
    $this->Members["ParallelJobs_2"] = new CWord();
    $this->Members["PDULength"] = new CWord();
  }
}

class_alias("TResFunNegotiateParams", "PResFunNegotiateParams");

//==============================================================================
//                               FUNCTION READ
//==============================================================================
class TReqFunReadItem extends CStruct {
  const SIZE = CByte::SIZE * 3 + CByte::SIZE + CWord::SIZE * 2 + CByte::SIZE + CByte::SIZE * 3;

  public function __construct() {
    $this->Members["ItemHead"] = new CArray("CByte", [3]);
    $this->Members["TransportSize"] = new CByte();
    $this->Members["Length"] = new CWord();
    $this->Members["DBNumber"] = new CWord();
    $this->Members["Area"] = new CByte();
    $this->Members["Address"] = new CArray("CByte", [3]);
  }
}

class_alias("TReqFunReadItem", "PReqFunReadItem");

//typedef TReqFunReadItem;

class TReqFunReadParams extends CStruct {
  const SIZE = CByte::SIZE * 2 + TReqFunReadItem::SIZE * MaxVars;

  public function __construct() {
    $this->Members["FunRead"] = new CByte();
    $this->Members["ItemsCount"] = new CByte();
    $this->Members["Items"] = new CArray("TReqFunReadItem", [MaxVars]);
  }
}

class_alias("TReqFunReadParams", "PReqFunReadParams");

class TResFunReadParams extends CStruct {
  const SIZE = CByte::SIZE * 2;

  public function __construct() {
    $this->Members["FunRead"] = new CByte();
    $this->Members["ItemCount"] = new CByte();
  }
}

class_alias("TResFunReadParams", "PResFunReadParams");

class TResFunReadItem extends CStruct {
  const SIZE = CByte::SIZE * 2 + CWord::SIZE + CByte::SIZE * (IsoPayload_Size - 17);

  public function __construct() {
    $this->Members["ReturnCode"] = new CByte();
    $this->Members["TransportSize"] = new CByte();
    $this->Members["DataLength"] = new CWord();
    $this->Members["Data"] = new CArray("CByte", [IsoPayload_Size - 17]); // 17 = header + params + data header - 1
  }
}

class_alias("TResFunReadItem", "PResFunReadItem");

class TResFunReadData extends CArray {
  public function __construct() {
    parent::__construct("PResFunReadItem", [MaxVars]);
  }
}

//==============================================================================
//                               FUNCTION WRITE
//==============================================================================
class TReqFunWriteItem extends CStruct {
  const SIZE = CByte::SIZE * 3 + CByte::SIZE + CWord::SIZE * 2 + CByte::SIZE + CByte::SIZE * 3;

  public function __construct() {
    $this->Members["ItemHead"] = new CArray("CByte", [3]);
    $this->Members["TransportSize"] = new CByte();
    $this->Members["Length"] = new CWord();
    $this->Members["DBNumber"] = new CWord();
    $this->Members["Area"] = new CByte();
    $this->Members["Address"] = new CArray("CByte", [3]);
  }
}

class_alias("TReqFunWriteItem", "PReqFunWriteItem");

class TReqFunWriteParams extends CStruct {
  const SIZE = CByte::SIZE * 2 + TReqFunWriteItem::SIZE * MaxVars;

  public function __construct() {
    $this->Members["FunWrite"] = new CByte();
    $this->Members["ItemsCount"] = new CByte();
    $this->Members["Items"] = new CArray("TReqFunWriteItem", [MaxVars]);
  }
}

class_alias("TReqFunWriteParams", "PReqFunWriteParams");

class TReqFunWriteDataItem extends CStruct {
  const SIZE = CByte::SIZE * 2 + CWord::SIZE + CByte::SIZE * (IsoPayload_Size - 17);

  public function __construct() {
    $this->Members["ReturnCode"] = new CByte();
    $this->Members["TransportSize"] = new CByte();
    $this->Members["DataLength"] = new CWord();
    $this->Members["Data"] = new CArray("CByte", [IsoPayload_Size - 17]); // 17 = header + params + data header -1
  }
}

class_alias("TReqFunWriteDataItem", "PReqFunWriteDataItem");

class TReqFunWriteData extends CArray {
  public function __construct() {
    parent::__construct("PReqFunWriteDataItem", [MaxVars]);
  }
}

class TResFunWrite extends CStruct {
  const SIZE = CByte::SIZE * 2 + CByte::SIZE * MaxVars;

  public function __construct() {
    $this->Members["FunWrite"] = new CByte();
    $this->Members["ItemCount"] = new CByte();
    $this->Members["Data"] = new CArray("CByte", [MaxVars]);
  }
}

class_alias("TResFunWrite", "PResFunWrite");

//==============================================================================
//                                 GROUP UPLOAD
//==============================================================================
class TReqFunStartUploadParams extends CStruct {
  const SIZE = CByte::SIZE + CByte::SIZE * 6 + CByte::SIZE * 5 + CByte::SIZE * 5 + CByte::SIZE;

  public function __construct() {
    $this->Members["FunSUpld"] = new CByte();   // function start upload 0x1D
    $this->Members["Uk6"] = new CArray("CByte", [6]);     // Unknown 6 bytes
    $this->Members["Upload_ID"] = new CByte();
    $this->Members["Len_1"] = new CByte();
    $this->Members["Prefix"] = new CByte();
    $this->Members["BlkPrfx"] = new CByte();     // always 0x30
    $this->Members["BlkType"] = new CByte();
    $this->Members["AsciiBlk"] = new CArray("CByte", [5]); // BlockNum in ascii
    $this->Members["A"] = new CByte();   // always 0x41 ('A')
  }
}

class_alias("TReqFunStartUploadParams", "PReqFunStartUploadParams");

class TResFunStartUploadParams extends CStruct {
  const SIZE = CByte::SIZE + CByte::SIZE * 6 + CByte::SIZE + CByte::SIZE * 3 + CByte::SIZE * 5;

  public function __construct() {
    $this->Members["FunSUpld"] = new CByte(); // function start upload 0x1D
    $this->Members["Data_1"] = new CArray("CByte", [6]);
    $this->Members["Upload_ID"] = new CByte();
    $this->Members["Uk"] = new CArray("CByte", [3]);
    $this->Members["LenLoad"] = new CArray("CByte", [5]);
  }
}

class_alias("TResFunStartUploadParams", "PResFunStartUploadParams");

class TReqFunUploadParams extends CStruct {
  const SIZE = CByte::SIZE + CByte::SIZE * 6 + CByte::SIZE;

  public function __construct() {
    $this->Members["FunSUpld"] = new CByte(); // function upload 0x1E
    $this->Members["Uk6"] = new CArray("CByte",[6]);   // Unknown 6 bytes
    $this->Members["Upload_ID"] = new CByte();
  }
}

class_alias("TReqFunUploadParams", "PReqFunUploadParams");

class TResFunUploadParams extends CStruct {
  const SIZE = CByte::SIZE * 2;

  public function __construct() {
    $this->Members["FunUpld"] = new CByte();  // function upload 0x1E
    $this->Members["EoU"] = new CByte();      // 0 = End Of Upload, 1 = Upload in progress
  }
}

class_alias("TResFunUploadParams", "PResFunUploadParams");

class TResFunUploadDataHeaderFirst extends CStruct {
  const SIZE = CWord::SIZE + CByte::SIZE * 2 + CWord::SIZE + CByte::SIZE * 4 + CWord::SIZE + CUInt::SIZE * 3 +
               CByte::SIZE + CUInt::SIZE + CWord::SIZE * 5;

  public function __construct() {
    $this->Members["Length"] = new CWord();   // Payload length - 4
    $this->Members["Uk_00"] = new CByte();    // Unknown 0x00
    $this->Members["Uk_FB"] = new CByte();    // Unknown 0xFB
    // from here is the same of TS7CompactBlockInfo
    $this->Members["Cst_pp"] = new CWord();
    $this->Members["Uk_01"] = new CByte();    // Unknown 0x01
    $this->Members["BlkFlags"] = new CByte();
    $this->Members["BlkLang"] = new CByte();
    $this->Members["SubBlkType"] = new CByte();
    $this->Members["BlkNum"] = new CWord();
    $this->Members["LenLoadMem"] = new CUInt();
    $this->Members["BlkSec"] = new CUInt();
    $this->Members["CodeTime_ms"] = new CUInt();
    $this->Members["CodeTime_dy"] = new CByte();
    $this->Members["IntfTime_ms"] = new CUInt();
    $this->Members["IntfTime_dy"] = new CWord();
    $this->Members["SbbLen"] = new CWord();
    $this->Members["AddLen"] = new CWord();
    $this->Members["LocDataLen"] = new CWord();
    $this->Members["MC7Len"] = new CWord();
  }
}

class_alias("TResFunUploadDataHeaderFirst", "PResFunUploadDataHeaderFirst");

class TResFunUploadDataHeaderNext extends CStruct {
  const SIZE = CWord::SIZE + CByte::SIZE * 2;

  public function __construct() {
    $this->Members["Length"] = new CWord(); // Payload length - 4
    $this->Members["Uk_00"] = new CByte();  // Unknown 0x00
    $this->Members["Uk_FB"] = new CByte();  // Unknown 0xFB
  }
}

class_alias("TResFunUploadDataHeaderNext", "PResFunUploadDataHeaderNext");

class TResFunUploadDataHeader extends CStruct {
  const SIZE = CWord::SIZE + CByte::SIZE * 2;

  public function __construct() {
    $this->Members["Length"] = new CWord(); // Payload length - 4
    $this->Members["Uk_00"] = new CByte();  // Unknown 0x00
    $this->Members["Uk_FB"] = new CByte();  // Unknown 0xFB
  }
}

class_alias("TResFunUploadDataHeader", "PResFunUploadDataHeader");

class TArrayUpldFooter extends CStruct {
  const SIZE = CByte::SIZE + CWord::SIZE + CByte::SIZE * 10 + CWord::SIZE * 2 + CByte::SIZE * 3 +
               CChar::SIZE * 8 * 3 + CByte::SIZE * 2 + CWord::SIZE + CByte::SIZE * 8;

  public function __construct() {
    $this->Members["ID"] = new CByte();   // 0x65
    $this->Members["Seq"] = new CWord();  // Sequence
    $this->Members["Const_1"] = new CArray("CByte", [10]);
    $this->Members["Lo_bound"] = new CWord();
    $this->Members["Hi_Bound"] = new CWord();
    $this->Members["u_shortLen"] = new CByte(); // 0x02 byte
           // 0x04 word
           // 0x05 int
           // 0x06 dword
           // 0x07 dint
           // 0x08 real
    $this->Members["c1"] = new CByte();
    $this->Members["c2"] = new CByte();
    $this->Members["Author"] = new CArray("CChar", [8]);
    $this->Members["Family"] = new CArray("CChar", [8]);
    $this->Members["Header"] = new CArray("CChar", [8]);
    $this->Members["B1"] = new CByte(); // 0x11
    $this->Members["B2"] = new CByte(); // 0x00
    $this->Members["Chksum"] = new CWord();
    $this->Members["Uk_8"] = new CArray("CByte", [8]);
  }
}

class_alias("TArrayUpldFooter", "PArrayUpldFooter");

class TReqFunEndUploadParams extends CStruct {
  const SIZE = CByte::SIZE + CByte::SIZE * 6 + CByte::SIZE;

  public function __construct() {
    $this->Members["FunEUpld"] = new CByte();         // function end upload 0x1F
    $this->Members["Uk6"] = new CArray("CByte", [6]); // Unknown 6 bytes
    $this->Members["Upload_ID"] = new CByte();
  }
}

class_alias("TReqFunEndUploadParams", "PReqFunEndUploadParams");

class TResFunEndUploadParams extends CStruct {
  const SIZE = CByte::SIZE;

  public function __construct() {
    $this->Members["FunEUpld"] = new CByte(); // function end upload 0x1F
  }
}

class_alias("TResFunEndUploadParams", "PResFunEndUploadParams");

//==============================================================================
//                               GROUP DOWNLOAD
//==============================================================================
class TReqStartDownloadParams extends CStruct {
  const SIZE = CByte::SIZE + CByte::SIZE * 6 + CByte::SIZE * 5 + CByte::SIZE * 5 + CByte::SIZE * 3 +
               CByte::SIZE * 6 * 2;

  public function __construct() {
    $this->Members["FunSDwnld"] = new CByte();              // function start Download 0x1A
    $this->Members["Uk6"] = new CArray("CByte", [6]);       // Unknown 6 bytes
    $this->Members["Dwnld_ID"] = new CByte();
    $this->Members["Len_1"] = new CByte();                  // 0x09
    $this->Members["Prefix"] = new CByte();                 // 0x5F
    $this->Members["BlkPrfx"] = new CByte();                // always 0x30
    $this->Members["BlkType"] = new CByte();
    $this->Members["AsciiBlk"] = new CArray("CByte", [5]);  // BlockNum in ascii
    $this->Members["P"] = new CByte();                      // 0x50 ('P')
    $this->Members["Len_2"] = new CByte();                  // 0x0D
    $this->Members["Uk1"] = new CByte();                    // 0x01
    $this->Members["AsciiLoad"] = new CArray("CByte", [6]); // load memory size (MC7 size + 92)
    $this->Members["AsciiMC7"] = new CArray("CByte", [6]);  // Block size in bytes
  }
}

class_alias("TReqStartDownloadParams", "PReqStartDownloadParams");
class_alias("CByte", "TResStartDownloadParams");
class_alias("TResStartDownloadParams", "PResStartDownloadParams");

class TReqDownloadParams extends CStruct {
  const SIZE = CByte::SIZE + CByte::SIZE * 7 + CByte::SIZE * 4 + CByte::SIZE * 5 + CByte::SIZE;

  public function __construct() {
    $this->Members["Fun"] = new CByte();                    // pduDownload or pduDownloadEnded
    $this->Members["Uk7"] = new CArray("CByte", [7]);
    $this->Members["Len_1"] = new CByte();                  // 0x09
    $this->Members["Prefix"] = new CByte();                 // 0x5F
    $this->Members["BlkPrfx"] = new CByte();                // always 0x30
    $this->Members["BlkType"] = new CByte();
    $this->Members["AsciiBlk"] = new CArray("CByte", [5]);  // BlockNum in ascii
    $this->Members["P"] = new CByte();                      // 0x50 ('P')
  }
}

class_alias("TReqDownloadParams", "PReqDownloadParams");

class TResDownloadParams extends CStruct {
  const SIZE = CByte::SIZE * 2;

  public function __construct() {
    $this->Members["FunDwnld"] = new CByte(); // 0x1B
    $this->Members["EoS"] = new CByte();      // End of sequence : 0x00 - Sequence in progress : 0x01
  }
}

class_alias("TResDownloadParams", "PResDownloadParams");

class TResDownloadDataHeader extends CStruct {
  const SIZE = CWord::SIZE * 2;

  public function __construct() {
    $this->Members["EoS"] = new CWord();
    $this->Members["EoS"] = new CWord();  // 0x00 0xFB
  }
}

class_alias("TResDownloadDataHeader", "PResDownloadDataHeader");
class_alias("CByte", "TResEndDownloadParams");
class_alias("TResEndDownloadParams", "PResEndDownloadParams");

class TS7CompactBlockInfo extends CStruct {
  const SIZE = CWord::SIZE + CByte::SIZE * 4 + CWord::SIZE + CUInt::SIZE * 3 + CWord::SIZE +
               CUInt::SIZE + CWord::SIZE * 5;

  public function __construct() {
    $this->Members["Cst_pp"] = new CWord();
    $this->Members["Uk_01"] = new CByte();  // Unknown 0x01
    $this->Members["BlkFlags"] = new CByte();
    $this->Members["BlkLang"] = new CByte();
    $this->Members["SubBlkType"] = new CByte();
    $this->Members["BlkNum"] = new CWord();
    $this->Members["LenLoadMem"] = new CUInt();
    $this->Members["BlkSec"] = new CUInt();
    $this->Members["CodeTime_ms"] = new CUInt();
    $this->Members["CodeTime_dy"] = new CWord();
    $this->Members["IntfTime_ms"] = new CUInt();
    $this->Members["IntfTime_dy"] = new CWord();
    $this->Members["SbbLen"] = new CWord();
    $this->Members["AddLen"] = new CWord();
    $this->Members["LocDataLen"] = new CWord();
    $this->Members["MC7Len"] = new CWord();
  }
}

class_alias("TS7CompactBlockInfo", "PS7CompactBlockInfo");

class TS7BlockFooter extends CStruct {
  const SIZE = CByte::SIZE * 20 + CByte::SIZE * 8 * 3 + CByte::SIZE * 2 + CWord::SIZE + CByte::SIZE * 8;

  public function __construct() {
    $this->Members["Uk_20"] = new CArray("CByte", [20]);
    $this->Members["Author"] = new CArray("CByte", [8]);
    $this->Members["Family"] = new CArray("CByte", [8]);
    $this->Members["Header"] = new CArray("CByte", [8]);
    $this->Members["B1"] = new CByte();  // 0x11
    $this->Members["B2"] = new CByte();  // 0x00
    $this->Members["Chksum"] = new CWord();
    $this->Members["Uk_12"] = new CArray("CByte", [8]); //TODO: 12 vs 8 seems to be a bug or a non common name
  }
}

class_alias("TS7BlockFooter", "PS7BlockFooter");

//==============================================================================
//                          FUNCTION INSERT/DELETE
//==============================================================================
class TReqControlBlockParams extends CStruct {
  const SIZE = CByte::SIZE + CByte::SIZE * 7 + CWord::SIZE + CByte::SIZE * 4 + CByte::SIZE * 5 +
               CByte::SIZE * 2 + CByte::SIZE * 5;

  public function __construct() {
    $this->Members["Fun"] = new CByte();                    // plc control 0x28
    $this->Members["Uk7"] = new CArray("CByte", [7]);       // unknown 7
    $this->Members["Len_1"] = new CWord();                  // Length part 1 : 10
    $this->Members["NumOfBlocks"] = new CByte();            // number of blocks to insert
    $this->Members["ByteZero"] = new CByte();               // 0x00
    $this->Members["AsciiZero"] = new CByte();              // 0x30 '0'
    $this->Members["BlkType"] = new CByte();
    $this->Members["AsciiBlk"] = new CArray("CByte", [5]);  // BlockNum in ascii
    $this->Members["SFun"] = new CByte();                   // 0x50 or 0x42
    $this->Members["Len_2"] = new CByte();                  // Length part 2 : 0x05 bytes
    $this->Members["Cmd"] = new CArray("CChar" [5]);        // ascii '_INSE' or '_DELE'
  }
}

class_alias("TReqControlBlockParams", "PReqControlBlockParams");

//==============================================================================
//                FUNCTIONS START/STOP/COPY RAM TO ROM/COMPRESS
//==============================================================================
class TReqFunPlcStop extends CStruct {
  const SIZE = CByte::SIZE + CByte::SIZE * 5 + CByte::SIZE + CChar::SIZE * 9;

  public function __construct() {
    $this->Members["Fun"] = new CByte();                // stop 0x29
    $this->Members["Uk_5"] = new CArray("CByte", [5]);  // unknown 5 bytes 0x00
    $this->Members["Len_2"] = new CByte();              // Length part 2 : 0x09
    $this->Members["Cmd"] = new CArray("CChar", [9]);   // ascii 'P_PROGRAM'
  }
}

class_alias("TReqFunPlcStop", "PReqFunPlcStop");

class TReqFunPlcHotStart extends CStruct {
  const SIZE = CByte::SIZE + CByte::SIZE * 7 + CWord::SIZE + CByte::SIZE + CChar::SIZE * 9;

  public function __construct() {
    $this->Members["Fun"] = new CByte();                // start 0x28
    $this->Members["Uk_7"] = new CArray("CByte", [7]);  // unknown 7
    $this->Members["Len_1"] = new CWord();              // Length part 1 : 0x0000
    $this->Members["Len_2"] = new CByte();              // Length part 2 : 0x09
    $this->Members["Cmd"] = new CArray("CChar", [9]);   // ascii 'P_PROGRAM'
  }
}

class_alias("TReqFunPlcHotStart", "PReqFunPlcHotStart");

class TReqFunPlcColdStart extends CStruct {
  const SIZE = CByte::SIZE + CByte::SIZE * 7 + CWord::SIZE * 2 + CByte::SIZE + CChar::SIZE * 9;

  public function __construct() {
    $this->Members["Fun"] = new CByte();                // start 0x28
    $this->Members["Uk_7"] = new CArray("CByte", [7]);  // unknown 7
    $this->Members["Len_1"] = new CWord();              // Length part 1 : 0x0002
    $this->Members["SFun"] = new CWord();               // 'C ' 0x4320
    $this->Members["Len_2"] = new CByte();              // Length part 2 : 0x09
    $this->Members["Cmd"] = new CArray("CChar", [9]);  // ascii 'P_PROGRAM'
  }
}

class_alias("TReqFunPlcColdStart", "PReqFunPlcColdStart");

class TReqFunCopyRamToRom extends CStruct {
  const SIZE = CByte::SIZE + CByte::SIZE * 7 + CWord::SIZE * 2 + CByte::SIZE + CChar::SIZE * 5;

  public function __construct() {
    $this->Members["Fun"] = new CByte();                // pduControl 0x28
    $this->Members["Uk_7"] = new CArray("CByte", [7]);  // unknown 7
    $this->Members["Len_1"] = new CWord();              // Length part 1 : 0x0002
    $this->Members["SFun"] = new CWord();               // 'EP' 0x4550
    $this->Members["Len_2"] = new CByte();              // Length part 2 : 0x05
    $this->Members["Cmd"] = new CArray("CChar", [5]);   // ascii '_MODU'
  }
}

class_alias("TReqFunCopyRamToRom", "PReqFunCopyRamToRom");

class TReqFunCompress extends CStruct {
  const SIZE = CByte::SIZE + CByte::SIZE * 7 + CWord::SIZE + CByte::SIZE + CChar::SIZE * 5;

  public function __construct() {
    $this->Members["Fun"] = new CByte();                // pduControl 0x28
    $this->Members["Uk_7"] = new CArray("CByte", [7]);  // unknown 7
    $this->Members["Len_1"] = new CWord();              // Length part 1 : 0x00
    $this->Members["Len_2"] = new CByte();              // Length part 2 : 0x05
    $this->Members["Cmd"] = new CArray("CChar", [5]);   // ascii '_GARB'
  }
}

class_alias("TReqFunCompress", "PReqFunCompress");

class TResFunCtrl extends CStruct {
  const SIZE = CByte::SIZE * 2;

  public function __construct() {
    $this->Members["ResFun"] = new CByte();
    $this->Members["para"] = new CByte();
  }
}

class_alias("TResFunCtrl", "PResFunCtrl");

//==============================================================================
//                            FUNCTIONS USERDATA
//==============================================================================
class TS7Params7 extends CStruct {
  const SIZE = CByte::SIZE * 3 + CByte::SIZE * 5 + CWord::SIZE * 2;

  public function __construct() {
    $this->Members["Head"] = new CArray("CByte", [3]);  // Always 0x00 0x01 0x12
    $this->Members["Plen"] = new CByte();               // par len 0x04 or 0x08
    $this->Members["Uk"] = new CByte();                 // unknown
    $this->Members["Tg"] = new CByte();                 // type and group  (4 bits type and 4 bits group)
    $this->Members["SubFun"] = new CByte();             // subfunction
    $this->Members["Seq"] = new CByte();                // sequence
    $this->Members["resvd"] = new CWord();              // present if plen=0x08 (S7 manager online functions)
    $this->Members["Err"] = new CWord();                // present if plen=0x08 (S7 manager online functions)
  }
}

class_alias("TS7Params7", "PS7ReqParams7");
class_alias("TS7Params7", "PS7ResParams7");

// for convenience Hi order bit of type are included (0x4X)
const grProgrammer  = 0x41;
const grCyclicData  = 0x42;
const grBlocksInfo  = 0x43;
const grSZL         = 0x44;
const grPassword    = 0x45;
const grBSend       = 0x46;
const grClock       = 0x47;
const grSecurity    = 0x45;

//==============================================================================
//                             GROUP SECURITY
//==============================================================================
class_alias("TReqFunTypedParams", "TReqFunSecurity");
class_alias("TReqFunSecurity", "PReqFunSecurity");

class TS7Password extends CArray {
  const SIZE = CChar::SIZE * 8;

  public function __construct() {
    parent::_construct("CChar", [8]);
  }
}

class TReqDataSecurity extends CStruct {
  const SIZE = CByte::SIZE * 2 + CWord::SIZE + CByte::SIZE * 8;

  public function __construct() {
    $this->Members["Ret"] = new CByte();              // 0xFF for request
    $this->Members["TS"] = new CByte();               // 0x09 Transport size
    $this->Members["DLen"] = new CWord();             // Data len  : 8 bytes
    $this->Members["Pwd"] = new CArray("CByte", [8]); // Password encoded into "AG" format
  }
}

class_alias("TReqDataSecurity", "PReqDataSecurity");
class_alias("TS7Params7", "TResParamsSecurity");
class_alias("TResParamsSecurity", "PResParamsSecurity");

class TResDataSecurity extends CStruct {
  const SIZE = CByte::SIZE * 2 + CWord::SIZE;

  public function __construct() {
    $this->Members["Ret"] = new CByte();
    $this->Members["TS"] = new CByte();
    $this->Members["DLen"] = new CWord();
  }
}

class_alias("TResDataSecurity", "PResDataSecurity");

//==============================================================================
//                             GROUP BLOCKS SZL
//==============================================================================
class_alias("TReqFunTypedParams", "TReqFunReadSZLFirst");
class_alias("TReqFunReadSZLFirst", "PReqFunReadSZLFirst");

class TReqFunReadSZLNext extends CStruct {
  const SIZE = CByte::SIZE * 3 + CByte::SIZE * 5 + CWord::SIZE * 2;

  public function __construct() {
    $this->Members["Head"] = new CArray("CByte", [3]);  // 0x00 0x01 0x12
    $this->Members["Plen"] = new CByte();               // par len 0x04
    $this->Members["Uk"] = new CByte();                 // unknown
    $this->Members["Tg"] = new CByte();                 // type and group (4 bits type and 4 bits group)
    $this->Members["SubFun"] = new CByte();             // subfunction
    $this->Members["Seq"] = new CByte();                // sequence
    $this->Members["Rsvd"] = new CWord();               // Reserved 0x0000
    $this->Members["ErrNo"] = new CWord();              // Error Code
  }
}

class_alias("TReqFunReadSZLNext", "PReqFunReadSZLNext");

class TS7ReqSZLData extends CStruct {
  const SIZE = CByte::SIZE * 3 + CWord::SIZE * 2;

  public function __construct() {
    $this->Members["Ret"] = new CByte();    // 0xFF for request
    $this->Members["TS"] = new CByte();     // 0x09 Transport size
    $this->Members["DLen"] = new CByte();   // Data len
    $this->Members["ID"] = new CWord();     // SZL-ID
    $this->Members["Index"] = new CWord();  // SZL-Index
  }
}

class_alias("TS7ReqSZLData", "PS7ReqSZLData");

class TS7ResSZLDataFirst extends CStruct {
  const SIZE = CByte::SIZE * 2 + CWord::SIZE * 5 + CWord::SIZE * 32747;

  public function __construct() {
    $this->Members["Ret"] = new CByte();
    $this->Members["TS"] = new CByte();
    $this->Members["DLen"] = new CWord();
    $this->Members["ID"] = new CWord();
    $this->Members["Index"] = new CWord();
    $this->Members["ListLen"] = new CWord();
    $this->Members["ListCount"] = new CWord();
    $this->Members["Data"] = new CArray("CWord", [32747]);
  }
}

class_alias("TS7ResSZLDataFirst", "PS7ResSZLDataFirst");

class TS7ResSZLDataNext extends CStruct {
  const SIZE = CByte::SIZE * 2 + CWord::SIZE + CWord::SIZE * 32751;

  public function __construct() {
    $this->Members["Ret"] = new CByte();
    $this->Members["TS"] = new CByte();
    $this->Members["DLen"] = new CWord();
    $this->Members["Data"] = new CArray("CWord", [32751]);
  }
}

class_alias("TS7ResSZLDataNext", "PS7ResSZLDataNext");

class TS7ResSZLData_0 extends CStruct {
  const SIZE = CByte::SIZE + CByte::SIZE * 9 + CWord::SIZE + CWord::SIZE * 32747;

  public function __construct() {
    $this->Members["Ret"] = new CByte();
    $this->Members["OtherInfo"] = new CArray("CByte", [9]);
    $this->Members["Count"] = new CWord();
    $this->Members["Items"] = new CArray("CWord", [32747]);
  }
}

class_alias("TS7ResSZLData_0", "PS7ResSZLData_0");

//==============================================================================
//                               GROUP CLOCK
//==============================================================================
class_alias("TReqFunTypedParams", "TReqFunDateTime");
class_alias("TReqFunDateTime", "PReqFunDateTime");

class TReqDataGetDateTime extends CArray {
  const SIZE = CByte::SIZE * 4;

  public function __construct() {
    parent::__construct("CByte", [4]);
  }
}

class_alias("CLongWord", "PReqDataGetDateTime");

class TResDataGetTime extends CStruct {
  const SIZE = CByte::SIZE * 2 + CWord::SIZE + CByte::SIZE * 2 + TTimeBuffer::SIZE;

  public function __construct() {
    $this->Members["RetVal"] = new CByte();
    $this->Members["TSize"] = new CByte();
    $this->Members["Length"] = new CWord();
    $this->Members["Rsvd"] = new CByte();
    $this->Members["HiYear"] = new CByte();
    $this->Members["Time"] = new TTimeBuffer();
  }
}

class_alias("TResDataGetTime", "PResDataGetTime");
class_alias("TResDataGetTime", "TReqDataSetTime");
class_alias("TReqDataSetTime", "PReqDataSetTime");

class TResDataSetTime extends CStruct {
  const SIZE = CByte::SIZE * 2 + CWord::SIZE;

  public function __construct() {
    $this->Members["RetVal"] = new CByte();
    $this->Members["TSize"] = new CByte();
    $this->Members["Length"] = new CWord();
  }
}

class_alias("TResDataSetTime", "PResDataSetTime");

//==============================================================================
//                            GROUP BLOCKS INFO
//==============================================================================
class_alias("TReqFunTypedParams", "TReqFunGetBlockInfo");
class_alias("TReqFunGetBlockInfo", "PReqFunGetBlockInfo");

class TReqDataFunBlocks extends CArray {
  const SIZE = CByte::SIZE * 4;

  public function __construct() {
    parent::__construct("CByte", [4]);
  }
}
//TODO: typedef u_char* PReqDataFunBlocks;

class TResFunGetBlockInfo extends CStruct {
  const SIZE = CByte::SIZE * 3 + CByte::SIZE * 5 + CWord::SIZE * 2;

  public function __construct() {
    $this->Members["Head"] = new CArray("CByte", [3]);  // 0x00 0x01 0x12
    $this->Members["Plen"] = new CByte();               // par len 0x04
    $this->Members["Uk"] = new CByte();                 // unknown
    $this->Members["Tg"] = new CByte();                 // type and group  (4 bits type and 4 bits group)
    $this->Members["SubFun"] = new CByte();             // subfunction
    $this->Members["Seq"] = new CByte();                // sequence
    $this->Members["Rsvd"] = new CWord();               // Reserved 0x0000
    $this->Members["ErrNo"] = new CWord();              // Error Code
  }
}

class_alias("TResFunGetBlockInfo", "PResFunGetBlockInfo");

class TResFunGetBlockItem extends CStruct {
  const SIZE = CByte::SIZE * 2 + CWord::SIZE;

  public function __construct() {
    $this->Members["Zero"] = new CByte();   // always 0x30 -> Ascii 0
    $this->Members["BType"] = new CByte();  // Block Type
    $this->Members["BCount"] = new CWord(); // Block count
  }
}

class TDataFunListAll extends CStruct {
  const SIZE = CByte::SIZE * 2 + CWord::SIZE + TResFunGetBlockItem::SIZE;

  public function __construct() {
    $this->Members["RetVal"] = new CByte();
    $this->Members["TRSize"] = new CByte();
    $this->Members["Length"] = new CWord();
    $this->Members["Blocks"] = new CArray("TResFunGetBlockItem", [7]);
  }
}

class_alias("TDataFunListAll", "PDataFunListAll");

class TDataFunGetBotItem extends CStruct {
  const SIZE = CWord::SIZE + CByte::SIZE * 2;

  public function __construct() {
    $this->Members["BlockNum"] = new CWord();
    $this->Members["Unknown"] = new CByte();
    $this->Members["BlockLang"] = new CByte();
  }
}

class TDataFunGetBot extends CStruct {
  const SIZE = CByte::SIZE * 2 + CWord::SIZE + TDataFunGetBotItem::SIZE * ((IsoPayload_Size - 29 ) / 4);

  public function __construct() {
    $this->Members["RetVal"] = new CByte();
    $this->Members["TSize"] = new CByte();
    $this->Members["DataLen"] = new CWord();
    $this->Members["Items"] = new CArray("TDataFunGetBotItem", [(IsoPayload_Size - 29 ) / 4]);
  }
}
// Note : 29 is the size of headers iso, COPT, S7 header, params, data

class_alias("TDataFunGetBot", "PDataFunGetBot");

class TReqDataBlockOfType extends CStruct {
  const SIZE = CByte::SIZE * 2 + CWord::SIZE + CByte::SIZE * 2;

  public function __construct() {
    $this->Members["RetVal"] = new CByte();   // 0xFF
    $this->Members["TSize"] = new CByte();    // Octet (0x09)
    $this->Members["Length"] = new CWord();   // 0x0002
    $this->Members["Zero"] = new CByte();     // Ascii '0' (0x30)
    $this->Members["BlkType"] = new CByte();
  }
}

class_alias("TReqDataBlockOfType", "PReqDataBlockOfType");

class TReqDataBlockInfo extends CStruct {
  const SIZE = CByte::SIZE * 2 + CWord::SIZE + CByte::SIZE * 2 + CByte::SIZE * 5 + CByte::SIZE;

  public function __construct() {
    $this->Members["RetVal"] = new CByte();
    $this->Members["TSize"] = new CByte();
    $this->Members["DataLen"] = new CWord();
    $this->Members["BlkPrfx"] = new CByte();                // always 0x30
    $this->Members["BlkType"] = new CByte();
    $this->Members["AsciiBlk"] = new CArray("CByte", [5]);  // BlockNum in ascii
    $this->Members["A"] = new CByte();                      // always 0x41 ('A')
  }
}

class_alias("TReqDataBlockInfo", "PReqDataBlockInfo");

class TResDataBlockInfo extends CStruct {
  const SIZE = CByte::SIZE * 2 + CWord::SIZE + CByte::SIZE * 2 + CWord::SIZE * 3 + CByte::SIZE * 4 +
               CWord::SIZE + CUInt::SIZE + CByte::SIZE * 4 + CUInt::SIZE + CWord::SIZE + CUInt::SIZE +
               CWord::SIZE * 5 + CByte::SIZE * 8 * 3 + CByte::SIZE * 2 + CWord::SIZE + CByte::SIZE * 4 * 2;

  public function __construct() {
    $this->Members["RetVal"] = new CByte();
    $this->Members["TSize"] = new CByte();
    $this->Members["Length"] = new CWord();
    $this->Members["Cst_b"] = new CByte();
    $this->Members["BlkType"] = new CByte();
    $this->Members["Cst_w1"] = new CWord();
    $this->Members["Cst_w2"] = new CWord();
    $this->Members["Cst_pp"] = new CWord();
    $this->Members["Unknown_1"] = new CByte();
    $this->Members["BlkFlags"] = new CByte();
    $this->Members["BlkLang"] = new CByte();
    $this->Members["SubBlkType"] = new CByte();
    $this->Members["BlkNumber"] = new CWord();
    $this->Members["LenLoadMem"] = new CUInt();
    $this->Members["BlkSec"] = new CArray("CByte", [4]);
    $this->Members["CodeTime_ms"] = new CUInt();
    $this->Members["CodeTime_dy"] = new CWord();
    $this->Members["IntfTime_ms"] = new CUInt();
    $this->Members["IntfTime_dy"] = new CWord();
    $this->Members["SbbLen"] = new CWord();
    $this->Members["AddLen"] = new CWord();
    $this->Members["LocDataLen"] = new CWord();
    $this->Members["MC7Len"] = new CWord();
    $this->Members["Author"] = new CArray("CByte", [8]);
    $this->Members["Family"] = new CArray("CByte", [8]);
    $this->Members["Header"] = new CArray("CByte", [8]);
    $this->Members["Version"] = new CByte();
    $this->Members["Unknown_2"] = new CByte();
    $this->Members["BlkChksum"] = new CWord();
    $this->Members["Resvd1"] = new CArray("CByte", [4]);
    $this->Members["Resvd2"] = new CArray("CByte", [4]);
  }
}

class_alias("TResDataBlockInfo", "PResDataBlockInfo");

//==============================================================================
//                                 BSEND / BRECV
//==============================================================================
class TPendingBuffer extends CStruct {
  const SIZE = CInt::SIZE + CLongWord::SIZE + CByte::SIZE * 65536;

  public function __construct() {
    $this->Members["Size"] = new CInt();
    $this->Members["R_ID"] = new CLongWord();
    $this->Members["Data"] = new CArray("CByte", [65536]);
  }
}

class TPacketInfo extends CStruct {
  const SIZE = TTPKT::SIZE + TCOTP_DT::SIZE + CByte::SIZE * 2;

  public function __construct() {
    $this->Members["TPKT"] = new TTPKT();
    $this->Members["COTP"] = new TCOTP_DT();
    $this->Members["P"] = new CByte();
    $this->Members["PDUType"] = new CByte();
  }
}

class TBSendParams extends CStruct {
  const SIZE = CByte::SIZE * 3 + CByte::SIZE * 8;

  public function __construct() {
    $this->Members["Head"] = new CArray("CByte", [3]);  // Always 0x00 0x01 0x12
    $this->Members["Plen"] = new CByte();               // par len 0x04 or 0x08
    $this->Members["Uk"] = new CByte();                 // unknown  (0x12)
    $this->Members["Tg"] = new CByte();                 // type and group, 4 bits type and 4 bits group  (0x46)
    $this->Members["SubFun"] = new CByte();             // subfunction (0x01)
    $this->Members["Seq"] = new CByte();                // sequence
    $this->Members["IDSeq"] = new CByte();              // ID Sequence (come from partner)
    $this->Members["EoS"] = new CByte();                // End of Sequence = 0x00 Sequence in progress = 0x01;
    $this->Members["Err"] = new CWord();                //
  }
}

class_alias("TBSendParams", "PBSendReqParams");
class_alias("TBSendParams", "PBSendResParams");

// Data frame

class TBsendRequestData extends CStruct {
  const SIZE = CByte::SIZE * 2 + CWord::SIZE + CByte::SIZE * 4 + CUInt::SIZE;

  public function __construct() {
    $this->Members["FF"] = new CByte();                 // 0xFF
    $this->Members["TRSize"] = new CByte();             // Transport Size 0x09 (octet)
    $this->Members["Len"] = new CWord();                // This Telegram Length
    $this->Members["DHead"] = new CArray("CByte", [4]); // sequence 0x12 0x06 0x13 0x00
    $this->Members["R_ID"] = new CUInt();               // R_ID
  }
}

class_alias("TBsendRequestData", "PBsendRequestData");

class TBSendResData extends CStruct {
  const SIZE = CByte::SIZE * 4;

  public function __construct() {
    $this->Members["DHead"] = new CArray("CByte", [4]); // sequence 0x0A 0x00 0x00 0x00
  }
}

class_alias("TBSendResData", "PBSendResData");

?>
