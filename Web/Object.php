<?php

class Object
{
	/**
	* @ignore
	*/
	function &__get($nm)
	{
		$ret = null;
		$func = 'Get' . $nm;
		if(method_exists($this, $func))
			$ret = $this->$func();
		else
		{
			$func = 'get' . $nm;
			if(method_exists($this, $func))
				$ret = $this->$func();
			/*elseif(strpos($nm, "CSS") == 0 && $nm != "CSSFile")
				$ret = $this->CSSPropertyArray[str_replace("_", "-", str_replace("CSS", "", $nm))];*/
		}
		return $ret;
		//else 
			//if(array_key_exists($nm, $this->PublicProperties))
				//return $this->PublicProperties[$nm];
			//else 
			//	return null;
	}
	/**
	* @ignore
	*/
	function __set($nm, $val)
	{
		$func = 'Set' . $nm;
		if(method_exists($this, $func))
			return $this->$func($val);
		else 
		{
			$func = 'set' . $nm;
			if(method_exists($this, $func))
				return $this->$func($val);
			/*elseif(strpos($nm, "CSS") === 0 && $nm != "CSSFile")
			{
				if($this->CSSPropertyArray == null)
					$this->CSSPropertyArray = array();
				$this->CSSPropertyArray[str_replace("_", "-", str_replace("CSS", "", $nm))] = $val;
			}*/
			
		}
		return $val;
	}
}

?>