<div id="upgradeModal" class="up-modal-backdrop" style="display:none;" onclick="closeUpgradeModal(event)">
    <div class="up-modal" onclick="event.stopPropagation()">

        <div id="confirmView">
            <div class="up-modal-icon" style="background:linear-gradient(135deg,#e8f0fb,#c5d8f5);">🚀</div>
            <h3 class="up-modal-title">Confirm Upgrade</h3>
            <p class="up-modal-sub">You're about to upgrade your plan to:</p>
            <div class="up-modal-plan-pill">
                <i class="fas fa-crown"></i>
                <span id="planName">—</span> — <span id="planPrice">—</span>
                <span id="planDuration" style="font-size:12px;opacity:0.8;"></span>
            </div>
            <p class="up-modal-sub" style="margin-bottom:0;">
                This is a <strong>simulation</strong>. No payment will be charged. Your features will be upgraded immediately.
            </p>
            <div class="up-modal-actions" style="margin-top:24px;">
                <button class="up-modal-confirm" id="confirmBtn" onclick="confirmUpgrade()">
                    <i class="fas fa-check"></i> Yes, Upgrade Now
                </button>
                <button class="up-modal-cancel" onclick="document.getElementById('upgradeModal').style.display='none'">
                    Maybe Later
                </button>
            </div>
        </div>

        <div id="successView" style="display:none;">
            <div class="up-success">
                <div class="up-modal-icon" style="background:rgba(34,197,94,0.15);font-size:36px;margin:0 auto 16px;">✅</div>
                <h3 class="up-modal-title">Plan Upgraded!</h3>
                <p class="up-modal-sub">
                    Your plan has been successfully upgraded to <strong id="successPlanName">—</strong>. New features are now active.
                </p>
                <button class="up-modal-confirm" onclick="window.location.href='{{ route('admin.dashboard') }}'">
                    <i class="fas fa-arrow-right"></i> Go to Dashboard
                </button>
            </div>
        </div>

    </div>
</div>

<div id="upgradeModal" class="up-modal-backdrop" style="display:none;" onclick="closeUpgradeModal(event)">
    <div class="up-modal" onclick="event.stopPropagation()">

        <div id="confirmView">
            <div class="up-modal-icon" style="background:linear-gradient(135deg,#e8f0fb,#c5d8f5);">🚀</div>
            <h3 class="up-modal-title">Confirm Upgrade</h3>
            <p class="up-modal-sub">You're about to upgrade your plan to:</p>
            <div class="up-modal-plan-pill">
                <i class="fas fa-crown"></i>
                <span id="planName">—</span> — <span id="planPrice">—</span>
                <span id="planDuration" style="font-size:12px;opacity:0.8;"></span>
            </div>

            {{-- Discount code field --}}
            <div style="margin-bottom:20px;text-align:left;">
                <label style="display:block;font-size:11px;font-weight:700;color:#5a7aaa;text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px;">
                    Discount Code <span style="font-weight:400;text-transform:none;">(optional)</span>
                </label>
                <div style="display:flex;gap:8px;">
                    <input type="text" id="modal-discount-code"
                           placeholder="e.g. SAVE20"
                           style="flex:1;padding:8px 10px;border-radius:8px;border:1.5px solid #c5d8f5;
                                  background:#fff;color:#001a4d;font-family:inherit;font-size:13px;
                                  outline:none;text-transform:uppercase;"
                           oninput="this.value=this.value.toUpperCase();validateModalCode()">
                    <button type="button" onclick="validateModalCode()"
                            style="padding:8px 14px;border-radius:8px;border:1.5px solid #c5d8f5;
                                   background:#f4f8ff;color:#003087;font-size:12px;font-weight:700;
                                   cursor:pointer;font-family:inherit;white-space:nowrap;">
                        Check
                    </button>
                </div>
                <div id="modal-discount-result"
                     style="margin-top:8px;border-radius:8px;padding:9px 12px;font-size:13px;font-weight:600;display:none;"></div>
            </div>

            {{-- Price display --}}
            <div id="price-summary" style="background:#f4f8ff;border-radius:12px;padding:14px;margin-bottom:20px;font-size:13px;">
                <div style="display:flex;justify-content:space-between;color:#5a7aaa;margin-bottom:4px;">
                    <span>Plan price</span>
                    <span id="summary-original">—</span>
                </div>
                <div id="summary-discount-row" style="display:none;justify-content:space-between;color:#16a34a;margin-bottom:4px;">
                    <span>Discount</span>
                    <span id="summary-discount">—</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-weight:700;color:#001a4d;border-top:1px solid #c5d8f5;padding-top:8px;margin-top:4px;">
                    <span>Total</span>
                    <span id="summary-final">—</span>
                </div>
            </div>

            <p class="up-modal-sub" style="margin-bottom:0;">
                This is a <strong>simulation</strong>. No payment will be charged. Your features will be upgraded immediately.
            </p>
            <div class="up-modal-actions" style="margin-top:24px;">
                <button class="up-modal-confirm" id="confirmBtn" onclick="confirmUpgrade()">
                    <i class="fas fa-check"></i> Yes, Upgrade Now
                </button>
                <button class="up-modal-cancel" onclick="document.getElementById('upgradeModal').style.display='none'">
                    Maybe Later
                </button>
            </div>
        </div>

        <div id="successView" style="display:none;">
            <div class="up-success">
                <div class="up-modal-icon" style="background:rgba(34,197,94,0.15);font-size:36px;margin:0 auto 16px;">✅</div>
                <h3 class="up-modal-title">Plan Upgraded!</h3>
                <p class="up-modal-sub">
                    Your plan has been successfully upgraded to <strong id="successPlanName">—</strong>. New features are now active.
                </p>
                <button class="up-modal-confirm" onclick="window.location.href='{{ route('admin.dashboard') }}'">
                    <i class="fas fa-arrow-right"></i> Go to Dashboard
                </button>
            </div>
        </div>

    </div>
</div>