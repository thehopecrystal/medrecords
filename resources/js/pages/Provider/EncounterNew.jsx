import { useForm, Link, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

const fieldStyle = { marginBottom: 16 };
const labelStyle = { display: 'block', fontSize: 12, fontWeight: 600, color: 'var(--color-text-2)', marginBottom: 5, textTransform: 'uppercase', letterSpacing: '0.04em' };
const inputStyle = { width: '100%', padding: '9px 12px', border: '1px solid var(--color-border)', borderRadius: 8, fontSize: 13, background: 'var(--color-card)', color: 'var(--color-text)', boxSizing: 'border-box', outline: 'none' };

export default function EncounterNew({ patient, provider }) {
    const now = new Date();

    const { data, setData, post, processing, errors } = useForm({
        type: 'outpatient',
        chief_complaint: '',
        history: '',
        diagnosis: '',
        icd10_code: '',
        vitals: '',
        assessment_plan: '',
        notes: '',
        drug_name: '',
        drug_dose: '',
        drug_frequency: '',
        drug_duration: '',
        drug_instructions: '',
    });

    function submit(e, action) {
        e.preventDefault();
        const url = `/provider/patients/${patient.id}/encounter`;
        if (action === 'sign') {
            post(url + '?sign=1');
        } else {
            post(url + '?draft=1');
        }
    }

    const cardStyle = { background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12, padding: '20px 24px' };

    return (
        <AppLayout title="New Encounter">
            <div style={{ marginBottom: 16 }}>
                <Link href={`/provider/patients/${patient.id}`} style={{ background: 'var(--color-card)', color: 'var(--color-text)', border: '1px solid var(--color-border)', padding: '7px 14px', borderRadius: 7, fontSize: 12, fontWeight: 600, textDecoration: 'none' }}>
                    Back to {patient.user?.name}
                </Link>
            </div>

            <div style={{ marginBottom: 20 }}>
                <h1 style={{ fontSize: 20, fontWeight: 800, color: 'var(--color-navy)', margin: 0 }}>New encounter — {patient.user?.name}</h1>
                <p style={{ fontSize: 13, color: 'var(--color-text-3)', marginTop: 4 }}>
                    {provider?.facility?.name ?? ''} · {now.toLocaleDateString('en-NG', { day: '2-digit', month: 'short', year: 'numeric' })} {now.toLocaleTimeString('en-NG', { hour: '2-digit', minute: '2-digit' })}
                </p>
            </div>

            <form>
                <div style={cardStyle}>
                    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '0 20px' }}>
                        <div style={fieldStyle}>
                            <label style={labelStyle}>Encounter type</label>
                            <select value={data.type} onChange={e => setData('type', e.target.value)} style={inputStyle} required>
                                {['outpatient', 'inpatient', 'emergency', 'telemedicine', 'dental', 'optical'].map(t => (
                                    <option key={t} value={t}>{t.charAt(0).toUpperCase() + t.slice(1)}</option>
                                ))}
                            </select>
                        </div>
                        <div style={fieldStyle}>
                            <label style={labelStyle}>Chief complaint</label>
                            <input style={inputStyle} value={data.chief_complaint} onChange={e => setData('chief_complaint', e.target.value)} placeholder="e.g. Routine diabetes follow-up" required />
                        </div>
                        <div style={{ ...fieldStyle, gridColumn: '1 / -1' }}>
                            <label style={labelStyle}>History of presenting illness</label>
                            <textarea style={{ ...inputStyle, minHeight: 80, resize: 'vertical' }} value={data.history} onChange={e => setData('history', e.target.value)} placeholder="Patient history..." />
                        </div>
                        <div style={fieldStyle}>
                            <label style={labelStyle}>Diagnosis (ICD-10)</label>
                            <input style={inputStyle} value={data.diagnosis} onChange={e => setData('diagnosis', e.target.value)} placeholder="e.g. Type 2 diabetes mellitus" />
                        </div>
                        <div style={fieldStyle}>
                            <label style={labelStyle}>ICD-10 code</label>
                            <input style={inputStyle} value={data.icd10_code} onChange={e => setData('icd10_code', e.target.value)} placeholder="e.g. E11.9" />
                        </div>
                        <div style={{ ...fieldStyle, gridColumn: '1 / -1' }}>
                            <label style={labelStyle}>Vital signs</label>
                            <input style={inputStyle} value={data.vitals} onChange={e => setData('vitals', e.target.value)} placeholder="BP 120/80 · HR 72 · Temp 36.6 · Wt 70kg" />
                        </div>
                        <div style={{ ...fieldStyle, gridColumn: '1 / -1' }}>
                            <label style={labelStyle}>Assessment & plan <span style={{ color: 'var(--color-red)' }}>*</span></label>
                            <textarea style={{ ...inputStyle, minHeight: 100, resize: 'vertical' }} value={data.assessment_plan} onChange={e => setData('assessment_plan', e.target.value)} placeholder="Your clinical assessment and management plan..." required />
                        </div>
                        <div style={{ ...fieldStyle, gridColumn: '1 / -1' }}>
                            <label style={labelStyle}>Additional notes</label>
                            <textarea style={{ ...inputStyle, minHeight: 60, resize: 'vertical' }} value={data.notes} onChange={e => setData('notes', e.target.value)} placeholder="Any other notes..." />
                        </div>
                    </div>

                    {/* Prescription section */}
                    <div style={{ borderTop: '1px solid var(--color-border)', marginTop: 20, paddingTop: 20 }}>
                        <div style={{ fontSize: 13, fontWeight: 700, color: 'var(--color-navy)', marginBottom: 14 }}>Add prescription (optional)</div>
                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '0 20px' }}>
                            <div style={fieldStyle}>
                                <label style={labelStyle}>Drug name</label>
                                <input style={inputStyle} value={data.drug_name} onChange={e => setData('drug_name', e.target.value)} placeholder="e.g. Metformin" />
                            </div>
                            <div style={fieldStyle}>
                                <label style={labelStyle}>Dose</label>
                                <input style={inputStyle} value={data.drug_dose} onChange={e => setData('drug_dose', e.target.value)} placeholder="e.g. 500mg" />
                            </div>
                            <div style={fieldStyle}>
                                <label style={labelStyle}>Frequency</label>
                                <input style={inputStyle} value={data.drug_frequency} onChange={e => setData('drug_frequency', e.target.value)} placeholder="e.g. Twice daily with meals" />
                            </div>
                            <div style={fieldStyle}>
                                <label style={labelStyle}>Duration (days)</label>
                                <input type="number" style={inputStyle} value={data.drug_duration} onChange={e => setData('drug_duration', e.target.value)} placeholder="e.g. 30" />
                            </div>
                            <div style={{ ...fieldStyle, gridColumn: '1 / -1' }}>
                                <label style={labelStyle}>Instructions</label>
                                <input style={inputStyle} value={data.drug_instructions} onChange={e => setData('drug_instructions', e.target.value)} placeholder="e.g. Take with food, avoid alcohol" />
                            </div>
                        </div>
                    </div>

                    <div style={{ display: 'flex', gap: 10, marginTop: 20, paddingTop: 20, borderTop: '1px solid var(--color-border)' }}>
                        <button type="button" onClick={e => submit(e, 'draft')} disabled={processing}
                            style={{ background: 'var(--color-card)', color: 'var(--color-text)', border: '1px solid var(--color-border)', padding: '10px 22px', borderRadius: 8, fontWeight: 600, fontSize: 13, cursor: 'pointer' }}>
                            Save draft
                        </button>
                        <button type="button" onClick={e => submit(e, 'sign')} disabled={processing}
                            style={{ background: 'var(--color-green)', color: '#fff', padding: '10px 22px', borderRadius: 8, fontWeight: 600, fontSize: 13, cursor: 'pointer', border: 'none' }}>
                            Sign & close
                        </button>
                    </div>
                </div>
            </form>
        </AppLayout>
    );
}
