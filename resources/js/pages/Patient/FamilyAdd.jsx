import { Link, useForm, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

function inputStyle() {
    return { width: '100%', padding: '9px 12px', borderRadius: 8, border: '1.5px solid var(--color-border)', fontSize: 13, background: 'var(--color-card)', color: 'var(--color-text)', boxSizing: 'border-box' };
}

function FormField({ label, error, children, full = false }) {
    return (
        <div style={{ gridColumn: full ? 'span 2' : 'span 1' }}>
            <label style={{ display: 'block', fontSize: 12, fontWeight: 600, color: 'var(--color-text-2)', marginBottom: 6 }}>{label}</label>
            {children}
            {error && <div style={{ fontSize: 11, color: 'var(--color-red)', marginTop: 4 }}>{error}</div>}
        </div>
    );
}

export default function FamilyAdd() {
    const { errors: pageErrors } = usePage().props;
    const { data, setData, post, processing, errors } = useForm({
        name:         '',
        dob:          '',
        gender:       '',
        relationship: '',
    });

    const yesterday = new Date(Date.now() - 86400000).toISOString().split('T')[0];

    function submit(e) {
        e.preventDefault();
        post('/patient/family');
    }

    return (
        <AppLayout title="Add Dependent">
            <div style={{ marginBottom: 16 }}>
                <Link href="/patient/family" style={{ display: 'inline-flex', alignItems: 'center', gap: 6, fontSize: 13, fontWeight: 600, color: 'var(--color-text-2)', background: 'var(--color-border)', borderRadius: 8, padding: '6px 14px', textDecoration: 'none' }}>
                    ← Back to My Family
                </Link>
            </div>

            <div style={{ marginBottom: 20 }}>
                <h1 style={{ fontSize: 22, fontWeight: 800, color: 'var(--color-navy)', margin: 0 }}>Add dependent</h1>
                <p style={{ fontSize: 13, color: 'var(--color-text-2)', marginTop: 4 }}>Create a MedRecords account for a family member under your care.</p>
            </div>

            {(pageErrors && Object.keys(pageErrors).length > 0) && (
                <div style={{ display: 'flex', gap: 8, padding: '10px 14px', background: 'var(--color-red-lt)', border: '1px solid var(--color-red)', borderRadius: 8, fontSize: 13, color: 'var(--color-red)', marginBottom: 16 }}>
                    <span>✗</span>
                    <span>{Object.values(pageErrors)[0]}</span>
                </div>
            )}

            <div style={{ maxWidth: 540, background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12, padding: 20 }}>
                <form onSubmit={submit}>
                    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16, marginBottom: 16 }}>
                        <FormField label="Full name" error={errors.name} full>
                            <input style={inputStyle()} type="text" value={data.name} onChange={e => setData('name', e.target.value)} placeholder="e.g. Chioma Okonkwo" required />
                        </FormField>
                        <FormField label="Date of birth" error={errors.dob}>
                            <input style={inputStyle()} type="date" value={data.dob} onChange={e => setData('dob', e.target.value)} max={yesterday} required />
                        </FormField>
                        <FormField label="Gender" error={errors.gender}>
                            <select style={{ ...inputStyle(), cursor: 'pointer' }} value={data.gender} onChange={e => setData('gender', e.target.value)} required>
                                <option value="">— select —</option>
                                <option value="female">Female</option>
                                <option value="male">Male</option>
                                <option value="other">Other</option>
                            </select>
                        </FormField>
                        <FormField label="Relationship" error={errors.relationship} full>
                            <select style={{ ...inputStyle(), cursor: 'pointer' }} value={data.relationship} onChange={e => setData('relationship', e.target.value)} required>
                                <option value="">— select —</option>
                                <option value="son">Son</option>
                                <option value="daughter">Daughter</option>
                                <option value="mother">Mother</option>
                                <option value="father">Father</option>
                                <option value="spouse">Spouse</option>
                                <option value="ward">Ward / guardianship</option>
                                <option value="other">Other</option>
                            </select>
                        </FormField>
                    </div>

                    <div style={{ display: 'flex', gap: 8, padding: '10px 14px', background: 'var(--color-navy-lt)', borderRadius: 8, fontSize: 12, color: 'var(--color-navy)', alignItems: 'flex-start', marginBottom: 16 }}>
                        <span style={{ flexShrink: 0, marginTop: 1 }}>ℹ</span>
                        <span>A MedRecords account will be created for this dependent. You will manage their record until they turn 16, at which point they can take ownership.</span>
                    </div>

                    <div style={{ display: 'flex', gap: 10 }}>
                        <Link href="/patient/family" style={{ background: 'var(--color-card)', color: 'var(--color-text-2)', border: '1.5px solid var(--color-border)', borderRadius: 8, padding: '9px 20px', fontSize: 13, fontWeight: 600, textDecoration: 'none' }}>
                            Cancel
                        </Link>
                        <button type="submit" disabled={processing} style={{ background: 'var(--color-navy)', color: '#fff', border: 'none', borderRadius: 8, padding: '9px 20px', fontSize: 13, fontWeight: 600, cursor: 'pointer' }}>
                            {processing ? 'Adding…' : 'Add dependent'}
                        </button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
