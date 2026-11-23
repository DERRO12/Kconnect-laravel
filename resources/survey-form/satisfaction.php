<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<meta name="referrer" content="no-referrer">
<title>Satisfaction Form - Kiboko Group</title>
<!--
    Kiboko satisfaction form (anonymous).
    Put this file (and kibokogroup.png) on dashboard.kibokogroup.com. The questions come from
    K-Connect (Admin > Satisfaction Surveys), so nothing here needs editing when questions change.
    Each survey link looks like: satisfaction.php?s=<survey code>

    Privacy: this page loads nothing from other sites (no fonts, analytics or trackers), sends no
    cookies, and sends only the answers. K-Connect stores the answers with the date only.
-->
<style>
    :root {
        --kiboko-green: #76bc21;
        --kiboko-blue: #009ada;
        --kiboko-dark: #0f172a;
        --glass: rgba(255, 255, 255, 0.72);
        --card-border: rgba(255, 255, 255, 0.85);
        --grad: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
        --grad-light: linear-gradient(135deg, rgba(0, 154, 218, 0.08) 0%, rgba(118, 188, 33, 0.08) 100%);
        --shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        background: #eef2f7; color: var(--kiboko-dark); min-height: 100vh; padding: 20px;
    }
    .bg-wrapper { position: fixed; inset: 0; z-index: -1; background: #f8fafc; overflow: hidden; }
    .orb { position: absolute; width: 400px; height: 400px; border-radius: 50%; filter: blur(80px); opacity: .2; animation: move 20s infinite alternate; }
    .orb-1 { background: var(--kiboko-blue); top: -10%; left: -10%; }
    .orb-2 { background: var(--kiboko-green); bottom: -10%; right: -10%; animation-delay: -5s; }
    @keyframes move { from { transform: translate(0, 0) scale(1); } to { transform: translate(60px, 30px) scale(1.1); } }
    @media (prefers-reduced-motion: reduce) { .orb { animation: none; } }

    .container {
        max-width: 960px; margin: 0 auto; background: var(--glass);
        backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
        border-radius: 28px; padding: 40px 45px; border: 1px solid var(--card-border); box-shadow: var(--shadow);
    }
    .header { text-align: center; padding-bottom: 20px; margin-bottom: 26px; border-bottom: 3px solid var(--kiboko-blue); }
    .header .logo { max-height: 70px; width: auto; margin-bottom: 8px; }
    .header h1 { font-size: 22px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; }
    .header .subtitle { color: #64748b; font-size: 14px; font-weight: 500; margin-top: 4px; }

    .anon-box { background: linear-gradient(135deg, #dcfce7 0%, #f0fdf4 100%); border-left: 5px solid var(--kiboko-green); padding: 16px 22px; border-radius: 12px; margin-bottom: 22px; }
    .anon-box strong { color: #166534; display: block; font-size: 15px; }
    .anon-box p { color: #14532d; font-size: 13px; line-height: 1.7; margin-top: 4px; }
    .intro { font-size: 14px; line-height: 1.75; color: #334155; white-space: pre-line; margin-bottom: 26px; }

    .progress-wrap { position: sticky; top: 0; z-index: 5; margin: 0 -45px 24px; padding: 10px 45px; background: rgba(248, 250, 252, .92); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border-bottom: 1px solid rgba(0,0,0,.05); }
    .progress-label { display: flex; justify-content: space-between; font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 6px; }
    .progress { height: 8px; background: #e2e8f0; border-radius: 8px; overflow: hidden; }
    .progress span { display: block; height: 100%; width: 0; background: var(--grad); transition: width .3s; }

    .section { margin-bottom: 30px; padding-bottom: 24px; border-bottom: 1px solid rgba(0,0,0,.06); }
    .section:last-of-type { border-bottom: none; }
    .section-title { font-size: 18px; font-weight: 700; margin-bottom: 16px; padding: 10px 14px; border-radius: 12px; background: var(--grad-light); border-left: 4px solid var(--kiboko-blue); }

    .q { margin-bottom: 18px; border-radius: 12px; }
    .q > label, .q > .q-label { display: block; font-weight: 600; font-size: 14px; color: #1e293b; margin-bottom: 7px; line-height: 1.5; }
    .req { color: #dc2626; margin-left: 2px; }
    .help-text { font-size: 12px; color: #94a3b8; margin: -3px 0 7px; line-height: 1.5; font-weight: 400; }
    .q.has-error { background: #fef2f2; outline: 2px solid #fecaca; outline-offset: 6px; }
    .q .err { display: none; color: #dc2626; font-size: 12px; font-weight: 600; margin-top: 6px; }
    .q.has-error .err { display: block; }

    .form-control {
        width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 14px;
        font-family: inherit; background: rgba(255,255,255,.6); transition: border-color .2s, box-shadow .2s;
    }
    .form-control:focus { outline: none; border-color: var(--kiboko-blue); box-shadow: 0 0 0 4px rgba(0,154,218,.12); background: #fff; }
    textarea.form-control { resize: vertical; min-height: 100px; }
    select.form-control { appearance: auto; }
    .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 0 22px; }

    .choices { display: flex; flex-wrap: wrap; gap: 8px 22px; }
    .choices label { font-weight: 400; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 8px; }
    .choices input { width: 18px; height: 18px; accent-color: var(--kiboko-blue); cursor: pointer; flex-shrink: 0; }

    /* Agreement scale: a table on wide screens, buttons on phones */
    .likert-head, .likert-row { display: grid; grid-template-columns: minmax(0, 1fr) repeat(5, 84px); align-items: center; }
    .likert-head { font-size: 11px; font-weight: 700; color: #475569; text-align: center; text-transform: uppercase; letter-spacing: .3px; padding: 0 0 8px; }
    .likert-head div:first-child { text-align: left; }
    .likert-row { padding: 10px 8px; margin: 0 -8px; border-radius: 10px; }
    .likert-row:nth-child(even) { background: rgba(241, 245, 249, .7); }
    .likert-row .q-label { font-weight: 500; font-size: 14px; line-height: 1.5; padding-right: 12px; }
    .likert-row .opts { display: contents; }
    .likert-row .opt { display: flex; justify-content: center; cursor: pointer; }
    .likert-row .opt input { width: 20px; height: 20px; accent-color: var(--kiboko-blue); cursor: pointer; }
    .likert-row .opt span { display: none; }
    .likert-row.has-error { background: #fef2f2; outline: 2px solid #fecaca; outline-offset: 0; }
    .likert-row .err { grid-column: 1 / -1; }

    .hp { position: absolute; left: -10000px; width: 1px; height: 1px; overflow: hidden; }

    .btn-submit {
        display: block; width: 100%; max-width: 360px; margin: 10px auto 0; padding: 16px 30px; border: 0; border-radius: 50px;
        background: var(--grad); color: #fff; font-size: 16px; font-weight: 700; font-family: inherit; cursor: pointer;
        box-shadow: 0 6px 20px rgba(0,130,179,.3); transition: transform .2s, opacity .2s;
    }
    .btn-submit:hover { transform: translateY(-2px); }
    .btn-submit:disabled { opacity: .6; cursor: wait; transform: none; }
    .btn-link { background: none; border: 0; color: var(--kiboko-blue); font-weight: 600; font-family: inherit; font-size: 14px; cursor: pointer; text-decoration: underline; }

    .alert { padding: 14px 18px; border-radius: 12px; font-size: 14px; line-height: 1.6; margin-bottom: 18px; }
    .alert-error { background: #fef2f2; color: #991b1b; border-left: 5px solid #dc2626; }
    .alert-info { background: #eff6ff; color: #1e3a8a; border-left: 5px solid var(--kiboko-blue); }
    .center-box { text-align: center; padding: 40px 10px; }
    .center-box .icon { font-size: 56px; margin-bottom: 14px; }
    .center-box h2 { font-size: 22px; margin-bottom: 10px; }
    .center-box p { color: #475569; font-size: 15px; line-height: 1.7; max-width: 560px; margin: 0 auto; white-space: pre-line; }
    .loading { text-align: center; padding: 50px 0; color: #64748b; }
    .spinner { width: 36px; height: 36px; border: 4px solid #e2e8f0; border-top-color: var(--kiboko-blue); border-radius: 50%; margin: 0 auto 12px; animation: spin 1s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }
    .footer { text-align: center; font-size: 12px; color: #94a3b8; margin-top: 26px; }

    @media (max-width: 760px) {
        body { padding: 10px; }
        .container { padding: 26px 18px; border-radius: 20px; }
        .progress-wrap { margin: 0 -18px 20px; padding: 10px 18px; }
        .two-col { grid-template-columns: 1fr; }
        .likert-head { display: none; }
        .likert-row { display: block; padding: 12px 10px; margin: 0 -10px 4px; }
        .likert-row .q-label { margin-bottom: 10px; padding: 0; }
        .likert-row .opts { display: grid; grid-template-columns: 1fr; gap: 6px; }
        .likert-row .opt { justify-content: flex-start; align-items: center; gap: 10px; padding: 9px 12px; border: 2px solid #e2e8f0; border-radius: 10px; background: #fff; font-size: 14px; }
        .likert-row .opt span { display: inline; }
        .likert-row .opt:has(input:checked) { border-color: var(--kiboko-blue); background: #f0f9ff; }
    }
</style>
</head>
<body>
<div class="bg-wrapper"><div class="orb orb-1"></div><div class="orb orb-2"></div></div>

<div class="container">
    <div class="header">
        <img src="kibokogroup.png" alt="Kiboko Group" class="logo" onerror="this.style.display='none'">
        <h1 id="title">Satisfaction Form</h1>
        <p class="subtitle">Kiboko Enterprises Ltd • Kiboko Financial Services • Lucid</p>
    </div>
    <div id="app"><div class="loading"><div class="spinner"></div>Loading the survey…</div></div>
    <div class="footer">© Kiboko Group · Anonymous satisfaction form</div>
</div>

<noscript><p style="text-align:center;margin-top:20px">Please turn on JavaScript in your browser to fill in this form.</p></noscript>

<script>
(function () {
    'use strict';
    // Where the questions come from (K-Connect). Do not change this line's format.
    var KCONNECT_API = 'https://kconnect.kibokogroup.com/api/survey';

    var app = document.getElementById('app');
    var slug = new URLSearchParams(location.search).get('s') || '';
    var DONE_KEY = 'kiboko_survey_done_' + slug;   // only stays on this device; never sent anywhere
    var survey = null;

    function el(tag, attrs, children) {
        var e = document.createElement(tag);
        if (attrs) Object.keys(attrs).forEach(function (k) {
            if (k === 'text') e.textContent = attrs[k];
            else if (k === 'class') e.className = attrs[k];
            else e.setAttribute(k, attrs[k]);
        });
        (children || []).forEach(function (c) { if (c) e.appendChild(c); });
        return e;
    }
    function api() { return KCONNECT_API + '?s=' + encodeURIComponent(slug); }
    function message(icon, heading, text) {
        app.innerHTML = '';
        app.appendChild(el('div', { class: 'center-box' }, [
            el('div', { class: 'icon', text: icon }), el('h2', { text: heading }), el('p', { text: text })
        ]));
    }
    function storage(get, val) {
        try { return get ? localStorage.getItem(DONE_KEY) : localStorage.setItem(DONE_KEY, val); } catch (e) { return null; }
    }

    if (!slug) {
        message('🔗', 'Link not complete', 'This form needs the survey link you were given. Please open the full link from your email or message.');
        return;
    }

    fetch(api(), { method: 'GET', credentials: 'omit', cache: 'no-store', referrerPolicy: 'no-referrer' })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (!data.ok) {
                if (data.title) document.getElementById('title').textContent = data.title;
                message('📋', 'Survey not available', data.message || 'This survey is not available.');
                return;
            }
            survey = data.survey;
            document.title = survey.title + ' - Kiboko Group';
            document.getElementById('title').textContent = survey.title;
            if (storage(true)) { alreadyDone(); } else { render(); }
        })
        .catch(function () {
            message('⚠️', 'Could not load the survey', 'Please check your internet connection and reload the page.');
        });

    function alreadyDone() {
        app.innerHTML = '';
        var again = el('button', { class: 'btn-link', type: 'button', text: 'Answer again (for example on a shared computer)' });
        again.onclick = render;
        app.appendChild(el('div', { class: 'center-box' }, [
            el('div', { class: 'icon', text: '✅' }),
            el('h2', { text: 'You have already answered' }),
            el('p', { text: 'This device has already sent answers for this survey. Thank you!' }),
            el('div', { style: 'margin-top:18px' }, [again])
        ]));
    }

    function render() {
        app.innerHTML = '';
        app.appendChild(el('div', { class: 'anon-box' }, [
            el('strong', { text: '🔒 This form is anonymous' }),
            el('p', { text: 'We do not ask for your name and we do not record your email, IP address, device or the time you answer. Only your answers and the date are saved. Please be open and honest.' })
        ]));
        if (survey.intro) app.appendChild(el('div', { class: 'intro', text: survey.intro }));

        var bar = el('span'), count = el('span'), pct = el('span');
        app.appendChild(el('div', { class: 'progress-wrap' }, [
            el('div', { class: 'progress-label' }, [count, pct]), el('div', { class: 'progress' }, [bar])
        ]));

        var form = el('form', { novalidate: 'novalidate', autocomplete: 'off' });
        var errorBox = el('div', { class: 'alert alert-error', style: 'display:none', role: 'alert' });
        form.appendChild(errorBox);
        var all = [];

        survey.sections.forEach(function (sec) {
            var box = el('div', { class: 'section' });
            if (sec.title) box.appendChild(el('div', { class: 'section-title', text: sec.title }));
            var allLikert = sec.questions.every(function (q) { return q.type === 'likert'; });
            if (sec.questions.some(function (q) { return q.type === 'likert'; })) {
                var head = el('div', { class: 'likert-head' }, [el('div', { text: allLikert ? 'Statement' : '' })]);
                survey.likert_labels.forEach(function (l) { head.appendChild(el('div', { text: l })); });
                if (allLikert) box.appendChild(head);
            }
            var grid = null, prevLikert = false;
            sec.questions.forEach(function (q) {
                var node = question(q);
                all.push({ q: q, node: node });
                // Short bio-data answers sit two per row on wide screens
                var small = ['single', 'dropdown', 'text', 'number'].indexOf(q.type) >= 0 && (q.type !== 'single' || q.options.length <= 6);
                if (small) {
                    if (!grid) { grid = el('div', { class: 'two-col' }); box.appendChild(grid); }
                    grid.appendChild(node);
                } else {
                    grid = null;
                    if (q.type === 'likert' && !allLikert && !prevLikert) {
                        var h = el('div', { class: 'likert-head' }, [el('div')]);
                        survey.likert_labels.forEach(function (l) { h.appendChild(el('div', { text: l })); });
                        box.appendChild(h);
                    }
                    box.appendChild(node);
                }
                prevLikert = q.type === 'likert';
            });
            form.appendChild(box);
        });

        form.appendChild(el('div', { class: 'hp', 'aria-hidden': 'true' }, [
            el('label', { text: 'Leave this empty' }), el('input', { type: 'text', name: 'website', tabindex: '-1', autocomplete: 'off' })
        ]));
        var btn = el('button', { type: 'submit', class: 'btn-submit', text: 'Submit my answers anonymously' });
        form.appendChild(btn);
        app.appendChild(form);

        function progress() {
            var done = 0;
            all.forEach(function (a) { if (value(a).length) done++; });
            var p = all.length ? Math.round(done * 100 / all.length) : 0;
            bar.style.width = p + '%';
            count.textContent = done + ' of ' + all.length + ' answered';
            pct.textContent = p + '%';
        }
        form.addEventListener('change', progress);
        form.addEventListener('input', progress);
        progress();

        form.addEventListener('submit', function (ev) {
            ev.preventDefault();
            errorBox.style.display = 'none';
            var answers = {}, first = null;
            all.forEach(function (a) {
                var v = value(a);
                a.node.classList.remove('has-error');
                if (a.q.required && !v.length) {
                    setError(a, 'Please answer this question.');
                    first = first || a.node;
                }
                if (v.length) answers[a.q.id] = a.q.type === 'multiple' ? v : v[0];
            });
            if (first) {
                errorBox.textContent = 'Please answer the questions marked in red.';
                errorBox.style.display = 'block';
                first.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }
            btn.disabled = true;
            btn.textContent = 'Sending…';
            var hp = form.querySelector('[name=website]').value;
            fetch(api(), {
                method: 'POST', credentials: 'omit', cache: 'no-store', referrerPolicy: 'no-referrer',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ answers: answers, website: hp })
            })
                .then(function (r) { return r.json(); })
                .then(function (res) {
                    if (res.ok) {
                        storage(false, '1');
                        message('🎉', 'Thank you!', res.message || survey.thank_you);
                        window.scrollTo(0, 0);
                        return;
                    }
                    var firstErr = null;
                    Object.keys(res.errors || {}).forEach(function (id) {
                        var a = all.filter(function (x) { return String(x.q.id) === id; })[0];
                        if (a) { setError(a, res.errors[id]); firstErr = firstErr || a.node; }
                    });
                    errorBox.textContent = res.message || 'Your answers could not be sent.';
                    errorBox.style.display = 'block';
                    (firstErr || errorBox).scrollIntoView({ behavior: 'smooth', block: 'center' });
                    btn.disabled = false;
                    btn.textContent = 'Submit my answers anonymously';
                })
                .catch(function () {
                    errorBox.textContent = 'Your answers could not be sent. Please check your connection and press Submit again.';
                    errorBox.style.display = 'block';
                    errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    btn.disabled = false;
                    btn.textContent = 'Submit my answers anonymously';
                });
        });
    }

    function setError(a, text) {
        a.node.classList.add('has-error');
        a.node.querySelector('.err').textContent = text;
    }

    function value(a) {
        var q = a.q, n = a.node;
        if (q.type === 'likert' || q.type === 'single') {
            var c = n.querySelector('input:checked');
            return c ? [c.value] : [];
        }
        if (q.type === 'multiple') {
            return Array.prototype.map.call(n.querySelectorAll('input:checked'), function (i) { return i.value; });
        }
        var f = n.querySelector('input, select, textarea');
        return f && f.value.trim() !== '' ? [f.value.trim()] : [];
    }

    var uid = 0;
    function labelFor(q, id) {
        var l = el(id ? 'label' : 'div', id ? { for: id } : { class: 'q-label' }, [document.createTextNode(q.text)]);
        if (q.required) l.appendChild(el('span', { class: 'req', text: '*', 'aria-label': 'required' }));
        return l;
    }

    function question(q) {
        var name = 'q' + q.id, id = 'f' + (++uid);
        var help = q.help ? el('div', { class: 'help-text', text: q.help }) : null;
        var err = el('div', { class: 'err' });

        if (q.type === 'likert') {
            var row = el('div', { class: 'q likert-row', role: 'radiogroup' });
            var lab = el('div', { class: 'q-label' }, [document.createTextNode(q.text)]);
            if (q.required) lab.appendChild(el('span', { class: 'req', text: '*' }));
            if (help) lab.appendChild(help);
            row.appendChild(lab);
            var opts = el('div', { class: 'opts' });
            survey.likert_labels.forEach(function (l, i) {
                opts.appendChild(el('label', { class: 'opt', title: l }, [
                    el('input', { type: 'radio', name: name, value: String(i + 1), 'aria-label': l }),
                    el('span', { text: l })
                ]));
            });
            row.appendChild(opts);
            row.appendChild(err);
            return row;
        }

        var wrap = el('div', { class: 'q' });
        if (q.type === 'single' || q.type === 'multiple') {
            wrap.setAttribute('role', q.type === 'single' ? 'radiogroup' : 'group');
            wrap.appendChild(labelFor(q));
            if (help) wrap.appendChild(help);
            var ch = el('div', { class: 'choices' });
            q.options.forEach(function (o) {
                ch.appendChild(el('label', null, [
                    el('input', { type: q.type === 'single' ? 'radio' : 'checkbox', name: name, value: o }),
                    document.createTextNode(o)
                ]));
            });
            wrap.appendChild(ch);
        } else if (q.type === 'dropdown') {
            wrap.appendChild(labelFor(q, id));
            if (help) wrap.appendChild(help);
            var s = el('select', { id: id, name: name, class: 'form-control' }, [el('option', { value: '', text: 'Choose…' })]);
            q.options.forEach(function (o) { s.appendChild(el('option', { value: o, text: o })); });
            wrap.appendChild(s);
        } else if (q.type === 'textarea') {
            wrap.appendChild(labelFor(q, id));
            if (help) wrap.appendChild(help);
            wrap.appendChild(el('textarea', { id: id, name: name, class: 'form-control', maxlength: '5000', rows: '4' }));
        } else {
            wrap.appendChild(labelFor(q, id));
            if (help) wrap.appendChild(help);
            wrap.appendChild(el('input', q.type === 'number'
                ? { id: id, name: name, class: 'form-control', type: 'number', inputmode: 'decimal' }
                : { id: id, name: name, class: 'form-control', type: 'text', maxlength: '500' }));
        }
        wrap.appendChild(err);
        return wrap;
    }
})();
</script>
</body>
</html>
