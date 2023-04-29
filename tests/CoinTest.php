<?php
/**
 * Coin specific tests
 *
 * No need to test the generate/etc's cos it all works. All about generating coin speicific seeds?
 **/
namespace SatyrSoftware\Cryptotool;

class CoinTest extends \PHPUnit\Framework\TestCase
{
	public $test_cases=array(
		[
			'coin'		=> 'QRL',
			'dictionary'	=> 'qrl',	// qrl.txt
			'description'	=> 'QRL coin : height 10, SHAKE_128 (default)',
			'seedlength'	=> 408,
			'mnemonic'	=> 'absorb filled recent tenant bias writ mere nape limit cairo prone energy noise wipe were acute junta super liquid cape cousin bright pastel adobe cost acute scare cow chop adhere decide magnum brisk goose',
			'seed'		=> '010500b26e0e166fdb8a79337f3224ab246d964fb1f8002475edb27fd2403191d89fc02e30a024c0131d29302a38a8541dd5f0',
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

	public function testQRL()
	{
		foreach ($this->test_cases as $test)
		{
			$entropy = Mnemonic::Mnemonic($test['mnemonic'],$test['dictionary'])->entropy;
			$this->assertEquals(((34*12)/8),strlen($entropy));
			$this->assertEquals($test['seed'],bin2hex($entropy));
		}
	}
}
?>
