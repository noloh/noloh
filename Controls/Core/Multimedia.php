<?php
/**
 * Multimedia class
 * 
 * An Multimedia is a Control used for showing various multimedia, e.g., Flash animation, etc...  
 * 
 * @package Controls/Core
 */
class Multimedia extends Control
{
	private $Data;
	private $Type;
	private $ClassId;
	private $IsMovie;
	public $Parameters;
	public $FlashVars;
	public $InnerMultimedia;
	
	public function Multimedia($data = null, $left = 0, $top = 0, $width = 100, $height = 100, $isMovie=false)
	{
		parent::Control($left, $top, null, null);
		$this->SetWidth($width);
		$this->SetHeight($height);
		$this->Parameters = new ImplicitArrayList($this, 'AddParameter'/*, 'RemoveParam', 'ClearParam'*/);
		$this->FlashVars = new ImplicitArrayList($this, 'AddFlashVar');//, 'RemoveFlashVar', 'ClearFlashVars'*/);
		$this->InnerMultimedia = new ArrayList();
		$this->InnerMultimedia->ParentId = $this->Id;
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
//			$this->Parameters->Add (new Item('wmode', 'window'));
			$this->Parameters->Add (new Item('wmode', 'window'));
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
	function AddFlashVar($flashVar)
	{
		NolohInternal::SetProperty('innerHTML', $this, $this);
		$this->FlashVars->Add($flashVar, true, true);
	}
/*	function SetWidth($width)
	{
		parent::SetWidth($width);
		//QueueClientFunction($this, 'NOLOHChange', array("'".$this->Id . "I'", "'style.width'", "'100%'"), false);
//		QueueClientFunction($this, 'NOLOHChange', array("'".$this->Id . "I'", "'style.width'", "'".$width ."px'"), false);
		//QueueClientFunction($this, 'NOLOHChange', array("'".$this->Id . "E'", "'width'", "'".$width ."'"), false);
		//NolohInternal::SetProperty("innerHTML", $this, $this);
	}
	function SetHeight($height)
	{
		parent::SetHeight($height);
		//QueueClientFunction($this, 'NOLOHChange', array("'".$this->Id . "I'", "'style.height'", "'100%'"), false);
//		QueueClientFunction($this, 'NOLOHChange', array("'".$this->Id . "I'", "'style.height'", "'".$height ."px'"), false);
		//NolohInternal::SetProperty("innerHTML", $this, $this);
	}*/
	/**
	 * @ignore
	 */
	function GetInnerString()
	{
		$tmpStr = '<OBJECT name="'.$this->Id.'I" id="'.$this->Id.'I" style="width:100%;height:100%" ';
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
		$embedFlashVars='';
		if(count($this->FlashVars) > 0)
		{
			$flashVars = '';
			foreach($this->FlashVars as $var)
				$flashVars .= $var->Text . '=' . $var->Value . '&';
			$flashVars = rtrim($flashVars, '&');
			$tmpStr .= '<PARAM name = "FlashVars" value = "'. $flashVars . '">';
			$embedFlashVars = ' FlashVars="'. $flashVars . '" ';
		}
		if($this->IsMovie)/*$this->Type == 'application/x-shockwave-flash')*/
//			$tmpStr .= "<EMBED name=\"{$this->Id}I\" type=\"$this->Type\" src=\"$this->Data\" $embedFlashVars width=\"$this->Width\" height=\"$this->Height\"></EMBED>";	
			$tmpStr .= "<EMBED name=\"{$this->Id}I\" type=\"$this->Type\" src=\"$this->Data\" $embedFlashVars width=100% height=100%></EMBED>";	
//		$InnerMultimediaCount = $this->InnerMultimedia->Count();
//		for($i=0; $i < $InnerMultimediaCount; $i++)
//			$this->InnerMultimedia->Elements[$i]->Show($this->IndentLevel + 1);	
		//print(str_repeat("  ", $IndentLevel) . "</OBJECT></DIV>\n");
		
		$tmpStr .= '</OBJECT>';
		//Terrible way of doing this, but width and height gets reset
		return str_replace("'", "\\'", $tmpStr);
	}
	function GetTalk()				{return $this->GetEvent('Talk');}
	function SetTalk($event)		{$this->SetEvent($event, 'Talk');}
	function InvokeFunction($function, $argsDotDotDot)
	{
//		$params = array_slice(func_get_args(), 1);
		$params = func_get_args();
		array_splice($params, 0, 0, $this->Id);
		$count = count($params);
		for($i=0; $i<$count; ++$i)
			$params[$i] = ClientEvent::ClientFormat($params[$i]);
		QueueClientFunction($this, '_NInvokeFlash', $params);
	}
	/**
	 * @ignore
	 */
	public function Show()
	{	
		AddNolohScriptSrc('Multimedia.js');
		NolohInternal::Show('DIV', parent::Show(), $this);
	}
	/**
	 * @ignore
	 */
	function __call($name, $args)
	{
		array_splice($args, 0, 0, $name);
		call_user_func_array(array($this, 'InvokeFunction'), $args);
	}
}
?>