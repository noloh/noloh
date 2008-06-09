<?php

/**
 * Animate class
 *
 * This class contains various static functions and constants pertaining to the animation of Controls.
 * 
 * @package Statics
 */

final class Animate
{	
	const Linear = 1;
	const Quadratic = 2;
	const Cubic = 3;
	
	private function Animate() {}
	
	static function Property($control, $property, $from, $to, $duration=1000, $units=null, $easing=Animate::Quadratic, $fps=45)
	{
		AddNolohScriptSrc('Animation.js', true);
		QueueClientFunction($control, '_NAniStart', array('\''.$control->Id.'\'', '\''.$property.'\'', $from!==null?$from:0, $to, $duration, '\''.$units.'\'', $easing, $fps), false, Priority::Low);
	}
	
	static function Left($control, $to, $duration=1000, $easing=Animate::Quadratic, $from=null, $fps=45)
	{
		Animate::Property($control, 'style.left', $from===null?$control->Left:$from, $to, $duration, 'px', $easing, $fps);
	}
	
	static function Top($control, $to, $duration=1000, $easing=Animate::Quadratic, $from=null, $fps=45)
	{
		Animate::Property($control, 'style.top', $from===null?$control->Top:$from, $to, $duration, 'px', $easing, $fps);
	}
	
	static function Location($control, $toLeft, $toTop, $duration=1000, $easing=Animate::Quadratic, $fromLeft=null, $fromTop=null, $fps=45)
	{
		Animate::Property($control, 'style.left', $fromLeft===null?$control->Left:$fromLeft, $toLeft, $duration, 'px', $easing, $fps);
		Animate::Property($control, 'style.top', $fromTop===null?$control->Top:$fromTop, $toTop, $duration, 'px', $easing, $fps);
	}
	
	static function Width($control, $to, $duration=1000, $easing=Animate::Quadratic, $from=null, $fps=45)
	{
		Animate::Property($control, 'style.width', $from===null?$control->Width:$from, $to, $duration, 'px', $easing, $fps);
	}
	
	static function Height($control, $to, $duration=1000, $easing=Animate::Quadratic, $from=null, $fps=45)
	{
		Animate::Property($control, 'style.height', $from===null?$control->Height:$from, $to, $duration, 'px', $easing, $fps);
	}
	
	static function Size($control, $toWidth, $toHeight, $duration=1000, $easing=Animate::Quadratic, $fromWidth=null, $fromHeight=null, $fps=45)
	{
		Animate::Property($control, 'style.width', $fromWidth===null?$control->Width:$fromWidth, $toWidth, $duration, 'px', $easing, $fps);
		Animate::Property($control, 'style.height', $fromHeight===null?$control->Height:$fromHeight, $toHeight, $duration, 'px', $easing, $fps);
	}
	
	static function ScrollLeft($control, $to, $duration=1000, $easing=Animate::Quadratic, $from=null, $fps=45)
	{
		Animate::Property($control, 'scrollLeft', $from===null?$control->ScrollLeft:$from, $to, $duration, '', $easing, $fps);
	}
	
	static function ScrollTop($control, $to, $duration=1000, $easing=Animate::Quadratic, $from=null, $fps=45)
	{
		Animate::Property($control, 'scrollTop', $from===null?$control->ScrollTop:$from, $to, $duration, '', $easing, $fps);
	}
	
	static function Opacity($control, $to, $duration=1000, $easing=Animate::Quadratic, $from=null, $fps=45)
	{
		Animate::Property($control, 'opacity', $from===null?$control->Opacity:$from, $to, $duration, '', $easing, $fps);
		/*if($_SESSION['_NIsIE'])
			Animate::Property($control, '.filter="alpha(opacity', $from===null?$control->Opacity:$from, $to, $duration, ')"', $easing, $fps);
		else
			Animate::Property($control, 'style.opacity', ($from===null?$control->Opacity:$from)/100.0, $to/100.0, $duration, '', $easing, $fps);*/
	}
}

?>