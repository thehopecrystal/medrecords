import { useForm, Link } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

const labelStyle = { display: 'block', fontSize: 12, fontWeight: 600, color: 'var(--color-text-2)', marginBottom: 5, textTransform: 'uppercase', letterSpacing: '0.04em' };
const inputStyle = { width: '100%', padding: '9px 12px', border: '1px solid var(--color-border)', borderRadius: 8, fontSize: 13, background: 'var(--color-card)', color: 'var(--color-text)', boxSizing: 'border-box', outline: 'none' };

export default function ConsentRequest({ patient }) {
    const { data, setData, post, processing } = useForm({
        scope: 'full',
        expires_in: '365',
    });

    function handleSubmit(e) {
        e.preventDefault();
        post(`/provider/patients/${patient.id}/consent-request`);
    }

    return (
        <AppLayout title="Request Consent">
            <div style={{ marginBottom: 16 }}>
                <Link href={`/provider/patients/${patient.id}`} style={{ background: 'var(--color-card)', color: 'var(--color-text)', border: '1px solid var(--color-border)', padding: '7px 14px', borderRadius: 7, fontSize: 12, fontWeight: 600, textDecoration: 'none' }}>
                    Back to {patient.user?.name}
                </Link>
            </div>

            <div style={{ marginBottom: 20 }}>
                <h1 style={{ fontSize: 20, fontWeight: 800, color: 'var(--color-navy)', margin: 0 }}>Request consent — {patient.user?.name}</h1>
                <p style={{ fontSize: 13, color: 'var(--color-text-3)', marginTop: 4 }}>The patient will receive an in-app notification to approve or decline.</p>
            </div>

            <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12, padding: '20px 24px', maxWidth: 560 }}>
                <form onSubmit={handleSubmit}>
                    <div style={{ marginBottom: 16 }}>
                        <label style={labelStyle}>Access scope</label>
                        <select value={data.scope} onChange={e => setData('scope', e.target.value)} style={inputStyle} required>
                            <option value="full">Full access — all categories</option>
                            <option value="encounter">Encounters & visits only</option>
                            <option value="prescription">Prescriptions only</option>
                            <option value="lab">Lab results only</option>
                            <option value="dental">Dental records only</option>
                            <option value="eye_care">Eye care records only</option>
                            <option value="vaccination">Vaccinations only</option>
                        </select>
                    </div>
                    <div style={{ marginBottom: 16 }}>
                        <label style={labelStyle}>Duration (days)</label>
                        <select value={data.expires_in} onChange={e => setData('expires_in', e.target.value)} style={inputStyle} required>
                            <option value="7">7 days — single visit</option>
                            <option value="30">30 days — short-term care</option>
                            <option value="90">90 days — episode of care</option>
                            <option value="180">180 days — 6 months</option>
                            <option value="365">365 days — one year</option>
                        </select>
                    </div>
                    <div style={{ display: 'flex', alignItems: 'flex-start', gap: 10, padding: '12px 16px', borderRadius: 10, background: 'var(--color-navy-lt)', color: 'var(--color-navy)', fontSize: 13, borderLeft: '3px solid var(--color-navy)', marginBottom: 16 }}>
                        <span>i</span>
                        <span>The patient must approve this request before you can access their record. They can revoke at any time.</span>
                    </div>
                    <div style={{ display: 'flex', gap: 10, paddingTop: 16, borderTop: '1px solid var(--color-border)' }}>
                        <Link href={`/provider/patients/${patient.id}`} style={{ background: 'var(--color-card)', color: 'var(--color-text)', border: '1px solid var(--color-border)', padding: '10px 22px', borderRadius: 8, fontWeight: 600, fontSize: 13, textDecoration: 'none' }}>
                            Cancel
                        </Link>
                        <button type="submit" disabled={processing} style={{ background: 'var(--color-navy)', color: '#fff', padding: '10px 22px', borderRadius: 8, fontWeight: 600, fontSize: 13, cursor: 'pointer', border: 'none' }}>
                            Send request to patient
                        </button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
