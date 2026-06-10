import { Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

const STATUS_COLORS = {
    pending:   { background: 'var(--color-amber-lt)', color: 'var(--color-amber)' },
    confirmed: { background: 'var(--color-green-lt)', color: 'var(--color-green)' },
    cancelled: { background: 'var(--color-red-lt)',   color: 'var(--color-red)' },
    completed: { background: 'var(--color-navy-lt)',  color: 'var(--color-navy)' },
    no_show:   { background: 'var(--color-border)',   color: 'var(--color-text-2)' },
};

function StatusPill({ status }) {
    const style = STATUS_COLORS[status] || STATUS_COLORS.no_show;
    return (
        <span style={{ display: 'inline-flex', alignItems: 'center', fontSize: 11, fontWeight: 600, padding: '2px 8px', borderRadius: 20, ...style }}>
            {status.replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase())}
        </span>
    );
}

export default function Appointments({ upcoming, past }) {
    function cancelAppointment(id) {
        if (confirm('Cancel this appointment?')) {
            router.post(`/patient/appointments/${id}/cancel`);
        }
    }

    return (
        <AppLayout title="Appointments">
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 24 }}>
                <div>
                    <h1 style={{ fontSize: 22, fontWeight: 800, color: 'var(--color-navy)', margin: 0 }}>My Appointments</h1>
                    <p style={{ fontSize: 13, color: 'var(--color-text-2)', marginTop: 4 }}>View upcoming and past appointments.</p>
                </div>
                <Link href="/patient/appointments/book" style={{ background: 'var(--color-navy)', color: '#fff', borderRadius: 8, padding: '9px 18px', fontSize: 13, fontWeight: 600, textDecoration: 'none' }}>
                    + Book Appointment
                </Link>
            </div>

            {/* Upcoming */}
            <div style={{ marginBottom: 28 }}>
                <div style={{ fontSize: 13, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.05em', color: 'var(--color-text-2)', marginBottom: 14 }}>Upcoming</div>
                {upcoming.length === 0 ? (
                    <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12, padding: '40px', textAlign: 'center' }}>
                        <div style={{ fontSize: 32, marginBottom: 10 }}>📅</div>
                        <h3 style={{ fontSize: 15, fontWeight: 700, color: 'var(--color-navy)', marginBottom: 6 }}>No upcoming appointments</h3>
                        <p style={{ fontSize: 13, color: 'var(--color-text-2)' }}>
                            <Link href="/patient/appointments/book" style={{ color: 'var(--color-navy)' }}>Book your first appointment</Link>
                        </p>
                    </div>
                ) : (
                    upcoming.map(apt => (
                        <div key={apt.id} style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12, marginBottom: 12, padding: '16px 20px', display: 'flex', alignItems: 'center', gap: 16 }}>
                            <div style={{ width: 56, textAlign: 'center', flexShrink: 0 }}>
                                <div style={{ fontSize: 22, fontWeight: 800, color: 'var(--color-navy)', lineHeight: 1 }}>{new Date(apt.appointment_date).getDate()}</div>
                                <div style={{ fontSize: 11, fontWeight: 700, textTransform: 'uppercase', color: 'var(--color-text-2)' }}>{new Date(apt.appointment_date).toLocaleString('en', { month: 'short' })}</div>
                            </div>
                            <div style={{ width: 1, background: 'var(--color-border)', alignSelf: 'stretch', flexShrink: 0 }} />
                            <div style={{ flex: 1, minWidth: 0 }}>
                                <div style={{ fontSize: 14, fontWeight: 700, color: 'var(--color-navy)' }}>
                                    {apt.type.charAt(0).toUpperCase() + apt.type.slice(1)} — Dr. {apt.provider?.name}
                                </div>
                                <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 3 }}>
                                    🕐 {apt.appointment_time ? new Date('1970-01-01T' + apt.appointment_time).toLocaleTimeString('en', { hour: 'numeric', minute: '2-digit' }) : ''}
                                    {apt.facility && <> · 🏥 {apt.facility.name}</>}
                                </div>
                                <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>{apt.reason}</div>
                            </div>
                            <div style={{ display: 'flex', alignItems: 'center', gap: 10, flexShrink: 0 }}>
                                <StatusPill status={apt.status} />
                                {(apt.status === 'pending' || apt.status === 'confirmed') && (
                                    <button onClick={() => cancelAppointment(apt.id)} style={{ background: 'var(--color-red-lt)', color: 'var(--color-red)', border: '1px solid var(--color-red)', borderRadius: 6, padding: '5px 12px', fontSize: 12, fontWeight: 600, cursor: 'pointer' }}>
                                        Cancel
                                    </button>
                                )}
                            </div>
                        </div>
                    ))
                )}
            </div>

            {/* Past */}
            {past.data && past.data.length > 0 && (
                <div>
                    <div style={{ fontSize: 13, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.05em', color: 'var(--color-text-2)', marginBottom: 14 }}>Past Appointments</div>
                    <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12, overflow: 'hidden' }}>
                        <div style={{ overflowX: 'auto' }}>
                            <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                                <thead>
                                    <tr style={{ background: 'var(--color-border)' }}>
                                        {['Date', 'Type', 'Provider', 'Reason', 'Status'].map(h => (
                                            <th key={h} style={{ padding: '10px 16px', fontSize: 12, fontWeight: 700, textAlign: 'left', color: 'var(--color-text-2)', textTransform: 'uppercase', letterSpacing: '0.04em' }}>{h}</th>
                                        ))}
                                    </tr>
                                </thead>
                                <tbody>
                                    {past.data.map((apt, i) => (
                                        <tr key={apt.id} style={{ borderTop: '1px solid var(--color-border)', background: i % 2 === 0 ? 'transparent' : 'rgba(0,0,0,0.01)' }}>
                                            <td style={{ padding: '12px 16px', fontSize: 13 }}>{new Date(apt.appointment_date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })}</td>
                                            <td style={{ padding: '12px 16px' }}>
                                                <span style={{ background: 'var(--color-navy-lt)', color: 'var(--color-navy)', fontSize: 10, fontWeight: 600, padding: '2px 8px', borderRadius: 20 }}>
                                                    {apt.type.charAt(0).toUpperCase() + apt.type.slice(1)}
                                                </span>
                                            </td>
                                            <td style={{ padding: '12px 16px', fontSize: 13 }}>{apt.provider?.name}</td>
                                            <td style={{ padding: '12px 16px', fontSize: 12, color: 'var(--color-text-2)', maxWidth: 200 }}>{apt.reason}</td>
                                            <td style={{ padding: '12px 16px' }}><StatusPill status={apt.status} /></td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                        {past.links && past.links.length > 3 && (
                            <div style={{ padding: '14px 22px', borderTop: '1px solid var(--color-border)', display: 'flex', gap: 6 }}>
                                {past.links.map((link, i) => (
                                    <button key={i} onClick={() => link.url && router.get(link.url)} disabled={!link.url} dangerouslySetInnerHTML={{ __html: link.label }} style={{ padding: '5px 10px', borderRadius: 6, border: '1px solid var(--color-border)', fontSize: 12, cursor: link.url ? 'pointer' : 'default', background: link.active ? 'var(--color-navy)' : 'var(--color-card)', color: link.active ? '#fff' : 'var(--color-text-2)', fontWeight: link.active ? 700 : 400 }} />
                                ))}
                            </div>
                        )}
                    </div>
                </div>
            )}
        </AppLayout>
    );
}
