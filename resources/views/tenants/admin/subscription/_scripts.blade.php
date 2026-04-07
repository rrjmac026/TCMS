<script>
/**
 * Subscription upgrade + renewal JS.
 *
 * KEY RULES:
 *  - selectPlan()      → opens upgrade modal. NO plan change.
 *  - checkPromoCode()  → validates code, updates price. NO plan change.
 *  - confirmUpgrade()  → the ONLY function that triggers an upgrade request.
 *  - selectRenewal()   → opens renewal modal with duration picker. NO change.
 *  - confirmRenewal()  → submits a renewal request (pending superadmin approval).
 */

// ── Shared plan data from Blade ───────────────────────────────────────────────
const planSlugs    = @json($planSlugs);
const currentPlan  = '{{ $currentPlan }}';
const currentIndex = planSlugs.indexOf(currentPlan);

// Plan base prices keyed by slug — used for pro-rating renewal prices
const PLAN_BASE_PRICES = {};
@foreach($plans as $plan)
PLAN_BASE_PRICES['{{ $plan->slug }}'] = {{ (float) $plan->price }};
PLAN_BASE_PRICES['{{ $plan->slug }}__days'] = {{ (int) $plan->duration_days }};
@endforeach

// Current tenant expiry (ISO string or empty)
const TENANT_EXPIRES_AT = '{{ $tenant->expires_at ? $tenant->expires_at->toIso8601String() : '' }}';

// ── Upgrade modal state ───────────────────────────────────────────────────────
let selectedPlanKey  = null;
let selectedPlanBase = 0;
let selectedPlanAuto = 0;
let validatedCode    = null;
let activeCodeFinal  = 0;
let codeTimer        = null;

// ── Open upgrade modal ────────────────────────────────────────────────────────
function selectPlan(slug, name, basePrice, autoPrice) {
    const newIndex = planSlugs.indexOf(slug);
    if (newIndex <= currentIndex) return;

    selectedPlanKey  = slug;
    selectedPlanBase = parseFloat(basePrice) || 0;
    selectedPlanAuto = parseFloat(autoPrice)  || selectedPlanBase;
    validatedCode    = null;
    activeCodeFinal  = 0;

    document.getElementById('planName').textContent        = name;
    document.getElementById('successPlanName').textContent = name;
    document.getElementById('modal-discount-code').value   = '';
    document.getElementById('modal-discount-result').style.display = 'none';

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

    updatePriceSummary(selectedPlanBase, selectedPlanAuto, null);

    document.getElementById('confirmView').style.display = 'block';
    document.getElementById('successView').style.display  = 'none';
    document.getElementById('upgradeModal').style.display = 'flex';
}

// ── Upgrade promo code ────────────────────────────────────────────────────────
function scheduleCodeCheck() {
    clearTimeout(codeTimer);
    const code = document.getElementById('modal-discount-code').value.trim();
    if (!code) {
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
            updatePriceSummary(selectedPlanBase, activeCodeFinal, 'Promo code (' + code + ')');
        } else {
            result.style.cssText = styleBox('red');
            result.innerHTML = '<i class="fas fa-times-circle" style="margin-right:5px;"></i>' + data.message;
            validatedCode   = null;
            activeCodeFinal = 0;
            updatePriceSummary(selectedPlanBase, selectedPlanAuto, null);
        }
    });
}

// ── Upgrade price summary ─────────────────────────────────────────────────────
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
        document.getElementById('summary-discount-label').textContent = 'Automatic discount';
        document.getElementById('summary-discount').textContent = '−₱' + fmt(saved);
        row.style.display = 'flex';
    } else {
        row.style.display = 'none';
    }
}

// ── Confirm upgrade ───────────────────────────────────────────────────────────
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

function closeUpgradeModal(event) {
    document.getElementById('upgradeModal').style.display = 'none';
}

// ══════════════════════════════════════════════════════════════════════════════
// RENEWAL MODAL — with duration picker
// ══════════════════════════════════════════════════════════════════════════════

let renewalPlanKey         = null;
let renewalPlanBasePPD     = 0;      // price-per-day for the selected plan
let renewalSelectedDays    = 0;      // total days chosen by tenant
let renewalValidCode       = null;
let renewalActiveCodeFinal = 0;
let renewalCodeTimer       = null;

