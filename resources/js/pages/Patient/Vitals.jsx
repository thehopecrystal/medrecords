import { useEffect, useRef } from 'react';
import { useForm, router } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import { fmtDateTime } from '@/utils/fmt';

function inputStyle() {
    return { width: '100%', padding: '8px 10px', borderRadius: 8, border: '1.5px solid var(--color-border)', fontSize: 13, background: 'var(--color-card)', color: 'var(--color-text)', boxSizing: 'border-box' };
}

function FormField({ label, error, children }) {
    return (
        <div>
            <label style={{ display: 'block', fontSize: 12, fontWeight: 600, color: 'var(--color-text-2)', marginBottom: 5 }}>{label}</label>
            {children}
            {error && <div style={{ fontSize: 11, color: 'var(--color-red)', marginTop: 3 }}>{error}</div>}
        </div>
    );
}

function now() {
    const d = new Date();
    d.setSeconds(0, 0);
    return d.toISOString().slice(0, 16);
}

export default function Vitals({ latest, logs, chartData }) {
    const canvasRef = useRef(null);
    const chartRef = useRef(null);

    const { data, setData, post, processing, errors, reset } = useForm({
        logged_at:         now(),
        bp_systolic:       '',
        bp_diastolic:      '',
        heart_rate:        '',
        temperature_c:     '',
        weight_kg:         '',
        glucose_mmol:      '',
        oxygen_saturation: '',
        pain_level:        '',
        notes:             '',
    });

    function submit(e) {
        e.preventDefault();
        post('/patient/vitals', { onSuccess: () => reset() });
    }

    function deleteLog(id) {
        if (confirm('Delete this reading?')) {
            router.delete(`/patient/vitals/${id}`);
        }
    }

    // Build chart
    useEffect(() => {
        if (!canvasRef.current || !chartData || chartData.length === 0) return;

        import('https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js').catch(() => null);

        let attempts = 0;
        const tryChart = setInterval(() => {
            attempts++;
            if (typeof window.Chart !== 'undefined') {
                clearInterval(tryChart);
                if (chartRef.current) chartRef.current.destroy();
                chartRef.current = new window.Chart(canvasRef.current, {
                    type: 'line',
                    data: {
                        labels: chartData.map(d => d.label),
                        datasets: [
                            { label: 'Systolic', data: chartData.map(d => d.sys), borderColor: '#B23A48', backgroundColor: 'rgba(178,58,72,0.08)', borderWidth: 2, pointRadius: 2, tension: 0.3, fill: true },
                            { label: 'Diastolic', data: chartData.map(d => d.dia), borderColor: '#1E3A5F', backgroundColor: 'rgba(30,58,95,0.05)', borderWidth: 2, pointRadius: 2, tension: 0.3, fill: true },
                        ],
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } } },
                        scales: {
                            x: { grid: { display: false }, ticks: { maxTicksLimit: 8, font: { size: 10 } } },
                            y: { beginAtZero: false, min: 40, grid: { color: '#F1F5F9' }, ticks: { font: { size: 11 } } },
                        },
                    },
                });
            }
            if (attempts > 20) clearInterval(tryChart);
        }, 300);

        return () => { clearInterval(tryChart); };
    }, [chartData]);

    const statCards = latest ? [
        latest.bp_systolic  && { label: 'Blood Pressure', value: `${latest.bp_systolic}/${latest.bp_diastolic}`, sub: 'mmHg', icon: '❤️', color: '' },
        latest.heart_rate   && { label: 'Heart Rate',  value: latest.heart_rate, sub: 'bpm', icon: '💓', color: 'green' },
        latest.weight_kg    && { label: 'Weight',      value: latest.weight_kg,  sub: 'kg',  icon: '⚖️', color: '' },
        latest.glucose_mmol && { label: 'Blood Glucose', value: latest.glucose_mmol, sub: 'mmol/L', icon: '🩸', color: 'amber' },
        latest.oxygen_saturation && { label: 'SpO₂', value: `${latest.oxygen_saturation}%`, sub: 'Oxygen sat.', icon: '🫁', color: '' },
    ].filter(Boolean) : [];

    const statColor = {
        '':      { background: 'var(--color-card)', border: '1px solid var(--color-border)' },
        green:   { background: 'var(--color-green-lt)', border: '1px solid var(--color-green)' },
        amber:   { background: 'var(--color-amber-lt)', border: '1px solid var(--color-amber)' },
    };

    return (
        <AppLayout title="Vitals Diary">
            <div style={{ marginBottom: 24 }}>
                <h1 style={{ fontSize: 22, fontWeight: 800, color: 'var(--color-navy)', margin: 0 }}>Vitals Diary</h1>
                <p style={{ fontSize: 13, color: 'var(--color-text-2)', marginTop: 4 }}>Track your daily health measurements.</p>
            </div>

            {/* Latest readings */}
            {statCards.length > 0 && (
                <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(150px, 1fr))', gap: 12, marginBottom: 24 }}>
                    {statCards.map(s => (
                        <div key={s.label} style={{ borderRadius: 12, padding: '14px 16px', position: 'relative', ...(statColor[s.color] || statColor['']) }}>
                            <div style={{ fontSize: 11, color: 'var(--color-text-2)', fontWeight: 600, marginBottom: 6 }}>{s.label}</div>
                            <div style={{ fontSize: 20, fontWeight: 800, color: 'var(--color-navy)' }}>{s.value}</div>
                            <div style={{ fontSize: 11, color: 'var(--color-text-2)' }}>{s.sub}</div>
                            <div style={{ position: 'absolute', top: 12, right: 12, fontSize: 18 }}>{s.icon}</div>
                        </div>
                    ))}
                </div>
            )}

            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 20, marginBottom: 24 }}>
                {/* Log form */}
                <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12 }}>
                    <div style={{ padding: '14px 20px', borderBottom: '1px solid var(--color-border)', fontWeight: 700, fontSize: 14 }}>Log New Reading</div>
                    <div style={{ padding: 20 }}>
                        <form onSubmit={submit}>
                            <FormField label="Date & Time *" error={errors.logged_at}>
                                <input style={inputStyle()} type="datetime-local" value={data.logged_at} onChange={e => setData('logged_at', e.target.value)} required />
                            </FormField>
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12, margin: '12px 0' }}>
                                {[
                                    { name: 'bp_systolic', label: 'BP Systolic (mmHg)', min: 50, max: 300, ph: '120' },
                                    { name: 'bp_diastolic', label: 'BP Diastolic (mmHg)', min: 30, max: 200, ph: '80' },
                                    { name: 'heart_rate', label: 'Heart Rate (bpm)', min: 20, max: 300, ph: '72' },
                                    { name: 'temperature_c', label: 'Temperature (°C)', min: 30, max: 45, step: '0.1', ph: '36.6' },
                                    { name: 'weight_kg', label: 'Weight (kg)', min: 1, max: 500, step: '0.1', ph: '70.0' },
                                    { name: 'glucose_mmol', label: 'Blood Glucose (mmol/L)', min: 1, max: 50, step: '0.1', ph: '5.5' },
                                    { name: 'oxygen_saturation', label: 'SpO₂ (%)', min: 50, max: 100, ph: '98' },
                                    { name: 'pain_level', label: 'Pain Level (0–10)', min: 0, max: 10, ph: '0' },
                                ].map(f => (
                                    <FormField key={f.name} label={f.label}>
                                        <input style={inputStyle()} type="number" value={data[f.name]} onChange={e => setData(f.name, e.target.value)} min={f.min} max={f.max} step={f.step || '1'} placeholder={f.ph} />
                                    </FormField>
                                ))}
                            </div>
                            <FormField label="Notes">
                                <textarea style={{ ...inputStyle(), resize: 'vertical', minHeight: 60 }} value={data.notes} onChange={e => setData('notes', e.target.value)} rows={2} placeholder="e.g. after exercise, fasting…" />
                            </FormField>
                            <div style={{ marginTop: 16 }}>
                                <button type="submit" disabled={processing} style={{ background: 'var(--color-navy)', color: '#fff', border: 'none', borderRadius: 8, padding: '9px 20px', fontSize: 13, fontWeight: 600, cursor: 'pointer' }}>
                                    {processing ? 'Saving…' : 'Save Reading'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {/* BP chart */}
                <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12 }}>
                    <div style={{ padding: '14px 20px', borderBottom: '1px solid var(--color-border)' }}>
                        <div style={{ fontWeight: 700, fontSize: 14 }}>Blood Pressure Trend</div>
                        <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>Last 30 readings</div>
                    </div>
                    <div style={{ padding: 16 }}>
                        <canvas ref={canvasRef} height={200} />
                        {(!chartData || chartData.length === 0) && (
                            <div style={{ textAlign: 'center', color: 'var(--color-text-2)', fontSize: 13, padding: '40px 0' }}>No data yet.</div>
                        )}
                    </div>
                </div>
            </div>

            {/* History */}
            <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12 }}>
                <div style={{ padding: '14px 20px', borderBottom: '1px solid var(--color-border)', fontWeight: 700, fontSize: 14 }}>History</div>
                <div style={{ overflowX: 'auto' }}>
                    <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                        <thead>
                            <tr style={{ background: 'var(--color-border)' }}>
                                {['Date/Time','BP','HR','Temp','Weight','Glucose','SpO₂','Pain',''].map((h, i) => (
                                    <th key={i} style={{ padding: '10px 14px', fontSize: 11, fontWeight: 700, textAlign: 'left', color: 'var(--color-text-2)', textTransform: 'uppercase', letterSpacing: '0.04em', whiteSpace: 'nowrap' }}>{h}</th>
                                ))}
                            </tr>
                        </thead>
                        <tbody>
                            {logs.data && logs.data.length > 0 ? logs.data.map((log, i) => (
                                <tr key={log.id} style={{ borderTop: '1px solid var(--color-border)' }}>
                                    <td style={{ padding: '11px 14px', fontSize: 12, whiteSpace: 'nowrap' }}>{fmtDateTime(log.logged_at)}</td>
                                    <td style={{ padding: '11px 14px', fontSize: 13 }}>{log.bp || '—'}</td>
                                    <td style={{ padding: '11px 14px', fontSize: 13 }}>{log.heart_rate ? `${log.heart_rate} bpm` : '—'}</td>
                                    <td style={{ padding: '11px 14px', fontSize: 13 }}>{log.temperature_c ? `${log.temperature_c}°C` : '—'}</td>
                                    <td style={{ padding: '11px 14px', fontSize: 13 }}>{log.weight_kg ? `${log.weight_kg} kg` : '—'}</td>
                                    <td style={{ padding: '11px 14px', fontSize: 13 }}>{log.glucose_mmol ? `${log.glucose_mmol} mmol/L` : '—'}</td>
                                    <td style={{ padding: '11px 14px', fontSize: 13 }}>{log.oxygen_saturation ? `${log.oxygen_saturation}%` : '—'}</td>
                                    <td style={{ padding: '11px 14px', fontSize: 13 }}>{log.pain_level != null ? `${log.pain_level}/10` : '—'}</td>
                                    <td style={{ padding: '11px 14px' }}>
                                        <button onClick={() => deleteLog(log.id)} style={{ background: 'none', border: '1px solid var(--color-border)', borderRadius: 6, padding: '3px 8px', cursor: 'pointer', fontSize: 12, color: 'var(--color-text-3)' }}>✕</button>
                                    </td>
                                </tr>
                            )) : (
                                <tr>
                                    <td colSpan={9}>
                                        <div style={{ textAlign: 'center', padding: 32 }}>
                                            <div style={{ fontSize: 28, marginBottom: 8 }}>📊</div>
                                            <p style={{ fontSize: 13, color: 'var(--color-text-2)' }}>No readings logged yet.</p>
                                        </div>
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
                {logs.links && logs.links.length > 3 && (
                    <div style={{ padding: '14px 22px', borderTop: '1px solid var(--color-border)', display: 'flex', gap: 6 }}>
                        {logs.links.map((link, i) => (
                            <button key={i} onClick={() => link.url && router.get(link.url)} disabled={!link.url} dangerouslySetInnerHTML={{ __html: link.label }} style={{ padding: '5px 10px', borderRadius: 6, border: '1px solid var(--color-border)', fontSize: 12, cursor: link.url ? 'pointer' : 'default', background: link.active ? 'var(--color-navy)' : 'var(--color-card)', color: link.active ? '#fff' : 'var(--color-text-2)', fontWeight: link.active ? 700 : 400 }} />
                        ))}
                    </div>
                )}
            </div>

            {/* Load Chart.js from CDN */}
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" async />
        </AppLayout>
    );
}
