{{-- ══════════════════════════════════════════
     CAMERA BARCODE SCANNER MODAL
     Requires: @zxing/library (loaded below)
     Usage: x-data must include barcodeScanner()
     spread, or this is included inside one.
   ══════════════════════════════════════════ --}}

{{-- Scanner trigger: replace the plain barcode input --}}
{{-- This partial assumes $barcodeValue is passed --}}

<style>
/* ── Scanner Modal ── */
#barcode-scanner-modal {
    display: none;
    position: fixed; inset: 0; z-index: 9999;
    background: rgba(0,0,0,0.85);
    backdrop-filter: blur(4px);
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 16px;
}
#barcode-scanner-modal.open { display: flex; }

.scanner-card {
    background: #fff;
    border-radius: 24px;
    overflow: hidden;
    width: 100%;
    max-width: 420px;
    box-shadow: 0 32px 80px rgba(0,0,0,0.5);
}

.scanner-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 20px;
    background: #155541;
}

#scanner-video-wrap {
    position: relative;
    background: #000;
    aspect-ratio: 4/3;
    overflow: hidden;
    display: flex; align-items: center; justify-content: center;
}

#scanner-video {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
}

/* Viewfinder overlay */
.scanner-viewfinder {
    position: absolute; inset: 0;
    display: flex; align-items: center; justify-content: center;
    pointer-events: none;
}
.scanner-frame {
    width: 65%; aspect-ratio: 2/1;
    border-radius: 12px;
    box-shadow: 0 0 0 9999px rgba(0,0,0,0.45);
    position: relative;
}
/* Corner brackets */
.scanner-frame::before,
.scanner-frame::after,
.scanner-frame > span::before,
.scanner-frame > span::after {
    content: '';
    position: absolute;
    width: 22px; height: 22px;
    border-color: #10B981;
    border-style: solid;
}
.scanner-frame::before  { top: -2px; left: -2px;  border-width: 3px 0 0 3px; border-radius: 4px 0 0 0; }
.scanner-frame::after   { top: -2px; right: -2px; border-width: 3px 3px 0 0; border-radius: 0 4px 0 0; }
.scanner-frame > span::before { bottom: -2px; left: -2px;  border-width: 0 0 3px 3px; border-radius: 0 0 0 4px; }
.scanner-frame > span::after  { bottom: -2px; right: -2px; border-width: 0 3px 3px 0; border-radius: 0 0 4px 0; }

/* Scan line animation */
@keyframes scanLine {
    0%   { top: 10%; }
    50%  { top: 85%; }
    100% { top: 10%; }
}
.scan-line {
    position: absolute;
    left: 0; right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, #10B981, transparent);
    box-shadow: 0 0 8px rgba(16,185,129,0.8);
    animation: scanLine 2s ease-in-out infinite;
}

/* Camera switch button */
.cam-switch-btn {
    position: absolute; top: 10px; right: 10px;
    width: 36px; height: 36px;
    border-radius: 50%;
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.25);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; color: #fff;
    backdrop-filter: blur(8px);
    transition: all 0.15s ease;
    z-index: 10;
}
.cam-switch-btn:hover { background: rgba(255,255,255,0.28); }

.scanner-footer {
    padding: 16px 20px;
    background: #fff;
}

.scanner-result-bar {
    display: flex; align-items: center; gap: 12px;
    padding: 10px 14px;
    border-radius: 12px;
    background: #F7F2E8;
    border: 1.5px solid rgba(210,194,168,0.5);
    min-height: 48px;
}

/* Scanner status states */
.scanner-status {
    font-size: 12px; font-weight: 600;
    padding: 4px 10px; border-radius: 99px;
    display: inline-flex; align-items: center; gap: 4px;
    min-height: unset;
}
.scanner-status.scanning {
    background: rgba(16,185,129,0.1); color: #1CA074;
}
.scanner-status.found {
    background: #DDF6ED; color: #155541;
}
.scanner-status.error {
    background: #fee2e2; color: #991b1b;
}
@keyframes blink {
    0%, 100% { opacity: 1; } 50% { opacity: 0.3; }
}
.blink { animation: blink 1s ease infinite; }

