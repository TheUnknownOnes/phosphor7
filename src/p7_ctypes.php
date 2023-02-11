<?php

/* Phosphor7 - Project
   Web: https://github.com/chaosben/phosphor7
   License: Apache License 2.0
*/

abstract class CVariable {
  //each descendant hast to define a const named "SIZE" with the size in bytes
  const SIZE = 0;

	//both functions has to return how many bytes they processed
	public abstract function readFromBytes($Bytes, $Index = null);
	public abstract function writeToBytes(&$Bytes, $Index = null);

  public function getAsBytes() {
    $Bytes = "";
    $this->writeToBytes($Bytes);
    return $Bytes;
  }

  public static function cast(CVariable $Variable) {
    if (is_null($Variable))
      return null;
    else {
      $retval = new static();
      $retval->readFromBytes($Variable->getAsBytes());
      return $retval;
    }
  }

  //get and set value in PHP-Format
	protected abstract function getValue();
	protected abstract function setValue($Value);

  public function __get($Name) {
    if ($Name == "__Value")
      return $this->getValue();
    else
      throw new Exception("Invalid property '{$Name}'");
  }

  public function __set($Name, $NewValue) {
    if ($Name == "__Value")
      $this->setValue($NewValue);
    else
      throw new Exception("Invalid property '{$Name}'");
  }
}

function memcpy(CVariable $Destination, CVariable $Source, $Num) {
  $Buffer = $Source->getAsBytes();
  $Buffer = substr($Buffer, 0, $Num);
  $Destination->readFromBytes($Buffer);
}

function memset(CVariable $Destination, $Value, $Num) {
  $Bytes = str_repeat(chr($Value), $Num);
  $Destination->readFromBytes($Bytes);
  $Destination->readFromBytes($Bytes);
}

abstract class CSimpleVariable extends CVariable {
	protected $Value;

	protected function getValue() {
		return $this->Value;
	}

	protected function setValue($Value) {
		$this->Value = $Value;
	}

	public function __toString() {
		return (string)$this->Value;
	}
}

class CPointer extends CSimpleVariable {
  const SIZE = 4;

	public function __construct(CVariable $InitialValue = null) {
		$this->setValue($InitialValue);
	}

  protected function setValue($Value) {
    if ((! is_null($Value) && (! ($Value instanceof CVariable))))
      throw new Exception("Value of a pointer has to be null or an instance of CVariable");

		$this->Value = $Value;
	}

	public function readFromBytes($Bytes, $Index = null){
		if (is_null($Index)) $Index = 0;
    if ($Index < strlen($Bytes)) {
      //TODO: find a way to find object by address
      //$this->Value = unpack('LValue', substr($Bytes, $Index))["Value"];
      return self::SIZE;
    }
    else
      return 0;
	}

	public function writeToBytes(&$Bytes, $Index = null){
		if (is_null($Index)) $Index = strlen($Bytes);
    //TODO: find a way to get address of an object
		$Bytes = substr_replace($Bytes, pack('L', 0), $Index, self::SIZE);
		return self::SIZE;
	}
}

class CBool extends CSimpleVariable {
  const SIZE = 1;

	public function __construct($InitialValue = true) {
		$this->setValue($InitialValue);
	}

	public function readFromBytes($Bytes, $Index = null){
		if (is_null($Index)) $Index = 0;
    if ($Index < strlen($Bytes)) {
      $this->Value = (unpack('CValue', substr($Bytes, $Index))["Value"]) != 0;
      return self::SIZE;
    }
    else
      return 0;
	}

	public function writeToBytes(&$Bytes, $Index = null){
		if (is_null($Index)) $Index = strlen($Bytes);
		$Bytes = substr_replace($Bytes, pack('C', ($this->Value)?1:0), $Index, self::SIZE);
		return self::SIZE;
	}
}

class uint8_t extends CSimpleVariable {
  const SIZE = 1;

	public function __construct($InitialValue = 0) {
		$this->setValue($InitialValue);
	}

	public function readFromBytes($Bytes, $Index = null){
		if (is_null($Index)) $Index = 0;
    if ($Index < strlen($Bytes)) {
      $this->Value = unpack('CValue', substr($Bytes, $Index))["Value"];
      return self::SIZE;
    }
    else
      return 0;
	}

	public function writeToBytes(&$Bytes, $Index = null){
		if (is_null($Index)) $Index = strlen($Bytes);
		$Bytes = substr_replace($Bytes, pack('C', $this->Value), $Index, self::SIZE);
		return self::SIZE;
	}
}

class_alias("uint8_t", "CChar");
class_alias("uint8_t", "CUChar");
class_alias("uint8_t", "CByte");

class int16_t extends CSimpleVariable {
  const SIZE = 2;

