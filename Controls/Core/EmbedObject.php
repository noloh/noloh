<?php
/**
 * @package Controls/Core
 */
/**
 * EmbedObject class
 * 
 * An EmbedObject is a Control used for showing various multimedia, e.g., Flash animation, etc...  
 */
class EmbedObject extends Control
{
	private $Data;
	private $Type;
	private $ClassId;
	private $IsMovie;
	public $Parameters;
	public $InnerEmbedObjects;
	
	public function EmbedObject($data = null, $left = 0, $top = 0, $width = 100, $height = 100, $isMovie=false)
	{
		parent::Control($left, $top, null, null);
		$this->SetWidth($width);
		$this->SetHeight($height);
		$this->Parameters = new ImplicitArrayList($this, 'AddParameter'/*, 'RemoveParam', 'ClearParam'*/);
		$this->InnerEmbedObjects = new ArrayList();
		$this->InnerEmbedObjects->ParentId = $this->Id;
		$this->SetData($data);
		$this->IsMovie = ($isMovie)?true:null;
		$this->BackColor = 'white';
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
		$splitString = explode('?', $data, 2);
		$splitString = explode('.', $splitString[0]);
		$extension = $splitString[count($splitString)-1];
		if($extension == 'swf')
		{
			$this->SetType('application/x-shockwave-flash');
			//$this->SetClassId('clsid:D27CDB6E-AE6D-11cf-96B8-444553540000');
			$this->Parameters->Add (new Item('wmode', 'transparent'));
			$this->Parameters->Add(new Item('movie', $this->Data));
		}
		//NolohInternal::SetProperty('data', $data, $this);
		NolohInternal::SetProperty('innerHTML', $this, $this);
	}
	function SetClassId($classId)
	{
		$this->ClassId = $classId;
		NolohInternal::SetProperty('innerHTML', $this, $this);
	}
	function GetType()
	{
		return $this->Type;
	}
	function SetType($newType)
	{
		$this->Type = $newType;
		NolohInternal::SetProperty('innerHTML', $this, $this);
	}
	function AddParameter(Item $item)
	{
//		if($this->GetShowStatus())
//		{
//			$initialProperties = ''name','$item->Text','value','$item->Value'';
//			NolohInternal::Show('PARAM', $initialProperties, $this, $this->Id);
//		}
		NolohInternal::SetProperty('innerHTML', $this, $this);
		$this->Parameters->Add($item, true, true);
	}
	function SetWidth($width)
	{
		parent::SetWidth($width);
		QueueClientFunction($this, 'NOLOHChange', array("'".$this->Id . "I'", "'style.width'", "'".$width ."px'"), false);
		//NolohInternal::SetProperty("innerHTML", $this, $this);
	}
	function SetHeight($height)
	{
		parent::SetHeight($height);
		QueueClientFunction($this, 'NOLOHChange', array("'".$this->Id . "I'", "'style.height'", "'".$height ."px'"), false);
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
		if($this->IsMovie)/*$this->Type == 'application/x-shockwave-flash')*/
			$tmpStr .= "<EMBED type=\"$this->Type\" src=\"$this->Data\" width=\"$this->Width\" height=\"$this->Height\"></EMBED>";	
//		$InnerEmbedObjectsCount = $this->InnerEmbedObjects->Count();
//		for($i=0; $i < $InnerEmbedObjectsCount; $i++)
//			$this->InnerEmbedObjects->Elements[$i]->Show($this->IndentLevel + 1);	
		//print(str_repeat("  ", $IndentLevel) . "</OBJECT></DIV>\n");
		
		$tmpStr .= '</OBJECT>';
		//Terrible way of doing this, but width and height gets reset
		return $tmpStr;
	}
	public function Show()
	{	
		NolohInternal::Show('DIV', parent::Show(), $this);
	}
}
?>