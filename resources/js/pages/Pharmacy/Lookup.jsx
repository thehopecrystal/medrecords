import { useState } from 'react';
import { router, useForm, Link } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

const labelStyle = { display: 'block', fontSize: 12, fontWeight: 600, color: 'var(--color-text-2)', marginBottom: 5, textTransform: 'uppercase', letterSpacing: '0.04em' };
const inputStyle = { width: '100%', padding: '9px 12px', border: '1px solid var(--color-border)', borderRadius: 8, fontSize: 13, background: 'var(--color-card)', color: 'var(--color-text)', boxSizing: 'border-box', outline: 'none' };

function DetailRow({ label, value, valueStyle }) {
    return (
        <div style={{ display: 'flex', justifyContent: 'space-between', padding: '7px 0', borderBottom: '1px solid var(--color-border)', fontSize: 13 }}>
            <span style={{ color: 'var(--color-text-3)', fontSize: 12 }}>{label}</span>
            <span style={{ fontWeight: 500, ...valueStyle }}>{value}</span>
        </div>
    );
}

function Alert({ type, children }) {
    const colors = {
        green: { bg: 'var(--color-green-lt)', border: 'var(--color-green)', color: 'var(--color-green)' },
        amber: { bg: 'var(--color-amber-lt)', border: 'var(--color-amber)', color: 'var(--color-amber)' },
        red:   { bg: 'var(--color-red-lt)',   border: 'var(--color-red)',   color: 'var(--color-red)'   },
        navy:  { bg: 'var(--color-navy-lt)',  border: 'var(--color-navy)',  color: 'var(--color-navy)'  },
    };
    const c = colors[type] ?? colors.navy;
    return (
        <div style={{ display: 'flex', alignItems: 'flex-start', gap: 8, padding: '10px 14px', borderRadius: 8, background: c.bg, borderLeft: `3px solid ${c.border}`, color: c.color, fontSize: 12, marginBottom: 8 }}>
            {children}
        </div>
    );
}

