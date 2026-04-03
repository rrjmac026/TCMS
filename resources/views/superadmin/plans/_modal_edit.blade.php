<div class="modal-overlay" id="modal-edit-discount">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">
                <i class="fas fa-pencil-alt mr-2" style="color:var(--sa-accent);"></i> Edit Discount Code
            </span>
            <button onclick="closeModal('modal-edit-discount')"
                    style="background:none;border:none;cursor:pointer;color:var(--sa-muted);font-size:18px;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="edit-discount-form" method="POST">
            @csrf @method('PATCH')
            <div class="modal-body space-y-4">

                <div class="form-row">
                    <div class="fi">
                        <label>Discount Name *</label>
                        <input type="text" name="name" id="ed-name" required>
                    </div>
                    <div class="fi">
                        <label>Code *</label>
                        <input type="text" name="code" id="ed-code" required
                               style="text-transform:uppercase;" oninput="this.value=this.value.toUpperCase()">
                    </div>
                </div>

                <div class="form-row">
                    <div class="fi">
                        <label>Type *</label>
                        <select name="type" id="ed-type" required>
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Amount (₱)</option>
                        </select>
                    </div>
                    <div class="fi">
                        <label>Value *</label>
                        <input type="number" name="value" id="ed-value" min="0.01" step="0.01" required>
                    </div>
                </div>

                <div class="fi">
                    <label>Applicable Plans</label>
                    <div class="check-group">
                        @foreach($plans as $p)
                            <label class="check-item">
                                <input type="checkbox" class="ed-plan-cb" name="applicable_plans[]" value="{{ $p->slug }}">
                                {{ $p->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="fi">
                    <label>Applicable Actions</label>
                    <div class="check-group">
                        @foreach(['approve' => 'Approve', 'upgrade_superadmin' => 'SA Upgrade', 'upgrade_admin' => 'Admin Upgrade', 'renewal' => 'Renewal'] as $val => $lbl)
                            <label class="check-item">
                                <input type="checkbox" class="ed-action-cb" name="applicable_actions[]" value="{{ $val }}">
                                {{ $lbl }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="fi">
                    <label>Restrict to Specific Tenant</label>
                    <select name="tenant_id" id="ed-tenant">
                        <option value="">— Any tenant —</option>
                        @foreach($tenants as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-row">
                    <div class="fi">
                        <label>Valid From</label>
                        <input type="date" name="valid_from" id="ed-valid-from">
                    </div>
                    <div class="fi">
                        <label>Valid Until</label>
                        <input type="date" name="valid_until" id="ed-valid-until">
                    </div>
                </div>

                <div class="form-row">
                    <div class="fi">
                        <label>Max Uses</label>
                        <input type="number" name="max_uses" id="ed-max-uses" min="1" placeholder="Unlimited">
                    </div>
                    <div class="fi">
                        <label>Min Price (₱)</label>
                        <input type="number" name="minimum_price" id="ed-min-price" min="0" step="0.01">
                    </div>
                </div>

                <div class="fi">
                    <label class="check-item">
                        <input type="checkbox" name="is_active" id="ed-active" value="1">
                        Active
                    </label>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('modal-edit-discount')" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>
