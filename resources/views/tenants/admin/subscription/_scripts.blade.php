<script>
let selectedPlanKey = null;

const planSlugs   = @json($planSlugs);
const currentPlan = '{{ $currentPlan }}';
const currentIndex = planSlugs.indexOf(currentPlan);

function selectPlan(key, name, price, duration) {
    const newIndex = planSlugs.indexOf(key);
    if (newIndex <= currentIndex) return;

    selectedPlanKey = key;
    document.getElementById('planName').textContent     = name;
    document.getElementById('planPrice').textContent    = price;
    document.getElementById('planDuration').textContent = '· ' + duration;
    document.getElementById('successPlanName').textContent = name;
    document.getElementById('confirmView').style.display = 'block';
    document.getElementById('successView').style.display  = 'none';
    document.getElementById('upgradeModal').style.display = 'flex';
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
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ subscription: selectedPlanKey })
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