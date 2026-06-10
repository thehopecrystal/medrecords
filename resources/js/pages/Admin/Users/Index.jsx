import { useState } from 'react';
import { Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

const ROLE_COLORS = {
    patient:       { background: 'var(--color-navy-lt)',  color: 'var(--color-navy)' },
    provider:      { background: 'var(--color-green-lt)', color: 'var(--color-green)' },
    pharmacist:    { background: 'var(--color-green-lt)', color: 'var(--color-green)' },
    facility_admin:{ background: 'var(--color-amber-lt)', color: 'var(--color-amber)' },
    super_admin:   { background: 'var(--color-red-lt)',   color: 'var(--color-red)' },
};

const ROLE_LABELS = {
    patient: 'Patient', provider: 'Provider', pharmacist: 'Pharmacist',
    facility_admin: 'Facility Admin', super_admin: 'Super Admin',
};

const STATS_CONFIG = [
    { key: 'patient',        icon: '👤', label: 'Patients',       color: 'navy' },
    { key: 'provider',       icon: '🩺', label: 'Providers',      color: 'green' },
    { key: 'pharmacist',     icon: '💊', label: 'Pharmacists',    color: 'green' },
    { key: 'facility_admin', icon: '🏥', label: 'Facility Admins',color: 'amber' },
    { key: 'super_admin',    icon: '🛡', label: 'Super Admins',   color: 'red' },
];

function initials(name = '') {
    return (name.trim().split(' ').slice(0, 2).map(p => p[0] ?? '').join('')).toUpperCase() || '?';
}

export default function Index({ users, roleCounts, filters }) {
    const { data, links, last_page } = users;
    const [search, setSearch]   = useState(filters?.search ?? '');
    const [role,   setRole]     = useState(filters?.role   ?? 'all');
    const [status, setStatus]   = useState(filters?.status ?? '');

    const inputStyle = { background: 'var(--color-card)', border: '1.5px solid var(--color-border)', borderRadius: 8, padding: '8px 12px', fontSize: 13, color: 'var(--color-text)', fontFamily: 'inherit', outline: 'none', width: '100%', boxSizing: 'border-box' };

    function applyFilter(e) {
        e.preventDefault();
        const params = {};
        if (search) params.search = search;
        if (role && role !== 'all') params.role = role;
        if (status) params.status = status;
        router.get('/admin/users', params, { preserveScroll: true });
    }

    function clearFilter() {
        setSearch(''); setRole('all'); setStatus('');
        router.get('/admin/users', {}, { preserveScroll: true });
    }

    const hasFilters = !!(search || (role && role !== 'all') || status);

    return (
        <AppLayout title="Users & Roles">
            <div style={{ display: 'flex', alignItems: 'flex-start', justifyContent: 'space-between', marginBottom: 24, gap: 12 }}>
                <div>
                    <h1 style={{ fontSize: 22, fontWeight: 800, color: 'var(--color-navy)', margin: 0 }}>Users &amp; Roles</h1>
                    <p style={{ fontSize: 13, color: 'var(--color-text-2)', marginTop: 4 }}>Manage all platform accounts and role assignments.</p>
                </div>
                <Link href="/admin/users/create" style={{ padding: '9px 18px', borderRadius: 9, background: 'var(--color-navy)', color: '#fff', fontSize: 13, fontWeight: 700, textDecoration: 'none', whiteSpace: 'nowrap' }}>+ New User</Link>
            </div>

            {/* Stats */}
            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(5,1fr)', gap: 14, marginBottom: 24 }}>
                {STATS_CONFIG.map(s => {
                    const bgMap = { navy: 'var(--color-navy-lt)', green: 'var(--color-green-lt)', amber: 'var(--color-amber-lt)', red: 'var(--color-red-lt)' };
                    const bdrMap = { navy: 'var(--color-navy)', green: 'var(--color-green)', amber: 'var(--color-amber)', red: 'var(--color-red)' };
                    return (
                        <div key={s.key} style={{ background: bgMap[s.color], borderTop: `3px solid ${bdrMap[s.color]}`, borderRadius: 12, padding: '16px 18px', position: 'relative' }}>
                            <div style={{ fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.06em', color: 'var(--color-text-2)', marginBottom: 4 }}>{s.label}</div>
                            <div style={{ fontSize: 26, fontWeight: 800, color: 'var(--color-navy)' }}>{roleCounts[s.key] ?? 0}</div>
                            <div style={{ position: 'absolute', right: 14, top: 14, fontSize: 20, opacity: 0.4 }}>{s.icon}</div>
                        </div>
                    );
                })}
            </div>

            {/* Filters */}
            <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 14, padding: '16px 20px', marginBottom: 20 }}>
                <form onSubmit={applyFilter} style={{ display: 'flex', gap: 10, flexWrap: 'wrap', alignItems: 'flex-end' }}>
                    <div style={{ flex: 1, minWidth: 200 }}>
                        <label style={{ display: 'block', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.06em', color: 'var(--color-text-2)', marginBottom: 5 }}>Search</label>
                        <input type="text" value={search} onChange={e => setSearch(e.target.value)} placeholder="Name, email, or phone…" style={inputStyle} />
                    </div>
                    <div style={{ width: 160 }}>
                        <label style={{ display: 'block', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.06em', color: 'var(--color-text-2)', marginBottom: 5 }}>Role</label>
                        <select value={role} onChange={e => setRole(e.target.value)} style={inputStyle}>
                            <option value="all">All roles</option>
                            <option value="patient">Patient</option>
                            <option value="provider">Provider</option>
                            <option value="pharmacist">Pharmacist</option>
                            <option value="facility_admin">Facility Admin</option>
                            <option value="super_admin">Super Admin</option>
                        </select>
                    </div>
                    <div style={{ width: 140 }}>
                        <label style={{ display: 'block', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.06em', color: 'var(--color-text-2)', marginBottom: 5 }}>Status</label>
                        <select value={status} onChange={e => setStatus(e.target.value)} style={inputStyle}>
                            <option value="">All</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <button type="submit" style={{ padding: '8px 16px', borderRadius: 8, background: 'var(--color-navy)', color: '#fff', border: 'none', fontSize: 12, fontWeight: 700, cursor: 'pointer', fontFamily: 'inherit' }}>Filter</button>
                    {hasFilters && (
                        <button type="button" onClick={clearFilter} style={{ padding: '8px 16px', borderRadius: 8, border: '1.5px solid var(--color-border)', background: 'var(--color-card)', color: 'var(--color-text-2)', fontSize: 12, fontWeight: 600, cursor: 'pointer', fontFamily: 'inherit' }}>Clear</button>
                    )}
                </form>
            </div>

            {/* Table */}
            <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 14, overflow: 'hidden' }}>
                <div style={{ overflowX: 'auto' }}>
                    <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: 13 }}>
                        <thead>
                            <tr style={{ background: '#F8FAFC' }}>
                                {['#','Name','Email / Phone','Role','Status','Last Login','Joined',''].map(h => (
                                    <th key={h} style={{ padding: '11px 16px', textAlign: 'left', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.05em', color: 'var(--color-text-2)', borderBottom: '1px solid var(--color-border)', whiteSpace: 'nowrap' }}>{h}</th>
                                ))}
                            </tr>
                        </thead>
                        <tbody>
                            {data.length === 0 ? (
                                <tr>
                                    <td colSpan={8}>
                                        <div style={{ textAlign: 'center', padding: '40px 24px', color: 'var(--color-text-2)' }}>
                                            <div style={{ fontSize: 36, marginBottom: 10 }}>👥</div>
                                            <div style={{ fontWeight: 600, fontSize: 15, marginBottom: 6 }}>No users found</div>
                                            <p style={{ fontSize: 13 }}>Try adjusting your filters or <Link href="/admin/users/create" style={{ color: 'var(--color-navy)' }}>create a new user</Link>.</p>
                                        </div>
                                    </td>
                                </tr>
                            ) : data.map(user => (
                                <tr key={user.id} style={{ borderBottom: '1px solid var(--color-border)' }}>
                                    <td style={{ padding: '12px 16px', color: 'var(--color-text-3)', fontSize: 12 }}>{user.id}</td>
                                    <td style={{ padding: '12px 16px' }}>
                                        <div style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
                                            <div style={{ width: 32, height: 32, borderRadius: '50%', background: 'linear-gradient(135deg,#2C5F2D,#1E3A5F)', color: '#fff', fontSize: 12, fontWeight: 700, display: 'flex', alignItems: 'center', justifyContent: 'center', flexShrink: 0 }}>
                                                {initials(user.name)}
                                            </div>
                                            <div>
                                                <div style={{ fontWeight: 600, fontSize: 13 }}>{user.name}</div>
                                                {user.nin && <div style={{ fontSize: 11, color: 'var(--color-text-3)' }}>NIN: {user.nin}</div>}
                                            </div>
                                        </div>
                                    </td>
                                    <td style={{ padding: '12px 16px' }}>
                                        <div style={{ fontSize: 13 }}>{user.email}</div>
                                        <div style={{ fontSize: 11, color: 'var(--color-text-3)' }}>{user.phone}</div>
                                    </td>
                                    <td style={{ padding: '12px 16px' }}>
                                        <span style={{ ...(ROLE_COLORS[user.role] ?? { background: '#F1F5F9', color: '#64748B' }), fontSize: 11, fontWeight: 700, padding: '2px 8px', borderRadius: 10 }}>
                                            {ROLE_LABELS[user.role] ?? user.role}
                                        </span>
                                    </td>
                                    <td style={{ padding: '12px 16px' }}>
                                        <span style={{ background: user.is_active ? 'var(--color-green-lt)' : 'var(--color-red-lt)', color: user.is_active ? 'var(--color-green)' : 'var(--color-red)', fontSize: 11, fontWeight: 700, padding: '2px 8px', borderRadius: 10 }}>
                                            {user.is_active ? '● Active' : '● Inactive'}
                                        </span>
                                    </td>
                                    <td style={{ padding: '12px 16px', fontSize: 12, color: 'var(--color-text-2)' }}>{user.last_login_human ?? 'Never'}</td>
                                    <td style={{ padding: '12px 16px', fontSize: 12, color: 'var(--color-text-2)' }}>{user.created_at_formatted}</td>
                                    <td style={{ padding: '12px 16px' }}>
                                        <Link href={`/admin/users/${user.id}`} style={{ padding: '5px 12px', borderRadius: 7, border: '1.5px solid var(--color-border)', fontSize: 12, fontWeight: 600, color: 'var(--color-text-2)', textDecoration: 'none' }}>View</Link>
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
