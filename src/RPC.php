<?php
/*
 * RPC - RPC Library to talk to crypto daemons run locally
 *
 * Entry point:
 *  RPC(coin)
 * Inherited:
 * Outputs
 * ->toMnemonic()	// Customized due to checksumming
 * ->toSeed()		// Customized for Bitcoin
 *
 * Uses "wordCount" to compute the rest? Presetting vars is faster, but calculating on the fly allows adjustment?
 */
declare(strict_types=1);

namespace SatyrSoftware\Cryptotool;

use SatyrSoftware\Cryptotool\Mnemonic;

class RPC extends Mnemonic
{
	/* Entry points */
	$supported_daemon=[
		'bitcoincore'	=> array(
			'coin'	=> 'btc',
			'type'	=> 'json-rpc',
			),
	];

	# Contructor - pick up daemon
	public function __construct(string $daemon='bitcoincore',?string $entropy = null)
	{
	}
	/* Output functions */

	/* Internal functions */
	/*
	 * generateEntropy() = parent
	 * setEntropy(string) = parent
	 */
}
?>
