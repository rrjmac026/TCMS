{{--
    Shared discount form fields.
    Used inside both the New and Edit modals.
    When $isEdit is true, inputs get the `ed-` id prefix so JS can populate them.
--}}
@php $e = isset($isEdit) && $isEdit; $p = $e ? 'ed-' : ''; @endphp

{{-- ── Discount Type Toggle ────────────────────────────────────────────────── --}}
<div style="margin-bottom:16px;">
    <label style="display:block;font-size:11px;font-weight:700;color:var(--sa-muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:8px;">
        Discount Type *
    </label>
    <div style="display:grid;grid-template-columns:1fr 1fr;border-radius:10px;overflow:hidden;border:1.5px solid var(--sa-border);">
        <label id="{{ $p }}lbl-automatic"
               style="display:flex;align-items:center;justify-content:center;gap:7px;padding:10px 14px;cursor:pointer;font-size:13px;font-weight:600;background:var(--sa-surface);color:var(--sa-muted);transition:background .15s,color .15s;user-select:none;">
            <input type="radio" name="is_automatic" value="1" id="{{ $p }}radio-automatic"
                   style="position:absolute;opacity:0;width:0;height:0;pointer-events:none;">
            <span style="font-size:15px;line-height:1;pointer-events:none;">🗓</span> Automatic
        </label>
        <label id="{{ $p }}lbl-code"
               style="display:flex;align-items:center;justify-content:center;gap:7px;padding:10px 14px;cursor:pointer;font-size:13px;font-weight:600;border-left:1.5px solid var(--sa-border);background:var(--sa-surface);color:var(--sa-muted);transition:background .15s,color .15s;user-select:none;">
            <input type="radio" name="is_automatic" value="0" id="{{ $p }}radio-code"
                   style="position:absolute;opacity:0;width:0;height:0;pointer-events:none;"
                   checked>
            <span style="font-size:15px;line-height:1;pointer-events:none;">🔑</span> Promo Code
        </label>
    </div>
    <p id="{{ $p }}hint-automatic" style="display:none;margin:6px 0 0;font-size:12px;color:var(--sa-muted);">
        Shown automatically on plan cards — no code needed by the tenant.
    </p>
    <p id="{{ $p }}hint-code" style="margin:6px 0 0;font-size:12px;color:var(--sa-muted);">
        Tenant must enter this code manually on the upgrade page.
    </p>
</div>

{{-- ── Label & Code ─────────────────────────────────────────────────────────── --}}
<div class="form-row">
    <div class="fi">
        <label>Discount Label *</label>
        <input type="text" name="label" id="{{ $p }}label"
               placeholder="e.g. TESDA Anniversary Promo" required>
    </div>
    <div class="fi" id="{{ $p }}code-field">
        <label>Code (uppercase) *</label>
        <input type="text" name="code" id="{{ $p }}code"
               placeholder="SAVE20"
               style="text-transform:uppercase;"
               oninput="this.value=this.value.toUpperCase()">
    </div>
</div>

{{-- ── Type & Value ─────────────────────────────────────────────────────────── --}}
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

{{-- ── Plan Restriction ─────────────────────────────────────────────────────── --}}
<div class="fi" style="margin-bottom:14px;">
    <label style="margin-bottom:6px;">
        Restrict to Plan(s)
        <span style="font-weight:400;text-transform:none;font-size:11px;color:var(--sa-muted);">
            — leave all unchecked to apply to all plans
        </span>
    </label>

    <div style="display:flex;flex-direction:column;gap:6px;">

        {{-- Basic --}}
        <label id="{{ $p }}plan-label-basic"
               style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:9px;
                      cursor:pointer;border:1.5px solid var(--sa-border);background:var(--sa-surface);
                      transition:border-color .15s,background .15s;user-select:none;">
            <span id="{{ $p }}plan-check-basic"
                  style="flex-shrink:0;width:18px;height:18px;border-radius:5px;border:1.5px solid var(--sa-border);
                         background:var(--sa-bg);display:flex;align-items:center;justify-content:center;
                         font-size:11px;font-weight:700;color:transparent;transition:all .15s;line-height:1;">✓</span>
            <input type="checkbox" name="plan_slugs[]" value="basic" id="{{ $p }}plan-basic"
                   style="position:absolute;opacity:0;width:0;height:0;pointer-events:none;"
                   onchange="syncPlanRow('{{ $p }}','basic','#5a7aaa','rgba(90,122,170')">
            <span style="font-size:15px;line-height:1;">🌱</span>
            <span style="font-size:13px;font-weight:600;color:var(--sa-text);">Basic</span>
            <span style="font-size:11px;color:var(--sa-muted);margin-left:auto;">Free · 30 days</span>
        </label>

        {{-- Standard --}}
        <label id="{{ $p }}plan-label-standard"
               style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:9px;
                      cursor:pointer;border:1.5px solid var(--sa-border);background:var(--sa-surface);
                      transition:border-color .15s,background .15s;user-select:none;">
            <span id="{{ $p }}plan-check-standard"
                  style="flex-shrink:0;width:18px;height:18px;border-radius:5px;border:1.5px solid var(--sa-border);
                         background:var(--sa-bg);display:flex;align-items:center;justify-content:center;
                         font-size:11px;font-weight:700;color:transparent;transition:all .15s;line-height:1;">✓</span>
            <input type="checkbox" name="plan_slugs[]" value="standard" id="{{ $p }}plan-standard"
                   style="position:absolute;opacity:0;width:0;height:0;pointer-events:none;"
                   onchange="syncPlanRow('{{ $p }}','standard','#0057B8','rgba(0,87,184')">
            <span style="font-size:15px;line-height:1;">🚀</span>
            <span style="font-size:13px;font-weight:600;color:var(--sa-text);">Standard</span>
            <span style="font-size:11px;color:var(--sa-muted);margin-left:auto;">₱1,499 · 6 months</span>
        </label>

        {{-- Premium --}}
        <label id="{{ $p }}plan-label-premium"
               style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:9px;
                      cursor:pointer;border:1.5px solid var(--sa-border);background:var(--sa-surface);
                      transition:border-color .15s,background .15s;user-select:none;">
            <span id="{{ $p }}plan-check-premium"
                  style="flex-shrink:0;width:18px;height:18px;border-radius:5px;border:1.5px solid var(--sa-border);
                         background:var(--sa-bg);display:flex;align-items:center;justify-content:center;
                         font-size:11px;font-weight:700;color:transparent;transition:all .15s;line-height:1;">✓</span>
            <input type="checkbox" name="plan_slugs[]" value="premium" id="{{ $p }}plan-premium"
                   style="position:absolute;opacity:0;width:0;height:0;pointer-events:none;"
                   onchange="syncPlanRow('{{ $p }}','premium','#a07800','rgba(161,122,0')">
            <span style="font-size:15px;line-height:1;">💎</span>
            <span style="font-size:13px;font-weight:600;color:var(--sa-text);">Premium</span>
            <span style="font-size:11px;color:var(--sa-muted);margin-left:auto;">₱3,999 · 1 year</span>
        </label>

    </div>

    <p id="{{ $p }}plan-hint" style="margin:8px 0 0;font-size:11px;color:var(--sa-muted);">
        No plans selected — discount applies to all plans.
    </p>