/* Pulse ring on scan */
@keyframes scanPulse {
    0%   { transform: scale(1); opacity: 1; }
    100% { transform: scale(1.5); opacity: 0; }
}
.scan-pulse {
    position: absolute;
    width: 60px; height: 60px;
    border-radius: 50%;
    background: rgba(16,185,129,0.3);
    animation: scanPulse 0.5s ease-out forwards;
}
</style>

{{-- ── Camera Scan Button (inline, beside barcode input) ── --}}
{{-- This is placed next to the barcode input in the parent template --}}

{{-- ── Scanner Modal ── --}}
<div id="barcode-scanner-modal" role="dialog" aria-modal="true" aria-label="Camera Barcode Scanner">
    <div class="scanner-card">

        {{-- Header --}}
        <div class="scanner-header">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-white/15 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-white">Scan Barcode</h3>
                    <p class="text-[10px] text-mint-300 font-medium" id="scanner-cam-label">Starting camera…</p>
                </div>
            </div>
            <button onclick="closeScanner()" type="button"
                    class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/25 text-white flex items-center justify-center transition-all"
                    style="min-height:unset; min-width:unset;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Viewfinder --}}
        <div id="scanner-video-wrap">
            <video id="scanner-video" autoplay playsinline muted></video>
            <div class="scanner-viewfinder">
                <div class="scanner-frame">
                    <span></span>
                    <div class="scan-line" id="scan-line"></div>
                </div>
            </div>
            {{-- Camera switch --}}
            <button onclick="switchCamera()" id="switch-cam-btn" type="button"
                    class="cam-switch-btn" title="Switch camera" style="display:none; min-height:unset;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </button>
            {{-- Loading overlay --}}
            <div id="scanner-loading" class="absolute inset-0 bg-black flex flex-col items-center justify-center gap-3">
                <svg class="w-10 h-10 text-mint-400 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <p class="text-mint-300 text-sm font-semibold">Starting camera…</p>
            </div>
            {{-- Error overlay --}}
            <div id="scanner-error-overlay" class="absolute inset-0 bg-black/90 flex-col items-center justify-center gap-3 p-6 text-center" style="display:none;">
                <svg class="w-12 h-12 text-red-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/></svg>
                <p class="text-white font-bold text-sm mt-2" id="scanner-error-msg">Camera access denied</p>
                <p class="text-beige-300 text-xs font-medium">Please allow camera permission in your browser settings, then try again.</p>
                <button onclick="retryCamera()" type="button"
                        class="mt-2 px-5 py-2.5 rounded-xl bg-mint-500 text-white text-xs font-bold hover:bg-mint-600 transition-colors"
                        style="min-height:unset;">
                    Retry
                </button>
            </div>
        </div>

        {{-- Footer --}}
        <div class="scanner-footer">
            {{-- Result display --}}
            <div class="scanner-result-bar mb-3">
                <svg class="w-4 h-4 text-beige-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="2" height="16" rx="0.5"/><rect x="7" y="4" width="1" height="16"/>
                    <rect x="10" y="4" width="2" height="16" rx="0.5"/><rect x="14" y="4" width="1" height="16"/>
                    <rect x="17" y="4" width="2" height="16" rx="0.5"/>
                </svg>
                <span id="scanner-result-text" class="flex-1 text-sm font-bold text-beige-400 italic">Point camera at a barcode…</span>
                <span class="scanner-status scanning blink" id="scanner-status-badge">
                    <span class="w-1.5 h-1.5 rounded-full bg-mint-500 inline-block"></span>
                    Scanning
                </span>
            </div>

            {{-- Manual entry fallback --}}
            <div class="flex items-center gap-2">
                <input type="text" id="scanner-manual-input"
                       placeholder="Or type barcode manually…"
                       class="flex-1 px-3 py-2.5 bg-beige-50 border border-beige-200 rounded-xl text-sm font-semibold text-mint-900 placeholder-beige-400 focus:outline-none focus:border-mint-400 focus:ring-2 focus:ring-mint-500/15 transition-all"
                       style="min-height:44px;"
                       @keydown.enter.prevent="confirmManualBarcode()">
                <button type="button" onclick="confirmManualBarcode()"
                        class="px-4 py-2.5 rounded-xl bg-mint-500 text-white text-xs font-bold hover:bg-mint-600 transition-colors flex-shrink-0"
                        style="min-height:44px;">
                    Use
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Html5-Qrcode library from CDN (Much better detection than plain ZXing) --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
/* ══════════════════════════════════════════
   CAMERA BARCODE SCANNER — html5-qrcode
   ══════════════════════════════════════════ */

