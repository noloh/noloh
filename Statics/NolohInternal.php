<?php
final class NolohInternal
{
	private function NolohInternal(){}

	public static function ShowQueue()
	{
		foreach($_SESSION['NOLOHControlQueue'] as $objId => $whatBool)
			self::ShowControl(GetComponentById($objId), $whatBool);
	}
	
	public static function ShowControl($control, $whatBool)
	{
		//if(isset($control))
		//{
			$parent = $control->Parent;
			if(!$parent)
			{
				$splitStr = explode("i", $control->ParentId, 2);
				$parent = GetComponentById($splitStr[0]);
			}
			if($parent->GetShowStatus()!==0)
			{
				if($whatBool)
				{
					if($control->GetShowStatus()===0)
						$control->Show();
					elseif($control->GetShowStatus()===2)
						$control->Resurrect();
				}
				elseif($control->GetShowStatus()!==0)
					$control->Hide();
			}
			elseif(isset($_SESSION['NOLOHControlQueue'][$parent->DistinctId]) && $_SESSION['NOLOHControlQueue'][$parent->DistinctId] && func_num_args()==2)
			{
				self::ShowControl($parent, true);
				self::ShowControl($control, $whatBool, false);
			}
		//}
		//else
		//	Alert("whatBool=" . ($whatBool?"TRUE":"FALSE"));
	}
	
	public static function Show($whatTag, $initialProperties, $whatObj, $addTo = null)
	{
		$parent = $whatObj->GetParent();

		$propertiesString = self::GetPropertiesString($whatObj->DistinctId);
		if($propertiesString != "")
			$initialProperties .= "," . $propertiesString;
			
		if($addTo == null)
			$addTo = $parent ? $parent->GetAddId($whatObj) : $whatObj->ParentId;
		AddScript("_NAdd('$addTo','$whatTag',Array($initialProperties))", Priority::High);
	}
	
	public static function Hide($whatObj)
	{
		AddScript("_NRem('$whatObj->DistinctId');", Priority::High);
	}
	
	public static function Resurrect($whatObj)
	{
		AddScript("_NRes('$whatObj->DistinctId','".self::GetImmediateParentId($whatObj)."');", Priority::High);
	}
	
	public static function GetPropertiesString($objId, $nameValPairs=array())
	{
		//$obj = GetComponentById($objId);
		$nameValPairsString = "";
		if(count($nameValPairs) == 0 && isset($_SESSION['NOLOHPropertyQueue'][$objId]))
			$nameValPairs = $_SESSION['NOLOHPropertyQueue'][$objId];
		foreach($nameValPairs as $name => $val)
		{
			if(is_string($val))
				$nameValPairsString .= "'$name','".addslashes($val)."',";
			elseif(is_numeric($val))
				$nameValPairsString .= "'$name',".$val.",";
			elseif(is_array($val))									// EVENTS!
				$nameValPairsString .= "'$name','".GetComponentById($objId)->GetEventString($val[0])."',";
			elseif(is_bool($val))
				$nameValPairsString .= "'$name',".($val?"true":"false").",";
			elseif($val === null)
			{
				$splitStr = explode(" ", $name);
				$nameValPairsString .= "'{$splitStr[0]}','',";
			}
			elseif(is_object($val))									// EMBEDS!
				$nameValPairsString .= "'$name','".$val->GetInnerString()."',";
		}
		unset($_SESSION['NOLOHPropertyQueue'][$objId]);
		return rtrim($nameValPairsString, ",");
		//return substr($nameValPairsString, 0, strlen($nameValPairsString)-1);
	}
	
	public static function SetPropertyQueue()
	{
		foreach($_SESSION['NOLOHPropertyQueue'] as $objId => $nameValPairs)
		{
			$obj = &GetComponentById($objId);
			if($obj!=null && $obj->GetShowStatus())
				AddScript("_NSetP('$objId', new Array(".self::GetPropertiesString($objId, $nameValPairs)."))");
			else 
			{
				$splitStr = explode("i", $objId, 2);
				$markupPanel = &GetComponentById($splitStr[0]);
				if($markupPanel!=null && $markupPanel->GetShowStatus())
				{
					$nameValPairsString = "";
					foreach($nameValPairs as $name => $eventType)
						$nameValPairsString .= "'$name','".$markupPanel->GetEventString($eventType, $objId)."',";
					AddScript("_NSetP('$objId', new Array(".rtrim($nameValPairsString,",")."))");
				}
			}
		}
	}
	
	public static function SetProperty($name, $value, $whatObj)
	{
		if(!isset($GLOBALS["PropertyQueueDisabled"]))
		{
			$objId = is_object($whatObj) ? $whatObj->DistinctId : $whatObj;
			if(!isset($_SESSION['NOLOHPropertyQueue'][$objId]))
				$_SESSION['NOLOHPropertyQueue'][$objId] = array();
			$_SESSION['NOLOHPropertyQueue'][$objId][$name] = $value;
		}
	}
	
	public static function FunctionQueue()
	{
		foreach($_SESSION['NOLOHFunctionQueue'] as $objId => $nameParam)
		{
			$obj = &GetComponentById($objId);
			if($obj != null)
			//{
				if($obj->GetShowStatus())
				{
					foreach($nameParam as $idx => $val)
						if(is_string($idx))
							AddScript($idx."(".implode(",",$val[0]).")", $val[1]);
						else
							AddScript($val[0]."(".implode(",",$val[1]).")", $val[2]);
					unset($_SESSION['NOLOHFunctionQueue'][$objId]);
				}
			//}
			//else
			//	Alert("Null Object: " . serialize($nameParam));
		}
	}
}
?>