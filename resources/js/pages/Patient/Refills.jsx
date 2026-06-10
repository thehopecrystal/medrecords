import { Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import { fmtDate } from '@/utils/fmt';

const STATUS_COLORS = {
    pending:  { background: 'var(--color-amber-lt)', color: 'var(--color-amber)' },
    approved: { background: 'var(--color-green-lt)', color: 'var(--color-green)' },
    denied:   { background: 'var(--color-red-lt)',   color: 'var(--color-red)' },
};

export default function Refills({ requests }) {
    return (
        <AppLayout title="Prescription Refills">
            <div style={{ marginBottom: 24 }}>
                <h1 style={{ fontSize: 22, fontWeight: 800, color: 'var(--color-navy)', margin: 0 }}>Prescription Refills</h1>
                <p style={{ fontSize: 13, color: 'var(--color-text-2)', marginTop: 4 }}>Request and track prescription refills from your providers.</p>
            </div>

            <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12, overflow: 'hidden' }}>
                <div style={{ overflowX: 'auto' }}>
                    <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                        <thead>
                            <tr style={{ background: 'var(--color-border)' }}>
                                {['Medication', 'Provider', 'Requested', 'Reason', 'Status', 'Provider Notes'].map(h => (
                                    <th key={h} style={{ padding: '10px 16px', fontSize: 12, fontWeight: 700, textAlign: 'left', color: 'var(--color-text-2)', textTransform: 'uppercase', letterSpacing: '0.04em' }}>{h}</th>
                                ))}
                            </tr>
                        </thead>
                        <tbody>
                            {requests.data && requests.data.length > 0 ? requests.data.map((req, i) => (
                                <tr key={req.id} style={{ borderTop: '1px solid var(--color-border)', background: i % 2 === 0 ? 'transparent' : 'rgba(0,0,0,0.01)' }}>
                                    <td style={{ padding: '12px 16px', fontWeight: 600, fontSize: 13 }}>{req.prescription?.drug_name || '—'}</td>
                                    <td style={{ padding: '12px 16px', fontSize: 12, color: 'var(--color-text-2)' }}>{req.provider?.name || '—'}</td>
                                    <td style={{ padding: '12px 16px', fontSize: 12, color: 'var(--color-text-3)' }}>{fmtDate(req.created_at)}</td>
                                    <td style={{ padding: '12px 16px', fontSize: 12, color: 'var(--color-text-2)', maxWidth: 180 }}>{req.reason || '—'}</td>
                                    <td style={{ padding: '12px 16px' }}>
                                        <span style={{ display: 'inline-flex', alignItems: 'center', fontSize: 11, fontWeight: 600, padding: '2px 8px', borderRadius: 20, ...(STATUS_COLORS[req.status] || { background: 'var(--color-border)', color: 'var(--color-text-2)' }) }}>
                                            {req.status.charAt(0).toUpperCase() + req.status.slice(1)}
                                        </span>
                                    </td>
                                    <td style={{ padding: '12px 16px', fontSize: 12, color: 'var(--color-text-2)' }}>{req.provider_notes || '—'}</td>
                                </tr>
                            )) : (
                                <tr>
                                    <td colSpan={6}>
                                        <div style={{ textAlign: 'center', padding: '40px 24px' }}>
                                            <div style={{ fontSize: 32, marginBottom: 10 }}>💊</div>
                                            <h3 style={{ fontSize: 15, fontWeight: 700, marginBottom: 6 }}>No refill requests</h3>
                                            <p style={{ fontSize: 13, color: 'var(--color-text-2)' }}>Request refills from your health records page.</p>
                                        </div>
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
                {requests.links && requests.links.length > 3 && (
                    <div style={{ padding: '14px 22px', borderTop: '1px solid var(--color-border)', display: 'flex', gap: 6 }}>
                        {requests.links.map((link, i) => (
                            <button key={i} onClick={() => link.url && router.get(link.url)} disabled={!link.url} dangerouslySetInnerHTML={{ __html: link.label }} style={{ padding: '5px 10px', borderRadius: 6, border: '1px solid var(--color-border)', fontSize: 12, cursor: link.url ? 'pointer' : 'default', background: link.active ? 'var(--color-navy)' : 'var(--color-card)', color: link.active ? '#fff' : 'var(--color-text-2)', fontWeight: link.active ? 700 : 400 }} />
                        ))}
                    </div>
                )}
            </div>

            <div style={{ marginTop: 16, display: 'flex', gap: 8, padding: '10px 14px', background: 'var(--color-navy-lt)', borderRadius: 8, fontSize: 13, color: 'var(--color-navy)', alignItems: 'center' }}>
                <span>💡</span>
                <span>
                    To request a refill, go to{' '}
                    <Link href="/patient/record?tab=prescriptions" style={{ color: 'var(--color-navy)', fontWeight: 700 }}>My Health Records → Prescriptions</Link>
                    {' '}and click "Request Refill" on an active prescription.
                </span>
            </div>
        </AppLayout>
    );
}
