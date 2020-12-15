<?php
/**
 * @author		Sunil G R
 *
 * Attempts to be as secure as possible given:
 *
 * - Key can be any string
 * - No knowledge of encryption is required
 * - Only key and raw/encrypted string is needed at each end
 * - Metadata can be anything (string, array, etc.)
 *
 */

/**
 * Encrypts a string
 *
 * @param string $key  Encryption key, also required for decryption
 * @param string $raw  Raw string to be encrypted
 * @param mixed  $meta Associated data that must be provided during decryption
 *
 * @return string Raw data encrypted with key
 */
function decrypt( $key, $ciphertext, $meta = '' ) {
	// Generate valid key
	$key = hash_pbkdf2( 'sha256', $key, '', 10000, 0, true );

	// Serialize metadata
	$meta = serialize($meta);

	// Derive two subkeys from the original key
	$mac_key = hash_hmac( 'sha256', 'mac', $key, true );
	$enc_key = hash_hmac( 'sha256', 'enc', $key, true );
	$enc_key = substr( $enc_key, 0, 32 );

	// Unpack MAC, nonce and encrypted message from the ciphertext
	$enc = base64_decode( $ciphertext );
	$siv = substr( $enc, 0, 16 );
	$nonce = substr( $enc, 16, 16 );
	$enc = substr( $enc, 16 + 16 );

	// Decrypt message
	$plaintext = mcrypt_decrypt( 'rijndael-128', $enc_key, $enc, 'ctr', $siv );

	// Verify MAC, return null if message is invalid
	$temp = $nonce;
	$temp .= hash_hmac( 'sha256', $plaintext, $mac_key, true );
	$temp .= hash_hmac( 'sha256', $meta, $mac_key, true );
	$mac = hash_hmac( 'sha256', $temp, $mac_key, true );
	if ( $siv !== substr( $mac, 0, 16 ) ) return null;

	return $plaintext;

}
?>
