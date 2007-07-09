<?php

class Eventee extends Object
{
	private $Id;
	private $Keyword;
	private $Value;
	private $PanelId;
	
	function Eventee($id, $keyword, $value, $panelId)
	{
		$this->Id = $id;
		$this->Keyword = $keyword;
		$this->Value = $value;
		$this->PanelId = $panelId;
	}
	
	function GetKeyword()
	{
		return $this->Keyword;
	}
	
	function GetValue()
	{
		return $this->Value;
	}
	
	function GetClick()
	{
		return GetComponentById($this->PanelId)->GetEvent("Click", $this->Id);
	}
	
	function SetClick($newClick)
	{
		GetComponentById($this->PanelId)->SetEvent($newClick, "Click", $this->Id);
	}
	
	function GetDoubleClick()
	{
		return GetComponentById($this->PanelId)->GetEvent("DoubleClick", $this->Id);
	}
	
	function SetDoubleClick($newDoubleClick)
	{
		GetComponentById($this->PanelId)->SetEvent($newDoubleClick, "DoubleClick", $this->Id);
	}
	
	function GetMouseDown()
	{
		return GetComponentById($this->PanelId)->GetEvent("MouseDown", $this->Id);
	}
	
	function SetMouseDown($newMouseDown)
	{
		GetComponentById($this->PanelId)->SetEvent($newMouseDown, "MouseDown", $this->Id);
	}
	
	function GetMouseOut()
	{
		return GetComponentById($this->PanelId)->GetEvent("MouseOut", $this->Id);
	}
	
	function SetMouseOut($newMouseOut)
	{
		GetComponentById($this->PanelId)->SetEvent($newMouseOut, "MouseOut", $this->Id);
	}	

	function GetMouseOver()
	{
		return GetComponentById($this->PanelId)->GetEvent("MouseOver", $this->Id);
	}
	
	function SetMouseOver($newMouseOver)
	{
		GetComponentById($this->PanelId)->SetEvent($newMouseOver, "MouseOver", $this->Id);
	}
	
	function GetMouseUp()
	{
		return GetComponentById($this->PanelId)->GetEvent("MouseUp", $this->Id);
	}
	
	function SetMouseUp($newMouseUp)
	{
		GetComponentById($this->PanelId)->SetEvent($newMouseUp, "MouseUp", $this->Id);
	}
	
	function GetRightClick()
	{
		return GetComponentById($this->PanelId)->GetEvent("RightClick", $this->Id);
	}
	
	function SetRightClick($newRightClick)
	{
		GetComponentById($this->PanelId)->SetEvent($newRightClick, "RightClick", $this->Id);
	}
}

?>