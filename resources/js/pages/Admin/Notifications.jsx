import { useState } from 'react';
import { useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

const ROLES = [
    { value: 'patient',        label: 'Patient' },
    { value: 'provider',       label: 'Provider / Doctor' },
    { value: 'pharmacist',     label: 'Pharmacist' },
    { value: 'facility_admin', label: 'Facility Admin' },
    { value: 'super_admin',    label: 'Super Admin' },
];

const AUDIENCE_OPTS = [
    { value: 'all',  icon: '👥', label: 'All Users' },
    { value: 'role', icon: '🏷', label: 'By Role' },
    { value: 'user', icon: '👤', label: 'Specific User' },
];

export default function Notifications({ rows, users }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        audience: 'all',
        role: '',
        user_identifier: '',
        title: '',
        body: '',
    });

    function submit(e) {
        e.preventDefault();
        if (data.audience === 'all') {
            if (!window.confirm('Send this notification to ALL active users? This will also email each one.')) return;
        }
        post('/admin/notifications/send', { onSuccess: () => reset() });
    }

    return (
        <AppLayout title="Send Notification">
            <div style={{ marginBottom: 24 }}>
                <h1 style={{ fontSize: 22, fontWeight: 800, color: 'var(--color-navy)', margin: 0 }}>Send Notification</h1>
                <p style={{ fontSize: 13, color: 'var(--color-text-2)', marginTop: 4 }}>Compose and broadcast in-app and email notifications to your users</p>
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 20, alignItems: 'start' }}>
                {/* Compose */}
                <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 14, overflow: 'hidden' }}>
                    <div style={{ padding: '16px 20px 12px', borderBottom: '1px solid var(--color-border)' }}>
                        <div style={{ fontWeight: 700, fontSize: 14, color: 'var(--color-navy)' }}>Compose Notification</div>
                        <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>Recipients receive both an in-app notification and an email</div>
                    </div>
                    <div style={{ padding: '20px' }}>
                        <form onSubmit={submit}>
                            {/* Audience */}
                            <div style={{ marginBottom: 18 }}>
                                <label style={{ display: 'block', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.06em', color: 'var(--color-text-2)', marginBottom: 8 }}>Audience</label>
                                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: 8 }}>
                                    {AUDIENCE_OPTS.map(opt => (
                                        <div
                                            key={opt.value}
                                            onClick={() => setData('audience', opt.value)}
                                            style={{
                                                display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center',
                                                padding: '14px 8px', borderRadius: 10, cursor: 'pointer', textAlign: 'center', gap: 2,
                                                border: data.audience === opt.value ? '2px solid var(--color-navy)' : '2px solid var(--color-border)',
                                                background: data.audience === opt.value ? 'var(--color-navy)' : 'var(--color-card)',
                                                color: data.audience === opt.value ? '#fff' : 'var(--color-text)',
                                                transition: 'all 0.15s',
                                            }}
                                        >
                                            <span style={{ fontSize: 22, display: 'block', marginBottom: 4 }}>{opt.icon}</span>
                                            <span style={{ fontSize: 12, fontWeight: 600 }}>{opt.label}</span>
                                        </div>
                                    ))}
                                </div>
                            </div>

                            {/* Role selector */}
                            {data.audience === 'role' && (
                                <div style={{ marginBottom: 18 }}>
                                    <label style={{ display: 'block', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.06em', color: 'var(--color-text-2)', marginBottom: 5 }}>Role</label>
                                    <select value={data.role} onChange={e => setData('role', e.target.value)} style={{ width: '100%', background: 'var(--color-card)', border: '1.5px solid var(--color-border)', borderRadius: 8, padding: '9px 12px', fontSize: 13, color: 'var(--color-text)', fontFamily: 'inherit', outline: 'none' }}>
                                        <option value="">Select role…</option>
                                        {ROLES.map(r => <option key={r.value} value={r.value}>{r.label}</option>)}
                                    </select>
                                    {errors.role && <div style={{ color: 'var(--color-red)', fontSize: 12, marginTop: 4 }}>{errors.role}</div>}
                                </div>
                            )}

                            {/* Specific user */}
                            {data.audience === 'user' && (
                                <div style={{ marginBottom: 18 }}>
                                    <label style={{ display: 'block', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.06em', color: 'var(--color-text-2)', marginBottom: 5 }}>User email or MedID</label>
                                    <input
                                        type="text"
                                        value={data.user_identifier}
                                        onChange={e => setData('user_identifier', e.target.value)}
                                        placeholder="patient@example.com or MR-XXXXXX-XXXXXX"
                                        list="user-list"
                                        style={{ width: '100%', background: 'var(--color-card)', border: '1.5px solid var(--color-border)', borderRadius: 8, padding: '9px 12px', fontSize: 13, color: 'var(--color-text)', fontFamily: 'inherit', outline: 'none', boxSizing: 'border-box' }}
                                    />
                                    <datalist id="user-list">
                                        {users.map(u => <option key={u.id} value={u.email}>{u.name}</option>)}
                                    </datalist>
                                    <div style={{ fontSize: 11, color: 'var(--color-text-3)', marginTop: 4 }}>Enter an email address or patient MedID</div>
                                    {errors.user_identifier && <div style={{ color: 'var(--color-red)', fontSize: 12, marginTop: 4 }}>{errors.user_identifier}</div>}
                                </div>
                            )}

                            {/* Title */}
                            <div style={{ marginBottom: 18 }}>
                                <label style={{ display: 'block', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.06em', color: 'var(--color-text-2)', marginBottom: 5 }}>Notification Title *</label>
                                <input
                                    type="text"
                                    value={data.title}
                                    onChange={e => setData('title', e.target.value)}
                                    placeholder="System maintenance on Saturday, 14 June"
                                    maxLength={120}
                                    required
                                    style={{ width: '100%', background: 'var(--color-card)', border: '1.5px solid var(--color-border)', borderRadius: 8, padding: '9px 12px', fontSize: 13, color: 'var(--color-text)', fontFamily: 'inherit', outline: 'none', boxSizing: 'border-box' }}
                                />
                                <div style={{ fontSize: 11, color: 'var(--color-text-3)', marginTop: 4 }}>Appears as the subject line in email and bold heading in-app</div>
                                {errors.title && <div style={{ color: 'var(--color-red)', fontSize: 12, marginTop: 4 }}>{errors.title}</div>}
                            </div>

                            {/* Body */}
                            <div style={{ marginBottom: 24 }}>
                                <label style={{ display: 'block', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.06em', color: 'var(--color-text-2)', marginBottom: 5 }}>Message *</label>
                                <textarea
                                    value={data.body}
                                    onChange={e => setData('body', e.target.value)}
                                    rows={5}
                                    maxLength={2000}
                                    required
                                    placeholder="Dear user, please be informed that…"
                                    style={{ width: '100%', background: 'var(--color-card)', border: '1.5px solid var(--color-border)', borderRadius: 8, padding: '9px 12px', fontSize: 13, color: 'var(--color-text)', fontFamily: 'inherit', outline: 'none', resize: 'vertical', boxSizing: 'border-box' }}
                                />
                                <div style={{ fontSize: 11, color: 'var(--color-text-3)', marginTop: 2, textAlign: 'right' }}>{data.body.length} / 2000 characters</div>
                                {errors.body && <div style={{ color: 'var(--color-red)', fontSize: 12, marginTop: 4 }}>{errors.body}</div>}
                            </div>

                            <div style={{ background: 'var(--color-navy-lt)', color: 'var(--color-navy)', borderLeft: '3px solid var(--color-navy)', borderRadius: 8, padding: '10px 14px', fontSize: 12, marginBottom: 20 }}>
                                ℹ Sending will create one in-app notification and one email per recipient. Large audiences may take a moment to process.
                            </div>

                            <button
                                type="submit"
                                disabled={processing}
                                style={{ width: '100%', padding: '12px 16px', borderRadius: 10, border: 'none', background: processing ? '#a0aec0' : 'var(--color-navy)', color: '#fff', fontSize: 14, fontWeight: 700, cursor: processing ? 'not-allowed' : 'pointer', fontFamily: 'inherit' }}
                            >
                                {processing ? 'Sending…' : 'Send Notification'}
                            </button>
                        </form>
                    </div>
                </div>

                {/* History */}
                <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 14, overflow: 'hidden' }}>
                    <div style={{ padding: '16px 20px 12px', borderBottom: '1px solid var(--color-border)' }}>
                        <div style={{ fontWeight: 700, fontSize: 14, color: 'var(--color-navy)' }}>Broadcast History</div>
                        <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>Recent admin notifications, newest first</div>
                    </div>
                    {rows.length === 0 ? (
                        <div style={{ textAlign: 'center', padding: '48px 24px', color: 'var(--color-text-2)' }}>
                            <div style={{ fontSize: 36, marginBottom: 10 }}>📭</div>
                            <div style={{ fontWeight: 600 }}>No broadcasts yet</div>
                            <div style={{ fontSize: 13, marginTop: 4 }}>Sent notifications will appear here</div>
                        </div>
                    ) : (
                        <div style={{ overflowX: 'auto' }}>
                            <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: 13 }}>
                                <thead>
                                    <tr style={{ background: '#F8FAFC' }}>
                                        {['Title','Recipients','Sent by','Date'].map(h => (
                                            <th key={h} style={{ padding: '11px 16px', textAlign: 'left', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.05em', color: 'var(--color-text-2)', borderBottom: '1px solid var(--color-border)', whiteSpace: 'nowrap' }}>{h}</th>
                                        ))}
                                    </tr>
                                </thead>
                                <tbody>
                                    {rows.map((n, i) => (
                                        <tr key={i} style={{ borderBottom: '1px solid var(--color-border)' }}>
                                            <td style={{ padding: '12px 16px' }}>
                                                <div style={{ fontWeight: 600, color: 'var(--color-text)', marginBottom: 2 }}>{n.title}</div>
                                                <div style={{ fontSize: 12, color: 'var(--color-text-2)', whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis', maxWidth: 220 }}>{n.body}</div>
                                            </td>
                                            <td style={{ padding: '12px 16px' }}>
                                                <span style={{ background: 'var(--color-navy-lt)', color: 'var(--color-navy)', fontSize: 11, fontWeight: 700, padding: '2px 8px', borderRadius: 10 }}>{n.recipient_count?.toLocaleString()}</span>
                                            </td>
                                            <td style={{ padding: '12px 16px', fontSize: 13, color: 'var(--color-text-2)' }}>{n.sent_by_name}</td>
                                            <td style={{ padding: '12px 16px', fontSize: 12, color: 'var(--color-text-2)', whiteSpace: 'nowrap' }}>
                                                {n.created_at_date}<br />
                                                <span style={{ opacity: 0.7 }}>{n.created_at_time}</span>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
