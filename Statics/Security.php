<?php

final class Security
{
	const Cipher = 'aes-256-cbc';
	
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
	static function Encrypt($data, $encryptionKey, $iv)
	{
		$encryptionKey = base64_decode($encryptionKey);
		$iv = base64_decode($iv);
		
		// Encrypt $data using aes-256-cbc cipher with the given encryption key and 
		// our initialization vector. The 0 gives us the default options, but can
		// be changed to OPENSSL_RAW_DATA or OPENSSL_ZERO_PADDING
		$encrypted = openssl_encrypt($data, static::Cipher, $encryptionKey, 0, $iv);
		return $encrypted;
	}
	static function Decrypt($data, $encryptionKey, $iv)
	{
		$encryptionKey = base64_decode($encryptionKey);
		$iv = base64_decode($iv);
		
		$decrypted = openssl_decrypt($data, static::Cipher, $encryptionKey, 0, $iv);
		return $decrypted;
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
}