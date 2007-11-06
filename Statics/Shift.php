<?php
/**
 * @package Statics
 */
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
		$id = $control->Id;
		return array($id,1,'Array("'.$id.'",1,'.$type.','.$ratio.','.
        	($min===null ? 1 : $min).
        	($max===null ? ')' : (','.$max.')')));
	}

	static function Height($control, $min=1, $max=null, $type=self::Normal, $ratio=1)
	{
		$id = $control->Id;
		return array($id,2,'Array("'.$id.'",2,'.$type.','.$ratio.','.
        	($min===null ? 1 : $min).
        	($max===null ? ')' : (','.$max.')')));
	}

	static function Size($control, $minWidth=1, $maxWidth=null, $minHeight=1, $maxHeight=null, $type=self::Normal, $ratio=1)
	{
		$id = $control->Id;
		return array($id,3,'Array("'.$id.'",3,'.$type.','.$ratio.','.
			($minWidth===null ? 1 : $minWidth).
        	($maxWidth===null ? ',null,' : (','.$maxWidth.',')).
        	($minHeight===null ? 1 : $minHeight).
        	($maxHeight===null ? ')' : (','.$maxheight.')')));
	}

	static function Left($control, $min=null, $max=null, $type=self::Normal, $ratio=1)
	{
		$id = $control->Id;
		$shiftStr = 'Array("'.$id.'",4,'.$type.','.$ratio;
		if($max !== null)
			$shiftStr .= ',' . ($min === null ? 'null' : $min) . ',' . $max;
		elseif($min !== null)
			$shiftStr .= ',' . $min;
        return array($id,4,$shiftStr.')');
	}
	
	static function Top($control, $min=null, $max=null, $type=self::Normal, $ratio=1)
	{
		$id = $control->Id;
		$shiftStr = 'Array("'.$id.'",5,'.$type.','.$ratio;
		if($max !== null)
			$shiftStr .= ',' . ($min === null ? 'null' : $min) . ',' . $max;
		elseif($min !== null)
			$shiftStr .= ',' . $min;
        return array($id,5,$shiftStr.')');
	}

	static function Location($control, $minLeft=null, $maxLeft=null, $minTop=null, $maxTop=null, $type=self::Normal, $ratio=1)
	{
		$id = $control->Id;
		$shiftStr = 'Array("'.$id.'",6,'.$type.','.$ratio;
		if($maxTop !== null)
			$shiftStr .= ',' .
				($minLeft === null ? 'null' : $minLeft) . ',' .
				($maxLeft === null ? 'null' : $maxLeft) . ',' .
				($minTop === null ? 'null' : $minTop) . ',' .
				$maxTop;
		elseif($minTop !== null)
			$shiftStr .= ',' .
				($minLeft === null ? 'null' : $minLeft) . ',' .
				($maxLeft === null ? 'null' : $maxLeft) . ',' .
				$minTop;
		elseif($maxLeft !== null)
			$shiftStr .= ',' .
				($minLeft === null ? 'null' : $minLeft) . ',' .
				$maxLeft;
		elseif($minLeft !== null)
			$shiftStr .= ',' .
				$minLeft;

		return array($id,6,$shiftStr.')');
	}
	
	static function With(Component $object, $shiftType, $constraint=Shift::Mirror, $min=1, $max=null, $ratio=1)
	{
		if($min === null)
			$min = 1;
		if($max === null)
			$max = "null";
		return array($object->Id, 7, "$shiftType,0,$ratio,$min,$max)");
	}
}
?>