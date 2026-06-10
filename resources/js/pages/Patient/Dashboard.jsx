import { usePage, Link } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import { fmtRelative } from '@/utils/fmt';

export default function Dashboard({ patient, recentActivity }) {
    const { auth } = usePage().props;
    const firstName = (auth.user.name || '').split(' ')[0];

    const hour = new Date().getHours();
    const greeting = hour < 12 ? 'Good morning' : hour < 17 ? 'Good afternoon' : 'Good evening';

    const quickActions = [
        { href: '/patient/access-code', icon: '📱', label: 'Share record' },
        { href: '/patient/consents',    icon: '🔐', label: 'Consents' },
        { href: '/patient/record',      icon: '📋', label: 'My record' },
        { href: '/patient/audit-log',   icon: '👁',  label: 'Who saw me' },
    ];

    return (
        <AppLayout title="Patient Dashboard">
            <div style={{ marginBottom: 24 }}>
                <p style={{ color: 'var(--color-text-2)', fontSize: 13, marginBottom: 2 }}>{greeting},</p>
                <h1 style={{ fontSize: 26, fontWeight: 800, color: 'var(--color-navy)', margin: 0 }}>{firstName}</h1>
            </div>

            {/* MedID card */}
            <div style={{ background: 'linear-gradient(135deg, #1E3A5F 0%, #2C5178 100%)', color: '#fff', borderRadius: 14, padding: '18px 22px', marginBottom: 24, maxWidth: 400 }}>
                <div style={{ fontSize: 10, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.08em', opacity: 0.65, marginBottom: 6 }}>MedRecords ID</div>
                <div style={{ fontSize: 26, fontWeight: 800, fontFamily: 'monospace', letterSpacing: '0.08em' }}>{patient.med_id}</div>
                <div style={{ marginTop: 8, fontSize: 12, opacity: 0.8, display: 'flex', alignItems: 'center', gap: 8 }}>
                    <span>{auth.user.name}{patient.age ? ` · ${patient.age}` : ''}</span>
                    {patient.nin_verified && (
                        <span style={{ background: 'rgba(44,95,45,0.7)', padding: '1px 8px', borderRadius: 20, fontSize: 11, fontWeight: 600 }}>✓ NIN verified</span>
                    )}
                </div>
            </div>

            {/* Quick actions */}
            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4,1fr)', gap: 12, marginBottom: 28, maxWidth: 600 }}>
                {quickActions.map(action => (
                    <Link key={action.href} href={action.href} style={{ background: 'var(--color-card)', borderRadius: 12, padding: '16px 8px', textAlign: 'center', boxShadow: '0 1px 4px rgba(15,23,42,0.08)', textDecoration: 'none', border: '1px solid var(--color-border)', display: 'block' }}>
                        <div style={{ fontSize: 22 }}>{action.icon}</div>
                        <div style={{ fontSize: 11, color: 'var(--color-text-2)', marginTop: 6, fontWeight: 500 }}>{action.label}</div>
                    </Link>
                ))}
            </div>

            {/* Recent activity */}
            <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12 }}>
                <div style={{ padding: '16px 20px', borderBottom: '1px solid var(--color-border)' }}>
                    <div style={{ fontWeight: 700, fontSize: 14, color: 'var(--color-text)' }}>Recent activity</div>
                    <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>What happened with your record recently</div>
                </div>
                {recentActivity.length === 0 ? (
                    <div style={{ padding: 24, textAlign: 'center', color: 'var(--color-text-2)', fontSize: 13 }}>No recent activity.</div>
                ) : (
                    recentActivity.map((log, i) => (
                        <div key={i} style={{ padding: '14px 20px', borderBottom: i < recentActivity.length - 1 ? '1px solid var(--color-border)' : 'none', display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', gap: 16 }}>
                            <div>
                                <div style={{ fontSize: 14, fontWeight: 600, color: 'var(--color-text)' }}>{log.description}</div>
                                <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>{log.actor?.name}</div>
                            </div>
                            <div style={{ fontSize: 12, color: 'var(--color-text-3)', whiteSpace: 'nowrap', flexShrink: 0 }}>
                                {log.created_at_human || fmtRelative(log.created_at)}
                            </div>
                        </div>
                    ))
                )}
            </div>
        </AppLayout>
    );
}
