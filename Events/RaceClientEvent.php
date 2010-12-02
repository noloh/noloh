<?php
/**
 * RaceClientEvent class
 *
 * A RaceClientEvent is a kind of Event that is executed on the client after a Race condition is met without communicating with the server.<br>
 * A RaceClientEvent is more responsive than a {@see ServerEvent}, and are similar to a ClientEvent. RaceClientEvents are most useful when 
 * implementing a 3rd party JavaScript library and you need to ensure that the library is loaded before executing its functions.
 *
 * <pre>
 * 	// Instantiates a new Button
 *  $btn = new Button("Click Me");
 * 	// Sets the click of the button to an event which check a race condition and trigger when the condition is true.
 * 	$btn->Click = new RaceClientEvent('1 == 1', 'alert', 'I have been clicked');
 * 	// When the button is clicked the condition 1 == 1 will be checked, when this is true the alert will trigger. In this case 1 == 1 is instantly true.
 * </pre>
 * The race condition can be a JavaScript object, a condition, a JavaScript function, or a ClientEvent.
 * <pre>
 * $btn->Click = new RaceClientEvent('CKEditor', 'alert', 'I have been clicked CKEditor is defined');
 * $btn->Click = new RaceClientEvent('CKEditor.SubObject', 'alert', 'I have been clicked and SubObject is defined');
 * $btn->Click = new RaceClientEvent('function(){return someObj.someFunc()}', 'alert', 'I have been clicked and the result of someFunc() is true');
 * $btn->Click = new RaceClientEvent(new ClientEvent('function(){return someObj.someFunc()}'), 'alert', 'I have been clicked and the result of someFunc() is true);
 * </pre>
 * Similarly, RaceClientEvent's other parameters are identical to ClientEvent. This means that you can also pass in statements instead of just function calls.
 * $btn->Click = new RaceClientEvent('CKEditor', 'alert("I have been clicked CKEditor is defined");');
 * </pre>
  For more information, please see
 * @link /Tutorials/Events.html#ClientEvents
 *
 * @package Events
 */
class RaceClientEvent extends ClientEvent
{
	/**
	 * Constructor.
	 * @param mixed $condition A JavaScript object, a statement, a JavaScript function or a ClientEvent
	 * @param string $allCodeAsString Either the full JavaScript code to be executed or the name of a JavaScript function as a string
	 * @param mixed,... $params the optional params to be passed to your JavaScript function
	 */
	function RaceClientEvent($condition, $allCodeAsString, $params=null)
	{
/*		if($allCodeAsString !== '' && !preg_match('/(?:;|})\s*?\z/', $allCodeAsString))
		{
			$allCodeAsString = trim($allCodeAsString) . '(';
			$params = func_get_args();
			$count = count($params);
			for($i=1;$i<$count;++$i)
				$allCodeAsString .= self::ClientFormat($params[$i]) .',';
			$allCodeAsString = rtrim($allCodeAsString, ',') . ');';
		}
		else
			$allCodeAsString = str_replace("\n", ' ', $allCodeAsString);*/
		$args = func_get_args();
		call_user_func_array(array('ClientEvent', 'ClientEvent'), array_splice($args, 1));
		//parent::ClientEvent($allCodeAsString, $params);
		if(!$condition instanceof ClientEvent)
		{	
			if(!preg_match('/^\s*?function\s*\(.*\)?\s*?\{.*\}\s*?$/si', $condition))
			{
				if(preg_match('/^[a-z$_][\w$.]*$/i', $condition))
//					$condition = "function(){return typeof($condition) != 'undefined';}";
//					$condition = "function(){var result; try{result = (typeof($condition) != 'undefined')}catch(e){result = false}return result;}";
				{
					$namespaces = explode('.', $condition);
					$count = count($namespaces);
					if($count > 1)
					{
						$condition = 'function(){return';
						$accumNamespace = $namespaces[0];
						for($i=0; $i < $count; ++$i)
						{
							$condition .= ' typeof(' .$accumNamespace . ') != \'undefined\' &&';
							if(isset($namespaces[$i + 1]))
								$accumNamespace .= '.' . $namespaces[$i + 1];
						}
						$condition = rtrim($condition, '&') . ';}';
					}
					else
						$condition = "function(){return typeof($condition) != 'undefined';}";
						
				}
				else
					$condition = "function(){return $condition;}";
			}
		}
		else
		{
			if (preg_match('/^\s*?function\s*\(.*\)?\s*?\{.*\}\s*?$/si', ($func = $param->ExecuteFunction)))
				$condition =  $func;
			else	
				$condition = 'function(){' . $param->GetEventString(null, null) .'}';
		}
		ClientScript::AddNOLOHSource('RaceCall.js');
		$this->ExecuteFunction = '_NChkCond(' . $condition . ',' . 'function(){' . $this->ExecuteFunction . '});';	
	}
}
?>