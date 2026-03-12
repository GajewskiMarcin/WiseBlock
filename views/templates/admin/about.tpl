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
            <h3 style="margin-bottom: 10px; font-size: 20px; font-weight: 600; color: var(--wb-gray-900);">{l s='Support the Project' mod='wiseblock'}</h3>
            <p style="color: var(--wb-gray-600); margin-bottom: 28px; font-size: 16px;">
                {l s='If you find WiseBlock useful, consider supporting development' mod='wiseblock'}
            </p>
            <div style="display: flex; flex-wrap: wrap; gap: 12px; justify-content: center; align-items: center;">
                <a href="{$buymeacoffee_url|escape:'html':'UTF-8'}" target="_blank" class="wb-btn-outlined" style="background: #eab308; color: white; border-color: #eab308;">
                    <i class="icon-coffee"></i> {l s='Buy me a coffee' mod='wiseblock'}
                </a>
                <a href="{$github_repo_url|escape:'html':'UTF-8'}" target="_blank" class="wb-btn-outlined" style="background: white;">
                    <i class="icon-star"></i> {l s='Star on GitHub' mod='wiseblock'}
                </a>
            </div>
        </div>
    </div>

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
