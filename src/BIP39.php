<?php
/*
 * BIP39 - Mnemonic library
 *
 * Entry point:
 *  BIP39::Generate(wordCount)
 *  BIP39::Mnemonic(string|array)
 *  BIP39::Entropy($bitstring)
 * Outputs
 * ->entropy		// Raw binary
 * ->toMnemonic()	// Dump array of mnemonic words
 * ->toSeed()		// Dump actual seed
 *
 * Uses "wordCount" to compute the rest? Presetting vars is faster, but calculating on the fly allows adjustment?
 */
declare(strict_types=1);

namespace SatyrSoftware\Cryptotool;

class BIP39
{
	public $entropy;	// Allow access from outside just for pure query
	private $wordList;	// Loaded upon object creation
	private $wordsCount;	// For Generate()

	private $language;	// just to keep track

	/* Entry points */

	# Accepts both raw binary, or hex(lowercase)
	public static function Entropy(string $entropy): self
	{
		if (preg_match('/^[a-f0-9]{2,}$/', strtolower($entropy)))		// Only Hex characters (chances of raw string being that way is 0%)
		{
			self::validateEntropyHex($entropy);
			return (new self((strlen($entropy)*3)/8))
				->setEntropy(hex2bin($entropy));
		}
		if (in_array(strlen($entropy),[16,20,24,28,32]))	// Raw binary
		{
			return (new self((strlen($entropy)*3)/4))
				->setEntropy($entropy);
		}
		throw new \Exception('Not Hex and not binary of right length');
	}

	# Straight generate - just need wordcount/length
	public static function Generate(int $wordCount = 12): self
	{
		return (new self($wordCount))->generateEntropy();
	}

	# Accepts string or array
	public static function Mnemonic($words, ?WordList $wordList = null, bool $verifyChecksum = true): self
	{
		if (is_string($words)) {
			$words = explode(" ", $words);
		}

		if (!is_array($words)) {
			throw new MnemonicException('BIP39: Mnemonic() requires an Array of words');
		}
		return (new self(count($words), ($wordList ? $wordlist :  'english')))
			->resolveMnemonic($words,$verifyChecksum);
	}

	# Actual constructor that preloads words, and mini checks
	public function __construct(int $wordCount=12,?string $language='english',?string $entropy = null)
	{
		if ($wordCount < 12 || $wordCount > 24) {
			echo "BIP39: Word count of $wordCount sent in\n";
			throw new \Exception('BIP39: Word count must be between 12-24');
		} elseif ($wordCount % 3 !== 0) {
			throw new \Exception('BIP39: Supported wordcount = 12,15,18,21,24');
		}

		$this->wordsCount=$wordCount;
		$this->entropy = $entropy;

		$this->language = trim($language);
		$this->wordList = [];

		$wordListFile = sprintf('%1$s%2$swordlists%2$s%3$s.txt', __DIR__, DIRECTORY_SEPARATOR, $this->language);
		if (!file_exists($wordListFile) || !is_readable($wordListFile))
		{
			throw new Exception( sprintf('BIP39 wordlist for "%s" not found or is not readable', ucfirst($this->language)) );
		}

		$wordList = preg_split("/(\r\n|\n|\r)/", file_get_contents($wordListFile));
		foreach ($wordList as $word)
		{
			$this->wordList[] = trim($word);
		}

		if (count($this->wordList) !== 2048)
		{
			throw new Exception('BIP39: Wordlist file must have exactly 2048 words');
		}
	}

	/* Output functions */
	public function toMnemonic():array
	{
		if (is_null($this->entropy)) { return []; }
		$fullbytes=$this->entropy . self::generateChecksum();
		$binarystring='';
		for ($i=0; $i < strlen($fullbytes); $i++)
		{
			$binarystring.=str_pad( decbin(ord($fullbytes[$i])) ,8,'0',STR_PAD_LEFT);
		}
		if (self::getChecksumBits() !== 8 )	// if 8, it miscalcs it as 0 to 0 (trim all). at 8 bytes, it's perfectly no trim
		{
			$wordsplits=str_split( substr($binarystring,0,-(8-self::getChecksumBits())), 11);	// Trim off excess binary
		}
		else
		{
			$wordsplits=str_split( $binarystring, 11);	// Trim off excess binary
		}
		$wordlist=[];
		foreach ($wordsplits as $wordsplit)
		{
			$wordlist[]=$this->wordList[bindec($wordsplit)];
		}
		return $wordlist;
	}

	public function toSeed(?string $passphrase):string
	{
		return hash_pbkdf2('sha512',implode(' ',self::toMnemonic()),'mnemonic'.$passphrase,2048,64,true);
	}

	/* Internal functions */
	private function generateEntropy(): self
	{
		$this->entropy=(random_bytes(($this->wordsCount*4)/3));
		return $this;
	}

	# Validation of entropy : has to be hex, and of a particular length in bits
	private static function validateEntropyHex(string $entropy): void
	{
		if (!preg_match('/^[a-f0-9]{2,}$/', $entropy)) {
			throw new \Exception('Invalid entropy (requires hexadecimal)');
		}
		if (!in_array(strlen($entropy), [32, 40, 48, 56, 64])) {
			throw new \Exception('Invalid entropy length');
		}
	}

	private function setEntropy($entropy=null):self
	{
		$this->entropy=$entropy;
		return $this;
	}

	private function resolveMnemonic(array $wordList,?bool $verifyChecksum):self
	{
		$binarystring='';
		foreach ($wordList as $word)
		{
			$i=0;
			$word=mb_strtolower($word);
			$index=array_search($word,$this->wordList);
			if ($index === false) { throw new \Exception('BIP39: Invalid Mnemonic words found'); }
			$binarystring.= str_pad(decbin($index), 11, '0', STR_PAD_LEFT);
		}
		for ($i=0; $i< (count($wordList)*11)/8; $i++)	//  [12,15,18,21,24] words = [32,40,48,56,64] bytes
		{
			$this->entropy .= chr( bindec(substr($binarystring,($i*8),8)) );
		}
		$cksum=substr($this->entropy,-1);		// Take the last byte off for verification
		$this->entropy=substr($this->entropy,0,-1);	// Trim the last checksum bit off

		if (self::getChecksumBits()<>8)			// Pad the right with 0's for comparison
		{
			$cksum=chr( ord($cksum) << self::getChecksumBits() );
		}
		if ( ($verifyChecksum === true) && ($cksum !== self::generateChecksum()))
		{
			throw new \Exception('BIP39: Checksum failed for Mnemonic');
		}
		return $this;
	}

	private function getChecksumBits() { return strlen($this->entropy)/4; }

	private function generateChecksum():string
	{
		$checksumChar = ord(hash("sha256",$this->entropy,true)[0]);
		return chr( ($checksumChar>>(8-self::getChecksumBits())) << (8-self::getChecksumBits())  );	// This is appended to the end
	}
}
?>
