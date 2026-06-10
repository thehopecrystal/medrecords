import { useForm, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import AppLayout from '@/layouts/AppLayout';

const STATUS_COLORS = { pending: 'amber', accepted: 'green', completed: 'navy', declined: 'red' };

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

function StatusForm({ id, currentStatus }) {
    const [status, setStatus] = useState(currentStatus);
    const { patch, processing } = useForm({ status });

    function handleSubmit(e) {
        e.preventDefault();
        router.patch(`/provider/referrals/${id}/status`, { status });
    }

    return (
        <form onSubmit={handleSubmit} style={{ display: 'flex', gap: 4 }}>
            <select
                value={status}
                onChange={e => setStatus(e.target.value)}
                style={{ fontSize: 11, padding: '4px 6px', border: '1px solid var(--color-border)', borderRadius: 6, background: 'var(--color-card)', color: 'var(--color-text)' }}
            >
                {['pending', 'accepted', 'completed', 'declined'].map(s => (
                    <option key={s} value={s}>{s.charAt(0).toUpperCase() + s.slice(1)}</option>
                ))}
            </select>
            <button type="submit" disabled={processing} style={{ background: 'var(--color-card)', color: 'var(--color-text)', border: '1px solid var(--color-border)', padding: '4px 10px', borderRadius: 6, fontSize: 11, fontWeight: 600, cursor: 'pointer' }}>
                Update
            </button>
        </form>
    );
}

export default function Referrals({ referrals }) {
    const data = referrals?.data ?? referrals ?? [];

    return (
        <AppLayout title="Referrals">
            <div style={{ marginBottom: 24 }}>
                <h1 style={{ fontSize: 22, fontWeight: 800, color: 'var(--color-navy)', margin: 0 }}>Referral Inbox</h1>
                <p style={{ fontSize: 13, color: 'var(--color-text-3)', marginTop: 4 }}>Referrals you have sent and their current status.</p>
            </div>

            <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12, overflow: 'hidden' }}>
                <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                    <thead>
                        <tr style={{ borderBottom: '1px solid var(--color-border)' }}>
                            {['Patient', 'Referred To', 'Specialty', 'Reason', 'Date', 'Status', ''].map((h, i) => (
                                <th key={i} style={{ padding: '11px 18px', textAlign: 'left', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.05em', color: 'var(--color-text-3)' }}>{h}</th>
                            ))}
                        </tr>
                    </thead>
                    <tbody>
                        {data.length === 0 ? (
                            <tr>
                                <td colSpan={7}>
                                    <div style={{ padding: '40px 20px', textAlign: 'center', color: 'var(--color-text-3)' }}>
                                        <div style={{ fontSize: 28, marginBottom: 8 }}>📋</div>
                                        <div style={{ fontSize: 14, fontWeight: 600 }}>No referrals sent</div>
                                        <div style={{ fontSize: 12, marginTop: 4 }}>Referrals you create for patients will appear here.</div>
                                    </div>
                                </td>
                            </tr>
                        ) : (
                            data.map(ref => (
                                <tr key={ref.id} style={{ borderBottom: '1px solid var(--color-border)' }}>
                                    <td style={{ padding: '12px 18px' }}>
                                        <div style={{ fontWeight: 600, fontSize: 13 }}>{ref.patient?.user?.name ?? '—'}</div>
                                        <div style={{ fontSize: 11, color: 'var(--color-text-3)' }}>{ref.patient?.med_id ?? ''}</div>
                                    </td>
                                    <td style={{ padding: '12px 18px', fontWeight: 600, fontSize: 13 }}>{ref.referred_to}</td>
                                    <td style={{ padding: '12px 18px' }}>
                                        <span style={{ ...pillStyle('navy'), fontSize: 10 }}>{ref.specialty}</span>
                                    </td>
                                    <td style={{ padding: '12px 18px', fontSize: 12, color: 'var(--color-text-2)', maxWidth: 200 }}>{ref.reason}</td>
                                    <td style={{ padding: '12px 18px', fontSize: 12, color: 'var(--color-text-3)' }}>
                                        {ref.created_at ? new Date(ref.created_at).toLocaleDateString('en-NG', { day: '2-digit', month: 'short', year: 'numeric' }) : '—'}
                                    </td>
                                    <td style={{ padding: '12px 18px' }}>
                                        <span style={pillStyle(STATUS_COLORS[ref.status] ?? 'grey')}>
                                            {ref.status ? ref.status.charAt(0).toUpperCase() + ref.status.slice(1) : ''}
                                        </span>
                                    </td>
                                    <td style={{ padding: '12px 18px' }}>
                                        <StatusForm id={ref.id} currentStatus={ref.status} />
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
