import { router } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import { fmtRelative } from '@/utils/fmt';

const ICONS = {
    consent_expiry:          '⏰',
    appointment_request:     '📅',
    appointment_confirmed:   '✅',
    refill_request:          '💊',
    refill_approved:         '✅',
    refill_denied:           '❌',
    break_glass:             '⚡',
};

export default function Notifications({ notifications }) {
    function deleteNotif(id) {
        router.delete(`/patient/notifications/${id}`);
    }

    return (
        <AppLayout title="Notifications">
            <div style={{ marginBottom: 24 }}>
                <h1 style={{ fontSize: 22, fontWeight: 800, color: 'var(--color-navy)', margin: 0 }}>Notifications</h1>
                <p style={{ fontSize: 13, color: 'var(--color-text-2)', marginTop: 4 }}>All your in-app alerts and updates.</p>
            </div>

            <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12, overflow: 'hidden' }}>
                {notifications.data && notifications.data.length > 0 ? (
                    notifications.data.map((notif, i) => {
                        const icon = ICONS[notif.type] || '🔔';
                        const isUnread = !notif.read_at;
                        return (
                            <div key={notif.id} style={{
                                display: 'flex', alignItems: 'flex-start', gap: 14,
                                padding: '16px 22px',
                                borderBottom: i < notifications.data.length - 1 ? '1px solid var(--color-border)' : 'none',
                                background: isUnread ? 'rgba(59,130,246,0.04)' : 'transparent',
                            }}>
                                <div style={{ width: 38, height: 38, borderRadius: '50%', background: 'var(--color-navy-lt)', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 18, flexShrink: 0 }}>
                                    {icon}
                                </div>
                                <div style={{ flex: 1, minWidth: 0 }}>
                                    <div style={{ fontSize: 14, fontWeight: isUnread ? 700 : 600, color: 'var(--color-text)' }}>{notif.title}</div>
                                    <div style={{ fontSize: 13, color: 'var(--color-text-2)', marginTop: 3, lineHeight: 1.5 }}>{notif.body}</div>
                                    <div style={{ fontSize: 11, color: 'var(--color-text-3)', marginTop: 6 }}>
                                        {fmtRelative(notif.created_at)}
                                    </div>
                                </div>
                                <div style={{ display: 'flex', gap: 6, flexShrink: 0, alignItems: 'center' }}>
                                    {isUnread && (
                                        <span style={{ display: 'inline-flex', alignItems: 'center', fontSize: 10, fontWeight: 700, padding: '2px 8px', borderRadius: 20, background: 'var(--color-navy-lt)', color: 'var(--color-navy)' }}>New</span>
                                    )}
                                    <button onClick={() => deleteNotif(notif.id)} title="Delete" style={{ background: 'none', border: '1px solid var(--color-border)', borderRadius: 6, padding: '4px 8px', cursor: 'pointer', fontSize: 13, color: 'var(--color-text-3)' }}>✕</button>
                                </div>
                            </div>
                        );
                    })
                ) : (
                    <div style={{ textAlign: 'center', padding: '56px 24px' }}>
                        <div style={{ fontSize: 36, marginBottom: 12 }}>🔔</div>
                        <h3 style={{ fontSize: 16, fontWeight: 700, marginBottom: 6 }}>All caught up</h3>
                        <p style={{ fontSize: 13, color: 'var(--color-text-2)' }}>No notifications yet. Important alerts about your health records will appear here.</p>
                    </div>
                )}
                {notifications.links && notifications.links.length > 3 && (
                    <div style={{ padding: '14px 22px', borderTop: '1px solid var(--color-border)', display: 'flex', gap: 6 }}>
                        {notifications.links.map((link, i) => (
                            <button key={i} onClick={() => link.url && router.get(link.url)} disabled={!link.url} dangerouslySetInnerHTML={{ __html: link.label }} style={{ padding: '5px 10px', borderRadius: 6, border: '1px solid var(--color-border)', fontSize: 12, cursor: link.url ? 'pointer' : 'default', background: link.active ? 'var(--color-navy)' : 'var(--color-card)', color: link.active ? '#fff' : 'var(--color-text-2)', fontWeight: link.active ? 700 : 400 }} />
                        ))}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
