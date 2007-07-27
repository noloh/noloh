<?php
/**
 * @package UI
 * @subpackage Controls
 */
class MarkupItem extends Object
{
	protected $Id;
	private $Keyword;
	private $Value;
	protected $PanelId;
	
	function MarkupItem($id, $keyword, $value, $panelId)
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
}

?>