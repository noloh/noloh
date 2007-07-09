<?php

class Shift
{
	const Normal = 0;
	const Ghost = 1;
	const Width = 1;
	const Height = 2;
	const Size = 3;
	const Left = 4;
	const Top = 5;
	const Location = 6;
	const Mirror = 7;
	
	static function Width($control, $min=1, $max=null, $type=self::Normal, $ratio=1)
	{
		if($min === null)
			$min = 1;
		if($max === null)
			$max = "null";
		$id = $control->DistinctId;
		return array($id,1,"Array(\"$id\",1,$type,$min,$max,null,null,$ratio)");
	}

	static function Height($control, $min=1, $max=null, $type=self::Normal, $ratio=1)
	{
		if($min === null)
			$min = 1;
		if($max === null)
			$max = "null";
		$id = $control->DistinctId;
		return array($id,2,"Array(\"$id\",2,$type,null,null,$min,$max,$ratio)");
	}
	
	static function Size($control, $minWidth=1, $maxWidth=null, $minHeight=1, $maxHeight=null, $type=self::Normal, $ratio=1)
	{
		if($minWidth === null)
			$minWidth = 1;
		if($maxWidth === null)
			$maxWidth = "null";
		if($minHeight === null)
			$minHeight = 1;
		if($maxHeight === null)
			$maxHeight = "null";
		$id = $control->DistinctId;
		return array($id,3,"Array(\"$id\",3,$type,$minWidth,$maxWidth,$minHeight,$maxHeight,$ratio)");
	}
	
	static function Left($control, $min=null, $max=null, $type=self::Normal, $ratio=1)
	{
		if($min === null)
			$min = "null";
		if($max === null)
			$max = "null";
		$id = $control->DistinctId;
		return array($id,4,"Array(\"$id\",4,$type,$min,$max,null,null,$ratio)");
	}
	
	static function Top($control, $min=null, $max=null, $type=self::Normal, $ratio=1)
	{
		if($min === null)
			$min = "null";
		if($max === null)
			$max = "null";
		$id = $control->DistinctId;
		return array($id,5,"Array(\"$id\",5,$type,null,null,$min,$max,$ratio)");
	}
	
	static function Location($control, $minLeft=null, $maxLeft=null, $minTop=null, $maxTop=null, $type=self::Normal, $ratio=1)
	{
		if($minLeft === null)
			$minLeft = "null";
		if($maxLeft === null)
			$maxLeft = "null";
		if($minTop === null)
			$minTop = "null";
		if($maxTop === null)
			$maxTop = "null";
		$id = $control->DistinctId;
		return array($id,6,"Array(\"$id\",6,$type,$minLeft,$maxLeft,$minTop,$maxTop,$ratio)");
	}
	
	static function With(Component $object, $shiftType, $constraint=Shift::Mirror, $min=1, $max=null, $ratio=1)
	{
		if($min === null)
			$min = 1;
		if($max === null)
			$max = "null";
		return array($object->DistinctId,7,"$shiftType,0,$min,$max,null,null,$ratio)");
	}
}

?>