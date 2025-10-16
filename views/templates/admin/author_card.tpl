{*
 * PromoBar - Author card template
 *
 * @author BeDOM - Solutions Web
 * @copyright 2025 BeDOM - Solutions Web
 * @license MIT
 *}

<style>
  #promobar-authorcard {
    margin-top: 20px;
    }
  .pb-card {
    display:flex; gap:16px; align-items:center; padding:16px;
    background:#fff; border:1px solid #e3e5e8; border-radius:8px;
    box-shadow:0 1px 2px rgba(0,0,0,.04);
  }
  .pb-card__logo {
  width:56px; height:56px; flex:0 0 auto; border-radius:8px; overflow:hidden; background:#f6f7f9;
  display:flex; align-items:center; justify-content:center;
}
.pb-card__logo a {
  display:flex;
  align-items:center;
  justify-content:center;
  width:100%;
  height:100%;
}
.pb-card__logo a:hover,
.pb-card__logo a:focus {
  opacity:.9;
}


.pb-card__logo img {
  display:block;
  max-width:100%;
  max-height:100%;
  width:auto;
  height:auto;
  object-fit:contain; 
}

  .pb-card__body {
    flex:1 1 auto;
     min-width:0;
     }
  .pb-card__title {
    margin:0; 
    font-weight:600; 
    font-size:15px;
    }
  .pb-card__meta {
    margin:.25rem 0 0; 
    color:#666; 
    font-size:12px;
    }
  .pb-card__links {
    display:flex; 
    flex-wrap:wrap; 
    gap:8px; 
    margin-left:auto;
    }
  .pb-btn {
    display:inline-block; 
    padding:6px 10px; 
    border:1px solid #d6d9de; 
    border-radius:9999px;
    text-decoration:none; 
    font-size:12px; 
    font-weight:600; 
    color:#222; 
    background:#fff;
  }
  .pb-btn:hover, .pb-btn:focus {
    text-decoration:none; 
    border-color:#b9bec6;
    }
  .pb-badges {
    display:flex; 
    align-items:center; 
    gap:8px; 
    margin-top:4px; 
    color:#777; 
    font-size:11px;
    }
  @media (max-width: 740px) {
     .pb-card {
        flex-direction:column; 
        align-items:flex-start;
        } 
    .pb-card__links {
        margin-left:0;
        } 
    }
</style>

<div id="promobar-authorcard">
  <div class="pb-card">
    <div class="pb-card__logo">
    <a href="{$pb_links[0].url|escape:'html':'UTF-8'}"
        target="_blank"
        rel="nofollow noopener noreferrer"
        title="{$pb_author_name|escape:'html':'UTF-8'}">
        <img src="{$pb_author_logo|escape:'html':'UTF-8'}"
            alt="{$pb_author_name|escape:'html':'UTF-8'}"
            width="500"
            height="341" />
    </a>
    </div>


    <div class="pb-card__body">
      <p class="pb-card__title">
        {$pb_title|escape:'html':'UTF-8'} <strong>{$pb_author_name|escape:'html':'UTF-8'}</strong>
      </p>
      <p class="pb-card__meta">
        {$pb_tagline|escape:'html':'UTF-8'}
      </p>
      <div class="pb-badges">
        <span>{$pb_module_name|escape:'html':'UTF-8'}</span>
        <span>•</span>
        <span>v{$pb_module_version|escape:'html':'UTF-8'}</span>
      </div>
    </div>

    {if isset($pb_links) && $pb_links|@count}
      <div class="pb-card__links">
        {foreach from=$pb_links item=lnk}
          <a class="pb-btn"
             href="{$lnk.url|escape:'html':'UTF-8'}"
             target="_blank"
             rel="nofollow noopener noreferrer">{$lnk.label|escape:'html':'UTF-8'}</a>
        {/foreach}
      </div>
    {/if}
  </div>
</div>