let _html5QrCode    = null;
let _devices        = [];
let _deviceIdx      = 0;
let _targetInput    = null;
let _scannerOpen    = false;
let _lastSound      = 0;

/* ── Open scanner ── */
function openBarcodeScanner(inputElement) {
    _targetInput = inputElement;
    _scannerOpen = true;

    const modal = document.getElementById('barcode-scanner-modal');
    modal.classList.add('open');
    document.body.style.overflow = 'hidden';

    // Reset UI
    _setStatus('scanning');
    document.getElementById('scanner-result-text').textContent = 'Point camera at a barcode…';
    document.getElementById('scanner-manual-input').value = '';
    document.getElementById('scanner-loading').style.display = 'flex';
    document.getElementById('scanner-error-overlay').style.display = 'none';

    _startScanner();
}

/* ── Close scanner ── */
function closeScanner() {
    _scannerOpen = false;
    _stopScanner();

    const modal = document.getElementById('barcode-scanner-modal');
    modal.classList.remove('open');
    document.body.style.overflow = '';
}

/* ── Start scanner / enumerate cameras ── */
async function _startScanner() {
    if (typeof Html5Qrcode === 'undefined') {
        _showError('Scanner library not loaded. Please check your internet connection.');
        return;
    }

    try {
        // Enumerate devices using Html5Qrcode
        _devices = await Html5Qrcode.getCameras();

        if (!_devices || _devices.length === 0) {
            _showError('No camera found on this device.');
            return;
        }

        // Prefer back camera
        _deviceIdx = 0;
        const backCamIdx = _devices.findIndex(d =>
            /back|rear|environment/i.test(d.label)
        );
        if (backCamIdx >= 0) _deviceIdx = backCamIdx;

        // Show switch button if multiple cameras
        const switchBtn = document.getElementById('switch-cam-btn');
        switchBtn.style.display = _devices.length > 1 ? 'flex' : 'none';

        _beginDecode();
    } catch (err) {
        _showError('Cannot access camera: ' + err.message);
    }
}

