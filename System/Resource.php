<?php

abstract class Resource extends Object
{
	function Resource() {}
	
	/* TODO: This doesn't allow parent::METHOD call.
		A better way would be to somehow check if method is inherited. */
	/* TODO: Find class methods, and send the Allow header.
		Except it's not strictly class methods, because it could inherit a middle class. */
	
	function Post($data)
	{
		return self::MethodNotAllowed();
	}
	
	function Get($params)
	{
		return self::MethodNotAllowed();
	}
	
	function Put($data)
	{
		return self::MethodNotAllowed();
	}
	
	function Delete()
	{
		return self::MethodNotAllowed();
	}
	
	/* TODO: We should prevent 2 Responds. This should probably just queue a response,
		and Router, at the end, should issue that response, or respond with a blank or perhaps an error code. */
	function Respond($data)
	{
		if (is_object($data))
		{
			if (method_exists($data, 'ToArray'))
			{
				// Custom things, namely, Models.
				$data = $data->ToArray();
			}
			elseif ($data instanceof DataCommand)
			{
				$data = $data->Execute()->Data;
			}
			elseif ($data instanceof DataReader)
			{
				$data = $data->Data;
			}
			else
			{
				$data = get_object_vars($data);
			}
		}
		// TODO: End buffering
		// TODO: gzip
		// TODO: Possibly send cache headers on GET requests
		echo json_encode($data, JSON_FORCE_OBJECT);
	}


	// Errors

	public static function BadRequest($text = '')
	{
		header('HTTP/1.1 400 Bad Request');
		die($text);
	}

	public static function Unauthorized()
	{
		header('HTTP/1.1 401 Unauthorized');
		die();
	}

	public static function NotFound()
	{
		header('HTTP/1.1 404 Not Found');
		die();
	}

	public static function MethodNotAllowed($allowedList = array())
	{
		header('HTTP/1.1 405 Method Not Allowed');
		if (!empty($allowedList))
		{
			header('Allow: ' . implode(', ', $allowedList));
		}
		die();
	}
}