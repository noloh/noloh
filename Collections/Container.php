<?php
/**
 * @package Web.UI.Controls
 */
//Hope for this to be a container for things that should not be in a panel.
class Container extends Component
{
	public $Controls;
	
	function Container()
	{
		parent::Component();
		$this->Controls = new ArrayList();
		$this->Controls->ParentId = $this->Id;
	}
	
	function SearchEngineShow()
	{
		foreach($this->Controls as $control)
			$control->SearchEngineShow();
	}
}