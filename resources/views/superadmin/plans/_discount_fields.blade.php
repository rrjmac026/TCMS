{{--
    Shared discount form fields.
    Used inside both the New and Edit modals.
    When $isEdit is true, inputs get the `ed-` id prefix so JS can populate them.
--}}
@php $e = isset($isEdit) && $isEdit; @endphp

<div class="form-row">
    <div class="fi">
        <label>Discount Label *</label>
        <input type="text" name="label" {{ $e ? 'id=ed-label' : '' }}
               placeholder="e.g. TESDA Anniversary Promo" required>
    </div>
    <div class="fi">
        <label>Code (uppercase) *</label>
        <input type="text" name="code" {{ $e ? 'id=ed-code' : '' }}
               placeholder="SAVE20" required
               style="text-transform:uppercase;"
               oninput="this.value=this.value.toUpperCase()">
    </div>
</div>

<div class="form-row">
    <div class="fi">
        <label>Type *</label>
        <select name="type" {{ $e ? 'id=ed-type' : '' }} required>
            <option value="percentage">Percentage (%)</option>
            <option value="fixed">Fixed Amount (₱)</option>
        </select>
    </div>
    <div class="fi">
        <label>Value *</label>
        <input type="number" name="value" {{ $e ? 'id=ed-value' : '' }}
               min="0.01" step="0.01" placeholder="e.g. 20 or 500" required>
    </div>
</div>

<div class="fi">
    <label>Restrict to Plan <span style="color:var(--sa-muted);font-weight:400;text-transform:none;">(blank = all plans)</span></label>
    <select name="plan_slug" {{ $e ? 'id=ed-plan-slug' : '' }}>
        <option value="">— All plans —</option>
        @foreach(config('plans') as $slug => $plan)
            <option value="{{ $slug }}">{{ $plan['name'] }}</option>
        @endforeach
    </select>
</div>

<div class="form-row">
    <div class="fi">
        <label>Valid From</label>
        <input type="date" name="valid_from" {{ $e ? 'id=ed-valid-from' : '' }}>
    </div>
    <div class="fi">
        <label>Valid Until</label>
        <input type="date" name="valid_until" {{ $e ? 'id=ed-valid-until' : '' }}>
    </div>
</div>

<div class="fi">
    <label class="check-item" style="width:fit-content;">
        <input type="checkbox" name="is_active" {{ $e ? 'id=ed-active' : '' }} value="1"
               {{ !$e ? 'checked' : '' }}>
        Active
    </label>
</div>