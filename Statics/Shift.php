<?php
// The code in this class is VERY repetitive. Don't code this way and don't judge me. I'll simplify it at a later date when I have time.

/**
 * Shift class
 *
 * The Shift class contains various static functions and constants that when added to the Shifts
 * ArrayList of any Control, allows the Control to be moved with another. Shift::With indicates
 * that a Control will Shift when another Control is Shifted or Animated, while the other Shifts
 * indicate a dragging behavior. 
 * 
 * <pre>
 * // Makes a panel draggable.
 * $panel->Shifts[] = Shift::Location($panel);
 * // Makes an image resize a panel.
 * $image->Shifts[] = Shift::Size($panel);
 * </pre>
 * 
 * If the ShiftStart and ShiftStop Events for that Control have been defined, they will be launched when
 * the animation starts and stops, respectfully.
 * If min and max bounds have not been set, the default behavior will be bounding to the size of the parent object.
 * 
 * @package Statics
 */
final class Shift
{
	/**
	 * A possible value for the type parameter, Normal indicates standard dragging behavior.
	 */
	const Normal = 0;
	/**
	 * A possible value for the type parameter, Ghost indicates that a transparant copy of the object will be temporarily created which will be draggable while the original object stays in place.
	 */
	const Ghost = 1;
	/**
	 * A possible value for the shiftWithType parameter, Width indicates that an object will Shift with the Width of another object.
	 */
	const Width = 1;
	/**
	 * A possible value for the shiftWithType parameter, Height indicates that an object will Shift with the Height of another object.
	 */
	const Height = 2;
	/**
	 * A possible value for the shiftWithType parameter, Size indicates that an object will Shift with the Size of another object.
	 */
	const Size = 3;
	/**
	 * A possible value for the shiftWithType parameter, Left indicates that an object will Shift with the Left of another object.
	 */
	const Left = 4;
	/**
	 * A possible value for the shiftWithType parameter, Top indicates that an object will Shift with the Top of another object.
	 */
	const Top = 5;
	/**
	 * A possible value for the shiftWithType parameter, Location indicates that an object will Shift with the Location of another object.
	 */
	const Location = 6;
	/**
	 * A possible value for the shiftWithType parameter, Mirror indicates that an object will Shift with the same property as the function specifies.
	 */
	const Mirror = 0;
	/**
	 * @ignore
	 */
	const All = 7;

