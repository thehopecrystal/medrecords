import { Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

const STATUS_STYLE = {
    active:       { background: 'var(--color-red-lt)',   color: 'var(--color-red)' },
    approved:     { background: 'var(--color-green-lt)', color: 'var(--color-green)' },
    disputed:     { background: 'var(--color-amber-lt)', color: 'var(--color-amber)' },
    under_review: { background: 'var(--color-amber-lt)', color: 'var(--color-amber)' },
    closed:       { background: '#F1F5F9', color: '#64748B' },
};

function post(url) {
    router.post(url, {}, { preserveScroll: true });
}

export default function BreakGlass({ events }) {
    const { data, links, last_page } = events;

    return (
        <AppLayout title="Break-Glass Review">
            <div style={{ marginBottom: 24 }}>
                <h1 style={{ fontSize: 22, fontWeight: 800, color: 'var(--color-navy)', margin: 0 }}>Break-Glass Events</h1>
                <p style={{ fontSize: 13, color: 'var(--color-text-2)', marginTop: 4 }}>All emergency access events — review within 7 days</p>
            </div>

            {data.length === 0 && (
                <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 14, textAlign: 'center', padding: 40, color: 'var(--color-text-3)' }}>
                    No break-glass events found.
                </div>
            )}

            {data.map(e => (
                <div key={e.id} style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderLeft: e.status === 'active' ? '4px solid var(--color-red)' : '1px solid var(--color-border)', borderRadius: 14, padding: '20px 22px', marginBottom: 16 }}>
                    <div style={{ display: 'grid', gridTemplateColumns: '1fr auto', gap: 16, alignItems: 'flex-start' }}>
                        <div>
                            <div style={{ display: 'flex', alignItems: 'center', gap: 10, marginBottom: 6 }}>
                                <span style={{ fontSize: 15, fontWeight: 700, color: 'var(--color-navy)' }}>{e.patient_name}</span>
                                <span style={{ ...(STATUS_STYLE[e.status] ?? STATUS_STYLE.closed), fontSize: 10, fontWeight: 700, padding: '2px 8px', borderRadius: 10, textTransform: 'capitalize' }}>{e.status}</span>
                            </div>
                            <div style={{ fontSize: 12, color: 'var(--color-text-3)', marginBottom: 8 }}>
                                Provider: <strong>{e.provider_name}</strong> &middot; {e.created_at_formatted} &middot; Reason: {(e.reason_code ?? '').replace(/_/g, ' ')}
                            </div>
                            <div style={{ background: '#F8FAFC', padding: '10px 12px', borderRadius: 8, fontSize: 13, color: 'var(--color-text-2)' }}>
                                {e.justification}
                            </div>
                            {e.review_notes && (
                                <div style={{ marginTop: 8, fontSize: 12, color: 'var(--color-text-3)', fontStyle: 'italic' }}>
                                    Review note: {e.review_notes}
                                </div>
                            )}
                        </div>

                        {(e.status === 'active' || e.status === 'under_review') && (
                            <div style={{ display: 'flex', flexDirection: 'column', gap: 8, minWidth: 200 }}>
                                <button
                                    onClick={() => post(`/admin/break-glass/${e.id}/approve`)}
                                    style={{ width: '100%', padding: '8px 14px', borderRadius: 8, border: 'none', background: 'var(--color-green)', color: '#fff', fontSize: 12, fontWeight: 700, cursor: 'pointer' }}
                                >
                                    ✓ Approve
                                </button>
                                <button
                                    onClick={() => { if (window.confirm('Mark as disputed? Patient will be notified.')) post(`/admin/break-glass/${e.id}/dispute`); }}
                                    style={{ width: '100%', padding: '8px 14px', borderRadius: 8, border: 'none', background: 'var(--color-red)', color: '#fff', fontSize: 12, fontWeight: 700, cursor: 'pointer' }}
                                >
                                    ⚠ Dispute
                                </button>
                                <button
                                    onClick={() => post(`/admin/break-glass/${e.id}/close`)}
                                    style={{ width: '100%', padding: '8px 14px', borderRadius: 8, border: '1.5px solid var(--color-border)', background: 'var(--color-card)', color: 'var(--color-text-2)', fontSize: 12, fontWeight: 700, cursor: 'pointer' }}
                                >
                                    Close
                                </button>
                            </div>
                        )}

                        {e.status === 'disputed' && (
                            <div style={{ minWidth: 160 }}>
                                <button
                                    onClick={() => post(`/admin/break-glass/${e.id}/close`)}
                                    style={{ width: '100%', padding: '8px 14px', borderRadius: 8, border: '1.5px solid var(--color-border)', background: 'var(--color-card)', color: 'var(--color-text-2)', fontSize: 12, fontWeight: 700, cursor: 'pointer' }}
                                >
                                    Close investigation
                                </button>
                            </div>
                        )}
                    </div>
                </div>
            ))}

            {last_page > 1 && (
                <div style={{ display: 'flex', gap: 6, flexWrap: 'wrap', marginTop: 16 }}>
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
        </AppLayout>
    );
}
