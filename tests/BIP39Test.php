<?php
/**
 * BIP39 Test
 **/
namespace SatyrSoftware\Cryptotool;

class BIP39Test extends \PHPUnit\Framework\TestCase
{
	public $test_cases=array(
		[
			'mnemonic'	=> 'ripple attitude job slide fiber ordinary purpose wish silver ugly junk predict',
			'entropy'	=> 'ba41d5e065c55b38ab9fe4c8dd81e554',
			'seed'		=> '8506c04f131cdbc4c4ce8e2af7f964ff9bab2fefccc13c49bb3159a08c0d923e4666eca953892a7ffbb945241d656553905aa0ba4a6c3fdc4486fdc79b778cd3',
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

	public function testBIP39Generate()
	{
		$bip39 = BIP39::Generate(12);
		$this->assertCount(12,$bip39->toMnemonic());
		$this->assertEquals(16,strlen($bip39->entropy));

		$bip39 = BIP39::Generate(24);
		$this->assertCount(24,$bip39->toMnemonic());
		$this->assertEquals(32,strlen($bip39->entropy));

	}
	public function testBIP39Mnemonic()
	{
		foreach ($this->test_cases as $test)
		{
			$bip39 = BIP39::Mnemonic($test['mnemonic']);					// String input
			$this->assertEquals($test['mnemonic'],implode(' ',$bip39->toMnemonic()));	// Also checks wordcount at the same time
			$this->assertEquals($test['entropy'],bin2hex($bip39->entropy));			// Auto check for strlen

			$bip39 = BIP39::Mnemonic(explode(' ',$test['mnemonic']));			// Array input
			$this->assertEquals($test['mnemonic'],implode(' ',$bip39->toMnemonic()));
			$this->assertEquals($test['entropy'],bin2hex($bip39->entropy));
		}

	}
	public function testBIP39Entropy()
	{
		foreach ($this->test_cases as $test)
		{
			$bip39 = Bip39::Entropy($test['entropy']);					// Hex input
			$this->assertEquals($test['mnemonic'],implode(' ',$bip39->toMnemonic()));	// Also checks wordcount at the same time
			$this->assertEquals($test['entropy'],bin2hex($bip39->entropy));			// Auto check for strlen

			$bip39 = Bip39::Entropy(hex2bin($test['entropy']));				// Binary input
			$this->assertEquals($test['mnemonic'],implode(' ',$bip39->toMnemonic()));
			$this->assertEquals($test['entropy'],bin2hex($bip39->entropy));
		}
	}
	public function testBIP39Seed()
	{
		foreach ($this->test_cases as $test)
		{
			$bip39 = Bip39::Entropy($test['entropy']);
			$this->assertEquals($test['seed'],bin2hex($bip39->toSeed($test['passphrase'])));
		}
	}
}
?>
