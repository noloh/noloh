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
	const Oblivion = 'Oblivion';
	
	private function Animate() {}
	
	static function Property($control, $property, $from, $to, $duration=1000, $units=null, $easing=Animate::Quadratic, $fps=30)
	{
		AddNolohScriptSrc('Animation.js', true);
		QueueClientFunction($control, '_NAniStart', array('\''.$control->Id.'\'', '\''.$property.'\'', $from!==null?$from:0, $to, $duration, '\''.$units.'\'', $easing, $fps), false, Priority::Low);
	}
	
	static function Left($control, $to, $duration=1000, $easing=Animate::Quadratic, $from=null, $fps=30)
	{
		if($to instanceof Control)
			$to = '_N(\''. $to->Id .'\').offsetLeft';
		Animate::Property($control, 'style.left', $from===null?$control->Left:$from, $to, $duration, 'px', $easing, $fps);
	}
	
	static function Top($control, $to, $duration=1000, $easing=Animate::Quadratic, $from=null, $fps=30)
	{
		if($to instanceof Control)
			$to = '_N(\''. $to->Id .'\').offsetTop';
		Animate::Property($control, 'style.top', $from===null?$control->Top:$from, $to, $duration, 'px', $easing, $fps);
	}
	
	static function Location($control, $toLeft, $toTop, $duration=1000, $easing=Animate::Quadratic, $fromLeft=null, $fromTop=null, $fps=30)
	{
		Animate::Left($control, $toLeft, $duration, $easing, $fromLeft, $fps);
		Animate::Top($control, $toTop, $duration, $easing, $fromTop, $fps);
	}
	
	static function Width($control, $to, $duration=1000, $easing=Animate::Quadratic, $from=null, $fps=30)
	{
		self::ProcessOblivion($control, $to);
		Animate::Property($control, 'style.width', $from===null?$control->Width:$from, $to, $duration, 'px', $easing, $fps);
	}
	
	static function Height($control, $to, $duration=1000, $easing=Animate::Quadratic, $from=null, $fps=30)
	{
		self::ProcessOblivion($control, $to);
		Animate::Property($control, 'style.height', $from===null?$control->Height:$from, $to, $duration, 'px', $easing, $fps);
	}
	
	static function Size($control, $toWidth, $toHeight, $duration=1000, $easing=Animate::Quadratic, $fromWidth=null, $fromHeight=null, $fps=30)
	{
		Animate::Property($control, 'style.width', $fromWidth===null?$control->Width:$fromWidth, $toWidth, $duration, 'px', $easing, $fps);
		Animate::Property($control, 'style.height', $fromHeight===null?$control->Height:$fromHeight, $toHeight, $duration, 'px', $easing, $fps);
	}
	
	static function ScrollLeft($control, $to, $duration=1000, $easing=Animate::Quadratic, $from=null, $fps=30)
	{
		if($to instanceof Control)
			$to = '_N(\''. $to->Id .'\').offsetLeft';
		$from = $from===null?$control->ScrollLeft: $from==Layout::Left?0: $from==Layout::Right?9999: $from;
		$to = $to==Layout::Left?0: $to==Layout::Right?9999: $to;
		Animate::Property($control, 'scrollLeft', $from, $to, $duration, '', $easing, $fps);
	}
	
	static function ScrollTop($control, $to, $duration=1000, $easing=Animate::Quadratic, $from=null, $fps=30)
	{
		if($to instanceof Control)
			$to = '_N(\''. $to->Id .'\').offsetTop';
		$from = $from===null?$control->ScrollTop: $from==Layout::Top?0: $from==Layout::Bottom?9999: $from;
		$to = $to==Layout::Top?0: $to==Layout::Bottom?9999: $to;
		Animate::Property($control, 'scrollTop', $from, $to, $duration, '', $easing, $fps);
	}
	
	static function Opacity($control, $to, $duration=1000, $easing=Animate::Quadratic, $from=null, $fps=30)
	{
		self::ProcessOblivion($control, $to);
		Animate::Property($control, 'opacity', $from===null?$control->Opacity:$from, $to, $duration, '', $easing, $fps);
	}
	/**
	 * @ignore
	 */
	static function ProcessOblivion($control, &$to)
	{
		if($to === 'Oblivion')
		{
			$to = 1;
			NolohInternal::SetProperty('_NOblivionC', true, $control);
		}
	}
}

?>