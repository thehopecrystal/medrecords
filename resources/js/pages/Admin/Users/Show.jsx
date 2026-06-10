import { Link, useForm, router, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

const ROLE_COLORS = {
    patient:        { background: 'var(--color-navy-lt)',  color: 'var(--color-navy)' },
    provider:       { background: 'var(--color-green-lt)', color: 'var(--color-green)' },
    pharmacist:     { background: 'var(--color-green-lt)', color: 'var(--color-green)' },
    facility_admin: { background: 'var(--color-amber-lt)', color: 'var(--color-amber)' },
    super_admin:    { background: 'var(--color-red-lt)',   color: 'var(--color-red)' },
};

const ROLE_LABELS = {
    patient: 'Patient', provider: 'Provider', pharmacist: 'Pharmacist',
    facility_admin: 'Facility Admin', super_admin: 'Super Admin',
};

const inputStyle = { width: '100%', background: 'var(--color-card)', border: '1.5px solid var(--color-border)', borderRadius: 8, padding: '9px 12px', fontSize: 13, color: 'var(--color-text)', fontFamily: 'inherit', outline: 'none', boxSizing: 'border-box', marginBottom: 12 };

function DetailRow({ label, value }) {
    return (
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', padding: '9px 0', borderBottom: '1px solid var(--color-border)', fontSize: 13 }}>
            <span style={{ color: 'var(--color-text-3)', fontWeight: 500 }}>{label}</span>
            <span style={{ color: 'var(--color-text)', fontWeight: 600 }}>{value}</span>
        </div>
    );
}

export default function Show({ user }) {
    const { auth } = usePage().props;
    const isOwnAccount = auth?.user?.id === user.id;

    const editForm = useForm({
        name:  user.name,
        email: user.email,
        phone: user.phone,
        role:  user.role,
    });

    const pwForm = useForm({
        new_password: '',
        new_password_confirmation: '',
    });

    function submitEdit(e) {
        e.preventDefault();
        editForm.put(`/admin/users/${user.id}`, { preserveScroll: true });
    }

    function submitPassword(e) {
        e.preventDefault();
        if (!window.confirm(`Reset password for ${user.name}?`)) return;
        pwForm.post(`/admin/users/${user.id}/reset-password`, { preserveScroll: true, onSuccess: () => pwForm.reset() });
    }

    function toggleActive() {
        const action = user.is_active ? 'Deactivate' : 'Activate';
        if (!window.confirm(`${action} this user?`)) return;
        router.patch(`/admin/users/${user.id}/toggle-active`, {}, { preserveScroll: true });
    }

    function destroy() {
        if (!window.confirm(`Permanently delete ${user.name}? This cannot be undone.`)) return;
        router.delete(`/admin/users/${user.id}`);
    }

    return (
        <AppLayout title="User Detail">
            <div style={{ display: 'flex', alignItems: 'flex-start', justifyContent: 'space-between', marginBottom: 24, gap: 12, flexWrap: 'wrap' }}>
                <div>
                    <h1 style={{ fontSize: 22, fontWeight: 800, color: 'var(--color-navy)', margin: 0 }}>{user.name}</h1>
                    <div style={{ marginTop: 6, display: 'flex', gap: 8, alignItems: 'center' }}>
                        <span style={{ ...(ROLE_COLORS[user.role] ?? {}), fontSize: 11, fontWeight: 700, padding: '2px 8px', borderRadius: 10 }}>{ROLE_LABELS[user.role] ?? user.role}</span>
                        <span style={{ background: user.is_active ? 'var(--color-green-lt)' : 'var(--color-red-lt)', color: user.is_active ? 'var(--color-green)' : 'var(--color-red)', fontSize: 11, fontWeight: 700, padding: '2px 8px', borderRadius: 10 }}>
                            {user.is_active ? '● Active' : '● Inactive'}
                        </span>
                    </div>
                </div>
                <div style={{ display: 'flex', gap: 8, flexWrap: 'wrap' }}>
                    {!isOwnAccount && (
                        <>
                            <button onClick={toggleActive} style={{ padding: '7px 14px', borderRadius: 8, border: 'none', background: user.is_active ? 'var(--color-red)' : 'var(--color-green)', color: '#fff', fontSize: 12, fontWeight: 700, cursor: 'pointer', fontFamily: 'inherit' }}>
                                {user.is_active ? '⊘ Deactivate' : '✓ Activate'}
                            </button>
                            <button onClick={destroy} style={{ padding: '7px 14px', borderRadius: 8, border: 'none', background: 'var(--color-red)', color: '#fff', fontSize: 12, fontWeight: 700, cursor: 'pointer', fontFamily: 'inherit' }}>
                                🗑 Delete
                            </button>
                        </>
                    )}
                    <Link href="/admin/users" style={{ padding: '7px 14px', borderRadius: 8, border: '1.5px solid var(--color-border)', background: 'var(--color-card)', color: 'var(--color-text-2)', fontSize: 12, fontWeight: 600, textDecoration: 'none' }}>← Back</Link>
                </div>
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 20 }}>
                {/* Edit profile */}
                <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 14, overflow: 'hidden' }}>
                    <div style={{ padding: '16px 20px 12px', borderBottom: '1px solid var(--color-border)' }}>
                        <div style={{ fontWeight: 700, fontSize: 14, color: 'var(--color-navy)' }}>Edit Profile</div>
                    </div>
                    <div style={{ padding: '20px' }}>
                        <form onSubmit={submitEdit}>
                            <label style={{ display: 'block', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.06em', color: 'var(--color-text-2)', marginBottom: 5 }}>Full Name</label>
                            <input type="text" value={editForm.data.name} onChange={e => editForm.setData('name', e.target.value)} required style={inputStyle} />
                            {editForm.errors.name && <div style={{ color: 'var(--color-red)', fontSize: 12, marginTop: -8, marginBottom: 12 }}>{editForm.errors.name}</div>}

                            <label style={{ display: 'block', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.06em', color: 'var(--color-text-2)', marginBottom: 5 }}>Email</label>
                            <input type="email" value={editForm.data.email} onChange={e => editForm.setData('email', e.target.value)} required style={inputStyle} />
                            {editForm.errors.email && <div style={{ color: 'var(--color-red)', fontSize: 12, marginTop: -8, marginBottom: 12 }}>{editForm.errors.email}</div>}

                            <label style={{ display: 'block', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.06em', color: 'var(--color-text-2)', marginBottom: 5 }}>Phone</label>
                            <input type="tel" value={editForm.data.phone} onChange={e => editForm.setData('phone', e.target.value)} required style={inputStyle} />
                            {editForm.errors.phone && <div style={{ color: 'var(--color-red)', fontSize: 12, marginTop: -8, marginBottom: 12 }}>{editForm.errors.phone}</div>}

                            <label style={{ display: 'block', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.06em', color: 'var(--color-text-2)', marginBottom: 5 }}>Role</label>
                            <select value={editForm.data.role} onChange={e => editForm.setData('role', e.target.value)} required style={{ ...inputStyle, marginBottom: 20 }}>
                                {Object.entries(ROLE_LABELS).map(([val, label]) => <option key={val} value={val}>{label}</option>)}
                            </select>
                            {editForm.errors.role && <div style={{ color: 'var(--color-red)', fontSize: 12, marginTop: -16, marginBottom: 12 }}>{editForm.errors.role}</div>}

                            <button type="submit" disabled={editForm.processing} style={{ padding: '9px 20px', borderRadius: 9, background: editForm.processing ? '#a0aec0' : 'var(--color-navy)', color: '#fff', border: 'none', fontSize: 13, fontWeight: 700, cursor: editForm.processing ? 'not-allowed' : 'pointer', fontFamily: 'inherit' }}>
                                {editForm.processing ? 'Saving…' : 'Save Changes'}
                            </button>
                        </form>
                    </div>
                </div>

                {/* Right column */}
                <div style={{ display: 'flex', flexDirection: 'column', gap: 20 }}>
                    {/* Account info */}
                    <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 14, overflow: 'hidden' }}>
                        <div style={{ padding: '16px 20px 12px', borderBottom: '1px solid var(--color-border)' }}>
                            <div style={{ fontWeight: 700, fontSize: 14, color: 'var(--color-navy)' }}>Account Information</div>
                        </div>
                        <div style={{ padding: '4px 20px 12px' }}>
                            <DetailRow label="User ID" value={`#${user.id}`} />
                            <DetailRow label="Joined" value={user.created_at_formatted} />
                            <DetailRow label="Last Login" value={user.last_login_formatted ?? 'Never'} />
                            {user.nin && <DetailRow label="NIN" value={user.nin} />}
                            {user.patient?.med_id && <DetailRow label="MedID" value={<span style={{ fontFamily: 'monospace', fontSize: 12 }}>{user.patient.med_id}</span>} />}
                            {user.provider && (
                                <>
                                    <DetailRow label="MDCN Folio" value={user.provider.mdcn_folio ?? '—'} />
                                    <DetailRow label="MDCN Verified" value={
                                        user.provider.is_mdcn_verified
                                            ? <span style={{ background: 'var(--color-green-lt)', color: 'var(--color-green)', fontSize: 11, fontWeight: 700, padding: '2px 8px', borderRadius: 10 }}>✓ Verified</span>
                                            : <span style={{ background: 'var(--color-amber-lt)', color: 'var(--color-amber)', fontSize: 11, fontWeight: 700, padding: '2px 8px', borderRadius: 10 }}>Pending</span>
                                    } />
                                </>
                            )}
                        </div>
                    </div>

                    {/* Reset password */}
                    <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 14, overflow: 'hidden' }}>
                        <div style={{ padding: '16px 20px 12px', borderBottom: '1px solid var(--color-border)' }}>
                            <div style={{ fontWeight: 700, fontSize: 14, color: 'var(--color-navy)' }}>Reset Password</div>
                        </div>
                        <div style={{ padding: '20px' }}>
                            <form onSubmit={submitPassword}>
                                <label style={{ display: 'block', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.06em', color: 'var(--color-text-2)', marginBottom: 5 }}>New Password</label>
                                <input type="password" value={pwForm.data.new_password} onChange={e => pwForm.setData('new_password', e.target.value)} required placeholder="Min. 8 characters" style={inputStyle} />
                                {pwForm.errors.new_password && <div style={{ color: 'var(--color-red)', fontSize: 12, marginTop: -8, marginBottom: 12 }}>{pwForm.errors.new_password}</div>}

                                <label style={{ display: 'block', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.06em', color: 'var(--color-text-2)', marginBottom: 5 }}>Confirm New Password</label>
                                <input type="password" value={pwForm.data.new_password_confirmation} onChange={e => pwForm.setData('new_password_confirmation', e.target.value)} required style={{ ...inputStyle, marginBottom: 16 }} />

                                <button type="submit" disabled={pwForm.processing} style={{ padding: '9px 20px', borderRadius: 9, border: '1.5px solid var(--color-border)', background: 'var(--color-card)', color: 'var(--color-text-2)', fontSize: 13, fontWeight: 600, cursor: pwForm.processing ? 'not-allowed' : 'pointer', fontFamily: 'inherit' }}>
                                    {pwForm.processing ? 'Resetting…' : '🔑 Reset Password'}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
