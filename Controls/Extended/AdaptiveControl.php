<?php

class AdaptiveControl extends ControlPair
{
	public $Translated = false;
	public $Styled = false;

	function __construct($placeholder, $secondControl = null, $left = 0, $top = 0, $adaptiveLabel = true)
	{
		$firstControl = new AdaptiveLabel($placeholder);
		$firstControl->CSSPadding = '0px';

		$removePadding = (class_exists('KendoSettings') && defined('KendoSettings::RemoveAdaptiveControlPadding')) ?
			KendoSettings::RemoveAdaptiveControlPadding :
			false;

		if ($secondControl == null)
		{
			$secondControl = new Kendo\TextBox(0, 0, 150);
		}

		$firstControl->ZIndex = isset($secondControl->TextField) ?
			$secondControl->TextField->ZIndex + 10 :
			$secondControl->ZIndex + 10;

		$secondControl->CSSBoxSizing = 'border-box';
		$secondControl->CSSClass = 'FieldValue';

		if ($removePadding)
		{
			$firstControl->Top = 15;
			$secondControl->Top -= 7;
		}
		else
		{
			$firstControl->Top = 22;
		}

		parent::__construct($firstControl, $secondControl, $left, $top, Layout::Vertical);

		ClientScript::Queue($this, "AdaptiveControl", array($this->First->Id, $this->Second->Id, $adaptiveLabel));

		if ($secondControl instanceof Kendo\CommonDateTimeBox)
		{
			$firstControl->AlwaysFocused = true;
		}

		if (!empty(WebPage::That()->Permissions['DataDictionaryFields']['can_edit']) && isset(WebPage::That()->LabelContextMenu))
		{
			$this->ContextMenu = WebPage::That()->LabelContextMenu;
		}

		if ($removePadding)
		{
			$this->Height -= 20;
		}
	}
	function SetCSSClass($cssClass = null)
	{
		parent::SetCSSClass('AdaptiveControl ' . $cssClass);
	}
	function Show()
	{
		ClientScript::AddNOLOHSource('AdaptiveControl.js');

		parent::Show();

		if (!in_array($this->Second->Value, array(null, ''), true))
		{
			$this->First->AddCSSClass('AdaptiveLabelFocused');
		}

		$this->First->ZIndex = $this->Second->ZIndex + 10;
	}
	function GetEnabled()
	{
		return $this->Enabled;
	}
	function SetEnabled($bool)
	{
		$this->Second->Enabled = $bool;
	}
	function SetToolTip($toolTip)
	{
		$this->First->ToolTip = $toolTip;
	}
}