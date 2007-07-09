<?php

	class SpecialArrayList extends ArrayList 
	{
		public $SpecialFunction;
		
		function SpecialArrayList($Items=array())
		{
			parent::ArrayList($Items);
		}
		
		function Add(&$whatObject, $PassByReference = true)
		{	
			$tempObj = GetComponentById($this->ParentId);
			$tempClass = get_class($tempObj);
			if($tempClass == "Table")
				$whatObject->Width = &$tempObj->Width;
			else if($tempClass == "TableRow")
			{
				$whatObject->Height = &$tempObj->Height;
				$whatObject->Left = $this->Item[count($this->Item) -1]->Right;
			}
			else if($tempClass == "MainMenu")
				$tempObj->AddMenuItem($whatObject);
			
			parent::Add($whatObject, $PassByReference);
		}
	}
?>