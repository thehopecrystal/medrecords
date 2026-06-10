import { Link, useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

function inputStyle() {
    return { width: '100%', padding: '9px 12px', borderRadius: 8, border: '1.5px solid var(--color-border)', fontSize: 13, background: 'var(--color-card)', color: 'var(--color-text)', boxSizing: 'border-box' };
}

function FormField({ label, subLabel, error, children, span2 = false }) {
    return (
        <div style={{ gridColumn: span2 ? 'span 2' : 'span 1' }}>
            <label style={{ display: 'block', fontSize: 12, fontWeight: 600, color: 'var(--color-text-2)', marginBottom: 6 }}>
                {label}
                {subLabel && <span style={{ color: 'var(--color-text-3)', fontWeight: 400 }}> {subLabel}</span>}
            </label>
            {children}
            {error && <div style={{ fontSize: 11, color: 'var(--color-red)', marginTop: 4 }}>{error}</div>}
        </div>
    );
}

export default function LabUpload() {
    const today = new Date().toISOString().split('T')[0];

    const { data, setData, post, processing, errors } = useForm({
        test_name:    '',
        result:       '',
        collected_at: '',
        flag:         'normal',
        notes:        '',
        file:         null,
    });

    function submit(e) {
        e.preventDefault();
        post('/patient/lab-upload', {
            forceFormData: true,
        });
    }

    return (
        <AppLayout title="Upload Lab Result">
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 24 }}>
                <div>
                    <h1 style={{ fontSize: 22, fontWeight: 800, color: 'var(--color-navy)', margin: 0 }}>Upload Lab Result</h1>
                    <p style={{ fontSize: 13, color: 'var(--color-text-2)', marginTop: 4 }}>Add a lab result from an external lab or clinic.</p>
                </div>
                <Link href="/patient/record?tab=labs" style={{ background: 'var(--color-card)', color: 'var(--color-text-2)', border: '1.5px solid var(--color-border)', borderRadius: 8, padding: '8px 16px', fontSize: 13, fontWeight: 600, textDecoration: 'none' }}>
                    ← Back to Records
                </Link>
            </div>

            <div style={{ maxWidth: 640 }}>
                <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12 }}>
                    <div style={{ padding: '14px 20px', borderBottom: '1px solid var(--color-border)' }}>
                        <div style={{ fontWeight: 700, fontSize: 14 }}>Lab Result Details</div>
                        <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>Patient-uploaded results are marked separately from provider-ordered tests.</div>
                    </div>
                    <div style={{ padding: 20 }}>
                        <form onSubmit={submit} encType="multipart/form-data">
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16, marginBottom: 16 }}>
                                <FormField label="Test Name *" error={errors.test_name} span2>
                                    <input style={inputStyle()} type="text" value={data.test_name} onChange={e => setData('test_name', e.target.value)} required placeholder="e.g. Full Blood Count, HbA1c, Lipid Panel" />
                                </FormField>
                                <FormField label="Result *" error={errors.result} span2>
                                    <input style={inputStyle()} type="text" value={data.result} onChange={e => setData('result', e.target.value)} required placeholder="e.g. 5.4 mmol/L, Negative, See file" />
                                </FormField>
                                <FormField label="Collection Date *" error={errors.collected_at}>
                                    <input style={inputStyle()} type="date" value={data.collected_at} onChange={e => setData('collected_at', e.target.value)} max={today} required />
                                </FormField>
                                <FormField label="Flag" error={errors.flag}>
                                    <select style={{ ...inputStyle(), cursor: 'pointer' }} value={data.flag} onChange={e => setData('flag', e.target.value)}>
                                        <option value="normal">Normal</option>
                                        <option value="high">High</option>
                                        <option value="low">Low</option>
                                        <option value="critical">Critical</option>
                                    </select>
                                </FormField>
                                <FormField label="Notes" span2>
                                    <textarea style={{ ...inputStyle(), resize: 'vertical', minHeight: 70 }} value={data.notes} onChange={e => setData('notes', e.target.value)} rows={3} placeholder="Lab name, doctor's comments, reference range…" />
                                </FormField>
                                <FormField label="Attach File" subLabel="(PDF, JPG, PNG — max 5 MB)" error={errors.file} span2>
                                    <input
                                        type="file"
                                        name="file"
                                        accept=".pdf,.jpg,.jpeg,.png"
                                        onChange={e => setData('file', e.target.files[0])}
                                        style={{ background: 'var(--color-card)', padding: 8, border: '1.5px dashed var(--color-border)', borderRadius: 8, cursor: 'pointer', width: '100%', boxSizing: 'border-box' }}
                                    />
                                </FormField>
                            </div>

                            <div style={{ display: 'flex', gap: 8, padding: '10px 14px', background: 'var(--color-amber-lt)', borderRadius: 8, fontSize: 12, color: 'var(--color-amber)', alignItems: 'center', marginBottom: 16 }}>
                                <span>⚠️</span>
                                <span>Only upload genuine lab results. Patient-uploaded results are flagged as self-reported and do not replace clinician-ordered tests.</span>
                            </div>

                            <div style={{ display: 'flex', gap: 10 }}>
                                <button type="submit" disabled={processing} style={{ background: 'var(--color-navy)', color: '#fff', border: 'none', borderRadius: 8, padding: '9px 20px', fontSize: 13, fontWeight: 600, cursor: 'pointer' }}>
                                    {processing ? 'Uploading…' : 'Upload Result'}
                                </button>
                                <Link href="/patient/record?tab=labs" style={{ background: 'var(--color-card)', color: 'var(--color-text-2)', border: '1.5px solid var(--color-border)', borderRadius: 8, padding: '9px 20px', fontSize: 13, fontWeight: 600, textDecoration: 'none' }}>
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
