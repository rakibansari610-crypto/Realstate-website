<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>vServeAI — Property Assistant</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial; margin:0; background:#f6f7f9;}
    .wrap{max-width:980px; margin:0 auto; padding:18px;}
    .header{display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; gap:12px;}
    .title{font-size:18px; font-weight:800;}
    .sub{font-size:13px; color:#666;}
    .tools{display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end;}
    .toolbtn{
      background:#111; color:#fff; border:0; border-radius:12px; padding:10px 12px;
      font-weight:800; cursor:pointer; font-size:13px;
    }
    .toolbtn.secondary{background:#fff; color:#111; border:1px solid #ddd;}
    .toolbtn.danger{background:#b00020;}

    .chat{background:#fff; border:1px solid #e6e6e6; border-radius:16px; height:72vh; overflow:auto; padding:14px;}
    .msg{max-width:86%; padding:10px 12px; border-radius:14px; margin:8px 0; line-height:1.35;}
    .me{margin-left:auto; background:#111; color:#fff; border-bottom-right-radius:6px;}
    .bot{margin-right:auto; background:#f2f2f2; color:#111; border-bottom-left-radius:6px;}
    .name{font-size:12px; opacity:.75; margin-bottom:6px; font-weight:700;}
    .row{display:flex; gap:10px; margin-top:12px;}
    input{flex:1; padding:12px 14px; border:1px solid #ddd; border-radius:14px; background:#fff;}
    button{padding:12px 14px; border:0; border-radius:14px; cursor:pointer; background:#111; color:#fff; font-weight:700;}
    .divider{height:1px; background:#eee; margin:10px 0;}
    .smallbtn{background:#0f62fe; color:#fff; padding:10px 12px; border-radius:12px; font-size:13px; font-weight:800;}
    .ok{color:#0a7a2f; font-size:13px;}
    .err{color:#b00020; font-size:13px;}

    /* Lead card input styles (inside chat) */
    .leadCard input{
      width:100%;
      margin:6px 0;
      padding:10px 12px;
      border:1px solid #ddd;
      border-radius:12px;
      background:#fff;
      box-sizing:border-box;
    }

    /* highlight animation when user clicks Enquire again */
    .pulse{
      outline: 2px solid rgba(15, 98, 254, 0.45);
      animation: pulse 1.2s ease-in-out 0s 2;
    }
    @keyframes pulse{
      0%{transform:scale(1); box-shadow:0 0 0 rgba(15,98,254,0);}
      50%{transform:scale(1.01); box-shadow:0 0 14px rgba(15,98,254,0.25);}
      100%{transform:scale(1); box-shadow:0 0 0 rgba(15,98,254,0);}
    }

    .hidden{display:none !important;}

    @media (max-width:720px){
      .msg{max-width:96%;}
      .tools{justify-content:flex-start;}
    }
  </style>
</head>

<body>
<div class="wrap">
  <div class="header">
    <div>
      <div class="title">vServeAI — Property Agent</div>
      <div class="sub">Try: “Dubai under 1M”, “2BR apartment in JVC ready”, “Mumbai 2BHK under 2Cr”.</div>
    </div>

    <div class="tools">
      <button class="toolbtn secondary" onclick="downloadChat()">Download</button>
      <button class="toolbtn secondary" id="closeBtn" onclick="toggleChat()">Close</button>
      <button class="toolbtn danger" onclick="resetChat()">Reset</button>
    </div>
  </div>

  <div id="chatWrap">
    <div id="chat" class="chat">
      <div class="msg bot" data-static="1">
        <div class="name">vServeAI</div>
        Welcome dear 👋 Tell me your <b>city/community</b> to start.<br>
        Examples: Dubai Marina, JVC, Dubai Hills, Delhi, Mumbai, Bangalore.
      </div>
    </div>

    <div class="row" id="inputRow">
      <input id="input" type="text" placeholder="Type your requirement…">
      <button onclick="sendMsg()">Send</button>
    </div>
  </div>
</div>

<script>
let lastIntent = null;
let lastTopProjectId = null;
let lastMessage = null;
let isSending = false;

const LEAD_CARD_KEY = 'vserveai_lead_card_submitted';
const CHAT_HTML_KEY = 'vserveai_chat_html';
const CHAT_CLOSED_KEY = 'vserveai_chat_closed';

function saveChat(){
  const chat = document.getElementById('chat');
  sessionStorage.setItem(CHAT_HTML_KEY, chat.innerHTML);
}

function restoreChat(){
  const saved = sessionStorage.getItem(CHAT_HTML_KEY);
  if(saved){
    document.getElementById('chat').innerHTML = saved;
    scrollToBottom();
  }

  // restore closed state
  const closed = sessionStorage.getItem(CHAT_CLOSED_KEY) === '1';
  if(closed) setChatClosed(true);
}

function scrollToBottom(){
  const chat = document.getElementById('chat');
  chat.scrollTop = chat.scrollHeight;
}

function appendMsg(text, who='bot', extraHtml=null){
  const chat = document.getElementById('chat');
  const el = document.createElement('div');
  el.className = 'msg ' + (who === 'me' ? 'me' : 'bot');

  const safe = (text || '')
    .replace(/\*\*(.*?)\*\*/g, '<b>$1</b>')
    .replace(/\n/g, '<br>');

  if(who === 'bot'){
    el.innerHTML = `<div class="name">vServeAI</div>` + safe + (extraHtml ? extraHtml : '');
  } else {
    el.innerHTML = safe;
  }

  chat.appendChild(el);
  scrollToBottom();
  saveChat();
  return el;
}

function setTyping(show=true){
  const existing = document.getElementById('typingIndicator');
  if(show){
    if(existing) return;
    const el = document.createElement('div');
    el.id = 'typingIndicator';
    el.className = 'msg bot';
    el.innerHTML = `<div class="name">vServeAI</div>Typing…`;
    document.getElementById('chat').appendChild(el);
    scrollToBottom();
  } else {
    if(existing) existing.remove();
  }
  saveChat();
}

function cardsHtml(results){
  if(!results || !results.length) return '';
  let html = '<div class="divider"></div>';

  for(const r of results){
    const loc = r.location ? r.location : (r.city || '');
    const price = r.min_price ? `${formatNum(r.min_price)} ${escapeHtml(r.currency || '')}` : 'Price on request';

    html += `
      <div style="border:1px solid #eee; border-radius:14px; padding:12px; background:#fff; margin-top:10px;">
        <div style="font-weight:800; font-size:15px;">${escapeHtml(r.name || '')}</div>
        <div style="font-size:13px; color:#444; margin-top:4px;">
          ${escapeHtml(loc)}${r.city ? ' · ' + escapeHtml(r.city) : ''}
        </div>
        <div style="font-size:13px; color:#444; margin-top:6px;">
          <b>From:</b> ${price}
        </div>
        <div style="margin-top:10px;">
          <button class="smallbtn" onclick="selectProject(${r.project_id})">Enquire</button>
        </div>
      </div>
    `;
  }
  return html;
}

/** Lead Card **/
function leadCardHtml(){
  return `
    <div class="leadCard" style="margin-top:10px; border:1px dashed #ddd; padding:12px; border-radius:14px; background:#fbfbfb;">
      <div style="font-weight:800; margin-bottom:6px;">Want a call back?</div>
      <div class="sub" style="margin-bottom:10px;">Share your contact — we’ll follow up with payment plan + availability.</div>

      <input class="leadName" type="text" placeholder="Name">
      <input class="leadPhone" type="text" placeholder="Phone">
      <input class="leadEmail" type="text" placeholder="Email (optional)">

      <button class="smallbtn" onclick="submitLeadFromChat(this)">Submit</button>
      <div class="leadStatus" style="margin-top:8px;"></div>
    </div>
  `;
}

function updateDetailsCardHtml(){
  return `
    <div class="leadUpdateCard" style="margin-top:10px; border:1px solid #e6e6e6; padding:12px; border-radius:14px; background:#fff;">
      <div style="font-weight:800;">✅ We already have your details</div>
      <div style="font-size:13px; color:#444; margin-top:6px;">
        Want to update your phone/email for this enquiry?
      </div>
      <div style="margin-top:10px;">
        <button class="smallbtn" onclick="reopenLeadForm()">Update details</button>
      </div>
    </div>
  `;
}

function hasLeadCard(){
  return document.querySelector('.leadCard') !== null;
}

function leadAlreadySubmitted(){
  return sessionStorage.getItem(LEAD_CARD_KEY) === '1';
}

function focusLeadCard(){
  const card = document.querySelector('.leadCard');
  if(!card) return;
  card.scrollIntoView({behavior:'smooth', block:'center'});
  card.classList.add('pulse');
  setTimeout(()=>card.classList.remove('pulse'), 2600);
}

function showLeadCardInChat(){
  // If already submitted, show update option instead of blocking completely
  if(leadAlreadySubmitted()){
    appendMsg("Noted ✅ For this enquiry, do you want to update your contact details?", "bot", updateDetailsCardHtml());
    return;
  }

  // If card exists, just focus it
  if(hasLeadCard()){
    appendMsg("Perfect ✅ Please fill the callback form above and hit Submit.", "bot");
    focusLeadCard();
    return;
  }

  // Otherwise insert a new card
  appendMsg(
    "Great ✅ If you like, share your details and I’ll arrange the call back.",
    "bot",
    leadCardHtml()
  );
}

function reopenLeadForm(){
  // allow showing again
  sessionStorage.removeItem(LEAD_CARD_KEY);

  // remove any old lead cards / update cards (optional cleanup)
  document.querySelectorAll('.leadUpdateCard').forEach(el => el.remove());

  // If a leadCard exists but collapsed content is there, remove and re-add fresh
  document.querySelectorAll('.leadCard').forEach(el => el.remove());

  appendMsg("Sure ✅ Please share your updated details below:", "bot", leadCardHtml());
  saveChat();
  setTimeout(focusLeadCard, 200);
}

function selectProject(projectId){
  lastTopProjectId = projectId;
  showLeadCardInChat();
}

/** Fetch helpers **/
async function safeJson(res){
  const contentType = res.headers.get('content-type') || '';
  if(!contentType.includes('application/json')){
    const txt = await res.text();
    return { __nonJson: true, text: txt };
  }
  return await res.json();
}

async function sendMsg(){
  if(isSending) return;

  const input = document.getElementById('input');
  const msg = (input.value || '').trim();
  if(!msg) return;

  lastMessage = msg;
  appendMsg(msg, 'me');
  input.value = '';

  isSending = true;
  setTyping(true);

  try{
    const res = await fetch("{{ route('ai.chat.message') }}", {
      method:'POST',
      headers:{
        'Content-Type':'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Accept': 'application/json'
      },
      body: JSON.stringify({message: msg})
    });

    setTyping(false);

    if(!res.ok){
      const body = await res.text();
      console.error("Chat request failed", res.status, body);
      appendMsg(res.status === 419 ? "Session expired. Refresh and try again." : `Server error (${res.status}). Check logs.`, 'bot');
      return;
    }

    const data = await safeJson(res);
    if(data.__nonJson){
      console.error("Non-JSON response:", data.text);
      appendMsg("Unexpected response. Check server logs.", 'bot');
      return;
    }

    if(!data.ok){
      appendMsg("Sorry dear — I couldn’t process that. Please try again.", 'bot');
      console.error("API ok=false:", data);
      return;
    }

    lastIntent = data.intent || null;
    if(data.results && data.results.length) lastTopProjectId = data.results[0].project_id;

    const extra = (data.show_cards && data.results && data.results.length)
      ? cardsHtml(data.results)
      : '';

    appendMsg(data.reply || "Okay dear ✅", 'bot', extra);

    if(data.suggest_capture){
      showLeadCardInChat();
    }

  }catch(e){
    setTyping(false);
    console.error("Chat error:", e);
    appendMsg("Network error. Please try again.", 'bot');
  }finally{
    isSending = false;
  }
}

/**
 * Submit lead and collapse.
 * After success: sets LEAD_CARD_KEY (so we show update option for next enquiries)
 */
async function submitLeadFromChat(btn){
  const leadCard = btn.closest('.leadCard');
  const status = leadCard.querySelector('.leadStatus');
  status.innerHTML = '';

  const name = (leadCard.querySelector('.leadName').value || '').trim();
  const phone = (leadCard.querySelector('.leadPhone').value || '').trim();
  const email = (leadCard.querySelector('.leadEmail').value || '').trim();

  if(!name || !phone){
    status.innerHTML = `<div class="err">Name and phone are required.</div>`;
    return;
  }

  const filters = lastIntent?.filters || null;

  const payload = {
    name,
    phone,
    email: email || null,
    project_id: lastTopProjectId || null,
    preferred_bhk: filters?.bedrooms != null ? String(filters.bedrooms) : null,
    budget_max: filters?.max_price ?? null,
    property_type: filters?.property_type ?? null,
    property_build: filters?.timing === 'off_plan' ? 'off_plan' : (filters?.timing === 'ready' ? 'ready' : null),
    notes: `AI Chat. Query: ${lastMessage || ''}`
  };

  btn.disabled = true;
  btn.textContent = 'Submitting...';

  try{
    const res = await fetch("{{ route('ai.chat.lead') }}", {
      method:'POST',
      headers:{
        'Content-Type':'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Accept': 'application/json'
      },
      body: JSON.stringify(payload)
    });

    if(!res.ok){
      const bodyText = await res.text();
      console.error("Lead submit failed", res.status, bodyText);

      let msg = `Server error (${res.status}).`;
      try{
        const maybe = JSON.parse(bodyText);
        if(maybe?.errors){
          msg = Object.values(maybe.errors).flat().join('<br>');
        } else if(maybe?.error){
          msg = maybe.error;
        } else if(maybe?.message){
          msg = maybe.message;
        }
      }catch(e){
        msg = bodyText?.slice(0, 500) || msg;
      }

      status.innerHTML = `<div class="err">${msg}</div>`;
      btn.disabled = false;
      btn.textContent = 'Submit';
      saveChat();
      return;
    }

    const data = await safeJson(res);

    if(data.ok){
      sessionStorage.setItem(LEAD_CARD_KEY, '1');

      leadCard.innerHTML = `
        <div class="ok" style="font-weight:800;">Submitted ✅</div>
        <div style="font-size:13px; color:#444; margin-top:6px;">
          Thanks ${escapeHtml(name)}! We’ll contact you shortly with availability + payment plan.
        </div>
      `;

      appendMsg("Done ✅ I’ve received your details. I’ll share the options/payment plan soon.", "bot");
      saveChat();
    } else {
      status.innerHTML = `<div class="err">Failed to submit.</div>`;
      btn.disabled = false;
      btn.textContent = 'Submit';
      saveChat();
    }
  }catch(e){
    console.error("Lead submit error:", e);
    status.innerHTML = `<div class="err">Network error. Try again.</div>`;
    btn.disabled = false;
    btn.textContent = 'Submit';
    saveChat();
  }
}

/** Buttons: Reset / Close / Download **/
async function resetChat(){
  // clear local state
  lastIntent = null;
  lastTopProjectId = null;
  lastMessage = null;

  // clear storage
  sessionStorage.removeItem(CHAT_HTML_KEY);
  sessionStorage.removeItem(LEAD_CARD_KEY);

  // reset UI to initial welcome
  const chat = document.getElementById('chat');
  chat.innerHTML = `
    <div class="msg bot" data-static="1">
      <div class="name">vServeAI</div>
      Welcome dear 👋 Tell me your <b>city/community</b> to start.<br>
      Examples: Dubai Marina, JVC, Dubai Hills, Delhi, Mumbai, Bangalore.
    </div>
  `;
  saveChat();
  scrollToBottom();

  // also clear server-side memory by sending "reset"
  try{
    setTyping(true);
    const res = await fetch("{{ route('ai.chat.message') }}", {
      method:'POST',
      headers:{
        'Content-Type':'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Accept': 'application/json'
      },
      body: JSON.stringify({message: "reset"})
    });
    setTyping(false);

    if(res.ok){
      const data = await safeJson(res);
      if(data?.ok && data?.reply){
        appendMsg(data.reply, 'bot');
      }
    }
  } catch(e){
    setTyping(false);
    console.error("Reset call failed:", e);
  }
}

function setChatClosed(closed){
  const chatWrap = document.getElementById('chatWrap');
  const closeBtn = document.getElementById('closeBtn');

  if(closed){
    chatWrap.classList.add('hidden');
    closeBtn.textContent = 'Open';
    sessionStorage.setItem(CHAT_CLOSED_KEY, '1');
  } else {
    chatWrap.classList.remove('hidden');
    closeBtn.textContent = 'Close';
    sessionStorage.setItem(CHAT_CLOSED_KEY, '0');
    setTimeout(scrollToBottom, 50);
  }
}

function toggleChat(){
  const closed = document.getElementById('chatWrap').classList.contains('hidden');
  setChatClosed(!closed);
}

function downloadChat(){
  const msgs = Array.from(document.querySelectorAll('#chat .msg'));
  const lines = msgs.map(el => {
    const who = el.classList.contains('me') ? 'You' : 'vServeAI';
    // use textContent to avoid HTML
    const text = (el.textContent || '').replace(/\s+\n/g, '\n').trim();
    return `${who}: ${text}`;
  });

  const content = lines.join('\n\n');
  const blob = new Blob([content], {type: 'text/plain;charset=utf-8'});
  const url = URL.createObjectURL(blob);

  const a = document.createElement('a');
  a.href = url;
  a.download = `vserveai-chat-${new Date().toISOString().slice(0,10)}.txt`;
  document.body.appendChild(a);
  a.click();
  a.remove();
  URL.revokeObjectURL(url);
}

/** utils **/
function formatNum(n){
  try { return Number(n).toLocaleString(); } catch(e){ return n; }
}
function escapeHtml(str){
  return String(str).replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[s]));
}

document.getElementById('input').addEventListener('keydown', (e) => {
  if(e.key === 'Enter'){ e.preventDefault(); sendMsg(); }
});

// Restore chat history on refresh
restoreChat();
</script>
</body>
</html>