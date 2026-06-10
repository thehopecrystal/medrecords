import { useForm, Link } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

const labelStyle = { display: 'block', fontSize: 12, fontWeight: 600, color: 'var(--color-text-2)', marginBottom: 5, textTransform: 'uppercase', letterSpacing: '0.04em' };
const inputStyle = { width: '100%', padding: '9px 12px', border: '1px solid var(--color-border)', borderRadius: 8, fontSize: 13, background: 'var(--color-card)', color: 'var(--color-text)', boxSizing: 'border-box', outline: 'none' };

export default function LabResultNew({ patient }) {
    const today = new Date().toISOString().split('T')[0];

    const { data, setData, post, processing } = useForm({
        test_name: '',
        loinc_code: '',
        result: '',
        unit: '',
        reference_range: '',
        flag: 'normal',
        collected_at: today,
        notes: '',
    });

    function handleSubmit(e) {
        e.preventDefault();
        post(`/provider/patients/${patient.id}/lab`);
    }

    return (
        <AppLayout title="Upload Lab Result">
            <div style={{ marginBottom: 16 }}>
                <Link href={`/provider/patients/${patient.id}?tab=labs`} style={{ background: 'var(--color-card)', color: 'var(--color-text)', border: '1px solid var(--color-border)', padding: '7px 14px', borderRadius: 7, fontSize: 12, fontWeight: 600, textDecoration: 'none' }}>
                    Back to {patient.user?.name}
                </Link>
            </div>

            <div style={{ marginBottom: 20 }}>
                <h1 style={{ fontSize: 20, fontWeight: 800, color: 'var(--color-navy)', margin: 0 }}>Upload lab result — {patient.user?.name}</h1>
            </div>

            <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12, padding: '20px 24px', maxWidth: 640 }}>
                <form onSubmit={handleSubmit}>
                    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '0 20px' }}>
                        <div style={{ marginBottom: 16 }}>
                            <label style={labelStyle}>Test name</label>
                            <input style={inputStyle} value={data.test_name} onChange={e => setData('test_name', e.target.value)} placeholder="e.g. HbA1c" required />
                        </div>
                        <div style={{ marginBottom: 16 }}>
                            <label style={labelStyle}>LOINC code (optional)</label>
                            <input style={inputStyle} value={data.loinc_code} onChange={e => setData('loinc_code', e.target.value)} placeholder="e.g. 4548-4" />
                        </div>
                        <div style={{ marginBottom: 16 }}>
                            <label style={labelStyle}>Result</label>
                            <input style={inputStyle} value={data.result} onChange={e => setData('result', e.target.value)} placeholder="e.g. 7.4" required />
                        </div>
                        <div style={{ marginBottom: 16 }}>
                            <label style={labelStyle}>Unit</label>
                            <input style={inputStyle} value={data.unit} onChange={e => setData('unit', e.target.value)} placeholder="e.g. %" />
                        </div>
                        <div style={{ marginBottom: 16 }}>
                            <label style={labelStyle}>Reference range</label>
                            <input style={inputStyle} value={data.reference_range} onChange={e => setData('reference_range', e.target.value)} placeholder="e.g. < 5.7%" />
                        </div>
                        <div style={{ marginBottom: 16 }}>
                            <label style={labelStyle}>Flag</label>
                            <select value={data.flag} onChange={e => setData('flag', e.target.value)} style={inputStyle}>
                                <option value="normal">Normal</option>
                                <option value="high">High</option>
                                <option value="low">Low</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                        <div style={{ marginBottom: 16 }}>
                            <label style={labelStyle}>Specimen collected on</label>
                            <input type="date" style={inputStyle} value={data.collected_at} onChange={e => setData('collected_at', e.target.value)} required />
                        </div>
                        <div style={{ marginBottom: 16, gridColumn: '1 / -1' }}>
                            <label style={labelStyle}>Notes</label>
                            <textarea rows={2} style={{ ...inputStyle, resize: 'vertical' }} value={data.notes} onChange={e => setData('notes', e.target.value)} placeholder="Any additional clinical context..." />
                        </div>
                    </div>
                    <div style={{ display: 'flex', gap: 10, paddingTop: 16, borderTop: '1px solid var(--color-border)' }}>
                        <Link href={`/provider/patients/${patient.id}`} style={{ background: 'var(--color-card)', color: 'var(--color-text)', border: '1px solid var(--color-border)', padding: '10px 22px', borderRadius: 8, fontWeight: 600, fontSize: 13, textDecoration: 'none' }}>
                            Cancel
                        </Link>
                        <button type="submit" disabled={processing} style={{ background: 'var(--color-navy)', color: '#fff', padding: '10px 22px', borderRadius: 8, fontWeight: 600, fontSize: 13, cursor: 'pointer', border: 'none' }}>
                            Upload result
                        </button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
