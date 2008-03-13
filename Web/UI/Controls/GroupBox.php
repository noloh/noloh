<?php
/**
 * @package Web.UI.Controls
 */

/**
 * @ignore
 */
class GroupBox extends Guardian
{	
	function GroupBox($whatCaption ='', $whatLeft = 0, $whatTop = 0, $whatWidth = 100, $whatHeight = 100)  
	{
		parent::Guardian($whatLeft, $whatTop, $whatWidth, $whatHeight);
		$this->Text = $whatCaption;
	}
	
	function OpenPrintableVersion()
	{
		AddScript("var oldNode = document.getElementById('" . $this->Id . "'); var newWin = window.open(); newWin.document.write(oldNode.innerHTML);");
	}

	function GetStyleString()
	{
		$parentShow = parent::Show();
		if ($parentShow == false)
			return false;
			
		if(is_string($this->Scrollable))
		{
			if($this->Scrollable == "Visible")
				$parentShow .=" overflow:visible;";
		}
		else
		{
			if(($this->Scrollable == true) && ($this->AutoScroll == false))
				$parentShow .= " overflow:scroll;";
			elseif($this->AutoScroll == true)
				$parentShow .=" overflow:auto;";
			else
				$parentShow .=" overflow:hidden;";
		}
		$parentShow .=" padding:0px;'>\n";
		
		return $parentShow;
	}
		
	function Show($IndentLevel = 0)
	{
		$parentShow = $this->GetStyleString();
		print(str_repeat("  ", $IndentLevel) . "<FIELDSET " . $parentShow);
		if(!is_null($this->Text))
			print(str_repeat("  ", $IndentLevel + 1) . "<LEGEND>" . $this->Text . "</LEGEND>\n");
		print(str_repeat("  ", $IndentLevel + 1) . "<DIV style='overflow:hidden;'>\n");
			$this->IterateThroughAllControls($IndentLevel+1);
		print(str_repeat("  ", $IndentLevel + 1) . "</DIV>\n");
		print(str_repeat("  ", $IndentLevel) . "</FIELDSET>\n");
		
		return $parentShow;
	}
	
	function IterateThroughAllControls($IndentLevel)
	{
		$ControlCount = $this->Controls->Count();
		for($i=0; $i<$ControlCount; $i++)
		{
			if($this->Controls->Elements[$i]->Overlap === false)
				for($j=0; $j<$i; $j++)
				{
					if($this->Controls->Elements[$i]->Left >= $this->Controls->Elements[$j]->Left &&
					 $this->Controls->Elements[$i]->Left < $this->Controls->Elements[$j]->Left + $this->Controls->Elements[$j]->Width &&
					 $this->Controls->Elements[$i]->Top >= $this->Controls->Elements[$j]->Top &&
					 $this->Controls->Elements[$i]->Top < $this->Controls->Elements[$j]->Top + $this->Controls->Elements[$j]->Height)
					 	$this->Controls->Elements[$i]->Left = $this->Controls->Elements[$j]->Left + $this->Controls->Elements[$j]->Width;
				}
				$this->Controls->Elements[$i]->Show($IndentLevel+1);
		}
	}
}

?>