 <?php

 /* Phosphor7 - Project
    Web: https://github.com/chaosben/phosphor7
    License: Apache License 2.0
 */


//---------------------------------------------------------------------------

if (! defined("PHOSPHOR7_EXTERNAL_REQUIRE_MANAGEMENT")) {
  require_once("p7_global.php");
  require_once("p7_ctypes.php");
  require_once("p7_netinet_in.php");
  require_once("p7_errno.php");
  require_once("snap_sysutils.php");
}

//----------------------------------------------------------------------------

//define("MSG_NOSIGNAL", 0);

//----------------------------------------------------------------------------
// Non blocking connection to avoid root priviledges under UNIX
// i.e. raw socket pinger is not more used.
// Thanks to Rolf Stalder that made it ;)
//----------------------------------------------------------------------------
#ifdef PLATFORM_UNIX
	#define NON_BLOCKING_CONNECT
#endif
#ifdef NON_BLOCKING_CONNECT
	#include <fcntl.h>
#endif

//----------------------------------------------------------------------------

define("SD_RECEIVE",      0x00);
define("SD_SEND",         0x01);
define("SD_BOTH",         0x02);
define("MaxPacketSize",   65536);

//----------------------------------------------------------------------------

if (PLATFORM_WINDOWS) {
  define("WSABASEERR", 10000);
  define("WSAEINTR", (WSABASEERR+4));
  define("WSAEBADF", (WSABASEERR+9));
  define("WSAEACCES", (WSABASEERR+13));
  define("WSAEFAULT", (WSABASEERR+14));
  define("WSAEINVAL", (WSABASEERR+22));
  define("WSAEMFILE", (WSABASEERR+24));
  define("WSAEWOULDBLOCK", (WSABASEERR+35));
  define("WSAEINPROGRESS", (WSABASEERR+36));
  define("WSAEALREADY", (WSABASEERR+37));
  define("WSAENOTSOCK", (WSABASEERR+38));
  define("WSAEDESTADDRREQ", (WSABASEERR+39));
  define("WSAEMSGSIZE", (WSABASEERR+40));
  define("WSAEPROTOTYPE", (WSABASEERR+41));
  define("WSAENOPROTOOPT", (WSABASEERR+42));
  define("WSAEPROTONOSUPPORT", (WSABASEERR+43));
  define("WSAESOCKTNOSUPPORT", (WSABASEERR+44));
  define("WSAEOPNOTSUPP", (WSABASEERR+45));
  define("WSAEPFNOSUPPORT", (WSABASEERR+46));
  define("WSAEAFNOSUPPORT", (WSABASEERR+47));
  define("WSAEADDRINUSE", (WSABASEERR+48));
  define("WSAEADDRNOTAVAIL", (WSABASEERR+49));
  define("WSAENETDOWN", (WSABASEERR+50));
  define("WSAENETUNREACH", (WSABASEERR+51));
  define("WSAENETRESET", (WSABASEERR+52));
  define("WSAECONNABORTED", (WSABASEERR+53));
  define("WSAECONNRESET", (WSABASEERR+54));
  define("WSAENOBUFS", (WSABASEERR+55));
  define("WSAEISCONN", (WSABASEERR+56));
  define("WSAENOTCONN", (WSABASEERR+57));
  define("WSAESHUTDOWN", (WSABASEERR+58));
  define("WSAETOOMANYREFS", (WSABASEERR+59));
  define("WSAETIMEDOUT", (WSABASEERR+60));
  define("WSAECONNREFUSED", (WSABASEERR+61));
  define("WSAELOOP", (WSABASEERR+62));
  define("WSAENAMETOOLONG", (WSABASEERR+63));
  define("WSAEHOSTDOWN", (WSABASEERR+64));
  define("WSAEHOSTUNREACH", (WSABASEERR+65));
  define("WSAENOTEMPTY", (WSABASEERR+66));
  define("WSAEPROCLIM", (WSABASEERR+67));
  define("WSAEUSERS", (WSABASEERR+68));
  define("WSAEDQUOT", (WSABASEERR+69));
  define("WSAESTALE", (WSABASEERR+70));
  define("WSAEREMOTE", (WSABASEERR+71));
  define("WSASYSNOTREADY", (WSABASEERR+91));
  define("WSAVERNOTSUPPORTED", (WSABASEERR+92));
  define("WSANOTINITIALISED", (WSABASEERR+93));
  define("WSAEDISCON", (WSABASEERR+101));
  define("WSAENOMORE", (WSABASEERR+102));
  define("WSAECANCELLED", (WSABASEERR+103));
  define("WSAEINVALIDPROCTABLE", (WSABASEERR+104));
  define("WSAEINVALIDPROVIDER", (WSABASEERR+105));
  define("WSAEPROVIDERFAILEDINIT", (WSABASEERR+106));
  define("WSASYSCALLFAILURE", (WSABASEERR+107));
  define("WSASERVICE_NOT_FOUND", (WSABASEERR+108));
  define("WSATYPE_NOT_FOUND", (WSABASEERR+109));
  define("WSA_E_NO_MORE", (WSABASEERR+110));
  define("WSA_E_CANCELLED", (WSABASEERR+111));
  define("WSAEREFUSED", (WSABASEERR+112));
  define("WSAHOST_NOT_FOUND", (WSABASEERR+1001));
  define("HOST_NOT_FOUND", WSAHOST_NOT_FOUND);
  define("WSATRY_AGAIN", (WSABASEERR+1002));
  define("TRY_AGAIN", WSATRY_AGAIN);
  define("WSANO_RECOVERY", (WSABASEERR+1003));
  define("NO_RECOVERY", WSANO_RECOVERY);
  define("WSANO_DATA", (WSABASEERR+1004));
  define("NO_DATA", WSANO_DATA);
  define("WSANO_ADDRESS", WSANO_DATA);
  define("NO_ADDRESS", WSANO_ADDRESS);
}
else {
// For other platform we need to re-define next constants
  #define INVALID_SOCKET (socket_t)(~0)
  define("SOCKET_ERROR", (-1));

  define("WSAEINTR", EINTR);
  define("WSAEBADF", EBADF);
  define("WSAEACCES", EACCES);
  define("WSAEFAULT", EFAULT);
  define("WSAEINVAL", EINVAL);
  define("WSAEMFILE", EMFILE);
  define("WSAEWOULDBLOCK", EWOULDBLOCK);
  define("WSAEINPROGRESS", EINPROGRESS);
  define("WSAEALREADY", EALREADY);
  define("WSAENOTSOCK", ENOTSOCK);
  define("WSAEDESTADDRREQ", EDESTADDRREQ);
  define("WSAEMSGSIZE", EMSGSIZE);
  define("WSAEPROTOTYPE", EPROTOTYPE);
  define("WSAENOPROTOOPT", ENOPROTOOPT);
  define("WSAEPROTONOSUPPORT", EPROTONOSUPPORT);
  define("WSAESOCKTNOSUPPORT", ESOCKTNOSUPPORT);
  define("WSAEOPNOTSUPP", EOPNOTSUPP);
  define("WSAEPFNOSUPPORT", EPFNOSUPPORT);
  define("WSAEAFNOSUPPORT", EAFNOSUPPORT);
  define("WSAEADDRINUSE", EADDRINUSE);
  define("WSAEADDRNOTAVAIL", EADDRNOTAVAIL);
  define("WSAENETDOWN", ENETDOWN);
  define("WSAENETUNREACH", ENETUNREACH);
  define("WSAENETRESET", ENETRESET);
  define("WSAECONNABORTED", ECONNABORTED);
  define("WSAECONNRESET", ECONNRESET);
  define("WSAENOBUFS", ENOBUFS);
  define("WSAEISCONN", EISCONN);
  define("WSAENOTCONN", ENOTCONN);
  define("WSAESHUTDOWN", ESHUTDOWN);
  define("WSAETOOMANYREFS", ETOOMANYREFS);
  define("WSAETIMEDOUT", ETIMEDOUT);
  define("WSAECONNREFUSED", ECONNREFUSED);
  define("WSAELOOP", ELOOP);
  define("WSAENAMETOOLONG", ENAMETOOLONG);
  define("WSAEHOSTDOWN", EHOSTDOWN);
  define("WSAEHOSTUNREACH", EHOSTUNREACH);
  define("WSAENOTEMPTY", ENOTEMPTY);
  define("WSAEUSERS", EUSERS);
  define("WSAEDQUOT", EDQUOT);
  define("WSAESTALE", ESTALE);
  define("WSAEREMOTE", EREMOTE);
}