export default function Lookup({ prescription, rxId, nhia }) {
    const [query, setQuery] = useState(rxId ?? '');

    function handleSearch(e) {
        e.preventDefault();
        router.get('/pharmacy/lookup', { rx_id: query }, { preserveState: false });
    }

    const { data, setData, post, processing } = useForm({
        quantity: '',
        batch_lot: '',
        price: '',
        payment_method: nhia?.status === 'enrolled' ? `${nhia.plan} (${nhia.coverage}% covered)` : '',
        counseling_notes: prescription?.instructions ?? '',
    });

    function handleDispense(e) {
        e.preventDefault();
        post(`/pharmacy/dispense/${prescription.id}`);
    }

    const cardStyle = { background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12, padding: '16px 20px' };

    return (
        <AppLayout title="Verify & Dispense">
            <div style={{ marginBottom: 20 }}>
                <h1 style={{ fontSize: 22, fontWeight: 800, color: 'var(--color-navy)', margin: 0 }}>Verify Prescription</h1>
                <p style={{ fontSize: 13, color: 'var(--color-text-3)', marginTop: 4 }}>Scan or enter a prescription ID to begin</p>
            </div>

            {/* Search form */}
            <div style={{ ...cardStyle, marginBottom: 20 }}>
                <form onSubmit={handleSearch} style={{ display: 'flex', gap: 12 }}>
                    <input
                        type="text"
                        value={query}
                        onChange={e => setQuery(e.target.value)}
                        placeholder="Prescription ID e.g. RX-ABCD-1234"
                        autoFocus
                        style={{ flex: 1, background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 8, padding: '10px 14px', fontSize: 14, color: 'var(--color-text)', outline: 'none' }}
                    />
                    <button type="submit" style={{ background: 'var(--color-navy)', color: '#fff', padding: '10px 20px', borderRadius: 8, fontWeight: 600, fontSize: 13, border: 'none', cursor: 'pointer' }}>
                        Look up
                    </button>
                </form>
            </div>

            {/* Not found */}
            {rxId && !prescription && (
                <Alert type="red"><span>✗</span><span>Prescription <strong>{rxId}</strong> not found.</span></Alert>
            )}

            {/* Prescription found */}
            {prescription && (
                <>
                    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: 20, marginBottom: 20 }}>
                        {/* Prescription card */}
                        <div style={cardStyle}>
                            <div style={{ fontSize: 14, fontWeight: 700, color: 'var(--color-navy)', marginBottom: 12 }}>Prescription</div>
                            <DetailRow label="ID" value={prescription.rx_id} valueStyle={{ fontFamily: 'monospace', fontSize: 12 }} />
                            <DetailRow label="Patient" value={prescription.patient?.user?.name} />
                            <DetailRow label="MedID" value={prescription.patient?.med_id} />
                            <DetailRow label="Prescriber" value={prescription.prescribed_by?.name ?? prescription.prescribedBy?.name} />
                            <DetailRow label="Date issued" value={prescription.created_at ? new Date(prescription.created_at).toLocaleDateString('en-NG', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '—'} />
                            <DetailRow label="Drug" value={`${prescription.drug_name} ${prescription.dose}`} valueStyle={{ fontWeight: 700 }} />
                            <DetailRow label="Frequency" value={prescription.frequency} />
                            <DetailRow label="Duration" value={prescription.duration_days ? prescription.duration_days + ' days' : '—'} />
                            <DetailRow label="Diagnosis" value={prescription.diagnosis_context || '—'} />
                            <div style={{ marginTop: 14 }}>
                                {prescription.status === 'active' && (
                                    <Alert type="green"><span>✓</span><span><strong>Prescription verified.</strong> Signed by {prescription.prescribed_by?.name ?? prescription.prescribedBy?.name}, not yet dispensed.</span></Alert>
                                )}
                                {prescription.status === 'dispensed' && (
                                    <Alert type="amber"><span>!</span><span><strong>Already dispensed.</strong> This prescription has been dispensed {prescription.dispenses?.length ?? 0} time(s).</span></Alert>
                                )}
                                {prescription.status !== 'active' && prescription.status !== 'dispensed' && (
                                    <Alert type="red"><span>✗</span><span>Prescription status: <strong>{prescription.status}</strong>. Cannot dispense.</span></Alert>
                                )}
                            </div>
                        </div>

                        {/* NHIA eligibility */}
                        <div style={cardStyle}>
                            <div style={{ fontSize: 14, fontWeight: 700, color: 'var(--color-navy)', marginBottom: 12 }}>NHIA Eligibility</div>
                            {nhia ? (
                                nhia.status === 'enrolled' ? (
                                    <>
                                        <Alert type="green"><span>✓</span><span><strong>{nhia.message}</strong></span></Alert>
                                        <DetailRow label="Plan" value={nhia.plan} />
                                        <DetailRow label="Insurer" value={nhia.insurer} />
                                        <DetailRow label="Member ID" value={nhia.member_id} valueStyle={{ fontFamily: 'monospace', fontSize: 12 }} />
                                        <DetailRow label="Coverage" value={`${nhia.coverage}%`} valueStyle={{ color: 'var(--color-green)', fontWeight: 700 }} />
                                        <DetailRow label="Patient copay" value={`${nhia.copay}%`} />
                                        <DetailRow label="Annual balance" value={`NGN ${Number(nhia.balance).toLocaleString()}`} />
                                    </>
                                ) : (
                                    <Alert type="amber"><span>!</span><span>{nhia.message} Patient will pay out of pocket.</span></Alert>
                                )
                            ) : (
                                <div style={{ fontSize: 12, color: 'var(--color-text-3)' }}>Look up a prescription to check eligibility.</div>
                            )}
                        </div>

                        {/* Safety check */}
                        <div style={cardStyle}>
                            <div style={{ fontSize: 14, fontWeight: 700, color: 'var(--color-navy)', marginBottom: 12 }}>Safety check</div>
                            {(() => {
                                const activeAllergies = prescription.patient?.active_allergies ?? prescription.patient?.activeAllergies ?? [];
                                const activeRx        = prescription.patient?.active_prescriptions ?? prescription.patient?.activePrescriptions ?? [];
                                const labResults      = prescription.patient?.lab_results ?? prescription.patient?.labResults ?? [];
                                const latestLab       = [...labResults].sort((a, b) => new Date(b.collected_at) - new Date(a.collected_at))[0];
                                return (
                                    <>
                                        <DetailRow
                                            label="Allergies on file"
                                            value={activeAllergies.length ? activeAllergies.map(a => a.allergen).join(', ') : 'None'}
                                            valueStyle={activeAllergies.length ? { color: 'var(--color-red)' } : {}}
                                        />
                                        <DetailRow
                                            label="Active medications"
                                            value={activeRx.length ? activeRx.map(r => r.drug_name).join(', ') : 'None'}
                                        />
                                        {latestLab && (
                                            <DetailRow label="Latest lab" value={`${latestLab.test_name} — ${latestLab.result}`} />
                                        )}
                                        <div style={{ marginTop: 14 }}>
                                            <Alert type="green"><span>✓</span><span>No duplicate dispense found.</span></Alert>
                                            {activeAllergies.length > 0 && (
                                                <Alert type="red"><span>!</span><span>Patient has recorded allergies — verify drug safety before dispensing.</span></Alert>
                                            )}
                                        </div>
                                    </>
                                );
                            })()}
                        </div>
                    </div>

                    {/* Dispense form */}
                    {prescription.status === 'active' && (
                        <div style={cardStyle}>
                            <div style={{ fontSize: 14, fontWeight: 700, color: 'var(--color-navy)', marginBottom: 16 }}>Record dispense</div>
                            <form onSubmit={handleDispense}>
                                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '0 20px' }}>
                                    <div style={{ marginBottom: 16 }}>
                                        <label style={labelStyle}>Quantity dispensed</label>
                                        <input type="number" style={inputStyle} value={data.quantity} onChange={e => setData('quantity', e.target.value)} placeholder="e.g. 60" min="1" required />
                                    </div>
                                    <div style={{ marginBottom: 16 }}>
                                        <label style={labelStyle}>Batch / lot number</label>
                                        <input style={inputStyle} value={data.batch_lot} onChange={e => setData('batch_lot', e.target.value)} placeholder="e.g. MET-2604-A" />
                                    </div>
                                    <div style={{ marginBottom: 16 }}>
                                        <label style={labelStyle}>Price (NGN)</label>
                                        <input type="number" style={inputStyle} value={data.price} onChange={e => setData('price', e.target.value)} placeholder="e.g. 4200" step="0.01" />
                                    </div>
                                    <div style={{ marginBottom: 16 }}>
                                        <label style={labelStyle}>Payment method</label>
                                        <input style={inputStyle} value={data.payment_method} onChange={e => setData('payment_method', e.target.value)} placeholder="e.g. NHIA / cash / transfer" />
                                    </div>
                                    <div style={{ marginBottom: 16, gridColumn: '1 / -1' }}>
                                        <label style={labelStyle}>Counseling notes</label>
                                        <textarea rows={3} style={{ ...inputStyle, resize: 'vertical' }} value={data.counseling_notes} onChange={e => setData('counseling_notes', e.target.value)} placeholder="Counseling provided to patient..." />
                                    </div>
                                </div>
                                <div style={{ paddingTop: 16, borderTop: '1px solid var(--color-border)' }}>
                                    <button type="submit" disabled={processing} style={{ background: 'var(--color-green)', color: '#fff', padding: '10px 22px', borderRadius: 8, fontWeight: 600, fontSize: 13, cursor: 'pointer', border: 'none' }}>
                                        Confirm dispense
                                    </button>
                                </div>
                            </form>
                        </div>
                    )}
                </>
            )}
        </AppLayout>
    );
}