// Quick-select chips: label → days
const RENEWAL_CHIPS = [
    { label: '1 month',  days: 30  },
    { label: '3 months', days: 90  },
    { label: '6 months', days: 180 },
    { label: '1 year',   days: 365 },
    { label: '2 years',  days: 730 },
];

function selectRenewal(slug, name, basePrice, autoPrice) {
    renewalPlanKey         = slug;
    renewalValidCode       = null;
    renewalActiveCodeFinal = 0;

    // Price-per-day (used for pro-rating)
    const planDays = PLAN_BASE_PRICES[slug + '__days'] || 30;
    renewalPlanBasePPD = basePrice > 0 ? basePrice / planDays : 0;

    document.getElementById('renewalPlanName').textContent        = name;
    document.getElementById('renewalSuccessPlanName').textContent = name;
    document.getElementById('renewal-discount-code').value        = '';
    document.getElementById('renewal-discount-result').style.display = 'none';
    document.getElementById('renewal-pending-warning').style.display  = 'none';
    document.getElementById('renewal-auto-discount-notice').style.display = 'none';

    // Build chip buttons
    buildRenewalChips();

    // Default to the plan's standard duration
    setRenewalDays(planDays, true);

    document.getElementById('renewalConfirmView').style.display  = 'block';
    document.getElementById('renewalSuccessView').style.display  = 'none';
    document.getElementById('renewalModal').style.display        = 'flex';
}

// ── Build quick-select chips ──────────────────────────────────────────────────
function buildRenewalChips() {
    const container = document.getElementById('renewal-duration-chips');
    container.innerHTML = '';
    RENEWAL_CHIPS.forEach(chip => {
        const btn = document.createElement('button');
        btn.type      = 'button';
        btn.className = 'renewal-chip';
        btn.id        = 'chip-' + chip.days;
        btn.textContent = chip.label;
        btn.onclick   = () => setRenewalDays(chip.days);
        container.appendChild(btn);
    });
}

// ── Set duration (days) and refresh everything ────────────────────────────────
function setRenewalDays(days, silent) {
    renewalSelectedDays = days;

    // Highlight the matching chip (if any)
    document.querySelectorAll('.renewal-chip').forEach(c => c.classList.remove('active'));
    const chip = document.getElementById('chip-' + days);
    if (chip) chip.classList.add('active');

    // Clear the custom input if a chip was clicked
    if (!silent) {
        document.getElementById('renewal-custom-amount').value = '';
        document.getElementById('renewal-custom-unit').value   = 'months';
    }

    document.getElementById('renewal-days-label').textContent = days;
    refreshRenewalPriceSummary();
    refreshRenewalDates();
}

// ── Custom duration input ─────────────────────────────────────────────────────
function onCustomDurationInput() {
    const amount = parseInt(document.getElementById('renewal-custom-amount').value) || 0;
    const unit   = document.getElementById('renewal-custom-unit').value;

    let days = 0;
    if (unit === 'months') days = Math.round(amount * 30.44);
    else if (unit === 'years')  days = amount * 365;
    else                        days = amount; // raw days

    if (days < 1) return;

    // Deactivate all chips
    document.querySelectorAll('.renewal-chip').forEach(c => c.classList.remove('active'));

    renewalSelectedDays = days;
    document.getElementById('renewal-days-label').textContent = days;
    refreshRenewalPriceSummary();
    refreshRenewalDates();
}

// ── Compute pro-rated price for selected days ─────────────────────────────────
function renewalBasePrice() {
    return Math.round(renewalPlanBasePPD * renewalSelectedDays * 100) / 100;
}

// ── Refresh price summary ─────────────────────────────────────────────────────
function refreshRenewalPriceSummary() {
    const base  = renewalBasePrice();
    const final = renewalActiveCodeFinal > 0 ? renewalActiveCodeFinal : base;

    const note = document.getElementById('renewal-duration-note');
    if (note) note.textContent = '(' + renewalSelectedDays + ' days)';

    updateRenewalPriceSummary(base, final,
        renewalValidCode ? 'Promo code (' + renewalValidCode + ')' : null
    );
}

// ── Refresh "extending from → to" dates ──────────────────────────────────────
function refreshRenewalDates() {
    const fromEl = document.getElementById('renewal-from-date');
    const toEl   = document.getElementById('renewal-to-date');
    if (!fromEl || !toEl) return;

    const now     = new Date();
    const expiry  = TENANT_EXPIRES_AT ? new Date(TENANT_EXPIRES_AT) : null;
    const baseDate = (expiry && expiry > now) ? expiry : now;

    const toDate = new Date(baseDate);
    toDate.setDate(toDate.getDate() + renewalSelectedDays);

    fromEl.textContent = baseDate.toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' });
    toEl.textContent   = toDate.toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' });
}