define("WSAEINVALIDADDRESS", 12001);

define("ICmpBufferSize", 4096);

class TIcmpBuffer extends CArray {
  const SIZE = CByte::SIZE * ICmpBufferSize;

  public function __construct() {
    parent::__construct("CByte", [ICmpBufferSize]);
  }
}

// Ping result
define("PR_CANNOT_PERFORM", -1);// cannot ping :
                                //   unix    : no root rights or SUID flag set to
                                //             open raw sockets
                                //   windows : neither helper DLL found nor raw
                                //             sockets can be opened (no administrator rights)
                                //  In this case the execution continues whitout
                                //  the benefit of the smart-connect.

define("PR_SUCCESS",         0);// Host found
define("PR_ERROR",           1);// Ping Error, Ping was performed but ...
                                //   - host didn't replied (not found)
                                //   - routing error
                                //   - TTL expired
                                //   - ... all other icmp error that we don't need
                                //      to know.

// Ping Kind
define("pkCannotPing",  1);  // see PR_CANNOT_PERFORM comments
define("pkWinHelper",   2);  // use iphlpapi.dll (only windows)
define("pkRawSocket",   3);  // use raw sockets (unix/windows)

const ICMP_ECHORP  = 0;  // ECHO Reply
const ICMP_ECHORQ  = 8;  // ECHO Request

