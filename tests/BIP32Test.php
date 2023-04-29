<?php
/**
 * BIP32 Test
 **/
namespace SatyrSoftware\Cryptotool;


class BIP32Test extends \PHPUnit\Framework\TestCase
{
	public $test_cases=array(
		[
			'mnemonic'	=> 'ripple attitude job slide fiber ordinary purpose wish silver ugly junk predict',
			'entropy'	=> 'ba41d5e065c55b38ab9fe4c8dd81e554',
			'seed'		=> '8506c04f131cdbc4c4ce8e2af7f964ff9bab2fefccc13c49bb3159a08c0d923e4666eca953892a7ffbb945241d656553905aa0ba4a6c3fdc4486fdc79b778cd3',
			'passphrase'	=> '',
		],
		[
			'mnemonic'	=> 'ripple attitude job slide fiber ordinary purpose wish silver ugly junk predict',
			'entropy'	=> 'ba41d5e065c55b38ab9fe4c8dd81e554',
			'seed'		=> '67f93560761e20617de26e0cb84f7234aaf373ed2e66295c3d7397e6d7ebe882ea396d5d293808b0defd7edd2babd4c091ad942e6a9351e6d075a29d4df872af',
			'passphrase'	=> '',
		],
	);

	public function testBIP32()
	{
		foreach ($this->test_cases as $test)
		{
			$bip32 = new BIP32($test['seed']);				// Hex input
			$this->assertEquals($test['seed'],bin2hex($bip32->toSeed()));
#echo "Master Private Key = ".bin2hex($bip32->master_private_key)."\n";
#echo "Master Chain Code  = ".bin2hex($bip32->master_chain_code)."\n";
			$bip32 = new BIP32(hex2bin($test['seed']));			// Binary input
			$this->assertEquals($test['seed'],bin2hex($bip32->toSeed()));
		}
	}
}
?>
