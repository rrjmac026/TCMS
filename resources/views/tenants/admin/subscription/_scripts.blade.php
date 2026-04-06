<script>
/**
 * Subscription upgrade JS
 *
 * KEY RULES mirrored from the backend:
 *  - selectPlan()      → opens modal, loads auto-discount price. NO plan change.
 *  - checkPromoCode()  → validates a code, updates price summary.      NO plan change.
 *  - confirmUpgrade()  → the ONLY function that sends an upgrade request.
 */

const planSlugs    = @json($planSlugs);
const currentPlan  = '{{ $currentPlan }}';
const currentIndex = planSlugs.indexOf(currentPlan);

// State for the currently open modal
let selectedPlanKey  = null;   // e.g. 'standard'
let selectedPlanBase = 0;      // original price from DB
let selectedPlanAuto = 0;      // price after auto-discount (= base if none)
let validatedCode    = null;   // promo code confirmed valid by server
let activeCodeFinal  = 0;      // final price when a promo code is active

let codeTimer = null;

// ── Open modal ────────────────────────────────────────────────────────────────

/**
 * @param {string} slug      plan slug e.g. 'standard'
 * @param {string} name      display name
 * @param {number} basePrice original price (PHP)
 * @param {number} autoPrice price after any active automatic discount
 */
function selectPlan(slug, name, basePrice, autoPrice) {
    const newIndex = planSlugs.indexOf(slug);
    if (newIndex <= currentIndex) return; // no downgrade

    selectedPlanKey  = slug;
    selectedPlanBase = parseFloat(basePrice) || 0;
    selectedPlanAuto = parseFloat(autoPrice)  || selectedPlanBase;
    validatedCode    = null;
    activeCodeFinal  = 0;

    // Reset modal UI
    document.getElementById('planName').textContent        = name;
    document.getElementById('successPlanName').textContent = name;
    document.getElementById('modal-discount-code').value   = '';
    document.getElementById('modal-discount-result').style.display = 'none';

    // Show auto-discount notice if applicable
    const hasAutoDiscount = selectedPlanAuto < selectedPlanBase;
    const notice = document.getElementById('auto-discount-notice');
    if (hasAutoDiscount) {
        const saved = selectedPlanBase - selectedPlanAuto;
        document.getElementById('auto-discount-text').textContent =
            'Automatic discount applied — you save ₱' + fmt(saved) +
            ' (₱' + fmt(selectedPlanBase) + ' → ₱' + fmt(selectedPlanAuto) + ')';
        notice.style.display = 'block';
    } else {
        notice.style.display = 'none';
    }

    // Set initial price summary (use auto-discounted price as baseline)
    updatePriceSummary(selectedPlanBase, selectedPlanAuto, null);

    // Show modal
    document.getElementById('confirmView').style.display = 'block';
    document.getElementById('successView').style.display  = 'none';
    document.getElementById('upgradeModal').style.display = 'flex';
}

// ── Promo code validation ─────────────────────────────────────────────────────

function scheduleCodeCheck() {
    clearTimeout(codeTimer);
    const code = document.getElementById('modal-discount-code').value.trim();
    if (!code) {
        // Code cleared — revert to auto-discount price
        validatedCode   = null;
        activeCodeFinal = 0;
        document.getElementById('modal-discount-result').style.display = 'none';
        updatePriceSummary(selectedPlanBase, selectedPlanAuto, null);
        return;
    }
    codeTimer = setTimeout(checkPromoCode, 500);
}

