<?php
/*
 * BIP32 - Extended Keys
 *
 * Entry point:
 *  BIP32::Generate(wordCount)
 *  BIP32::Mnemonic(string|array)
 *  BIP32::Entropy($bitstring)
 * Outputs
 * ->entropy		// Raw binary
 * ->toMnemonic()	// Dump array of mnemonic words
 * ->toSeed()		// Dump actual seed
 *
 * Uses "wordCount" to compute the rest? Presetting vars is faster, but calculating on the fly allows adjustment?
 */
declare(strict_types=1);

namespace SatyrSoftware\Cryptotool;

class BIP32
{
	public $seed;
	public $master_private_key;
	public $master_chain_code;

	/* Entry points */

	# hex or binary - 64 bytes is assumed binary, otherwise 128
	public function __construct(string $seed)
	{
		if (strlen($seed) == 64)
		{
			$this->seed=$seed;
		} elseif (strlen($seed)==128) {
			$this->seed=hex2bin($seed);
		} else {
			throw new \Exception('BIP32: Seed is expecting 64 byte (binary) or 128 byte (hex) output from BIP39. Given='.strlen($seed));
		}
		$master_key=hash_hmac('sha512',$this->seed,'Bitcoin seed',true);
		$this->master_private_key=substr($master_key,0,32);
		$this->master_chain_code=substr($master_key,32);
	}

	/* Output functions */
	/* Internal functions */

}
?>
