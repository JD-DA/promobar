{*
 * PromoBar - Configuration form template
 *
 * @author BeDOM - Solutions Web
 * @copyright 2025 BeDOM - Solutions Web
 * @license MIT
 *}

<style>
  .promobar-message-card {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px;
    margin-bottom: 15px;
    background: #f9f9f9;
    position: relative;
  }
  .promobar-message-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #ddd;
  }
  .promobar-message-title {
    font-weight: 600;
    font-size: 14px;
  }
  .promobar-remove-message {
    background: #d9534f;
    color: white;
    border: none;
    padding: 5px 12px;
    border-radius: 3px;
    cursor: pointer;
    font-size: 12px;
  }
  .promobar-remove-message:hover {
    background: #c9302c;
  }
  .promobar-add-message {
    background: #5cb85c;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 3px;
    cursor: pointer;
    font-size: 14px;
    margin-top: 10px;
  }
  .promobar-add-message:hover {
    background: #4cae4c;
  }
  .promobar-field-row {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
  }
  .promobar-field {
    flex: 1;
  }
  .promobar-field label {
    display: block;
    font-weight: 600;
    margin-bottom: 5px;
    font-size: 13px;
  }
  .promobar-field input[type="text"],
  .promobar-field input[type="date"],
  .promobar-field input[type="color"],
  .promobar-field select,
  .promobar-field textarea {
    width: 100%;
    padding: 6px 10px;
    border: 1px solid #ccc;
    border-radius: 3px;
    box-sizing: border-box;
  }
  .promobar-field-desc {
    font-size: 11px;
    color: #666;
    margin-top: 3px;
  }
  .promobar-fieldset {
    border: 1px solid #ddd;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 4px;
  }
  .promobar-fieldset legend {
    font-weight: 600;
    padding: 0 10px;
    font-size: 15px;
  }

  /* Language switcher */
  .promobar-lang-tabs {
    margin-bottom: 10px;
  }
  .promobar-lang-tab {
    display: inline-block;
    padding: 5px 12px;
    background: #e7e7e7;
    border: 1px solid #ccc;
    cursor: pointer;
    margin-right: 5px;
    border-radius: 3px 3px 0 0;
  }
  .promobar-lang-tab.active {
    background: #fff;
    border-bottom-color: #fff;
  }

  /* Language content - hide by default */
  textarea.promobar-lang-content,
  input.promobar-lang-content {
    display: none !important;
    width: 100%;
    box-sizing: border-box;
  }

  /* Show only active language content */
  textarea.promobar-lang-content.active {
    display: block !important;
  }
  input.promobar-lang-content.active {
    display: block !important;
  }
</style>

