<style>
    :root {
        --sa-primary:  #003087;
        --sa-accent:   #0057B8;
        --sa-success:  #16a34a;
        --sa-warning:  #b38a00;
        --sa-danger:   #CE1126;
        --sa-gold:     #F5C518;
        --sa-border:   #c5d8f5;
        --sa-text:     #001a4d;
        --sa-muted:    #5a7aaa;
        --sa-bg:       #ffffff;
        --sa-surface:  #f4f8ff;
    }
    .dark {
        --sa-bg:      #0a1628;
        --sa-surface: #0d1f3c;
        --sa-border:  #1e3a6b;
        --sa-text:    #dde8ff;
        --sa-muted:   #6b8abf;
    }

    /* ── Tabs ── */
    .tab-nav { display: flex; gap: 0; border-bottom: 2px solid var(--sa-border); margin-bottom: 24px; }
    .tab-btn {
        padding: 11px 22px; font-size: 13px; font-weight: 700;
        color: var(--sa-muted); border: none; background: none;
        border-bottom: 3px solid transparent; margin-bottom: -2px;
        cursor: pointer; font-family: inherit; transition: all .15s;
        display: flex; align-items: center; gap: 7px;
    }
    .tab-btn.active { color: var(--sa-accent); border-bottom-color: var(--sa-accent); }
    .tab-btn:hover:not(.active) { color: var(--sa-text); }
    .tab-content { display: none; }
    .tab-content.active { display: block; }

    /* ── Plan Cards ── */
    .plan-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px,1fr)); gap: 20px; }

    .plan-card {
        border-radius: 18px; border: 2px solid var(--sa-border);
        background: var(--sa-bg); overflow: hidden;
        transition: box-shadow .18s, transform .18s;
    }
    .plan-card:hover { box-shadow: 0 8px 30px rgba(0,48,135,.10); transform: translateY(-2px); }

    .plan-header { padding: 22px 24px 18px; border-bottom: 2px solid var(--sa-border); }

    .plan-slug-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px; border-radius: 20px;
        font-size: 10px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase;
        margin-bottom: 10px;
    }
    .badge-basic    { background: rgba(90,122,170,.12); color: var(--sa-muted); }
    .badge-standard { background: rgba(0,87,184,.12);   color: var(--sa-accent); }
    .badge-premium  { background: rgba(245,197,24,.15); color: #a07800; }

    .plan-price { font-size: 28px; font-weight: 800; color: var(--sa-primary); line-height: 1; }
    .plan-price span { font-size: 14px; font-weight: 500; color: var(--sa-muted); margin-left: 2px; }
    .plan-duration { font-size: 12px; color: var(--sa-muted); margin-top: 4px; }

    .plan-body { padding: 18px 24px; }

    .feature-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 7px 0; border-bottom: 1px solid var(--sa-border); font-size: 13px;
    }
    .feature-row:last-child { border-bottom: none; }
    .feature-label { color: var(--sa-muted); font-weight: 500; }
    .feature-val   { font-weight: 700; color: var(--sa-text); }
    .feature-yes   { color: var(--sa-success); }
    .feature-no    { color: var(--sa-danger); opacity: .6; }

    .plan-actions { padding: 16px 24px; border-top: 2px solid var(--sa-border); }

    /* ── Inline edit form ── */
    .plan-edit-form { display: none; padding: 0 24px 20px; }
    .plan-edit-form.open { display: block; }

    .form-row   { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px; }
    .form-row-3 { grid-template-columns: 1fr 1fr 1fr; }

    .fi { display: flex; flex-direction: column; gap: 5px; }
    .fi label { font-size: 11px; font-weight: 700; color: var(--sa-muted); text-transform: uppercase; letter-spacing: .4px; }
    .fi input, .fi select, .fi textarea {
        padding: 8px 10px; border-radius: 8px; border: 1.5px solid var(--sa-border);
        background: var(--sa-bg); color: var(--sa-text); font-family: inherit;
        font-size: 13px; outline: none; transition: border-color .15s;
    }
    .fi input:focus, .fi select:focus, .fi textarea:focus { border-color: var(--sa-accent); }
    .fi textarea { resize: vertical; min-height: 64px; }

    .check-group { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 4px; }
    .check-item  { display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; color: var(--sa-text); cursor: pointer; }
    .check-item input { accent-color: var(--sa-accent); width: 15px; height: 15px; cursor: pointer; }

    /* ── Discount table ── */
    .disc-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .disc-table th {
        padding: 10px 14px; text-align: left; font-weight: 700;
        font-size: 11px; letter-spacing: .4px; text-transform: uppercase;
        color: var(--sa-muted); border-bottom: 2px solid var(--sa-border);
        background: var(--sa-surface);
    }
    .disc-table td { padding: 11px 14px; color: var(--sa-text); border-bottom: 1px solid var(--sa-border); }
    .disc-table tr:last-child td { border-bottom: none; }
    .disc-table tr:hover td { background: var(--sa-surface); }

    /* ── Status badges ── */
    .status-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: 700;
    }
    .sb-success { background: rgba(22,163,74,.10);  color: var(--sa-success); }
    .sb-warning { background: rgba(179,138,0,.10);  color: var(--sa-warning); }
    .sb-danger  { background: rgba(206,17,38,.10);  color: var(--sa-danger);  }
    .sb-muted   { background: rgba(90,122,170,.10); color: var(--sa-muted);   }

    /* ── Buttons ── */
    .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 9px; font-size: 12px; font-weight: 700; border: none; cursor: pointer; font-family: inherit; text-decoration: none; transition: all .15s; }
    .btn:hover { transform: translateY(-1px); }
    .btn-primary { background: var(--sa-accent); color: #fff; box-shadow: 0 2px 8px rgba(0,87,184,.22); }
    .btn-success { background: var(--sa-success); color: #fff; }
    .btn-danger  { background: rgba(206,17,38,.10); color: var(--sa-danger); border: 1.5px solid rgba(206,17,38,.25); }
    .btn-outline { background: var(--sa-surface); color: var(--sa-text); border: 1.5px solid var(--sa-border); }
    .btn-gold    { background: linear-gradient(135deg,var(--sa-gold) 0%,#d4a800 100%); color: #001a4d; }
    .btn-sm { padding: 5px 10px; font-size: 11px; border-radius: 7px; }

    /* ── Modal ── */
    .modal-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,.45);
        z-index: 1000; display: flex; align-items: center; justify-content: center;
        padding: 24px; opacity: 0; pointer-events: none; transition: opacity .2s;
    }
    .modal-overlay.open { opacity: 1; pointer-events: all; }
    .modal-box {
        background: var(--sa-bg); border-radius: 20px; border: 2px solid var(--sa-border);
        width: 100%; max-width: 600px; max-height: 90vh; overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0,0,0,.25); transform: translateY(20px);
        transition: transform .2s;
    }
    .modal-overlay.open .modal-box { transform: translateY(0); }
    .modal-header {
        padding: 22px 26px 18px; border-bottom: 2px solid var(--sa-border);
        display: flex; align-items: center; justify-content: space-between;
    }
    .modal-title  { font-size: 17px; font-weight: 800; color: var(--sa-primary); }
    .modal-body   { padding: 24px 26px; }
    .modal-footer { padding: 16px 26px; border-top: 2px solid var(--sa-border); display: flex; gap: 10px; justify-content: flex-end; }

    /* ── Apply discount panel ── */
    .apply-panel { border-radius: 18px; border: 2px solid var(--sa-border); background: var(--sa-bg); padding: 24px 28px; }

    /* ── Validator pill ── */
    .validate-result { border-radius: 10px; padding: 10px 14px; font-size: 13px; font-weight: 600; display: none; margin-top: 10px; }
    .validate-result.valid   { background: rgba(22,163,74,.08); border: 1.5px solid rgba(22,163,74,.3); color: var(--sa-success); display: block; }
    .validate-result.invalid { background: rgba(206,17,38,.08); border: 1.5px solid rgba(206,17,38,.3); color: var(--sa-danger);  display: block; }

    /* ── Stat pills ── */
    .stat-pill { display: flex; flex-direction: column; align-items: center; padding: 14px 20px; border-radius: 14px; background: var(--sa-surface); border: 1.5px solid var(--sa-border); gap: 4px; }
    .stat-pill-val { font-size: 22px; font-weight: 800; color: var(--sa-primary); line-height: 1; }
    .stat-pill-lbl { font-size: 10px; font-weight: 600; color: var(--sa-muted); text-transform: uppercase; letter-spacing: .5px; }
</style>
