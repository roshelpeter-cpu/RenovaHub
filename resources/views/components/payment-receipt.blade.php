@props(['receipts' => [], 'open' => null])

<div id="rh-receipt-root" class="rh-receipt-root" hidden>
    <div class="rh-receipt-backdrop" data-receipt-close></div>
    <article class="rh-receipt-card" role="dialog" aria-modal="true" aria-labelledby="rh-receipt-title">
        <button type="button" class="rh-receipt-close" data-receipt-close aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <header class="rh-receipt-head">
            <div class="rh-receipt-brand">
                <span class="rh-receipt-mark" aria-hidden="true">
                    <svg viewBox="0 0 32 32" fill="currentColor"><path d="M16 4.2 4.2 14.4c-.3.3-.5.7-.5 1.1v11.2A2.2 2.2 0 0 0 5.9 29h6.1v-7.4c0-.7.6-1.2 1.2-1.2h5.6c.7 0 1.2.5 1.2 1.2V29h6.1a2.2 2.2 0 0 0 2.2-2.3V15.5c0-.4-.2-.8-.5-1.1L16 4.2Z"/></svg>
                </span>
                <span class="rh-receipt-name">RenovaHub</span>
            </div>
            <h2 id="rh-receipt-title" class="rh-receipt-title">Transaction Receipt</h2>
        </header>
        <p class="rh-receipt-amount" data-receipt-amount></p>
        <p class="rh-receipt-status" data-receipt-status>Successful</p>
        <p class="rh-receipt-when" data-receipt-when></p>
        <hr class="rh-receipt-rule">
        <div data-receipt-rows></div>
        <hr class="rh-receipt-dash">
        <footer class="rh-receipt-foot">
            <p>RenovaHub</p>
            <p>Secure Project Payment</p>
        </footer>
    </article>
</div>
<script type="application/json" id="rh-receipt-data">@json($receipts)</script>
<style>
    .rh-receipt-root[hidden] { display: none !important; }
    .rh-receipt-root {
        position: fixed;
        inset: 0;
        z-index: 80;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 28px 16px;
    }
    .rh-receipt-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(18, 24, 28, 0.48);
    }
    .rh-receipt-card {
        position: relative;
        width: min(440px, 100%);
        max-height: min(860px, calc(100vh - 40px));
        overflow: auto;
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 28px 70px rgba(16, 24, 40, 0.22);
        padding: 26px 28px 22px;
        color: #1c1c1c;
        font-family: Outfit, ui-sans-serif, system-ui, sans-serif;
    }
    .rh-receipt-close {
        position: absolute;
        top: 14px;
        right: 14px;
        width: 32px;
        height: 32px;
        border: 0;
        border-radius: 999px;
        background: transparent;
        color: #6b7280;
        font-size: 26px;
        line-height: 1;
        cursor: pointer;
    }
    .rh-receipt-close:hover { background: #f3f4f6; color: #111; }
    .rh-receipt-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding-right: 28px;
    }
    .rh-receipt-brand { display: flex; align-items: center; gap: 8px; min-width: 0; }
    .rh-receipt-mark {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 999px;
        background: #00a878;
        color: #fff;
        flex: none;
    }
    .rh-receipt-mark svg { width: 16px; height: 16px; }
    .rh-receipt-name { font-size: 18px; font-weight: 700; letter-spacing: -0.03em; color: #123d2b; }
    .rh-receipt-title { margin: 0; font-size: 18px; font-weight: 700; letter-spacing: -0.03em; color: #161616; }
    .rh-receipt-amount {
        margin: 28px 0 0;
        text-align: center;
        font-size: 34px;
        font-weight: 700;
        letter-spacing: -0.03em;
        color: #00a86b;
        line-height: 1.1;
    }
    .rh-receipt-status {
        margin: 10px 0 0;
        text-align: center;
        font-size: 22px;
        font-weight: 700;
        color: #1a1a1a;
    }
    .rh-receipt-when {
        margin: 8px 0 0;
        text-align: center;
        font-size: 14px;
        color: #9aa0a6;
    }
    .rh-receipt-rule {
        margin: 22px 0 8px;
        border: 0;
        border-top: 1px solid #e6e6e6;
    }
    .rh-receipt-row {
        display: flex;
        justify-content: space-between;
        gap: 18px;
        padding: 13px 0;
    }
    .rh-receipt-label {
        color: #8d8d8d;
        font-size: 14px;
        line-height: 1.35;
    }
    .rh-receipt-value {
        text-align: right;
        color: #1a1a1a;
        font-size: 14.5px;
        font-weight: 600;
        line-height: 1.35;
    }
    .rh-receipt-dash {
        margin: 16px 0 14px;
        border: 0;
        border-top: 1px dashed #cfcfcf;
    }
    .rh-receipt-foot { text-align: center; color: #16324f; font-size: 13.5px; line-height: 1.45; }
    .rh-receipt-foot p { margin: 0; }
    .rh-receipt-foot p:first-child { font-weight: 700; }
</style>
<script>
    (function () {
        if (window.__rhReceiptBound) {
            return;
        }
        window.__rhReceiptBound = true;

        const root = document.getElementById('rh-receipt-root');
        const dataNode = document.getElementById('rh-receipt-data');
        if (!root || !dataNode) {
            return;
        }

        const data = JSON.parse(dataNode.textContent || '{}');
        const amount = root.querySelector('[data-receipt-amount]');
        const status = root.querySelector('[data-receipt-status]');
        const when = root.querySelector('[data-receipt-when]');
        const rows = root.querySelector('[data-receipt-rows]');

        function closeReceipt() {
            root.hidden = true;
            document.body.style.overflow = '';
        }

        function openReceipt(id) {
            const receipt = data[id];
            if (!receipt) {
                return;
            }
            amount.textContent = receipt.amount || '';
            status.textContent = receipt.status || 'Successful';
            when.textContent = receipt.datetime || '';
            rows.replaceChildren();
            (receipt.rows || []).forEach(function (row) {
                const line = document.createElement('div');
                line.className = 'rh-receipt-row';
                const label = document.createElement('div');
                label.className = 'rh-receipt-label';
                label.textContent = row.label || '';
                const value = document.createElement('div');
                value.className = 'rh-receipt-value';
                (row.lines || []).forEach(function (text) {
                    const part = document.createElement('div');
                    part.textContent = text;
                    value.appendChild(part);
                });
                line.append(label, value);
                rows.appendChild(line);
            });
            root.hidden = false;
            document.body.style.overflow = 'hidden';
        }

        document.addEventListener('click', function (event) {
            const trigger = event.target.closest('[data-receipt-open]');
            if (trigger) {
                event.preventDefault();
                openReceipt(trigger.getAttribute('data-receipt-open'));
                return;
            }
            if (event.target.closest('[data-receipt-close]')) {
                event.preventDefault();
                closeReceipt();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !root.hidden) {
                closeReceipt();
            }
        });

        const initial = @json($open);
        if (initial) {
            openReceipt(initial);
        }
    })();
</script>
