<?php

class ListItem extends Control
{
	/**
	 * An ArrayList of Controls that will be Shown when added, provided the Panel has also been Shown
	 * @var ArrayList
	 */
	public $Controls;

	function __construct($text = '')
	{
		parent::__construct(null, null, null, null);

		$this->Controls = new ArrayList();
		$this->Controls->ParentId = $this->Id;

		$this->CSSPosition = null;
		$this->SetText($text);
	}
	function SetText($text)
	{
		parent::SetText($text);
		NolohInternal::SetProperty('innerHTML', preg_replace('(\r\n|\n|\r)', '<BR>', $text), $this);
	}
	function Show()
	{
		NolohInternal::Show('LI', parent::Show(), $this);
	}
}