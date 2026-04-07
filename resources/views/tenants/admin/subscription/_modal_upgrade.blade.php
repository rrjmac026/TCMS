{{-- Single modal instance (the old file had it duplicated — fixed here) --}}
<div id="upgradeModal" class="up-modal-backdrop" style="display:none;" onclick="closeUpgradeModal(event)">
    <div class="up-modal" onclick="event.stopPropagation()">

        {{-- ── Confirm view ── --}}
        <div id="confirmView">
            <div class="up-modal-icon" style="background:linear-gradient(135deg,#e8f0fb,#c5d8f5);">🚀</div>
            <h3 class="up-modal-title">Confirm Upgrade</h3>
            <p class="up-modal-sub">You're about to upgrade your plan to:</p>

            <div class="up-modal-plan-pill">
                <i class="fas fa-crown"></i>
                <span id="planName">—</span>
                <span id="planDuration" style="font-size:12px;opacity:0.8;"></span>
            </div>

            {{-- Auto-discount notice (shown when plan already has an active automatic discount) --}}
            <div id="auto-discount-notice" style="display:none;
                 background:rgba(22,163,74,.08);border:1.5px solid rgba(22,163,74,.3);
                 border-radius:10px;padding:10px 14px;margin-bottom:16px;
                 font-size:13px;font-weight:600;color:#16a34a;text-align:left;">
                <i class="fas fa-tag" style="margin-right:6px;"></i>
                <span id="auto-discount-text">—</span>
            </div>

            {{-- Promo code field (code-based, manual entry) --}}
            <div style="margin-bottom:16px;text-align:left;">
                <label style="display:block;font-size:11px;font-weight:700;color:#5a7aaa;
                              text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px;">
                    Promo Code <span style="font-weight:400;text-transform:none;">(optional — overrides automatic discount)</span>
                </label>
                <div style="display:flex;gap:8px;">
                    <input type="text" id="modal-discount-code"
                           placeholder="e.g. SAVE20"
                           style="flex:1;padding:8px 10px;border-radius:8px;border:1.5px solid #c5d8f5;
                                  background:#fff;color:#001a4d;font-family:inherit;font-size:13px;
                                  outline:none;text-transform:uppercase;"
                           oninput="this.value=this.value.toUpperCase(); scheduleCodeCheck()">
                    <button type="button" onclick="checkPromoCode()"
                            style="padding:8px 14px;border-radius:8px;border:1.5px solid #c5d8f5;
                                   background:#f4f8ff;color:#003087;font-size:12px;font-weight:700;
                                   cursor:pointer;font-family:inherit;white-space:nowrap;">
                        Apply
                    </button>
                </div>
                <div id="modal-discount-result" style="display:none;margin-top:8px;border-radius:8px;
                     padding:9px 12px;font-size:13px;font-weight:600;"></div>
            </div>

            {{-- Price summary --}}
            <div id="price-summary" style="background:#f4f8ff;border-radius:12px;padding:14px;
                 margin-bottom:20px;font-size:13px;">
                <div style="display:flex;justify-content:space-between;color:#5a7aaa;margin-bottom:4px;">
                    <span>Plan price</span>
                    <span id="summary-original">—</span>
                </div>
                <div id="summary-discount-row" style="display:none;justify-content:space-between;
                     color:#16a34a;margin-bottom:4px;">
                    <span id="summary-discount-label">Discount</span>
                    <span id="summary-discount">—</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-weight:700;color:#001a4d;
                     border-top:1px solid #c5d8f5;padding-top:8px;margin-top:4px;">
                    <span>Total</span>
                    <span id="summary-final">—</span>
                </div>
            </div>

            <p class="up-modal-sub" style="margin-bottom:0;">
                This is a <strong>simulation</strong>. No payment will be charged.
                Your features will be upgraded immediately.
            </p>

            <div class="up-modal-actions" style="margin-top:24px;">
                <button class="up-modal-confirm" id="confirmBtn" onclick="confirmUpgrade()">
                    <i class="fas fa-check"></i> Yes, Upgrade Now
                </button>
                <button class="up-modal-cancel"
                        onclick="document.getElementById('upgradeModal').style.display='none'">
                    Maybe Later
                </button>
            </div>
        </div>

        {{-- ── Success view ── --}}
        <div id="successView" style="display:none;">
            <div class="up-success">
                <div class="up-modal-icon"
                     style="background:rgba(34,197,94,0.15);font-size:36px;margin:0 auto 16px;">✅</div>
                <h3 class="up-modal-title">Plan Upgraded!</h3>
                <p class="up-modal-sub">
                    Your plan has been successfully upgraded to
                    <strong id="successPlanName">—</strong>.
                    New features are now active.
                </p>
                <button class="up-modal-confirm"
                        onclick="window.location.href='{{ route('admin.dashboard') }}'">
                    <i class="fas fa-arrow-right"></i> Go to Dashboard
                </button>
            </div>
        </div>

    </div>
</div>

