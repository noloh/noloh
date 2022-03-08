<?php

abstract class IpLibrary extends Base
{
	static function ValidateIpWhitelisting($ip, $cidr)
	{
		$result = array();

		if (empty($cidr))
		{
			return $result;
		}

		if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
		{
			$validateFunction = 'ValidateIpv4SubnetRange';
		}
		elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))
		{
			$validateFunction = 'ValidateIpv6SubnetRange';
		}
		else
		{
			$result['code'] = 400;
			$result['error_message'] = 'Invalid IP Address detected.';
			return $result;
		}

		foreach ($cidr as $whitelisting)
		{
			if (static::$validateFunction($ip, $whitelisting))
			{
				return true;
			}
		}

		$result['code'] = 401;
		$result['error_message'] = 'Unauthorized IP.';
		return $result;
	}
	/**
	 * Checks if provided IP is a valid subnet within the subnet range. If the provided range is IPv6, return false.
	 * @param string $ip IPv4 Address
	 * @param string $cidr A subnet range to validate $ip against.
	 * @return bool
	 */
	static function ValidateIpv4SubnetRange($ip, $cidr)
	{
		list($subnet, $mask) = explode('/', $cidr);
		if (filter_var($subnet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))
		{
			return false;
		}
		$ip = ip2long($ip);
		$subnet = ip2long($subnet);
		$mask = -1 << (32 - $mask);
		$subnet &= $mask;
		return ($ip & $mask) == $subnet;
	}
	/**
	 * Checks if provided IP is a valid subnet within the subnet range. If the provided range is IPv4, return false.
	 * @param string $ip IPv6 Address
	 * @param string $cidr A subnet range to validate $ip against.
	 * @return bool
	 */
	static function ValidateIpv6SubnetRange($ip, $cidr)
	{
		list($subnet, $mask) = explode('/', $cidr);
		if (filter_var($subnet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
		{
			return false;
		}
		$binaryIp = static::InetToBits(inet_pton($ip));
		$binaryNet = static::InetToBits(inet_pton($subnet));

		$ipNetBits = substr($binaryIp, 0, $mask);
		$netBits = substr($binaryNet, 0, $mask);
		return $ipNetBits == $netBits;
	}
	static function InetToBits($inet)
	{
		$split = str_split($inet);
		$binaryIp = '';
		foreach ($split as $char)
		{
			$binaryIp .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
		}
		return $binaryIp;
	}
}