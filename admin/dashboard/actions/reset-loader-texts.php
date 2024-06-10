<?php
session_start();

require $_SERVER['DOCUMENT_ROOT'] . '/encryption.php';
require $_SERVER["DOCUMENT_ROOT"] . '/settings-default.php';

$filePath = $_SERVER["DOCUMENT_ROOT"] . '/settings.txt';

if (file_exists($filePath)) {
    $site_settings_encrypted = file_get_contents($filePath);

    try {
        // Trying to decrypt using AES
        $site_settings_decrypted = AesCtr::decrypt($site_settings_encrypted, 'c3VwZXJfc3Ryb25nX2tleQ', 128);
        $site_settings = json_decode($site_settings_decrypted, true);
        if ($site_settings === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid", 1);
        }
    } catch (Exception $error) {
        // Trying to decrypt using BASE64
        $site_settings_decrypted = base64_decode($site_settings_encrypted);
        $site_settings = json_decode($site_settings_decrypted, true);
    }
} else {
    $site_settings = $settings_default;
}

$site_settings['loader_text'] = $settings_default['loader_text'];

$data_json = json_encode($site_settings);

$data_encrypted = AesCtr::encrypt($data_json, "c3VwZXJfc3Ryb25nX2tleQ", 128);
$is_settings_saved = file_put_contents($filePath, $data_encrypted);

if ($is_settings_saved) {
    $_SESSION["update-message-status"] = 'save-success';
} else {
    $_SESSION["update-message-status"] = 'save-error';
}

header("Location: ../");

?>