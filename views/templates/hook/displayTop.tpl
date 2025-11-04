{*
 * PromoBar - DisplayTop template (Carousel version)
 *
 * @author BeDOM - Solutions Web
 * @copyright 2025 BeDOM - Solutions Web
 * @license MIT
 *}

<div
  id="promobar"
  class="promobar promobar--controls-{$promobar_controls_color|escape:'html':'UTF-8'}{if $promobar_carousel_enabled} promobar--carousel promobar--{$promobar_carousel_transition|escape:'html':'UTF-8'}{/if}"
  data-cookie="{$promobar_cookie|escape:'html':'UTF-8'}"
  data-cookie-days="{$promobar_cookie_days|intval}"
  {if $promobar_carousel_enabled}
  data-carousel-enabled="1"
  data-carousel-transition="{$promobar_carousel_transition|escape:'html':'UTF-8'}"
  data-carousel-interval="{$promobar_carousel_interval|intval}"
  data-carousel-pause="{$promobar_carousel_pause|intval}"
  {/if}
  style="background:{$promobar_bg_color|escape:'html':'UTF-8'}"
  role="region"
  aria-label="{l s='Promo bar' mod='promobar'}"
>
  <div class="promobar__track">
    {foreach from=$promobar_messages item=msg name=msgLoop}
      <div
        class="promobar__slide{if $smarty.foreach.msgLoop.first} promobar__slide--active{/if} promobar--{$msg.animation|escape:'html':'UTF-8'}"
        data-slide-id="{$msg.id|intval}"
        style="
          color:{$msg.text_color|escape:'html':'UTF-8'};
          font-family:{if isset($promobar_font_stacks[$msg.font_family])}{$promobar_font_stacks[$msg.font_family]|escape:'html':'UTF-8'}{else}{$msg.font_family|escape:'html':'UTF-8'}{/if}
        "
      >
        <div class="promobar__inner">
          <div class="promobar__message">
            {* sanitized HTML injected client-side to satisfy validator *}
            <span class="promobar__marquee" data-html="{$msg.message_html|escape:'html':'UTF-8'}"></span>
            <noscript>{$msg.message_plain|escape:'html':'UTF-8'}</noscript>
          </div>

          {if $msg.cta_enabled && $msg.cta_text && $msg.cta_url}
            <a
              class="promobar__cta"
              href="{$msg.cta_url|escape:'html':'UTF-8'}"
              target="_blank"
              rel="noopener noreferrer"
              style="
                background:{$msg.cta_bg_color|escape:'html':'UTF-8'};
                color:{$msg.cta_text_color|escape:'html':'UTF-8'};
                border-color:{$msg.cta_border|escape:'html':'UTF-8'}
              "
            >
              {$msg.cta_text|escape:'html':'UTF-8'}
            </a>
          {/if}

          {if $msg.countdown_enabled && $msg.countdown_end}
            <div class="promobar__countdown" data-end="{$msg.countdown_end|escape:'html':'UTF-8'}" aria-live="polite">
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
        </div>
      </div>
    {/foreach}
  </div>

  {if $promobar_carousel_enabled && $promobar_carousel_arrows}
    <button class="promobar__arrow promobar__arrow--prev" type="button" aria-label="{l s='Previous message' mod='promobar'}">
      &#8249;
    </button>
    <button class="promobar__arrow promobar__arrow--next" type="button" aria-label="{l s='Next message' mod='promobar'}">
      &#8250;
    </button>
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
