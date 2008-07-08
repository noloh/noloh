<?php
/**
 * Container class
 *
 * A Container is a Component which is capable of having and showing children Controls, but which is not a Control.
 * 
 * It is distinguished from Panel Controls in that the latter is a Control, having a physical representation and presense on the client, 
 * including properties like position (Left, Top), size, and visual styles (Border, Color, etc...), whereas Container merely shows its
 * children Controls and has no appearance or presense of its own.
 * 
 * @package Collections
 */
class Container extends Component
{
	/**
	 * ArrayList holding the children Controls. Any Controls Added to this ArrayList will be automatically shown, provided that the Container itself has been added to something that's Shown. 
	 * @var ArrayList
	 */
	public $Controls;
	/**
	* Constructor.
	* Be sure to call this from the constructor of any class that extends Container
	* @return Container
	*/
	function Container()
	{
		parent::Component();
		$this->Controls = new ArrayList();
		$this->Controls->ParentId = $this->Id;
	}
		/**
	 * @ignore
	 */
	function Bury()
	{
		foreach($this->Controls as $control)
			$control->Bury();
		parent::Bury();
	}
	/**
	* @ignore
	*/
	function Resurrect()
	{
		foreach($this->Controls as $control)
			$control->Resurrect();
		parent::Resurrect();	
	}
	/**
	 * @ignore
	 */
	function SearchEngineShow()
	{
		foreach($this->Controls as $control)
			$control->SearchEngineShow();
	}
}