//---------------------------------------------------------------------------
// RAW SOCKET PING STRUCTS
//---------------------------------------------------------------------------

class TIPHeader extends CStruct {
  const SIZE = CByte::SIZE * 2 + CWord::SIZE * 3 + CByte::SIZE * 2 + CWord::SIZE + CLongWord::SIZE * 2;

  public function __construct() {
    $this->Members["ip_hl_v"] = new CByte();
    $this->Members["ip_tos"] = new CByte();
    $this->Members["ip_len"] = new CWord();
    $this->Members["ip_id"] = new CWord();
    $this->Members["ip_off"] = new CWord();
    $this->Members["ip_ttl"] = new CByte();
    $this->Members["ip_p"] = new CByte();
    $this->Members["ip_sum"] = new CWord();
    $this->Members["ip_src"] = new CLongWord();
    $this->Members["ip_dst"] = new CLongWord();
  }
}

class TIcmpHeader extends CStruct {
  const SIZE = CByte::SIZE * 2 + CWord::SIZE * 3;

  public function __construct() {
    $this->Members["ic_type"] = new CByte();  // Type of message
    $this->Members["ic_code"] = new CByte();  // Code
    $this->Members["ic_cksum"] = new CWord(); // 16 bit checksum
    $this->Members["ic_id"] = new CWord();    // ID (ic1 : ipv4)
    $this->Members["ic_seq"] = new CWord();   // Sequence
  }
}

class TIcmpPacket extends CStruct {
  const SIZE = TIcmpHeader::SIZE + CByte::SIZE * 32;

  public function __construct() {
    $this->Members["Header"] = new TIcmpHeader();
    $this->Members["Data"] = new CArray("CByte", [32]); // use the well known default
  }
}
class_alias("TIcmpPacket", "PIcmpPacket");

class TIcmpReply extends CStruct {
  const SIZE = TIPHeader::SIZE + TIcmpPacket::SIZE;

  public function __construct() {
    $this->Members["IPH"] = new TIPHeader();
    $this->Members["ICmpReply"] = new TIcmpPacket();
  }
};
class_alias("TIcmpReply", "PIcmpReply");

//---------------------------------------------------------------------------

class TRawSocketPinger {
  private $FSocket;
  private $SendPacket; //PIcmpPacket
  private $FId;         //word
  private $FSeq = 0;        //word

  private function InitPacket() {
    $this->FSeq++;

    //SendPacket=PIcmpPacket(pbyte(&IcmpBuffer)+sizeof(TIPHeader));
    $this->SendPacket->Header->ic_type=ICMP_ECHORQ;
    $this->SendPacket->Header->ic_code=0;
    $this->SendPacket->Header->ic_cksum=0;
    $this->SendPacket->Header->ic_id=$this->FId;
    $this->SendPacket->Header->ic_seq=$this->FSeq;

    $this->SendPacket->Data->__Value = array_fill(0, count($this->SendPacket->Data), 0);
    $this->SendPacket->Header->ic_cksum=$this->PacketChecksum();
  }

  private function PacketChecksum() {
    $Sum = 0;
    $Bytes = "";

    $this->SendPacket->writeToBytes($Bytes); //put all into one binary string
    $Words = unpack("S*Word", $Bytes); //split into words
    foreach($Words as $Word)
      $Sum += $Word;

    $Sum=($Sum >> 16) + ($Sum & 0xFFFF);
    $Sum=$Sum+($Sum >> 16);
    return (~$Sum);
  }

  private function CanRead($Timeout) {
    $x = 0;
    $FDset = array();
    $FDwrite = null;
    $FDexcept = null;

    $tv_usec = ($Timeout % 1000) * 1000;
    $tv_sec = $Timeout / 1000;

    $FDset[] = $this->FSocket;

    $x = socket_select($FDset, $FDwrite, $FDexcept, $tv_sec, $tv_usec);
    if ($x===false)
       $x=0;
    return ($x > 0);
  }

