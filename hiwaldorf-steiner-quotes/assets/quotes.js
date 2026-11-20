(function () {
  'use strict';
  function nearest(el, selector) { return el && el.closest ? el.closest(selector) : null; }
  // Normalize GA labels for cards loaded via AJAX without touching stored gaid.
  function gaLabel(value) {
    var rest = String(value == null ? '' : value).trim().replace(/^(?:GA\s*)+/gi, '').trim();
    return rest ? 'GA ' + rest : '';
  }

  function share(card) {
    var url = card.getAttribute('data-quote-url');
    var quote = card.querySelector('.hw-steiner-quote');
    var content = quote ? quote.textContent : '';
    if (navigator.share) {
      navigator.share({title:'施泰纳语录', text:content, url:url}).catch(function() {});
    } else if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(url).then(function(){ window.alert('已复制语录链接'); }, function(){window.prompt('复制语录链接：',url);});
    } else { window.prompt('复制语录链接：',url); }
  }
  function change(card) {
    if (card.dataset.busy === '1') { return; }
    card.dataset.busy = '1'; card.classList.add('hw-steiner-changing');
    var form = new URLSearchParams({action:'hw_steiner_random', exclude:card.getAttribute('data-quote-id') || '0'});
    fetch(HWSteiner.ajax, {method:'POST',credentials:'same-origin',body:form,headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'}})
      .then(function(r){if(!r.ok)throw Error('request');return r.json();})
      .then(function(json){
        if(!json.success || !json.data)throw Error('missing');
        var data = json.data;
        card.style.setProperty('--hw-quote-bg',data.color);
        // The immersive reader uses the same tone for the full page and footer.
        if(card.classList.contains('hw-steiner-immersive')) {
          document.body.style.setProperty('--hw-steiner-scene', data.color);
        }
        card.setAttribute('data-quote-id', String(data.id));
        card.setAttribute('data-quote-url',data.url);
        card.querySelector('.hw-steiner-quote').textContent = data.content;
        var a = card.querySelector('.hw-steiner-link'); if(a)a.href=data.url;
        var ga = card.querySelector('.hw-steiner-ga');
        var label = gaLabel(data.gaid);
        if(label) {
          if(!ga){ga=document.createElement('p');ga.className='hw-steiner-ga';card.querySelector('.hw-steiner-author').after(ga);}
          ga.textContent=label;
        } else if(ga){ga.remove();}
        var toggle = card.querySelector('.hw-steiner-toggle');
        var original = card.querySelector('.hw-steiner-original');
        if(data.original){
          if(!toggle){ toggle=document.createElement('button');toggle.type='button';toggle.className='hw-steiner-toggle';card.querySelector('.hw-steiner-author').after(toggle); }
          toggle.textContent='查看原文';toggle.setAttribute('aria-expanded','false');
          if(!original){original=document.createElement('div');original.className='hw-steiner-original';toggle.after(original);}
          original.textContent=data.original; original.hidden=true;
        }else {if(toggle)toggle.remove();if(original)original.remove();}
        var status=card.querySelector('.hw-steiner-status'); if(status)status.textContent='已切换到新语录';
      })
      .catch(function(){ var status=card.querySelector('.hw-steiner-status');if(status)status.textContent='暂时无法切换，请稍后重试';})
      .finally(function(){card.dataset.busy='0';card.classList.remove('hw-steiner-changing');});
  }
  // A single quiet share-card design, 1080×1350, rendered once and cached on
  // the server when CJK-capable GD is present. Canvas is the portable fallback.
  var shareCardBusy = false;
  function cardTextWidth(ctx, text) { return ctx.measureText(text).width; }
  function wrapCardText(ctx, text, maxWidth) {
    var lines = [];
    String(text).replace(/\r\n?/g, '\n').trim().split('\n').forEach(function(p) {
      var line = '';
      Array.from(p).forEach(function(ch) {
        if (!line && /^\s$/.test(ch)) return;
        if (line && cardTextWidth(ctx, line + ch) > maxWidth) {
          lines.push(line.trim()); line = ch;
        } else {line += ch;}
      });
      if (line) lines.push(line.trim());
    });
    return lines;
  }
  function imageFromCanvas(card) {
    return new Promise(function(resolve, reject) {
      var canvas = document.createElement('canvas'); canvas.width = 1080; canvas.height = 1350;
      var ctx = canvas.getContext('2d');
      if (!ctx) { reject(Error('当前浏览器不支持图片生成')); return; }
      var quote = card.querySelector('.hw-steiner-quote');
      var text = quote ? quote.textContent.trim() : '';
      var color = getComputedStyle(card).getPropertyValue('--hw-quote-bg').trim() || '#466f70';
      ctx.fillStyle = color; ctx.fillRect(0, 0, 1080, 1350);
      ctx.fillStyle = '#fffefa'; ctx.textBaseline = 'alphabetic';
      ctx.font = '25px "Noto Serif CJK SC", "Songti SC", "SimSun", serif';
      ctx.textAlign = 'left'; ctx.fillText('施泰纳语录', 100, 113);
      var size, lines, step;
      for (size = 57; size >= 29; size -= 2) {
        ctx.font = size + 'px "Noto Serif CJK SC", "Songti SC", "SimSun", serif';
        lines = wrapCardText(ctx, text, 844);
        step = Math.round(size * 1.76);
        if (lines.length && lines.length <= 19 && lines.length * step <= 855) break;
      }
      if (size < 29) { reject(Error('这条语录较长，暂时无法完整排入单张卡片。请使用永久链接分享。')); return; }
      ctx.fillStyle = '#fffefa'; ctx.textAlign = 'center';
      var startY = Math.round(602 - lines.length * step / 2 + size);
      lines.forEach(function(line, index) { ctx.fillText(line, 540, startY + index * step); });
      ctx.font = '29px "Noto Serif CJK SC", "Songti SC", "SimSun", serif';
      ctx.fillText('鲁道夫·施泰纳', 540, 1090);
      var ga = card.querySelector('.hw-steiner-ga');
      if (ga && ga.textContent.trim()) {
        ctx.fillStyle = '#dce9e5'; ctx.font = '22px "Noto Serif CJK SC", "Songti SC", "SimSun", serif';
        ctx.fillText(ga.textContent.trim(), 540, 1138);
      }
      ctx.textAlign = 'right'; ctx.font = '24px Georgia, serif';
      ctx.fillStyle = '#dce9e5'; ctx.fillText('HiWaldorf', 980, 1272);
      canvas.toBlob(function(blob){ if(blob) resolve(blob); else reject(Error('无法导出 PNG')); }, 'image/png');
    });
  }
  function quoteCode(card) {
    var raw = card.getAttribute('data-quote-url') || '';
    try { return new URL(raw, window.location.href).pathname.replace(/\/$/, '').split('/').pop(); }
    catch (_) { return ''; }
  }
  function serverImage(card) {
    var code = quoteCode(card);
    if (!/^[a-zA-Z0-9_-]+$/.test(code)) return Promise.resolve(null);
    var form = new URLSearchParams({action: 'hw_steiner_card', code: code});
    return fetch(HWSteiner.ajax, {method:'POST', credentials:'same-origin', body:form,
      headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'}})
      .then(function(resp){if(!resp.ok) throw Error('server'); return resp.json();})
      .then(function(result){ return result && result.success && result.data && result.data.mode === 'server' ? result.data.url : null; })
      .catch(function(){return null;});
  }
  function shareImage(card) {
    if (shareCardBusy) return;
    shareCardBusy = true;
    var sourceId = card.getAttribute('data-quote-id');
    var sourceUrl = card.getAttribute('data-quote-url');
    var button = card.querySelector('.hw-steiner-image-share');
    if (button) {button.disabled = true;button.textContent = '生成中…';}
    var shade = document.createElement('div'); shade.className = 'hw-steiner-modal-shade';
    var panel = document.createElement('div'); panel.className = 'hw-steiner-modal';
    panel.setAttribute('role','dialog');panel.setAttribute('aria-modal','true');panel.setAttribute('aria-label','分享语录图片');
    var top = document.createElement('div'); top.className = 'hw-steiner-modal-top';
    var heading = document.createElement('strong'); heading.textContent = '分享图片';
    var close = document.createElement('button'); close.type = 'button';close.className = 'hw-steiner-modal-close';close.textContent = '关闭';
    var preview = document.createElement('img'); preview.className = 'hw-steiner-modal-preview'; preview.alt='施泰纳语录分享卡片预览';
    var notice = document.createElement('p');notice.className='hw-steiner-modal-note';notice.textContent='正在生成图片…';
    var actions = document.createElement('div');actions.className='hw-steiner-modal-actions';
    var download = document.createElement('a');download.textContent='保存图片';download.className='hw-steiner-modal-download';download.setAttribute('aria-disabled','true');
    var shareNative = document.createElement('button');shareNative.type='button';shareNative.textContent='发送图片';shareNative.hidden=true;
    actions.appendChild(download);actions.appendChild(shareNative);
    top.appendChild(heading);top.appendChild(close);panel.appendChild(top);panel.appendChild(preview);panel.appendChild(notice);panel.appendChild(actions);shade.appendChild(panel);document.body.appendChild(shade);
    var focusBefore = document.activeElement;
    var objectUrl = null, fileBlob = null, closed = false, fileName = 'steiner-quote-' + quoteCode(card) + '.png';
    function finish(){shareCardBusy=false;if(button){button.disabled=false;button.textContent='分享图片';}}
    function closeModal(){if(closed)return;closed=true;shade.remove();if(objectUrl)URL.revokeObjectURL(objectUrl);document.removeEventListener('keydown',onEscape);if(focusBefore && focusBefore.focus)focusBefore.focus();finish();}
    function onEscape(event){if(event.key==='Escape'){event.preventDefault();closeModal();}}
    document.addEventListener('keydown',onEscape);
    close.addEventListener('click',closeModal);shade.addEventListener('click',function(event){if(event.target===shade)closeModal();});
    close.focus();
    function setReady(url, blob) {
      if(closed)return;
      fileBlob=blob;
      preview.src=url;
      download.href=url;download.download=fileName;download.removeAttribute('aria-disabled');
      notice.textContent='1080 × 1350 PNG · 可保存后分享至社交平台';
      if(navigator.canShare && blob) {
        try{shareNative.hidden=!navigator.canShare({files:[new File([blob],fileName,{type:'image/png'})]});}catch(_){shareNative.hidden=true;}
      }
    }
    function asBlob(url) {
      return fetch(url, {credentials:'same-origin'}).then(function(resp){if(!resp.ok)throw Error('image fetch');return resp.blob();});
    }
    shareNative.addEventListener('click',function(){
      if(!fileBlob || !navigator.share)return;
      navigator.share({files:[new File([fileBlob],fileName,{type:'image/png'})],title:'施泰纳语录'}).catch(function(){});
    });
    serverImage(card).then(function(url){
      if (closed) return;
      // Protect against rapid random changes while the card is prepared.
      if(card.getAttribute('data-quote-id') !== sourceId || card.getAttribute('data-quote-url') !== sourceUrl) {
        throw Error('语录已切换，请重新生成图片');
      }
      if(url) return asBlob(url).then(function(blob){
        if(closed)return;
        objectUrl=URL.createObjectURL(blob);setReady(objectUrl,blob);
      }).catch(function(){return imageFromCanvas(card).then(function(blob){objectUrl=URL.createObjectURL(blob);setReady(objectUrl,blob);});});
      return imageFromCanvas(card).then(function(blob){if(closed)return;objectUrl=URL.createObjectURL(blob);setReady(objectUrl,blob);});
    }).catch(function(error){if(!closed){notice.textContent=error.message || '图片生成失败，请稍后重试';}})
      .finally(function(){if(!closed)finish();});
  }
  document.addEventListener('click',function(event){
    var card=nearest(event.target,'.hw-steiner-card');if(!card)return;
    var toggle=nearest(event.target,'.hw-steiner-toggle');
    if(toggle){var orig=card.querySelector('.hw-steiner-original');if(orig){orig.hidden=!orig.hidden;toggle.textContent=orig.hidden?'查看原文':'收起原文';toggle.setAttribute('aria-expanded',String(!orig.hidden));}return;}
    if(nearest(event.target,'.hw-steiner-share')){share(card);return;}
    if(nearest(event.target,'.hw-steiner-image-share')){shareImage(card);return;}
    if(nearest(event.target,'a,button,.hw-steiner-copy'))return;
    if(card.classList.contains('hw-steiner-immersive'))change(card);
  });
  document.addEventListener('keydown',function(event){
    if(event.code!=='Space'||event.repeat||event.altKey||event.ctrlKey||event.metaKey)return;
    if(nearest(event.target,'input,textarea,select,button,a,[contenteditable="true"]'))return;
    var card=document.querySelector('.hw-steiner-immersive');if(!card)return;
    event.preventDefault();change(card);
  });
})();