{*
 * PromoBar - Message card template (for existing messages)
 *
 * @author BeDOM - Solutions Web
 * @copyright 2025 BeDOM - Solutions Web
 * @license MIT
 *}

<div class="promobar-message-card" data-message-index="{$index|intval}">
  <div class="promobar-message-header">
    <div class="promobar-message-title">{l s='Message' mod='promobar'} #{$index+1}</div>
    <button type="button" class="promobar-remove-message" onclick="removeMessage({$index|intval})">
      {l s='Remove' mod='promobar'}
    </button>
  </div>

  <input type="hidden" name="messages[{$index|intval}][id]" value="{$message.id|intval}" />

  <!-- Message text (multilingual) -->
  <div class="promobar-field">
    <label>{l s='Message text' mod='promobar'}</label>
    <div class="promobar-lang-tabs">
      {foreach from=$languages item=lang name=langLoop}
        <span class="promobar-lang-tab{if $smarty.foreach.langLoop.first} active{/if}"
              data-lang="{$lang.id_lang|intval}"
              onclick="switchLang({$index|intval}, {$lang.id_lang|intval}, event)">
          {$lang.iso_code|upper|escape:'html':'UTF-8'}
        </span>
      {/foreach}
    </div>
    {foreach from=$languages item=lang name=langLoop}
      <textarea class="promobar-lang-content{if $smarty.foreach.langLoop.first} active{/if}"
                data-lang="{$lang.id_lang|intval}"
                name="messages[{$index|intval}][message][{$lang.id_lang|intval}]"
                rows="3">{if isset($message.message[$lang.id_lang])}{$message.message[$lang.id_lang]|escape:'html':'UTF-8'}{/if}</textarea>
    {/foreach}
    <div class="promobar-field-desc">{l s='Tip: use **bold** for emphasis and [text](url) to insert a link.' mod='promobar'}</div>
  </div>

  <!-- Text color, font and animation -->
  <div class="promobar-field-row">
    <div class="promobar-field">
      <label>{l s='Text color' mod='promobar'}</label>
      <input type="color" name="messages[{$index|intval}][text_color]" value="{$message.text_color|escape:'html':'UTF-8'}" />
    </div>
    <div class="promobar-field">
      <label>{l s='Font' mod='promobar'}</label>
      <select name="messages[{$index|intval}][font_family]">
        {foreach from=$font_options item=font}
          <option value="{$font|escape:'html':'UTF-8'}" {if $message.font_family == $font}selected{/if}>
            {$font|escape:'html':'UTF-8'}
          </option>
        {/foreach}
      </select>
    </div>
    <div class="promobar-field">
      <label>{l s='Animation' mod='promobar'}</label>
      <select name="messages[{$index|intval}][animation]">
        {foreach from=$animation_options item=opt}
          <option value="{$opt.value|escape:'html':'UTF-8'}" {if $message.animation == $opt.value}selected{/if}>
            {$opt.label|escape:'html':'UTF-8'}
          </option>
        {/foreach}
      </select>
    </div>
  </div>

  <!-- Dates and countdown -->
  <div class="promobar-field-row">
    <div class="promobar-field">
      <label>{l s='Start date (optional)' mod='promobar'}</label>
      <input type="date" name="messages[{$index|intval}][start_date]" value="{$message.start_date|escape:'html':'UTF-8'}" />
    </div>
    <div class="promobar-field">
      <label>{l s='End date (optional)' mod='promobar'}</label>
      <input type="date" name="messages[{$index|intval}][end_date]" value="{$message.end_date|escape:'html':'UTF-8'}" />
    </div>
    <div class="promobar-field">
      <label>{l s='Show countdown' mod='promobar'}</label>
      <select name="messages[{$index|intval}][countdown]">
        <option value="0" {if !$message.countdown}selected{/if}>{l s='No' mod='promobar'}</option>
        <option value="1" {if $message.countdown}selected{/if}>{l s='Yes' mod='promobar'}</option>
      </select>
    </div>
  </div>

  <!-- CTA -->
  <div class="promobar-field">
    <label>{l s='Show CTA button' mod='promobar'}</label>
    <select name="messages[{$index|intval}][cta_enabled]">
      <option value="0" {if !$message.cta_enabled}selected{/if}>{l s='No' mod='promobar'}</option>
      <option value="1" {if $message.cta_enabled}selected{/if}>{l s='Yes' mod='promobar'}</option>
    </select>
  </div>

  <div class="promobar-field">
    <label>{l s='Button text (multilingual)' mod='promobar'}</label>
    <div class="promobar-lang-tabs">
      {foreach from=$languages item=lang name=langLoop}
        <span class="promobar-lang-tab{if $smarty.foreach.langLoop.first} active{/if}"
              data-lang="{$lang.id_lang|intval}"
              onclick="switchLangCta({$index|intval}, {$lang.id_lang|intval}, event)">
          {$lang.iso_code|upper|escape:'html':'UTF-8'}
        </span>
      {/foreach}
    </div>
    {foreach from=$languages item=lang name=langLoop}
      <input type="text"
             class="promobar-lang-content{if $smarty.foreach.langLoop.first} active{/if}"
             data-lang="{$lang.id_lang|intval}"
             name="messages[{$index|intval}][cta_text][{$lang.id_lang|intval}]"
             value="{if isset($message.cta_text[$lang.id_lang])}{$message.cta_text[$lang.id_lang]|escape:'html':'UTF-8'}{/if}"
             placeholder="{l s='Learn more' mod='promobar'}" />
    {/foreach}
  </div>

  <div class="promobar-field">
    <label>{l s='Button URL' mod='promobar'}</label>
    <input type="text" name="messages[{$index|intval}][cta_url]" value="{$message.cta_url|escape:'html':'UTF-8'}" placeholder="https://" />
    <div class="promobar-field-desc">{l s='Must start with http:// or https://' mod='promobar'}</div>
  </div>

  <div class="promobar-field-row">
    <div class="promobar-field">
      <label>{l s='Button background' mod='promobar'}</label>
      <input type="color" name="messages[{$index|intval}][cta_bg_color]" value="{$message.cta_bg_color|escape:'html':'UTF-8'}" />
    </div>
    <div class="promobar-field">
      <label>{l s='Button text color' mod='promobar'}</label>
      <input type="color" name="messages[{$index|intval}][cta_text_color]" value="{$message.cta_text_color|escape:'html':'UTF-8'}" />
    </div>
    <div class="promobar-field">
      <label>{l s='Button border' mod='promobar'}</label>
      <input type="color" name="messages[{$index|intval}][cta_border]" value="{$message.cta_border|escape:'html':'UTF-8'}" />
    </div>
  </div>
</div>
