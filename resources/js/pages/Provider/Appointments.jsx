import { router, Link, useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

const STATUS_COLORS = { pending: 'amber', confirmed: 'green', cancelled: 'red', completed: 'navy', no_show: 'grey' };

const pillStyle = (color) => ({
    display: 'inline-block', padding: '2px 9px', borderRadius: 20, fontSize: 11, fontWeight: 600,
    background: color === 'green'  ? 'var(--color-green-lt)'  :
                color === 'amber'  ? 'var(--color-amber-lt)'  :
                color === 'red'    ? 'var(--color-red-lt)'    :
                color === 'navy'   ? 'var(--color-navy-lt)'   : '#f3f4f6',
    color:      color === 'green'  ? 'var(--color-green)'     :
                color === 'amber'  ? 'var(--color-amber)'     :
                color === 'red'    ? 'var(--color-red)'       :
                color === 'navy'   ? 'var(--color-navy)'      : '#6b7280',
});

function fmtTime(timeStr) {
    if (!timeStr) return '';
    const [h, m] = timeStr.split(':');
    const d = new Date();
    d.setHours(parseInt(h, 10), parseInt(m, 10));
    return d.toLocaleTimeString('en-NG', { hour: 'numeric', minute: '2-digit', hour12: true });
}

function ActionForm({ action, aptId, label, btnStyle }) {
    const { post, processing } = useForm({});
    return (
        <form onSubmit={e => { e.preventDefault(); post(`/provider/appointments/${aptId}/${action}`); }}>
            <button type="submit" disabled={processing} style={{ ...btnStyle, cursor: 'pointer', border: 'none' }}>
                {label}
            </button>
        </form>
    );
}

export default function Appointments({ appointments, upcoming, date, pendingCount }) {
    const cardStyle = { background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12 };

    const parsedDate = new Date(date + 'T00:00:00');
    const dateLabel = parsedDate.toLocaleDateString('en-NG', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' });

    return (
        <AppLayout title="Appointments">
            {/* Header */}
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 24 }}>
                <div>
                    <h1 style={{ fontSize: 22, fontWeight: 800, color: 'var(--color-navy)', margin: 0 }}>My Schedule</h1>
                    <p style={{ fontSize: 13, color: 'var(--color-text-3)', marginTop: 4 }}>Manage appointment requests and your daily schedule.</p>
                </div>
                {pendingCount > 0 && (
                    <span style={pillStyle('amber')}>{pendingCount} pending request{pendingCount > 1 ? 's' : ''}</span>
                )}
            </div>

            {/* Date picker */}
            <div style={{ ...cardStyle, padding: '16px 20px', marginBottom: 20 }}>
                <div style={{ display: 'flex', alignItems: 'flex-end', gap: 12 }}>
                    <div>
                        <label style={{ display: 'block', fontSize: 11, fontWeight: 600, color: 'var(--color-text-3)', textTransform: 'uppercase', letterSpacing: '0.05em', marginBottom: 4 }}>View Schedule For</label>
                        <input
                            type="date"
                            value={date}
                            onChange={e => router.get('/provider/appointments', { date: e.target.value }, { preserveState: false })}
                            style={{ padding: '8px 12px', border: '1px solid var(--color-border)', borderRadius: 8, fontSize: 13, background: 'var(--color-card)', color: 'var(--color-text)' }}
                        />
                    </div>
                    <Link href="/provider/appointments" style={{ background: 'var(--color-card)', color: 'var(--color-text)', border: '1px solid var(--color-border)', padding: '8px 16px', borderRadius: 7, fontSize: 12, fontWeight: 600, textDecoration: 'none' }}>
                        Today
                    </Link>
                </div>
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: '2fr 1fr', gap: 20 }}>
                {/* Day schedule */}
                <div style={cardStyle}>
                    <div style={{ padding: '14px 20px', borderBottom: '1px solid var(--color-border)' }}>
                        <div style={{ fontSize: 15, fontWeight: 700, color: 'var(--color-navy)' }}>{dateLabel}</div>
                        <div style={{ fontSize: 12, color: 'var(--color-text-3)', marginTop: 2 }}>{appointments.length} appointment(s)</div>
                    </div>
                    {appointments.length === 0 ? (
                        <div style={{ padding: '40px 20px', textAlign: 'center', color: 'var(--color-text-3)' }}>
                            <div style={{ fontSize: 28, marginBottom: 8 }}>📅</div>
                            <div style={{ fontSize: 14, fontWeight: 600 }}>No appointments</div>
                            <div style={{ fontSize: 12, marginTop: 4 }}>No appointments scheduled for this day.</div>
                        </div>
                    ) : (
                        appointments.map(apt => (
                            <div key={apt.id} style={{ display: 'flex', alignItems: 'flex-start', gap: 12, padding: '14px 20px', borderBottom: '1px solid var(--color-border)' }}>
                                <div style={{ width: 52, textAlign: 'center', flexShrink: 0 }}>
                                    <div style={{ fontSize: 14, fontWeight: 800, color: 'var(--color-navy)' }}>{fmtTime(apt.appointment_time).split(' ')[0]}</div>
                                    <div style={{ fontSize: 10, color: 'var(--color-text-3)' }}>{fmtTime(apt.appointment_time).split(' ')[1]}</div>
                                </div>
                                <div style={{ flex: 1, minWidth: 0 }}>
                                    <div style={{ fontSize: 14, fontWeight: 600, color: 'var(--color-navy)' }}>{apt.patient?.user?.name}</div>
                                    <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>
                                        <span style={pillStyle('navy')}>{apt.type ? apt.type.charAt(0).toUpperCase() + apt.type.slice(1) : ''}</span>
                                        {' '}{apt.reason}
                                    </div>
                                    {apt.notes && <div style={{ fontSize: 11, color: 'var(--color-text-3)', marginTop: 3 }}>{apt.notes}</div>}
                                </div>
                                <div style={{ display: 'flex', gap: 6, flexShrink: 0, flexWrap: 'wrap', justifyContent: 'flex-end' }}>
                                    <span style={pillStyle(STATUS_COLORS[apt.status] ?? 'grey')}>{apt.status ? apt.status.replace('_', ' ').charAt(0).toUpperCase() + apt.status.replace('_', ' ').slice(1) : ''}</span>
                                    {apt.status === 'pending' && (
                                        <ActionForm action="confirm" aptId={apt.id} label="Confirm"
                                            btnStyle={{ background: 'var(--color-green)', color: '#fff', padding: '5px 12px', borderRadius: 6, fontSize: 11, fontWeight: 600 }} />
                                    )}
                                    {apt.status === 'confirmed' && (<>
                                        <ActionForm action="complete" aptId={apt.id} label="Complete"
                                            btnStyle={{ background: 'var(--color-card)', color: 'var(--color-text)', border: '1px solid var(--color-border)', padding: '5px 12px', borderRadius: 6, fontSize: 11, fontWeight: 600 }} />
                                        <ActionForm action="no-show" aptId={apt.id} label="No-show"
                                            btnStyle={{ background: 'var(--color-red-lt)', color: 'var(--color-red)', border: '1px solid var(--color-red)', padding: '5px 12px', borderRadius: 6, fontSize: 11, fontWeight: 600 }} />
                                    </>)}
                                    <Link href={`/provider/patients/${apt.patient?.id}`} style={{ background: 'var(--color-card)', color: 'var(--color-text)', border: '1px solid var(--color-border)', padding: '5px 12px', borderRadius: 6, fontSize: 11, fontWeight: 600, textDecoration: 'none' }}>
                                        View Patient
                                    </Link>
                                </div>
                            </div>
                        ))
                    )}
                </div>

                {/* Upcoming sidebar */}
                <div style={cardStyle}>
                    <div style={{ padding: '14px 20px', borderBottom: '1px solid var(--color-border)' }}>
                        <div style={{ fontSize: 15, fontWeight: 700, color: 'var(--color-navy)' }}>Upcoming (all pending)</div>
                    </div>
                    {upcoming.length === 0 ? (
                        <div style={{ padding: '32px 20px', textAlign: 'center', color: 'var(--color-text-3)' }}>
                            <div style={{ fontSize: 28, marginBottom: 6 }}>✅</div>
                            <div style={{ fontSize: 12 }}>No pending requests.</div>
                        </div>
                    ) : (
                        upcoming.map(apt => (
                            <div key={apt.id} style={{ padding: '12px 18px', borderBottom: '1px solid var(--color-border)', fontSize: 12 }}>
                                <div style={{ fontWeight: 600, color: 'var(--color-text)' }}>{apt.patient?.user?.name}</div>
                                <div style={{ color: 'var(--color-text-2)', marginTop: 2 }}>
                                    {apt.appointment_date ? new Date(apt.appointment_date + 'T00:00:00').toLocaleDateString('en-NG', { day: '2-digit', month: 'short' }) : ''} at {fmtTime(apt.appointment_time)}
                                </div>
                                <div style={{ marginTop: 4, display: 'flex', gap: 4 }}>
                                    <span style={{ ...pillStyle('amber'), fontSize: 10 }}>Pending</span>
                                    <span style={{ ...pillStyle('navy'), fontSize: 10 }}>{apt.type ? apt.type.charAt(0).toUpperCase() + apt.type.slice(1) : ''}</span>
                                </div>
                                <ActionForm action="confirm" aptId={apt.id} label="Confirm"
                                    btnStyle={{ marginTop: 6, width: '100%', background: 'var(--color-green)', color: '#fff', padding: '6px 12px', borderRadius: 6, fontSize: 11, fontWeight: 600 }} />
                            </div>
                        ))
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
