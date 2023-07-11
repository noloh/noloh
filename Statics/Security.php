<?php

final class Security
{
	const Cipher = 'aes-256-cbc';

	/**
	 * @ignore
	 */
	private function __construct() {}
	/**
	 * @param $data
	 * The data to be encrypted
	 * @param $encryptionKey
	 * Generate a 256-bit encryption key
	 * This should be stored somewhere instead of recreating it each time
	 * $encryptionKey = base64_encode(openssl_random_pseudo_bytes(32));
	 * @param $iv
	 * Generate an initialization vector
	 * This *MUST* be available for decryption as well
	 * $iv = base64_encode(openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher)));
	 * @return string
	 */
	static function Encrypt($data, $encryptionKey, $iv = null)
	{
		if ($data == null)
		{
			return '';
		}

		$generatedIV = ($iv === null);

		if ($generatedIV)
		{
			$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(static::Cipher));
		}

		// Encrypt $data using aes-256-cbc cipher with the given encryption key and 
		// our initialization vector. The 0 gives us the default options, but can
		// be changed to OPENSSL_RAW_DATA or OPENSSL_ZERO_PADDING
		$encrypted = openssl_encrypt($data, static::Cipher, base64_decode($encryptionKey), 0, base64_decode($iv));

		if ($generatedIV)
		{
			$encrypted = str_replace('=', '', base64_encode($iv)) . base64_encode($encrypted);
		}

