<?php
/**
 * @ignore
 */
final class NolohInternal
{
	private static $SessionState;

	private function __construct(){}

	public static function Queues()
	{
		NolohInternal::ResetSecureValuesQueue();
		NolohInternal::LinkTokensQueue();
		NolohInternal::ControlQueue();
		NolohInternal::SetPropertyQueue();
		NolohInternal::FunctionQueue();
		NolohInternal::ClientEventQueue();
	}
	
	public static function NonstandardShowQueues()
	{
		//return;
		//NolohInternal::LinkTokensQueue();
		$root = $_SESSION['_NControlQueueRoot'];
		$deep = $_SESSION['_NControlQueueDeep'];
		$prop = $_SESSION['_NPropertyQueue'];
		foreach($_SESSION['_NControlQueueRoot'] as $id => $show)
			self::NonStandardShowHelper($id, $show);
		//self::ControlQueue();
		$_SESSION['_NControlQueueRoot'] = $root;
		$_SESSION['_NControlQueueDeep'] = $deep;
		$_SESSION['_NPropertyQueue'] = $prop;
	}
	
	public static function NonStandardShowHelper($id, $show)
	{
		$obj = GetComponentById($id);
		if($show && $obj)
		{
			if($obj->GetShowStatus()===0)
				$obj->Show();
			if(!empty($_SESSION['_NControlQueueDeep'][$id]))
				foreach($_SESSION['_NControlQueueDeep'][$id] as $innerId => $innerShow)
					self::NonStandardShowHelper($innerId, $innerShow);
			}
	}
	
	public static function ControlQueue()
	{
		foreach ($_SESSION['_NControlQueueRoot'] as $objId => $bool)
		{
			self::ShowControl($objId, $bool);
		}
		if(isset($GLOBALS['_NAddedSomething']))
		{
			AddScript('_NQ()', Priority::High);
		}
	}

	public static function ShowControl($id, $bool)
	{
		$control = GetComponentById($id);
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
		if(isset($_SESSION['_NControlQueueDeep'][$id]))
		{
			//while (list($childObjId, $bool) = each($_SESSION['_NControlQueueDeep'][$id]))
			foreach ($_SESSION['_NControlQueueDeep'][$id] as $childObjId => $bool)
			{
				self::ShowControl($childObjId/*, $control*/, $bool);
			}
			unset($_SESSION['_NControlQueueDeep'][$id]);
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
			ClientScript::Add('_NAdd(\''.$addTo.'\',\''.$tag.'\',\''.$objId.'\',['.$properties.'],\''.$_SESSION['_NControlInserts'][$objId].'\');', Priority::High);
//			AddScript('_NAdd(\''.$addTo.'\',\''.$tag.'\',\''.$objId.'\',['.$properties.'],\''.$_SESSION['_NControlInserts'][$objId].'\')', Priority::High);
			unset($_SESSION['_NControlInserts'][$objId]);
		}
		else
//			AddScript('_NAdd(\''.$addTo.'\',\''.$tag.'\',\''.$objId.'\',['.$properties.'])', Priority::High);
			ClientScript::Add('_NAdd(\''.$addTo.'\',\''.$tag.'\',\''.$objId.'\',['.$properties.']);', Priority::High);
		$GLOBALS['_NAddedSomething'] = true;
	}

	public static function Bury($obj)
	{
		ClientScript::Add('_NRem(\''.$obj->Id.'\');', Priority::High);
//		AddScript('_NRem(\''.$obj->Id.'\')', Priority::High);
	}

	public static function Resurrect($obj)
	{
//		AddScript('_NRes(\''.$obj->Id.'\',\''.($obj->GetBuoyant() ? $_SESSION['_NStartUpPageId'] : $obj->GetParent()->GetAddId($obj)).'\')', Priority::High);
		ClientScript::Add('_NRes(\''.$obj->Id.'\',\''.($obj->GetBuoyant() ? $_SESSION['_NStartUpPageId'] : $obj->GetParent()->GetAddId($obj)).'\');', Priority::High);
	}

    public static function Adoption($obj)
    {
        if(!$obj->GetBuoyant())
            ClientScript::Add('_NAdopt(\''.$obj->Id.'\',\'' . $obj->GetParent()->GetAddId($obj) . '\');', Priority::High);
//            AddScript('_NAdopt(\''.$obj->Id.'\',\'' . $obj->GetParent()->GetAddId($obj) . '\')', Priority::High);
        $GLOBALS['_NAddedSomething'] = true;
		//unset($_SESSION['_NControlQueue'][$obj->Id]);
		if(isset($_SESSION['_NControlQueueRoot'][$obj->Id]))
			unset($_SESSION['_NControlQueueRoot'][$obj->Id]);
    }

