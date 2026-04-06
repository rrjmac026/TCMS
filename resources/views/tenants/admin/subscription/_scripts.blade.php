<script>
let selectedPlanKey  = null;
let selectedPlanBase = 0;
let validatedCode    = null;   // only set when server confirmed valid

const planSlugs    = @json($planSlugs);
const currentPlan  = '{{ $currentPlan }}';
const currentIndex = planSlugs.indexOf(currentPlan);

function selectPlan(key, name, price, duration, basePrice) {
    const newIndex = planSlugs.indexOf(key);
    if (newIndex <= currentIndex) return;

    selectedPlanKey  = key;
    selectedPlanBase = parseFloat(basePrice) || 0;
    validatedCode    = null;

    document.getElementById('planName').textContent     = name;
    document.getElementById('planPrice').textContent    = price;
    document.getElementById('planDuration').textContent = '· ' + duration;
    document.getElementById('successPlanName').textContent = name;

    // Reset discount field
    document.getElementById('modal-discount-code').value = '';
    document.getElementById('modal-discount-result').style.display = 'none';
    document.getElementById('summary-discount-row').style.display  = 'none';

    // Set base price summary
    document.getElementById('summary-original').textContent = '₱' + selectedPlanBase.toLocaleString('en-PH', {minimumFractionDigits:2});
    document.getElementById('summary-final').textContent    = '₱' + selectedPlanBase.toLocaleString('en-PH', {minimumFractionDigits:2});
    document.getElementById('summary-discount').textContent = '—';

    document.getElementById('confirmView').style.display = 'block';
    document.getElementById('successView').style.display  = 'none';
    document.getElementById('upgradeModal').style.display = 'flex';
}

let codeTimer;
function validateModalCode() {
    clearTimeout(codeTimer);
    const code   = document.getElementById('modal-discount-code').value.trim();
    const result = document.getElementById('modal-discount-result');

    if (!code) {
        result.style.display = 'none';
        validatedCode = null;
        resetPriceSummary();
        return;
    }

    codeTimer = setTimeout(() => {
        fetch('{{ route("admin.subscription.validate-code") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ code, plan_slug: selectedPlanKey })
        })
        .then(r => r.json())
        .then(data => {
            result.style.display = 'block';
            if (data.valid) {
                result.style.cssText = 'display:block;margin-top:8px;border-radius:8px;padding:9px 12px;font-size:13px;font-weight:600;background:rgba(22,163,74,.08);border:1.5px solid rgba(22,163,74,.3);color:#16a34a;';
                result.innerHTML = '<i class="fas fa-check-circle" style="margin-right:5px;"></i> Code valid — saves ' + data.formatted_value;
                validatedCode = code;

                // Update price summary
                document.getElementById('summary-discount').textContent = '−₱' + parseFloat(data.discount_amount).toLocaleString('en-PH', {minimumFractionDigits:2});
                document.getElementById('summary-final').textContent    = '₱'  + parseFloat(data.final_price).toLocaleString('en-PH', {minimumFractionDigits:2});
                document.getElementById('summary-discount-row').style.display = 'flex';
            } else {
                result.style.cssText = 'display:block;margin-top:8px;border-radius:8px;padding:9px 12px;font-size:13px;font-weight:600;background:rgba(206,17,38,.08);border:1.5px solid rgba(206,17,38,.3);color:#CE1126;';
                result.innerHTML = '<i class="fas fa-times-circle" style="margin-right:5px;"></i> ' + data.message;
                validatedCode = null;
                resetPriceSummary();
            }
        });
    }, 400);
}

function resetPriceSummary() {
    document.getElementById('summary-final').textContent    = '₱' + selectedPlanBase.toLocaleString('en-PH', {minimumFractionDigits:2});
    document.getElementById('summary-discount-row').style.display = 'none';
}

function closeUpgradeModal(event) {
    document.getElementById('upgradeModal').style.display = 'none';
}

function confirmUpgrade() {
    if (!selectedPlanKey) return;
    const btn = document.getElementById('confirmBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Upgrading...';

    fetch('{{ route("admin.subscription.upgrade") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({
            subscription:  selectedPlanKey,
            discount_code: validatedCode ?? '',
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('confirmView').style.display = 'none';
            document.getElementById('successView').style.display = 'block';
        } else {
            alert(data.message || 'Upgrade failed. Please try again.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check"></i> Yes, Upgrade Now';
        }
    })
    .catch(() => {
        alert('An error occurred. Please try again.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check"></i> Yes, Upgrade Now';
    });
}
</script>