<?php
/**
 * @package Web.UI.Controls
 */
class Panel extends Guardian
{
	private $Scrolling;
	public $SelectFix;
	//public $DropShadow;
	
	function Panel($left = 0, $top = 0, $width = 100, $height = 100, $implicitObject = null)
	{
		parent::Guardian($left, $top, $width, $height, $implicitObject);
		$this->SetScrolling(($width === null || $height === null)?null:false);
		$this->SetCSSClass();
		//$this->SetScrolling(false);
	}
	function SetCSSClass($cssClass=null)
	{
		parent::SetCSSClass('NPanel '.$cssClass);
	}		
	function OpenPrintableVersion()
	{
		AddScript("var oldNode = document.getElementById('$this->Id'); var newWin = window.open(); newWin.document.write(oldNode.innerHTML);");
	}
	function GetScrolling()
	{
		return $this->Scrolling;
	}
	function SetScrolling($scrollType)
	{
		$this->Scrolling = $scrollType;
		$tmpScroll = null;
		if($scrollType == System::Auto)
			$tmpScroll = 'auto';
		elseif($scrollType == System::Full)
			$tmpScroll = 'visible';
		elseif($scrollType === null)
			$tmpScroll = '';
		elseif($scrollType)
			$tmpScroll = 'scroll';
		else//if(!$scrollType)
			$tmpScroll = 'hidden';
		//Alert($tmpScroll);
		NolohInternal::SetProperty('style.overflow', $tmpScroll, $this);
	}
	function GetStyleString()
	{
		$parentShow = parent::Show();
		//if ($parentShow == false)
		//	return false;
			
		/*if(is_string($this->Scrollable))
		{
			if($this->Scrollable == "Visible")
				$parentShow .= ",'style.overflow','visible'";
		}
		else
		{
			if(($this->Scrollable == true) && ($this->AutoScroll == false))
				$parentShow .= ",'style.overflow','scroll'";
			else
				$parentShow .=  ",'style.overflow','" . ($this->AutoScroll == true ? "auto" : "hidden") . "'";
		}*/
		/*if($this->Border != null);
			//$parentShow .=" border:" . $this->Border . ";";
		else 
			$parentShow .=" border:0px;";
		*/
		////$parentShow .= ",'style.padding','0px'"; -  Only line that was left pre style sheet
		
		return $parentShow;
	}
	
	/*function Show($IndentLevel = 0)
	{
		$parentShow = parent::Show();
		if ($parentShow == false)
			return false;
		
		print(str_repeat("  ", $IndentLevel) . "<DIV " . $parentShow);
		if(($this->Scrollable == true) && ($this->AutoScroll == false))
			print(" overflow:scroll;");	//print(" overflow:scroll;");
		else if($this->AutoScroll == true)
			print(" overflow:auto;");
		else if($this->Scrollable == "Visible")
			print(" overflow:visible;");
		else
			print(" overflow:hidden;");
			
		if($this->Border != null)
			print(" border:" . $this->Border . ";");
		else 
			print(" border:0px;");
		print(" padding:0px;'>\n");
		/*$ControlCount = $this->Controls->Count();
		for($i=0; $i<$ControlCount; $i++)
		{		
			if($this->Controls->Item[$i]->Overlap == false)
				for($j=0; $j<$i; $j++)
				{
					if($this->Controls->Item[$i]->Left >= $this->Controls->Item[$j]->Left &&
					 $this->Controls->Item[$i]->Left <= $this->Controls->Item[$j]->Left + $this->Controls->Item[$j]->Width &&
					 $this->Controls->Item[$i]->Top >= $this->Controls->Item[$j]->Top &&
					 $this->Controls->Item[$i]->Top <= $this->Controls->Item[$j]->Top + $this->Controls->Item[$j]->Top)
					 	$this->Controls->Item[$i]->Left = $this->Controls->Item[$j]->Left + $this->Controls->Item[$j]->Width;
				}
				$this->Controls->Item[$i]->Show($IndentLevel+1);
		}*/
		/*$this->IterateThroughAllControls();
		print(str_repeat("  ", $IndentLevel) . "</DIV>\n");
		unset($parentShow, $ControlCount);
		return true;
	}*/
		
	function Show()
	{
        NolohInternal::Show('DIV', parent::Show(), $this);
		//$initialProperties = $this->GetStyleString();
		//$initialProperties = parent::Show();
		//NolohInternal::Show('DIV', $initialProperties, $this);

//			if(false && $this->SelectFix && (GetBrowser() == "ie"))
//			{
//				$initialProperties = "'id','{$this->Id}IFRAME','style.position','absolute','style.left','{$this->Left}px','style.top','{$this->Top}px','style.width','{$this->Width}px','style.height','{$this->Height}px','src','javascript:false','scrolling','no','frameborder','0'";
//				NolohInternal::Show("IFRAME", $initialProperties, $this);
//				AddScript("document.getElementById('{$this->Id}').ShiftsWith = '{$this->Id}IFRAME'");
//			}
		
		
		/*
		if($showIFrame)
			$dispStr .= "<IFRAME ID = '{$this->Id}IFRAME' style='POSITION:absolute; LEFT:{$this->Left}px; TOP:{$this->Top}px; WIDTH:{$this->Width}px; HEIGHT:{$this->Height}px;' src='javascript:false;' scrolling='no' frameborder='0'></IFRAME>";
		//if($this->DropShadow == true)
		//{
		//	print(str_repeat("  ", $IndentLevel) . "<DIV ID = '{$this->Id}DS' style='POSITION:absolute; LEFT:".($this->Left + 5)."px; TOP:".($this->Top+5)."px; WIDTH:{$this->Width}px; HEIGHT:{$this->Height}px; background:black; filter:alpha(opacity=20)'></DIV>\n");
		//	AddScript("document.getElementById('{$this->Id}').ShiftsWith = '{$this->Id}DS'");
		//}
			/*else
			{
				print(str_repeat("  ", $IndentLevel) . "<IFRAME ID = '{$this->Id}IFRAME' style='POSITION:absolute; LEFT:{$this->Left}px; TOP:{$this->Top}px; WIDTH:{$this->Width}px; HEIGHT:{$this->Height}px; filter: alpha(opacity=0)' src='javascript:false;' scrolling='no' frameborder='0'></IFRAME>\n");
				AddScript("document.getElementById('{$this->Id}').ShiftsWith = '{$this->Id}IFRAME'");
			}*
		
		$dispStr .= "<DIV " . $parentShow;
		//if(GetBrowser() == "ie" && $this->AddIFrame == true)
			//print(str_repeat("  ", $IndentLevel+1) . "<IFRAME style='POSITION:absolute; LEFT:0px; TOP:0px; WIDTH:{$this->Width}px; HEIGHT:{$this->Height}px; filter: alpha(opacity=0);' frameborder=0 scrolling=no src='javascript:false;'></IFRAME>\n");		
		$this->IterateThroughAllControls($IndentLevel);
		$dispStr .= "</DIV>";
		NolohInternal::Show($dispStr, $this);*/
		//$this->IterateThroughAllControls();
		//if($showIFrame)
		//	AddScript("document.getElementById('{$this->Id}').ShiftsWith = '{$this->Id}IFRAME'");

		//return $initialProperties;
	}
	
	function SearchEngineShow()
	{
		foreach($this->Controls as $control)
			$control->SearchEngineShow();
	}
}
?>