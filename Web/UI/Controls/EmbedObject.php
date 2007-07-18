<?php

class EmbedObject extends Control
{
	private $Data;
	private $Type;
	private $ClassId;
	public $Parameters;
	public $InnerEmbedObjects;
	
	public function EmbedObject($data = null, $left = 0, $top = 0, $width = 100, $height = 100)
	{
		parent::Control($left, $top, $width, $height);
		$this->Parameters = new ImplicitArrayList($this, "AddParameter"/*, "RemoveParam", "ClearParam"*/);
		$this->InnerEmbedObjects = new ArrayList();
		$this->InnerEmbedObjects->ParentId = $this->Id;
		$this->SetData($data);
		$this->BackColor = "white";
	}
	function GetData()
	{
		return $this->Data;
	}
	function SetData($data)
	{
		if($data == null)
			return;
		$this->Data = $data;
		$splitString = explode(".", $data);
		$extension = $splitString[count($splitString)-1];
		if($extension == "swf")
		{
			$this->SetType("application/x-shockwave-flash");
			//$this->SetClassId("clsid:D27CDB6E-AE6D-11cf-96B8-444553540000");
			$this->Parameters->Add(new Item($this->Data, "movie"));
		}
		//NolohInternal::SetProperty("data", $data, $this);
		NolohInternal::SetProperty("innerHTML", $this, $this);
	}
	function SetClassId($classId)
	{
		$this->ClassId = $classId;
		NolohInternal::SetProperty("innerHTML", $this, $this);
	}
	function GetType()
	{
		return $this->Type;
	}
	function SetType($newType)
	{
		$this->Type = $newType;
		NolohInternal::SetProperty("innerHTML", $this, $this);
	}
	function AddParameter(Item $item)
	{
//		if($this->GetShowStatus())
//		{
//			$initialProperties = "'name','$item->Text','value','$item->Value'";
//			NolohInternal::Show("PARAM", $initialProperties, $this, $this->Id);
//		}
		NolohInternal::SetProperty("innerHTML", $this, $this);
		$this->Parameters->Add($item, true, true);
	}
	function SetWidth($width)
	{
		$this->Width = $width;
		QueueClientFunction($this, "NOLOHChange", array("'".$this->Id . "I'", "'style.width'", "'".$width ."px'"), false);
		//NolohInternal::SetProperty("innerHTML", $this, $this);
	}
	function SetHeight($height)
	{
		$this->Height = $height;
		QueueClientFunction($this, "NOLOHChange", array("'".$this->Id . "I'", "'style.height'", "'".$height ."px'"), false);
		//NolohInternal::SetProperty("innerHTML", $this, $this);
	}
	function GetInnerString()
	{
		$tmpStr = '<OBJECT id="'.$this->Id.'I"';
		if($this->Type != null)
			$tmpStr.='type="'.$this->Type.'" ';
		if($this->Data != null)
			$tmpStr.='data="'.$this->Data.'"';
		if($this->ClassId != null)
			$tmpStr.='classid = "'.$this->ClassId.'" ';
		$tmpStr.='>';
//		$paramCount = $this->Parameters->Count();
		foreach($this->Parameters as $item)
			$tmpStr .='<PARAM name = "'.$item->Text.'" value = "'.$item->Value.'">';
//		$InnerEmbedObjectsCount = $this->InnerEmbedObjects->Count();
//		for($i=0; $i < $InnerEmbedObjectsCount; $i++)
//			$this->InnerEmbedObjects->Item[$i]->Show($this->IndentLevel + 1);	
		//print(str_repeat("  ", $IndentLevel) . "</OBJECT></DIV>\n");
		$tmpStr .= '</OBJECT>';
		//Terrible way of doing this, but width and height get's reset
		return $tmpStr;
	}
	public function Show()
	{
		$initialProperties = parent::Show();
		$transItem = new Item("transparent", "wmode");
		if($this->Parameters->IndexOf($transItem) == -1)
			$this->Parameters->Add($transItem);			
		NolohInternal::Show("DIV", $initialProperties, $this);
//		foreach($this->Parameters as $item)
//		{
//			$paramProps = "'name','$item->Text','value','$item->Value'";
//			NolohInternal::Show("PARAM", $paramProps, $this, $this->Id);
//		}
//		$InnerEmbedObjectsCount = $this->InnerEmbedObjects->Count();
//		for($i=0; $i < $InnerEmbedObjectsCount; $i++)
//			$this->InnerEmbedObjects->Item[$i]->Show();
		
//		print(str_repeat("  ", $IndentLevel) . "<OBJECT " . $parentShow . "' ");
//		if(!empty($this->Type))
//			print("type='" . $this->Type . "' ");
//		if(!empty($this->Data))
//			print("data='" . $this->Data . "' ");
//		print(">\n");
//		$ParametersCount = $this->Parameters->Count();
//		for($i=0; $i < $ParametersCount; $i++)
//			print(str_repeat("  ", $IndentLevel + 1) . "<PARAM name = '{$this->Parameters->Item[$i]->Text}' value = '{$this->Parameters->Item[$i]->Value}'>\n");
//		$InnerEmbedObjectsCount = $this->InnerEmbedObjects->Count();
//		for($i=0; $i < $InnerEmbedObjectsCount; $i++)
//			$this->InnerEmbedObjects->Item[$i]->Show();
//		//print(str_repeat("  ", $IndentLevel) . "</OBJECT></DIV>\n");
//		print(str_repeat("  ", $IndentLevel) . "</OBJECT>\n");
	}
}
?>