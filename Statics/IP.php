<?php

/**
 * Class IP
 *
 * The IP class contains static functions pertaining to IP Addresses.
 *
 * @package Statics
 */
final class IP
{
	/**
	 * @ignore
	 */
	private function __construct() {}
	/**
	 * Validates whether the IP is a valid address against a range of CIDRs.
	 * @param string $ip IPv4 or IPv6 address
	 * @param array $cidrs
	 * @return bool
	 * @throws Exception
	 */
	static function ValidateIpCidrRanges($ip, $cidrs)
	{
		if (empty($cidrs))
		{
			return true;
		}

		if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
		{
			$validateFunction = 'ValidateIpv4CidrRange';
		}
		elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))
		{
			$validateFunction = 'ValidateIpv6CidrRange';
		}
		else
		{
			throw new Exception('Invalid IP Address.');
		}

		foreach ($cidrs as $cidr)
		{
			if (static::$validateFunction($ip, $cidr))
			{
				return true;
			}
		}

		return false;
	}
	/**
	 * Checks if provided IP is a valid subnet within the subnet range. If the provided range is IPv6, return false.
	 * @param string $ip IPv4 Address
	 * @param string $cidr A subnet range to validate $ip against.
	 * @return bool
	 */
	static function ValidateIpv4CidrRange($ip, $cidr)
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
	static function ValidateIpv6CidrRange($ip, $cidr)
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
	/**
	 * Returns a binary representation of an IP in its packed in_addr representation.
	 * An IP can be converted to its packed in_addr form by called inet_pton(<ip>).
	 * @param string $inet
	 * @return string
	 */
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