	public function __construct($InitialValue = 0) {
		$this->setValue($InitialValue);
	}

	public function readFromBytes($Bytes, $Index = null){
		if (is_null($Index)) $Index = 0;
    if ($Index < strlen($Bytes)) {
      $this->Value = unpack('sValue', substr($Bytes, $Index))["Value"];
      return self::SIZE;
    }
    else
      return 0;
	}

	public function writeToBytes(&$Bytes, $Index = null){
		if (is_null($Index)) $Index = strlen($Bytes);
		$Bytes = substr_replace($Bytes, pack('s', $this->Value), $Index, self::SIZE);
		return self::SIZE;
	}
}

class_alias("int16_t", "CShort");

class uint16_t extends CSimpleVariable {
  const SIZE = 2;

	public function __construct($InitialValue = 0) {
		$this->setValue($InitialValue);
	}

	public function readFromBytes($Bytes, $Index = null){
		if (is_null($Index)) $Index = 0;
    if ($Index < strlen($Bytes)) {
      $this->Value = unpack('SValue', substr($Bytes, $Index))["Value"];
      return self::SIZE;
    }
    else
      return 0;
	}

	public function writeToBytes(&$Bytes, $Index = null){
		if (is_null($Index)) $Index = strlen($Bytes);
		$Bytes = substr_replace($Bytes, pack('S', $this->Value), $Index, self::SIZE);
		return self::SIZE;
	}
}

class_alias("uint16_t", "CWord");
class_alias("uint16_t", "CUShort");


class int32_t extends CSimpleVariable {
  const SIZE = 4;

	public function __construct($InitialValue = 0) {
		$this->setValue($InitialValue);
	}

	public function readFromBytes($Bytes, $Index = null){
		if (is_null($Index)) $Index = 0;
    if ($Index < strlen($Bytes)) {
      $this->Value = unpack('lValue', substr($Bytes, $Index))["Value"];
      return self::SIZE;
    }
    else
      return 0;
	}

	public function writeToBytes(&$Bytes, $Index = null){
		if (is_null($Index)) $Index = strlen($Bytes);
		$Bytes = substr_replace($Bytes, pack('l', $this->Value), $Index, self::SIZE);
		return self::SIZE;
	}
}

class_alias("int32_t", "CInt");
class_alias("int32_t", "CLong");


class uint32_t extends CSimpleVariable {
  const SIZE = 4;

	public function __construct($InitialValue = 0) {
		$this->setValue($InitialValue);
	}

	public function readFromBytes($Bytes, $Index = null){
		if (is_null($Index)) $Index = 0;
    if ($Index < strlen($Bytes)) {
      $this->Value = unpack('LValue', substr($Bytes, $Index))["Value"];
      return self::SIZE;
    }
    else
      return 0;
	}

	public function writeToBytes(&$Bytes, $Index = null){
		if (is_null($Index)) $Index = strlen($Bytes);
		$Bytes = substr_replace($Bytes, pack('L', $this->Value), $Index, self::SIZE);
		return self::SIZE;
	}
}

class_alias("uint32_t", "CUInt");
class_alias("uint32_t", "CLongWord");

class CStruct extends CVariable {
	protected $Members = array(); //of CVariable

	protected function getValue() {
		$Value = array();
		foreach($this->Members as $Name => $Member)
		  $Value[$Name] = $Member->getValue();
		return $Value;
	}

	protected function setValue($Value) {
		if (is_array($Value)) {
			foreach($Value as $Name => $NewValue) {
				if (array_key_exists($Name, $this->Members))
					$this->Members[$Name]->setValue($NewValue);
			}
		}
		else
		  throw new Exception("Please supply a valid array");
	}

	public function readFromBytes($Bytes, $Index = null) {
		$BytesProcessed = 0;
		$BytesProcessedTotal = 0;

		if (is_null($Index)) $Index = 0;

		foreach($this->Members as $Name => $Member) {
      if ($Index < strlen($Bytes)) {
        $BytesProcessed = $Member->readFromBytes($Bytes, $Index);
        $BytesProcessedTotal += $BytesProcessed;
        $Index += $BytesProcessed;
      }
      else
        break;
		}

		return $BytesProcessedTotal;
	}

	public function writeToBytes(&$Bytes, $Index = null) {
		$BytesProcessed = 0;
		$BytesProcessedTotal = 0;

		if (is_null($Index)) $Index = strlen($Bytes);

		foreach($this->Members as $Member) {
			$BytesProcessed = $Member->writeToBytes($Bytes, $Index);
			$BytesProcessedTotal += $BytesProcessed;
			$Index += $BytesProcessed;
		}

		return $BytesProcessedTotal;
	}

