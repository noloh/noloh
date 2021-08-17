<?php
/**
 * Form class
 *
 * The Form class can be used to Get or Post data to a server. It is discouraged in the strongest possible terms to use this as a way of 
 * communicating with the server running your NOLOH application, as various ways of doing so are built-in and much easier to use, e.g., 
 * a ServerEvent or curl. This class should probably only be used to communicate with an outside server unfortunate enough to not have NOLOH 
 * installed on it running a website or web service.
 * 
 * @package Controls/Core
 */
class Form extends Component 
{
	/**
	 * Posting is the most common Method of submitting data to a server. It is known to be slightly slower than Get but has no character limit and 
	 * is better at handling Unicode. It is commonly suggested to use Post when something on the server is being modified.
	 */
	const Post = 'POST';
	/**
	 * Getting is a Method of submitting data to a server via URL tokens, so the values are exposed and visible to a user. 
	 * Note also that there is usually a character limit on the size of the submission, typically 2000 characters.
	 * It is commonly suggested to use Get when nothing on the server is being modified and when a page is simply requested.
	 * Note that in NOLOH, a URL::Redirect can (and should in most cases) be used for simply requesting a page from a server.
	 */
	const Get = 'GET';
	/**
	 * A possible value for the Target, Here indicates that the action will be performed in the same frame as your NOLOH App, essentially navigating away.
	 */
	const Here = '_self';
	/**
	 * A possible value for the Target, NewWindow indicates that the action will be performed in a new browser window.
	 */
	const NewWindow = '_blank';
	/**
	 * An ArrayList of objects that are added to the Form and whose values are posted when the Form is submitted.
	 * @var ArrayList
	 */
	public $Controls;
	
	private $Action;
	private $Method;
	private $EncType;
	private $Target;
	/**
	 * Constructor.
	 * Be sure to call this from the constructor of any class that extends Form.
	 * @param string $action Typically, this is the URL of another web server to where you are submitting, but it can sometimes also be something else, e.g., a mailto: command
	 * @param Form::Get|Form::Post $method
	 * @param string $encType Specifies the content-type to be used when the Method is Form::Post; The default used is 'application/x-www-form-urlencoded'
	 * @return Form
	 */
	function Form($action, $method = Form::Post, $target = Form::Here, $encType = 'application/x-www-form-urlencoded')
	{
		parent::Component();
		//parent::Control($left, $top, $width, $height);
//		$this->Controls = new ArrayList();
		$this->Controls = new ImplicitArrayList($this, 'AddControl');
		$this->Controls->ParentId = $this->Id;
		$this->SetAction($action);
		$this->SetMethod($method);
		$this->SetTarget($target);
		$this->SetEncType($encType);
	}
	/**
	* @ignore
	*/
	function AddControl($control)
	{
		if($control->GetHTMLName() === null)
			$control->SetHTMLName($control->Id);
		return $this->Controls->Add($control, true);
	}
	/**
	 * Returns the Action of the Form. Typically, this is the URL of another web server to where you are submitting, but it can sometimes also be something else, e.g., a mailto: command
	 * @return string
	 */
	function GetAction()
	{
		return $this->Action;
	}
	/**
	 * Sets the Action of the Form. Typically, this is the URL of another web server to where you are submitting, but it can sometimes also be something else, e.g., a mailto: command
	 * @return string
	 */	
	function SetAction($action)
	{
		$this->Action = $action;
		NolohInternal::SetProperty('action', $action, $this);
	}
	/**
	 * Returns the Method used by the Form. 
	 * @return Form::Get|Form::Post
	 */
	function GetMethod()
	{
		return $this->Method;
	}
	/**
	 * Sets the Method used by the Form
	 * @param Form::Get|Form::Post $method
	 */
	function SetMethod($method)
	{
		$this->Method = $method;
		NolohInternal::SetProperty('method', $method, $this);
	}
	/**
	 * Returns the content-type to be used when the Method is Form::Post; The default used is 'application/x-www-form-urlencoded'
	 * @return string
	 */
	function GetEncType()
	{
		return $this->EncType === null && $this->Method == Form::Post ? 'application/x-www-form-urlencoded' : $this->EncType;
	}
	/**
	 * Sets the content-type to be used when the Method is Form::Post; The default used is 'application/x-www-form-urlencoded'
	 * @param string $encType
	 */
	function SetEncType($encType)
	{
		$this->EncType = $encType;
		NolohInternal::SetProperty('enctype', $encType, $this);
	}
	/**
	 * Returns where the action will open. Possible values include Form::Here, Form::NewWindow, an IFrame object, or a string.
	 */
	function GetTarget()
	{
		return $this->Target;
	}
	/**
	 * Sets where the action will open. Possible values include Form::Here, Form::NewWindow, an IFrame object, or a string.
	 * @param mixed $target
	 */
	function SetTarget($target)
	{
		if($target === null)
			$target = Form::Here;
		$this->Target = $target;
		NolohInternal::SetProperty('target', $target instanceof IFrame ? $target->Id : $target, $this);
	}
	/**
	 * Manually submits the Form. This can also be accomplished with the click on a Button of Type Button::Submit. Note that submitting
	 * a Form will navigate away from your application.
	 */
	function Submit()
	{
		QueueClientFunction($this, '_N(\''.$this->Id.'\').submit');
	}
	/**
	 * @ignore
	 */
	function GetAddId()
	{
		return $this->Id;
	}
	/**
	 * @ignore
	 */
	function GetBuoyant()
	{
		return false;
	}
	/**
	 * @ignore
	 */
	function Show()
	{
		NolohInternal::Show('FORM', parent::Show(), $this);
	}
	/**
	 * @ignore
	 */
	function SearchEngineShow()
	{
		$this->SearchEngineShowChildren();
	}
	/**
	 * @ignore
	 */
	function NoScriptShow($indent)
	{
		$str = parent::NoScriptShowIndent($indent);
		if($str !== false)
		{
			echo $indent, '<FORM action="', $this->Action, '" method="', $this->Method, "\">\n";
			$this->NoScriptShowChildren();
			echo $indent, "</FORM>\n";
		}
	}
	/**
	 * @ignore
	 */
	function Bury()
	{
		foreach($this->Controls as $control)
			$control->Bury();
		parent::Bury();
	}
	/**
	* @ignore
	*/
	function Resurrect()
	{
		foreach($this->Controls as $control)
			$control->Resurrect();
		parent::Resurrect();	
	}
}
?>