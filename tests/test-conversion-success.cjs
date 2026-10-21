const assert = require('node:assert/strict');
const test = require('node:test');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');

const sourceDir = process.env.SOFTKOM_TEST_SOURCE_DIR || path.join(__dirname, '..', 'wp-content', 'themes', 'softkom-v3', 'assets', 'js');
const trackingSource = fs.readFileSync(path.join(sourceDir, 'softkom-conversion-tracking.js'), 'utf8');
const assessmentSource = fs.readFileSync(path.join(sourceDir, 'softkom-assessment.js'), 'utf8');

class CustomEvent extends Event {
  constructor(name, options = {}) { super(name, options); this.detail = options.detail; }
}
class Element extends EventTarget {
  constructor(id = '') {
    super(); this.id = id; this.dataset = {}; this.style = {};
    this.hidden = false; this.disabled = false; this.textContent = '';
    this.children = new Map();
  }
  querySelector(selector) { return this.children.get(selector) || null; }
  scrollIntoView() {}
}

function environment({ contact = true, assessment = false, search = '', fetchImpl } = {}) {
  const doc = new EventTarget();
  const win = new EventTarget();
  const queries = new Map();
  const events = [];
  const alerts = [];
  const form = new Element('srfm-form-2722');
  const lead = new Element();
  const submit = new Element();
  const results = new Element();
  results.hidden = true;
  const store = new Map();
  doc.referrer = '';
  doc.querySelector = (s) => queries.get(s) || null;
  doc.querySelectorAll = (s) => s === '[data-sk-contact-form], #srfm-form-2722' && contact ? [form] : [];
  doc.getElementById = (id) => contact && id === form.id ? form : null;
  win.location = { href: 'https://softkom.test/assessment/' + search, pathname: '/assessment/', search };
  win.sessionStorage = { getItem: (k) => store.get(k), setItem: (k, v) => store.set(k, v) };
  win.setTimeout = () => 0;
  win.gtag = (command, name, params) => events.push({ command, name, params });
  win.alert = (message) => alerts.push(message);
  if (assessment) {
    const app = new Element();
    for (const s of ['question', 'section', 'help', 'options', 'back', 'next', 'progress-label', 'progress-percent', 'progress-bar']) {
      app.children.set('[data-assessment-' + s + ']', new Element());
    }
    queries.set('[data-assessment-start]', new Element());
    queries.set('[data-assessment-app]', app);
    queries.set('[data-assessment-lead]', new Element());
    queries.set('[data-assessment-results]', results);
    queries.set('[data-assessment-lead-form]', lead);
    lead.children.set('button[type="submit"]', submit);
  }
  const ctx = vm.createContext({
    window: win, document: doc, URL, URLSearchParams, Event, CustomEvent,
    console: { error() {} },
    softkomAssessment: { ajaxUrl: 'https://softkom.test/wp-admin/admin-ajax.php', nonce: 'local-test-only' },
    FormData: class { constructor() { this.values = []; } append(k, v) { this.values.push([k, v]); } },
    fetch: fetchImpl || (async () => ({ ok: true, json: async () => ({ success: true, data: { scores: {} } }) }))
  });
  if (assessment) vm.runInContext(assessmentSource, ctx);
  vm.runInContext(trackingSource, ctx);
  doc.dispatchEvent(new Event('DOMContentLoaded'));
  return {
    doc, form, lead, submit, results, alerts, events,
    names: () => events.map((x) => x.name),
    submitLead: () => lead.dispatchEvent(new Event('submit', { cancelable: true })),
    sureFormsSuccess: (id = form.id) => doc.dispatchEvent(new CustomEvent('srfm_form_submission_success', { detail: { formId: id } }))
  };
}
const flush = () => new Promise((resolve) => setImmediate(resolve));
const conversionNames = ['softkom_contact_form_submit', 'softkom_enquiry_success', 'softkom_assessment_lead_submit'];
const conversions = (env) => env.names().filter((name) => conversionNames.includes(name));

test('the current SureForms contact form records one start and only accepted submissions count', () => {
  const env = environment();
  env.form.dispatchEvent(new Event('focusin'));
  env.form.dispatchEvent(new Event('focusin'));
  assert.deepEqual(env.names(), ['softkom_contact_form_start']);
  env.form.dispatchEvent(new Event('submit', { cancelable: true }));
  env.doc.dispatchEvent(new CustomEvent('srfm_on_trigger_form_submission_failure', { detail: { form: env.form } }));
  env.sureFormsSuccess('srfm-form-9999');
  assert.deepEqual(conversions(env), []);
  env.sureFormsSuccess();
  env.sureFormsSuccess();
  assert.deepEqual(conversions(env), ['softkom_contact_form_submit', 'softkom_enquiry_success']);
});

test('an arbitrary sent=1 URL and a success event for an absent form cannot create a conversion', () => {
  const env = environment({ contact: false, search: '?sent=1' });
  env.sureFormsSuccess();
  assert.deepEqual(conversions(env), []);
});

test('assessment conversion waits for the successful endpoint response and is counted once', async () => {
  let accept;
  const env = environment({ assessment: true, fetchImpl: () => new Promise((resolve) => { accept = resolve; }) });
  env.submitLead();
  assert.deepEqual(conversions(env), []);
  assert.equal(env.submit.disabled, true);
  accept({ ok: true, json: async () => ({ success: true, data: { scores: {} } }) });
  await flush();
  assert.deepEqual(conversions(env), ['softkom_assessment_lead_submit']);
  assert.equal(env.results.hidden, false);
  assert.equal(env.submit.disabled, false);
  assert.deepEqual(env.alerts, []);
  env.lead.dispatchEvent(new CustomEvent('softkom_assessment_submission_success'));
  assert.deepEqual(conversions(env), ['softkom_assessment_lead_submit']);
});

for (const [name, fetchImpl] of [
  ['server HTTP failure', async () => ({ ok: false, json: async () => ({ success: true, data: {} }) })],
  ['validation rejection', async () => ({ ok: true, json: async () => ({ success: false, data: { message: 'Invalid form' } }) })],
  ['missing success data', async () => ({ ok: true, json: async () => ({ success: true }) })],
  ['malformed JSON', async () => ({ ok: true, json: async () => { throw new SyntaxError('Invalid JSON'); } })],
  ['network failure', async () => { throw new Error('Network unavailable'); }]
]) {
  test(name + ' does not count an assessment conversion and permits retry', async () => {
    const env = environment({ assessment: true, fetchImpl });
    env.submitLead();
    await flush();
    assert.deepEqual(conversions(env), []);
    assert.equal(env.results.hidden, true);
    assert.equal(env.submit.disabled, false);
    assert.equal(env.alerts.length, 1);
  });
}

test('a successful retry after an assessment rejection produces exactly one conversion', async () => {
  let attempts = 0;
  const env = environment({ assessment: true, fetchImpl: async () => ({
    ok: true, json: async () => ++attempts === 1
      ? { success: false, data: { message: 'Rejected' } }
      : { success: true, data: { scores: {} } }
  }) });
  env.submitLead(); await flush();
  assert.deepEqual(conversions(env), []);
  env.submitLead(); await flush();
  assert.deepEqual(conversions(env), ['softkom_assessment_lead_submit']);
});
