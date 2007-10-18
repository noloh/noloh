<?php
/**
 * @package Web.UI.Controls
 * Guardian class file.
 */
 
/**
 * Guardian class
 *
 * Guardian is the Parent Class for Components that have a Controls ArrayList
 * 
 * If you're making a Control that has a Controls ArrayList and is <b>NOT</b> a Panel, it is recommended that you extend this Guardian Class
 * <br>
 * See Also - Panel
 * 
 * In addition, if you extend Guardian you <b>MUST</b> override the Show() in order for it to display correctly. 
 * It is recommended that you extend Panel in most cases, to avoid having to override the Show();
 * <br>
 * Properties
 * - <b>Controls</b>, ArrayList, 
 *   <br>The Container for all Controls in this Component
 * 
 * You can use the Guardian as follows
 * <code>
 *
 * class MyNewControl extends Guardian
 * {
 * 		function MyNewHolder()
 *		{
 *			parent::Guardian(0,0,100, 100);
 *		}
 *		
 * </code>
 */
	class Guardian extends Control
	{
		/**
 		* Controls, An ArrayList to hold this Control's Controls
 		* @var ArrayList
 		*/
		public $Controls;
		/**
		 * ScrollLeft of the component
		 * @var integer
		 */
		private $ScrollLeft;
		/**
		 * ScrollTop of the component
		 * @var integer
		 */
		private $ScrollTop;

		/**
		* Constructor.
		* for inherited components, be sure to call the parent constructor first
	 	* so that the component properties and events are defined.
	 	* Example
	 	*	<code> $tempVar = new Guardian(15, 15, 219, 21);</code>
		* @param integer|optional
		* @param integer|optional
		* @param integer|optional
		* @param integer|optional
		*/
		function Guardian($whatLeft=0, $whatTop=0, $whatWidth = 0, $whatHeight = 0, $implicitObject=null)
		{
			parent::Control($whatLeft, $whatTop, $whatWidth, $whatHeight);
			if($implicitObject == null)
				$this->Controls = new ArrayList();
			elseif($implicitObject == $this)
				$this->Controls = new ImplicitArrayList(null);
			else 
				$this->Controls = new ImplicitArrayList($implicitObject);
			$this->Controls->ParentId = $this->Id;
		}
		function GetScroll()							{return $this->GetEvent('Scroll');}
		function SetScroll($newScroll)					{$this->SetEvent($newScroll, 'Scroll');}
        function SetScrollLeft($scrollLeft)
        {
            NolohInternal::SetProperty('scrollLeft', $scrollLeft, $this);
        }
        function SetScrollTop($scrollTop)
        {
            NolohInternal::SetProperty('scrollTop', $scrollTop, $this);
        }
	}
?>