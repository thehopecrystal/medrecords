import { useState } from 'react';
import { Link, router, usePage, useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

const TABS = [
    { key: 'summary',       label: 'Summary' },
    { key: 'encounters',    label: 'Encounters' },
    { key: 'conditions',    label: 'Conditions' },
    { key: 'prescriptions', label: 'Medications' },
    { key: 'labs',          label: 'Labs' },
    { key: 'vaccinations',  label: 'Vaccines' },
];

const pillStyle = (color) => ({
    display: 'inline-block', padding: '2px 9px', borderRadius: 20, fontSize: 11, fontWeight: 600,
    background: color === 'green'  ? 'var(--color-green-lt)'  :
                color === 'amber'  ? 'var(--color-amber-lt)'  :
                color === 'red'    ? 'var(--color-red-lt)'    :
                color === 'navy'   ? 'var(--color-navy-lt)'   : '#f3f4f6',
    color:      color === 'green'  ? 'var(--color-green)'     :
                color === 'amber'  ? 'var(--color-amber)'     :
                color === 'red'    ? 'var(--color-red)'       :
                color === 'navy'   ? 'var(--color-navy)'      : '#6b7280',
});

function fmt(dateStr) {
    if (!dateStr) return '—';
    return new Date(dateStr).toLocaleDateString('en-NG', { day: '2-digit', month: 'short', year: 'numeric' });
}

export default function Patient({ patient, consent, tab: initialTab }) {
    const [tab, setTab] = useState(initialTab ?? 'summary');

    const encounters     = patient.encounters     ?? [];
    const conditions     = patient.conditions     ?? [];
    const prescriptions  = patient.prescriptions  ?? [];
    const labResults     = patient.lab_results    ?? patient.labResults ?? [];
    const activeRx       = patient.active_prescriptions ?? patient.activePrescriptions ?? [];
    const activeAllergies= patient.active_allergies    ?? patient.activeAllergies ?? [];
    const activeConditions = patient.active_conditions ?? patient.activeConditions ?? [];

    const cardStyle = { background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12, marginBottom: 20 };
    const entryStyle = { padding: '14px 20px', borderBottom: '1px solid var(--color-border)' };

    const showEncounters    = tab === 'summary' || tab === 'encounters';
    const showConditions    = tab === 'summary' || tab === 'conditions';
    const showPrescriptions = tab === 'summary' || tab === 'prescriptions';
    const showLabs          = tab === 'labs';

    return (
        <AppLayout title="Patient Record">
            {/* Topbar actions */}
            <div style={{ display: 'flex', gap: 8, marginBottom: 16, flexWrap: 'wrap' }}>
                <Link href={`/provider/patients/${patient.id}/encounter/new`} style={{ background: 'var(--color-navy)', color: '#fff', padding: '7px 14px', borderRadius: 7, fontSize: 12, fontWeight: 600, textDecoration: 'none' }}>+ New encounter</Link>
                <Link href={`/provider/patients/${patient.id}/refer`} style={{ background: 'var(--color-card)', color: 'var(--color-text)', border: '1px solid var(--color-border)', padding: '7px 14px', borderRadius: 7, fontSize: 12, fontWeight: 600, textDecoration: 'none' }}>Refer</Link>
                <Link href={`/provider/patients/${patient.id}/lab/new`} style={{ background: 'var(--color-card)', color: 'var(--color-text)', border: '1px solid var(--color-border)', padding: '7px 14px', borderRadius: 7, fontSize: 12, fontWeight: 600, textDecoration: 'none' }}>Add lab</Link>
                {!consent && (
                    <>
                        <Link href={`/provider/patients/${patient.id}/consent-request`} style={{ background: 'var(--color-card)', color: 'var(--color-text)', border: '1px solid var(--color-border)', padding: '7px 14px', borderRadius: 7, fontSize: 12, fontWeight: 600, textDecoration: 'none' }}>Request consent</Link>
                        <Link href={`/provider/patients/${patient.id}/break-glass`} style={{ background: 'var(--color-red-lt)', color: 'var(--color-red)', border: '1px solid var(--color-red)', padding: '7px 14px', borderRadius: 7, fontSize: 12, fontWeight: 600, textDecoration: 'none' }}>Break-glass</Link>
                    </>
                )}
            </div>

            {/* No consent alert */}
            {!consent && (
                <div style={{ display: 'flex', alignItems: 'flex-start', gap: 10, padding: '12px 16px', borderRadius: 10, background: 'var(--color-amber-lt)', color: 'var(--color-amber)', fontSize: 13, borderLeft: '3px solid var(--color-amber)', marginBottom: 20 }}>
                    <span>!</span>
                    <span><strong>No active consent.</strong> You do not have an active consent grant from this patient. Request consent or use break-glass for emergencies.</span>
                </div>
            )}

            {/* Patient banner */}
            <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12, padding: '16px 20px', marginBottom: 20, display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', gap: 16 }}>
                <div style={{ flex: 1 }}>
                    <div style={{ fontSize: 20, fontWeight: 800, color: 'var(--color-navy)', marginBottom: 4 }}>{patient.user?.name}</div>
                    <div style={{ fontSize: 13, color: 'var(--color-text-3)', marginBottom: 8 }}>
                        {[patient.gender ? patient.gender.charAt(0).toUpperCase() + patient.gender.slice(1) : null, patient.dob && patient.age ? `${patient.age} yrs` : null, patient.med_id, patient.user?.phone].filter(Boolean).join(' · ')}
                    </div>
                    <div style={{ display: 'flex', gap: 6, flexWrap: 'wrap' }}>
                        {activeAllergies.map((a, i) => <span key={i} style={pillStyle('red')}>! {a.allergen} allergy</span>)}
                        {activeConditions.map((c, i) => <span key={i} style={pillStyle('amber')}>{c.name}</span>)}
                        {activeRx.length > 0 && <span style={pillStyle('navy')}>{activeRx.length} active Rx</span>}
                    </div>
                </div>
                {consent && (
                    <div style={{ fontSize: 12, textAlign: 'right', color: 'var(--color-text-3)' }}>
                        <div style={{ fontSize: 11, textTransform: 'uppercase', letterSpacing: '0.05em', marginBottom: 2 }}>Consent scope</div>
                        <div style={{ fontWeight: 700, color: 'var(--color-green)' }}>{consent.scope_label ?? consent.scopeLabel ?? consent.scope}</div>
                        <div style={{ fontSize: 11 }}>expires {consent.expires_at ? fmt(consent.expires_at) : 'no expiry'}</div>
                    </div>
                )}
                <Link href="/provider/search" style={{ background: 'var(--color-card)', color: 'var(--color-text)', border: '1px solid var(--color-border)', padding: '7px 14px', borderRadius: 7, fontSize: 12, fontWeight: 600, textDecoration: 'none', flexShrink: 0 }}>
                    Back
                </Link>
            </div>

            {/* Tabs */}
            <div style={{ display: 'flex', gap: 4, marginBottom: 20, borderBottom: '1px solid var(--color-border)', paddingBottom: 0 }}>
                {TABS.map(t => (
                    <button key={t.key} onClick={() => setTab(t.key)} style={{
                        padding: '9px 16px', fontSize: 13, fontWeight: tab === t.key ? 700 : 500,
                        color: tab === t.key ? 'var(--color-navy)' : 'var(--color-text-3)',
                        background: 'none', border: 'none', cursor: 'pointer',
                        borderBottom: tab === t.key ? '2px solid var(--color-navy)' : '2px solid transparent',
                        marginBottom: -1,
                    }}>
                        {t.label}
                    </button>
                ))}
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: '1fr 280px', gap: 20 }}>
                <div>
                    {/* Encounters */}
                    {showEncounters && (
                        <div style={cardStyle}>
                            <div style={{ padding: '14px 20px', borderBottom: '1px solid var(--color-border)', display: 'flex', alignItems: 'center', gap: 8 }}>
                                <span style={{ fontSize: 15, fontWeight: 700, color: 'var(--color-navy)' }}>Encounters</span>
                                <span style={pillStyle('navy')}>{encounters.length}</span>
                            </div>
                            {encounters.length === 0 ? (
                                <div style={{ padding: 16, color: 'var(--color-text-3)', fontSize: 13 }}>No encounters on record.</div>
                            ) : (
                                [...encounters].sort((a, b) => new Date(b.created_at) - new Date(a.created_at)).map(enc => (
                                    <div key={enc.id} style={entryStyle}>
                                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start' }}>
                                            <div>
                                                <div style={{ fontSize: 14, fontWeight: 600, color: 'var(--color-text)' }}>{enc.chief_complaint || (enc.type ? enc.type.charAt(0).toUpperCase() + enc.type.slice(1) : '')}</div>
                                                <div style={{ fontSize: 12, color: 'var(--color-text-3)', marginTop: 2 }}>{enc.provider?.name} · {enc.facility?.name}</div>
                                            </div>
                                            <div style={{ textAlign: 'right', flexShrink: 0 }}>
                                                <div style={{ fontSize: 12, color: 'var(--color-text-3)', marginBottom: 4 }}>{fmt(enc.created_at)}</div>
                                                <span style={pillStyle(enc.status === 'signed' ? 'green' : 'amber')}>{enc.status}</span>
                                            </div>
                                        </div>
                                        {enc.assessment_plan && (
                                            <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 6 }}>
                                                {enc.assessment_plan.length > 200 ? enc.assessment_plan.substring(0, 200) + '...' : enc.assessment_plan}
                                            </div>
                                        )}
                                        {enc.diagnosis && (
                                            <div style={{ fontSize: 12, color: 'var(--color-text-3)', marginTop: 4, fontStyle: 'italic' }}>{enc.diagnosis}</div>
                                        )}
                                        {enc.status === 'signed' && (
                                            <div style={{ marginTop: 8 }}>
                                                <a href={`/provider/patients/${patient.id}/encounters/${enc.id}/print`} target="_blank" rel="noreferrer" style={{ background: 'var(--color-card)', color: 'var(--color-text)', border: '1px solid var(--color-border)', padding: '5px 12px', borderRadius: 6, fontSize: 11, fontWeight: 600, textDecoration: 'none' }}>
                                                    Print
                                                </a>
                                            </div>
                                        )}
                                    </div>
                                ))
                            )}
                        </div>
                    )}

                    {/* Conditions */}
                    {showConditions && (
                        <div style={cardStyle}>
                            <div style={{ padding: '14px 20px', borderBottom: '1px solid var(--color-border)' }}>
                                <span style={{ fontSize: 15, fontWeight: 700, color: 'var(--color-navy)' }}>Conditions</span>
                            </div>
                            {conditions.length === 0 ? (
                                <div style={{ padding: 16, color: 'var(--color-text-3)', fontSize: 13 }}>No conditions recorded.</div>
                            ) : (
                                conditions.map(c => (
                                    <div key={c.id} style={entryStyle}>
                                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start' }}>
                                            <div>
                                                <div style={{ fontSize: 14, fontWeight: 600, color: 'var(--color-text)' }}>{c.name}</div>
                                                <div style={{ fontSize: 12, color: 'var(--color-text-3)', marginTop: 2 }}>
                                                    {c.icd10_code} · diagnosed {c.diagnosed_at ? new Date(c.diagnosed_at).toLocaleDateString('en-NG', { month: 'short', year: 'numeric' }) : '—'}
                                                </div>
                                            </div>
                                            <span style={pillStyle(['active', 'chronic'].includes(c.status) ? 'amber' : 'grey')}>{c.status}</span>
                                        </div>
                                    </div>
                                ))
                            )}
                        </div>
                    )}

                    {/* Prescriptions */}
                    {showPrescriptions && (
                        <div style={cardStyle}>
                            <div style={{ padding: '14px 20px', borderBottom: '1px solid var(--color-border)', display: 'flex', alignItems: 'center', gap: 8 }}>
                                <span style={{ fontSize: 15, fontWeight: 700, color: 'var(--color-navy)' }}>Medications</span>
                                <span style={pillStyle('green')}>{activeRx.length} active</span>
                            </div>
                            {prescriptions.length === 0 ? (
                                <div style={{ padding: 16, color: 'var(--color-text-3)', fontSize: 13 }}>No prescriptions.</div>
                            ) : (
                                [...prescriptions].sort((a, b) => new Date(b.created_at) - new Date(a.created_at)).map(rx => (
                                    <div key={rx.id} style={entryStyle}>
                                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start' }}>
                                            <div>
                                                <div style={{ fontSize: 14, fontWeight: 600, color: 'var(--color-text)' }}>{rx.drug_name} {rx.dose}</div>
                                                <div style={{ fontSize: 12, color: 'var(--color-text-3)', marginTop: 2 }}>{rx.frequency} · {rx.prescribed_by?.name ?? rx.prescribedBy?.name}</div>
                                            </div>
                                            <span style={pillStyle(rx.status_color ?? rx.statusColor ?? (rx.status === 'active' ? 'green' : 'grey'))}>{rx.status}</span>
                                        </div>
                                    </div>
                                ))
                            )}
                        </div>
                    )}

                    {/* Labs */}
                    {showLabs && (
                        <div style={cardStyle}>
                            <div style={{ padding: '14px 20px', borderBottom: '1px solid var(--color-border)', display: 'flex', alignItems: 'center', gap: 8 }}>
                                <span style={{ fontSize: 15, fontWeight: 700, color: 'var(--color-navy)' }}>Lab Results</span>
                                <span style={pillStyle('navy')}>{labResults.length}</span>
                            </div>
                            {labResults.length === 0 ? (
                                <div style={{ padding: 16, color: 'var(--color-text-3)', fontSize: 13 }}>No lab results.</div>
                            ) : (
                                [...labResults].sort((a, b) => new Date(b.collected_at) - new Date(a.collected_at)).map(lab => (
                                    <div key={lab.id} style={entryStyle}>
                                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start' }}>
                                            <div>
                                                <div style={{ fontSize: 14, fontWeight: 600, color: 'var(--color-text)' }}>{lab.test_name}</div>
                                                <div style={{ fontSize: 12, color: 'var(--color-text-3)', marginTop: 2 }}>{fmt(lab.collected_at)}</div>
                                            </div>
                                            <div style={{ textAlign: 'right' }}>
                                                <span style={{ fontSize: 13 }}>{lab.result} {lab.unit}</span>
                                                {' '}
                                                <span style={pillStyle(lab.flag_color ?? lab.flagColor ?? 'grey')}>{lab.flag}</span>
                                            </div>
                                        </div>
                                    </div>
                                ))
                            )}
                        </div>
                    )}
                </div>

                {/* Sidebar */}
                <aside>
                    {/* Active medications */}
                    <div style={{ ...cardStyle, padding: '16px 18px' }}>
                        <div style={{ fontSize: 11, color: 'var(--color-text-3)', textTransform: 'uppercase', letterSpacing: '0.05em', marginBottom: 10 }}>Active medications</div>
                        {activeRx.length === 0 ? (
                            <div style={{ fontSize: 12, color: 'var(--color-text-3)' }}>None</div>
                        ) : (
                            activeRx.map((rx, i) => (
                                <div key={i} style={{ padding: '8px 0', borderBottom: '1px solid var(--color-border)', fontSize: 13 }}>
                                    <strong>{rx.drug_name}</strong> {rx.dose}<br />
                                    <span style={{ fontSize: 11, color: 'var(--color-text-3)' }}>{rx.frequency}</span>
                                </div>
                            ))
                        )}
                    </div>

                    {/* Allergies */}
                    {activeAllergies.length > 0 && (
                        <div style={{ ...cardStyle, padding: '16px 18px', borderLeft: '3px solid var(--color-red)' }}>
                            <div style={{ fontSize: 11, color: 'var(--color-text-3)', textTransform: 'uppercase', letterSpacing: '0.05em', marginBottom: 8 }}>Allergies</div>
                            {activeAllergies.map((a, i) => (
                                <div key={i} style={{ fontSize: 13, padding: '4px 0' }}>
                                    <strong style={{ color: 'var(--color-red)' }}>! {a.allergen}</strong>
                                    <span style={{ fontSize: 11, color: 'var(--color-text-3)' }}> · {a.severity}</span>
                                </div>
                            ))}
                        </div>
                    )}

                    {/* Patient info */}
                    <div style={{ ...cardStyle, padding: '16px 18px' }}>
                        <div style={{ fontSize: 11, color: 'var(--color-text-3)', textTransform: 'uppercase', letterSpacing: '0.05em', marginBottom: 10 }}>Patient info</div>
                        {[['Blood type', patient.blood_type], ['Genotype', patient.genotype], ['DOB', patient.dob ? fmt(patient.dob) : null]].map(([k, v]) => (
                            <div key={k} style={{ display: 'flex', justifyContent: 'space-between', padding: '6px 0', borderBottom: '1px solid var(--color-border)', fontSize: 13 }}>
                                <span style={{ color: 'var(--color-text-3)' }}>{k}</span>
                                <span style={{ fontWeight: 500 }}>{v || '—'}</span>
                            </div>
                        ))}
                    </div>
                </aside>
            </div>
        </AppLayout>
    );
}
