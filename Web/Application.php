<?php
/**
* @ignore
*/
class Application
{
	public $WebPage;
	
	function Application()
	{
		session_name(hash("md5", $_SERVER['PHP_SELF']));
		ini_set('session.gc_probability', 50);
		session_start();
	}
	
	function Run()
	{
		global $OmniscientBeing;
		if(++$_SESSION['NOLOHVisit']==0)
		{
			if($GLOBALS["NOLOHURLTokenMode"] == 1)
			{
				$_SESSION['NOLOHTokens'] = $_GET;
				unset($_SESSION['NOLOHTokens']["NWidth"], $_SESSION['NOLOHTokens']["NHeight"]);
			}
			elseif($GLOBALS["NOLOHURLTokenMode"] == 2)
			{
				$split = explode("&", base64_decode(key($_GET)));
				$count = count($split);
				for($i=0; $i<$count; $i++)
				{
					$split2 = explode("=", $split[$i]."=");
					$_SESSION['NOLOHTokens'][$split2[0]] = $split2[1];
				}
			}
			$whatClassName = $_SESSION['NOLOHStartUpPageClass'];
			$startUpPage = new $whatClassName();
			$_SESSION['NOLOHStartUpPageId'] = $startUpPage->DistinctId;
			$this->WebPage = $startUpPage;
			$startUpPage->Show();
		}
		
		if(isset($GLOBALS["NOLOHTokenUpdate"]))
			URL::UpdateTokens();
		NolohInternal::ShowQueue();
		NolohInternal::FunctionQueue();
		NolohInternal::SetPropertyQueue();
		print(/*$_SESSION['NOLOHSrcScript'] .*/ "/*~NScript~*/" . $_SESSION['NOLOHScript'][0] . $_SESSION['NOLOHScript'][1] . $_SESSION['NOLOHScript'][2]);
		$_SESSION['NOLOHSrcScript'] = "";
		$_SESSION['NOLOHScript'] = array("", "", "");
		$_SESSION['NOLOHOmniscientBeing'] = serialize($OmniscientBeing);
		$GLOBALS["NOLOHGarbage"] = true;
		unset($OmniscientBeing, $GLOBALS["OmniscientBeing"]);
		unset($GLOBALS["NOLOHGarbage"]);
	}
}

?>