/* ── Begin decoding with current device ── */
function _beginDecode() {
    _stopScanner().then(() => {
        if (!_scannerOpen) return;

        const device = _devices[_deviceIdx];
        document.getElementById('scanner-cam-label').textContent =
            (device.label || 'Camera ' + (_deviceIdx + 1)).split(' ').slice(0,4).join(' ');

        _html5QrCode = new Html5Qrcode("scanner-video-wrap");

        // Use high resolution for better barcode reading
        const config = {
            fps: 10,
            aspectRatio: 1.333334,
            videoConstraints: {
                deviceId: { exact: device.id },
                width: { ideal: 1280 },
                height: { ideal: 720 },
                focusMode: "continuous" // request continuous autofocus if available
            }
        };

        // We only care about 1D barcodes and QR codes
        config.formatsToSupport = [
            Html5QrcodeSupportedFormats.EAN_13,
            Html5QrcodeSupportedFormats.EAN_8,
            Html5QrcodeSupportedFormats.CODE_128,
            Html5QrcodeSupportedFormats.CODE_39,
            Html5QrcodeSupportedFormats.UPC_A,
            Html5QrcodeSupportedFormats.UPC_E,
            Html5QrcodeSupportedFormats.QR_CODE
        ];

        // Ensure the custom viewfinder CSS is hidden because html5-qrcode adds its own
        document.querySelector('.scanner-viewfinder').style.display = 'none';

        _html5QrCode.start(
            device.id,
            config,
            (decodedText, decodedResult) => {
                // Success callback
                if (_scannerOpen) {
                    _onBarcodeFound(decodedText);
                }
            },
            (errorMessage) => {
                // Parse errors can be ignored (happens when no barcode is found in frame)
            }
        ).then(() => {
            document.getElementById('scanner-loading').style.display = 'none';
        }).catch(err => {
            _showError('Camera error: ' + (err.message || err));
        });
    });
}

/* ── Stop scanner ── */
function _stopScanner() {
    if (_html5QrCode) {
        return _html5QrCode.stop().then(() => {
            _html5QrCode.clear();
            _html5QrCode = null;
        }).catch(err => {
            _html5QrCode = null;
        });
    }
    return Promise.resolve();
}

/* ── Switch camera ── */
function switchCamera() {
    if (_devices.length < 2) return;
    _deviceIdx = (_deviceIdx + 1) % _devices.length;
    document.getElementById('scanner-loading').style.display = 'flex';
    _beginDecode();
}

/* ── Retry after permission error ── */
function retryCamera() {
    document.getElementById('scanner-error-overlay').style.display = 'none';
    document.getElementById('scanner-loading').style.display = 'flex';
    _startScanner();
}

/* ── Barcode found ── */
function _onBarcodeFound(text) {
    const now = Date.now();
    if (now - _lastSound < 1500) return;
    _lastSound = now;

    if (navigator.vibrate) navigator.vibrate([80, 30, 80]);

    _setStatus('found');
    document.getElementById('scanner-result-text').textContent = text;
    document.getElementById('scanner-manual-input').value = text;

    if (_targetInput) {
        _targetInput.value = text;
        _targetInput.dispatchEvent(new Event('input', { bubbles: true }));
        _targetInput.dispatchEvent(new Event('change', { bubbles: true }));
        _targetInput.dispatchEvent(new InputEvent('input', { bubbles: true, data: text }));
    }

    _beep();

    setTimeout(() => {
        if (_scannerOpen) closeScanner();
    }, 900);
}

/* ── Manual barcode confirm ── */
function confirmManualBarcode() {
    const val = document.getElementById('scanner-manual-input').value.trim();
    if (!val) return;
    _onBarcodeFound(val);
}

/* ── Show error ── */
function _showError(msg) {
    document.getElementById('scanner-loading').style.display = 'none';
    document.getElementById('scanner-error-msg').textContent = msg;
    document.getElementById('scanner-error-overlay').style.display = 'flex';
    _setStatus('error');
}

/* ── Status badge ── */
function _setStatus(state) {
    const badge = document.getElementById('scanner-status-badge');
    badge.className = 'scanner-status ' + state + (state === 'scanning' ? ' blink' : '');
    const labels = { scanning: '● Scanning', found: '✓ Found', error: '✗ Error' };
    badge.textContent = labels[state] || state;
}

/* ── Beep ── */
function _beep() {
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.type = 'sine';
        osc.frequency.value = 1200;
        gain.gain.setValueAtTime(0.4, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.18);
        osc.start(ctx.currentTime);
        osc.stop(ctx.currentTime + 0.18);
    } catch(e) {}
}

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && _scannerOpen) closeScanner();
});
document.getElementById('barcode-scanner-modal').addEventListener('click', (e) => {
    if (e.target === document.getElementById('barcode-scanner-modal')) closeScanner();
});
</script>
