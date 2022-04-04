<?php
abstract class OAuthConnection extends Base
{
	const OAUTH_AUTHORIZE_URL = null;
	const OAUTH_TOKEN_URL = null;
	const RESOURCE_ENDPOINT_BASE = null;
	const RESOURCE_API_ENDPOINT_PATH = null;
	const AverageRequestTime = null;
	const APP_NAME = null;
	const OUTBOUND_REDIRECT_HANDLER = null;
	const INBOUND_REDIRECT_HANDLER = null;
	const STATE_COOKIE_NAME = 'state_cookie';

	static $AuthParams = [];
	static $TokenParams = [];

	protected $LastRequestTime;
	protected $AccessToken;
	protected $AuthTime;
	protected $DisplayErrorCallback = null;

	function __construct()
	{

	}
	function GetClient($contentType = 'application/json', $additionalHeaders = [])
	{
		$headers = [
			'Content-Type' => $contentType
		];

		$headers = array_merge($headers, $additionalHeaders);

		return new GuzzleHttp\Client(
			[
				'defaults' => ['headers' => $headers]
			]
		);
	}
	static function Authorize()
	{
		$defaultAuthParams = [
			'redirect_uri' => static::INBOUND_REDIRECT_HANDLER,
			'state' => static::GenerateStateParam()
		];
		$authParams = array_merge(static::$AuthParams, $defaultAuthParams);

		$url = static::OAUTH_AUTHORIZE_URL;

		$delimiter = '?';
		foreach ($authParams as $key => $param)
		{
			$url .= $delimiter . $key . '=' . $param;
			$delimiter = '&';
		}

		$redirectURL = urlencode(base64_encode($url));
		$originURL = urlencode(base64_encode(URL::GetPath(false)));

		URL::Redirect(static::OUTBOUND_REDIRECT_HANDLER . '?redirect_url=' . $redirectURL . '&origin_url=' . $originURL . '&appname=' . static::APP_NAME);
	}
	function FetchAccessToken($contentType = 'application/x-www-form-urlencoded', $additionalParams = [], $additionalHeaders = [])
	{
		$payload = array_merge(static::$TokenParams, $additionalParams);
		$body = '';

		foreach ($payload as $key => $param)
		{
			$body .= '&' . $key . '=' . $param;
		}

		$client = static::GetClient($contentType, $additionalHeaders);

		try
		{
			$response = $client->post(
				static::OAUTH_TOKEN_URL,
				[
					'body' => $body
				]
			);
		}
		catch (Exception $e)
		{
			static::CatchException($e, true);
			return false;
		}

		$response = json_decode($response->getBody(), true);
		if (empty($response['access_token']))
		{
			return false;
		}

		$this->AuthTime = Library::GetCurrentTime();
		$this->AccessToken = $response['access_token'];
		return true;
	}
	function PostAuthentication($params, $headers = [])
	{
		$this->FetchAccessToken('application/x-www-form-urlencoded', $params, $headers);
	}
	function ValidateRequestRate()
	{
		/* We need to maintain a request rate of 1 request per second */
		if ($this->LastRequestTime != null && (round(microtime(true) - $this->LastRequestTime, 2) < static::AverageRequestTime))
		{
			usleep((static::AverageRequestTime - round(microtime(true) - $this->LastRequestTime, 2)) * 1000000);
		}
		$this->LastRequestTime = microtime(true);
	}
	function Post($contentType, $resource, $body, $additionalHeaders = [], $displayErrors = false)
	{
		$json = json_encode($body);
		$headers['Content-Length'] = strlen($json);
		$headers = array_merge($headers, $additionalHeaders);
		$client = static::GetClient($contentType, $headers);
		$url = static::GetResourceURL($resource);

		$this->ValidateRequestRate();

		try
		{
			$response = $client->post(
				$url,
				[
					'body' => $json
				]
			);
		}
		catch (Exception $e)
		{
			static::CatchException($e, $displayErrors);
			return false;
		}

		return json_decode($response->getBody(), true);
	}
	function Put($contentType, $resource, $body, $additionalHeaders = [], $displayErrors = false)
	{
		$json = json_encode($body);
		$headers['Content-Length'] = strlen($json);
		$headers = array_merge($headers, $additionalHeaders);
		$client = static::GetClient($contentType, $headers);
		$url = static::GetResourceURL($resource);

		$this->ValidateRequestRate();

		try
		{
			$response = $client->put(
				$url,
				[
					'body' => $json
				]
			);
		}
		catch (Exception $e)
		{
			static::CatchException($e, $displayErrors);
			return false;
		}

		return json_decode($response->getBody(), true);
	}
	function Get($contentType, $resource, $params = [], $headers = [], $displayErrors = false, $rawURL = false)
	{
		$client = $this->GetClient($contentType, $headers);
		$url = $rawURL ? $resource : static::GetResourceURL($resource);

		$delimiter = '?';
		foreach ($params as $key => $param)
		{
			$url .= $delimiter . $key . '=' . $param;
			$delimiter = '&';
		}

		$this->ValidateRequestRate();

		try
		{
			$response = $client->get(
				$url
			);
		}
		catch (Exception $e)
		{
			static::CatchException($e, $displayErrors);
			return false;
		}

		$response = json_decode($response->getBody(), true);

		return $response;
	}
	static function FetchExceptionMessage($body)
	{
		$message = '';
		if (isset($body->Message))
		{
			$message .= $body->Message;
		}
		
		return $message;
	}
	function CatchException($e, $displayErrors)
	{
		$message = '';

		if (
			method_exists($e, 'GetResponse')
			&& method_exists($e->GetResponse(), 'GetBody')
		)
		{
			$body = json_decode($e->GetResponse()->GetBody());
			$message = static::FetchExceptionMessage($body);
		}

		$message = $message ?: $e->getMessage();

		if ($displayErrors && $this->DisplayErrorCallback)
		{
			call_user_func($this->DisplayErrorCallback, "{$e->getCode()} : {$message}");
		}
		else
		{
			throw new Exception($message);
		}
	}
	function GetResourceURL($resource)
	{
		return static::RESOURCE_ENDPOINT_BASE . '/' . static::RESOURCE_API_ENDPOINT_PATH . '/' . $resource;
	}
	static function GenerateStateParam()
	{
		$state = uniqid('', true);
		setcookie(static::STATE_COOKIE_NAME, $state, strtotime('1 hour'));

		return $state;
	}
	function VerifyState($state)
	{
		if (isset($_COOKIE[static::STATE_COOKIE_NAME]) && $_COOKIE[static::STATE_COOKIE_NAME] === $state)
		{
			setcookie(static::STATE_COOKIE_NAME, '', 1);
			return true;
		}

		return false;
	}
	function GetAuthTime()
	{
		return $this->AuthTime;
	}
}