  public function Ping($ip_addr, $Timeout) {
    $LSockAddr = null;
    $RSockAddr = null;
    $Reply = new PIcmpReply();

    if (! (is_resource($this->FSocket) || $this->FSocket instanceof Socket))
      return true;

    // Init packet
    $this->InitPacket();
    $Reply=new PIcmpReply();

    // Bind to local
    if (! socket_bind($this->FSocket, "0.0.0.0", 0))
        return false;
    // Connect to remote (not a really TCP connection, only to setup the socket)
    if (! socket_connect($this->FSocket, long2ip($ip_addr), 0))
        return false;
    // Send ICMP packet
    $Buffer = "";
    $this->SendPacket->writeToBytes($Buffer);
    if (socket_send($this->FSocket, $Buffer, strlen($Buffer), 0)!==strlen($Buffer))
        return false;
    // Wait for a reply
    if (! $this->CanRead($Timeout))
        return false;// time expired
    // Get the answer
    $Buffer = "";
    if (socket_recv($this->FSocket, $Buffer, ICmpBufferSize, 0)<TIcmpReply::SIZE)
        return false;
    $Reply->readFromBytes($Buffer);
    // Check the answer
    return (pack("L", $Reply->IPH->ip_src)==inet_pton(long2ip($ip_addr))) &&  // the peer is what we are looking for
           ($Reply->ICmpReply->Header->ic_type==ICMP_ECHORP);    // type = reply
  }

  public function __construct() {
    $this->FSocket =socket_create(AF_INET, SOCK_RAW, getprotobyname("icmp"));
    $this->FId  = rand(0, 65535);
    $this->FSeq =0;
    $this->SendPacket = new PIcmpPacket();
  }

  public function __destruct() {
    if (is_resource($this->FSocket) || $this->FSocket instanceof Socket)
      socket_close($this->FSocket);
  }
}
class_alias("TRawSocketPinger", "PRawSocketPinger");

//---------------------------------------------------------------------------

$PingKind = null;

class TPinger {
  private function RawPing($ip_addr, $Timeout) {
    $RawPinger = new TRawSocketPinger();
    return $RawPinger->Ping($ip_addr, $Timeout);
  }

	public function Ping($Host_or_IP, $Timeout) {
    global $PingKind;

    //original function is overloaded (ip_addr as long or as string)
    if (is_string($Host_or_IP))
      $ip_addr = ip2long($Host_or_IP);
    else
      $ip_addr = $Host_or_IP;

    if ($PingKind==pkRawSocket)
      return $this->RawPing($ip_addr, $Timeout);
    else
      return true; // we still need to continue
  }
}
class_alias("TPinger", "PPinger");

function RawSocketsCheck() {
  $RawSocket = null;
  $Result = null;
  try {
    $OldErrorReporting = error_reporting(0);
    $RawSocket = socket_create(AF_INET, SOCK_RAW, getprotobyname("icmp"));
    if ($RawSocket !== false) {
      $Result = true;
      socket_close($RawSocket);
    }
    else {
      $Result = false;
    }
  }
  catch(Exception $e) {
    $Result = false;
  }
  finally {
    error_reporting($OldErrorReporting);
  }

  return $Result;
}

//---------------------------------------------------------------------------

class TSnapBase { // base class endian-aware
  private $LittleEndian; //bool

  protected function SwapDWord($Value) { //returns longword
    if ($this->LittleEndian)
      return ($Value >> 24) | (($Value << 8) & 0x00FF0000) | (($Value >> 8) & 0x0000FF00) | ($Value << 24);
    else
      return $Value;
  }

  protected function SwapWord($Value) { //returns word
    if ($this->LittleEndian)
      return  (($Value >> 8) & 0xFF) | (($Value << 8) & 0xFF00);
    else
	    return $Value;
  }

  public function __construct() {
    $this->LittleEndian = unpack("S", "\x01\x00")[1] === 1;
  }
}

class TMsgSocket extends TSnapBase {
  private $Pinger; //PPinger

  /**
   *  @return int
   */
  private function GetLastSocketError() {
    if (is_resource($this->FSocket) || $this->FSocket instanceof Socket)
      return socket_last_error($this->FSocket);
    else
      return socket_last_error();
  }

  /**
   *  @param int $SockResult
   *  @return int
   */
  private function SockCheck($SockResult) {
    if ($SockResult === false)
      $this->LastTcpError = $this->GetLastSocketError();

    return $this->LastTcpError;
  }

  private function DestroySocket() {
    if (is_resource($this->FSocket) || $this->FSocket instanceof Socket) {
      if (socket_shutdown($this->FSocket, SD_SEND))
			$this->Purge();
      socket_close($this->FSocket);
      $this->FSocket=null;
    }
    $this->LastTcpError=0;
  }

