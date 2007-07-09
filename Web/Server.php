<?php

function SetStartUpPage($whatClassName, $unsupportedURL="", $URLTokenMode=URL::Display)
{
	$thisApp = new Application();
	
	if(isset($_GET['NOLOHImage']))
		Image::MagicGeneration($_GET['NOLOHImage'], $_GET['Class'], $_GET['Function']);
	elseif(isset($_GET['NOLOHFileUpload']))
	{
		FileUpload::ShowInside($_GET['NOLOHFileUpload'], $_GET['Width'], $_GET['Height']);
		if(isset($_FILES['NOLOHFile']) && $_FILES['NOLOHFile']['tmp_name']!="")
		{
			rename($_FILES['NOLOHFile']['tmp_name'], $_FILES['NOLOHFile']['tmp_name']."N");
			$_SESSION['NOLOHFiles'][$_GET['NOLOHFileUpload']] = $_FILES['NOLOHFile'];
			$_SESSION['NOLOHFiles'][$_GET['NOLOHFileUpload']]['tmp_name'] .= "N";
		}
	}
	else 
	{
		if(isset($_SESSION['NOLOHVisit']) || isset($_POST['NOLOHVisit']))
		{
			if(!isset($_SESSION['NOLOHVisit']) || $_SESSION['NOLOHURL'] != $_SERVER['PHP_SELF'] || 
			  (empty($_POST['NOLOHVisit']) && !isset($_POST['NOLOHServerEvent']) && $_SESSION['NOLOHVisit']>=0) ||
			  (!empty($_POST['NOLOHVisit']) && $_SESSION['NOLOHVisit'] != $_POST['NOLOHVisit']))
			{
				if(isset($_POST['NOLOHServerEvent']) || !isset($_SESSION['NOLOHVisit']) || isset($_GET["NWidth"]))
					ResetApp();
				session_destroy();
				session_unset(); 
				SetStartUpPage($whatClassName);
				return;
			}
			$GLOBALS["NOLOHURLTokenMode"] = $URLTokenMode;
			if(isset($_SESSION["NOLOHOmniscientBeing"]))
				TheComingOfTheOmniscientBeing($thisApp);
			if(!empty($_POST['NOLOHClientChanges']))
				HandleClientChanges();
			if(!empty($_POST['NOLOHFileUploadId']))
				GetComponentById($_POST['NOLOHFileUploadId'])->File = &$_FILES['NOLOHFileUpload'];
			foreach($_SESSION['NOLOHFiles'] as $key => $val)
				GetComponentById($key)->File = new File($val);
			if(!empty($_POST['NOLOHServerEvent']))
				HandleServerEvent();
			foreach($_SESSION['NOLOHFiles'] as $key => $val)
			{
				unlink($_SESSION['NOLOHFiles'][$key]['tmp_name']);
				GetComponentById($key)->File = null;
				unset($_SESSION['NOLOHFiles'][$key]);
			}
			$thisApp->Run();
		}
		else
			HandleFirstRun($whatClassName, $thisApp, $unsupportedURL);
	}
}

function HandleFirstRun($whatClassName, &$thisApp, $unsupportedURL)
{
	$_SESSION['NOLOHControlQueue'] = array();
	$_SESSION['NOLOHFunctionQueue'] = array();
	$_SESSION['NOLOHPropertyQueue'] = array();
	$_SESSION['NOLOHScript'] = array("", "", "");
	//$_SESSION['NOLOHSrcScript'] = "";
	$_SESSION['NOLOHScriptSrcs'] = array();
	$_SESSION['NOLOHFiles'] = array();
	$_SESSION['NOLOHNumberOfComponents'] = 0;
	$_SESSION['NOLOHVisit'] = -1;
	$_SESSION['NOLOHGarbage'] = array();
	$_SESSION['NOLOHStartUpPageClass'] = $whatClassName;
	$_SESSION['NOLOHURL'] = $_SERVER['PHP_SELF'];
	DeclareGlobal("HighestZIndex", 0);
	DeclareGlobal("LowestZIndex", 0);
	UserAgentDetect::LoadInformation();
	if($_SESSION["NOLOHBrowser"] == "other" && $_SESSION["NOLOHOS"] == "other")
	{
		
	}
	else 
		WebPage::SkeletalShow($unsupportedURL);
}