<form action="{$form_action|escape:'html':'UTF-8'}" method="post" class="defaultForm form-horizontal">

  <!-- Global Settings -->
  <fieldset class="promobar-fieldset">
    <legend><i class="icon-cogs"></i> {l s='Global Settings' mod='promobar'}</legend>

    <div class="promobar-field-row">
      <div class="promobar-field">
        <label>{l s='Display position' mod='promobar'}</label>
        <select name="{$CFG_POSITION|escape:'html':'UTF-8'}">
          <option value="afterbody" {if $position == 'afterbody'}selected{/if}>{l s='After opening <body> (recommended)' mod='promobar'}</option>
          <option value="top" {if $position == 'top'}selected{/if}>{l s='Top of page (displayTop)' mod='promobar'}</option>
        </select>
        <div class="promobar-field-desc">{l s='The banner will display on the chosen hook only to avoid duplicates.' mod='promobar'}</div>
      </div>
    </div>

    <div class="promobar-field-row">
      <div class="promobar-field">
        <label>{l s='Enable banner' mod='promobar'}</label>
        <select name="{$CFG_ENABLED|escape:'html':'UTF-8'}">
          <option value="1" {if $enabled}selected{/if}>{l s='Yes' mod='promobar'}</option>
          <option value="0" {if !$enabled}selected{/if}>{l s='No' mod='promobar'}</option>
        </select>
      </div>

      <div class="promobar-field">
        <label>{l s='Close button (do not show again)' mod='promobar'}</label>
        <select name="{$CFG_DISMISSIBLE|escape:'html':'UTF-8'}">
          <option value="1" {if $dismissible}selected{/if}>{l s='Yes' mod='promobar'}</option>
          <option value="0" {if !$dismissible}selected{/if}>{l s='No' mod='promobar'}</option>
        </select>
      </div>

      <div class="promobar-field">
        <label>{l s='Cookie lifetime' mod='promobar'}</label>
        <select name="{$CFG_COOKIE_DAYS|escape:'html':'UTF-8'}">
          {foreach from=$cookie_options item=opt}
            <option value="{$opt.value|intval}" {if $cookie_days == $opt.value}selected{/if}>{$opt.label|escape:'html':'UTF-8'}</option>
          {/foreach}
        </select>
      </div>
    </div>

    <div class="promobar-field-row">
      <div class="promobar-field">
        <label>{l s='Background color' mod='promobar'}</label>
        <input type="color" name="{$CFG_BG_COLOR|escape:'html':'UTF-8'}" value="{$bg_color|escape:'html':'UTF-8'}" />
        <div class="promobar-field-desc">{l s='This background color will be applied to all messages in the banner.' mod='promobar'}</div>
      </div>

      <div class="promobar-field">
        <label>{l s='Control buttons color' mod='promobar'}</label>
        <select name="{$CFG_CONTROLS_COLOR|escape:'html':'UTF-8'}">
          <option value="dark" {if $controls_color == 'dark'}selected{/if}>{l s='Dark (for light backgrounds)' mod='promobar'}</option>
          <option value="light" {if $controls_color == 'light'}selected{/if}>{l s='Light (for dark backgrounds)' mod='promobar'}</option>
        </select>
        <div class="promobar-field-desc">{l s='Choose dark buttons for light backgrounds, or light buttons for dark backgrounds.' mod='promobar'}</div>
      </div>
    </div>
  </fieldset>

  <!-- Carousel Settings -->
  <fieldset class="promobar-fieldset">
    <legend><i class="icon-refresh"></i> {l s='Carousel Settings' mod='promobar'}</legend>

    <div class="promobar-field-row">
      <div class="promobar-field">
        <label>{l s='Enable carousel mode' mod='promobar'}</label>
        <select name="{$CFG_CAROUSEL_ENABLED|escape:'html':'UTF-8'}">
          <option value="1" {if $carousel_enabled}selected{/if}>{l s='Yes' mod='promobar'}</option>
          <option value="0" {if !$carousel_enabled}selected{/if}>{l s='No' mod='promobar'}</option>
        </select>
        <div class="promobar-field-desc">{l s='Enable this to rotate through multiple messages. When disabled, only the first active message will be shown.' mod='promobar'}</div>
      </div>

      <div class="promobar-field">
        <label>{l s='Transition type' mod='promobar'}</label>
        <select name="{$CFG_CAROUSEL_TRANSITION|escape:'html':'UTF-8'}">
          <option value="fade" {if $carousel_transition == 'fade'}selected{/if}>{l s='Fade' mod='promobar'}</option>
          <option value="slide" {if $carousel_transition == 'slide'}selected{/if}>{l s='Slide' mod='promobar'}</option>
        </select>
      </div>

      <div class="promobar-field">
        <label>{l s='Interval (seconds)' mod='promobar'}</label>
        <input type="number" name="{$CFG_CAROUSEL_INTERVAL|escape:'html':'UTF-8'}" value="{$carousel_interval|intval}" min="1" max="60" />
      </div>
    </div>

    <div class="promobar-field-row">
      <div class="promobar-field">
        <label>{l s='Show prev/next arrows' mod='promobar'}</label>
        <select name="{$CFG_CAROUSEL_ARROWS|escape:'html':'UTF-8'}">
          <option value="1" {if $carousel_arrows}selected{/if}>{l s='Yes' mod='promobar'}</option>
          <option value="0" {if !$carousel_arrows}selected{/if}>{l s='No' mod='promobar'}</option>
        </select>
      </div>

      <div class="promobar-field">
        <label>{l s='Pause on hover' mod='promobar'}</label>
        <select name="{$CFG_CAROUSEL_PAUSE|escape:'html':'UTF-8'}">
          <option value="1" {if $carousel_pause}selected{/if}>{l s='Yes' mod='promobar'}</option>
          <option value="0" {if !$carousel_pause}selected{/if}>{l s='No' mod='promobar'}</option>
        </select>
      </div>
    </div>
  </fieldset>

  <!-- Messages -->
  <fieldset class="promobar-fieldset">
    <legend><i class="icon-comments"></i> {l s='Messages' mod='promobar'}</legend>

    <div id="promobar-messages-container">
      {foreach from=$messages item=msg name=msgLoop}
        {include file="./message_card.tpl" message=$msg index=$smarty.foreach.msgLoop.index}
      {/foreach}
    </div>

    <button type="button" class="promobar-add-message" id="promobar-add-message-btn">
      <i class="icon-plus"></i> {l s='Add Message' mod='promobar'}
    </button>
  </fieldset>

  <!-- Submit -->
  <div class="panel-footer">
    <button type="submit" name="submitPromobar" class="btn btn-default pull-right">
      <i class="process-icon-save"></i> {l s='Save' mod='promobar'}
    </button>
  </div>