  private function SetSocketOptions() {
    $NoDelay = 1;
    $KeepAlive = 1;
    $LastTcpError=0;
    $this->SockCheck(socket_set_option($this->FSocket, SOL_TCP, TCP_NODELAY, $NoDelay));

    if ($this->LastTcpError==0)
      $this->SockCheck(socket_set_option($this->FSocket, SOL_SOCKET, SO_KEEPALIVE, $KeepAlive));
  }

  /**
   *  @param int $Timeout
   *  @return bool
   */
  private function CanWrite($Timeout) {
    $x = 0;
    $FDset = array();
    $FDread = null;
    $FDexcept = null;

	if(! (is_resource($this->FSocket) || $this->FSocket instanceof Socket))
		return false;

    $tv_usec = intval(($Timeout % 1000) * 1000);
    $tv_sec = intval($Timeout / 1000);

    $FDset[] = $this->FSocket;

    $x = socket_select($FDread, $FDset, $FDexcept, $tv_sec, $tv_usec);
    if ($x===false)
    {
        $this->LastTcpError = $this->GetLastSocketError();
        $x=0;
    }
    return ($x > 0);
  }

  private function GetLocal() {
    $Name = null;
    $Port = null;
    if (socket_getsockname($this->FSocket, $Name, $Port)) {
      $this->LocalSin->sin_addr->s_addr = unpack("NValue", inet_pton($Name))["Value"]; //stored in network byte order
      $this->LocalSin->sin_port = unpack("nValue", pack("n", $Port))["Value"]; //stored in network byte order

      $this->GetSin($this->LocalSin, $this->LocalAddress, $this->LocalPort);
    }
  }

  private function GetRemote() {
    $Name = null;
    $Port = null;
    if (socket_getpeername($this->FSocket, $Name, $Port)) {
      $this->RemoteSin->sin_addr->s_addr = unpack("NValue", inet_pton(ip2long($Name)))["Value"]; //stored in network byte order
      $this->RemoteSin->sin_port = unpack("nValue", pack("n", $Port))["Value"]; //stored in network byte order

      $this->GetSin($this->RemoteSin, $this->RemoteAddress, $this->RemotePort);
    }
  }

  /**
   *  @param sockaddr_in $sin
   *  @param string $Address
   *  @param int $Port
   */
  private function SetSin(sockaddr_in &$sin, $Address, $Port) {
    $in_addr = null;
    $in_addr=inet_pton($Address);

    $this->LastTcpError=0;

    if ($in_addr!==false) {
      $sin->sin_addr->s_addr = unpack("NValue", $in_addr)["Value"];
      $sin->sin_family = AF_INET;
      $sin->sin_port = unpack("nValue", pack("S", $Port))["Value"];
    }
    else
      $this->LastTcpError=WSAEINVALIDADDRESS;
  }

  /**
   *  @param sockaddr_in $sin
   *  @param string $Address
   *  @param int $Port
   */
  private function GetSin(sockaddr_in $sin, &$Address, &$Port) {
    $Address = inet_ntop(pack("N", $sin->sin_addr->s_addr));
    $Port = unpack("nValue", pack("S", $sin->sin_port))["Value"]; //TODO: this is htons, but it seems to be wrong; should be ntohs in my opinion
  }

  protected $FSocket;
  protected $LocalSin; //sockaddr_in
  protected $RemoteSin; //sockaddr_in

  //--------------------------------------------------------------------------

  // low level socket
  protected function CreateSocket() {
    $this->DestroySocket();
    $this->LastTcpError=0;
    $this->FSocket =socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    if (is_resource($this->FSocket) || $this->FSocket instanceof Socket)
      $this->SetSocketOptions();
    else
      $this->LastTcpError =$this->GetLastSocketError();
  }

  // Called when a socket is assigned externally
  protected function GotSocket() {
    $this->ClientHandle = $this->RemoteSin->sin_addr->s_addr;
  }

  /**
   *  Returns how many bytes are ready to be read in the winsock buffer
   *  @return int
   */
  protected function WaitingData(){
    $buffer = null;
    $result = 0;
    //there is no ioctl in php at the moment; PEEK should act similar
    if (defined('MSG_DONTWAIT'))
      $result = socket_recv($this->FSocket, $buffer, MaxPacketSize, MSG_PEEK | MSG_DONTWAIT);
    else {
      if (socket_set_nonblock($this->FSocket)) {
        $result = socket_recv($this->FSocket, $buffer, MaxPacketSize, MSG_PEEK);
        if ($result === false)
          $result = 0;
        socket_set_block($this->FSocket);
      }
    }
    return $result;
  }

