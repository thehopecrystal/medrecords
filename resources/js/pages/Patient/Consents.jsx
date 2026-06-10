import { router } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import { fmtDate, fmtRelative, fmtUntil } from '@/utils/fmt';

function ConsentEntry({ consent }) {
    const { id, grantee, scope_label, created_at, expires_at, revoked_at, status } = consent;

    function approve() {
        router.patch(`/patient/consents/${id}/approve`);
    }
    function decline() {
        router.delete(`/patient/consents/${id}/decline`);
    }
    function revoke() {
        if (confirm('Revoke this consent?')) {
            router.patch(`/patient/consents/${id}/revoke`);
        }
    }

    return (
        <div style={{ padding: '14px 20px', borderBottom: '1px solid var(--color-border)' }}>
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', gap: 16 }}>
                <div style={{ flex: 1 }}>
                    <div style={{ fontSize: 14, fontWeight: 600, color: 'var(--color-text)' }}>
                        {grantee?.name}
                        {scope_label && (
                            <span style={{ display: 'inline-flex', alignItems: 'center', fontSize: 11, fontWeight: 600, padding: '2px 8px', borderRadius: 20, background: 'var(--color-navy-lt)', color: 'var(--color-navy)', marginLeft: 6 }}>
                                {scope_label}
                            </span>
                        )}
                    </div>
                    <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 3 }}>
                        {grantee?.role ? grantee.role.charAt(0).toUpperCase() + grantee.role.slice(1) : ''}
                        {status === 'pending' && expires_at && ` · Requested ${fmtRelative(created_at)} · Valid for ${fmtUntil(expires_at)}`}
                        {status === 'active'  && ` · expires ${expires_at ? fmtDate(expires_at) : 'no expiry'}`}
                        {status === 'expired' && ` · ${scope_label} · expired ${fmtDate(expires_at)}`}
                        {status === 'revoked' && ` · ${scope_label} · revoked ${fmtDate(revoked_at)}`}
                    </div>
                    {status === 'pending' && (
                        <div style={{ marginTop: 10, background: 'var(--color-border)', padding: '10px 12px', borderRadius: 8, fontSize: 12, color: 'var(--color-text-2)' }}>
                            <strong>Scope:</strong> {scope_label}<br />
                            <strong>Expires:</strong> {fmtDate(expires_at)}<br />
                            <strong>Access type:</strong> Read + write
                        </div>
                    )}
                    {status === 'pending' && (
                        <p style={{ fontSize: 11, color: 'var(--color-text-3)', marginTop: 6 }}>You can revoke this at any time. All access is logged.</p>
                    )}
                </div>

                <div style={{ display: 'flex', gap: 8, flexShrink: 0, alignItems: 'center' }}>
                    {status === 'pending' && (
                        <>
                            <button onClick={decline} style={{ background: 'var(--color-card)', color: 'var(--color-text-2)', border: '1.5px solid var(--color-border)', borderRadius: 6, padding: '6px 14px', fontSize: 12, fontWeight: 600, cursor: 'pointer' }}>Decline</button>
                            <button onClick={approve} style={{ background: 'var(--color-green)', color: '#fff', border: 'none', borderRadius: 6, padding: '6px 14px', fontSize: 12, fontWeight: 600, cursor: 'pointer' }}>Approve</button>
                        </>
                    )}
                    {status === 'active' && (
                        <button onClick={revoke} style={{ background: 'var(--color-red-lt)', color: 'var(--color-red)', border: '1px solid var(--color-red)', borderRadius: 6, padding: '6px 14px', fontSize: 12, fontWeight: 600, cursor: 'pointer' }}>Revoke</button>
                    )}
                    {status === 'expired' && (
                        <span style={{ display: 'inline-flex', alignItems: 'center', fontSize: 11, fontWeight: 600, padding: '2px 8px', borderRadius: 20, background: 'var(--color-border)', color: 'var(--color-text-2)' }}>Expired</span>
                    )}
                    {status === 'revoked' && (
                        <span style={{ display: 'inline-flex', alignItems: 'center', fontSize: 11, fontWeight: 600, padding: '2px 8px', borderRadius: 20, background: 'var(--color-red-lt)', color: 'var(--color-red)' }}>Revoked</span>
                    )}
                </div>
            </div>
        </div>
    );
}

function Section({ title, pill, borderLeft = false, subtitle, children }) {
    return (
        <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12, marginBottom: 20, ...(borderLeft ? { borderLeft: '4px solid var(--color-navy)' } : {}) }}>
            <div style={{ padding: '14px 20px', borderBottom: '1px solid var(--color-border)' }}>
                <div style={{ display: 'flex', alignItems: 'center', gap: 8, fontWeight: 700, fontSize: 14 }}>{title} {pill}</div>
                {subtitle && <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>{subtitle}</div>}
            </div>
            {children}
        </div>
    );
}

export default function Consents({ pending, active, expired, revoked }) {
    return (
        <AppLayout title="Consent Management">
            <div style={{ marginBottom: 24 }}>
                <h1 style={{ fontSize: 22, fontWeight: 800, color: 'var(--color-navy)', margin: 0 }}>My Consents</h1>
                <p style={{ fontSize: 13, color: 'var(--color-text-2)', marginTop: 4 }}>Who can see what, for how long</p>
            </div>

            {/* Pending */}
            {pending.length > 0 && (
                <Section
                    title="Pending consent requests"
                    borderLeft
                    subtitle="These providers are asking to access your record — approve or decline each one."
                    pill={<span style={{ display: 'inline-flex', alignItems: 'center', fontSize: 11, fontWeight: 600, padding: '2px 8px', borderRadius: 20, background: 'var(--color-navy-lt)', color: 'var(--color-navy)' }}>{pending.length}</span>}
                >
                    {pending.map(c => <ConsentEntry key={c.id} consent={{ ...c, status: 'pending' }} />)}
                </Section>
            )}

            {/* Active */}
            <Section
                title="Active consents"
                pill={<span style={{ display: 'inline-flex', alignItems: 'center', fontSize: 11, fontWeight: 600, padding: '2px 8px', borderRadius: 20, background: 'var(--color-green-lt)', color: 'var(--color-green)' }}>{active.length}</span>}
            >
                {active.length > 0
                    ? active.map(c => <ConsentEntry key={c.id} consent={{ ...c, status: 'active' }} />)
                    : <div style={{ padding: 16, color: 'var(--color-text-2)', fontSize: 13 }}>No active consents.</div>
                }
            </Section>

            {/* Expired */}
            {expired.length > 0 && (
                <Section title="Expired consents">
                    {expired.map(c => <ConsentEntry key={c.id} consent={{ ...c, status: 'expired' }} />)}
                </Section>
            )}

            {/* Revoked */}
            {revoked.length > 0 && (
                <Section title="Revoked consents">
                    {revoked.map(c => <ConsentEntry key={c.id} consent={{ ...c, status: 'revoked' }} />)}
                </Section>
            )}
        </AppLayout>
    );
}
