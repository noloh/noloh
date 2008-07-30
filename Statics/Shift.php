<?php
/**
 * Shift class
 *
 * We're sorry, but this class doesn't have a description yet. We're working very hard on our documentation so check back soon!
 * 
 * @package Statics
 */
final class Shift
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
	const All = 'null';

	private function Shift() {}
	
	static function Width($control, $min=1, $max=null, $type=Shift::Normal, $ratio=1)
	{
		$id = $control->Id;
		return array($id,1,'["'.$id.'",1,'.$type.','.$ratio.','.
        	($min===null ? 1 : $min).
        	($max===null ? ']' : (','.$max.']')));
	}

	static function Height($control, $min=1, $max=null, $type=Shift::Normal, $ratio=1)
	{
		$id = $control->Id;
		return array($id,2,'["'.$id.'",2,'.$type.','.$ratio.','.
        	($min===null ? 1 : $min).
        	($max===null ? ']' : (','.$max.']')));
	}

	static function Size($control, $minWidth=1, $maxWidth=null, $minHeight=1, $maxHeight=null, $type=Shift::Normal, $ratio=1)
	{
		$id = $control->Id;
		return array($id,3,'["'.$id.'",3,'.$type.','.$ratio.','.
			($minWidth===null ? 1 : $minWidth).
        	($maxWidth===null ? ',null,' : (','.$maxWidth.',')).
        	($minHeight===null ? 1 : $minHeight).
        	($maxHeight===null ? ']' : (','.$maxHeight.']')));
	}

	static function Left($control, $min=null, $max=null, $type=Shift::Normal, $ratio=1)
	{
		$id = $control->Id;
		$shiftStr = '["'.$id.'",4,'.$type.','.$ratio;
		if($max !== null)
			$shiftStr .= ',' . ($min === null ? 'null' : $min) . ',' . $max;
		elseif($min !== null)
			$shiftStr .= ',' . $min;
        return array($id,4,$shiftStr.']');
	}
	
	static function Top($control, $min=null, $max=null, $type=Shift::Normal, $ratio=1)
	{
		$id = $control->Id;
		$shiftStr = '["'.$id.'",5,'.$type.','.$ratio;
		if($max !== null)
			$shiftStr .= ',' . ($min === null ? 'null' : $min) . ',' . $max;
		elseif($min !== null)
			$shiftStr .= ',' . $min;
        return array($id,5,$shiftStr.']');
	}

	static function Location($control, $minLeft=null, $maxLeft=null, $minTop=null, $maxTop=null, $type=Shift::Normal, $ratio=1)
	{
		$id = $control->Id;
		$shiftStr = '["'.$id.'",6,'.$type.','.$ratio;
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

		return array($id,6,$shiftStr.']');
	}
	
	static function With(Component $object, $shiftMeType, $shiftWithType=Shift::All, $min=1, $max=null, $ratio=1)
	{
		if($min === null)
			$min = 1;
		if($max === null)
			$max = 'null';
		return array($object->Id, 7, $shiftMeType.',0,'.$ratio.','.$min.','.$max.','.$min.','.$max.','.$shiftWithType.']');
	}
}
?>