{{-- ── Renewal Modal ── --}}
<div id="renewalModal" class="up-modal-backdrop" style="display:none;" onclick="closeRenewalModal(event)">
    <div class="up-modal" onclick="event.stopPropagation()">

        <div id="renewalConfirmView">
            <div class="up-modal-icon" style="background:linear-gradient(135deg,#e6f9f0,#bbf7d0);">🔄</div>
            <h3 class="up-modal-title">Renew Your Plan</h3>
            <p class="up-modal-sub">You're renewing your current plan:</p>

            <div class="up-modal-plan-pill"
                 style="background:linear-gradient(135deg,#0a7c3e,#16a34a);
                        box-shadow:0 4px 16px rgba(22,163,74,.30);">
                <i class="fas fa-rotate"></i>
                <span id="renewalPlanName">—</span>
                <span id="renewalPlanDuration" style="font-size:12px;opacity:0.8;"></span>
            </div>

            {{-- Pending request warning --}}
            <div id="renewal-pending-warning" style="display:none;
                 background:rgba(245,197,24,.08);border:1.5px solid rgba(245,197,24,.35);
                 border-radius:10px;padding:10px 14px;margin-bottom:16px;
                 font-size:13px;font-weight:600;color:#a07800;text-align:left;">
                <i class="fas fa-clock" style="margin-right:6px;"></i>
                You already have a pending renewal request. Cancel it first before submitting a new one.
            </div>

            {{-- Auto-discount notice --}}
            <div id="renewal-auto-discount-notice" style="display:none;
                 background:rgba(22,163,74,.08);border:1.5px solid rgba(22,163,74,.3);
                 border-radius:10px;padding:10px 14px;margin-bottom:16px;
                 font-size:13px;font-weight:600;color:#16a34a;text-align:left;">
                <i class="fas fa-tag" style="margin-right:6px;"></i>
                <span id="renewal-auto-discount-text">—</span>
            </div>

            {{-- Promo code --}}
            <div style="margin-bottom:16px;text-align:left;">
                <label style="display:block;font-size:11px;font-weight:700;color:#5a7aaa;
                              text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px;">
                    Promo Code <span style="font-weight:400;text-transform:none;">(optional)</span>
                </label>
                <div style="display:flex;gap:8px;">
                    <input type="text" id="renewal-discount-code"
                           placeholder="e.g. SAVE20"
                           style="flex:1;padding:8px 10px;border-radius:8px;border:1.5px solid #c5d8f5;
                                  background:#fff;color:#001a4d;font-family:inherit;font-size:13px;
                                  outline:none;text-transform:uppercase;"
                           oninput="this.value=this.value.toUpperCase(); scheduleRenewalCodeCheck()">
                    <button type="button" onclick="checkRenewalPromoCode()"
                            style="padding:8px 14px;border-radius:8px;border:1.5px solid #c5d8f5;
                                   background:#f4f8ff;color:#003087;font-size:12px;font-weight:700;
                                   cursor:pointer;font-family:inherit;white-space:nowrap;">
                        Apply
                    </button>
                </div>
                <div id="renewal-discount-result" style="display:none;margin-top:8px;border-radius:8px;
                     padding:9px 12px;font-size:13px;font-weight:600;"></div>
            </div>

            {{-- Price summary --}}
            <div id="renewal-price-summary" style="background:#f4f8ff;border-radius:12px;padding:14px;
                 margin-bottom:20px;font-size:13px;">
                <div style="display:flex;justify-content:space-between;color:#5a7aaa;margin-bottom:4px;">
                    <span>Plan price</span>
                    <span id="renewal-summary-original">—</span>
                </div>
                <div id="renewal-summary-discount-row" style="display:none;justify-content:space-between;
                     color:#16a34a;margin-bottom:4px;">
                    <span id="renewal-summary-discount-label">Discount</span>
                    <span id="renewal-summary-discount">—</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-weight:700;color:#001a4d;
                     border-top:1px solid #c5d8f5;padding-top:8px;margin-top:4px;">
                    <span>Total</span>
                    <span id="renewal-summary-final">—</span>
                </div>
            </div>

            <p class="up-modal-sub" style="margin-bottom:0;">
                Renewing will extend your access by another full plan period.
                A super admin will review and approve your request.
            </p>

            <div class="up-modal-actions" style="margin-top:24px;">
                <button class="up-modal-confirm" id="renewalConfirmBtn"
                        style="background:linear-gradient(135deg,#0a7c3e,#16a34a);
                               box-shadow:0 4px 16px rgba(22,163,74,.30);"
                        onclick="confirmRenewal()">
                    <i class="fas fa-paper-plane"></i> Submit Renewal Request
                </button>
                <button class="up-modal-cancel" onclick="closeRenewalModal()">
                    Maybe Later
                </button>
            </div>
        </div>

        {{-- Success view --}}
        <div id="renewalSuccessView" style="display:none;">
            <div class="up-success">
                <div class="up-modal-icon"
                     style="background:rgba(34,197,94,0.15);font-size:36px;margin:0 auto 16px;">✅</div>
                <h3 class="up-modal-title">Request Submitted!</h3>
                <p class="up-modal-sub">
                    Your renewal request for
                    <strong id="renewalSuccessPlanName">—</strong>
                    has been submitted. Please wait for super admin approval.
                </p>
                <button class="up-modal-confirm"
                        style="background:linear-gradient(135deg,#0a7c3e,#16a34a);"
                        onclick="location.reload()">
                    <i class="fas fa-check"></i> Done
                </button>
            </div>
        </div>

    </div>
</div>