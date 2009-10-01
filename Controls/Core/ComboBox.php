<?php
/**
 * ComboBox class
 *
 * A Control for a conventional web ComboBox. A ComboBox allows a user to select exactly one Item from a dropdown menu. The menu will not pull
 * down until a user explicitly clicks on it to view the options. That is one fundamental way in which it differs from a Group of RadioButtons,
 * another possible way of allowing a user to select exactly one string of text out of many, but will display all the options at once without
 * a menu.
 * 
 * @package Controls/Core
 */
class ComboBox extends ListControl 
{
	private $SelectedIndex;
	/**
	 * Constructor.
	 * Be sure to call this from the constructor of any class that extends ComboBox
	 * @param integer $left The left coordinate of this element
	 * @param integer $top The top coordinate of this element
	 * @param integer $width The width of this element
	 * @param integer $height The height of this element
	 * @return ComboBox
	 */
	function ComboBox($left = 0, $top = 0, $width = 83, $height = 20)
	{
		parent::ListControl($left, $top, $width, $height);
		//$this->SetSelectedIndex(0);
	}
	/**
	 * Returns the index of the Item that is selected, or -1 if none are selected.
	 * @return integer
	 */
	function GetSelectedIndex()
	{
		return ($this->SelectedIndex === null)?-1:$this->SelectedIndex;
	}
	/**
	 * Sets an Item of a particular index as selected
	 * @param integer $index
	 */
	function SetSelectedIndex($index)
	{
		if($this->GetSelectedIndex() != $index)
		{
			$this->SelectedIndex = $index;
			parent::SetSelectedIndex($index);
		}
	}
	/**
	 * Returns the Item that is selected, or null if none are selected
	 * @return Item
	 */
	function GetSelectedItem()
	{
		return $this->SelectedIndex != -1 ? $this->Items->Elements[$this->SelectedIndex] : null;
	}
	/**
	 * @ignore
	 */
	function GetEventString($eventTypeAsString)
	{
		$preStr = '';
		if($eventTypeAsString == 'Change')
			$preStr = '_NSave("'.$this->Id.'","'.selectedIndex.'");';
		return $preStr . parent::GetEventString($eventTypeAsString);
	}
	/**
	 * @ignore
	 */
	function AddItem($item)
	{
		parent::AddItem($item);
		if($this->Items->Count == 1)
			$this->SetSelectedIndex(0);
	}
	/**
	 * @ignore
	 * 
	 * constraints
	 *field, array(text, key) or array(array(field, false), array(text, key)) or array(array(field, false), array(field, key), field or array(field))) 
	 */
	function Bind($dataSource, $constraints=null, $title='- Select Item -', $rowCallback=null)
	{
		$this->Items->Clear();
		if($title)
		{
			if($title instanceof Item)
				$this->Items->Add($title);
			else
				$this->Items->Add(new Item($title, null));
		}
			
		$textField = null;
		$keyField = null;	
		
		if(isset($constraints))
		{
			if(is_array($constraints))
			{
				$count = count($constraints);
				$properties = array(null, null);
				
				for($i=0; $i < $count; ++$i)
				{
					if(is_array($constraints[$i]))
					{
						/*$currentProperty = 0;
						//0=>text, 1=>key
						foreach($constraints[$i] as $constraint => $value)
						{
							if(is_string($constraint))
							{
								$constraint = strtolower($constraint);
								if(strtolower($constraint) == 'name')
									$properties[0] = $value;
								elseif(strtolower($constraint) == 'title')
									$properties[1] = $value;
								elseif(strtolower($constraint) == 'width')
									$properties[2] = $value;
							}
							else
								$properties[$currentProperty++] = $value;
						}*/
					}
					else
						$properties[$i] = $constraints[$i];
					
					/*if($properties[1] !== false)
					{
						$this->DataColumns[] = $i;
						$this->AddColumn($properties[1], $properties[2]);
					}
					if($properties[0])
						$columns[] = $properties[0];
					*/
				}
				if(isset($properties[0]))
					$textField = $properties[0];
				if(isset($properties[1]))
					$keyField = $properties[1];
			}
			else
				$textField = $constraints;	
		}
			
		if($dataSource instanceof DataReader || is_array($dataSource))
		{
			foreach($dataSource as $row)
			{
				if($keyField !== null)
					$this->Items->Add(new Item($row[$textField], $row[$keyField]));
				elseif($textField !== null)
					$this->Items->Add($row[$textField]);
				else
					$this->Items->Add($row);
			}
		}			
	}
	/**
	 * @ignore
	 */
	function Show()
	{
		//AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/ListControl.js");
		ClientScript::AddNOLOHSource('ListControl.js');
		NolohInternal::Show('SELECT', parent::Show() . $this->GetEventString(null), $this);
	}
	/**
	 * @ignore
	 */
	function NoScriptShow($indent)
	{
		$str = Control::NoScriptShow($indent);
		if($str !== false)
			echo $indent, '<SELECT ', $str, ">\n", ListControl::NoScriptShow($indent), $indent, "</INPUT>\n";
	}
}

?>