// ── Renewal promo code ────────────────────────────────────────────────────────
function scheduleRenewalCodeCheck() {
    clearTimeout(renewalCodeTimer);
    const code = document.getElementById('renewal-discount-code').value.trim();
    if (!code) {
        renewalValidCode       = null;
        renewalActiveCodeFinal = 0;
        document.getElementById('renewal-discount-result').style.display = 'none';
        refreshRenewalPriceSummary();
        return;
    }
    renewalCodeTimer = setTimeout(checkRenewalPromoCode, 500);
}

function checkRenewalPromoCode() {
    const code   = document.getElementById('renewal-discount-code').value.trim();
    const result = document.getElementById('renewal-discount-result');
    if (!code || !renewalPlanKey) return;

    fetch('{{ route("admin.subscription.validate-code") }}', {
        method  : 'POST',
        headers : { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body    : JSON.stringify({ code, plan_slug: renewalPlanKey }),
    })
    .then(r => r.json())
    .then(data => {
        result.style.display = 'block';
        if (data.valid) {
            result.style.cssText = styleBox('green');
            result.innerHTML = '<i class="fas fa-check-circle" style="margin-right:5px;"></i>' +
                               'Code valid — saves ' + data.formatted_value;

            // Apply discount to the pro-rated base
            const base = renewalBasePrice();
            const ratio = data.discount_amount / data.original_price;
            const saved = Math.min(data.discount_amount, base * ratio);
            renewalActiveCodeFinal = Math.max(0, base - saved);
            renewalValidCode       = code;
        } else {
            result.style.cssText = styleBox('red');
            result.innerHTML = '<i class="fas fa-times-circle" style="margin-right:5px;"></i>' + data.message;
            renewalValidCode       = null;
            renewalActiveCodeFinal = 0;
        }
        refreshRenewalPriceSummary();
    });
}

function updateRenewalPriceSummary(base, final, discountLabel) {
    document.getElementById('renewal-summary-original').textContent = '₱' + fmt(base);
    document.getElementById('renewal-summary-final').textContent    = '₱' + fmt(final);

    const row   = document.getElementById('renewal-summary-discount-row');
    const saved = base - final;

    if (discountLabel && saved > 0) {
        document.getElementById('renewal-summary-discount-label').textContent = discountLabel;
        document.getElementById('renewal-summary-discount').textContent       = '−₱' + fmt(saved);
        row.style.display = 'flex';
    } else if (!discountLabel && final < base) {
        document.getElementById('renewal-summary-discount-label').textContent = 'Automatic discount';
        document.getElementById('renewal-summary-discount').textContent       = '−₱' + fmt(saved);
        row.style.display = 'flex';
    } else {
        row.style.display = 'none';
    }
}

// ── Submit renewal request ────────────────────────────────────────────────────
function confirmRenewal() {
    if (!renewalPlanKey)       return;
    if (!renewalSelectedDays)  { alert('Please choose a duration.'); return; }

    const btn = document.getElementById('renewalConfirmBtn');
    btn.disabled  = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting…';

    fetch('{{ route("admin.renewal.request") }}', {
        method  : 'POST',
        headers : { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body    : JSON.stringify({
            plan_slug     : renewalPlanKey,
            duration_days : renewalSelectedDays,
            discount_code : renewalValidCode ?? '',
        }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('renewalConfirmView').style.display = 'none';
            document.getElementById('renewalSuccessView').style.display  = 'block';
        } else {
            if (data.message && data.message.toLowerCase().includes('pending')) {
                document.getElementById('renewal-pending-warning').style.display = 'block';
            } else {
                alert(data.message || 'Submission failed. Please try again.');
            }
            btn.disabled  = false;
            btn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Renewal Request';
        }
    })
    .catch(() => {
        alert('A network error occurred. Please try again.');
        btn.disabled  = false;
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Renewal Request';
    });
}

function closeRenewalModal(event) {
    if (event && event.target !== document.getElementById('renewalModal')) return;
    document.getElementById('renewalModal').style.display = 'none';
}

// ── Shared utilities ──────────────────────────────────────────────────────────
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