<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- ── Apply Form ── --}}
    <div class="apply-panel">
        <h2 class="font-bold text-lg mb-5" style="color:var(--sa-primary);">
            <i class="fas fa-magic mr-2" style="color:var(--sa-accent);"></i>
            Apply Discount to Tenant
        </h2>

        <form action="{{ route('superadmin.plans.discounts.apply') }}" method="POST" class="space-y-4">
            @csrf

            <div class="fi">
                <label>Select Tenant</label>
                <select name="tenant_id" required>
                    <option value="">— Choose tenant —</option>
                    @foreach($tenants as $t)
                        <option value="{{ $t->id }}">{{ $t->name }} ({{ ucfirst($t->subscription) }})</option>
                    @endforeach
                </select>
            </div>

            <div class="fi">
                <label>Target Plan</label>
                <select name="plan_slug" id="apply-plan-select" required onchange="liveValidate()">
                    <option value="">— Choose plan —</option>
                    @foreach($plans as $p)
                        <option value="{{ $p->slug }}">{{ $p->name }} (₱{{ number_format($p->price, 2) }})</option>
                    @endforeach
                </select>
            </div>

            <div class="fi">
                <label>Action</label>
                <select name="action" required>
                    <option value="approve">Approve (new tenant)</option>
                    <option value="upgrade_superadmin">Upgrade (by SuperAdmin)</option>
                    <option value="renewal">Renewal / Extend</option>
                </select>
            </div>

            <div class="fi">
                <label>Discount Code</label>
                <div class="flex gap-2">
                    <input type="text" name="discount_code" id="apply-code-input"
                           placeholder="e.g. TESDA2025"
                           style="text-transform:uppercase;flex:1;"
                           oninput="this.value=this.value.toUpperCase();liveValidate()">
                    <button type="button" onclick="liveValidate()" class="btn btn-outline" style="white-space:nowrap;">
                        <i class="fas fa-check-circle"></i> Check
                    </button>
                </div>
                <div id="validate-result" class="validate-result"></div>
            </div>

            <div class="flex gap-2 pt-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-magic"></i> Apply Discount & Set Plan
                </button>
            </div>
        </form>
    </div>

    {{-- ── Info & Quick List ── --}}
    <div class="space-y-4">

        <div class="rounded-xl border-2 p-5" style="background:rgba(0,87,184,.04);border-color:rgba(0,87,184,.2);">
            <div class="font-bold text-sm mb-3" style="color:var(--sa-primary);">
                <i class="fas fa-info-circle mr-2"></i> How Discounts Work
            </div>
            <div class="space-y-2 text-sm" style="color:var(--sa-muted);">
                <p>• <strong style="color:var(--sa-text);">Percentage</strong> — deducts a % from the plan's base price</p>
                <p>• <strong style="color:var(--sa-text);">Fixed (₱)</strong> — deducts a flat amount from the base price</p>
                <p>• Discounts can be restricted by plan, action, tenant, and date range</p>
                <p>• Every usage is recorded in the discount history</p>
                <p>• Applying a discount here also sets the tenant's plan and expiry</p>
            </div>
        </div>

        @php $activeDiscounts = $discounts->where('is_active', true)->take(6); @endphp
        @if($activeDiscounts->count())
            <div class="rounded-xl border-2 p-5" style="background:var(--sa-bg);border-color:var(--sa-border);">
                <div class="font-bold text-sm mb-3" style="color:var(--sa-primary);">
                    <i class="fas fa-bolt mr-2" style="color:var(--sa-gold);"></i> Active Codes
                </div>
                <div class="space-y-2">
                    @foreach($activeDiscounts as $d)
                        <div class="flex items-center justify-between text-sm">
                            <code class="px-2 py-0.5 rounded text-xs font-bold"
                                  style="background:rgba(0,48,135,.08);color:var(--sa-accent);">
                                {{ $d->code }}
                            </code>
                            <span class="font-semibold" style="color:var(--sa-success);">{{ $d->formatted_value }}</span>
                            <span class="text-xs" style="color:var(--sa-muted);">
                                {{ $d->uses_count }} use{{ $d->uses_count !== 1 ? 's' : '' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</div>
