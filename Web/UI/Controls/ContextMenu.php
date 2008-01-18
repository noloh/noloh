<?php

class ContextMenu extends Button
{
	public static $Source;
	
	function ContextMenu()
	{
		parent::Button('Menu');
		$this->Visible = false;
	}
	
	function Show()
	{
		AddNolohScriptSrc('ContextMenu.js', true);
		parent::Show();
	}
}

?>