  /**
   *  Waits until there at least "size" bytes ready to be read or until receive timeout occurs
   *  @param int $Size
   *  @param int $Timeout
   *  @return int
   */
  protected function WaitForData($Size, $Timeout) {
    $Elapsed = null;

    // Check for connection active
    if ($this->CanRead(0) && ($this->WaitingData()==0))
        $this->LastTcpError=WSAECONNRESET;
    else
        $this->LastTcpError=0;

    // Enter main loop
    if ($this->LastTcpError==0) {
        $Elapsed =SysGetTick();
        while(($this->WaitingData()<$Size) && ($this->LastTcpError==0)){
            // Checks timeout
            if (DeltaTime($Elapsed)>=($Timeout))
                $this->LastTcpError =WSAETIMEDOUT;
            else
                SysSleep(1);
        }
    }
    if($this->LastTcpError==WSAECONNRESET)
            $this->Connected =false;

    return $this->LastTcpError;
  }

  // Clear socket input buffer
  protected function Purge() {
    // small buffer to empty the socket
    $Trash = null;
    $Read = null;
    if ($this->LastTcpError!=WSAECONNRESET) {
      if ($this->CanRead(0)) {
        do {
          $Read=socket_recv($this->FSocket, $Trash, 512, 0);
        } while($Read==512);
      }
    }
  }

  public $ClientHandle; //longword
  public $LocalBind; //longword
  // Coordinates Address:Port
  public $LocalAddress; //char[16]
  public $RemoteAddress; //char[16]
  public $LocalPort; //word
  public $RemotePort; //word
  // "speed" of the socket listener (used server-side)
  public $WorkInterval; //int
  // Timeouts : 3 different values for fine tuning.
  // Send timeout should be small since with work with small packets and TCP_NO_DELAY
  //   option, so we don't expect "time to wait".
  // Recv timeout depends of equipment's processing time : we send a packet, the equipment
  //   processes the message, finally it sends the answer. In any case Recv timeout > Send Timeout.
  // PingTimeout is the maximum time interval during which we expect that the PLC answers.
  //   By default is 750 ms, increase it if there are many switch/repeaters.
  public $PingTimeout; //int
  public $RecvTimeout; //int
  public $SendTimeout; //int
  //int ConnTimeout;
  // Output : Last operation error
  public $LastTcpError; //int
  // Output : Connected to the remote Host/Peer/Client
  public $Connected; //bool

  //--------------------------------------------------------------------------

  public function __construct() {
    parent::__construct();

    $this->LocalSin = new sockaddr_in();
    $this->RemoteSin = new sockaddr_in();

    $this->Pinger = new TPinger();
    // Set Defaults
    $this->LocalAddress = "0.0.0.0";
    $this->LocalPort=0;
    $this->RemoteAddress = "127.0.0.1";
    $this->RemotePort=0;
    $this->WorkInterval=100;
    $this->RecvTimeout=500;
    $this->SendTimeout=10;
    $this->PingTimeout=750;
    $this->Connected=false;
    $this->FSocket=null;
    $this->LastTcpError=0;
    $this->LocalBind=0;
  }

  public function __destruct() {
    $this->DestroySocket();
    $this->Pinger = null;
  }

  // Returns true if "something" can be read during the Timeout interval..
  /**
   *  @param int $Timeout
   *  @return bool
   */
  public function CanRead($Timeout) {
    $tv_usec = null;
    $tv_sec = null;
    $x = null;
    $FDset = array();
    $FDwrite = null;
    $FDexcept = null;

    if(! (is_resource($this->FSocket) || $this->FSocket instanceof Socket))
      return false;

    $tv_usec = ($Timeout % 1000) * 1000;
    $tv_sec = $Timeout / 1000;

    $FDset[] = $this->FSocket;

    $x = socket_select($FDset, $FDwrite, $FDexcept, $tv_sec, $tv_usec);
    if ($x===false) {
       $this->LastTcpError = $this->GetLastSocketError();
       $x=0;
    }
    return ($x > 0);
  }