	public static function GetPropertiesString($objId, $nameValPairs=array())
	{
		$nameValPairsString = '';
//		if(count($nameValPairs) === 0 && isset($_SESSION['_NPropertyQueue'][$objId]))
		if(!$nameValPairs && isset($_SESSION['_NPropertyQueue'][$objId]))
			$nameValPairs = $_SESSION['_NPropertyQueue'][$objId];
		foreach($nameValPairs as $name => $val)
		{
			if(is_string($val))
				$nameValPairsString .= '\''.$name.'\',\''.addslashes($val).'\',';
			elseif(is_numeric($val))
				$nameValPairsString .= '\''.$name.'\','.$val.',';
			elseif(is_array($val))
            {
                    if(!isset($obj))
                    	$obj = Component::Get($objId);
					$nameValPairsString .= '\''.$name.'\',' . call_user_func_array(array(&$obj, array_shift($val)), $val) . ',';
            }
			elseif(is_bool($val))
				$nameValPairsString .= '\''.$name.'\','.($val?'true':'false').',';
			elseif($val === null)
			{
				$splitStr = explode(' ', $name);
				$nameValPairsString .= '\''.$splitStr[0].'\',\'\',';
			}
			//elseif(is_object($val))									// EMBEDS!
			//	$nameValPairsString .= '\''.$name.'\',\''.$val->GetInnerString().'\',';
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
//				AddScript('_NSetP(\''.$objId.'\',['.self::GetPropertiesString($objId, $nameValPairs).'])');
				ClientScript::Add('_NSetP(\''.$objId.'\',['.self::GetPropertiesString($objId, $nameValPairs).']);');
			else
			{
				$splitStr = explode('i', $objId, 2);
				$markupPanel = GetComponentById($splitStr[0]);
				if($markupPanel!==null && $markupPanel->GetShowStatus())
				{
					ClientScript::AddNOLOHSource('Eventee.js');
					$nameValPairsString = '';
					foreach($nameValPairs as $name => $val)
						$nameValPairsString .= '\''.$name.'\',\''.($name=='href'?$val:$markupPanel->GetEventString($val, $objId)).'\',';
//					AddScript('_NEvteeSetP(\''.$objId.'\',['.rtrim($nameValPairsString,',').'])');
					ClientScript::Add('_NEvteeSetP(\''.$objId.'\',['.rtrim($nameValPairsString,',').']);');
					//Might fix the duplicate eventee set issues? - Asher
					unset($_SESSION['_NPropertyQueue'][$objId]);
				}
				unset($markupPanel);
			}
		}
	}

	public static function SetProperty($name, $value, $obj)
	{
        $objId = is_object($obj) ? $obj->Id : $obj;
		if(!isset($GLOBALS['_NQueueDisabled']) || $GLOBALS['_NQueueDisabled'] !== $objId)
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
							if($val[0] === null)
								AddScript($idx, $val[1]);
							else
								AddScript($idx.'('.implode(',',$val[0]).')', $val[1]);
						else
							if($val[1] === null)
								AddScript($val[0], $val[2]);
							else
								AddScript($val[0].'('.implode(',',$val[1]).')', $val[2]);
					unset($_SESSION['_NFunctionQueue'][$objId]);
				}
		}
	}

	public static function ClientEventQueue()
	{
		if(isset($GLOBALS['_NClientEventExecs']))
		{
			$count = count($GLOBALS['_NClientEventExecs']);
			for($i=0; $i<$count; ++$i)
				if($GLOBALS['_NClientEventExecs'][$i]->GetShowStatus())
					$GLOBALS['_NClientEventExecs'][$i]->AddToScript();
		}
	}

	public static function LinkTokensQueue()
	{
		if(isset($GLOBALS['_NQueuedLinks']))
			foreach($GLOBALS['_NQueuedLinks'] as $id => $nothing)
				GetComponentById($id)->UpdateTokens();
	}
	
	public static function ResetSecureValuesQueue()
	{
		if(isset($GLOBALS['_NResetSecureValues']))
			foreach($GLOBALS['_NResetSecureValues'] as $id => $value)
			{
				$obj = &Component::Get($id);
				if($obj->Secure === true)
				{
					if(isset($_SESSION['_NPropertyQueue'][$id]))
					{
						$oldQueue = &$_SESSION['_NPropertyQueue'][$id];
						unset($_SESSION['_NPropertyQueue'][$id]);
					}
					$obj->SetValue($value);
					if(isset($_SESSION['_NPropertyQueue'][$id]))
						$prop = key($_SESSION['_NPropertyQueue'][$id]);
					if($oldQueue)
					{
						$_SESSION['_NPropertyQueue'][$id] = &$oldQueue;
						unset($oldQueue);
					}
					else
						unset($_SESSION['_NPropertyQueue'][$id]);
					if($prop)
					{
						ClientScript::AddNOLOHSource('Secure.js');
						ClientScript::Queue($obj, '_NSecRemind', array($obj, $prop));
						unset($prop);
					}
				}
				elseif($obj->Secure === 'Reset')
				{
					$GLOBALS['_NQueueDisabled'] = $id;
					$obj->SetValue($value);
					$GLOBALS['_NQueueDisabled'] = null;
				}
			}
	}

	public static function ResetSession()
	{
		global $OmniscientBeing;

		foreach (unserialize(static::$SessionState) as $key => $val)
		{
			$_SESSION[$key] = $val;
		}

		++$_SESSION['_NVisit'];
		$_SESSION['_NOmniscientBeing'] = defined('FORCE_GZIP') ? gzcompress(serialize($OmniscientBeing), 1) : serialize($OmniscientBeing);
	}

	public static function SaveSessionState()
	{
		$session = array();
		foreach ($_SESSION as $key => $value)
		{
			$session[$key] = $value;
		}

		unset($session['_NOmniscientBeing']);
		static::$SessionState = serialize($session);
	}
}
?>