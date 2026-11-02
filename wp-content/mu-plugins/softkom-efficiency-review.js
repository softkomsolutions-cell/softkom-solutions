(function(){
  const root=document.querySelector('[data-skber-form]');
  if(!root||!window.SoftkomEfficiencyReview)return;
  const steps=[...root.querySelectorAll('[data-skber-step]')];
  const bar=root.querySelector('[data-skber-progress]');
  const label=root.querySelector('[data-skber-progress-label]');
  const prev=root.querySelector('[data-skber-prev]');
  const next=root.querySelector('[data-skber-next]');
  const submit=root.querySelector('[data-skber-submit]');
  const msg=root.querySelector('[data-skber-message]');
  let current=0;

  function visibleInputs(step){return [...step.querySelectorAll('input,select,textarea')].filter(el=>!el.disabled);}
  function validate(){
    const required=visibleInputs(steps[current]).filter(el=>el.required);
    for(const el of required){
      if(el.type==='checkbox' && !el.checked){el.focus();return false;}
      if(!String(el.value||'').trim()){el.focus();return false;}
      if(el.type==='email' && !el.checkValidity()){el.focus();return false;}
    }
    return true;
  }
  function show(){
    steps.forEach((s,i)=>s.hidden=i!==current);
    const pct=Math.round(((current+1)/steps.length)*100);
    if(bar)bar.style.width=pct+'%';
    if(label)label.textContent='Step '+(current+1)+' of '+steps.length;
    if(prev)prev.hidden=current===0;
    if(next)next.hidden=current===steps.length-1;
    if(submit)submit.hidden=current!==steps.length-1;
    root.scrollIntoView({behavior:'smooth',block:'start'});
  }
  next&&next.addEventListener('click',()=>{if(validate()&&current<steps.length-1){current++;show();}});
  prev&&prev.addEventListener('click',()=>{if(current>0){current--;show();}});

  root.addEventListener('submit',async(e)=>{
    e.preventDefault();
    if(!validate())return;
    submit.disabled=true;
    submit.textContent='Sending...';
    msg.textContent='';
    const fd=new FormData(root);
    fd.append('action','softkom_efficiency_review_submit');
    fd.append('nonce',SoftkomEfficiencyReview.nonce);
    try{
      const r=await fetch(SoftkomEfficiencyReview.ajaxUrl,{method:'POST',body:fd,credentials:'same-origin'});
      const j=await r.json();
      if(!j.success)throw new Error((j.data&&j.data.message)||'Unable to submit right now.');
      root.querySelector('[data-skber-form-body]').hidden=true;
      root.querySelector('[data-skber-success]').hidden=false;
      root.querySelector('[data-skber-success-name]').textContent=fd.get('name')||'';
    }catch(err){
      msg.textContent=err.message||'Unable to submit right now.';
      submit.disabled=false;
      submit.textContent='Send My Review Request';
    }
  });
  show();
})();