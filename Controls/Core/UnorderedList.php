<?php

class UnorderedList extends Control
{
	/**
	 * An ArrayList of Controls that will be Shown when added, provided the Panel has also been Shown
	 * @var ArrayList
	 */
	public $Controls;

	function UnorderedList($left = 0, $top = 0)
	{
		parent::Control($left, $top, null, null);

		$this->Controls = new ArrayList();
		$this->Controls->ParentId = $this->Id;
	}
	function Show()
	{
		NolohInternal::Show('UL', parent::Show(), $this);
	}
}