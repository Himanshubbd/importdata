<?php

function decryptData($encrypted_data, $key) {
    // Decode the Base64-encoded data
    $decoded_data = base64_decode($encrypted_data);

    // Extract the IV and ciphertext
    $iv = substr($decoded_data, 0, 16); // First 16 bytes = IV
    $ciphertext = substr($decoded_data, 16); // Remaining bytes = Ciphertext

    // Decrypt the ciphertext
    $plaintext = openssl_decrypt($ciphertext, 'aes-256-cbc', $key, 0, $iv);

    return $plaintext;
}
