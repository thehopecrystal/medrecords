import { useForm, Link } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

const labelStyle = { display: 'block', fontSize: 12, fontWeight: 600, color: 'var(--color-text-2)', marginBottom: 5, textTransform: 'uppercase', letterSpacing: '0.04em' };
const inputStyle = { width: '100%', padding: '9px 12px', border: '1px solid var(--color-border)', borderRadius: 8, fontSize: 13, background: 'var(--color-card)', color: 'var(--color-text)', boxSizing: 'border-box', outline: 'none' };

export default function ReferralNew({ patient }) {
    const { data, setData, post, processing } = useForm({
        referred_to: '',
        specialty: '',
        reason: '',
        notes: '',
    });

    function handleSubmit(e) {
        e.preventDefault();
        post(`/provider/patients/${patient.id}/refer`);
    }

    return (
        <AppLayout title="New Referral">
            <div style={{ marginBottom: 16 }}>
                <Link href={`/provider/patients/${patient.id}`} style={{ background: 'var(--color-card)', color: 'var(--color-text)', border: '1px solid var(--color-border)', padding: '7px 14px', borderRadius: 7, fontSize: 12, fontWeight: 600, textDecoration: 'none' }}>
                    Back to {patient.user?.name}
                </Link>
            </div>

            <div style={{ marginBottom: 20 }}>
                <h1 style={{ fontSize: 20, fontWeight: 800, color: 'var(--color-navy)', margin: 0 }}>Referral — {patient.user?.name}</h1>
                <p style={{ fontSize: 13, color: 'var(--color-text-3)', marginTop: 4 }}>Create a structured referral to another specialist or facility</p>
            </div>

            <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12, padding: '20px 24px', maxWidth: 640 }}>
                <form onSubmit={handleSubmit}>
                    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '0 20px' }}>
                        <div style={{ marginBottom: 16 }}>
                            <label style={labelStyle}>Refer to (facility / specialist)</label>
                            <input style={inputStyle} value={data.referred_to} onChange={e => setData('referred_to', e.target.value)} placeholder="e.g. Lagos University Teaching Hospital" required />
                        </div>
                        <div style={{ marginBottom: 16 }}>
                            <label style={labelStyle}>Specialty</label>
                            <input style={inputStyle} value={data.specialty} onChange={e => setData('specialty', e.target.value)} placeholder="e.g. Endocrinology" required />
                        </div>
                        <div style={{ marginBottom: 16, gridColumn: '1 / -1' }}>
                            <label style={labelStyle}>Reason for referral</label>
                            <textarea value={data.reason} onChange={e => setData('reason', e.target.value)} rows={3} placeholder="Clinical reason for this referral..." required style={{ ...inputStyle, resize: 'vertical' }} />
                        </div>
                        <div style={{ marginBottom: 16, gridColumn: '1 / -1' }}>
                            <label style={labelStyle}>Additional notes (optional)</label>
                            <textarea value={data.notes} onChange={e => setData('notes', e.target.value)} rows={2} placeholder="Any further notes for the receiving specialist..." style={{ ...inputStyle, resize: 'vertical' }} />
                        </div>
                    </div>
                    <div style={{ display: 'flex', gap: 10, paddingTop: 16, borderTop: '1px solid var(--color-border)' }}>
                        <Link href={`/provider/patients/${patient.id}`} style={{ background: 'var(--color-card)', color: 'var(--color-text)', border: '1px solid var(--color-border)', padding: '10px 22px', borderRadius: 8, fontWeight: 600, fontSize: 13, textDecoration: 'none' }}>
                            Cancel
                        </Link>
                        <button type="submit" disabled={processing} style={{ background: 'var(--color-navy)', color: '#fff', padding: '10px 22px', borderRadius: 8, fontWeight: 600, fontSize: 13, cursor: 'pointer', border: 'none' }}>
                            Create referral
                        </button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
