<?php
/**
 * @ignore
 */
final class NolohInternal
{
	private function NolohInternal(){}

	public static function ControlQueue()
	{
        while (list($objId, $bool) = each($_SESSION['_NControlQueueRoot']))
			self::ShowControl(GetComponentById($objId), $bool);
		if(isset($GLOBALS['_NAddedSomething']))
			AddScript('_NQ()', Priority::High);
	}

	public static function ShowControl($control, $bool)
	{
		if($bool)
		{
			if($control->GetShowStatus()===0)
				$control->Show();
            elseif($control->GetShowStatus()===1)
            	$control->Adopt();
			elseif($control->GetShowStatus()===2)
				$control->Resurrect();
		}
		elseif($control->GetShowStatus()!==0)
			$control->Bury();
		if(isset($_SESSION['_NControlQueueDeep'][$control->Id]))
		{
			while (list($childObjId, $bool) = each($_SESSION['_NControlQueueDeep'][$control->Id]))
				self::ShowControl(GetComponentById($childObjId)/*, $control*/, $bool);
			unset($_SESSION['_NControlQueueDeep'][$control->Id]);
		}
	}
	
	public static function Show($tag, $initialProperties, $obj, $addTo = null, $newId = null)
	{
		$objId = $obj->Id;

		$setProperties = self::GetPropertiesString($objId);
		if($initialProperties)
			if($setProperties)
				$properties = ltrim($initialProperties, ',') . ',' . $setProperties;
			else 
				$properties = ltrim($initialProperties, ',');
		elseif($setProperties)
			$properties = $setProperties;
		else 
			$properties = '';

		if($addTo === null)
		{
			$parent = $obj->GetParent();
			if($obj->GetBuoyant())
			{
				$addTo = $_SESSION['_NStartUpPageId'];
				AddScript('_NByntSta(\''.$objId.'\',\''.$parent->GetAddId($obj).'\')', Priority::Low);
				unset($_SESSION['_NFunctionQueue'][$objId]['_NByntStp']);
			}
			else
				$addTo = $parent ? $parent->GetAddId($obj) : $obj->GetParentId();
		}
		elseif($newId)
			$objId = $newId;
		if(isset($_SESSION['_NControlInserts'][$objId]))
		{
			AddScript('_NAdd(\''.$addTo.'\',\''.$tag.'\',\''.$objId.'\',['.$properties.'],\''.$_SESSION['_NControlInserts'][$objId].'\')', Priority::High);
			unset($_SESSION['_NControlInserts'][$objId]);
		}
		else
			AddScript('_NAdd(\''.$addTo.'\',\''.$tag.'\',\''.$objId.'\',['.$properties.'])', Priority::High);
		$GLOBALS['_NAddedSomething'] = true;
	}
	
	public static function Bury($obj)
	{
		AddScript('_NRem(\''.$obj->Id.'\')', Priority::High);
	}
	
	public static function Resurrect($obj)
	{
		AddScript('_NRes(\''.$obj->Id.'\',\''.($obj->GetBuoyant() ? $_SESSION['_NStartUpPageId'] : $obj->GetParent()->GetAddId($obj)).'\')', Priority::High);
	}

    public static function Adoption($obj)
    {
        if(!$obj->GetBuoyant())
            AddScript('_NAdopt(\''.$obj->Id.'\',\'' . $obj->GetParent()->GetAddId($obj) . '\')', Priority::High);
        unset($_SESSION['_NControlQueue'][$obj->Id]);
    }
	
	public static function GetPropertiesString($objId, $nameValPairs=array())
	{
		$nameValPairsString = '';
		if(count($nameValPairs) === 0 && isset($_SESSION['_NPropertyQueue'][$objId]))
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
					$nameValPairsString .= '\''.$name.'\',' . '_NEvent(\'' . GetComponentById($objId)->GetEventString($val[0]) . '\',\'' . $objId . '\'),';
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
	}
	
	public static function SetPropertyQueue()
	{
		foreach($_SESSION['_NPropertyQueue'] as $objId => $nameValPairs)
		{
			$obj = &GetComponentById($objId);
			if($obj!==null && $obj->GetShowStatus())
				AddScript('_NSetP(\''.$objId.'\',['.self::GetPropertiesString($objId, $nameValPairs).'])');
			else 
			{
				$splitStr = explode('i', $objId, 2);
				$markupPanel = &GetComponentById($splitStr[0]);
				if($markupPanel!=null && $markupPanel->GetShowStatus())
				{
					AddNolohScriptSrc('Eventee.js');
					$nameValPairsString = '';
					foreach($nameValPairs as $name => $val)
						$nameValPairsString .= '\''.$name.'\',\''.($name=='href'?$val:$markupPanel->GetEventString($val, $objId)).'\',';
					AddScript('_NEvteeSetP(\''.$objId.'\',['.rtrim($nameValPairsString,',').'])');
				}
			}
		}
	}
	
	public static function SetProperty($name, $value, $obj)
	{
        $objId = is_object($obj) ? $obj->Id : $obj;
		if($GLOBALS['_NQueueDisabled'] !== $objId)
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
			if($obj !== null)
				if($obj->GetShowStatus())
				{
					foreach($nameParam as $idx => $val)
						if(is_string($idx))
							AddScript($idx.'('.implode(',',$val[0]).')', $val[1]);
						else
							AddScript($val[0].'('.implode(',',$val[1]).')', $val[2]);
					unset($_SESSION['_NFunctionQueue'][$objId]);
				}
		}
	}
	
	public static function ClientEventQueue()
	{
		$count = count($GLOBALS['_NClientEventExecs']);
		for($i=0; $i<$count; ++$i)
			if($GLOBALS['_NClientEventExecs'][$i]->GetShowStatus())
				$GLOBALS['_NClientEventExecs'][$i]->AddToScript();
	}
}
?>