  /**
   *  Connects to a peer (using RemoteAddress and RemotePort)
   *  @return mixed
   */
  public function SckConnect() { // (client-side)
    $n = $flags = $err = null; //int
    $len = null; //socklen_t
    $rset = $wset = $eset = array();
    $tv_sec = $tv_usec = null;

    $this->SetSin($this->RemoteSin, $this->RemoteAddress, $this->RemotePort);

    if ($this->LastTcpError == 0) {
      $this->CreateSocket();
      if ($this->LastTcpError == 0) {
        if (socket_set_nonblock($this->FSocket)) {
          //n = connect(FSocket, (struct sockaddr*)&RemoteSin, sizeof(RemoteSin));
          if (! socket_connect($this->FSocket, $this->RemoteAddress, $this->RemotePort)) {
            $errno = socket_last_error($this->FSocket);
            if ((PLATFORM_WINDOWS and ($errno != WSAEWOULDBLOCK)) or ((! PLATFORM_WINDOWS) and ($errno != EINPROGRESS))) {
              $this->LastTcpError = GetLastSocketError();
            }
            else {
              // still connecting ...
              $rset[] = $this->FSocket;
              $wset = $rset;
              $eset = null;
              $tv_sec = intval($this->PingTimeout / 1000);
              $tv_usec = intval(($this->PingTimeout % 1000) * 1000);

              $n = socket_select($rset, $wset, $eset, $tv_sec, $tv_usec);
              if ($n == 0) {
                // timeout
                $this->LastTcpError = WSAEHOSTUNREACH;
              }
              else {
                if (in_array($this->FSocket, $rset) or in_array($this->FSocket, $wset)) {
                  $err = socket_get_option($this->FSocket, SOL_SOCKET, SO_ERROR);
                  if ($err !== false) {
                    if ($err != 0) {
                      $this->LastTcpError = $err;
                    }
                    else {
                      if (socket_set_block($this->FSocket)) {
                        $this->GetLocal();
                        $this->ClientHandle = $this->LocalSin->sin_addr->s_addr;
                      }
                      else {
                        $this->LastTcpError = $this->GetLastSocketError();
                      }
                    }
                  }
                  else {
                    $this->LastTcpError = $this->GetLastSocketError();
                  }
                }
                else {
                  $this->LastTcpError = -1;
                }
              }
            } // still connecting
          }
          else {
            // connected immediatly
            $this->GetLocal();
            $this->ClientHandle = $this->LocalSin->sin_addr->s_addr;
          }
        }
        else {
          $this->LastTcpError = $this->GetLastSocketError();
        } // socket_set_nonblock
      } //valid socket
    } // LastTcpError==0
    $this->Connected=$this->LastTcpError==0;
    return $this->LastTcpError;
  }

  /**
   *  Disconnects from a peer (gracefully)
   */
  public function SckDisconnect() {
    $this->DestroySocket();
    $this->Connected=false;
  }

  /**
   *  Disconnects RAW
   */
  public function ForceClose() {
    if (is_resource($this->FSocket) || $this->FSocket instanceof Socket)
    {
      try {
        socket_close($this->FSocket);
      } catch (Exception $e) {
      }
      $this->FSocket=null;
    }
    $this->LastTcpError=0;
  }

  /**
   *  Binds to a local adapter (using LocalAddress and LocalPort) (server-side)
   *  @return int
   */
  public function SckBind() {
    $Res = 0; //int
    $Opt=1; //int
    $this->SetSin($this->LocalSin, $this->LocalAddress, $this->LocalPort);
    if ($this->LastTcpError==0)
    {
      $this->CreateSocket();
      if ($this->LastTcpError==0)
      {
        socket_set_option($this->FSocket ,SOL_SOCKET, SO_REUSEADDR, $Opt);
        $Res=socket_bind($this->FSocket, $this->LocalAddress, $this->LocalPort);
        $this->SockCheck($Res);
        if ($Res==0) {
            $this->LocalBind=$this->LocalSin->sin_addr->s_addr;
        }
      }
    }
    else
        $this->LastTcpError=WSAEINVALIDADDRESS;

    return $this->LastTcpError;
  }

  /**
   *  Listens for an incoming connection (server-side)
   *  @return int
   */
  public function SckListen() {
    $this->LastTcpError=0;
    $this->SockCheck(socket_listen($this->FSocket ,SOMAXCONN));
    return $this->LastTcpError;
  }

  /**
   *  Set an external socket reference (tipically from a listener)
   *  @param socket_t $s
   */
  public function SetSocket($s) {
    $this->FSocket=$s;
    if (is_resource($this->FSocket) || $this->FSocket instanceof Socket) {
      $this->SetSocketOptions();
      $this->GetLocal();
      $this->GetRemote();
      $this->GotSocket();
    }
    $this->Connected=is_resource($this->FSocket) || $this->FSocket instanceof Socket;
  }

  /**
   *  Accepts an incoming connection returning a socket descriptor (server-side)
   *  @return socket_t
   */
  public function SckAccept() {
    $result = null; //socket_t
    $this->LastTcpError=0;
    $result = socket_accept($this->FSocket, NULL, NULL);
    if(! (is_resource($result) || $result instanceof Socket))
        $this->LastTcpError =$this->GetLastSocketError();
    return $result;
  }

