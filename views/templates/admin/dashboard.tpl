<!-- Stats Cards -->
<div class="wb-stats-grid">
    <!-- Active Blocks Card -->
    <div class="wb-stat-card-new wb-stat-blocks">
        <div class="wb-stat-card-top">
            <div class="wb-stat-icon wb-stat-icon-blue">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                </svg>
            </div>
            <div class="wb-stat-trend wb-trend-up">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                    <polyline points="17 6 23 6 23 12"></polyline>
                </svg>
                <span>+12%</span>
            </div>
        </div>
        <div class="wb-stat-card-bottom">
            <div class="wb-stat-number">{$stats.active_blocks|default:0}</div>
            <div class="wb-stat-label">{l s='Active Blocks' mod='wiseblock'}</div>
        </div>
        <div class="wb-stat-blob wb-blob-blue"></div>
    </div>

    <!-- Total Rules Card -->
    <div class="wb-stat-card-new wb-stat-rules">
        <div class="wb-stat-card-top">
            <div class="wb-stat-icon wb-stat-icon-purple">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="3"></circle>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                </svg>
            </div>
            <div class="wb-stat-trend wb-trend-up">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                    <polyline points="17 6 23 6 23 12"></polyline>
                </svg>
                <span>+8%</span>
            </div>
        </div>
        <div class="wb-stat-card-bottom">
            <div class="wb-stat-number">{$stats.total_rules|default:0}</div>
            <div class="wb-stat-label">{l s='Total Rules' mod='wiseblock'}</div>
        </div>
        <div class="wb-stat-blob wb-blob-purple"></div>
    </div>

    <!-- Active Hooks Card -->
    <div class="wb-stat-card-new wb-stat-hooks">
        <div class="wb-stat-card-top">
            <div class="wb-stat-icon wb-stat-icon-orange">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                </svg>
            </div>
            <div class="wb-stat-trend wb-trend-up wb-trend-orange">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                    <polyline points="17 6 23 6 23 12"></polyline>
                </svg>
                <span>+3</span>
            </div>
        </div>
        <div class="wb-stat-card-bottom">
            <div class="wb-stat-number">{$stats.total_hooks|default:0}</div>
            <div class="wb-stat-label">{l s='Active Hooks' mod='wiseblock'}</div>
        </div>
        <div class="wb-stat-blob wb-blob-orange"></div>
    </div>
</div>
