<?php
/*
 * Mnemonic  - Mnemonic library
 *
 * Entry point:
 *  Mnemonic::Generate(bits)
 *  Mnemonic::Mnemonic(string|array,[dictionary:string])
 *  Mnemonic::Entropy($bitstring)
 * Parameter-changes:
 *  loadLanguage(string)
 *   Changes the dictionary/wordlist
 * Outputs
 * ->entropy		// Raw binary
 * ->toMnemonic()	// Dump array of mnemonic words
 * ->toSeed()		// Dump actual seed
 *
 * Uses "wordCount" to compute the rest? Presetting vars is faster, but calculating on the fly allows adjustment?
 */
declare(strict_types=1);

namespace SatyrSoftware\Cryptotool;

class Mnemonic
{
	public $entropy;	// Allow access from outside just for pure query

	protected $wordList;	// Loaded upon object creation
	private int $wordLength;	// in bits

	private $bitCount;	// for seed

	protected $dictionary;	// just to keep track

	/* Entry points */

	# Accepts both raw binary, or hex(lowercase)
	public static function Entropy(string $entropy): self
	{
		if (preg_match('/^[a-f0-9]{2,}$/', strtolower($entropy)))		// Only Hex characters (chances of raw string being that way is 0%)
		{
			self::validateEntropyHex($entropy);
			return (new self((strlen($entropy)/2)*8))
				->setEntropy(hex2bin($entropy));
		}
		return (new self((strlen($entropy)*8)))
			->setEntropy($entropy);
	}

	# Straight generate - just need wordcount/length
	public static function Generate(int $bitCount = 96): self
	{
		return (new self($bitCount))->generateEntropy($bitCount);
	}

	# Accepts string or array
	public static function Mnemonic($words, string $dictionary = 'english'): self
	{
		if (is_string($words)) {
			$words = explode(" ", $words);
		}

		if (!is_array($words)) {
			throw new MnemonicException('BIP39: Mnemonic() requires an Array of words');
		}
		return (new self(0, $dictionary))		// Bitcount 0 = unknown. Filled by resolve
			->resolveMnemonic($words);
	}

	# Actual constructor that preloads words, and mini checks
	public function __construct(int $bitCount=128,?string $dictionary='english',?string $entropy = null)
	{
		if ($bitCount % 8 !== 0)
		{
			throw new \Exception('Mnemonic: bitCount % 8 !== 0');
		}

		$this->bitCount=$bitCount;
		$this->entropy = $entropy;

		$this->dictionary = trim($dictionary);
		$this->wordList = [];

		$this->loadDictionary($this->dictionary);
	}

	/* Dictionary Loading */

	private function loadDictionary($dictionary='english')
	{
		$this->dictionary=$dictionary;
		$this->wordList=[];
		$wordListFile = sprintf('%1$s%2$swordlists%2$s%3$s.txt', __DIR__, DIRECTORY_SEPARATOR, $this->dictionary);
		if (!file_exists($wordListFile) || !is_readable($wordListFile))
		{
			throw new \Exception( sprintf('Mnemonic wordlist for "%s" not found or is not readable', ucfirst($this->dictionary)) );
		}

		$wordList = preg_split("/(\r\n|\n|\r)/", file_get_contents($wordListFile));
		foreach ($wordList as $word)
		{
			if ($word=='') { continue; }
			$this->wordList[] = trim($word);
		}

		$this->wordLength=(int)(log(count($this->wordList)) / log(2));	// Calculate wordLen from number of entries in the wordlist
		$valtest=log(count($this->wordList)) / log(2);	// Calculate wordLen from number of entries in the wordlist
		if (floor($valtest)!==$valtest)
		{
			throw new \Exception('Mnemonic: Wordlist file must have exactly '.pow(2,$this->wordLength).' words '. count($this->wordList)." found");
		}
	}

	/* Output functions */
	public function toMnemonic():array
	{
		if (is_null($this->entropy)) { return []; }
		$this->loadDictionary($this->dictionary);
		$fullbytes=$this->entropy;
		$binarystring='';
		for ($i=0; $i < strlen($fullbytes); $i++)
		{
			$binarystring.=str_pad( decbin(ord($fullbytes[$i])) ,8,'0',STR_PAD_LEFT);
		}
		$wordsplits=str_split( $binarystring, $this->wordLength);	// Trim off excess binary

		$wordlist=[];
		foreach ($wordsplits as $wordsplit)
		{
			$wordlist[]=$this->wordList[bindec($wordsplit)];
		}
		return $wordlist;
	}

	/* Upper classes */
	/**
	 * toSeed()
	 **/
	/* Internal functions */
	protected function generateEntropy(int $bitCount):self
	{
		$this->entropy=(random_bytes($bitCount/8));
		return $this;
	}

	# Validation of entropy : has to be hex. Length is done on upper level
	protected static function validateEntropyHex(string $entropy): void
	{
		if (!preg_match('/^[a-f0-9]{2,}$/', $entropy)) {
			throw new \Exception('Invalid entropy (requires hexadecimal)');
		}
	}
	# Basic setEntropy()
	protected function setEntropy($entropy=null):self
	{
		$this->entropy=$entropy;
		return $this;
	}

	private function resolveMnemonic(array $wordList):self
	{
		$this->loadDictionary($this->dictionary);
		$binarystring='';
		foreach ($wordList as $word)
		{
			$i=0;
			$word=mb_strtolower($word);
			$index=array_search($word,$this->wordList);
			if ($index === false) { throw new \Exception('Mnemonic: Invalid Mnemonic words found'); }
			$binarystring.= str_pad(decbin($index), $this->wordLength, '0', STR_PAD_LEFT);
		}
		for ($i=0; $i< (count($wordList)*$this->wordLength)/8; $i++)	//  
		{
			$this->entropy .= chr( bindec(substr($binarystring,($i*8),8)) );
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
