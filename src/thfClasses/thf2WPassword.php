<?php
	class thf2WPassword {
		private static $key = '9|j3@n^m%F!8-s65/2a$0)ldX1&w';
		
		/**
		 * Returns an encrypted string, base64 encoded
		 * @param $string
		 * @return encoded string
		 */
		static function encrypt($string,$keyk='') {
			$ivSize = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
			$iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);
			$encryptedString = mcrypt_encrypt(MCRYPT_BLOWFISH, ($keyk ? $keyk: self::$key), trim($string), MCRYPT_MODE_ECB, $iv);
			return base64_encode($encryptedString);
		}
		
		/**
		 * Returns the original string
		 * @param $string
		 * @return decoded string
		 */
		static function decrypt($string,$keyk='') {
			$ivSize = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
			$iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);
			$decryptedString = mcrypt_decrypt(MCRYPT_BLOWFISH, ($keyk ? $keyk: self::$key), base64_decode($string), MCRYPT_MODE_ECB, $iv);
			return str_replace("\0", "", $decryptedString); // replace null characters
		}
	}