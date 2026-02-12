@php
    $flashSuccess = session('success') ?? session('status');
    $flashError = session('error');
    $flashWarning = session('warning');
    $flashInfo = session('info');
    $flashErrors = $errors?->any() ? $errors->all() : [];
@endphp

<style>
  .app-notice-stack{
    position:fixed;
    top:50%;
    left:50%;
    transform: translate(-50%,-50%);
    z-index:2000;
    display:flex;
    flex-direction:column;
    gap:.85rem;
    width:min(560px, calc(100vw - 2rem));
    align-items:stretch;
  }

  .app-notice{
    background:#fff;
    border:1px solid rgba(0,0,0,.08);
    box-shadow:0 22px 70px rgba(0,0,0,.22);
    border-radius:1.25rem;
    overflow:hidden;
    display:flex;
    gap:1rem;
    padding:1.05rem 1.15rem;
    align-items:flex-start;
    transform: translateY(16px) scale(.96);
    opacity:0;
    transition: transform .22s cubic-bezier(.2,.9,.2,1), opacity .22s ease;
  }
  .app-notice.show{transform: translateY(0) scale(1); opacity:1;}

  .app-notice__icon{
    width:64px;
    height:64px;
    border-radius:999px;
    display:flex;
    align-items:center;
    justify-content:center;
    flex:0 0 auto;
    margin-top:.05rem;
  }
  .app-notice__body{min-width:0; flex:1 1 auto;}
  .app-notice__title{font-weight:800; font-size:1.05rem; line-height:1.15; margin:0; letter-spacing:.01em;}
  .app-notice__msg{font-size:1rem; margin:.15rem 0 0; color: rgba(0,0,0,.74); word-break: break-word;}
  .app-notice__close{border:0; background:transparent; color:rgba(0,0,0,.5); padding:.25rem .35rem; margin-left:auto; line-height:1;}
  .app-notice__close:hover{color:rgba(0,0,0,.85)}

  .app-notice--success .app-notice__icon{background: rgba(25,135,84,.12); color:#198754;}
  .app-notice--error .app-notice__icon{background: rgba(220,53,69,.12); color:#dc3545;}
  .app-notice--warning .app-notice__icon{background: rgba(255,193,7,.16); color:#b58100;}
  .app-notice--info .app-notice__icon{background: rgba(13,110,253,.12); color:#0d6efd;}

  .app-notice--success{border-color: rgba(25,135,84,.18)}
  .app-notice--error{border-color: rgba(220,53,69,.18)}
  .app-notice--warning{border-color: rgba(255,193,7,.22)}
  .app-notice--info{border-color: rgba(13,110,253,.18)}

  /* PhonePe-style animated SVG icons */
  .app-notice__svg{width:34px; height:34px; display:block; filter: drop-shadow(0 10px 14px rgba(0,0,0,.08));}
  .app-notice__svg path, .app-notice__svg circle, .app-notice__svg line{
    fill:none;
    stroke:currentColor;
    stroke-width:3.2;
    stroke-linecap:round;
    stroke-linejoin:round;
  }
  .app-notice__svg .ring{stroke-dasharray: 220; stroke-dashoffset: 220; animation: appDraw .42s cubic-bezier(.2,.95,.3,1) forwards;}
  .app-notice__svg .mark{stroke-dasharray: 90; stroke-dashoffset: 90; animation: appDraw .30s cubic-bezier(.2,.95,.3,1) .18s forwards;}
  @keyframes appDraw{to{stroke-dashoffset:0;}}

  .app-notice--warning .app-notice__icon{position:relative;}
  .app-notice--warning .app-notice__svg{animation: appPop .22s ease-out both;}
  .app-notice--success .app-notice__svg,
  .app-notice--error .app-notice__svg,
  .app-notice--info .app-notice__svg{animation: appPop .22s ease-out both;}
  @keyframes appPop{0%{transform:scale(.86);} 70%{transform:scale(1.06);} 100%{transform:scale(1);}}

  /* Reduced motion */
  @media (prefers-reduced-motion: reduce){
    .app-notice{transition:none}
    .app-notice__svg .ring, .app-notice__svg .mark{animation:none; stroke-dashoffset:0;}
    .app-notice--warning .app-notice__svg{animation:none;}
  }
</style>

<div id="appNoticeStack" class="app-notice-stack" aria-live="polite" aria-relevant="additions"></div>

<!-- Global confirm modal (single system-wide confirm) -->
<div class="modal fade" id="appConfirmModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="appConfirmTitle">Please confirm</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="appConfirmBody">Are you sure?</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="appConfirmOk">Confirm</button>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  if(window.AppNotice) return;

  const stack = document.getElementById('appNoticeStack');
  function iconFor(type){
    if(type==='success'){
      return `
        <svg class="app-notice__svg" viewBox="0 0 32 32" aria-hidden="true">
          <circle class="ring" cx="16" cy="16" r="12"></circle>
          <path class="mark" d="M10 16.5l4 4 8-9"></path>
        </svg>
      `;
    }
    if(type==='error'){
      return `
        <svg class="app-notice__svg" viewBox="0 0 32 32" aria-hidden="true">
          <circle class="ring" cx="16" cy="16" r="12"></circle>
          <path class="mark" d="M12 12l8 8"></path>
          <path class="mark" d="M20 12l-8 8"></path>
        </svg>
      `;
    }
    if(type==='warning'){
      return `
        <svg class="app-notice__svg" viewBox="0 0 32 32" aria-hidden="true">
          <circle class="ring" cx="16" cy="16" r="12"></circle>
          <path class="mark" d="M16 9v10"></path>
          <path class="mark" d="M16 23h0"></path>
        </svg>
      `;
    }
    return `
      <svg class="app-notice__svg" viewBox="0 0 32 32" aria-hidden="true">
        <circle class="ring" cx="16" cy="16" r="12"></circle>
        <path class="mark" d="M16 14v9"></path>
        <path class="mark" d="M16 10h0"></path>
      </svg>
    `;
  }
  function titleFor(type){
    if(type==='success') return 'Success';
    if(type==='warning') return 'Warning';
    if(type==='error') return 'Error';
    return 'Info';
  }

  window.AppNotice = {
    show(type, message, opts){
      opts = opts || {};
      const timeout = Number.isFinite(opts.timeout) ? opts.timeout : (type === 'error' ? 4200 : 2600);
      const node = document.createElement('div');
      node.className = `app-notice app-notice--${type||'info'}`;
      node.innerHTML = `
        <div class="app-notice__icon">${iconFor(type)}</div>
        <div class="app-notice__body">
          <p class="app-notice__title">${opts.title || titleFor(type)}</p>
          <p class="app-notice__msg"></p>
        </div>
        <button class="app-notice__close" type="button" aria-label="Close">&times;</button>
      `;
      node.querySelector('.app-notice__msg').textContent = String(message||'');
      const closeBtn = node.querySelector('.app-notice__close');
      let timer = null;

      function remove(){
        if(timer) clearTimeout(timer);
        node.classList.remove('show');
        setTimeout(()=> node.remove(), 180);
      }

      closeBtn.addEventListener('click', remove);
      stack.appendChild(node);
      requestAnimationFrame(()=> node.classList.add('show'));
      if(timeout > 0){ timer = setTimeout(remove, timeout); }
      return { remove };
    }
  };

  // Back-compat: route all existing toastr.* calls into AppNotice
  window.toastr = window.toastr || {};
  ['success','error','warning','info'].forEach(function(k){
    window.toastr[k] = function(msg){ window.AppNotice.show(k, msg); };
  });

  // Single confirm system: intercept any form with data-confirm
  let pendingConfirm = null;
  const modalEl = document.getElementById('appConfirmModal');
  const okBtn = document.getElementById('appConfirmOk');
  const titleEl = document.getElementById('appConfirmTitle');
  const bodyEl = document.getElementById('appConfirmBody');
  const modal = modalEl && window.bootstrap ? new bootstrap.Modal(modalEl) : null;

  document.addEventListener('submit', function(e){
    const form = e.target;
    if(!(form instanceof HTMLFormElement)) return;
    const msg = form.getAttribute('data-confirm');
    if(!msg || !modal) return;
    e.preventDefault();
    pendingConfirm = { form };
    titleEl.textContent = form.getAttribute('data-confirm-title') || 'Please confirm';
    bodyEl.textContent = msg;
    okBtn.className = 'btn ' + (form.getAttribute('data-confirm-ok-class') || 'btn-danger');
    okBtn.textContent = form.getAttribute('data-confirm-ok-text') || 'Confirm';
    modal.show();
  });

  okBtn?.addEventListener('click', async function(){
    if(!pendingConfirm) return;
    const form = pendingConfirm.form;
    pendingConfirm = null;
    modal?.hide();

    if(form.hasAttribute('data-ajax-delete')){
      try{
        const fd = new FormData(form);
        const res = await fetch(form.action, {
          method: 'POST',
          headers: {
            'X-Requested-With':'XMLHttpRequest',
            'X-CSRF-TOKEN': fd.get('_token') || document.querySelector('meta[name="csrf-token"]')?.content,
            'Accept':'application/json'
          },
          body: fd,
          credentials:'same-origin'
        });
        const data = await res.json().catch(()=> ({}));
        if(!res.ok || data.ok === false){
          throw new Error(data.message || 'Delete failed');
        }
        const rowSel = form.getAttribute('data-row-selector');
        if(rowSel){
          const row = document.querySelector(rowSel);
          row?.remove();
        }
        window.AppNotice.show('success', data.message || 'Deleted');
      }catch(err){
        window.AppNotice.show('error', err.message || 'Delete failed');
      }
      return;
    }

    form.submit();
  });
})();
</script>

<script>
(function(){
  function push(type, msg){
    if(!msg) return;
    if(window.AppNotice) window.AppNotice.show(type, msg);
  }

  @if($flashSuccess)
    push('success', @json($flashSuccess));
  @endif
  @if($flashError)
    push('error', @json($flashError));
  @endif
  @if($flashWarning)
    push('warning', @json($flashWarning));
  @endif
  @if($flashInfo)
    push('info', @json($flashInfo));
  @endif
  @if(!empty($flashErrors))
    @foreach($flashErrors as $e)
      push('error', @json($e));
    @endforeach
  @endif
})();
</script>
