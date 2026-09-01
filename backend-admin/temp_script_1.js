
document.addEventListener('DOMContentLoaded', function() {
        function filterPenutupMaterial(statusSelectId, materialSelectId) {
            const statusSelect = document.getElementById(statusSelectId);
            const materialSelect = document.getElementById(materialSelectId);
            if (!statusSelect || !materialSelect) return;

            if (!materialSelect.originalOptions) {
                materialSelect.originalOptions = Array.from(materialSelect.options).filter(opt => opt.value !== "");
            }

            function updateMaterialOptions() {
                const status = statusSelect.value;
                const currentVal = materialSelect.value;
                
                materialSelect.innerHTML = '<option value="">Pilih Material...</option>';
                
                let targetType = null;
                if (status === 'Papan Full' || status === 'Papan Setengah') {
                    targetType = 'Papan';
                } else if (status === 'Tripleks' || status === 'Triplek') {
                    targetType = 'Triplek';
                }

                materialSelect.originalOptions.forEach(opt => {
                    if (targetType === null || opt.getAttribute('data-type') === targetType) {
                        materialSelect.appendChild(opt.cloneNode(true));
                    }
                });

                if (Array.from(materialSelect.options).some(o => o.value === currentVal)) {
                    materialSelect.value = currentVal;
                }
                
                if (status === 'Tanpa Penutup') {
                    materialSelect.value = "";
                    materialSelect.disabled = true;
                } else {
                    materialSelect.disabled = false;
                }
            }

            statusSelect.addEventListener('change', updateMaterialOptions);
            updateMaterialOptions(); // trigger on load
        }

        // Jalankan untuk Area Bawah dan Area Atas
        filterPenutupMaterial('s2_ptb_status', 's2_ptb_material');
        filterPenutupMaterial('s2_pta_status', 's2_pta_material');

        // Validate Dimensions
        const validateDimensions = () => {
            const p = parseFloat(document.getElementById('s2_p')?.value) || 0;
            const l = parseFloat(document.getElementById('s2_l')?.value) || 0;
            const t = parseFloat(document.getElementById('s2_t')?.value) || 0;
            
            // Just simple validation or other dimension checks if needed in the future
        };

        ['s2_p', 's2_l', 's2_t', 's2_inner_carton_box'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', validateDimensions);
                el.addEventListener('change', validateDimensions);
            }
        });
        
        document.querySelectorAll('input[name="additional_mat"]').forEach(el => {
            el.addEventListener('change', validateDimensions);
        });

    });
