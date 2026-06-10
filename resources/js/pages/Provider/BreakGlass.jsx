import { useForm, Link } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

const labelStyle = { display: 'block', fontSize: 12, fontWeight: 600, color: 'var(--color-text-2)', marginBottom: 5, textTransform: 'uppercase', letterSpacing: '0.04em' };
const inputStyle = { width: '100%', padding: '9px 12px', border: '1px solid var(--color-border)', borderRadius: 8, fontSize: 13, background: 'var(--color-card)', color: 'var(--color-text)', boxSizing: 'border-box', outline: 'none' };

export default function BreakGlass({ patient }) {
    const { data, setData, post, processing } = useForm({
        reason_code: '',
        justification: '',
    });

    function handleSubmit(e) {
        e.preventDefault();
        post(`/provider/patients/${patient.id}/break-glass`);
    }

    return (
        <AppLayout title="Emergency Access">
            {/* Alert */}
            <div style={{ display: 'flex', alignItems: 'flex-start', gap: 10, padding: '12px 16px', borderRadius: 10, background: 'var(--color-red-lt)', color: 'var(--color-red)', fontSize: 13, borderLeft: '3px solid var(--color-red)', marginBottom: 20 }}>
                <span>!</span>
                <span><strong>Break-glass access.</strong> Patient consent is not on file. Invoking emergency access will be reviewed by compliance. The patient will be notified immediately.</span>
            </div>

            <div style={{ marginBottom: 16 }}>
                <Link href={`/provider/patients/${patient.id}`} style={{ background: 'var(--color-card)', color: 'var(--color-text)', border: '1px solid var(--color-border)', padding: '7px 14px', borderRadius: 7, fontSize: 12, fontWeight: 600, textDecoration: 'none' }}>
                    Cancel
                </Link>
            </div>

            <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12, padding: '20px 24px', maxWidth: 640 }}>
                <div style={{ marginBottom: 20 }}>
                    <div style={{ fontSize: 16, fontWeight: 700, color: 'var(--color-navy)' }}>Justification required — {patient.user?.name}</div>
                    <div style={{ fontSize: 13, color: 'var(--color-text-3)', marginTop: 4 }}>All break-glass events are logged, reviewed, and visible to the patient.</div>
                </div>

                <form onSubmit={handleSubmit}>
                    <div style={{ marginBottom: 16 }}>
                        <label style={labelStyle}>Reason code</label>
                        <select value={data.reason_code} onChange={e => setData('reason_code', e.target.value)} style={inputStyle} required>
                            <option value="">— select —</option>
                            <option value="life_threatening_emergency">Life-threatening emergency — patient unconscious</option>
                            <option value="suspected_overdose">Suspected overdose or poisoning</option>
                            <option value="trauma">Major trauma requiring urgent history</option>
                            <option value="other_emergency">Other clinical emergency</option>
                        </select>
                    </div>
                    <div style={{ marginBottom: 16 }}>
                        <label style={labelStyle}>Justification (minimum 20 characters)</label>
                        <textarea
                            value={data.justification}
                            onChange={e => setData('justification', e.target.value)}
                            rows={4}
                            placeholder="Describe the clinical emergency in detail..."
                            required
                            minLength={20}
                            style={{ ...inputStyle, resize: 'vertical' }}
                        />
                    </div>
                    <div style={{ display: 'flex', gap: 10, paddingTop: 16, borderTop: '1px solid var(--color-border)' }}>
                        <Link href={`/provider/patients/${patient.id}`} style={{ background: 'var(--color-card)', color: 'var(--color-text)', border: '1px solid var(--color-border)', padding: '10px 22px', borderRadius: 8, fontWeight: 600, fontSize: 13, textDecoration: 'none' }}>
                            Cancel
                        </Link>
                        <button type="submit" disabled={processing} style={{ background: 'var(--color-red)', color: '#fff', padding: '10px 22px', borderRadius: 8, fontWeight: 600, fontSize: 13, cursor: 'pointer', border: 'none' }}>
                            Proceed with break-glass
                        </button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