function checkPromoCode() {
    const code   = document.getElementById('modal-discount-code').value.trim();
    const result = document.getElementById('modal-discount-result');

    if (!code || !selectedPlanKey) return;

    fetch('{{ route("admin.subscription.validate-code") }}', {
        method  : 'POST',
        headers : { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body    : JSON.stringify({ code, plan_slug: selectedPlanKey }),
    })
    .then(r => r.json())
    .then(data => {
        result.style.display = 'block';
        if (data.valid) {
            result.style.cssText = styleBox('green');
            result.innerHTML = '<i class="fas fa-check-circle" style="margin-right:5px;"></i>' +
                               'Code valid — saves ' + data.formatted_value;
            validatedCode   = code;
            activeCodeFinal = parseFloat(data.final_price);

            // Promo code overrides the auto-discount
            updatePriceSummary(selectedPlanBase, activeCodeFinal, 'Promo code (' + code + ')');
        } else {
            result.style.cssText = styleBox('red');
            result.innerHTML = '<i class="fas fa-times-circle" style="margin-right:5px;"></i>' + data.message;
            validatedCode   = null;
            activeCodeFinal = 0;
            // Revert to auto-discount price
            updatePriceSummary(selectedPlanBase, selectedPlanAuto, null);
        }
    });
}

// ── Price summary helper ──────────────────────────────────────────────────────

/**
 * @param {number}      base         original plan price
 * @param {number}      final        price after discount (= base if none)
 * @param {string|null} discountLabel label for the discount row, or null to hide it
 */
function updatePriceSummary(base, final, discountLabel) {
    document.getElementById('summary-original').textContent = '₱' + fmt(base);
    document.getElementById('summary-final').textContent    = '₱' + fmt(final);

    const row   = document.getElementById('summary-discount-row');
    const saved = base - final;

    if (discountLabel && saved > 0) {
        document.getElementById('summary-discount-label').textContent = discountLabel;
        document.getElementById('summary-discount').textContent = '−₱' + fmt(saved);
        row.style.display = 'flex';
    } else if (!discountLabel && final < base) {
        // Auto-discount — show without a specific label
        document.getElementById('summary-discount-label').textContent = 'Automatic discount';
        document.getElementById('summary-discount').textContent = '−₱' + fmt(saved);
        row.style.display = 'flex';
    } else {
        row.style.display = 'none';
    }
}

// ── Confirm upgrade ───────────────────────────────────────────────────────────

/**
 * THE ONLY function that triggers a real plan change.
 * Sends the chosen plan slug + optional promo code to the server.
 * The server applies discounts to the recorded price only.
 */
function confirmUpgrade() {
    if (!selectedPlanKey) return;

    const btn = document.getElementById('confirmBtn');
    btn.disabled  = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Upgrading…';

    fetch('{{ route("admin.subscription.upgrade") }}', {
        method  : 'POST',
        headers : { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body    : JSON.stringify({
            subscription  : selectedPlanKey,
            discount_code : validatedCode ?? '',
        }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('confirmView').style.display = 'none';
            document.getElementById('successView').style.display  = 'block';
        } else {
            alert(data.message || 'Upgrade failed. Please try again.');
            btn.disabled  = false;
            btn.innerHTML = '<i class="fas fa-check"></i> Yes, Upgrade Now';
        }
    })
    .catch(() => {
        alert('An error occurred. Please try again.');
        btn.disabled  = false;
        btn.innerHTML = '<i class="fas fa-check"></i> Yes, Upgrade Now';
    });
}

// ── Utilities ─────────────────────────────────────────────────────────────────

function closeUpgradeModal(event) {
    document.getElementById('upgradeModal').style.display = 'none';
}

function fmt(n) {
    return parseFloat(n).toLocaleString('en-PH', { minimumFractionDigits: 2 });
}

function styleBox(color) {
    const map = {
        green : 'display:block;margin-top:8px;border-radius:8px;padding:9px 12px;font-size:13px;font-weight:600;background:rgba(22,163,74,.08);border:1.5px solid rgba(22,163,74,.3);color:#16a34a;',
        red   : 'display:block;margin-top:8px;border-radius:8px;padding:9px 12px;font-size:13px;font-weight:600;background:rgba(206,17,38,.08);border:1.5px solid rgba(206,17,38,.3);color:#CE1126;',
    };
    return map[color] || '';
}
</script>