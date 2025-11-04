<?php
/**
 * PromoBar (Announcement banner)
 *
 * @author BeDOM - Solutions Web
 * @copyright 2025 BeDOM - Solutions Web
 * @license MIT
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class Promobar extends Module
{
    // Config keys
    public const CFG_ENABLED = 'PROMOBAR_ENABLED';
    public const CFG_DISMISSIBLE = 'PROMOBAR_DISMISSIBLE';
    public const CFG_POSITION = 'PROMOBAR_POSITION';      // 'afterbody' | 'top'
    public const CFG_COOKIE_DAYS = 'PROMOBAR_COOKIE_DAYS';   // int

    // Global styling
    public const CFG_BG_COLOR = 'PROMOBAR_BG_COLOR';     // Global background color
    public const CFG_CONTROLS_COLOR = 'PROMOBAR_CONTROLS_COLOR';  // 'dark' | 'light'

    // Carousel settings
    public const CFG_CAROUSEL_ENABLED = 'PROMOBAR_CAROUSEL_ENABLED';
    public const CFG_CAROUSEL_TRANSITION = 'PROMOBAR_CAROUSEL_TRANSITION';  // 'fade' | 'slide'
    public const CFG_CAROUSEL_INTERVAL = 'PROMOBAR_CAROUSEL_INTERVAL';      // seconds
    public const CFG_CAROUSEL_ARROWS = 'PROMOBAR_CAROUSEL_ARROWS';          // bool
    public const CFG_CAROUSEL_PAUSE = 'PROMOBAR_CAROUSEL_PAUSE';            // bool (pause on hover)

    // Messages (JSON array of message objects with individual settings)
    public const CFG_MESSAGES = 'PROMOBAR_MESSAGES';

    // Legacy keys (for migration)
    public const CFG_MESSAGE = 'PROMOBAR_MESSAGE';       // multilingual
    public const CFG_TEXT_COLOR = 'PROMOBAR_TEXT_COLOR';
    public const CFG_START_DATE = 'PROMOBAR_START_DATE';
    public const CFG_END_DATE = 'PROMOBAR_END_DATE';
    public const CFG_FONT_FAMILY = 'PROMOBAR_FONT_FAMILY';   // whitelist
    public const CFG_ANIMATION = 'PROMOBAR_ANIMATION';     // enum: none,scroll,pulse,blink
    public const CFG_COUNTDOWN = 'PROMOBAR_COUNTDOWN';     // bool
    public const CFG_CTA_ENABLED = 'PROMOBAR_CTA_ENABLED';
    public const CFG_CTA_TEXT = 'PROMOBAR_CTA_TEXT';      // multilingual
    public const CFG_CTA_URL = 'PROMOBAR_CTA_URL';
    public const CFG_CTA_BG_COLOR = 'PROMOBAR_CTA_BG_COLOR';
    public const CFG_CTA_TEXT_COLOR = 'PROMOBAR_CTA_TEXT_COLOR';
    public const CFG_CTA_BORDER = 'PROMOBAR_CTA_BORDER';
    // Author / branding
    public const AUTHOR_NAME = 'BeDOM – Solutions Web';
    public const AUTHOR_SITE = 'https://bedom.fr/';
    public const AUTHOR_CONTACT = 'https://bedom.fr/support';
    public const AUTHOR_LINKEDIN = 'https://www.linkedin.com/company/bedom-web';
    public const AUTHOR_FACEBOOK = 'https://www.facebook.com/agencebedom';
    public const AUTHOR_INSTAGRAM = 'https://www.instagram.com/bedom_web/';

    public function __construct()
    {
        $this->name = 'promobar';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'BeDOM - Solutions Web';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->ps_versions_compliancy = ['min' => '1.7.0.0', 'max' => _PS_VERSION_];

        parent::__construct();

        // English strings → validator-friendly
        $this->displayName = $this->l('PromoBar (Announcement banner)');
        $this->description = $this->l('Configurable, secure and self-contained announcement bar (multilingual, dates, colors, font, button, animations, countdown).');
        $this->confirmUninstall = $this->l('Remove the module and its configuration?');
    }

    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        $idShop = (int) $this->context->shop->id;

        // Global settings
        Configuration::updateValue(self::CFG_ENABLED, 1, false, null, $idShop);
        Configuration::updateValue(self::CFG_DISMISSIBLE, 1, false, null, $idShop);
        Configuration::updateValue(self::CFG_POSITION, 'afterbody', false, null, $idShop);
        Configuration::updateValue(self::CFG_COOKIE_DAYS, 30, false, null, $idShop);
        Configuration::updateValue(self::CFG_BG_COLOR, '#111111', false, null, $idShop);
        Configuration::updateValue(self::CFG_CONTROLS_COLOR, 'light', false, null, $idShop);

        // Carousel settings
        Configuration::updateValue(self::CFG_CAROUSEL_ENABLED, 0, false, null, $idShop);
        Configuration::updateValue(self::CFG_CAROUSEL_TRANSITION, 'fade', false, null, $idShop);
        Configuration::updateValue(self::CFG_CAROUSEL_INTERVAL, 5, false, null, $idShop);
        Configuration::updateValue(self::CFG_CAROUSEL_ARROWS, 1, false, null, $idShop);
        Configuration::updateValue(self::CFG_CAROUSEL_PAUSE, 1, false, null, $idShop);

        // Create default message (bg_color is now global)
        $defaultMessage = [
            'id' => 1,
            'message' => [],
            'text_color' => '#ffffff',
            'font_family' => 'system-ui',
            'animation' => 'none',
            'start_date' => '',
            'end_date' => '',
            'countdown' => 0,
            'cta_enabled' => 0,
            'cta_text' => [],
            'cta_url' => '',
            'cta_bg_color' => 'transparent',
            'cta_text_color' => '#ffffff',
            'cta_border' => '#ffffff',
        ];

        foreach (Language::getLanguages(false) as $lang) {
            $idLang = (int) $lang['id_lang'];
            $defaultMessage['message'][$idLang] = $this->l('Your message here.');
            $defaultMessage['cta_text'][$idLang] = $this->l('Learn more');
        }

        $messages = [$defaultMessage];
        Configuration::updateValue(self::CFG_MESSAGES, json_encode($messages), false, null, $idShop);

        // Hooks
        $ok = $this->registerHook('displayTop')
            && $this->registerHook('displayAfterBodyOpeningTag')
            && $this->registerHook('header');

        $this->migrateLegacyConfig($idShop);
        return $ok;
    }

    private function migrateLegacyConfig($idShop)
    {
        // Check if we need to migrate from old single-message format
        $existingMessages = Configuration::get(self::CFG_MESSAGES, null, null, $idShop);
        if ($existingMessages) {
            return; // Already using new format
        }

        // Migrate old BEDOM_AB_* keys first
        $oldKeyMap = [
            'BEDOM_AB_ENABLED' => self::CFG_ENABLED,
            'BEDOM_AB_DISMISSIBLE' => self::CFG_DISMISSIBLE,
            'BEDOM_AB_BG_COLOR' => self::CFG_BG_COLOR,
            'BEDOM_AB_TEXT_COLOR' => self::CFG_TEXT_COLOR,
            'BEDOM_AB_START_DATE' => self::CFG_START_DATE,
            'BEDOM_AB_END_DATE' => self::CFG_END_DATE,
        ];

        foreach ($oldKeyMap as $old => $new) {
            $oldVal = Configuration::get($old, null, null, $idShop);
            if ($oldVal !== false) {
                Configuration::updateValue($new, $oldVal, false, null, $idShop);
            }
        }

        // Check if we have old message config
        $oldMessage = Configuration::get(self::CFG_MESSAGE, null, null, $idShop);
        if (!$oldMessage || $oldMessage === 'Your message here.') {
            return; // No legacy config to migrate
        }

        // Build migrated message from legacy config (bg_color is now global, not per-message)
        $migratedMessage = [
            'id' => 1,
            'message' => [],
            'text_color' => Configuration::get(self::CFG_TEXT_COLOR, null, null, $idShop) ?: '#ffffff',
            'font_family' => Configuration::get(self::CFG_FONT_FAMILY, null, null, $idShop) ?: 'system-ui',
            'animation' => Configuration::get(self::CFG_ANIMATION, null, null, $idShop) ?: 'none',
            'start_date' => Configuration::get(self::CFG_START_DATE, null, null, $idShop) ?: '',
            'end_date' => Configuration::get(self::CFG_END_DATE, null, null, $idShop) ?: '',
            'countdown' => (int) Configuration::get(self::CFG_COUNTDOWN, null, null, $idShop),
            'cta_enabled' => (int) Configuration::get(self::CFG_CTA_ENABLED, null, null, $idShop),
            'cta_text' => [],
            'cta_url' => Configuration::get(self::CFG_CTA_URL, null, null, $idShop) ?: '',
            'cta_bg_color' => Configuration::get(self::CFG_CTA_BG_COLOR, null, null, $idShop) ?: 'transparent',
            'cta_text_color' => Configuration::get(self::CFG_CTA_TEXT_COLOR, null, null, $idShop) ?: '#ffffff',
            'cta_border' => Configuration::get(self::CFG_CTA_BORDER, null, null, $idShop) ?: '#ffffff',
        ];

        // Get multilingual message text
        foreach (Language::getLanguages(false) as $lang) {
            $idLang = (int) $lang['id_lang'];
            $migratedMessage['message'][$idLang] = Configuration::get(self::CFG_MESSAGE, $idLang, null, $idShop) ?: '';
            $migratedMessage['cta_text'][$idLang] = Configuration::get(self::CFG_CTA_TEXT, $idLang, null, $idShop) ?: '';
        }

        $messages = [$migratedMessage];
        Configuration::updateValue(self::CFG_MESSAGES, json_encode($messages), false, null, $idShop);
    }

    public function uninstall()
    {
        $keys = [
            self::CFG_ENABLED,
            self::CFG_DISMISSIBLE,
            self::CFG_POSITION,
            self::CFG_COOKIE_DAYS,
            self::CFG_BG_COLOR,
            self::CFG_CONTROLS_COLOR,
            self::CFG_CAROUSEL_ENABLED,
            self::CFG_CAROUSEL_TRANSITION,
            self::CFG_CAROUSEL_INTERVAL,
            self::CFG_CAROUSEL_ARROWS,
            self::CFG_CAROUSEL_PAUSE,
            self::CFG_MESSAGES,
            // Legacy keys (in case they still exist)
            self::CFG_MESSAGE,
            self::CFG_TEXT_COLOR,
            self::CFG_START_DATE,
            self::CFG_END_DATE,
            self::CFG_FONT_FAMILY,
            self::CFG_ANIMATION,
            self::CFG_COUNTDOWN,
            self::CFG_CTA_ENABLED,
            self::CFG_CTA_TEXT,
            self::CFG_CTA_URL,
            self::CFG_CTA_BG_COLOR,
            self::CFG_CTA_TEXT_COLOR,
            self::CFG_CTA_BORDER,
        ];
        foreach ($keys as $k) {
            Configuration::deleteByName($k);
        }
        return parent::uninstall();
    }

    /** BO: config page */
    public function getContent()
    {
        $out = '';
        if (Tools::isSubmit('submitPromobar')) {
            $this->postProcess();
            $out .= $this->displayConfirmation($this->l('Configuration saved.'));
        }
        $out .= $this->renderForm();
        $out .= $this->renderAuthorCard();

        return $out;
    }

    protected function postProcess()
    {
        $idShop = (int) $this->context->shop->id;

        // Global settings
        $enabled = (int) Tools::getValue(self::CFG_ENABLED, 0);
        $dismiss = (int) Tools::getValue(self::CFG_DISMISSIBLE, 0);
        $position = (string) Tools::getValue(self::CFG_POSITION, 'afterbody');
        if (!in_array($position, ['afterbody', 'top'], true)) {
            $position = 'afterbody';
        }

        $cookieDays = (int) Tools::getValue(self::CFG_COOKIE_DAYS, 30);
        $allowedCookie = [1, 3, 7, 15, 30, 90, 365];
        if (!in_array($cookieDays, $allowedCookie, true)) {
            $cookieDays = 30;
        }

        // Carousel settings
        $carouselEnabled = (int) Tools::getValue(self::CFG_CAROUSEL_ENABLED, 0);
        $carouselTransition = (string) Tools::getValue(self::CFG_CAROUSEL_TRANSITION, 'fade');
        if (!in_array($carouselTransition, ['fade', 'slide'], true)) {
            $carouselTransition = 'fade';
        }
        $carouselInterval = (int) Tools::getValue(self::CFG_CAROUSEL_INTERVAL, 5);
        if ($carouselInterval < 1) {
            $carouselInterval = 5;
        }
        $carouselArrows = (int) Tools::getValue(self::CFG_CAROUSEL_ARROWS, 1);
        $carouselPause = (int) Tools::getValue(self::CFG_CAROUSEL_PAUSE, 1);

        // Global background color
        $reColor = '/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$|^transparent$/i';
        $bgColor = Tools::substr(trim((string) Tools::getValue(self::CFG_BG_COLOR, '#111111')), 0, 20);
        if (!preg_match($reColor, $bgColor)) {
            $bgColor = '#111111';
        }

        // Controls color (dark or light)
        $controlsColor = (string) Tools::getValue(self::CFG_CONTROLS_COLOR, 'light');
        if (!in_array($controlsColor, ['dark', 'light'], true)) {
            $controlsColor = 'light';
        }

        // Process messages
        $messagesData = Tools::getValue('messages', []);
        $validatedMessages = [];
        $fontWhitelist = $this->getFontWhitelist();
        $allowedAnim = ['none', 'scroll', 'pulse', 'blink'];
        $reDate = '/^\d{4}-\d{2}-\d{2}$/';

        foreach ($messagesData as $msgData) {
            if (!is_array($msgData)) {
                continue;
            }

            $message = [
                'id' => isset($msgData['id']) ? (int) $msgData['id'] : count($validatedMessages) + 1,
                'message' => [],
                'text_color' => '#ffffff',
                'font_family' => 'system-ui',
                'animation' => 'none',
                'start_date' => '',
                'end_date' => '',
                'countdown' => 0,
                'cta_enabled' => 0,
                'cta_text' => [],
                'cta_url' => '',
                'cta_bg_color' => 'transparent',
                'cta_text_color' => '#ffffff',
                'cta_border' => '#ffffff',
            ];

            // Multilingual message text
            if (isset($msgData['message']) && is_array($msgData['message'])) {
                foreach ($msgData['message'] as $idLang => $text) {
                    $text = Tools::substr(trim(strip_tags((string) $text)), 0, 1000);
                    $message['message'][(int) $idLang] = $text;
                }
            }

            // Text color
            if (isset($msgData['text_color']) && preg_match($reColor, $msgData['text_color'])) {
                $message['text_color'] = $msgData['text_color'];
            }

            // Font and animation
            if (isset($msgData['font_family']) && isset($fontWhitelist[$msgData['font_family']])) {
                $message['font_family'] = $msgData['font_family'];
            }
            if (isset($msgData['animation']) && in_array($msgData['animation'], $allowedAnim, true)) {
                $message['animation'] = $msgData['animation'];
            }

            // Dates
            if (isset($msgData['start_date']) && preg_match($reDate, $msgData['start_date'])) {
                $message['start_date'] = $msgData['start_date'];
            }
            if (isset($msgData['end_date']) && preg_match($reDate, $msgData['end_date'])) {
                $message['end_date'] = $msgData['end_date'];
            }

            // Countdown
            $message['countdown'] = isset($msgData['countdown']) ? (int) $msgData['countdown'] : 0;

            // CTA
            $message['cta_enabled'] = isset($msgData['cta_enabled']) ? (int) $msgData['cta_enabled'] : 0;

            if (isset($msgData['cta_text']) && is_array($msgData['cta_text'])) {
                foreach ($msgData['cta_text'] as $idLang => $text) {
                    $text = Tools::substr(trim(Tools::purifyHTML((string) $text)), 0, 80);
                    $message['cta_text'][(int) $idLang] = $text;
                }
            }

            if (isset($msgData['cta_url'])) {
                $url = trim($msgData['cta_url']);
                if ($url && filter_var($url, FILTER_VALIDATE_URL) && preg_match('#^https?://#i', $url)) {
                    $message['cta_url'] = Tools::substr($url, 0, 255);
                }
            }

            if (isset($msgData['cta_bg_color']) && preg_match($reColor, $msgData['cta_bg_color'])) {
                $message['cta_bg_color'] = $msgData['cta_bg_color'];
            }
            if (isset($msgData['cta_text_color']) && preg_match($reColor, $msgData['cta_text_color'])) {
                $message['cta_text_color'] = $msgData['cta_text_color'];
            }
            if (isset($msgData['cta_border']) && preg_match($reColor, $msgData['cta_border'])) {
                $message['cta_border'] = $msgData['cta_border'];
            }

            $validatedMessages[] = $message;
        }

        // Save all configuration
        Configuration::updateValue(self::CFG_ENABLED, $enabled, false, null, $idShop);
        Configuration::updateValue(self::CFG_DISMISSIBLE, $dismiss, false, null, $idShop);
        Configuration::updateValue(self::CFG_POSITION, $position, false, null, $idShop);
        Configuration::updateValue(self::CFG_COOKIE_DAYS, $cookieDays, false, null, $idShop);
        Configuration::updateValue(self::CFG_BG_COLOR, $bgColor, false, null, $idShop);
        Configuration::updateValue(self::CFG_CONTROLS_COLOR, $controlsColor, false, null, $idShop);

        Configuration::updateValue(self::CFG_CAROUSEL_ENABLED, $carouselEnabled, false, null, $idShop);
        Configuration::updateValue(self::CFG_CAROUSEL_TRANSITION, $carouselTransition, false, null, $idShop);
        Configuration::updateValue(self::CFG_CAROUSEL_INTERVAL, $carouselInterval, false, null, $idShop);
        Configuration::updateValue(self::CFG_CAROUSEL_ARROWS, $carouselArrows, false, null, $idShop);
        Configuration::updateValue(self::CFG_CAROUSEL_PAUSE, $carouselPause, false, null, $idShop);

        Configuration::updateValue(self::CFG_MESSAGES, json_encode($validatedMessages), false, null, $idShop);
    }

    /** Whitelist of fonts (no external loads) */
    protected function getFontWhitelist()
    {
        return [
            'system-ui' => 'system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif',
            'inter' => '"Inter", system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif',
            'arial' => 'Arial, Helvetica, sans-serif',
            'helvetica' => 'Helvetica, Arial, sans-serif',
            'georgia' => 'Georgia, "Times New Roman", Times, serif',
            'times' => '"Times New Roman", Times, serif',
            'courier' => '"Courier New", Courier, monospace',
            'mono' => 'ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace',
        ];
    }

    /** Build the configuration form */
    protected function renderForm()
    {
        $idShop = (int) $this->context->shop->id;

        // Load messages from JSON
        $messagesJson = Configuration::get(self::CFG_MESSAGES, null, null, $idShop);
        $messages = [];
        if ($messagesJson) {
            $decoded = json_decode($messagesJson, true);
            if (is_array($decoded)) {
                $messages = $decoded;
            }
        }

        // If no messages, create default
        if (empty($messages)) {
            $defaultMessage = [
                'id' => 1,
                'message' => [],
                'text_color' => '#ffffff',
                'font_family' => 'system-ui',
                'animation' => 'none',
                'start_date' => '',
                'end_date' => '',
                'countdown' => 0,
                'cta_enabled' => 0,
                'cta_text' => [],
                'cta_url' => '',
                'cta_bg_color' => 'transparent',
                'cta_text_color' => '#ffffff',
                'cta_border' => '#ffffff',
            ];
            foreach (Language::getLanguages(false) as $lang) {
                $idLang = (int) $lang['id_lang'];
                $defaultMessage['message'][$idLang] = $this->l('Your message here.');
                $defaultMessage['cta_text'][$idLang] = $this->l('Learn more');
            }
            $messages = [$defaultMessage];
        }

        // Prepare template variables
        $this->context->smarty->assign([
            'form_action' => AdminController::$currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'),
            'CFG_POSITION' => self::CFG_POSITION,
            'CFG_ENABLED' => self::CFG_ENABLED,
            'CFG_DISMISSIBLE' => self::CFG_DISMISSIBLE,
            'CFG_COOKIE_DAYS' => self::CFG_COOKIE_DAYS,
            'CFG_BG_COLOR' => self::CFG_BG_COLOR,
            'CFG_CONTROLS_COLOR' => self::CFG_CONTROLS_COLOR,
            'CFG_CAROUSEL_ENABLED' => self::CFG_CAROUSEL_ENABLED,
            'CFG_CAROUSEL_TRANSITION' => self::CFG_CAROUSEL_TRANSITION,
            'CFG_CAROUSEL_INTERVAL' => self::CFG_CAROUSEL_INTERVAL,
            'CFG_CAROUSEL_ARROWS' => self::CFG_CAROUSEL_ARROWS,
            'CFG_CAROUSEL_PAUSE' => self::CFG_CAROUSEL_PAUSE,
            'position' => Configuration::get(self::CFG_POSITION, null, null, $idShop) ?: 'afterbody',
            'enabled' => (int) Configuration::get(self::CFG_ENABLED, null, null, $idShop),
            'dismissible' => (int) Configuration::get(self::CFG_DISMISSIBLE, null, null, $idShop),
            'cookie_days' => (int) Configuration::get(self::CFG_COOKIE_DAYS, null, null, $idShop) ?: 30,
            'bg_color' => Configuration::get(self::CFG_BG_COLOR, null, null, $idShop) ?: '#111111',
            'controls_color' => Configuration::get(self::CFG_CONTROLS_COLOR, null, null, $idShop) ?: 'light',
            'cookie_options' => [
                ['value' => 1, 'label' => '1 ' . $this->l('day')],
                ['value' => 3, 'label' => '3 ' . $this->l('days')],
                ['value' => 7, 'label' => '7 ' . $this->l('days')],
                ['value' => 15, 'label' => '15 ' . $this->l('days')],
                ['value' => 30, 'label' => '30 ' . $this->l('days')],
                ['value' => 90, 'label' => '90 ' . $this->l('days')],
                ['value' => 365, 'label' => '365 ' . $this->l('days')],
            ],
            'carousel_enabled' => (int) Configuration::get(self::CFG_CAROUSEL_ENABLED, null, null, $idShop),
            'carousel_transition' => Configuration::get(self::CFG_CAROUSEL_TRANSITION, null, null, $idShop) ?: 'fade',
            'carousel_interval' => (int) Configuration::get(self::CFG_CAROUSEL_INTERVAL, null, null, $idShop) ?: 5,
            'carousel_arrows' => (int) Configuration::get(self::CFG_CAROUSEL_ARROWS, null, null, $idShop),
            'carousel_pause' => (int) Configuration::get(self::CFG_CAROUSEL_PAUSE, null, null, $idShop),
            'messages' => $messages,
            'languages' => Language::getLanguages(false),
            'font_options' => array_keys($this->getFontWhitelist()),
            'animation_options' => [
                ['value' => 'none', 'label' => $this->l('None')],
                ['value' => 'scroll', 'label' => $this->l('Horizontal marquee')],
                ['value' => 'pulse', 'label' => $this->l('Soft pulse')],
                ['value' => 'blink', 'label' => $this->l('Light blink')],
            ],
        ]);

        return $this->display(__FILE__, 'views/templates/admin/configure.tpl');
    }
    /** Render the author's badge/card with logo and social links */
    protected function renderAuthorCard()
    {
        $links = [];

        if (self::AUTHOR_SITE !== '' && filter_var(self::AUTHOR_SITE, FILTER_VALIDATE_URL)) {
            $links[] = ['label' => $this->l('Website'), 'url' => self::AUTHOR_SITE];
        }
        if (self::AUTHOR_CONTACT !== '' && filter_var(self::AUTHOR_CONTACT, FILTER_VALIDATE_URL)) {
            $links[] = ['label' => $this->l('Support'), 'url' => self::AUTHOR_CONTACT];
        }
        if (self::AUTHOR_LINKEDIN !== '' && filter_var(self::AUTHOR_LINKEDIN, FILTER_VALIDATE_URL)) {
            $links[] = ['label' => 'LinkedIn', 'url' => self::AUTHOR_LINKEDIN];
        }
        if (self::AUTHOR_FACEBOOK !== '' && filter_var(self::AUTHOR_FACEBOOK, FILTER_VALIDATE_URL)) {
            $links[] = ['label' => 'Facebook', 'url' => self::AUTHOR_FACEBOOK];
        }
        if (self::AUTHOR_INSTAGRAM !== '' && filter_var(self::AUTHOR_INSTAGRAM, FILTER_VALIDATE_URL)) {
            $links[] = ['label' => 'Instagram', 'url' => self::AUTHOR_INSTAGRAM];
        }

        $this->context->smarty->assign([
            'pb_author_name' => self::AUTHOR_NAME,
            'pb_author_logo' => $this->_path . 'views/img/author_logo.png',
            'pb_links' => $links,
            'pb_title' => $this->l('Module by'),
            'pb_tagline' => $this->l('High-performance web solutions, built for impact.'),
            'pb_module_version' => $this->version,
            'pb_module_name' => $this->displayName,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/author_card.tpl');
    }

    protected function getConfigValues()
    {
        $idShop = (int) $this->context->shop->id;

        $vals = [
            self::CFG_POSITION => (string) Configuration::get(self::CFG_POSITION, null, null, $idShop),
            self::CFG_ENABLED => (int) Configuration::get(self::CFG_ENABLED, null, null, $idShop),
            self::CFG_DISMISSIBLE => (int) Configuration::get(self::CFG_DISMISSIBLE, null, null, $idShop),
            self::CFG_BG_COLOR => (string) Configuration::get(self::CFG_BG_COLOR, null, null, $idShop),
            self::CFG_TEXT_COLOR => (string) Configuration::get(self::CFG_TEXT_COLOR, null, null, $idShop),
            self::CFG_START_DATE => (string) Configuration::get(self::CFG_START_DATE, null, null, $idShop),
            self::CFG_END_DATE => (string) Configuration::get(self::CFG_END_DATE, null, null, $idShop),
            self::CFG_COOKIE_DAYS => (int) Configuration::get(self::CFG_COOKIE_DAYS, null, null, $idShop),
            self::CFG_FONT_FAMILY => (string) Configuration::get(self::CFG_FONT_FAMILY, null, null, $idShop),
            self::CFG_ANIMATION => (string) Configuration::get(self::CFG_ANIMATION, null, null, $idShop),
            self::CFG_COUNTDOWN => (int) Configuration::get(self::CFG_COUNTDOWN, null, null, $idShop),
            self::CFG_CTA_ENABLED => (int) Configuration::get(self::CFG_CTA_ENABLED, null, null, $idShop),
            self::CFG_CTA_URL => (string) Configuration::get(self::CFG_CTA_URL, null, null, $idShop),
            self::CFG_CTA_BG_COLOR => (string) Configuration::get(self::CFG_CTA_BG_COLOR, null, null, $idShop),
            self::CFG_CTA_TEXT_COLOR => (string) Configuration::get(self::CFG_CTA_TEXT_COLOR, null, null, $idShop),
            self::CFG_CTA_BORDER => (string) Configuration::get(self::CFG_CTA_BORDER, null, null, $idShop),
        ];

        // Multilingual
        $vals[self::CFG_MESSAGE] = [];
        $vals[self::CFG_CTA_TEXT] = [];
        foreach (Language::getLanguages(false) as $lang) {
            $idLang = (int) $lang['id_lang'];
            $vals[self::CFG_MESSAGE][$idLang] = (string) Configuration::get(self::CFG_MESSAGE, $idLang, null, $idShop);
            $vals[self::CFG_CTA_TEXT][$idLang] = (string) Configuration::get(self::CFG_CTA_TEXT, $idLang, null, $idShop);
        }

        return $vals;
    }

    /** Front: assets */
    public function hookHeader($params)
    {
        $controller = $this->context->controller;

        if (method_exists($controller, 'registerStylesheet')) {
            $controller->registerStylesheet(
                'promobar-front',
                'modules/' . $this->name . '/views/css/front.css',
                ['media' => 'all', 'priority' => 150]
            );
            $controller->registerJavascript(
                'promobar-front',
                'modules/' . $this->name . '/views/js/front.js',
                ['position' => 'bottom', 'priority' => 150]
            );
        } else {
            $controller->addCSS('modules/' . $this->name . '/views/css/front.css', 'all');
            $controller->addJS('modules/' . $this->name . '/views/js/front.js');
        }
    }

    private function shouldRenderInHook($where)
    {
        $idShop = (int) $this->context->shop->id;
        $pos = (string) Configuration::get(self::CFG_POSITION, null, null, $idShop);
        if ($pos !== 'top' && $pos !== 'afterbody') {
            $pos = 'afterbody';
        }
        return $where === $pos;
    }

    /**
     * Safe mini-markup to HTML:
     * - **bold** → <strong>
     * - [text](https://url) → <a href="…">
     * - auto-link http(s)://… → <a href="…">
     * - \n → <br>
     */
    private function renderMiniMarkup($text)
    {
        // 1) escape everything
        $safe = htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');

        // 2) [label](url)
        $safe = preg_replace_callback(
            '#\[(.+?)\]\((https?://[^\s)]+)\)#i',
            function ($m) {
                $label = htmlspecialchars($m[1], ENT_QUOTES, 'UTF-8');
                $href = htmlspecialchars($m[2], ENT_QUOTES, 'UTF-8');
                return '<a href="' . $href . '" target="_blank" rel="noopener noreferrer">' . $label . '</a>';
            },
            $safe
        );

        // 3) auto-link naked URLs
        $safe = preg_replace_callback(
            '#(?<!["\'])(https?://[^\s<]+)#i',
            function ($m) {
                $u = htmlspecialchars($m[1], ENT_QUOTES, 'UTF-8');
                return '<a href="' . $u . '" target="_blank" rel="noopener noreferrer">' . $u . '</a>';
            },
            $safe
        );

        // 4) **bold**
        $safe = preg_replace('#\*\*(.+?)\*\*#s', '<strong>$1</strong>', $safe);

        // 5) newlines → <br>
        $safe = nl2br($safe, false);

        return $safe;
    }

    private function renderBar()
    {
        $idShop = (int) $this->context->shop->id;

        if (!(int) Configuration::get(self::CFG_ENABLED, null, null, $idShop)) {
            return '';
        }

        // Check if already dismissed (bypass with ?promobar_test=1 for testing)
        $dismissible = (int) Configuration::get(self::CFG_DISMISSIBLE, null, null, $idShop);
        if (Tools::getValue('promobar_preview') != 1 && Tools::getValue('promobar_test') != 1) {
            if ($dismissible && isset($_COOKIE['promobar_dismissed']) && $_COOKIE['promobar_dismissed'] === '1') {
                return '';
            }
        }

        // Load messages from JSON
        $messagesJson = Configuration::get(self::CFG_MESSAGES, null, null, $idShop);
        $messages = [];
        if ($messagesJson) {
            $decoded = json_decode($messagesJson, true);
            if (is_array($decoded)) {
                $messages = $decoded;
            }
        }

        if (empty($messages)) {
            return '';
        }

        // Filter messages by date range
        $today = new \DateTime('now');
        $idLang = (int) $this->context->language->id;
        $activeMessages = [];

        foreach ($messages as $msg) {
            // Check date range
            $startDate = isset($msg['start_date']) ? $msg['start_date'] : '';
            $endDate = isset($msg['end_date']) ? $msg['end_date'] : '';

            try {
                $eligibleStart = !$startDate || ($today >= new \DateTime($startDate . ' 00:00:00'));
                $eligibleEnd = !$endDate || ($today <= new \DateTime($endDate . ' 23:59:59'));
                $isActive = $eligibleStart && $eligibleEnd;
            } catch (\Exception $e) {
                $isActive = true;
            }

            if (!$isActive) {
                continue;
            }

            // Check if message has text for current language
            $messageText = isset($msg['message'][$idLang]) ? trim($msg['message'][$idLang]) : '';
            if ($messageText === '') {
                continue;
            }

            // Process message
            $processedMsg = [
                'id' => $msg['id'],
                'message_html' => $this->renderMiniMarkup($messageText),
                'message_plain' => $messageText,
                'text_color' => $msg['text_color'],
                'font_family' => $msg['font_family'],
                'animation' => $msg['animation'],
            ];

            // Process countdown
            $processedMsg['countdown_enabled'] = 0;
            $processedMsg['countdown_end'] = '';
            if ($msg['countdown'] && $endDate) {
                try {
                    $dtEnd = new \DateTime($endDate . ' 23:59:59');
                    if ($dtEnd > $today) {
                        $processedMsg['countdown_enabled'] = 1;
                        $processedMsg['countdown_end'] = $dtEnd->getTimestamp() * 1000;
                    }
                } catch (\Exception $e) {
                    // ignore
                }
            }

            // Process CTA
            $processedMsg['cta_enabled'] = $msg['cta_enabled'];
            $processedMsg['cta_text'] = isset($msg['cta_text'][$idLang]) ? trim($msg['cta_text'][$idLang]) : '';
            $processedMsg['cta_url'] = $msg['cta_url'];
            $processedMsg['cta_bg_color'] = $msg['cta_bg_color'];
            $processedMsg['cta_text_color'] = $msg['cta_text_color'];
            $processedMsg['cta_border'] = $msg['cta_border'];

            $activeMessages[] = $processedMsg;
        }

        if (empty($activeMessages)) {
            return '';
        }

        // Get carousel settings
        $carouselEnabled = (int) Configuration::get(self::CFG_CAROUSEL_ENABLED, null, null, $idShop);
        $fontWhitelist = $this->getFontWhitelist();

        // If carousel disabled or only one message, disable carousel mode
        if (!$carouselEnabled || count($activeMessages) === 1) {
            $carouselEnabled = 0;
        }

        $this->context->smarty->assign([
            'promobar_messages' => $activeMessages,
            'promobar_bg_color' => Configuration::get(self::CFG_BG_COLOR, null, null, $idShop) ?: '#111111',
            'promobar_controls_color' => Configuration::get(self::CFG_CONTROLS_COLOR, null, null, $idShop) ?: 'light',
            'promobar_carousel_enabled' => $carouselEnabled,
            'promobar_carousel_transition' => Configuration::get(self::CFG_CAROUSEL_TRANSITION, null, null, $idShop) ?: 'fade',
            'promobar_carousel_interval' => (int) Configuration::get(self::CFG_CAROUSEL_INTERVAL, null, null, $idShop) ?: 5,
            'promobar_carousel_arrows' => (int) Configuration::get(self::CFG_CAROUSEL_ARROWS, null, null, $idShop),
            'promobar_carousel_pause' => (int) Configuration::get(self::CFG_CAROUSEL_PAUSE, null, null, $idShop),
            'promobar_dismissible' => $dismissible,
            'promobar_cookie' => 'promobar_dismissed',
            'promobar_cookie_days' => (int) Configuration::get(self::CFG_COOKIE_DAYS, null, null, $idShop),
            'promobar_font_stacks' => $fontWhitelist,
        ]);

        return $this->display(__FILE__, 'views/templates/hook/displayTop.tpl');
    }

    public function hookDisplayTop($params)
    {
        return $this->shouldRenderInHook('top') ? $this->renderBar() : '';
    }

    public function hookDisplayAfterBodyOpeningTag($params)
    {
        return $this->shouldRenderInHook('afterbody') ? $this->renderBar() : '';
    }
}
