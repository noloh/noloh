<?php
/**
 * System class
 *
 * The System class contains various constants used by various parts of NOLOH, especially constants having to do with the
 * physical properties of controls such as size.
 * 
 * @package Statics
 */
final class System
{
	/**
	 * @ignore
	 */
	private function System(){}
	/**
	 * System::Auto is used to indicate that various properties should figured out their values automatically.
	 * For example:
	 * <code>
	 * // Creates a label with automatic width and height
	 * $lbl = new Label("This is my string", 0, 0, System::Auto, System::Auto);
	 * // Will Alert the actual width, in pixels, after performing a calculation 
	 * // based on the string and font size
	 * Alert($lbl->Width);
	 * </code>
	 */
	const Auto = 'Auto';
	/**
	 * System::AutoHtmlTrim is used to indicate that various properties should figured out their values automatically
	 * and any HTML in them should be trimmed out.
	 * For example:
	 * <code>
	 * // Creates a label with automatic width and height and HTML Trimming
	 * $lbl = new Label("<b>This is my string</b>", 0, 0, System::AutoHtmlTrim, System::AutoHtmlTrim);
	 * // Will Alert the actual width, in pixels, after performing a calculation based on 
	 * // the string and font size, while not considering the bold tags as part of the string.
	 * Alert($lbl->Width);
	 * </code>
	 */
	const AutoHtmlTrim = 'HtmlTrim';
	/**
	 * System::Full is used to indicate that various properties should expand to accomodate the full control.
	 * For example:
	 * <code>
	 * // Instantiate a new Panel
	 * $pnl = new Panel();
	 * // Tells the Panel to not cut off its contents, hence, the panel's width and height will be largely ignored.
	 * $pnl->Scrolling = System::Full;
	 * </code>
	 */
	const Full = 'Full';
	/**
	 * System::Vacuous is used in connection with {@link Control::SetVisible()} to indicate that the control will not
	 * take up space. This is similar to false except that if either static or relative Layout is used, the
	 * control will not occupy space.
	 * <code>
	 * // Add a new Button
	 * $this->Controls->Add($btn1 = new Button());
	 * // Add another new Button
	 * $this->Controls->Add($btn2 = new Button());
	 * // Position them both statically
	 * $btn1->Layout = $btn2->Layout = 2;
	 * // Make the first button vacuous
	 * $btn1->Visible = System::Vacuous;
	 * // Now $btn2 will be on the left side of the screen, not to the right of an invisible object
	 * </code>
	 */
	const Vacuous = null;
	/**
	* System::Unhandled is used in connection with {@link SetStartUpPage} as the fifth, $debugMode, parameter to 
	* indicate that NOLOH's error handling will be disabled and regular crashing behavior will occur in case of an error.
	*/
	const Unhandled = 'Unhandled';
	
	const Horizontal = 'horizontal';
	const Vertical = 'vertical';
	
	static function Log($text)
	{
		//Currently only works with firebug, ideally this would log to our debug window, or firebug with a param
		//See http://getfirebug.com/console.html for some ideas
		if(!UserAgent::IsIE())
			AddScript('console.log(' . ClientEvent::ClientFormat($text) . ');');
	}
}

?>