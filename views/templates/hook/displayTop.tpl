{*
 * PromoBar - DisplayTop template
 *
 * @author BeDOM - Solutions Web
 *copyright 2025 BeDOM - Solutions Web
 * @license MIT
 *}

<div
  id="promobar"
  class="promobar promobar--{$promobar_animation|escape:'html':'UTF-8'}"
  style="
    background:{$promobar_bg|escape:'html':'UTF-8'};
    color:{$promobar_fg|escape:'html':'UTF-8'};
    font-family:{$promobar_font|escape:'html':'UTF-8'}
  "
  data-cookie="{$promobar_cookie|escape:'html':'UTF-8'}"
  data-cookie-days="{$promobar_cookie_days|intval}"
>
  <div class="promobar__inner" role="region" aria-label="{l s='Promo bar' mod='promobar'}">
    <div class="promobar__message">
      {* sanitized HTML injected client-side to satisfy validator *}
      <span class="promobar__marquee" data-html="{$promobar_message|escape:'html':'UTF-8'}"></span>
      <noscript>{$promobar_message_plain|escape:'html':'UTF-8'}</noscript>
    </div>

    {if $promobar_cta_enabled && $promobar_cta_text && $promobar_cta_url}
      <a
        class="promobar__cta"
        href="{$promobar_cta_url|escape:'html':'UTF-8'}"
        target="_blank"
        rel="noopener noreferrer"
        style="
          background:{$promobar_cta_bg|escape:'html':'UTF-8'};
          color:{$promobar_cta_text_col|escape:'html':'UTF-8'};
          border-color:{$promobar_cta_border|escape:'html':'UTF-8'}
        "
      >
        {$promobar_cta_text|escape:'html':'UTF-8'}
      </a>
    {/if}

    {if $promobar_countdown && $promobar_countdown_end}
      <div class="promobar__countdown" data-end="{$promobar_countdown_end|escape:'html':'UTF-8'}" aria-live="polite">
        <div class="promobar__cd-item">
          <span class="promobar__cd-number" data-cd="d">--</span>
          <span class="promobar__cd-label">j</span>
        </div>
        <div class="promobar__cd-item">
          <span class="promobar__cd-number" data-cd="h">--</span>
          <span class="promobar__cd-label">h</span>
        </div>
        <div class="promobar__cd-item">
          <span class="promobar__cd-number" data-cd="m">--</span>
          <span class="promobar__cd-label">m</span>
        </div>
        <div class="promobar__cd-item">
          <span class="promobar__cd-number" data-cd="s">--</span>
          <span class="promobar__cd-label">s</span>
        </div>
      </div>
    {/if}

    {if $promobar_dismissible}
      <button
        class="promobar__close"
        type="button"
        aria-label="{l s='Close the banner' mod='promobar'}"
      >
        &times;
      </button>
    {/if}
  </div>
</div>