	private function Shift() {}
	/**
	 * Allows the Width of a specified Control to be changed via dragging.
	 * @param Control $control The Control to be shifted.
	 * @param integer $min The minimum bound.
	 * @param integer $max The maximum bound.
	 * @param mixed $type Indicates whether Normal or Ghost behavior is used.
	 * @param float $ratio The ratio of the movement of the Control to the movement of the mouse, useful for changing the speed or direction of the movement.
	 * @param integer $grid Indicates the minimum number of pixels that the Control will be moved to create a jumpy, discrete as opossed to a continuous motion.
	 * @return Shift
	 */
	static function Width($control, $min=1, $max=null, $type=Shift::Normal, $ratio=1, $grid=1)
	{
		$id = $control->Id;
		if(func_num_args()>1)
		{
			$args = func_get_args();
			unset($args[0]);
			$str = '["'.$id.'",1,' . implode(',', $args) . ']';
		}
		else 
			$str = '["'.$id.'",1]';
		return array($id, 1, $str);
	}
	/**
	 * Allows the Height of a specified Control to be changed via dragging.
	 * @param Control $control The Control to be shifted.
	 * @param integer $min The minimum bound.
	 * @param integer $max The maximum bound.
	 * @param mixed $type Indicates whether Normal or Ghost behavior is used.
	 * @param float $ratio The ratio of the movement of the Control to the movement of the mouse, useful for changing the speed or direction of the movement.
	 * @param integer $grid Indicates the minimum number of pixels that the Control will be moved to create a jumpy, discrete as opossed to a continuous motion.
	 * @return Shift
	 */
	static function Height($control, $min=1, $max=null, $type=Shift::Normal, $ratio=1, $grid=1)
	{
		$id = $control->Id;
		if(func_num_args()>1)
		{
			$args = func_get_args();
			unset($args[0]);
			$str = '["'.$id.'",2,' . implode(',', $args) . ']';
		}
		else 
			$str = '["'.$id.'",2]';
		return array($id, 2, $str);
	}
	/**
	 * Allows the Size (both Width and Height) of a specified Control to be changed via dragging.
	 * @param Control $control The Control to be shifted.
	 * @param integer $minWidth The minimum horizontal bound.
	 * @param integer $maxWidth The maximum horizontal bound.
	 * @param integer $minHeight The minimum vertical bound.
	 * @param integer $maxHeight The maximum vertical bound.
	 * @param mixed $type Indicates whether Normal or Ghost behavior is used.
	 * @param float $ratio The ratio of the movement of the Control to the movement of the mouse, useful for changing the speed or direction of the movement.
	 * @param integer $grid Indicates the minimum number of pixels that the Control will be moved to create a jumpy, discrete as opossed to a continuous motion.
	 * @return Shift
	 */
	static function Size($control, $minWidth=1, $maxWidth=null, $minHeight=1, $maxHeight=null, $type=Shift::Normal, $ratio=1, $grid=1)
	{
		$id = $control->Id;
		$numArgs = func_num_args();
		if($numArgs > 1)
		{
			$args = func_get_args();
			unset($args[0], $args[3], $args[4]);
			$str1 = '["'.$id.'",1,' . implode(',', $args) . ']';
			if($numArgs > 3)
				$args[1] = $minHeight;
			if($numArgs > 4)
				$args[2] = $maxHeight;
			$str2 = '["'.$id.'",2,' . implode(',', $args) . ']';
		}
		else 
		{
			$str1 = '["'.$id.'",1]';
			$str2 = '["'.$id.'",2]';
		}
		return array($control->Id, 3, $str1, $str2);
	}
	/**
	 * Allows the Left of a specified Control to be changed via dragging.
	 * @param Control $control The Control to be shifted.
	 * @param integer $min The minimum bound.
	 * @param integer $max The maximum bound.
	 * @param mixed $type Indicates whether Normal or Ghost behavior is used.
	 * @param float $ratio The ratio of the movement of the Control to the movement of the mouse, useful for changing the speed or direction of the movement.
	 * @param integer $grid Indicates the minimum number of pixels that the Control will be moved to create a jumpy, discrete as opossed to a continuous motion.
	 * @return Shift
	 */
	static function Left($control, $min=0, $max=null, $type=Shift::Normal, $ratio=1, $grid=1)
	{
		$id = $control->Id;
		if(func_num_args()>1)
		{
			$args = func_get_args();
			unset($args[0]);
			$str = '["'.$id.'",4,' . implode(',', $args) . ']';
		}
		else 
			$str = '["'.$id.'",4]';
		return array($id, 4, $str);
	}
	/**
	 * Allows the Top of a specified Control to be changed via dragging.
	 * @param Control $control The Control to be shifted.
	 * @param integer $min The minimum bound.
	 * @param integer $max The maximum bound.
	 * @param mixed $type Indicates whether Normal or Ghost behavior is used.
	 * @param float $ratio The ratio of the movement of the Control to the movement of the mouse, useful for changing the speed or direction of the movement.
	 * @param integer $grid Indicates the minimum number of pixels that the Control will be moved to create a jumpy, discrete as opossed to a continuous motion.
	 * @return Shift
	 */
	static function Top($control, $min=0, $max=null, $type=Shift::Normal, $ratio=1, $grid=1)
	{
		$id = $control->Id;
		if(func_num_args()>1)
		{
			$args = func_get_args();
			unset($args[0]);
			$str = '["'.$id.'",5,' . implode(',', $args) . ']';
		}
		else 
			$str = '["'.$id.'",5]';
		return array($id, 5, $str);
	}
	/**
	 * Allows the Location (both Left and Top) of a specified Control to be changed via dragging.
	 * @param Control $control The Control to be shifted.
	 * @param integer $minLeft The minimum horizontal bound.
	 * @param integer $maxLeft The maximum horizontal bound.
	 * @param integer $minTop The minimum vertical bound.
	 * @param integer $maxTop The maximum vertical bound.
	 * @param mixed $type Indicates whether Normal or Ghost behavior is used.
	 * @param float $ratio The ratio of the movement of the Control to the movement of the mouse, useful for changing the speed or direction of the movement.
	 * @param integer $grid Indicates the minimum number of pixels that the Control will be moved to create a jumpy, discrete as opossed to a continuous motion.
	 * @return Shift
	 */
	static function Location($control, $minLeft=0, $maxLeft=null, $minTop=0, $maxTop=null, $type=Shift::Normal, $ratio=1, $grid=1)
	{
		$id = $control->Id;
		$numArgs = func_num_args();
		if($numArgs > 1)
		{
			$args = func_get_args();
			unset($args[0], $args[3], $args[4]);
			$str1 = '["'.$id.'",4,' . implode(',', $args) . ']';
			if($numArgs > 3)
				$args[1] = $minTop;
			if($numArgs > 4)
				$args[2] = $maxTop;
			$str2 = '["'.$id.'",5,' . implode(',', $args) . ']';
		}
		else 
		{
			$str1 = '["'.$id.'",4]';
			$str2 = '["'.$id.'",5]';
		}
		return array($control->Id, 6, $str1, $str2);
	}
	/**
	 * Allows a property of one Control to move with the property of another Control. DEPRECATED: Please use the Shift::___With functions instead.
	 * @param Component $object The Control to be shifted with.
	 * @param mixed $shiftMeType A Shift constant specifying what property of a Control will be changed.
	 * @param mixed $shiftWithType A Shift constant specifying what property the Control will change with.
	 * @param integer $min The minimum bound.
	 * @param integer $max The maximum bound.
	 * @param float $ratio The ratio of the movement of the Control to the movement of the mouse, useful for changing the speed or direction of the movement.
	 * @param integer $grid Indicates the minimum number of pixels that the Control will be moved to create a jumpy, discrete as opossed to a continuous motion.
	 * @return Shift
	 */
	static function With(Component $object, $shiftMeType, $shiftWithType=Shift::Mirror, $min=null, $max=null, $ratio=null, $grid=1)
	{
		$array = array($object->Id, 7);
		$numArgs = func_num_args();
		if($numArgs >= 3)
		{
			$args = func_get_args();
			unset($args[0], $args[2]);
			if($numArgs >= 6)
				array_splice($args, 3, 0, $shiftWithType);
			if($shiftMeType === 3 || $shiftMeType === 6)
			{
				unset($args[0]);
				$str = ',' . implode(',', $args) . ']';
				if($shiftMeType === 3)
					if(!$shiftWithType || $shiftWithType === 3)
						array_push($array, 1, '1'.$str, 2, '2'.$str);
					elseif($shiftWithType === 6)
						array_push($array, 4, '1'.$str, 5, '2'.$str);
					else
						array_push($array, $shiftWithType, '1'.$str, $shiftWithType, '2'.$str);
				else
					if(!$shiftWithType || $shiftWithType === 6)
						array_push($array, 4, '4'.$str, 5, '5'.$str);
					elseif($shiftWithType === 3)
						array_push($array, 1, '4'.$str, 2, '5'.$str);
					else
						array_push($array, $shiftWithType, '4'.$str, $shiftWithType, '5'.$str);
			}
			else 
			{
				$str = implode(',', $args) . ']';
				if($shiftWithType === 3)
					array_push($array, 1, $str, 2, $str);
				elseif($shiftWithType === 6)
					array_push($array, 4, $str, 5, $str);
				else
					array_push($array, $shiftWithType ? $shiftWithType : $shiftMeType, $str);
			}
		}
		else 
			if($shiftMeType === 3)
				array_push($array, 1, '1]', 2, '2]');
			elseif($shiftMeType === 6)
				array_push($array, 4, '4]', 5, '5]');
			else 
				array_push($array, $shiftWithType ? $shiftWithType : $shiftMeType, $shiftMeType.']');		
		return $array;
	}
	/**
	 * Allows a Control's Width to shift with another Control.
	 * @param Component $object The Control to be shifted with.
	 * @param mixed $shiftWithType A Shift constant specifying what property the Control will change with.
	 * @param integer $min The minimum bound.
	 * @param integer $max The maximum bound.
	 * @param float $ratio The ratio of the movement of the Control to the movement of the mouse, useful for changing the speed or direction of the movement.
	 * @param integer $grid Indicates the minimum number of pixels that the Control will be moved to create a jumpy, discrete as opossed to a continuous motion.
	 * @return Shift
	 */
	static function WidthWith(Component $object, $shiftWithType=Shift::Width, $min=null, $max=null, $ratio=null, $grid=1)
	{
		$array = array($object->Id, 7);
		$numArgs = func_num_args();
		if($numArgs >= 2)
		{
			$args = func_get_args();
			unset($args[0], $args[1]);
			if($numArgs >= 5)
				array_splice($args, 2, 0, $shiftWithType);
			$str = '1,' . implode(',', $args) . ']';
			if($shiftWithType === 3)
				array_push($array, 1, $str, 2, $str);
			elseif($shiftWithType === 6)
				array_push($array, 4, $str, 5, $str);
			else
				array_push($array, $shiftWithType ? $shiftWithType : 1, $str);
		}
		else 
			array_push($array, $shiftWithType ? $shiftWithType : 1, '1]');
		return $array;
	}
	/**
	 * Allows a Control's Height to shift with another Control.
	 * @param Component $object The Control to be shifted with.
	 * @param mixed $shiftWithType A Shift constant specifying what property the Control will change with.
	 * @param integer $min The minimum bound.
	 * @param integer $max The maximum bound.
	 * @param float $ratio The ratio of the movement of the Control to the movement of the mouse, useful for changing the speed or direction of the movement.
	 * @param integer $grid Indicates the minimum number of pixels that the Control will be moved to create a jumpy, discrete as opossed to a continuous motion.
	 * @return Shift
	 */
	static function HeightWith(Component $object, $shiftWithType=Shift::Height, $min=null, $max=null, $ratio=null, $grid=1)
	{
		$array = array($object->Id, 7);
		$numArgs = func_num_args();
		if($numArgs >= 2)
		{
			$args = func_get_args();
			unset($args[0], $args[1]);
			if($numArgs >= 5)
				array_splice($args, 2, 0, $shiftWithType);
			$str = '2,' . implode(',', $args) . ']';
			if($shiftWithType === 3)
				array_push($array, 1, $str, 2, $str);
			elseif($shiftWithType === 6)
				array_push($array, 4, $str, 5, $str);
			else
				array_push($array, $shiftWithType ? $shiftWithType : 2, $str);
		}
		else 
			array_push($array, $shiftWithType ? $shiftWithType : 2, '2]');
		return $array;
	}
	/**
	 * Allows a Control's Size (both Width and Height) to shift with another Control.
	 * @param Component $object The Control to be shifted with.
	 * @param mixed $shiftWithType A Shift constant specifying what property the Control will change with.
	 * @param integer $minWidth The minimum horizontal bound.
	 * @param integer $maxWidth The maximum horizontal bound.
	 * @param integer $minHeight The minimum vertical bound.
	 * @param integer $maxHeight The maximum vertical bound.
	 * @param float $ratio The ratio of the movement of the Control to the movement of the mouse, useful for changing the speed or direction of the movement.
	 * @param integer $grid Indicates the minimum number of pixels that the Control will be moved to create a jumpy, discrete as opossed to a continuous motion.
	 * @return Shift
	 */
	static function SizeWith(Component $object, $shiftWithType=Shift::Size, $minWidth=null, $minHeight=null, $maxWidth=null, $maxHeight=null, $ratio=null, $grid=1)
	{
		$array = array($object->Id, 7);
		$numArgs = func_num_args();
		if($numArgs >= 2)
		{
			$args = func_get_args();
			unset($args[0], $args[1], $args[3], $args[5]);
			if($numArgs >= 6)
				array_splice($args, 2, 0, $shiftWithType);
			$str1 = '1,' . implode(',', $args) . ']';
			if($numArgs >= 4)
			{
				$args[0] = $minHeight;
				if($numArgs >= 6)
					$args[1] = $maxHeight;
			}
			$str2 = '2,' . implode(',', $args) . ']';
			if(!$shiftWithType || $shiftWithType === 3)
				array_push($array, 1, $str1, 2, $str2);
			elseif($shiftWithType === 6)
				array_push($array, 4, $str1, 5, $str2);
			else
				array_push($array, $shiftWithType, $str1, $shiftWithType, $str2);
		}
		else 
			array_push($array, 1, '1]', 2, '2]');
		return $array;
	}
	/**
	 * Allows a Control's Left to shift with another Control.
	 * @param Component $object The Control to be shifted with.
	 * @param mixed $shiftWithType A Shift constant specifying what property the Control will change with.
	 * @param integer $min The minimum bound.
	 * @param integer $max The maximum bound.
	 * @param float $ratio The ratio of the movement of the Control to the movement of the mouse, useful for changing the speed or direction of the movement.
	 * @param integer $grid Indicates the minimum number of pixels that the Control will be moved to create a jumpy, discrete as opossed to a continuous motion.
	 * @return Shift
	 */
	static function LeftWith(Component $object, $shiftWithType=Shift::Left, $min=null, $max=null, $ratio=null, $grid=1)
	{
		$array = array($object->Id, 7);
		$numArgs = func_num_args();
		if($numArgs >= 2)
		{
			$args = func_get_args();
			unset($args[0], $args[1]);
			if($numArgs >= 5)
				array_splice($args, 2, 0, $shiftWithType);
			$str = '4,' . implode(',', $args) . ']';
			if($shiftWithType === 3)
				array_push($array, 1, $str, 2, $str);
			elseif($shiftWithType === 6)
				array_push($array, 4, $str, 5, $str);
			else
				array_push($array, $shiftWithType ? $shiftWithType : 4, $str);
		}
		else 
			array_push($array, $shiftWithType ? $shiftWithType : 4, '4]');
		return $array;
	}
	/**
	 * Allows a Control's Top to shift with another Control.
	 * @param Component $object The Control to be shifted with.
	 * @param mixed $shiftWithType A Shift constant specifying what property the Control will change with.
	 * @param integer $min The minimum bound.
	 * @param integer $max The maximum bound.
	 * @param float $ratio The ratio of the movement of the Control to the movement of the mouse, useful for changing the speed or direction of the movement.
	 * @param integer $grid Indicates the minimum number of pixels that the Control will be moved to create a jumpy, discrete as opossed to a continuous motion.
	 * @return Shift
	 */
	static function TopWith(Component $object, $shiftWithType=Shift::Top, $min=null, $max=null, $ratio=null, $grid=1)
	{
		$array = array($object->Id, 7);
		$numArgs = func_num_args();
		if($numArgs >= 2)
		{
			$args = func_get_args();
			unset($args[0], $args[1]);
			if($numArgs >= 5)
				array_splice($args, 2, 0, $shiftWithType);
			$str = '5,' . implode(',', $args) . ']';
			if($shiftWithType === 3)
				array_push($array, 1, $str, 2, $str);
			elseif($shiftWithType === 6)
				array_push($array, 4, $str, 5, $str);
			else
				array_push($array, $shiftWithType ? $shiftWithType : 5, $str);
		}
		else 
			array_push($array, $shiftWithType ? $shiftWithType : 5, '5]');
		return $array;
	}
	/**
	 * Allows a Control's Size (both Left and Top) to shift with another Control.
	 * @param Component $object The Control to be shifted with.
	 * @param mixed $shiftWithType A Shift constant specifying what property the Control will change with.
	 * @param integer $minLeft The minimum horizontal bound.
	 * @param integer $maxLeft The maximum horizontal bound.
	 * @param integer $minTop The minimum vertical bound.
	 * @param integer $maxTop The maximum vertical bound.
	 * @param float $ratio The ratio of the movement of the Control to the movement of the mouse, useful for changing the speed or direction of the movement.
	 * @param integer $grid Indicates the minimum number of pixels that the Control will be moved to create a jumpy, discrete as opossed to a continuous motion.
	 * @return Shift
	 */
	static function LocationWith(Component $object, $shiftWithType=Shift::Location, $minLeft=null, $minTop=null, $maxLeft=null, $maxTop=null, $ratio=null, $grid=1)
	{
		$array = array($object->Id, 7);
		$numArgs = func_num_args();
		if($numArgs >= 2)
		{
			$args = func_get_args();
			unset($args[0], $args[1], $args[3], $args[5]);
			if($numArgs >= 6)
				array_splice($args, 2, 0, $shiftWithType);
			$str1 = '4,' . implode(',', $args) . ']';
			if($numArgs >= 4)
			{
				$args[0] = $minTop;
				if($numArgs >= 6)
					$args[1] = $maxTop;
			}
			$str2 = '5,' . implode(',', $args) . ']';
			if(!$shiftWithType || $shiftWithType === 6)
				array_push($array, 4, $str1, 5, $str2);
			elseif($shiftWithType === 3)
				array_push($array, 1, $str1, 2, $str2);
			else
				array_push($array, $shiftWithType, $str1, $shiftWithType, $str2);
		}
		else 
			array_push($array, 4, '4]', 5, '5]');
		return $array;
	}
}
?>