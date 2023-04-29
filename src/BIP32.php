<?php
/*
 * BIP32 - Extended Keys
 *
 * Entry point:
 *  new BIP32(seed,coin?)
 * Outputs
 * ->master_public_key	// raw public key
 * ->toPrivate()	// raw private key
 * ->toChainCode()	// raw chain_code
 *
 * Uses "wordCount" to compute the rest? Presetting vars is faster, but calculating on the fly allows adjustment?
 */
declare(strict_types=1);

namespace SatyrSoftware\Cryptotool;
use Elliptic\EC;

class BIP32
{
	private $seed;
	private $master_private_key;
	private $master_chain_code;

	protected $master_public_key;

	private $supported_coins=array('bitcoin');

	/* Entry points */

	# hex or binary - 64 bytes is assumed binary, otherwise 128
	public function __construct(string $seed,string $coin='bitcoin')
	{
		if (strlen($seed) == 64)
		{
			$this->seed=$seed;
		} elseif (strlen($seed)==128) {
			$this->seed=hex2bin($seed);
		} else {
			throw new \Exception('BIP32: Seed is expecting 64 byte (binary) or 128 byte (hex) output from BIP39. Given='.strlen($seed));
		}
# Auto assume Bitcoin for now		
		$master_key=hash_hmac('sha512',$this->seed,'Bitcoin seed',true);
		$this->master_private_key=substr($master_key,0,32);
		$this->master_chain_code=substr($master_key,32);

		$priv=(new EC('secp256k1'))->keyFromPrivate(bin2hex($this->master_private_key));
		$this->master_public_key=hex2bin($priv->getPublic(true,"hex"));
	}

	/* Output functions */
	public function toSeed() { return $this->seed; }
	public function toMasterPrivateKey() { return $this->master_private_key; }
	public function toMasterPublicKey() { return $this->master_public_key; }
	public function toMasterChainCode() { return $this->master_chain_code; }

	public function toPublic()
	{
		return $this->master_public_key;
	}
	/* Internal functions */

	public function serialize(bool $private_key)
	{
		if ($private_key==true)
		{
			$string='0488ade4';			// Sig: 'xprv'
			$string.='00';				// Depth: m'/0'
			$string.=substr('12345678',0,8);	// Parent Fingerprint : 4 bytes of hash160
			$string.='00000000';			// Child Number : ?
			$string.='00000000000000000000000000000000';			// Chain Code: extra 32 byte secret?
			$string.='00000000000000000000000000000000';			// Chain Code: extra 32 byte secret?
#			return base58encode($string);
		}
	}
}
?>
