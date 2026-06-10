import { Link, useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

const TYPES = [
    { value: 'consultation', label: 'Consultation' },
    { value: 'followup',     label: 'Follow-up' },
    { value: 'lab',          label: 'Lab / Test' },
    { value: 'dental',       label: 'Dental' },
    { value: 'eye',          label: 'Eye Care' },
    { value: 'procedure',    label: 'Procedure' },
    { value: 'other',        label: 'Other' },
];

function inputStyle() {
    return { width: '100%', padding: '9px 12px', borderRadius: 8, border: '1.5px solid var(--color-border)', fontSize: 13, background: 'var(--color-card)', color: 'var(--color-text)', boxSizing: 'border-box' };
}

function FormField({ label, error, children }) {
    return (
        <div>
            <label style={{ display: 'block', fontSize: 12, fontWeight: 600, color: 'var(--color-text-2)', marginBottom: 6 }}>{label}</label>
            {children}
            {error && <div style={{ fontSize: 11, color: 'var(--color-red)', marginTop: 4 }}>{error}</div>}
        </div>
    );
}

export default function AppointmentCreate({ providers }) {
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const minDate = tomorrow.toISOString().split('T')[0];

    const { data, setData, post, processing, errors } = useForm({
        provider_id:      '',
        appointment_date: '',
        appointment_time: '09:00',
        type:             'consultation',
        reason:           '',
        notes:            '',
    });

    function submit(e) {
        e.preventDefault();
        post('/patient/appointments');
    }

    return (
        <AppLayout title="Book Appointment">
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 24 }}>
                <div>
                    <h1 style={{ fontSize: 22, fontWeight: 800, color: 'var(--color-navy)', margin: 0 }}>Book an Appointment</h1>
                    <p style={{ fontSize: 13, color: 'var(--color-text-2)', marginTop: 4 }}>Request a consultation with a registered provider.</p>
                </div>
                <Link href="/patient/appointments" style={{ background: 'var(--color-card)', color: 'var(--color-text-2)', border: '1.5px solid var(--color-border)', borderRadius: 8, padding: '8px 16px', fontSize: 13, fontWeight: 600, textDecoration: 'none' }}>
                    ← Back
                </Link>
            </div>

            <div style={{ maxWidth: 640 }}>
                <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12 }}>
                    <div style={{ padding: '16px 20px', borderBottom: '1px solid var(--color-border)' }}>
                        <div style={{ fontWeight: 700, fontSize: 14 }}>Appointment Details</div>
                    </div>
                    <div style={{ padding: 20 }}>
                        <form onSubmit={submit}>
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16, marginBottom: 16 }}>
                                <div style={{ gridColumn: 'span 2' }}>
                                    <FormField label="Provider *" error={errors.provider_id}>
                                        <select style={inputStyle()} value={data.provider_id} onChange={e => setData('provider_id', e.target.value)} required>
                                            <option value="">— Select a provider —</option>
                                            {providers.map(prov => (
                                                <option key={prov.id} value={prov.id}>
                                                    {prov.name}
                                                    {prov.provider?.specialty ? ` — ${prov.provider.specialty}` : ''}
                                                    {prov.provider?.facility?.name ? ` · ${prov.provider.facility.name}` : ''}
                                                </option>
                                            ))}
                                        </select>
                                    </FormField>
                                </div>
                                <FormField label="Date *" error={errors.appointment_date}>
                                    <input style={inputStyle()} type="date" value={data.appointment_date} onChange={e => setData('appointment_date', e.target.value)} min={minDate} required />
                                </FormField>
                                <FormField label="Time *" error={errors.appointment_time}>
                                    <input style={inputStyle()} type="time" value={data.appointment_time} onChange={e => setData('appointment_time', e.target.value)} required />
                                </FormField>
                                <FormField label="Appointment Type *" error={errors.type}>
                                    <select style={inputStyle()} value={data.type} onChange={e => setData('type', e.target.value)} required>
                                        {TYPES.map(t => <option key={t.value} value={t.value}>{t.label}</option>)}
                                    </select>
                                </FormField>
                                <FormField label="Reason for Visit *" error={errors.reason}>
                                    <input style={inputStyle()} type="text" value={data.reason} onChange={e => setData('reason', e.target.value)} maxLength={300} required placeholder="Brief description…" />
                                </FormField>
                                <div style={{ gridColumn: 'span 2' }}>
                                    <FormField label="Additional Notes" error={errors.notes}>
                                        <textarea style={{ ...inputStyle(), resize: 'vertical', minHeight: 80 }} value={data.notes} onChange={e => setData('notes', e.target.value)} rows={3} maxLength={500} placeholder="Symptoms, questions, anything else the provider should know…" />
                                    </FormField>
                                </div>
                            </div>

                            <div style={{ display: 'flex', gap: 8, padding: '10px 14px', background: 'var(--color-navy-lt)', borderRadius: 8, fontSize: 12, color: 'var(--color-navy)', alignItems: 'center', marginBottom: 16 }}>
                                <span>ℹ️</span>
                                <span>Appointments are requests — the provider will confirm or suggest a different time. You will be notified in-app.</span>
                            </div>

                            <div style={{ display: 'flex', gap: 10 }}>
                                <button type="submit" disabled={processing} style={{ background: 'var(--color-navy)', color: '#fff', border: 'none', borderRadius: 8, padding: '9px 20px', fontSize: 13, fontWeight: 600, cursor: 'pointer' }}>
                                    {processing ? 'Submitting…' : 'Request Appointment'}
                                </button>
                                <Link href="/patient/appointments" style={{ background: 'var(--color-card)', color: 'var(--color-text-2)', border: '1.5px solid var(--color-border)', borderRadius: 8, padding: '9px 20px', fontSize: 13, fontWeight: 600, textDecoration: 'none' }}>
                                    Cancel
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
