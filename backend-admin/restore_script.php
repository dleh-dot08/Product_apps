<?php
$sourceFile = 'c:/xampp/htdocs/gte-aqpa/Product_apps/backend-admin/resources/views/packaging/partials copy/step1.blade.php';
$targetFile = 'c:/xampp/htdocs/gte-aqpa/Product_apps/backend-admin/resources/views/packaging/partials/modals/_product-setup.blade.php';

$sourceContent = file_get_contents($sourceFile);
$targetContent = file_get_contents($targetFile);

// Extract the original JS script block from step1
// It's the only script tag at the bottom of the file
preg_match('/<script>\s*document\.addEventListener\(\'DOMContentLoaded\', function \(\) \{.*?<\/script>/s', $sourceContent, $matches);
if (isset($matches[0])) {
    $originalScript = $matches[0];
    
    // In target, find everything from the first <script> up to the end of the AUTO-FILL LOGIC
    // We'll replace it with the original script, but we also need to append the AUTO-FILL LOGIC
    
    $autoFill = <<<JS
    // --- AUTO-FILL LOGIC FROM DB ---
    document.addEventListener('DOMContentLoaded', function() {
        window.formatSONumber = function(value) {
            return new Intl.NumberFormat('id-ID', {
                maximumFractionDigits: 0
            }).format(Number(value || 0));
        };

        @if(\$hasJob)
            const initialSO = "{{ \$initialSO }}";
            const initialProduct = "{{ \$initialProduct }}";
            
            if (initialSO) {
                document.getElementById('searchSO').value = initialSO;
                document.getElementById('infoNoSO').textContent = initialSO;
                document.getElementById('infoCustomer').textContent = "{{ \$initialCustomer }}";
                document.getElementById('infoDeliveryDate').textContent = "{{ \$initialDelivery }}";
                document.getElementById('infoShipto').textContent = "{{ \$initialAddress }}";
                
                const dropdown = document.getElementById('itemDropdown');
                if (dropdown && initialProduct) {
                    dropdown.disabled = false;
                    dropdown.innerHTML = `<option value="\${initialProduct}" selected
                            data-item-no="\${initialProduct}" 
                            data-item-desc="{{ \$initialDesc }}"
                            data-qty-order="{{ \$initialQtyOrder }}" 
                            data-qty-remaining="{{ \$initialQtyRemaining }}"
                            data-short-text="\${initialProduct}"
                            data-full-text="\${initialProduct} - {{ \$initialDesc }}">
                            \${initialProduct} - {{ \$initialDesc }}
                        </option>`;
                    
                    document.getElementById('detailPartNo').innerText = initialProduct;
                    document.getElementById('detailDesc').innerText = "{{ \$initialDesc }}";
                    document.getElementById('detailQtyOrder').innerText = window.formatSONumber("{{ \$initialQtyOrder }}");
                    document.getElementById('detailQtyRemaining').innerText = window.formatSONumber("{{ \$initialQtyRemaining }}");
                }
            }
        @endif
    });
    // --- END AUTO-FILL LOGIC ---
</script>
JS;

    // Remove the closing </script> from originalScript, and append our autoFill
    $originalScript = str_replace('</script>', '', $originalScript);
    $finalScript = $originalScript . "\n" . $autoFill;

    // Now find the script block in target to replace. It starts with <script> and ends with <!-- Step 2:
    $targetContent = preg_replace('/<script>\s*document\.addEventListener\(\'DOMContentLoaded\', function \(\) \{.*?(?=<\/div>\s*<!-- Step 2:)/s', $finalScript . "\n", $targetContent);
    
    // One more thing, in the original script `formatSONumber` is defined inside DOMContentLoaded. Let's make it global by replacing `function formatSONumber` with `window.formatSONumber = function`
    $targetContent = str_replace('function formatSONumber(value) {', 'window.formatSONumber = function(value) {', $targetContent);

    // Also, my previous replace_file_content butchered the CSS block. I should restore the whole <style> block from source!
    preg_match('/<style>.*?<\/style>/s', $sourceContent, $styleMatches);
    if (isset($styleMatches[0])) {
        $originalStyle = $styleMatches[0];
        $targetContent = preg_replace('/<style>.*?<\/style>/s', $originalStyle, $targetContent);
    }
    
    file_put_contents($targetFile, $targetContent);
    echo "Restored script and style blocks from backup.";
} else {
    echo "Failed to extract script from source.";
}
