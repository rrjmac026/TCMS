<script>
    // ── Tab switching ─────────────────────────────────────────────────────────
    function switchTab(name, btn) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        document.getElementById('tab-' + name).classList.add('active');
        btn.classList.add('active');
    }

    // ── Plan edit toggle ──────────────────────────────────────────────────────
    function toggleEdit(id) {
        document.getElementById('edit-form-' + id).classList.toggle('open');
    }

    // ── Modal helpers ─────────────────────────────────────────────────────────
    function openModal(id) {
        document.getElementById(id).classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('open');
        document.body.style.overflow = '';
    }

    // Close modals on backdrop click
    document.querySelectorAll('.modal-overlay').forEach(el => {
        el.addEventListener('click', function (e) {
            if (e.target === this) closeModal(this.id);
        });
    });

    // ── Populate edit discount modal ──────────────────────────────────────────
    function openEditDiscount(id) {
        const d = document.getElementById('disc-data-' + id).dataset;

        document.getElementById('edit-discount-form').action = d.updateUrl;
        document.getElementById('ed-name').value        = d.name;
        document.getElementById('ed-code').value        = d.code;
        document.getElementById('ed-type').value        = d.type;
        document.getElementById('ed-value').value       = d.value;
        document.getElementById('ed-tenant').value      = d.tenant;
        document.getElementById('ed-valid-from').value  = d.validFrom;
        document.getElementById('ed-valid-until').value = d.validUntil;
        document.getElementById('ed-max-uses').value    = d.maxUses;
        document.getElementById('ed-min-price').value   = d.minPrice;
        document.getElementById('ed-active').checked    = d.active === '1';

        const plans   = JSON.parse(d.plans   || '[]');
        const actions = JSON.parse(d.actions || '[]');

        document.querySelectorAll('.ed-plan-cb').forEach(cb => {
            cb.checked = plans.includes(cb.value);
        });
        document.querySelectorAll('.ed-action-cb').forEach(cb => {
            cb.checked = actions.includes(cb.value);
        });

        openModal('modal-edit-discount');
    }

    // ── Live discount code validator ──────────────────────────────────────────
    let validateTimeout;

    function liveValidate() {
        clearTimeout(validateTimeout);

        const code   = document.getElementById('apply-code-input').value.trim();
        const plan   = document.getElementById('apply-plan-select').value;
        const result = document.getElementById('validate-result');

        if (!code || !plan) {
            result.className = 'validate-result';
            return;
        }

        validateTimeout = setTimeout(() => {
            fetch('{{ route('superadmin.plans.discounts.validate') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ code, plan_slug: plan })
            })
            .then(r => r.json())
            .then(data => {
                if (data.valid) {
                    result.className = 'validate-result valid';
                    result.innerHTML =
                        `<i class="fas fa-check-circle mr-1"></i> ${data.message}<br>` +
                        `<span style="font-size:11px;">` +
                        `Original: ₱${Number(data.original_price).toFixed(2)} → ` +
                        `Discount: ₱${Number(data.discount_amount).toFixed(2)} → ` +
                        `<strong>Final: ₱${Number(data.final_price).toFixed(2)}</strong></span>`;
                } else {
                    result.className = 'validate-result invalid';
                    result.innerHTML = `<i class="fas fa-times-circle mr-1"></i> ${data.message}`;
                }
            })
            .catch(() => {
                result.className = 'validate-result invalid';
                result.innerHTML = '<i class="fas fa-exclamation-circle mr-1"></i> Validation failed.';
            });
        }, 400);
    }
</script>