</form>

<script type="text/javascript">
(function() {
  var messageIndex = {$messages|@count};
  var languages = {$languages|json_encode};
  var fontOptions = {$font_options|json_encode};
  var animationOptions = {$animation_options|json_encode};

  // Template for new message
  function getMessageTemplate(index) {
    var html = '<div class="promobar-message-card" data-message-index="' + index + '">';
    html += '<div class="promobar-message-header">';
    html += '<div class="promobar-message-title">' + '{l s='Message' mod='promobar'}' + ' #' + (index + 1) + '</div>';
    html += '<button type="button" class="promobar-remove-message" onclick="removeMessage(' + index + ')">{l s='Remove' mod='promobar'}</button>';
    html += '</div>';

    // Message text (multilingual)
    html += '<div class="promobar-field"><label>{l s='Message text' mod='promobar'}</label>';
    html += '<div class="promobar-lang-tabs">';
    languages.forEach(function(lang, i) {
      html += '<span class="promobar-lang-tab' + (i === 0 ? ' active' : '') + '" data-lang="' + lang.id_lang + '" onclick="switchLang(' + index + ', ' + lang.id_lang + ', event)">' + lang.iso_code.toUpperCase() + '</span>';
    });
    html += '</div>';
    languages.forEach(function(lang, i) {
      html += '<textarea class="promobar-lang-content' + (i === 0 ? ' active' : '') + '" data-lang="' + lang.id_lang + '" name="messages[' + index + '][message][' + lang.id_lang + ']" rows="3"></textarea>';
    });
    html += '<div class="promobar-field-desc">{l s='Tip: use **bold** for emphasis and [text](url) to insert a link.' mod='promobar'}</div></div>';

    // Text color, font and animation
    html += '<div class="promobar-field-row">';
    html += '<div class="promobar-field"><label>{l s='Text color' mod='promobar'}</label><input type="color" name="messages[' + index + '][text_color]" value="#ffffff" /></div>';
    html += '<div class="promobar-field"><label>{l s='Font' mod='promobar'}</label><select name="messages[' + index + '][font_family]">';
    fontOptions.forEach(function(opt) {
      html += '<option value="' + opt + '">' + opt + '</option>';
    });
    html += '</select></div>';
    html += '<div class="promobar-field"><label>{l s='Animation' mod='promobar'}</label><select name="messages[' + index + '][animation]">';
    animationOptions.forEach(function(opt) {
      html += '<option value="' + opt.value + '">' + opt.label + '</option>';
    });
    html += '</select></div>';
    html += '</div>';

    // Dates and countdown
    html += '<div class="promobar-field-row">';
    html += '<div class="promobar-field"><label>{l s='Start date (optional)' mod='promobar'}</label><input type="date" name="messages[' + index + '][start_date]" /></div>';
    html += '<div class="promobar-field"><label>{l s='End date (optional)' mod='promobar'}</label><input type="date" name="messages[' + index + '][end_date]" /></div>';
    html += '<div class="promobar-field"><label>{l s='Show countdown' mod='promobar'}</label><select name="messages[' + index + '][countdown]"><option value="0">{l s='No' mod='promobar'}</option><option value="1">{l s='Yes' mod='promobar'}</option></select></div>';
    html += '</div>';

    // CTA
    html += '<div class="promobar-field"><label>{l s='Show CTA button' mod='promobar'}</label><select name="messages[' + index + '][cta_enabled]"><option value="0">{l s='No' mod='promobar'}</option><option value="1">{l s='Yes' mod='promobar'}</option></select></div>';

    html += '<div class="promobar-field"><label>{l s='Button text (multilingual)' mod='promobar'}</label>';
    html += '<div class="promobar-lang-tabs">';
    languages.forEach(function(lang, i) {
      html += '<span class="promobar-lang-tab' + (i === 0 ? ' active' : '') + '" data-lang="' + lang.id_lang + '" onclick="switchLangCta(' + index + ', ' + lang.id_lang + ', event)">' + lang.iso_code.toUpperCase() + '</span>';
    });
    html += '</div>';
    languages.forEach(function(lang, i) {
      html += '<input type="text" class="promobar-lang-content' + (i === 0 ? ' active' : '') + '" data-lang="' + lang.id_lang + '" name="messages[' + index + '][cta_text][' + lang.id_lang + ']" placeholder="Learn more" />';
    });
    html += '</div>';

    html += '<div class="promobar-field"><label>{l s='Button URL' mod='promobar'}</label><input type="text" name="messages[' + index + '][cta_url]" placeholder="https://" /><div class="promobar-field-desc">{l s='Must start with http:// or https://' mod='promobar'}</div></div>';

    html += '<div class="promobar-field-row">';
    html += '<div class="promobar-field"><label>{l s='Button background' mod='promobar'}</label><input type="color" name="messages[' + index + '][cta_bg_color]" value="transparent" /></div>';
    html += '<div class="promobar-field"><label>{l s='Button text color' mod='promobar'}</label><input type="color" name="messages[' + index + '][cta_text_color]" value="#ffffff" /></div>';
    html += '<div class="promobar-field"><label>{l s='Button border' mod='promobar'}</label><input type="color" name="messages[' + index + '][cta_border]" value="#ffffff" /></div>';
    html += '</div>';

    html += '</div>';
    return html;
  }

  // Add message
  window.addMessage = function() {
    var container = document.getElementById('promobar-messages-container');
    var newCard = document.createElement('div');
    newCard.innerHTML = getMessageTemplate(messageIndex);
    container.appendChild(newCard.firstChild);
    messageIndex++;
  };

  // Remove message
  window.removeMessage = function(index) {
    var card = document.querySelector('.promobar-message-card[data-message-index="' + index + '"]');
    if (card && confirm('{l s='Are you sure you want to remove this message?' mod='promobar'}')) {
      card.remove();
    }
  };

  // Language tab switching - syncs ALL language fields across ALL message cards
  window.switchLang = function(msgIndex, langId, event) {
    // Find ALL message cards
    var cards = document.querySelectorAll('.promobar-message-card');

    cards.forEach(function(card) {
      // Switch ALL tabs in this card
      var tabs = card.querySelectorAll('.promobar-lang-tab[data-lang]');
      tabs.forEach(function(tab) {
        if (tab.getAttribute('data-lang') == langId) {
          tab.classList.add('active');
        } else {
          tab.classList.remove('active');
        }
      });

      // Switch ALL language content (textareas and inputs) in this card
      var contents = card.querySelectorAll('.promobar-lang-content');
      contents.forEach(function(content) {
        if (content.getAttribute('data-lang') == langId) {
          content.classList.add('active');
        } else {
          content.classList.remove('active');
        }
      });
    });
  };

  // Alias for CTA - now it does the same thing
  window.switchLangCta = window.switchLang;

  // Add message button
  document.getElementById('promobar-add-message-btn').addEventListener('click', addMessage);
})();
</script>
