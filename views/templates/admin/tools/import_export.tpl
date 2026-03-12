<!-- Tools Grid -->
<div class="wb-tools-grid">
    <!-- Export Configuration -->
    <div class="wb-tool-card">
        <div class="wb-tool-card-header">
            <div class="wb-tool-icon-row">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                <h3 class="wb-tool-title">{l s='Export Configuration' mod='wiseblock'}</h3>
            </div>
            <p class="wb-tool-subtitle">{l s='Download your blocks and rules as JSON' mod='wiseblock'}</p>
        </div>
        <div class="wb-tool-card-body">
            <form method="post">
                <button type="submit" name="submitWiseBlockExport" value="1" class="wb-btn-outlined">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    {l s='Export as JSON' mod='wiseblock'}
                </button>
            </form>
        </div>
    </div>

    <!-- Cache Management -->
    <div class="wb-tool-card">
        <div class="wb-tool-card-header">
            <div class="wb-tool-icon-row">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                </svg>
                <h3 class="wb-tool-title">{l s='Cache Management' mod='wiseblock'}</h3>
            </div>
            <p class="wb-tool-subtitle">{l s='Clear module cache to see latest changes' mod='wiseblock'}</p>
        </div>
        <div class="wb-tool-card-body">
            {if isset($cache_last_cleared) && $cache_last_cleared}
            <div class="wb-cache-info">
                <span class="wb-cache-label">{l s='Last cleared:' mod='wiseblock'}</span>
                <span class="wb-cache-time">{$cache_last_cleared}</span>
            </div>
            {/if}
            <form method="post">
                <button type="submit" name="submitWiseBlockClearCache" value="1" class="wb-btn-outlined">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                    {l s='Clear Cache' mod='wiseblock'}
                </button>
            </form>
        </div>
    </div>

    <!-- Import Configuration -->
    <div class="wb-tool-card">
        <div class="wb-tool-card-header">
            <div class="wb-tool-icon-row">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
                <h3 class="wb-tool-title">{l s='Import Configuration' mod='wiseblock'}</h3>
            </div>
            <p class="wb-tool-subtitle">{l s='Upload a JSON file to import blocks' mod='wiseblock'}</p>
        </div>
        <div class="wb-tool-card-body">
            <form method="post" enctype="multipart/form-data" id="wb-import-form">
                <div class="wb-dropzone" id="wb-dropzone">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                    <p class="wb-dropzone-text">{l s='Drop your JSON file here' mod='wiseblock'}</p>
                    <p class="wb-dropzone-subtext">{l s='or click to browse' mod='wiseblock'}</p>
                    <input type="file" name="wiseblock_import_file" accept="application/json,.json" id="wb-import-file" class="wb-dropzone-input">
                </div>
                <div style="margin-top: 12px;">
                    <button type="submit" name="submitWiseBlockImport" value="1" class="wb-btn-outlined">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                        {l s='Import File' mod='wiseblock'}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- CreativeElements Integration -->
    <div class="wb-tool-card">
        <div class="wb-tool-card-header">
            <div class="wb-tool-icon-row">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="16 18 22 12 16 6"></polyline>
                    <polyline points="8 6 2 12 8 18"></polyline>
                </svg>
                <h3 class="wb-tool-title">{l s='CreativeElements Integration' mod='wiseblock'}</h3>
            </div>
            <p class="wb-tool-subtitle">{l s='Use blocks in your page builder' mod='wiseblock'}</p>
        </div>
        <div class="wb-tool-card-body">
            <div class="wb-ce-accordion open" id="wb-ce-accordion">
                <button type="button" class="wb-ce-accordion-toggle" onclick="toggleCEAccordion()">
                    <span>{l s='How to integrate' mod='wiseblock'}</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="wb-ce-chevron">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div class="wb-ce-accordion-content" id="wb-ce-content">
                    <div class="wb-ce-step">
                        <strong>{l s='Step 1:' mod='wiseblock'}</strong>
                        <p>{l s='Create a new HTML widget in CreativeElements' mod='wiseblock'}</p>
                    </div>
                    <div class="wb-ce-step">
                        <strong>{l s='Step 2:' mod='wiseblock'}</strong>
                        <p>{l s='Copy and paste this code:' mod='wiseblock'}</p>
                        <div class="wb-ce-code">
                            <code>{literal}{hook h='displayWiseBlockPromo'}{/literal}</code>
                        </div>
                    </div>
                    <div class="wb-ce-step">
                        <strong>{l s='Step 3:' mod='wiseblock'}</strong>
                        <p>{l s='Save and publish your page' mod='wiseblock'}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{include file="./placeholders.tpl"}

<!-- Custom CSS Section -->
<div class="wb-tool-card wb-tool-card-standalone">
    <div class="wb-tool-card-header">
        <div class="wb-tool-icon-row">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
            </svg>
            <h3 class="wb-tool-title">{l s='Custom CSS' mod='wiseblock'}</h3>
        </div>
        <p class="wb-tool-subtitle">{l s='Add global styles for your content blocks' mod='wiseblock'}</p>
    </div>
    <div class="wb-tool-card-body">
        <form method="post" id="wb-custom-css-form">
            <textarea name="wiseblock_custom_css" class="wb-custom-css-editor" placeholder="/* {l s='Add your custom CSS here' mod='wiseblock'} */
.wiseblock-content {ldelim}
    /* {l s='Your styles' mod='wiseblock'} */
{rdelim}">{if isset($custom_css)}{$custom_css|escape:'html':'UTF-8'}{/if}</textarea>
            <div class="wb-css-actions">
                <button type="submit" name="submitWiseBlockCustomCSS" value="1" class="wb-btn-save">
                    {l s='Save CSS' mod='wiseblock'}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleCEAccordion() {
    var accordion = document.getElementById('wb-ce-accordion');
    accordion.classList.toggle('open');
}

// Dropzone functionality
document.addEventListener('DOMContentLoaded', function() {
    var dropzone = document.getElementById('wb-dropzone');
    var fileInput = document.getElementById('wb-import-file');

    if (dropzone && fileInput) {
        dropzone.addEventListener('click', function() {
            fileInput.click();
        });

        dropzone.addEventListener('dragover', function(e) {
            e.preventDefault();
            dropzone.classList.add('wb-dropzone-active');
        });

        dropzone.addEventListener('dragleave', function() {
            dropzone.classList.remove('wb-dropzone-active');
        });

        dropzone.addEventListener('drop', function(e) {
            e.preventDefault();
            dropzone.classList.remove('wb-dropzone-active');
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                updateDropzoneText(e.dataTransfer.files[0].name);
            }
        });

        fileInput.addEventListener('change', function() {
            if (this.files.length) {
                updateDropzoneText(this.files[0].name);
            }
        });

        function updateDropzoneText(filename) {
            var textEl = dropzone.querySelector('.wb-dropzone-text');
            if (textEl) {
                textEl.innerHTML = '{l s='Selected:' mod='wiseblock'} <strong>' + filename + '</strong>';
            }
        }
    }
});
</script>
