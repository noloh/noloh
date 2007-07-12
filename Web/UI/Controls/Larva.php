<?php

class Larva extends MarkupItem 
{
	public static function Morphy(&$larva, $component)
	{
		$larva = $component;
		return $component;
	}
	
	function Eventee($id, $keyword, $value, $panelId)
	{
		parent::MarkupItem($id, $keyword, $value, $panelId);
	}
	
	function Morph(Component $component)
	{
		$markUpPanel = GetComponentById($this->PanelId);
		if(isset($markUpPanel->ComponentSpace[$this->Id]))
			if($markUpPanel->ComponentSpace[$this->Id] === $component)
				return;
			else
				$markUpPanel->ComponentSpace[$this->Id]->SetParentId(null);
		$markUpPanel->ComponentSpace[$this->Id] = $component;
		$component->SetParentId($this->Id);
		//return Larva::Morphy($this, $component);
		return $component;
	}
}

?>