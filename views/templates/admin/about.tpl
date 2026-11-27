{*
* WiseBlock - About & Help
*}

<div class="wb-dashboard-container">
    {* Hero Section *}
    <div class="wb-tool-card" style="margin-bottom: 24px;">
        <div style="text-align: center; padding: 48px 24px;">
            <div style="width: 80px; height: 80px; background: var(--wb-gradient-primary); border-radius: 20px; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center;">
                <i class="icon-bolt" style="font-size: 40px; color: white;"></i>
            </div>
            <h1 style="font-size: 32px; font-weight: 700; margin-bottom: 12px; color: var(--wb-gray-900);">WiseBlock</h1>
            <span style="display: inline-block; padding: 6px 14px; background: var(--wb-gradient-primary); color: white; border-radius: 20px; font-size: 14px; font-weight: 600; margin-bottom: 16px;">v{$module_version|escape:'html':'UTF-8'}</span>
            <p style="font-size: 16px; color: var(--wb-gray-500); max-width: 600px; margin: 0 auto;">
                {l s='The most powerful content block management module for PrestaShop' mod='wiseblock'}
            </p>
        </div>
    </div>

    {* Features Grid *}
    <div class="wb-tool-card" style="margin-bottom: 24px;">
        <div class="wb-tool-card-header">
            <div style="display: flex; align-items: center; gap: 10px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                </svg>
                <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: var(--wb-gray-900); line-height: 1;">{l s='Features' mod='wiseblock'}</h3>
            </div>
        </div>
        <div class="wb-tool-card-body">
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
                {* Feature 1 *}
                <div style="padding: 20px; background: var(--wb-gray-50); border-radius: 12px; display: flex; gap: 16px;">
                    <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #dbeafe 0%, #e9d5ff 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="icon-bullseye" style="font-size: 22px; color: #2563eb;"></i>
                    </div>
                    <div>
                        <h4 style="margin: 0 0 6px 0; font-size: 15px; font-weight: 600; color: var(--wb-gray-900);">{l s='Advanced Targeting' mod='wiseblock'}</h4>
                        <p style="margin: 0; color: var(--wb-gray-500); font-size: 14px; line-height: 1.5;">{l s='Target content by category, tag, customer group, country, and cart value' mod='wiseblock'}</p>
                    </div>
                </div>
                {* Feature 2 *}
                <div style="padding: 20px; background: var(--wb-gray-50); border-radius: 12px; display: flex; gap: 16px;">
                    <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #dbeafe 0%, #e9d5ff 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="icon-bolt" style="font-size: 22px; color: #2563eb;"></i>
                    </div>
                    <div>
                        <h4 style="margin: 0 0 6px 0; font-size: 15px; font-weight: 600; color: var(--wb-gray-900);">{l s='Dynamic Hooks' mod='wiseblock'}</h4>
                        <p style="margin: 0; color: var(--wb-gray-500); font-size: 14px; line-height: 1.5;">{l s='Create custom hooks to display content anywhere in your theme' mod='wiseblock'}</p>
                    </div>
                </div>
                {* Feature 3 *}
                <div style="padding: 20px; background: var(--wb-gray-50); border-radius: 12px; display: flex; gap: 16px;">
                    <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #dbeafe 0%, #e9d5ff 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="icon-calendar" style="font-size: 22px; color: #2563eb;"></i>
                    </div>
                    <div>
                        <h4 style="margin: 0 0 6px 0; font-size: 15px; font-weight: 600; color: var(--wb-gray-900);">{l s='Content Scheduling' mod='wiseblock'}</h4>
                        <p style="margin: 0; color: var(--wb-gray-500); font-size: 14px; line-height: 1.5;">{l s='Schedule blocks to appear during specific date ranges' mod='wiseblock'}</p>
                    </div>
                </div>
                {* Feature 4 *}
                <div style="padding: 20px; background: var(--wb-gray-50); border-radius: 12px; display: flex; gap: 16px;">
                    <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #dbeafe 0%, #e9d5ff 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="icon-globe" style="font-size: 22px; color: #2563eb;"></i>
                    </div>
                    <div>
                        <h4 style="margin: 0 0 6px 0; font-size: 15px; font-weight: 600; color: var(--wb-gray-900);">{l s='Multi-language Support' mod='wiseblock'}</h4>
                        <p style="margin: 0; color: var(--wb-gray-500); font-size: 14px; line-height: 1.5;">{l s='Create content in multiple languages for international stores' mod='wiseblock'}</p>
                    </div>
                </div>
                {* Feature 5 *}
                <div style="padding: 20px; background: var(--wb-gray-50); border-radius: 12px; display: flex; gap: 16px;">
                    <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #dbeafe 0%, #e9d5ff 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="icon-flask" style="font-size: 22px; color: #2563eb;"></i>
                    </div>
                    <div>
                        <h4 style="margin: 0 0 6px 0; font-size: 15px; font-weight: 600; color: var(--wb-gray-900);">{l s='Rule Testing' mod='wiseblock'}</h4>
                        <p style="margin: 0; color: var(--wb-gray-500); font-size: 14px; line-height: 1.5;">{l s='Test your targeting rules before going live' mod='wiseblock'}</p>
                    </div>
                </div>
                {* Feature 6 *}
                <div style="padding: 20px; background: var(--wb-gray-50); border-radius: 12px; display: flex; gap: 16px;">
                    <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #dbeafe 0%, #e9d5ff 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="icon-dashboard" style="font-size: 22px; color: #2563eb;"></i>
                    </div>
                    <div>
                        <h4 style="margin: 0 0 6px 0; font-size: 15px; font-weight: 600; color: var(--wb-gray-900);">{l s='High Performance' mod='wiseblock'}</h4>
                        <p style="margin: 0; color: var(--wb-gray-500); font-size: 14px; line-height: 1.5;">{l s='Optimized caching and efficient database queries' mod='wiseblock'}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {* Getting Help Section *}
    <div class="wb-tool-card" style="margin-bottom: 24px;">
        <div class="wb-tool-card-header">
            <div style="display: flex; align-items: center; gap: 10px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: var(--wb-gray-900); line-height: 1;">{l s='Getting Help' mod='wiseblock'}</h3>
            </div>
        </div>
        <div class="wb-tool-card-body">
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                {* Documentation *}
                <div style="text-align: center; padding: 32px 20px; background: var(--wb-gray-50); border-radius: 12px;">
                    <div style="width: 56px; height: 56px; background: #dbeafe; border-radius: 50%; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center;">
                        <i class="icon-book" style="font-size: 26px; color: #2563eb;"></i>
                    </div>
                    <h4 style="margin-bottom: 8px; font-size: 16px; font-weight: 600; color: var(--wb-gray-900);">{l s='Documentation' mod='wiseblock'}</h4>
                    <p style="color: var(--wb-gray-500); font-size: 14px; margin-bottom: 20px; line-height: 1.5;">{l s='User guides and tutorials on GitHub Wiki' mod='wiseblock'}</p>
                    <a href="{$github_wiki_url|escape:'html':'UTF-8'}" target="_blank" class="wb-btn-outlined wb-btn-full">
                        <i class="icon-book"></i> {l s='Read Wiki' mod='wiseblock'}
                    </a>
                </div>
                {* GitHub Issues *}
                <div style="text-align: center; padding: 32px 20px; background: var(--wb-gray-50); border-radius: 12px;">
                    <div style="width: 56px; height: 56px; background: #f3e8ff; border-radius: 50%; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center;">
                        <i class="icon-github" style="font-size: 26px; color: #9333ea;"></i>
                    </div>
                    <h4 style="margin-bottom: 8px; font-size: 16px; font-weight: 600; color: var(--wb-gray-900);">{l s='GitHub Issues' mod='wiseblock'}</h4>
                    <p style="color: var(--wb-gray-500); font-size: 14px; margin-bottom: 20px; line-height: 1.5;">{l s='Report bugs or request features' mod='wiseblock'}</p>
                    <a href="{$github_issues_url|escape:'html':'UTF-8'}" target="_blank" class="wb-btn-outlined wb-btn-full">
                        <i class="icon-github"></i> {l s='Open Issue' mod='wiseblock'}
                    </a>
                </div>
                {* Discussions *}
                <div style="text-align: center; padding: 32px 20px; background: var(--wb-gray-50); border-radius: 12px;">
                    <div style="width: 56px; height: 56px; background: #d1fae5; border-radius: 50%; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center;">
                        <i class="icon-comments" style="font-size: 26px; color: #059669;"></i>
                    </div>
                    <h4 style="margin-bottom: 8px; font-size: 16px; font-weight: 600; color: var(--wb-gray-900);">{l s='Discussions' mod='wiseblock'}</h4>
                    <p style="color: var(--wb-gray-500); font-size: 14px; margin-bottom: 20px; line-height: 1.5;">{l s='Join the community on GitHub' mod='wiseblock'}</p>
                    <a href="{$github_discussions_url|escape:'html':'UTF-8'}" target="_blank" class="wb-btn-outlined wb-btn-full">
                        <i class="icon-comments"></i> {l s='Join Discussion' mod='wiseblock'}
                    </a>
                </div>
            </div>
        </div>
    </div>

    {* Support the Project *}
    <div class="wb-tool-card" style="margin-bottom: 24px; background: linear-gradient(135deg, #eff6ff 0%, #f3e8ff 100%);">
        <div style="text-align: center; padding: 48px 24px;">
            <h3 style="margin-bottom: 10px; font-size: 20px; font-weight: 600; color: var(--wb-gray-900); justify-content: center;">{l s='Support the Project' mod='wiseblock'}</h3>
            <p style="color: var(--wb-gray-600); margin-bottom: 28px; font-size: 16px;">
                {l s='If you find WiseBlock useful, consider supporting development' mod='wiseblock'}
            </p>
            <div style="display: flex; flex-wrap: wrap; gap: 12px; justify-content: center; align-items: center;">
                <a href="{$kofi_url|escape:'html':'UTF-8'}" target="_blank" class="wb-btn-outlined" style="background: #eab308; color: white; border-color: #eab308;">
                    <i class="icon-coffee"></i> {l s='Support on Ko-fi' mod='wiseblock'}
                </a>
                <a href="{$github_repo_url|escape:'html':'UTF-8'}" target="_blank" class="wb-btn-outlined" style="background: white;">
                    <i class="icon-star"></i> {l s='Star on GitHub' mod='wiseblock'}
                </a>
            </div>
        </div>
    </div>

    {* Quince Project: blok w wyglądzie nagłówka strony projektu
       (quinceproject.github.io): pastelowa poświata, skośne smugi, znak
       w gradiencie krojem Instrument Sans, kanciasty przycisk i pasek faktów. *}
    <style>
        @font-face { font-family: 'WB Instrument Sans'; font-weight: 400 600; font-display: swap;
            src: url('{$module_dir|escape:'html':'UTF-8'}views/fonts/instrument-sans-latin-ext.woff2') format('woff2');
            unicode-range: U+0100-02BA, U+02BD-02C5, U+02C7-02CC, U+02CE-02D7, U+02DD-02FF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20C4, U+2113, U+2C60-2C7F, U+A720-A7FF; }
        @font-face { font-family: 'WB Instrument Sans'; font-weight: 400 600; font-display: swap;
            src: url('{$module_dir|escape:'html':'UTF-8'}views/fonts/instrument-sans-latin.woff2') format('woff2');
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD; }
        .wb-quince { position: relative; overflow: hidden; margin-bottom: 24px; border-radius: 12px; background: #f3f5f8; color: #161b28;
            font-family: 'WB Instrument Sans', Arial, sans-serif; -webkit-font-smoothing: antialiased; }
        {* Arkusz panelu WiseBlock wymusza Inter na każdym elemencie, więc krój bloku też musi być wymuszony. *}
        .wb-quince, .wb-quince * { font-family: 'WB Instrument Sans', Arial, sans-serif !important; }
        .wb-quince-wash { position: absolute; inset: 0; pointer-events: none; background:
            radial-gradient(60% 70% at 8% 10%, rgba(255, 197, 107, .55), transparent 70%),
            radial-gradient(45% 60% at 62% 0%, rgba(184, 174, 246, .55), transparent 70%),
            radial-gradient(50% 70% at 100% 20%, rgba(143, 211, 247, .6), transparent 70%); }
        .wb-quince-beam { position: absolute; top: -10%; width: 150px; height: 80%; background: rgba(255, 255, 255, .35); transform: skewY(-18deg); pointer-events: none; }
        .wb-quince-beam.b1 { left: 6%; }
        .wb-quince-beam.b2 { left: 24%; width: 90px; background: rgba(255, 255, 255, .25); }
        .wb-quince-beam.b3 { left: 58%; width: 120px; }
        .wb-quince-beam.b4 { left: 78%; width: 60px; background: rgba(255, 255, 255, .3); }
        .wb-quince-in { position: relative; padding: 44px 56px 0; }
        .wb-quince .wb-quince-lead { margin: 0; font-size: 22px; line-height: 1.45; color: #161b28; }
        {* Wyściółka od dołu: gradient wypełnia tylko pudełko napisu, a ogonek „j” wychodził poza nie. *}
        .wb-quince .wb-quince-word { display: inline-block; margin: 10px 0 -.22em; padding: 0 .04em .22em 0; font-size: 96px; line-height: .9; letter-spacing: -.06em; font-weight: 500;
            background: linear-gradient(90deg, #ffc56b 0%, #b8aef6 55%, #8fd3f7 100%); -webkit-background-clip: text; background-clip: text; color: transparent; }
        .wb-quince .wb-quince-text { margin: 28px 0 0; max-width: 36rem; font-size: 19px; line-height: 1.5; color: #161b28; }
        .wb-quince-cta { margin-top: 36px; }
        .wb-quince a.wb-quince-btn { display: inline-flex; align-items: center; gap: 10px; height: 54px; padding: 0 28px; font-weight: 500; font-size: 16px; text-decoration: none; background: #1f2533; color: #fff; transition: background .15s; }
        .wb-quince a.wb-quince-btn:hover, .wb-quince a.wb-quince-btn:focus { background: #0b1730; color: #fff; text-decoration: none; }
        .wb-quince-btn svg { width: 18px; height: 18px; fill: currentColor; }
        .wb-quince-facts { display: grid; grid-template-columns: repeat(3, 1fr); gap: 32px; margin-top: 56px; padding-block: 24px 32px; border-top: 1px solid rgba(22, 27, 40, .15); }
        .wb-quince-facts b { display: block; color: #161b28; font-weight: 500; font-size: 17px; }
        @media (max-width: 767px) {
            .wb-quince-in { padding: 32px 24px 0; }
            .wb-quince .wb-quince-word { font-size: 56px; }
            .wb-quince-facts { grid-template-columns: 1fr; gap: 12px; }
        }
    </style>
    <section class="wb-quince">
        <div class="wb-quince-wash" aria-hidden="true"></div>
        <i class="wb-quince-beam b1" aria-hidden="true"></i><i class="wb-quince-beam b2" aria-hidden="true"></i><i class="wb-quince-beam b3" aria-hidden="true"></i><i class="wb-quince-beam b4" aria-hidden="true"></i>
        <div class="wb-quince-in">
            <p class="wb-quince-lead">{l s='Discover the theme and engine for PrestaShop' mod='wiseblock'}</p>
            <p class="wb-quince-word">Quince Project</p>
            <p class="wb-quince-text">{l s='A set of over 20 modules and a theme that let you freely manage the look and content of your shop and support back-office processes.' mod='wiseblock'}</p>
            <div class="wb-quince-cta">
                <a class="wb-quince-btn" href="{$quince_url|escape:'html':'UTF-8'}" target="_blank" rel="noopener">
                    <svg viewBox="0 0 16 16" aria-hidden="true"><path d="M8 0c4.42 0 8 3.58 8 8a8.013 8.013 0 0 1-5.45 7.59c-.4.08-.55-.17-.55-.38 0-.27.01-1.13.01-2.2 0-.75-.25-1.23-.54-1.48 1.78-.2 3.65-.88 3.65-3.95 0-.88-.31-1.59-.82-2.15.08-.2.36-1.02-.08-2.12 0 0-.67-.22-2.2.82-.64-.18-1.32-.27-2-.27-.68 0-1.36.09-2 .27-1.53-1.03-2.2-.82-2.2-.82-.44 1.1-.16 1.92-.08 2.12-.51.56-.82 1.28-.82 2.15 0 3.06 1.86 3.75 3.64 3.95-.23.2-.44.55-.51 1.07-.46.21-1.61.55-2.33-.66-.15-.24-.6-.83-1.23-.82-.67.01-.27.38.01.53.34.19.73.9.82 1.13.16.45.68 1.31 2.69.94 0 .67.01 1.3.01 1.49 0 .21-.15.45-.55.38A7.995 7.995 0 0 1 0 8c0-4.42 3.58-8 8-8Z"/></svg>
                    {l s='Download for free from GitHub' mod='wiseblock'}
                </a>
            </div>
            <div class="wb-quince-facts">
                <div><b>PrestaShop 9.2</b></div>
                <div><b>{l s='PHP 8.1 or newer' mod='wiseblock'}</b></div>
                <div><b>{l s='OSL-3.0 licence' mod='wiseblock'}</b></div>
            </div>
        </div>
    </section>

    {* System Information *}
    <div class="wb-tool-card wb-ce-accordion">
        <button type="button" class="wb-ce-accordion-toggle" onclick="jQuery(this).closest('.wb-ce-accordion').toggleClass('open');">
            <span style="display: flex; align-items: center; gap: 8px;">
                <i class="icon-info-circle"></i> {l s='System Information' mod='wiseblock'}
            </span>
            <svg class="wb-ce-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
        </button>
        <div class="wb-ce-accordion-content">
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;">
                <div style="padding: 16px; background: var(--wb-gray-50); border-radius: 10px; display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="icon-check-circle" style="color: #059669; font-size: 18px;"></i>
                        <strong style="font-size: 14px; color: var(--wb-gray-700);">{l s='PrestaShop Version' mod='wiseblock'}</strong>
                    </div>
                    <span style="font-family: monospace; font-size: 13px; background: var(--wb-gray-200); padding: 4px 10px; border-radius: 6px; color: var(--wb-gray-700);">{$ps_version|escape:'html':'UTF-8'}</span>
                </div>
                <div style="padding: 16px; background: var(--wb-gray-50); border-radius: 10px; display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="icon-check-circle" style="color: #059669; font-size: 18px;"></i>
                        <strong style="font-size: 14px; color: var(--wb-gray-700);">{l s='PHP Version' mod='wiseblock'}</strong>
                    </div>
                    <span style="font-family: monospace; font-size: 13px; background: var(--wb-gray-200); padding: 4px 10px; border-radius: 6px; color: var(--wb-gray-700);">{$php_os|escape:'html':'UTF-8'} {$php_version|escape:'html':'UTF-8'}</span>
                </div>
                <div style="padding: 16px; background: var(--wb-gray-50); border-radius: 10px; display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="icon-check-circle" style="color: #059669; font-size: 18px;"></i>
                        <strong style="font-size: 14px; color: var(--wb-gray-700);">{l s='Module Version' mod='wiseblock'}</strong>
                    </div>
                    <span style="font-family: monospace; font-size: 13px; background: var(--wb-gray-200); padding: 4px 10px; border-radius: 6px; color: var(--wb-gray-700);">{$module_version|escape:'html':'UTF-8'}</span>
                </div>
                <div style="padding: 16px; background: var(--wb-gray-50); border-radius: 10px; display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="icon-check-circle" style="color: #059669; font-size: 18px;"></i>
                        <strong style="font-size: 14px; color: var(--wb-gray-700);">{l s='Module Author' mod='wiseblock'}</strong>
                    </div>
                    <span style="font-family: monospace; font-size: 13px; background: var(--wb-gray-200); padding: 4px 10px; border-radius: 6px; color: var(--wb-gray-700);">{$module_author|escape:'html':'UTF-8'}</span>
                </div>
            </div>
        </div>
    </div>
</div>
