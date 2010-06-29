<?php
// The code in this class is VERY repetitive. Don't code this way and don't judge me. I'll simplify it at a later date when I have time.

/**
 * Shift class
 *
 * The Shift class contains various static functions and constants that when added to the Shifts
 * ArrayList of any Control, allows that Control to be moved with another. The Shift static functions 
 * fall into two distinct categories, whose differences are crucial to understand for a mastery of Shifts:
 * <ol>
 * <li>Those whose names are properties (e.g., Shift::Left) always define a dragging behavior. Only one of
 * these functions should be defined per drag.
 * <pre>
 * // Makes a Panel draggable.
 * $panel->Shifts[] = Shift::Location($panel);
 * // Makes an Image resize a Panel.
 * $image->Shifts[] = Shift::Size($panel);
 * </li>
 * <li>Those whose names end in 'With' (e.g., Shift::LeftWith) specifies how a Control will behave when
 * another Control is either Shifted <b>or</b> Animated. Notice that good object-oriented techniques
 * imply that in most cases, Shift Withs should be defined rather than regular Shifts. The reason being 
 * that a complex object should know "internally" how its components should stand to one another, and then a 
 * single Shift per drag behavior can be applied externally without having to specify the individual components. 
 * In addition, the With functions work with the Animate functions. 
 * <pre>
 * // Makes a Panel resize with an Image, but without defining a dragging behavior on that Image.
 * $panel->Shifts[] = Shift::SizeWith($image);
 * </pre>
 * </li>
 * </ol>
 * 
 * If the ShiftStart and ShiftStop Events for that Control have been defined, they will be launched when
 * the animation starts and stops, respectfully.
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
	 * A possible value for a min or max bound, Parent specifies that a Control will be bound by the space available in its Parent container.
	 */
	const Parent = 'P';
	/**
	 * A possible value for the min only of Width or Height, Reflect specifies that negative values will reflect the Control through its location.
	 */
	const Reflect = '"R"';
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
	static function Width($control, $min=Shift::Parent, $max=Shift::Parent, $type=Shift::Normal, $ratio=1, $grid=1)
	{
		$id = $control->Id;
		if(func_num_args()>1)
		{
			$args = func_get_args();
			self::BoundsToClient($args, 1, 2);
			unset($args[0]);
			$str = '["'.$id.'",1,' . implode(',', $args) . ']';
//			$str = '["'.$id.'",1,' . implode(',', array_map(array('ClientEvent', 'ClientFormat'), $args)) . ']';
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
	static function Height($control, $min=Shift::Parent, $max=Shift::Parent, $type=Shift::Normal, $ratio=1, $grid=1)
	{
		$id = $control->Id;
		if(func_num_args()>1)
		{
			$args = func_get_args();
			self::BoundsToClient($args, 1, 2);
			unset($args[0]);
			$str = '["'.$id.'",2,' . implode(',', $args) . ']';
//			$str = '["'.$id.'",2,' . implode(',', array_map(array('ClientEvent', 'ClientFormat'), $args)) . ']';
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
	static function Size($control, $minWidth=Shift::Parent, $maxWidth=Shift::Parent, $minHeight=Shift::Parent, $maxHeight=Shift::Parent, $type=Shift::Normal, $ratio=1, $grid=1)
	{
		$id = $control->Id;
		$numArgs = func_num_args();
		if($numArgs > 1)
		{
			$args = func_get_args();
			self::BoundsToClient($args, 1, 2);
			unset($args[0], $args[3], $args[4]);
			$str1 = '["'.$id.'",1,' . implode(',', $args) . ']';
//			$str1 = '["'.$id.'",1,' . implode(',', array_map(array('ClientEvent', 'ClientFormat'), $args)) . ']';
			if($numArgs >= 4)
			{
				$args[1] = $minHeight;
				if($numArgs >= 5)
					$args[2] = $maxHeight;
				else 
					unset($args[2]);
				self::BoundsToClient($args, 1, 2);
			}
			else 
				unset($args[1], $args[2]);
			$str2 = '["'.$id.'",2,' . implode(',', $args) . ']';
//			$str2 = '["'.$id.'",2,' . implode(',', array_map(array('ClientEvent', 'ClientFormat'), $args)) . ']';
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
	static function Left($control, $min=Shift::Parent, $max=Shift::Parent, $type=Shift::Normal, $ratio=1, $grid=1)
	{
		$id = $control->Id;
		if(func_num_args()>1)
		{
			$args = func_get_args();
			self::BoundsToClient($args, 1, 2);
			unset($args[0]);
			$str = '["'.$id.'",4,' . implode(',', $args) . ']';
//			$str = '["'.$id.'",4,' . implode(',', array_map(array('ClientEvent', 'ClientFormat'), $args)) . ']';
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
	static function Top($control, $min=Shift::Parent, $max=Shift::Parent, $type=Shift::Normal, $ratio=1, $grid=1)
	{
		$id = $control->Id;
		if(func_num_args()>1)
		{
			$args = func_get_args();
			self::BoundsToClient($args, 1, 2);
			unset($args[0]);
			$str = '["'.$id.'",5,' . implode(',', $args) . ']';
//			$str = '["'.$id.'",5,' . implode(',', array_map(array('ClientEvent', 'ClientFormat'), $args)) . ']';
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
	static function Location($control, $minLeft=Shift::Parent, $maxLeft=Shift::Parent, $minTop=Shift::Parent, $maxTop=Shift::Parent, $type=Shift::Normal, $ratio=1, $grid=1)
	{
		$id = $control->Id;
		$numArgs = func_num_args();
		if($numArgs > 1)
		{
			$args = func_get_args();
			self::BoundsToClient($args, 1, 2);
			unset($args[0], $args[3], $args[4]);
			$str1 = '["'.$id.'",4,' . implode(',', $args) . ']';
//			$str1 = '["'.$id.'",4,' . implode(',', array_map(array('ClientEvent', 'ClientFormat'), $args)) . ']';
			if($numArgs >= 4)
			{
				$args[1] = $minTop;
				if($numArgs >= 5)
					$args[2] = $maxTop;
				else 
					unset($args[2]);
				self::BoundsToClient($args, 1, 2);
			}
			else 
				unset($args[1], $args[2]);
			$str2 = '["'.$id.'",5,' . implode(',', $args) . ']';
//			$str2 = '["'.$id.'",5,' . implode(',', array_map(array('ClientEvent', 'ClientFormat'), $args)) . ']';
		}
		else 
		{
			$str1 = '["'.$id.'",4]';
			$str2 = '["'.$id.'",5]';
		}
		return array($control->Id, 6, $str1, $str2);
	}
	/**
	 * Allows a property of one Control to move with the property of another Control. 
	 * @param Component $object The Control to be shifted with.
	 * @param mixed $shiftMeType A Shift constant specifying what property of a Control will be changed.
	 * @param mixed $shiftWithType A Shift constant specifying what property the Control will change with.
	 * @param integer $min The minimum bound.
	 * @param integer $max The maximum bound.
	 * @param float $ratio The ratio of the movement of the Control to the movement of the mouse, useful for changing the speed or direction of the movement.
	 * @param integer $grid Indicates the minimum number of pixels that the Control will be moved to create a jumpy, discrete as opossed to a continuous motion.
	 * @return Shift
	 * @deprecated Please use the Shift::{Property}With functions instead (e.g., Shift::LeftWith, etc...). While basic Shift::With functionality is supported for historical reasons, it does not have as much functionality as the Shift::{Property}With counter-parts.
	 */
	static function With(Component $object, $shiftMeType, $shiftWithType=Shift::Mirror, $min=Shift::Parent, $max=Shift::Parent, $ratio=1, $grid=1)
	{
		$array = array($object->Id, 7);
		$numArgs = func_num_args();
		if($numArgs >= 3)
		{
			$args = func_get_args();
			self::BoundsToClient($args, 3, 4);
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
	static function WidthWith(Component $object, $shiftWithType=Shift::Width, $min=Shift::Parent, $max=Shift::Parent, $ratio=1, $grid=1)
	{
		$array = array($object->Id, 7);
		$numArgs = func_num_args();
		if($numArgs >= 2)
		{
			$args = func_get_args();
			self::BoundsToClient($args, 2, 3);
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
	static function HeightWith(Component $object, $shiftWithType=Shift::Height, $min=Shift::Parent, $max=Shift::Parent, $ratio=1, $grid=1)
	{
		$array = array($object->Id, 7);
		$numArgs = func_num_args();
		if($numArgs >= 2)
		{
			$args = func_get_args();
			self::BoundsToClient($args, 2, 3);
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
	static function SizeWith(Component $object, $shiftWithType=Shift::Size, $minWidth=Shift::Parent, $maxWidth=Shift::Parent, $minHeight=Shift::Parent, $maxHeight=Shift::Parent, $ratio=1, $grid=1)
	{
		$array = array($object->Id, 7);
		$numArgs = func_num_args();
		if($numArgs >= 2)
		{
			$args = func_get_args();
			self::BoundsToClient($args, 2, 3);
			unset($args[0], $args[1], $args[4], $args[5]);
			if($numArgs >= 7)
				array_splice($args, 2, 0, $shiftWithType);
			$str1 = '1,' . implode(',', $args) . ']';
			if($numArgs >= 7)
			{
				$args[0] = $minHeight;
				$args[1] = $maxHeight;
				self::BoundsToClient($args, 0, 1);
			}
			elseif($numArgs >= 5)
			{
				$args[2] = $minHeight;
				if($numArgs >= 6)
					$args[3] = $maxHeight;
				else 
					unset($args[3]);
				self::BoundsToClient($args, 2, 3);
			}
			else 
				unset($args[2], $args[3]);
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
	static function LeftWith(Component $object, $shiftWithType=Shift::Left, $min=Shift::Parent, $max=Shift::Parent, $ratio=1, $grid=1)
	{
		$array = array($object->Id, 7);
		$numArgs = func_num_args();
		if($numArgs >= 2)
		{
			$args = func_get_args();
			self::BoundsToClient($args, 2, 3);
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
	static function TopWith(Component $object, $shiftWithType=Shift::Top, $min=Shift::Parent, $max=Shift::Parent, $ratio=1, $grid=1)
	{
		$array = array($object->Id, 7);
		$numArgs = func_num_args();
		if($numArgs >= 2)
		{
			$args = func_get_args();
			self::BoundsToClient($args, 2, 3);
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
	static function LocationWith(Component $object, $shiftWithType=Shift::Location, $minLeft=Shift::Parent, $maxLeft=Shift::Parent, $minTop=Shift::Parent, $maxTop=Shift::Parent, $ratio=1, $grid=1)
	{
		$array = array($object->Id, 7);
		$numArgs = func_num_args();
		if($numArgs >= 2)
		{
			$args = func_get_args();
			unset($args[0], $args[1], $args[3], $args[4]);
			if($numArgs >= 7)
				array_splice($args, 2, 0, $shiftWithType);
			$str1 = '4,' . implode(',', $args) . ']';
			if($numArgs >= 7)
			{
				$args[0] = $minTop;
				$args[1] = $maxTop;
				self::BoundsToClient($args, 0, 1);
			}
			elseif($numArgs >= 5)
			{
				$args[2] = $minTop;
				if($numArgs >= 6)
					$args[3] = $maxTop;
				else 
					unset($args[3]);
				self::BoundsToClient($args, 2, 3);
			}
			else 
				unset($args[2], $args[3]);
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
	/**
	 * @ignore
	 */
	static function BoundsToClient(&$args, $minIdx, $maxIdx)
	{
		if(array_key_exists($minIdx, $args))
		{
			if(($tmp = $args[$minIdx]) === 'P')
				$args[$minIdx] = '';
			elseif($tmp === null)
				$args[$minIdx] = '"N"';
			else
				$args[$minIdx] = ClientEvent::ClientFormat($args[$minIdx]);
			if(array_key_exists($maxIdx, $args))
				if(($tmp = $args[$maxIdx]) === 'P')
					$args[$maxIdx] = '';
				elseif($tmp === null)
					$args[$maxIdx] = '"N"';
				else
					$args[$maxIdx] = ClientEvent::ClientFormat($args[$maxIdx]);
		}
	}
}
?>