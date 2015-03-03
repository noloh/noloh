<?php
/**
 * TabPage class
 *
 * A TabPage represents a Tab and Panel in the TabPanel Control.
 * 
 * <pre>
 * $tabPanel = new TabPanel();
 * $cars = new TabPage('Cars');
 * $trains = new TabPage('Trains');
 *     
 * $tabPanel->TabPages->AddRange($cars, $trains);
 * </pre>
 * @package Controls/Auxiliary
 */
class TabPage extends Panel 
{
	private $RolloverTab;
	/**
	 * Constructor
	 * @param string|Control $text
	 * @return TabPage
	 */
	function TabPage($text='TabPage')
	{
		parent::Panel(0, 0, '100%', '100%');
		$this->SetRolloverTab($text);
	}
	/**
	 * Assigns the RolloverTab to be used with the TabPage. 
	 * This is useful in situations where you prefer to set a custom look and feel
	 * for the RolloverTab.
	 * @param RolloverTab $rolloverTab
	 */
	public function SetRolloverTab($rolloverTab = null)
	{
		if(!is_object($rolloverTab))
			$rolloverTab = new RolloverTab($rolloverTab);
		$this->RolloverTab = $rolloverTab;
		$rolloverTab->Leave = new ClientEvent('_NLeave', $this);
	}
	/**
	* @ignore
	*/
	public function SetSelected($bool)	{$this->RolloverTab->SetSelected($bool);}
	/**
	* @ignore
	*/
	public function GetSelected()	{return $this->RolloverTab->GetSelected();}
	/**
	 * Returns the RolloverTab associated with the TabPage
	 * @return RolloverTab
	 */
	public function GetRolloverTab(){return $this->RolloverTab;}
	/**
	 * @ignore
	 */
	public function SetText($text)	{$this->RolloverTab->SetText($text);}
	/**
	 * @ignore
	 */
	public function GetText()		{return $this->RolloverTab->GetText();}
	/**
	* @ignore
	*/
	public function GetWidth()
	{
		if(($parent = $this->GetParent()) !== null)
			return $parent->GetWidth();
		else
			return parent::GetWidth();
	}
	/**
	* Sets whether this TabPage's Tab is closeable. A value of true will display an x the user can click to remove the TabPage.
	* 
	* @param mixed $closeable Whether this RolloverTab is closeable
	* @param mixed $object Optional object for the close.
	*/
	public function SetCloseable($bool, $image=null)
	{
		$this->RolloverTab->SetCloseable($bool, $image=null);
	}
	/**
	* Sets whether this TabPage's Tab is closeable. A value of true will display an x the user can click to remove the Tab.
	*/
	public function GetCloseable()	{return $this->RolloverTab->GetCloseable();}
	/**
	* @ignore
	*/
	public function GetHeight()
	{
		if(($parent = $this->GetParent()) !== null)
			return $parent->GetHeight();
		else
			return parent::GetHeight();
	}
}
?>