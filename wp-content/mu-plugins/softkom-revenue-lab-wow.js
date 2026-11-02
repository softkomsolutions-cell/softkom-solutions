(function(){
'use strict';
var cfg=window.softkomRevenueLab||{};
var form=document.getElementById('sk-rl-scan-form');
var battlePanel=document.getElementById('sk-rl-battle-panel');
var battleEl=document.getElementById('sk-rl-battle');
var simPanel=document.getElementById('sk-rl-simulation-panel');
var simEl=document.getElementById('sk-rl-simulation');
if(!form||!cfg.ajaxUrl){return;}

function esc(v){return String(v==null?'':v).replace(/[&<>"']/g,function(ch){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[ch];});}
function renderBattle(b){
  if(!battlePanel||!battleEl){return;}
  if(!b){battlePanel.hidden=true;return;}
  battlePanel.hidden=false;
  var winner=b.winner==='primary'?'Your site wins':(b.winner==='competitor'?'Competitor wins':'Too close to call');
  var reasons=(b.reasons||[]).map(function(r){return '<li><strong>'+(r.advantage==='primary'?'You':'Competitor')+':</strong> '+esc(r.label)+'</li>';}).join('');
  battleEl.innerHTML='<div class="sk-rl-battle-score"><div><span>Your site</span><strong>'+esc(b.primary.score)+'</strong><small>'+esc(b.primary.successes)+'/5 journeys</small></div><div class="sk-rl-vs">VS</div><div><span>Competitor</span><strong>'+esc(b.competitor.score)+'</strong><small>'+esc(b.competitor.successes)+'/5 journeys</small></div></div><p class="sk-rl-battle-winner">'+esc(winner)+'</p>'+(reasons?'<ul class="sk-rl-battle-reasons">'+reasons+'</ul>':'');
}
function renderSim(s){
  if(!simPanel||!simEl){return;}
  if(!s){simPanel.hidden=true;return;}
  simPanel.hidden=false;
  var repairs=(s.repairs||[]).map(function(r){return '<li>'+esc(r)+'</li>';}).join('');
  simEl.innerHTML='<div class="sk-rl-sim-meter"><div><span>Before</span><strong>'+esc(s.before)+'/5</strong></div><div class="sk-rl-arrow">→</div><div><span>After simulated fixes</span><strong>'+esc(s.after)+'/5</strong></div></div>'+(repairs?'<p><strong>Modelled repairs</strong></p><ul>'+repairs+'</ul>':'')+'<p class="sk-rl-note">'+esc(s.note||'')+'</p>';
}

var originalFetch=window.fetch;
window.fetch=function(input,init){
  return originalFetch.apply(this,arguments).then(function(response){
    try{
      var url=typeof input==='string'?input:(input&&input.url?input.url:'');
      if(url.indexOf(cfg.ajaxUrl)!==-1 && init && typeof init.body==='string' && init.body.indexOf('action=softkom_revenue_lab_scan')!==-1){
        response.clone().json().then(function(json){
          if(json&&json.success&&json.data){renderBattle(json.data.battle||null);renderSim(json.data.simulation||null);}
        }).catch(function(){});
      }
    }catch(e){}
    return response;
  });
};
})();