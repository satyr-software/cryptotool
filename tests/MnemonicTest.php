<?php
/**
 * Mnemonic Test
 **/
namespace SatyrSoftware\Cryptotool;

class MnemonicTest extends \PHPUnit\Framework\TestCase
{
	public $test_cases=array(
		[
			'mnemonic'	=> 'ripple attitude job slide fiber ordinary purpose wish silver ugly junk appear',
			'entropy'	=> 'ba41d5e065c55b38ab9fe4c8dd81e554',
			'seed'		=> 'bfae7ba063e09ea9b3d940a76af23c28d908fe92c8be6ca96093d8a10334df97a03a5a433014b95417a90168740fb90e8ac5a6ad17d50321aadc92dc1b1b093d',
			'passphrase'	=> '',
		],
/*
		[
			'mnemonic'	=> 'ripple attitude job slide fiber ordinary purpose wish silver ugly junk predict',
			'entropy'	=> 'ba41d5e065c55b38ab9fe4c8dd81e554',
			'seed'		=> '67f93560761e20617de26e0cb84f7234aaf373ed2e66295c3d7397e6d7ebe882ea396d5d293808b0defd7edd2babd4c091ad942e6a9351e6d075a29d4df872af',
			'passphrase'	=> '',
		],
*/
	);

	public function testMnemonicGenerate()
	{
		$bip39 = Mnemonic::Generate(128);	// 12 words
		$this->assertCount(12,$bip39->toMnemonic());
		$this->assertEquals(16,strlen($bip39->entropy));

		$bip39 = Mnemonic::Generate(256);	// 24 words
		$this->assertCount(24,$bip39->toMnemonic());
		$this->assertEquals(32,strlen($bip39->entropy));

	}
/*
	public function testMnemonicMnemonic()
	{
		foreach ($this->test_cases as $test)
		{
			$bip39 = Mnemonic::Mnemonic($test['mnemonic']);					// String input
			$this->assertEquals($test['mnemonic'],implode(' ',$bip39->toMnemonic()));	// Also checks wordcount at the same time
			$this->assertEquals($test['entropy'],bin2hex($bip39->entropy));			// Auto check for strlen

			$bip39 = Mnemonic::Mnemonic(explode(' ',$test['mnemonic']));			// Array input
			$this->assertEquals($test['mnemonic'],implode(' ',$bip39->toMnemonic()));
			$this->assertEquals($test['entropy'],bin2hex($bip39->entropy));
		}

	}
*/
	public function testMnemonicEntropy()
	{
		foreach ($this->test_cases as $test)
		{
			$bip39 = Mnemonic::Entropy($test['entropy']);				      // Hex input
			$this->assertEquals($test['mnemonic'],implode(' ',$bip39->toMnemonic()));       // Also checks wordcount at the same time
			$this->assertEquals($test['entropy'],bin2hex($bip39->entropy));		 // Auto check for strlen

			$bip39 = Mnemonic::Entropy(hex2bin($test['entropy']));			     // Binary input
			$this->assertEquals($test['mnemonic'],implode(' ',$bip39->toMnemonic()));
			$this->assertEquals($test['entropy'],bin2hex($bip39->entropy));
		}
	}


}
?>
