<?php
/**
 * The Horde_Cipher_BlockMode_cbc:: This class implements the
 * Horde_Cipher_BlockMode using the Cipher Block Chaininng method of
 * encrypting blocks of data.
 *
 * $Horde: horde/lib/Cipher/BlockMode/cbc.php,v 1.2.2.4 2003/04/12 20:50:41 slusarz Exp $
 *
 * Copyright 2002-2003 Mike Cochrane <mike@graftonhall.co.nz>
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Mike Cochrane <mike@graftonhall.co.nz>
 * @version $Revision: 1.2.2.4 $
 * @since   Horde 2.2
 * @package horde.cipher
 */
class Horde_Cipher_BlockMode_cbc extends Horde_Cipher_BlockMode {

    function encrypt(&$cipher, $plaintext)
    {
        $encrypted = '';

        $blocksize = $cipher->getBlockSize();
        $previousCipher = $this->_iv;

        $jMax = strlen($plaintext);
        for ($j = 0; $j < $jMax; $j += $blocksize) {
            $plain = substr($plaintext, $j, $blocksize);

            if (strlen($plain) < $blocksize) {
                // pad the block with \0's if it's not long enough
                $plain = str_pad($plain, 8, "\0");
            }

            $plain = $plain ^ $previousCipher;
            $previousCipher = $cipher->encryptBlock($plain);
            $encrypted .= $previousCipher;
        }

        return $encrypted;
    }

    function decrypt(&$cipher, $ciphertext)
    {
        $decrypted = '';

        $blocksize = $cipher->getBlockSize();
        $previousCipher = $this->_iv;

        $jMax = strlen($ciphertext);
        for ($j = 0; $j < $jMax; $j += $blocksize) {
            $plain = substr($ciphertext, $j, $blocksize);
            $decrypted .= $cipher->decryptBlock($plain) ^ $previousCipher;
            $previousCipher = $plain;
        }

        // remove trailing \0's used to pad the last block
        while (substr($decrypted, -1, 1) == "\0") {
            $decrypted = substr($decrypted, 0, -1);
        }

        return $decrypted;
    }

}
