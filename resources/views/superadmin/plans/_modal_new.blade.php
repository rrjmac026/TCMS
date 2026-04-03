<div class="modal-overlay" id="modal-new-discount">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">
                <i class="fas fa-percent mr-2" style="color:var(--sa-accent);"></i> New Discount Code
            </span>
            <button onclick="closeModal('modal-new-discount')"
                    style="background:none;border:none;cursor:pointer;color:var(--sa-muted);font-size:18px;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="{{ route('superadmin.plans.discounts.store') }}" method="POST">
            @csrf
            <div class="modal-body space-y-4">

                <div class="form-row">
                    <div class="fi">
                        <label>Discount Name *</label>
                        <input type="text" name="name" placeholder="e.g. TESDA Anniversary Promo" required>
                    </div>
                    <div class="fi">
                        <label>Code (uppercase) *</label>
                        <input type="text" name="code" placeholder="TESDA2025" required
                               style="text-transform:uppercase;" oninput="this.value=this.value.toUpperCase()">
                    </div>
                </div>

                <div class="form-row">
                    <div class="fi">
                        <label>Discount Type *</label>
                        <select name="type" required>
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Amount (₱)</option>
                        </select>
                    </div>
                    <div class="fi">
                        <label>Discount Value *</label>
                        <input type="number" name="value" placeholder="e.g. 20 or 500" min="0.01" step="0.01" required>
                    </div>
                </div>

                <div class="fi">
                    <label>Applicable Plans (leave blank = all plans)</label>
                    <div class="check-group">
                        @foreach($plans as $p)
                            <label class="check-item">
                                <input type="checkbox" name="applicable_plans[]" value="{{ $p->slug }}">
                                {{ $p->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="fi">
                    <label>Applicable Actions (leave blank = all actions)</label>
                    <div class="check-group">
                        @foreach(['approve' => 'Approve (new)', 'upgrade_superadmin' => 'SA Upgrade', 'upgrade_admin' => 'Admin Upgrade', 'renewal' => 'Renewal'] as $val => $lbl)
                            <label class="check-item">
                                <input type="checkbox" name="applicable_actions[]" value="{{ $val }}">
                                {{ $lbl }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="fi">
                    <label>Restrict to Specific Tenant (optional)</label>
                    <select name="tenant_id">
                        <option value="">— Any tenant —</option>
                        @foreach($tenants as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-row">
                    <div class="fi">
                        <label>Valid From</label>
                        <input type="date" name="valid_from">
                    </div>
                    <div class="fi">
                        <label>Valid Until</label>
                        <input type="date" name="valid_until">
                    </div>
                </div>

                <div class="form-row">
                    <div class="fi">
                        <label>Max Total Uses (blank = unlimited)</label>
                        <input type="number" name="max_uses" placeholder="e.g. 50" min="1">
                    </div>
                    <div class="fi">
                        <label>Minimum Plan Price (₱) to qualify</label>
                        <input type="number" name="minimum_price" placeholder="Optional" min="0" step="0.01">
                    </div>
                </div>

                <div class="fi">
                    <label class="check-item">
                        <input type="checkbox" name="is_active" value="1" checked>
                        Active immediately
                    </label>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('modal-new-discount')" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Create Discount</button>
            </div>
        </form>
    </div>
</div>