function TheComingOfTheOmniscientBeing(&$thisApp)
{
	global $OmniscientBeing;
	$OmniscientBeing = unserialize($_SESSION['NOLOHOmniscientBeing']);
	unset($_SESSION['NOLOHOmniscientBeing']);
	foreach($_SESSION["NOLOHGarbage"] as $id => $nothing)
	{
		$control = &$GLOBALS["OmniscientBeing"][$id];
		if(!isset($_SESSION["NOLOHGarbage"][$control->ParentId]) && $control->GetShowStatus()!==0 && $control instanceof Control)
			AddScript("_NAsc('$id')");
		unset($GLOBALS["OmniscientBeing"][$id]);
	}
	$_SESSION["NOLOHGarbage"] = array();
	//foreach($OmniscientBeing as $key => $val)
	//	$val->RestoreValues();
	$thisApp->WebPage = &GetComponentById($_SESSION['NOLOHStartUpPageId']);
}

function HandleClientChanges()
{
	$GLOBALS["PropertyQueueDisabled"] = true;
	$runThisString = "";
	$splitChanges = explode("~d0~", $_POST['NOLOHClientChanges']);
	$numChanges = count($splitChanges);
	for($i = 0; $i < $numChanges; $i++)
	{
		$runThisString = 'GetComponentById($splitChange[0])->';
		$splitChange = explode("~d1~", $splitChanges[$i]);
		switch($splitChange[1])
		{
			// Strings
			case "ViewMonth":
			case "ViewYear":
			case "Date":
			case "Month":
			case "Year":
			case "Text":
			case "Src":
			case "BackColor":
			case "Color":
			case "ZIndex":
			case "SelectedTab":
				$runThisString .= $splitChange[1] . ' = "' . $splitChange[2] . '";';
				break;
			// Functions
			case "KillLater":
				if(GetComponentById($splitChange[0]) != null)
					$runThisString .= 'Close();';
				else
					$runThisString = "";
				break;
			//case "SelectedTab":
			//	$runThisString .= 'SelectedIndex = GetComponentById($splitChange[0])->TabControlBar->Controls->IndexOf(GetComponentById($splitChange[2]));';
				//break;
			// Booleans
			//case "Checked":
			//case "ClientVisible":
				//$runThisString .= $splitChange[1] . ' = ' . $splitChange[2] . ';';
				//break;
			// Explode string to array
			case "Items":
			case "SelectedIndices":
				$tmp = strpos($splitChange[1], "->");
				$runThisString .= stripslashes($splitChange[1]) . ' = Explode' . ($tmp===false?$splitChange[1]:substr($splitChange[1], 0, $tmp)) . '("' . $splitChange[2] . '");';
				break;
			default:
				$runThisString .= $splitChange[1] . ' = ' . $splitChange[2] . ';';
		}
		//echo $runThisString;
		eval($runThisString);
	}
	unset($GLOBALS["PropertyQueueDisabled"]);
}

function HandleServerEvent()
{
	if(isset($_POST['NOLOHKey']))
		Event::$Key = $_POST['NOLOHKey'];
	if(isset($_POST['NOLOHCaught']))
		Event::$Caught = ExplodeDragCatch($_POST['NOLOHCaught']);
	Event::$MouseX = $_POST["NOLOHMouseX"];
	Event::$MouseY = $_POST["NOLOHMouseY"];
	$splitEvent = explode("@", $_POST['NOLOHServerEvent']);
	$obj = GetComponentById($splitEvent[1]);
	if($obj != null)
		return $obj->{$splitEvent[0]}->Exec($execClientEvents=false);
	else 
	{
		$splitStr = explode("e", $splitEvent[1], 2);
		return GetComponentById($splitStr[0])->ExecEvent($splitEvent[0], $splitEvent[1]);
	}
	//$runThisString = 'return GetComponentById($splitEvent[1])->' . $splitEvent[0] . '->Exec(false);';
	//eval($runThisString);
}

function ExplodeDragCatch($ObjectsString)
{
	$Objs = array();
	$ObjectsIdArray = explode(",", $ObjectsString);
	$ObjectsCount = count($ObjectsIdArray);
	for($i=0; $i<$ObjectsCount; $i++)
		$Objs[] = GetComponentById($ObjectsIdArray[$i]);
	return $Objs;
}

function ExplodeItems($OptionsString)
{
	$Items = new ArrayList();
	$OptionsArray = explode("~d3~", $OptionsString);

	for($i=0; $i<sizeof($OptionsArray); $i++)
	{
		$Option = split("~d2~", $OptionsArray[$i]);
		$Items->Add(new Item($Option[0], $Option[1]));
	}
	return $Items;
}

function ExplodeSelectedIndices($IndicesString)
{
	return explode("~d2~", $IndicesString);
}
	
?>