	public function __get($Name) {
		if (array_key_exists($Name, $this->Members)) {
      if ($this->Members[$Name] instanceof CSimpleVariable)
        //shortcut
        return $this->Members[$Name]->__Value;
      else
        return $this->Members[$Name];
    }
		else
			return parent::__get($Name);
	}

	public function __set($Name, $NewValue) {

		if (array_key_exists($Name, $this->Members)) {
      //shortcut
      $this->Members[$Name]->__Value = $NewValue;
    }
		else
			parent::__set($Name, $NewValue);
	}
}

class CArray extends CVariable implements Countable, ArrayAccess {
	protected $Items = array(); //of CVariable

	public function __construct($Type, $Dimensions, $InitialValues = null) {
		$MaxIndex = array_shift($Dimensions);

		for($idx = 0; $idx < $MaxIndex; $idx++) {
			if (count($Dimensions) == 0) {
				if (is_array($InitialValues))
				  $this->Items[] = new $Type($InitialValues[$idx]);
				else
					$this->Items[] = new $Type();
			}
			else
				$this->Items[] = new CArray($Type, $Dimensions, $InitialValues[$idx]);
		}
	}

	protected function getValue() {
		$Value = array();
		foreach($this->Items as $Item) {
				$Value[] = $Item->__Value;
		}
		return $Value;
	}

	protected function setValue($Value) {
		foreach($Value as $idx => $NewValue) {
			if (array_key_exists($idx, $this->Items))
				$this->Items[$idx]->__Value = $NewValue;
			else
				throw new Exception("Please supply a valid array");
		}
	}

	public function readFromBytes($Bytes, $Index = null) {
		if (is_null($Index)) $Index = 0;
		$BytesProcessed = 0;
		$BytesProcessedTotal = 0;

		foreach($this->Items as $Key => $Item) {
      if ($Index < strlen($Bytes)) {
        $BytesProcessed = $Item->readFromBytes($Bytes, $Index);
        $BytesProcessedTotal += $BytesProcessed;
        $Index += $BytesProcessed;
      }
      else
        break;
		}

		return $BytesProcessedTotal;
	}

	public function writeToBytes(&$Bytes, $Index = null) {
		if (is_null($Index)) $Index = strlen($Bytes);
		$BytesProcessed = 0;
		$BytesProcessedTotal = 0;

		foreach($this->Items as $Item) {
		  $BytesProcessed = $Item->writeToBytes($Bytes, $Index);
			$BytesProcessedTotal += $BytesProcessed;
			$Index += $BytesProcessed;
		}

		return $BytesProcessedTotal;
	}

  public function count(): int
  {
    return count($this->Items);
  }

  public function offsetExists($offset): bool
  {
    if (is_int($offset))
      return array_key_exists($offset, $this->Items);
    else
      throw new Exception("Invalid offset");
  }

  #[ReturnTypeWillChange] public function &offsetGet($offset) {
    if ($this->offsetExists($offset)) {
      $Item = $this->Items[$offset];
      $RetVal = null;

      if ($Item instanceof CSimpleVariable)
        $RetVal = $Item->__Value;
      else
        $RetVal = $Item;
    }
    else
      $RetVal = null;

    return $RetVal;
  }

  #[ReturnTypeWillChange] public function offsetSet($offset, $value) {
    if ($this->offsetExists($offset)) {
      $Item = $this->Items[$offset];

      if ($Item instanceof CSimpleVariable)
        $Item->__Value = $value;
      else if ($value instanceof CVariable)
        $this->Items[$offset] = $value;
      else
        throw new Exception("Can not assign '{$value}' to item '{$offset}'");
    }
  }

  #[ReturnTypeWillChange] public function offsetUnset($offset) {
  }
}

class tm extends CStruct {
  const SIZE = CInt::SIZE * 9;

  public function __construct() {
    $this->Members["tm_sec"] = new CInt();   //int	seconds after the minute	0-60*
    $this->Members["tm_min"] = new CInt(); 	//int	minutes after the hour	0-59
    $this->Members["tm_hour"] = new CInt();  //int	hours since midnight	0-23
    $this->Members["tm_mday"] = new CInt();  //int	day of the month	1-31
    $this->Members["tm_mon"] = new CInt();   //int	months since January	0-11
    $this->Members["tm_year"] = new CInt();  //int	years since 1900
    $this->Members["tm_wday"] = new CInt();  //int	days since Sunday	0-6
    $this->Members["tm_yday"] = new CInt();  //int	days since January 1	0-365
    $this->Members["tm_isdst"] = new CInt(); //int	Daylight Saving Time flag
  }
}

class timeval extends CStruct {
  const SIZE = CLong::SIZE * 2;

  public function __construct() {
    $this->Members["tv_sec"] = new CLong();
    $this->Members["tv_usec"] = new CLong();
  }
}

?>
