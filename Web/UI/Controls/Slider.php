<?php
class Slider extends Panel
{
	private $SlidingObject;
	
	public function Slider($whatLeft, $whatTop, $whatHeight)
	{
		parent::Panel($whatLeft, $whatTop, 0, $whatHeight);
		$tmpArrowUp = new Image(NOLOHConfig::GetNOLOHPath()."Web/UI/Controls/Images/Win/VerScrollArrowUp.gif", 0, 0);
		$tmpArrowDown = new Image(NOLOHConfig::GetNOLOHPath()."Web/UI/Controls/Images/Win/VerScrollArrowDown.gif", 0);
		$tmpArrowDown->Top = $this->Height - $tmpArrowDown->Height;
		$this->SlidingObject = new Image(NOLOHConfig::GetNOLOHPath()."Web/UI/Controls/Images/Win/VerScrollKnob.gif", 0, $tmpArrowUp->Bottom);
		$this->SlidingObject->Shifts[] = Shift::Top($this->SlidingObject, $tmpArrowUp->Bottom, ($tmpArrowDown->Top - $tmpArrowDown->Height));
		$this->Width = $tmpArrowUp->Width + 1;
		$this->Controls->AddRange($tmpArrowDown, $tmpArrowUp, $this->SlidingObject);
	}
}
?>