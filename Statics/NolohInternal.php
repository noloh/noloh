<?php
final class NolohInternal
{
	private function NolohInternal(){}

	public static function ShowQueue()
	{
		foreach($_SESSION['NOLOHControlQueue'] as $objId => $whatBool)
			NolohInternal::ShowControl(GetComponentById($objId), $whatBool);
		//{
			//$control = &GetComponentById($objId);
			//NolohInternal::ShowControl($control, $whatBool);
		//}
	}
	
	public static function ShowControl($control, $whatBool)
	{
		if(isset($control))
		{
			$parent = $control->Parent;
			//$tmpParent = &$tmpControl->GetParent(array("Control", "WebPage"));
						//if(!($tmpParent instanceof Guardian || $tmpParent instanceof Table || $tmpParent instanceof TableRow || $tmpParent instanceof TableColumn) || $tmpParent->HasShown())
			//if($tmpParent == null)
				//Alert(get_class($tmpControl));
		//else 
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
				NolohInternal::ShowControl($parent, true);
				NolohInternal::ShowControl($control, $whatBool, false);
			}
		}
		//else
		//	Alert("whatBool=" . ($whatBool?"TRUE":"FALSE"));
	}
	/*
	public static function GetImmediateParentId($whatObj)
	{
		$parent = $whatObj->Parent;
		if(!($parent instanceof Control))
			return $parent instanceof WebPage ? $parent->DistinctId : NolohInternal::GetImmediateParentId($parent);
		elseif($parent instanceof TableColumn)
			return $parent->DistinctId . "InnerCol";
		elseif($parent instanceof WindowPanel && !in_array($whatObj, $parent->WindowPanelComponents->Item))
			return $parent->BodyPanel->DistinctId;
		else
			return $parent->DistinctId;
	}
	*/
	public static function Show($whatTag, $initialProperties, $whatObj, $addTo = null)
	{
		$objId = $whatObj->DistinctId;
		$parent = $whatObj->GetParent();
		
		if($addTo == null)
			$addTo = $parent ? $parent->GetAddId($whatObj) : $whatObj->ParentId;
		//	$addTo = NolohInternal::GetImmediateParentId($whatObj);

				
		/*
		elseif($parent instanceof Table)
		{
			if(GetBrowser() == "ie")
			{
				AddScript("document.getElementById('INNERTABLE{$whatObj->ParentId}').appendChild(document.createElement(\"$whatTag\"));SaveControl(\"$objId\")", Priority::High);
				return;
			}
			$addTo = "INNERTABLE" . $whatObj->ParentId;
		}
		elseif($parent instanceof TableRow)
		{
			if(GetBrowser() == "ie")
				AddScript("document.getElementById('{$whatObj->ParentId}').appendChild(document.createElement(\"{$whatTag[0]}\"));" . 
					"document.getElementById('{$whatObj->DistinctId}').appendChild(document.createElement(\"{$whatTag[1]}\"));SaveControl(\"$objId\")", Priority::High);
			else 
				AddScript("document.getElementById('{$whatObj->ParentId}').innerHTML += \"{$whatTag[0]}\";" . 
					"document.getElementById('{$whatObj->DistinctId}').innerHTML += \"{$whatTag[1]}\";SaveControl(\"$objId\")", Priority::High);
			return;
		}
		elseif($parent instanceof TableColumn)
			$addTo = "INNERCOLUMN" . $whatObj->ParentId;
		elseif(!($parent instanceof Control))
			$addTo = $parent->GetParent(array("Control", "WebPage"))->DistinctId;
		else//if($addTo == NolohInternal::TheParent)
			$addTo = $whatObj->ParentId;*/
			
		// Special fixes involving the object
		if($whatObj instanceof RadioButton && GetBrowser()=="ie")
			$whatTag = "<$whatTag name=\"$whatObj->GroupName\">";

		$propertiesString = NolohInternal::GetPropertiesString($objId);
		if($propertiesString != "")
			$initialProperties .= "," . $propertiesString;
		AddScript("_NAdd('$addTo','$whatTag',Array($initialProperties))", Priority::High);
	}
	
	public static function Hide($whatObj)
	{
		//AddScript("document.getElementById('".NolohInternal::GetImmediateParentId($whatObj)."').removeChild(document.getElementById('$whatObj->DistinctId'));", Priority::High);
		//AddScript("_NRem('$whatObj->DistinctId','".NolohInternal::GetImmediateParentId($whatObj)."');", Priority::High);
		AddScript("_NRem('$whatObj->DistinctId');", Priority::High);
	}
	
	public static function Resurrect($whatObj)
	{
		AddScript("_NRes('$whatObj->DistinctId','".NolohInternal::GetImmediateParentId($whatObj)."');", Priority::High);
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
			/*
				if($val instanceof Event)
				{
					$splitStr = explode(" ", $name, 2);
					//$nameValPairsString .= "'{$splitStr[0]}','".$val->GetEventString($splitStr[1], $objId) ."',";
					$nameValPairsString .= "'{$splitStr[0]}','".GetComponentById($objId)->GetEventString($splitStr[1]) ."',";
				}
				elseif($val instanceof EmbedObject)*/
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
				AddScript("_NSetP('$objId', new Array(".NolohInternal::GetPropertiesString($objId, $nameValPairs)."))");
			else 
			{
				$splitStr = explode("e", $objId, 2);
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
			//$_SESSION['NOLOHPropertyQueue'][$objId][$name] = str_replace("'", "\'", $value);
			//$_SESSION['NOLOHPropertyQueue'][$objId][$name] = addslashes($value);
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