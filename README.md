# CryptoTool

This will be a tool to cover most utilities required to make a Cryptocurrency tool (Mostly HD Wallets)

# Features

# Plan

- [x] BIP39 - Mnemonic/entropy
- [ ] BIP32 - HD Wallets
- [ ] BIP44 - Multi-Account Herarchy
- [ ] BIP49 - P2WPKH-nested-in-P2SH based
- [ ] Laravel testing/integration

# Usage
Usual PHP thing to do with composer: `composer require satyr-software/cryptotool`
## BIP39 - Mnemonic tool
```
use SatyrSoftware\CryptoTool\BIP39
```
### Entry points
 BIP39::Mnemonic(string|array)
 BIP39::Generate(int words)
 BIP39::Entropy(string hextring)
### Outputs
 ->dumpMnemonic():array[words]
 ->entropy : string(binary)

# Ideas

# Related projects

- [Ian Coleman's BIP39 tool](https://github.com/iancoleman/bip39)

# Credits

- Based partially on code at [furqansiddiqui/bip39-mnemonic-php](https://github.com/furqansiddiqui/bip39-mnemonic-php/)

Parts taken:
- the "Words/Entropy/Generate" static functions interface
- Wordlist finding functions (pretty generic anyway)



