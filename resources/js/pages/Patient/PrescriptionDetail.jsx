import { Link } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import { fmtDate, fmtNaira, statusColor } from '@/utils/fmt';

const PILL_COLORS = {
    navy:   { background: 'var(--color-navy-lt)', color: 'var(--color-navy)' },
    green:  { background: 'var(--color-green-lt)', color: 'var(--color-green)' },
    amber:  { background: 'var(--color-amber-lt)', color: 'var(--color-amber)' },
    red:    { background: 'var(--color-red-lt)', color: 'var(--color-red)' },
    grey:   { background: 'var(--color-border)', color: 'var(--color-text-2)' },
};

function Pill({ color = 'grey', children }) {
    return (
        <span style={{ display: 'inline-flex', alignItems: 'center', fontSize: 11, fontWeight: 600, padding: '2px 8px', borderRadius: 20, ...(PILL_COLORS[color] || PILL_COLORS.grey) }}>
            {children}
        </span>
    );
}

function DetailRow({ label, value }) {
    return (
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', padding: '10px 0', borderBottom: '1px solid var(--color-border)', fontSize: 13, gap: 16 }}>
            <span style={{ color: 'var(--color-text-2)', flexShrink: 0 }}>{label}</span>
            <span style={{ fontWeight: 500, textAlign: 'right' }}>{value || '—'}</span>
        </div>
    );
}

export default function PrescriptionDetail({ prescription }) {
    const pillColor = prescription.status_color || statusColor(prescription.status);

    return (
        <AppLayout title="Prescription Detail">
            <div style={{ marginBottom: 16 }}>
                <Link href="/patient/record?tab=prescriptions" style={{ display: 'inline-flex', alignItems: 'center', gap: 6, fontSize: 13, fontWeight: 600, color: 'var(--color-text-2)', background: 'var(--color-border)', borderRadius: 8, padding: '6px 14px', textDecoration: 'none', border: '1px solid var(--color-border)' }}>
                    ← Back to record
                </Link>
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: '1fr 320px', gap: 20 }}>
                <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12, padding: 20 }}>
                    <div style={{ borderBottom: '1px solid var(--color-border)', marginBottom: 16, paddingBottom: 14 }}>
                        <div style={{ display: 'flex', alignItems: 'center', gap: 8, flexWrap: 'wrap' }}>
                            <span style={{ fontSize: 18, fontWeight: 800, color: 'var(--color-navy)' }}>
                                {prescription.drug_name} {prescription.dose}
                            </span>
                            <Pill color={pillColor}>{prescription.status}</Pill>
                        </div>
                        <div style={{ fontSize: 12, color: 'var(--color-text-3)', marginTop: 4 }}>{prescription.rx_id}</div>
                    </div>

                    <DetailRow label="Prescribed by" value={prescription.prescribed_by?.name} />
                    <DetailRow label="Date" value={fmtDate(prescription.created_at)} />
                    <DetailRow label="Dose" value={prescription.dose} />
                    <DetailRow label="Frequency" value={prescription.frequency} />
                    <DetailRow label="Duration" value={prescription.duration_days ? `${prescription.duration_days} days` : null} />
                    <DetailRow label="Diagnosis" value={prescription.diagnosis_context} />

                    {prescription.instructions && (
                        <div style={{ marginTop: 14, padding: 12, background: 'var(--color-border)', borderRadius: 8, fontSize: 13, color: 'var(--color-text-2)', lineHeight: 1.6 }}>
                            {prescription.instructions}
                        </div>
                    )}
                </div>

                <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12, padding: 16 }}>
                    <div style={{ fontSize: 11, color: 'var(--color-text-2)', textTransform: 'uppercase', letterSpacing: '0.05em', marginBottom: 10 }}>Dispense history</div>
                    {prescription.dispenses?.length ? prescription.dispenses.map(d => (
                        <div key={d.id} style={{ padding: '10px 0', borderBottom: '1px solid var(--color-border)' }}>
                            <strong style={{ fontSize: 13 }}>{d.facility?.name}</strong><br />
                            <span style={{ fontSize: 11, color: 'var(--color-text-2)' }}>{fmtDate(d.created_at)} · {d.quantity} units</span>
                            {d.price && <div style={{ fontSize: 11, color: 'var(--color-text-2)' }}>NGN {fmtNaira(d.price)}</div>}
                        </div>
                    )) : <div style={{ fontSize: 12, color: 'var(--color-text-2)' }}>Not yet dispensed.</div>}
                </div>
            </div>
        </AppLayout>
    );
}
