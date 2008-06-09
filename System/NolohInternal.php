<?php
/**
 * @ignore
 */
final class NolohInternal
{
	private function NolohInternal(){}

	public static function ShowQueue()
	{
		//foreach($_SESSION['_NControlQueue'] as $objId => $bool)
        while (list($objId, $bool) = each($_SESSION['_NControlQueue']))
			self::ShowControl(GetComponentById($objId), $bool);
	}

	public static function ShowControl($control, $bool)
	{
		//if(isset($control))
		//{
			$parent = $control->GetParent();
			if(!$parent)
			{
				$splitStr = explode('i', $control->GetParentId(), 2);
				$parent = GetComponentById($splitStr[0]);
				if(!$parent)
				{
					$control->SecondGuessParent();
					return;
				}
			}
			if($parent->GetShowStatus()!==0)
			{
				if($bool)
				{
					if($control->GetShowStatus()===0)
						$control->Show();
                    elseif($control->GetShowStatus()===1)
                        self::Adoption($control, $parent);
					elseif($control->GetShowStatus()===2)
						$control->Resurrect();
				}
				elseif($control->GetShowStatus()!==0)
					$control->Bury();
			}
			elseif(isset($_SESSION['_NControlQueue'][$parent->Id]) && $_SESSION['_NControlQueue'][$parent->Id] && func_num_args()==2)
			{
				self::ShowControl($parent, true);
				self::ShowControl($control, $bool, false);
			}
		//}
		//else
		//	Alert("whatBool=" . ($bool?"TRUE":"FALSE"));
	}
	
	public static function Show($tag, $initialProperties, $obj, $addTo = null)
	{
		$objId = $obj->Id;
		$parent = $obj->GetParent();

		$propertiesString = self::GetPropertiesString($objId);
		if($propertiesString != '')
			$initialProperties .= ',' . $propertiesString;
			
		if($addTo == null)
			if($obj->GetBuoyant())
			{
				$addTo = 'N1';
				AddScript('StartBuoyant(\''.$objId.'\',\''.$parent->GetAddId($obj).'\')');
				unset($_SESSION['_NFunctionQueue'][$objId]['StopBuoyant']);
			}
			else
				$addTo = $parent ? $parent->GetAddId($obj) : $obj->GetParentId();
		if(isset($_SESSION['_NControlInserts'][$objId]))
		{
			AddScript('_NAdd(\''.$addTo.'\',\''.$tag.'\',Array('.$initialProperties.'),\''.$_SESSION['_NControlInserts'][$objId].'\')', Priority::High);
			unset($_SESSION['_NControlInserts'][$objId]);
		}
		else
			AddScript('_NAdd(\''.$addTo.'\',\''.$tag.'\',Array('.$initialProperties.'))', Priority::High);
	}
	
	public static function Bury($obj)
	{
		AddScript('_NRem(\''.$obj->Id.'\')', Priority::High);
	}
	
	public static function Resurrect($obj)
	{
		AddScript('_NRes(\''.$obj->Id.'\',\''.($obj->GetBuoyant() ? 'N1' : $obj->GetParent()->GetAddId($obj)).'\')', Priority::High);
	}

    public static function Adoption($obj, $parent)
    {
        if(!$obj->GetBuoyant())
            AddScript('_NAdopt(\''.$obj->Id.'\',\'' . $parent->GetAddId($obj) . '\')', Priority::High);
        unset($_SESSION['_NControlQueue'][$obj->Id]);
    }
	
	public static function GetPropertiesString($objId, $nameValPairs=array())
	{
		$nameValPairsString = '';
		if(count($nameValPairs) == 0 && isset($_SESSION['_NPropertyQueue'][$objId]))
			$nameValPairs = $_SESSION['_NPropertyQueue'][$objId];
		foreach($nameValPairs as $name => $val)
		{
			if(is_string($val))
				$nameValPairsString .= '\''.$name.'\',\''.addslashes($val).'\',';
			elseif(is_numeric($val))
				$nameValPairsString .= '\''.$name.'\','.$val.',';
			elseif(is_array($val))									// EVENTS!
			{
				if(isset(Event::$Conversion[$name]))
					$nameValPairsString .= '\''.Event::$Conversion[$name].'\',\''.GetComponentById($objId)->GetEventString($val[0]).'\',';
				else 
					$nameValPairsString .= '\''.$name.'\',' . 'function(event) {' . GetComponentById($objId)->GetEventString($val[0]) . '},';
			}
			elseif(is_bool($val))
				$nameValPairsString .= '\''.$name.'\','.($val?'true':'false').',';
			elseif($val === null)
			{
				$splitStr = explode(' ', $name);
				$nameValPairsString .= '\''.$splitStr[0].'\',\'\',';
			}
			elseif(is_object($val))									// EMBEDS!
				$nameValPairsString .= '\''.$name.'\',\''.$val->GetInnerString().'\',';
		}
		unset($_SESSION['_NPropertyQueue'][$objId]);
		return rtrim($nameValPairsString, ',');
		//return substr($nameValPairsString, 0, strlen($nameValPairsString)-1);
	}
	
	public static function SetPropertyQueue()
	{
		foreach($_SESSION['_NPropertyQueue'] as $objId => $nameValPairs)
		{
			$obj = &GetComponentById($objId);
			if($obj!=null && $obj->GetShowStatus())
				AddScript('_NSetP(\''.$objId.'\',Array('.self::GetPropertiesString($objId, $nameValPairs).'))');
			else 
			{
				$splitStr = explode('i', $objId, 2);
				$markupPanel = &GetComponentById($splitStr[0]);
				if($markupPanel!=null && $markupPanel->GetShowStatus())
				{
					$nameValPairsString = '';
					foreach($nameValPairs as $name => $val)
						$nameValPairsString .= '\''.$name.'\',\''.($name=='href'?$val:$markupPanel->GetEventString($val, $objId)).'\',';
					AddScript('_NSetPEvtee(\''.$objId.'\',Array('.rtrim($nameValPairsString,',').'))');
				}
			}
		}
	}
	
	public static function SetProperty($name, $value, $obj)
	{
        $objId = is_object($obj) ? $obj->Id : $obj;
		if($GLOBALS['_NQueueDisabled'] != $objId)
		{
			if(!isset($_SESSION['_NPropertyQueue'][$objId]))
				$_SESSION['_NPropertyQueue'][$objId] = array();
			$_SESSION['_NPropertyQueue'][$objId][$name] = $value;
		}
	}
	
	public static function FunctionQueue()
	{
		foreach($_SESSION['_NFunctionQueue'] as $objId => $nameParam)
		{
			$obj = &GetComponentById($objId);
			if($obj != null)
			//{
				if($obj->GetShowStatus())
				{
					foreach($nameParam as $idx => $val)
						if(is_string($idx))
							AddScript($idx.'('.implode(',',$val[0]).')', $val[1]);
						else
							AddScript($val[0].'('.implode(',',$val[1]).')', $val[2]);
					unset($_SESSION['_NFunctionQueue'][$objId]);
				}
			//}
			//else
			//	Alert("Null Object: " . serialize($nameParam));
		}
	}
}
?>