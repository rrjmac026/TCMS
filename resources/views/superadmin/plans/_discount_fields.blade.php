{{--
    Shared discount form fields.
    Used inside both the New and Edit modals.
    When $isEdit is true, inputs get the `ed-` id prefix so JS can populate them.

    KEY: is_automatic controls whether this is an auto-shown discount or a promo code.
         Automatic discounts don't need a code — the code field is hidden/disabled.
--}}
@php $e = isset($isEdit) && $isEdit; $p = $e ? 'ed-' : ''; @endphp

{{-- Discount type toggle (automatic vs code-based) --}}
<div class="fi" style="margin-bottom:16px;">
    <label style="display:block;font-size:12px;font-weight:700;color:#5a7aaa;text-transform:uppercase;letter-spacing:.4px;margin-bottom:8px;">
        Discount Type *
    </label>
    <div style="display:flex;gap:0;border-radius:10px;overflow:hidden;border:1.5px solid #c5d8f5;">
        <label style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:9px 12px;cursor:pointer;font-size:13px;font-weight:600;transition:background .15s;"
               id="{{ $p }}lbl-automatic">
            <input type="radio" name="is_automatic" value="1"
                   id="{{ $p }}radio-automatic"
                   onchange="toggleCodeField('{{ $p }}')"
                   style="margin:0;">
            🗓 Automatic (date-based)
        </label>
        <label style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:9px 12px;cursor:pointer;font-size:13px;font-weight:600;border-left:1.5px solid #c5d8f5;transition:background .15s;"
               id="{{ $p }}lbl-code">
            <input type="radio" name="is_automatic" value="0"
                   id="{{ $p }}radio-code"
                   onchange="toggleCodeField('{{ $p }}')"
                   style="margin:0;" checked>
            🔑 Promo Code
        </label>
    </div>
    <p id="{{ $p }}hint-automatic" style="display:none;margin:6px 0 0;font-size:12px;color:#5a7aaa;">
        Shown automatically on plan cards — no code needed by the tenant.
    </p>
    <p id="{{ $p }}hint-code" style="margin:6px 0 0;font-size:12px;color:#5a7aaa;">
        Tenant must enter this code manually on the upgrade page.
    </p>
</div>

<div class="form-row">
    <div class="fi">
        <label>Discount Label *</label>
        <input type="text" name="label" id="{{ $p }}label"
               placeholder="e.g. TESDA Anniversary Promo" required>
    </div>
    {{-- Code field — hidden for automatic discounts --}}
    <div class="fi" id="{{ $p }}code-field">
        <label>Code (uppercase) *</label>
        <input type="text" name="code" id="{{ $p }}code"
               placeholder="SAVE20"
               style="text-transform:uppercase;"
               oninput="this.value=this.value.toUpperCase()">
    </div>
</div>

<div class="form-row">
    <div class="fi">
        <label>Type *</label>
        <select name="type" id="{{ $p }}type" required>
            <option value="percentage">Percentage (%)</option>
            <option value="fixed">Fixed Amount (₱)</option>
        </select>
    </div>
    <div class="fi">
        <label>Value *</label>
        <input type="number" name="value" id="{{ $p }}value"
               min="0.01" step="0.01" placeholder="e.g. 20 or 500" required>
    </div>
</div>

<div class="fi">
    <label>
        Restrict to Plan
        <span style="color:var(--sa-muted);font-weight:400;text-transform:none;">(blank = all plans)</span>
    </label>
    <select name="plan_slug" id="{{ $p }}plan-slug">
        <option value="">— All plans —</option>
        @foreach(config('plans') as $slug => $plan)
            <option value="{{ $slug }}">{{ $plan['name'] }}</option>
        @endforeach
    </select>
</div>

<div class="form-row">
    <div class="fi">
        <label>Valid From</label>
        <input type="date" name="valid_from" id="{{ $p }}valid-from">
    </div>
    <div class="fi">
        <label>Valid Until</label>
        <input type="date" name="valid_until" id="{{ $p }}valid-until">
    </div>
</div>

<div class="fi">
    <label class="check-item" style="width:fit-content;">
        <input type="checkbox" name="is_active" id="{{ $p }}active" value="1"
               {{ !$e ? 'checked' : '' }}>
        Active
    </label>
</div>

<script>
function toggleCodeField(prefix) {
    const isAuto     = document.getElementById(prefix + 'radio-automatic').checked;
    const codeField  = document.getElementById(prefix + 'code-field');
    const codeInput  = document.getElementById(prefix + 'code');
    const hintAuto   = document.getElementById(prefix + 'hint-automatic');
    const hintCode   = document.getElementById(prefix + 'hint-code');
    const lblAuto    = document.getElementById(prefix + 'lbl-automatic');
    const lblCode    = document.getElementById(prefix + 'lbl-code');

    if (isAuto) {
        codeField.style.display  = 'none';
        if (codeInput) codeInput.removeAttribute('required');
        hintAuto.style.display   = 'block';
        hintCode.style.display   = 'none';
        lblAuto.style.background = '#e8f0fb';
        lblCode.style.background = '';
    } else {
        codeField.style.display  = '';
        if (codeInput) codeInput.setAttribute('required', 'required');
        hintAuto.style.display   = 'none';
        hintCode.style.display   = 'block';
        lblAuto.style.background = '';
        lblCode.style.background = '#e8f0fb';
    }
}

// Run once on load so edit modals reflect saved state
document.addEventListener('DOMContentLoaded', function () {
    toggleCodeField('');       // new modal (no prefix)
    toggleCodeField('ed-');    // edit modal
});
</script>