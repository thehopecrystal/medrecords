import { useForm, Link } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

const STATUS_COLORS = { pending: 'amber', approved: 'green', denied: 'red' };

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

function ApproveForm({ id }) {
    const { post, processing } = useForm({});
    return (
        <form onSubmit={e => { e.preventDefault(); post(`/provider/refills/${id}/approve`); }}>
            <button type="submit" disabled={processing} style={{ background: 'var(--color-green)', color: '#fff', padding: '5px 12px', borderRadius: 6, fontSize: 11, fontWeight: 600, cursor: 'pointer', border: 'none' }}>Approve</button>
        </form>
    );
}

function DenyForm({ id }) {
    const { post, processing, setData, data } = useForm({ notes: 'Request denied by provider.' });
    return (
        <form onSubmit={e => { e.preventDefault(); if (window.confirm('Deny this refill request?')) post(`/provider/refills/${id}/deny`); }}>
            <input type="hidden" name="notes" value={data.notes} />
            <button type="submit" disabled={processing} style={{ background: 'var(--color-red-lt)', color: 'var(--color-red)', border: '1px solid var(--color-red)', padding: '5px 12px', borderRadius: 6, fontSize: 11, fontWeight: 600, cursor: 'pointer' }}>Deny</button>
        </form>
    );
}

export default function Refills({ requests, pendingCount }) {
    const data = requests?.data ?? requests ?? [];
    const links = requests?.links ?? null;

    return (
        <AppLayout title="Refill Requests">
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 24 }}>
                <div>
                    <h1 style={{ fontSize: 22, fontWeight: 800, color: 'var(--color-navy)', margin: 0 }}>Refill Requests</h1>
                    <p style={{ fontSize: 13, color: 'var(--color-text-3)', marginTop: 4 }}>Review and action patient prescription refill requests.</p>
                </div>
                {pendingCount > 0 && <span style={pillStyle('amber')}>{pendingCount} pending</span>}
            </div>

            <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12, overflow: 'hidden' }}>
                <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                    <thead>
                        <tr style={{ borderBottom: '1px solid var(--color-border)' }}>
                            {['Patient', 'Medication', 'Requested', 'Reason', 'Status', 'Action'].map(h => (
                                <th key={h} style={{ padding: '11px 18px', textAlign: 'left', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.05em', color: 'var(--color-text-3)' }}>{h}</th>
                            ))}
                        </tr>
                    </thead>
                    <tbody>
                        {data.length === 0 ? (
                            <tr>
                                <td colSpan={6}>
                                    <div style={{ padding: '40px 20px', textAlign: 'center', color: 'var(--color-text-3)' }}>
                                        <div style={{ fontSize: 28, marginBottom: 8 }}>💊</div>
                                        <div style={{ fontSize: 14, fontWeight: 600 }}>No refill requests</div>
                                        <div style={{ fontSize: 12, marginTop: 4 }}>Patients will send requests from their prescriptions page.</div>
                                    </div>
                                </td>
                            </tr>
                        ) : (
                            data.map(req => (
                                <tr key={req.id} style={{ borderBottom: '1px solid var(--color-border)' }}>
                                    <td style={{ padding: '12px 18px' }}>
                                        <div style={{ fontWeight: 600, fontSize: 13 }}>{req.patient?.user?.name ?? '—'}</div>
                                        <div style={{ fontSize: 11, color: 'var(--color-text-3)' }}>{req.patient?.med_id ?? ''}</div>
                                    </td>
                                    <td style={{ padding: '12px 18px', fontWeight: 600, fontSize: 13 }}>{req.prescription?.drug_name ?? '—'}</td>
                                    <td style={{ padding: '12px 18px', fontSize: 12, color: 'var(--color-text-2)' }}>
                                        {req.created_at ? new Date(req.created_at).toLocaleDateString('en-NG', { day: '2-digit', month: 'short', year: 'numeric' }) : '—'}
                                    </td>
                                    <td style={{ padding: '12px 18px', fontSize: 12, color: 'var(--color-text-2)', maxWidth: 180 }}>{req.reason || '—'}</td>
                                    <td style={{ padding: '12px 18px' }}>
                                        <span style={pillStyle(STATUS_COLORS[req.status] ?? 'grey')}>{req.status ? req.status.charAt(0).toUpperCase() + req.status.slice(1) : ''}</span>
                                    </td>
                                    <td style={{ padding: '12px 18px' }}>
                                        {req.status === 'pending' ? (
                                            <div style={{ display: 'flex', gap: 6 }}>
                                                <ApproveForm id={req.id} />
                                                <DenyForm id={req.id} />
                                            </div>
                                        ) : (
                                            <span style={{ fontSize: 12, color: 'var(--color-text-3)' }}>
                                                {req.actioned_at ? new Date(req.actioned_at).toLocaleDateString('en-NG', { day: '2-digit', month: 'short', year: 'numeric' }) : '—'}
                                            </span>
                                        )}
                                    </td>
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            </div>
        </AppLayout>
    );
}