</div>

{{-- ── Valid Dates ─────────────────────────────────────────────────────────── --}}
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

{{-- ── Active ───────────────────────────────────────────────────────────────── --}}
<div class="fi">
    <label class="check-item" style="width:fit-content;">
        <input type="checkbox" name="is_active" id="{{ $p }}active" value="1"
               {{ !$e ? 'checked' : '' }}>
        Active
    </label>
</div>

<script>
(function () {
    var p = '{{ $p }}';

    /* ── Discount type toggle ─────────────────────────────────────────────── */
    function setToggleStyles() {
        var isAuto  = document.getElementById(p + 'radio-automatic').checked;
        var lblAuto = document.getElementById(p + 'lbl-automatic');
        var lblCode = document.getElementById(p + 'lbl-code');

        var base     = 'display:flex;align-items:center;justify-content:center;gap:7px;padding:10px 14px;cursor:pointer;font-size:13px;user-select:none;transition:background .15s,color .15s;';
        var active   = base + 'font-weight:700;background:#e8f0fb;color:var(--sa-accent);';
        var inactive = base + 'font-weight:600;background:var(--sa-surface);color:var(--sa-muted);';
        var rightBorder = 'border-left:1.5px solid var(--sa-border);';

        lblAuto.style.cssText = isAuto  ? active   : inactive;
        lblCode.style.cssText = !isAuto ? active + rightBorder : inactive + rightBorder;

        var codeField = document.getElementById(p + 'code-field');
        var codeInput = document.getElementById(p + 'code');
        var hintAuto  = document.getElementById(p + 'hint-automatic');
        var hintCode  = document.getElementById(p + 'hint-code');

        codeField.style.display = isAuto ? 'none' : '';
        if (codeInput) isAuto ? codeInput.removeAttribute('required') : codeInput.setAttribute('required','required');
        hintAuto.style.display = isAuto ? 'block' : 'none';
        hintCode.style.display = isAuto ? 'none'  : 'block';
    }

    document.getElementById(p + 'radio-automatic').addEventListener('change', setToggleStyles);
    document.getElementById(p + 'radio-code').addEventListener('change',      setToggleStyles);

    /* ── Plan row checkbox sync ───────────────────────────────────────────── */
    window.syncPlanRow = function(prefix, slug, accent, colorBase) {
        var cb    = document.getElementById(prefix + 'plan-' + slug);
        var row   = document.getElementById(prefix + 'plan-label-' + slug);
        var check = document.getElementById(prefix + 'plan-check-' + slug);
        var hint  = document.getElementById(prefix + 'plan-hint');

        if (cb.checked) {
            row.style.borderColor   = accent;
            row.style.background    = colorBase + ',.07)';
            check.style.background  = accent;
            check.style.borderColor = accent;
            check.style.color       = '#fff';
        } else {
            row.style.borderColor   = 'var(--sa-border)';
            row.style.background    = 'var(--sa-surface)';
            check.style.background  = 'var(--sa-bg)';
            check.style.borderColor = 'var(--sa-border)';
            check.style.color       = 'transparent';
        }

        /* Refresh hint text */
        var selected = [];
        ['basic','standard','premium'].forEach(function(s) {
            var el = document.getElementById(prefix + 'plan-' + s);
            if (el && el.checked) selected.push(s.charAt(0).toUpperCase() + s.slice(1));
        });
        if (hint) {
            hint.textContent = selected.length
                ? 'Applies to: ' + selected.join(', ') + ' only.'
                : 'No plans selected — discount applies to all plans.';
        }
    };

    /* ── Init on load ─────────────────────────────────────────────────────── */
    setToggleStyles();
    var planMeta = {
        basic:    { accent: '#5a7aaa', color: 'rgba(90,122,170'  },
        standard: { accent: '#0057B8', color: 'rgba(0,87,184'    },
        premium:  { accent: '#a07800', color: 'rgba(161,122,0'   },
    };
    ['basic','standard','premium'].forEach(function(s) {
        syncPlanRow(p, s, planMeta[s].accent, planMeta[s].color);
    });
})();
</script>