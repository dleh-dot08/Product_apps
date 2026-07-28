<?php
$file = 'c:/xampp/htdocs/gte-aqpa/Product_apps/backend-admin/resources/views/packaging/partials/modals/_product-setup.blade.php';
$content = file_get_contents($file);

$startMarker = '    function escapeSOHtml(value) {';
$endMarker = "    });\n});\n    // --- AUTO-FILL LOGIC FROM DB ---";

$posStart = strpos($content, $startMarker);
$posEnd = strpos($content, $endMarker);

if ($posStart !== false && $posEnd !== false) {
    $before = substr($content, 0, $posStart);
    $after = substr($content, $posEnd + strlen($endMarker));
    
    $correctBlock = <<<JAVASCRIPT
    function escapeSOHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    window.formatSONumber = function(value) {
        return new Intl.NumberFormat('id-ID', {
            maximumFractionDigits: 0
        }).format(Number(value || 0));
    };

    const btnCopyPartNo = document.getElementById('btnCopyPartNo');

    btnCopyPartNo?.addEventListener('click', async function () {
        const value = document.getElementById('detailPartNo')?.textContent?.trim();

        if (!value || value === '-') return;

        try {
            await navigator.clipboard.writeText(value);

            const originalHtml = this.innerHTML;
            this.innerHTML = '<i class="fa-solid fa-check text-success"></i>';

            setTimeout(() => {
                this.innerHTML = originalHtml;
            }, 1200);
        } catch (error) {
            console.error('Gagal menyalin Part Number:', error);
        }
    });

    const btnUseSelectedItem = document.getElementById('btnUseSelectedItem');

    btnUseSelectedItem?.addEventListener('click', function () {
        const dropdown = document.getElementById('itemDropdown');
        const selectedItem = dropdown?.value;

        if (!selectedItem) {
            alert('Silakan pilih barang terlebih dahulu.');
            return;
        }

        document.dispatchEvent(new CustomEvent('salesOrderItemSelected', {
            detail: {
                itemId: selectedItem,
                partNumber: document.getElementById('detailPartNo')?.textContent?.trim(),
                description: document.getElementById('detailDesc')?.textContent?.trim(),
                qtyOrder: document.getElementById('detailQtyOrder')?.textContent?.trim(),
                qtyRemaining: document.getElementById('detailQtyRemaining')?.textContent?.trim()
            }
        }));

        const modalElement = this.closest('.modal');

        if (modalElement && window.bootstrap) {
            bootstrap.Modal.getOrCreateInstance(modalElement).hide();
        }
    });
});
    // --- AUTO-FILL LOGIC FROM DB ---
JAVASCRIPT;

    file_put_contents($file, $before . $correctBlock . $after);
    echo "Successfully repaired script block.";
} else {
    echo "Could not find markers.";
}
