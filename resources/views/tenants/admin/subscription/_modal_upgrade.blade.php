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