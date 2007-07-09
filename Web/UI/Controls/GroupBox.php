<?php

class GroupBox extends Guardian
{	
	function GroupBox($whatCaption ="", $whatLeft = 0, $whatTop = 0, $whatWidth = 100, $whatHeight = 100)  
	{
		parent::Guardian($whatLeft, $whatTop, $whatWidth, $whatHeight);
		$this->Text = $whatCaption;
	}
	
	function OpenPrintableVersion()
	{
		AddScript("var oldNode = document.getElementById('" . $this->DistinctId . "'); var newWin = window.open(); newWin.document.write(oldNode.innerHTML);");
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
			else if($this->AutoScroll == true)
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
			if($this->Controls->Item[$i]->Overlap === false)
				for($j=0; $j<$i; $j++)
				{
					if($this->Controls->Item[$i]->Left >= $this->Controls->Item[$j]->Left &&
					 $this->Controls->Item[$i]->Left < $this->Controls->Item[$j]->Left + $this->Controls->Item[$j]->Width &&
					 $this->Controls->Item[$i]->Top >= $this->Controls->Item[$j]->Top &&
					 $this->Controls->Item[$i]->Top < $this->Controls->Item[$j]->Top + $this->Controls->Item[$j]->Height)
					 	$this->Controls->Item[$i]->Left = $this->Controls->Item[$j]->Left + $this->Controls->Item[$j]->Width;
				}
				$this->Controls->Item[$i]->Show($IndentLevel+1);
		}
	}
}

?>