  /**
   *  Pings the peer before connecting
   *  @param string|long $Host_or_IP
   *  @return bool
   */
  public function Ping($Host_or_IP) {
    if ($this->PingTimeout == 0)
      return true;
    else
      return $this->Pinger->Ping($Host_or_IP, $this->PingTimeout);
  }

  /**
   *  Sends a packet
   *  @param string Data
   *  @param int Size
   *  @return int
   */
  public function SendPacket($Data, $Size) {
    $Result = null; //int

    $this->LastTcpError=0;
    if ($this->SendTimeout>0) {
      if (! $this->CanWrite($this->SendTimeout)) {
        $this->LastTcpError = WSAETIMEDOUT;
        return $this->LastTcpError;
      }
    }
    if (socket_send($this->FSocket, $Data, $Size, 0)==$Size)
      return 0;
    else
      $Result =SOCKET_ERROR;

    $this->SockCheck($Result);
    return $Result;
  }

  /**
   *  Returns true if a Packet at least of "Size" bytes is ready to be read
   *  @param int Size
   *  @return bool
   */
  public function PacketReady($Size) {
    return ($this->WaitingData()>=$Size);
  }

  /**
   *  Receives everything
   *  @param string Data
   *  @param int BufSize
   *  @param int SizeRecvd
   *  @return int
   */
  public function Receive(&$Data, $BufSize, &$SizeRecvd) {
    $this->LastTcpError=0;

    if ($this->CanRead($this->RecvTimeout)) {
      $SizeRecvd=socket_recv($this->FSocket, $Data, $BufSize, 0);

      if ($SizeRecvd>0) // something read (default case)
       $this->LastTcpError=0;
      else
        if ($SizeRecvd===0)
          $this->LastTcpError = WSAECONNRESET;  // Connection reset by Peer
        else
          $this->LastTcpError=$this->GetLastSocketError(); // we need to know what happened
    }
    else
      $this->LastTcpError = WSAETIMEDOUT;

    if ($this->LastTcpError==WSAECONNRESET)
      $this->Connected = false;

    return $this->LastTcpError;
  }

  /**
   *  Receives a packet of size specified.
   *  @param string Data
   *  @param int Size
   *  @return int
   */
  public function RecvPacket(&$Data, $Size) {
    $BytesRead = null; //int

    $this->WaitForData($Size, $this->RecvTimeout);
    if ($this->LastTcpError==0) {
      $BytesRead=socket_recv($this->FSocket, $Data, $Size, 0);
      if ($BytesRead===0)
        $this->LastTcpError = WSAECONNRESET;  // Connection reset by Peer
      else
        if ($BytesRead<0)
          $this->LastTcpError = $this->GetLastSocketError();
    }
    else // After the timeout the bytes waiting were less then we expected
      if ($this->LastTcpError==WSAETIMEDOUT)
        $this->Purge();

    if ($this->LastTcpError==WSAECONNRESET)
      $this->Connected =false;

    return $this->LastTcpError;
  }

  /**
   *  Peeks a packet of size specified without extract it from the socket queue
   *  @param string Data
   *  @param mixed Size
   *  @return int
   */
  public function PeekPacket(&$Data, $Size) {
    $BytesRead = null; //int
    $this->WaitForData($Size, $this->RecvTimeout);
    if ($this->LastTcpError==0)
    {
      $BytesRead=socket_recv($this->FSocket, $Data, $Size, MSG_PEEK);
      if ($BytesRead===0)
        $this->LastTcpError = WSAECONNRESET;  // Connection reset by Peer
      else
        if ($BytesRead<0)
          $this->LastTcpError = $this->GetLastSocketError();
    }
    else // After the timeout the bytes waiting were less then we expected
      if ($this->LastTcpError==WSAETIMEDOUT)
        $this->Purge();

    if ($this->LastTcpError==WSAECONNRESET)
      $this->Connected =false;

    return $this->LastTcpError;
  }

  /**
   *  @return bool
   */
  public function Execute() {
    return true;
  }
};

class_alias("TMsgSocket", "PMsgSocket");

//---------------------------------------------------------------------------

/**
 *  @param socket_t  $FSocket
 */
function Msg_CloseSocket($FSocket) {
  socket_close($FSocket);
}

/**
 *  @param socket_t FSocket
 *  @return longword
 */
function Msg_GetSockAddr($FSocket) {
  $Addr = null;
  if (socket_getpeername($FSocket, $Addr))
    return ip2long($Addr);
  else
    return 0;
}

//---------------------------------------------------------------------------

class SocketsLayer {
  public function __construct() {
    $PingKind = RawSocketsCheck()?pkRawSocket:pkCannotPing;
  }

  public function __destruct() {

  }
}

?>
