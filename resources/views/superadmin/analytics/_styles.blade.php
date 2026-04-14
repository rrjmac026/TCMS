<style>
:root {
    --c-ink:     #0f172a;
    --c-ink2:    #334155;
    --c-muted:   #64748b;
    --c-line:    #e2e8f0;
    --c-surf:    #f8fafc;
    --c-blue:    #2563eb;
    --c-blue-lt: #eff6ff;
    --c-green:   #16a34a;
    --c-green-lt:#f0fdf4;
    --c-amber:   #d97706;
    --c-amber-lt:#fffbeb;
    --c-red:     #dc2626;
    --c-red-lt:  #fef2f2;
    --c-gold:    #ca8a04;
    --c-gold-lt: #fefce8;
}
.dark {
    --c-ink:     #f1f5f9;
    --c-ink2:    #cbd5e1;
    --c-muted:   #94a3b8;
    --c-line:    #1e293b;
    --c-surf:    #0f172a;
    --c-blue-lt: #1e3a5f;
    --c-green-lt:#052e16;
    --c-amber-lt:#451a03;
    --c-red-lt:  #450a0a;
    --c-gold-lt: #422006;
}
.ac { background:var(--color-background-primary); border:0.5px solid var(--c-line); border-radius:14px; padding:20px 22px; }
.ac-title { font-size:12px; font-weight:500; color:var(--c-muted); text-transform:uppercase; letter-spacing:.6px; margin-bottom:14px; display:flex; align-items:center; gap:7px; }
.kpi { background:var(--color-background-secondary); border-radius:10px; padding:16px; }
.kpi-val { font-size:28px; font-weight:500; line-height:1; }
.kpi-lbl { font-size:12px; color:var(--c-muted); margin-top:4px; }
.pill { background:var(--color-background-secondary); border-radius:8px; padding:12px 14px; flex:1; min-width:80px; display:flex; flex-direction:column; align-items:center; gap:2px; }
.pill-v { font-size:20px; font-weight:500; color:var(--c-ink); }
.pill-l { font-size:11px; color:var(--c-muted); text-transform:uppercase; letter-spacing:.4px; }
.badge { display:inline-flex; align-items:center; font-size:11px; font-weight:500; padding:2px 8px; border-radius:99px; }
.brow { display:flex; align-items:center; justify-content:space-between; padding:9px 0; border-bottom:0.5px solid var(--c-line); font-size:13px; color:var(--c-ink); }
.brow:last-child { border-bottom:none; }
.bar-r { display:flex; align-items:center; gap:10px; margin-bottom:8px; }
.bar-t { flex:1; height:10px; border-radius:5px; background:var(--c-line); overflow:hidden; }
.bar-f { height:100%; border-radius:5px; background:var(--c-blue); }
.pcard { background:var(--color-background-secondary); border-radius:12px; padding:16px; border:0.5px solid var(--c-line); }
</style>