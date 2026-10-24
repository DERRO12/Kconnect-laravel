/*
 * K-Academy lesson videos: sends a chosen video to academy/admin/upload-video-chunk in 4MB pieces, with a
 * progress bar, then submits the lesson form with the saved file name. Long and heavy videos upload this way
 * whatever the server's upload_max_filesize is. Forms opt in with data-ka-video-upload; the file input is
 * [data-ka-video] (one video) or [data-ka-videos] (several, one lesson each).
 * The K-Connect page snippet (InjectCsrfToken) adds the X-CSRF-TOKEN header to each piece.
 */
(function () {
    var CHUNK = 4 * 1024 * 1024;
    var endpoint = document.querySelector('meta[name="ka-chunk-url"]');

    function mb(bytes) { return (bytes / 1048576).toFixed(1) + ' MB'; }

    function randomId() {
        var s = '';
        while (s.length < 32) s += Math.random().toString(36).slice(2);
        return s.replace(/[^a-z0-9]/g, '').slice(0, 32);
    }

    function sendPiece(file, id, offset) {
        return new Promise(function (resolve, reject) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', endpoint.content);
            xhr.setRequestHeader('Content-Type', 'application/octet-stream');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('X-Upload-Id', id);
            xhr.setRequestHeader('X-Upload-Offset', offset);
            xhr.setRequestHeader('X-Upload-Total', file.size);
            xhr.setRequestHeader('X-Upload-Name', encodeURIComponent(file.name));
            xhr.onload = function () {
                var data = {};
                try { data = JSON.parse(xhr.responseText); } catch (e) {}
                if (xhr.status === 200 || xhr.status === 409) resolve(data);
                else reject(new Error(data.error || (xhr.status === 419 ? 'Your session expired. Reload the page and try again.' : 'Upload failed (' + xhr.status + ')')));
            };
            xhr.onerror = function () { reject(new Error('Network error')); };
            xhr.send(file.slice(offset, Math.min(offset + CHUNK, file.size)));
        });
    }

    // Sends one file; onProgress(bytesSent). Resolves with the saved file name.
    function upload(file, onProgress) {
        var id = randomId();
        var offset = 0;
        var tries = 0;
        function next() {
            return sendPiece(file, id, offset).then(function (data) {
                tries = 0;
                if (data.done) { onProgress(file.size); return data.filename; }
                offset = data.offset || 0;
                onProgress(offset);
                return next();
            }, function (err) {
                // Retry a failed piece a few times (flaky Wi-Fi) before giving up
                if (++tries > 4 || /not allowed|exceeds|session/i.test(err.message)) throw err;
                return new Promise(function (r) { setTimeout(r, 1500 * tries); }).then(next);
            });
        }
        return next();
    }

    function progressBox(input) {
        var box = input.parentNode.querySelector('.ka-upload-progress');
        if (!box) {
            box = document.createElement('div');
            box.className = 'ka-upload-progress mt-2';
            box.innerHTML = '<div class="progress" style="height:10px"><div class="progress-bar progress-bar-striped progress-bar-animated" style="width:0%"></div></div>' +
                '<div class="small text-secondary mt-1 ka-upload-text"></div>';
            input.parentNode.appendChild(box);
        }
        box.classList.remove('d-none');
        return {
            set: function (pct, text) {
                box.querySelector('.progress-bar').style.width = pct + '%';
                box.querySelector('.ka-upload-text').textContent = text;
            },
            fail: function (text) {
                box.querySelector('.progress-bar').classList.add('bg-danger');
                box.querySelector('.progress-bar').classList.remove('progress-bar-animated');
                box.querySelector('.ka-upload-text').innerHTML = '<span class="text-danger">' + text + '</span>';
            },
            reset: function () {
                box.querySelector('.progress-bar').classList.remove('bg-danger');
                box.querySelector('.progress-bar').classList.add('progress-bar-animated');
            }
        };
    }

    function hidden(form, name, value) {
        var el = document.createElement('input');
        el.type = 'hidden';
        el.name = name;
        el.value = value;
        el.className = 'ka-uploaded-field';
        form.appendChild(el);
    }

    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form.hasAttribute || !form.hasAttribute('data-ka-video-upload') || form.dataset.kaReady === '1' || !endpoint) return;
        var single = form.querySelector('[data-ka-video]');
        var multi = form.querySelector('[data-ka-videos]');
        var input = multi || single;
        var type = form.querySelector('[name="video_type"]');
        if (!input || !input.files.length || (type && type.value !== 'upload')) return;

        e.preventDefault();
        var files = Array.prototype.slice.call(input.files);
        var max = parseInt(input.getAttribute('data-max-mb') || '0', 10) * 1048576;
        var tooBig = files.filter(function (f) { return max && f.size > max; });
        var box = progressBox(input);
        box.reset();
        if (tooBig.length) {
            box.fail(tooBig.map(function (f) { return f.name; }).join(', ') + ' is larger than ' + mb(max) + '.');
            return;
        }

        var button = form.querySelector('[type=submit]');
        var label = button ? button.innerHTML : '';
        if (button) { button.disabled = true; button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Uploading...'; }
        form.querySelectorAll('.ka-uploaded-field').forEach(function (el) { el.remove(); });
        window.onbeforeunload = function () { return 'The video is still uploading.'; };

        var total = files.reduce(function (s, f) { return s + f.size; }, 0);
        var done = 0;
        var chain = Promise.resolve();
        files.forEach(function (file, i) {
            chain = chain.then(function () {
                return upload(file, function (sent) {
                    var pct = total ? Math.floor((done + sent) / total * 100) : 100;
                    box.set(pct, (files.length > 1 ? 'Video ' + (i + 1) + ' of ' + files.length + ': ' : '') +
                        file.name + ' - ' + mb(done + sent) + ' of ' + mb(total) + ' (' + pct + '%)');
                }).then(function (saved) {
                    done += file.size;
                    if (multi) { hidden(form, 'uploaded_videos[]', saved); hidden(form, 'uploaded_names[]', file.name); }
                    else hidden(form, 'uploaded_video', saved);
                });
            });
        });
        chain.then(function () {
            window.onbeforeunload = null;
            box.set(100, 'Upload complete. Saving the lesson' + (files.length > 1 ? 's' : '') + '...');
            input.required = false;
            input.disabled = true; // the video is already on the server; do not send it again
            form.dataset.kaReady = '1';
            form.submit();
        }, function (err) {
            window.onbeforeunload = null;
            box.fail('Upload failed: ' + err.message);
            if (button) { button.disabled = false; button.innerHTML = label; }
        });
    }, true);
})();