		return $encrypted;
	}
	static function Decrypt($data, $encryptionKey, $iv = null)
	{
		if ($data == null)
		{
			return '';
		}

		if ($iv === null)
		{
			$ivLen = openssl_cipher_iv_length(static::Cipher);

			// Total length of base64 encoded string given $ivLen bytes
			$ivStrLength = ((4 * $ivLen / 3) + 3) & ~3;

			// Length of '=' padding
			$padding = $ivStrLength - ceil((4 * $ivLen / 3));

			$iv = substr($data, 0, $ivStrLength - $padding);
			$iv .= str_repeat('=', $padding);
			$iv = base64_decode($iv);

			$cipher = substr($data, $ivStrLength - $padding);
			$data = base64_decode($cipher);
		}

		$iv = base64_decode($iv);
		$encryptionKey = base64_decode($encryptionKey);

		$decrypted = openssl_decrypt($data, static::Cipher, $encryptionKey, 0, $iv);

		if ($decrypted)
		{
			return $decrypted;
		}
		else
		{
			throw new Exception('Invalid decryption result. Verify your key is correct.');
		}
	}
	/**
	 * Generates an initilization vector
	 *
	 * @return string
	 */
	static function GenerateIV()
	{
		$iv = base64_encode(openssl_random_pseudo_bytes(openssl_cipher_iv_length(Security::Cipher)));

		return $iv;
	}
	/**
	 * Hash the password
	 *
	 * @param string $password The password to hash
	 * @param integer $cost  The hash computation cost
	 *
	 * @return string|false The hashed password, or false on error.
	 */
	static function Hash($password, $cost = 10)
	{
		if (!function_exists('crypt'))
		{
			BloodyMurder("Password::Hash(): Crypt must be loaded to function");
			return null;
		}
		if (is_null($password) || is_int($password))
		{
			$password = (string) $password;
		}
		if (!is_string($password))
		{
			BloodyMurder("Password::Hash(): Password must be a string");
			return null;
		}

		if ($cost < 4 || $cost > 31)
		{
			BloodyMurder(sprintf("Password::Hash(): Invalid bcrypt cost parameter specified: %d", $cost));
			return null;
		}

		// The length of salt to generate
		$rawSaltLength = 16;
		// The length required in the final serialization
		$requiredSaltLength = 22;
		// Salt revision and hashing cost
		$hashFormat = sprintf("$2y$%02d$", $cost);
		// The expected length of the final crypt() output
		$resultLength = 60;

		$buffer = '';
		$bufferValid = false;
		if (function_exists('mcrypt_create_iv') && !defined('PHALANGER'))
		{
			$buffer = mcrypt_create_iv($rawSaltLength, MCRYPT_DEV_URANDOM);
			if ($buffer)
			{
				$bufferValid = true;
			}
		}
		if (!$bufferValid && function_exists('openssl_random_pseudo_bytes'))
		{
			$strong = false;
			$buffer = openssl_random_pseudo_bytes($rawSaltLength, $strong);
			if ($buffer && $strong)
			{
				$bufferValid = true;
			}
		}
		if (!$bufferValid && @is_readable('/dev/urandom'))
		{
			$file = fopen('/dev/urandom', 'r');
			$read = 0;
			$localBuffer = '';
			while ($read < $rawSaltLength)
			{
				$localBuffer .= fread($file, $rawSaltLength - $read);
				$read = self::StrLen($localBuffer);
			}
			fclose($file);
			if ($read >= $rawSaltLength)
			{
				$bufferValid = true;
			}
			$buffer = str_pad($buffer, $rawSaltLength, "\0") ^ str_pad($localBuffer, $rawSaltLength, "\0");
		}
		if (!$bufferValid || self::StrLen($buffer) < $rawSaltLength)
		{
			$bufferLength = self::StrLen($buffer);
			for ($i = 0; $i < $rawSaltLength; $i++)
			{
				if ($i < $bufferLength)
				{
					$buffer[$i] = $buffer[$i] ^ chr(mt_rand(0, 255));
				}
				else
				{
					$buffer .= chr(mt_rand(0, 255));
				}
			}
		}
		$salt = $buffer;

		// encode string with the Base64 variant used by crypt
		$base64Digits = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
		$bcrypt64Digits = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

		$base64String = base64_encode($salt);
		$salt = strtr(rtrim($base64String, '='), $base64Digits, $bcrypt64Digits);
		$salt = self::SubStr($salt, 0, $requiredSaltLength);

		$hash = $hashFormat . $salt;

		$ret = crypt($password, $hash);

		if (!is_string($ret) || self::StrLen($ret) != $resultLength)
		{
			return false;
		}

		return $ret;
	}
	/**
	 * Verify a password against a hash using a timing attack resistant approach
	 *
	 * @param string $password The password to verify
	 * @param string $hash     The hash to verify against
	 *
	 * @return boolean If the password matches the hash
	 */
	static function VerifyHash($password, $hash)
	{
		if (!function_exists('crypt'))
		{
			BloodyMurder("Password::Verify(): Crypt must be loaded for function");
			return false;
		}
		$ret = crypt($password, $hash);
		if (!is_string($ret) || self::StrLen($ret) != self::StrLen($hash) || self::StrLen($ret) <= 13)
		{
			return false;
		}

		$status = 0;
		for ($i = 0; $i < self::StrLen($ret); $i++)
		{
			$status |= (ord($ret[$i]) ^ ord($hash[$i]));
		}

		return $status === 0;
	}
	/**
	 * Get information about the password hash. Returns an array of the information
	 * that was used to generate the password hash.
	 *
	 * @param string $hash The password hash to extract info from
	 *
	 * @return array The array of information about the hash.
	 */
	static function HashInfo($hash)
	{
		$data = array (
			'algorithm_name' => 'unknown',
			'cost' 		 	 => 0
		);

		if (self::SubStr($hash, 0, 4) === '$2y$' && self::StrLen($hash) === 60)
		{
			$data['algorithm_name'] = 'bcrypt';

			list($cost) = sscanf($hash, "$2y$%d$");

			$data['cost'] = (int)$cost;
		}
		return $data;
	}
	/**
	 * Determine if the password hash needs to be rehashed according to the options provided
	 *
	 * If the answer is true, after validating the password using password_verify, rehash it.
	 *
	 * @param string $hash    The hash to test
	 * @param int    $cost    The algorithm used for new password hashes
	 *
	 * @return boolean True if the password needs to be rehashed.
	 */
	static function NeedsRehash($hash, $cost)
	{
		$info = self::HashInfo($hash);
		if ((int)$cost !== $info['cost'])
		{
			return true;
		}
		return false;
	}
	/**
	 * @ignore
	 */
	private static function StrLen($binaryString)
	{
		if (function_exists('mb_strlen'))
		{
			return mb_strlen($binaryString, '8bit');
		}
		return strlen($binaryString);
	}
	/**
	 * @ignore
	 */
	private static function SubStr($binaryString, $start, $length)
	{
		if (function_exists('mb_substr'))
		{
			return mb_substr($binaryString, $start, $length, '8bit');
		}
		return substr($binaryString, $start, $length);
	}
	/**
	 * Gets encryption key from reading a file at the specified path.
	 * Does not retain read value in memory itself.
	 *
	 * @return string
	 */
	static public function GetEncryptionKeyFromPath($encryptionKeyPath = null)
	{
		if (is_null($encryptionKeyPath))
		{
			$encryptionKeyPath = Configuration::$DefaultEncryptionKeyPath;
		}
		$encryptionKey = file_get_contents($encryptionKeyPath);
		if (empty($encryptionKey))
		{
			BloodyMurder('No encryption key found at the specified path');
		}
		return $encryptionKey;
	}
}