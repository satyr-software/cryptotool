# CryptoTool
[![License: MIT](https://img.shields.io/github/license/satyr-software/cryptotool)](https://github.com/satyr-software/cryptotool/blob/master/LICENSE)

This will be a tool to cover most utilities required to make a Cryptocurrency tool (Mostly HD Wallets)

# Features

# Plan

- [x] BIP39 - Mnemonic/entropy
- [ ] BIP32 - Extended Keys/HD Wallets
- [ ] BIP44 - Multi-Account Herarchy
- [ ] BIP49 - P2WPKH-nested-in-P2SH based
- [ ] Laravel testing/integration

# Usage
Usual PHP thing to do with composer: `composer require satyr-software/cryptotool`
## BIP39 - Mnemonic tool
```
use SatyrSoftware\Cryptotool\BIP39
```
### Entry points
 BIP39::Mnemonic(string|array)

 BIP39::Generate(int words)

 BIP39::Entropy(string hextring)

### Outputs

 ->toMnemonic():array[words]

 ->toSeed(): string(binary)

 ->entropy : string(binary)

## BIP32 - Extended Keys/HD Wallets

Disclaimer: BIP32 uses ECCurve implementation that is not personally verified.

```
use SatyrSoftware\Cryptotool\BIP32
```
### Entry points

 new BIP32(string[binary|hex])

### Outputs

 ->master_private_key
 ->master_public_key
 ->master_chain_code

## Mnemonic - Mnemonic tool
This is a clone for BIP39 but with more generic parameters (no checksumming, etc) for use with other Crypto HD Gen like QRL. The unit is now in 'bits' not words, as different coins
can end up with different length wordlists (eg. 4096 = QRL). Autodetects the bits from dictionary
```
 new Mnemonic::Mnemonic(string|array,dictionary)
```
This module will be Beta for a bit, but will be used for just about any other non-BIP39 compliant coin


# Ideas

# Related projects

- [Ian Coleman's BIP39 tool](https://github.com/iancoleman/bip39)

# Credits

- Based partially on code at [furqansiddiqui/bip39-mnemonic-php](https://github.com/furqansiddiqui/bip39-mnemonic-php/)
 Rewrote a lot of the code
- Outsourced a lot of the Elliptic Curve maths to [simplito/elliptic-php](https://github.com/simplito/elliptic-php)
- Base58 conversion done by Stephen Hill package [stephenhill/base58](https://github.com/stephenhill/base58)

