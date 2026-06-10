import { Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

export default function Providers({ providers }) {
    const { data, links, last_page } = providers;

    function verify(id) {
        router.post(`/admin/mdcn/${id}/verify`, {}, { preserveScroll: true });
    }

    return (
        <AppLayout title="MDCN Verification">
            <div style={{ marginBottom: 24 }}>
                <h1 style={{ fontSize: 22, fontWeight: 800, color: 'var(--color-navy)', margin: 0 }}>Provider MDCN Verification</h1>
                <p style={{ fontSize: 13, color: 'var(--color-text-2)', marginTop: 4 }}>Verify clinician licenses against the MDCN portal</p>
            </div>

            <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 14, overflow: 'hidden' }}>
                <div style={{ overflowX: 'auto' }}>
                    <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: 13 }}>
                        <thead>
                            <tr style={{ background: '#F8FAFC' }}>
                                {['Provider','Facility','Specialty','MDCN Folio','Status','Action'].map(h => (
                                    <th key={h} style={{ padding: '11px 16px', textAlign: 'left', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.05em', color: 'var(--color-text-2)', borderBottom: '1px solid var(--color-border)', whiteSpace: 'nowrap' }}>{h}</th>
                                ))}
                            </tr>
                        </thead>
                        <tbody>
                            {data.length === 0 ? (
                                <tr><td colSpan={6} style={{ textAlign: 'center', color: 'var(--color-text-3)', padding: 24 }}>No providers registered.</td></tr>
                            ) : data.map(p => (
                                <tr key={p.id} style={{ borderBottom: '1px solid var(--color-border)' }}>
                                    <td style={{ padding: '12px 16px' }}>
                                        <strong>{p.user_name}</strong>
                                        <br />
                                        <span style={{ fontSize: 11, color: 'var(--color-text-3)' }}>{p.user_email}</span>
                                    </td>
                                    <td style={{ padding: '12px 16px', color: 'var(--color-text-2)' }}>{p.facility_name}</td>
                                    <td style={{ padding: '12px 16px', color: 'var(--color-text-2)' }}>{p.specialty}</td>
                                    <td style={{ padding: '12px 16px', fontFamily: 'monospace', fontSize: 12 }}>{p.mdcn_folio || '—'}</td>
                                    <td style={{ padding: '12px 16px' }}>
                                        {p.mdcn_verified ? (
                                            <>
                                                <span style={{ background: 'var(--color-green-lt)', color: 'var(--color-green)', fontSize: 11, fontWeight: 700, padding: '2px 8px', borderRadius: 10 }}>✓ Verified</span>
                                                {p.mdcn_verified_at && <div style={{ fontSize: 10, color: 'var(--color-text-3)', marginTop: 2 }}>{p.mdcn_verified_at}</div>}
                                            </>
                                        ) : p.mdcn_status === 'pending' ? (
                                            <span style={{ background: 'var(--color-amber-lt)', color: 'var(--color-amber)', fontSize: 11, fontWeight: 700, padding: '2px 8px', borderRadius: 10 }}>Pending</span>
                                        ) : (
                                            <span style={{ background: '#F1F5F9', color: '#64748B', fontSize: 11, fontWeight: 700, padding: '2px 8px', borderRadius: 10 }}>Unverified</span>
                                        )}
                                    </td>
                                    <td style={{ padding: '12px 16px' }}>
                                        <button
                                            onClick={() => verify(p.id)}
                                            style={{ padding: '6px 14px', borderRadius: 8, border: '1.5px solid var(--color-border)', background: 'var(--color-card)', color: 'var(--color-text-2)', fontSize: 12, fontWeight: 600, cursor: 'pointer' }}
                                        >
                                            {p.mdcn_verified ? 'Re-verify' : 'Verify now'}
                                        </button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>

                {last_page > 1 && (
                    <div style={{ padding: '14px 16px', borderTop: '1px solid var(--color-border)', display: 'flex', gap: 6, flexWrap: 'wrap' }}>
                        {links.map((link, i) => (
                            <Link
                                key={i}
                                href={link.url ?? '#'}
                                style={{
                                    padding: '5px 10px', borderRadius: 6, fontSize: 12, fontWeight: 600,
                                    textDecoration: 'none',
                                    background: link.active ? 'var(--color-navy)' : 'var(--color-card)',
                                    color: link.active ? '#fff' : 'var(--color-text-2)',
                                    border: '1px solid var(--color-border)',
                                    pointerEvents: link.url ? 'auto' : 'none',
                                    opacity: link.url ? 1 : 0.4,
                                }}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ))}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
