(function () {
    'use strict';

    var cfg  = window.AlienDialogCfg;
    var alienId = cfg.alienId;
    var s = cfg.strings;

    // Greek / cryptic glyph pool used for the scramble animation
    var GLYPHS = 'ΑΒΓΔΕΖΗΘΙΚΛΜΝΞΟΠΡΣΤΥΦΧΨΩαβγδεζηθικλμνξοπρστυφχψωϞϟϠϡϢϣϤϥ';

    var pollTimer = null;
    var POLL_INTERVAL = 15000; // ms
    var dialogTypeLabels = {}; // type → human-readable label cache

    // -------------------------------------------------------
    // Utility: bridge call
    // -------------------------------------------------------
    function bridge(action, extra, callback) {
        var body = Object.assign({ action: action }, extra || {});
        fetch('alien_dialog.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body)
        })
        .then(function (r) {
            return r.text().then(function (text) {
                // Bot-check pages are HTML — detect by leading '<'
                if (text.trimStart().charAt(0) === '<') {
                    return { ok: false, code: 'SESSION_EXPIRED' };
                }
                try {
                    return JSON.parse(text);
                } catch (e) {
                    return { ok: false, code: 'NETWORK' };
                }
            });
        })
        .then(callback)
        .catch(function () {
            callback({ ok: false, error: s.errLoad, code: 'NETWORK' });
        });
    }

    // -------------------------------------------------------
    // Glyph scramble animation
    // Transforms readable text char-by-char into random glyphs
    // -------------------------------------------------------
    function scrambleText(text, steps, onDone) {
        var el    = document.getElementById('alien-signal-text');
        var chars = text.split('');
        var step  = 0;

        function randomGlyph() {
            return GLYPHS.charAt(Math.floor(Math.random() * GLYPHS.length));
        }

        function tick() {
            var ratio = step / steps;
            var result = chars.map(function (c, i) {
                if (c === ' ') return ' ';
                // Increasingly replace original chars with glyphs
                if (Math.random() < ratio) return randomGlyph();
                return c;
            });
            el.textContent = result.join('');
            step++;
            if (step <= steps) {
                setTimeout(tick, 60);
            } else {
                // Final fully-scrambled state
                el.textContent = chars.map(function (c) {
                    return c === ' ' ? ' ' : randomGlyph();
                }).join('');
                if (onDone) onDone();
            }
        }
        tick();
    }

    // -------------------------------------------------------
    // Render helpers
    // -------------------------------------------------------
    function showStatus(msg) {
        document.getElementById('alien-status').textContent = msg;
    }

    function showError(msg) {
        var el = document.getElementById('alien-error');
        el.textContent = msg;
        el.style.display = 'block';
    }

    function hideError() {
        var el = document.getElementById('alien-error');
        el.textContent = '';
        el.style.display = 'none';
    }

    function setView(name) {
        var views = ['view-select', 'view-transmitting', 'view-waiting', 'view-answered', 'view-terminal'];
        views.forEach(function (v) {
            var el = document.getElementById(v);
            if (el) el.style.display = (v === name) ? '' : 'none';
        });
    }

    // -------------------------------------------------------
    // Render: select dialog types
    // -------------------------------------------------------
    function renderSelect(types) {
        var sel = document.getElementById('dialog-type-select');
        sel.innerHTML = '';
        types.forEach(function (t) {
            var opt = document.createElement('option');
            opt.value       = t.type;
            opt.textContent = t.label;
            sel.appendChild(opt);
            dialogTypeLabels[t.type] = t.label; // cache for later use
        });
        showStatus(s.connInit);
        setView('view-select');
    }

    // -------------------------------------------------------
    // Ensure dialogTypeLabels is populated before rendering
    // (on page reload the selector may never have been shown)
    // -------------------------------------------------------
    function ensureLabels(callback) {
        if (Object.keys(dialogTypeLabels).length > 0) {
            callback();
            return;
        }
        bridge('listDialogTypes', {}, function (res) {
            if (res.ok && res.data) {
                res.data.forEach(function (t) {
                    dialogTypeLabels[t.type] = t.label;
                });
            }
            callback();
        });
    }

    // -------------------------------------------------------
    // Render: waiting state
    // -------------------------------------------------------
    function renderWaiting(data) {
        stopPolling();
        ensureLabels(function () {
            var label = (data && data.dialogType && dialogTypeLabels[data.dialogType])
                ? dialogTypeLabels[data.dialogType]
                : (data && data.dialogType ? data.dialogType : '');
            document.getElementById('waiting-plain').textContent  = label;
            document.getElementById('waiting-signal').textContent = scrambleStaticText(label);
            showStatus(s.waiting);
            setView('view-waiting');
            startPolling();
        });
    }

    // -------------------------------------------------------
    // Progressive glyph → plain unscramble animation
    // Reveals characters left-to-right over `steps` frames
    // -------------------------------------------------------
    function unscrambleText(el, text, steps, onDone) {
        var chars = text.split('');
        var step  = 0;
        el.classList.add('alien-signal-animated');

        function randomGlyph() {
            return GLYPHS.charAt(Math.floor(Math.random() * GLYPHS.length));
        }

        function tick() {
            // Each step reveals proportionally more plain chars from the left
            var revealed = Math.floor((step / steps) * chars.length);
            var result = chars.map(function (c, i) {
                if (c === ' ') return ' ';
                return i < revealed ? c : randomGlyph();
            });
            el.textContent = result.join('');
            step++;
            if (step <= steps) {
                setTimeout(tick, 60);
            } else {
                el.textContent = text; // ensure final state is exact
                el.classList.remove('alien-signal-animated');
                if (onDone) onDone();
            }
        }
        tick();
    }

    // -------------------------------------------------------
    // Render: answered state
    // Shows: sent question (plain) + encrypted→decoded answer
    // Also handles EXPIRED / CANCELLED (no animation)
    // -------------------------------------------------------
    function renderAnswered(data, forcedStatus) {
        stopPolling();
        var status      = forcedStatus || (data && data.status) || '';
        var npcMessage  = data && data.npcMessage ? data.npcMessage : '';

        ensureLabels(function () {
            var label = (data && data.dialogType && dialogTypeLabels[data.dialogType])
                ? dialogTypeLabels[data.dialogType]
                : (data && data.dialogType ? data.dialogType : '');

            // Always show what was asked
            document.getElementById('answer-question').textContent = label;

            var encEl      = document.getElementById('answer-encrypted');
            var encLblEl   = document.getElementById('answer-encrypted-label');
            var answerEl   = document.getElementById('answer-text');

            if (status === 'EXPIRED') {
                showStatus(s.expired);
                encEl.style.display    = 'none';
                encLblEl.style.display = 'none';
                answerEl.textContent   = s.expiredMsg;
                setView('view-answered');
                return;
            }
            if (status === 'CANCELLED') {
                showStatus(s.cancelled);
                encEl.style.display    = 'none';
                encLblEl.style.display = 'none';
                answerEl.textContent   = '';
                setView('view-answered');
                return;
            }

            // ANSWERED — show encrypted line + animate decryption
            showStatus(s.answered);
            if (npcMessage) {
                encEl.textContent      = scrambleStaticText(npcMessage);
                encEl.style.display    = '';
                encLblEl.style.display = '';
                answerEl.textContent   = '';
                answerEl.style.display = 'none';
                setView('view-answered');
                unscrambleText(encEl, npcMessage, 25, function () {
                    encEl.style.display    = 'none';
                    encLblEl.style.display = 'none';
                    answerEl.textContent   = npcMessage;
                    answerEl.style.display = '';
                });
            } else {
                encEl.style.display    = 'none';
                encLblEl.style.display = 'none';
                answerEl.textContent   = '';
                answerEl.style.display = '';
                setView('view-answered');
            }
        });
    }

    // -------------------------------------------------------
    // Render: expired / cancelled — delegates to renderAnswered
    // -------------------------------------------------------
    function renderExpiredOrCancelled(status) {
        renderAnswered(null, status);
    }

    // -------------------------------------------------------
    // A static partial scramble — used to display the sent
    // signal while waiting (frozen mid-scramble look)
    // -------------------------------------------------------
    function scrambleStaticText(text) {
        return text.split('').map(function (c) {
            if (c === ' ') return ' ';
            return Math.random() < 0.75
                ? GLYPHS.charAt(Math.floor(Math.random() * GLYPHS.length))
                : c;
        }).join('');
    }

    // -------------------------------------------------------
    // Dispatch current request state on page load
    // -------------------------------------------------------
    function dispatchState(data) {
        if (!data) {
            // No existing request — load dialog types for selection
            bridge('listDialogTypes', {}, function (res) {
                if (!res.ok || !res.data || res.data.length === 0) {
                    showError(s.errLoad);
                    return;
                }
                renderSelect(res.data);
            });
            return;
        }
        var st = data.status;
        if (st === 'WAITING_FOR_NPC' || st === 'CREATED') {
            renderWaiting(data);
        } else if (st === 'ANSWERED') {
            renderAnswered(data);
        } else if (st === 'EXPIRED' || st === 'CANCELLED') {
            renderExpiredOrCancelled(st);
        } else {
            // Unknown state — show selector
            bridge('listDialogTypes', {}, function (res) {
                if (res.ok && res.data) renderSelect(res.data);
            });
        }
    }

    // -------------------------------------------------------
    // Polling for waiting state
    // -------------------------------------------------------
    function startPolling() {
        if (pollTimer) return;
        pollTimer = setInterval(function () {
            bridge('getRequest', { npcId: alienId }, function (res) {
                if (!res.ok) {
                    if (res.code === 'SESSION_EXPIRED') {
                        stopPolling();
                        showError(s.errSession);
                    }
                    return;
                }
                if (res.data && (res.data.status === 'ANSWERED' || res.data.status === 'EXPIRED' || res.data.status === 'CANCELLED')) {
                    dispatchState(res.data);
                }
            });
        }, POLL_INTERVAL);
    }

    function stopPolling() {
        if (pollTimer) {
            clearInterval(pollTimer);
            pollTimer = null;
        }
    }

    // -------------------------------------------------------
    // Send transmission
    // -------------------------------------------------------
    function sendTransmission() {
        hideError();
        var sel        = document.getElementById('dialog-type-select');
        var dialogType = sel ? sel.value : '';
        var labelText  = sel ? sel.options[sel.selectedIndex].text : '';

        if (!dialogType) return;

        // Show transmitting view with the original text first
        document.getElementById('alien-signal-text').textContent = labelText;
        showStatus(s.transmitting);
        setView('view-transmitting');

        // Run scramble animation, then call the bridge
        scrambleText(labelText, 18, function () {
            bridge('createRequest', { npcId: alienId, dialogType: dialogType }, function (res) {
                if (!res.ok) {
                    if (res.code === 'CONFLICT') {
                        showError(s.errConflict);
                        // Fetch and show existing request
                        bridge('getRequest', { npcId: alienId }, function (r) {
                            dispatchState(r.ok ? r.data : null);
                        });
                    } else {
                        showError(s.errSend);
                        setView('view-select');
                    }
                    return;
                }
                dispatchState(res.data);
            });
        });
    }

    // -------------------------------------------------------
    // Cancel waiting request
    // -------------------------------------------------------
    function cancelRequest() {
        bridge('cancelRequest', { npcId: alienId }, function (res) {
            if (!res.ok && res.code !== 'NOT_FOUND') {
                showError(res.error || s.errSend);
                return;
            }
            // Reload dialog type selector for fresh request
            bridge('listDialogTypes', {}, function (r) {
                if (r.ok && r.data) renderSelect(r.data);
            });
        });
    }

    // -------------------------------------------------------
    // Poll once manually (button)
    // -------------------------------------------------------
    function pollOnce() {
        bridge('getRequest', { npcId: alienId }, function (res) {
            if (!res.ok) {
                showError(s.errLoad);
                return;
            }
            dispatchState(res.data);
        });
    }

    // -------------------------------------------------------
    // New request: go back to selector
    // -------------------------------------------------------
    function newRequest() {
        stopPolling();
        hideError();
        bridge('listDialogTypes', {}, function (res) {
            if (res.ok && res.data) renderSelect(res.data);
        });
    }

    // -------------------------------------------------------
    // Init: check existing request state on page load
    // -------------------------------------------------------
    document.addEventListener('DOMContentLoaded', function () {
        bridge('getRequest', { npcId: alienId }, function (res) {
            if (!res.ok) {
                showError(s.errLoad);
                return;
            }
            dispatchState(res.data);
        });
    });

    // Expose to inline onclick handlers
    window.alienDialog = {
        send:   sendTransmission,
        cancel: cancelRequest,
        poll:   pollOnce,
        newReq: newRequest
    };
}());
