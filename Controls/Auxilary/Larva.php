<?php
/**
 * Larva class
 *
 * A Larva is a MarkupItem that is capable of turning into Components.<br>
 * This is used only in connection with RichMarkupRegion.<br>
 * This class should never be instantiated by the developer. Use only the Larva objects that RichMarkupRegion::GetLarvae() returns.<br>
 * See {@see RichMarkupRegion} and {@see RichMarkupRegion::GetLarvae()} for more information.<br>
 *  
 * <pre>
 * // A function which takes an RichMarkupRegion object that we'll locally name $eventMarkupRegion
 * function MakeLarvaeButtons(RichMarkupRegion $eventMarkupRegion)
 * {
 * 	// Iterates through all Larvae of $eventMarkupRegion
 * 	foreach($eventMarkupRegion->GetLarvae() as $larva)
 * 		// Morphs each Larva intoa Button whose Text is that Larva's value
 * 		$larva->Morph(new Button($eventee->Value));
 * }
 * </pre>
 * 
 * @package Controls/Auxiliary
 */
class Larva extends MarkupItem 
{	
	/**
	 * @ignore
	 */
	function Eventee($id, $keyword, $value, $panelId)
	{
		parent::MarkupItem($id, $keyword, $value, $panelId);
	}
	/**
	 * Morph inserts a Component object into where your n:larva tag would have been. The Component's Layout will 
	 * automatically be set to relative so that it would look correct in the context of surrounding mark-up.
	 * @param Component $component
	 * @return Component The object passed in
	 */
	function Morph(Component $component)
	{
		$markUpPanel = GetComponentById($this->PanelId);
		if(isset($markUpPanel->ComponentSpace[$this->Id]))
			if($markUpPanel->ComponentSpace[$this->Id] === $component)
				return;
			else
				$markUpPanel->ComponentSpace[$this->Id]->SetParentId(null);
		$markUpPanel->ComponentSpace[$this->Id] = &$component;
		//$component->SetParentId($this->Id);
		$component->SetMorphedParentId($this->Id);
		$component->SetLayout(Layout::Relative);
		return $component;
	}
}

?>