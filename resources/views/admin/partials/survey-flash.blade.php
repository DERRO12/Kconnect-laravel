@php($flash = session('survey_flash'))
@if (is_array($flash) && count($flash) === 2)
    <div class="alert alert-{{ in_array($flash[0], ['success', 'danger', 'warning', 'info']) ? $flash[0] : 'info' }} alert-dismissible fade show">
        {{ $flash[1] }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
<script>
function kcCopy(id, btn) {
    var el = document.getElementById(id);
    el.select();
    (navigator.clipboard ? navigator.clipboard.writeText(el.value) : Promise.reject()).catch(function () { document.execCommand('copy'); });
    var old = btn.innerHTML; btn.innerHTML = '<i class="fas fa-check"></i> Copied';
    setTimeout(function () { btn.innerHTML = old; }, 1500);
}
</script>
