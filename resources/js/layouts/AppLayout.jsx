import { useState, useEffect, useRef } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import { useFlash } from '@/hooks/useFlash';

const NAV = {
    patient: [
        { section: 'Menu' },
        { icon: '🏠', label: 'Dashboard',         href: '/patient/dashboard',    match: ['patient.dashboard'] },
        { icon: '📋', label: 'My Health Records',  href: '/patient/record',       match: ['patient.record','patient.prescription'] },
        { icon: '🔐', label: 'Consent Management', href: '/patient/consents',     match: ['patient.consents'], badgeKey: 'pendingConsents' },
        { icon: '🔑', label: 'Share Record',        href: '/patient/access-code',  match: ['patient.access-code'] },
        { icon: '👨‍👩‍👧', label: 'My Family',           href: '/patient/family',       match: ['patient.family'] },
        { icon: '📊', label: 'Family Summary',      href: '/patient/family/summary', match: ['patient.family.summary'] },
        { section: 'Health Tools' },
        { icon: '📅', label: 'Appointments',        href: '/patient/appointments', match: ['patient.appointments'] },
        { icon: '❤️', label: 'Vitals Diary',         href: '/patient/vitals',       match: ['patient.vitals'] },
        { icon: '💊', label: 'Refill Requests',     href: '/patient/refills',      match: ['patient.refills'] },
        { icon: '🧪', label: 'Lab Upload',           href: '/patient/lab-upload',   match: ['patient.lab-upload'] },
        { icon: '🔔', label: 'Notifications',        href: '/patient/notifications',match: ['patient.notifications'], badgeKey: 'unreadCount' },
        { section: 'Account' },
        { icon: '👁',  label: 'Access History',      href: '/patient/audit-log',    match: ['patient.audit-log'] },
        { icon: '👤', label: 'Edit Profile',         href: '/patient/profile',      match: ['patient.profile'] },
    ],
    provider: [
        { section: 'Workstation' },
        { icon: '🏥', label: 'Dashboard',           href: '/provider/dashboard',   match: ['provider.dashboard'] },
        { icon: '🔍', label: 'Patient Search',       href: '/provider/search',      match: ['provider.search','provider.patient'] },
        { icon: '📅', label: 'My Schedule',          href: '/provider/appointments',match: ['provider.appointments'] },
        { icon: '💊', label: 'Refill Requests',      href: '/provider/refills',     match: ['provider.refills'] },
        { icon: '📋', label: 'Referral Inbox',       href: '/provider/referrals',   match: ['provider.referrals'] },
    ],
    pharmacist: [
        { section: 'Pharmacy' },
        { icon: '🏥', label: 'Dashboard',             href: '/pharmacy/dashboard', match: ['pharmacy.dashboard'] },
        { icon: '🔍', label: 'Verify Prescription',   href: '/pharmacy/lookup',    match: ['pharmacy.lookup'] },
        { icon: '📋', label: 'Dispense History',      href: '/pharmacy/history',   match: ['pharmacy.history'] },
    ],
    admin: [
        { section: 'Administration' },
        { icon: '📊', label: 'Dashboard',             href: '/admin/dashboard',           match: ['admin.dashboard'] },
        { icon: '🏥', label: 'Facilities',            href: '/admin/facilities',           match: ['admin.facilities'] },
        { icon: '📥', label: 'Import Facilities',     href: '/admin/facilities/import',    match: ['admin.facilities.import'] },
        { icon: '⚡', label: 'Break-glass Review',    href: '/admin/break-glass',         match: ['admin.break-glass'], badgeKey: 'activeBreakGlass' },
        { icon: '🩺', label: 'MDCN Verification',     href: '/admin/providers',            match: ['admin.providers'] },
        { section: 'Super Admin', superOnly: true },
        { icon: '👥', label: 'Users & Roles',         href: '/admin/users',                match: ['admin.users'], superOnly: true },
        { icon: '💳', label: 'Plans & Pricing',       href: '/admin/plans',                match: ['admin.plans'], superOnly: true },
        { section: 'System' },
        { icon: '📣', label: 'Send Notification',     href: '/admin/notifications',        match: ['admin.notifications'] },
        { icon: '⚙️', label: 'Settings',              href: '/admin/settings',             match: ['admin.settings'] },
    ],
};

function getInitials(name = '') {
    const parts = name.trim().split(' ');
    return ((parts[0]?.[0] ?? '') + (parts[1]?.[0] ?? '')).toUpperCase() || '?';
}

function getRoleLabel(role) {
    if (role === 'facility_admin' || role === 'super_admin') return 'Administrator';
    return role || '';
}

function getPortalLabel(role) {
    if (role === 'patient')   return 'Patient Portal';
    if (role === 'provider')  return 'Clinical Workstation';
    if (role === 'pharmacist') return 'Pharmacy';
    if (role === 'facility_admin' || role === 'super_admin') return 'Administration';
    return 'MedRecords';
}

function isActive(itemMatches, currentPath) {
    return itemMatches?.some(m => currentPath.includes(m.replace(/\./g, '/')));
}

export default function AppLayout({ children, title }) {
    const { auth, site, ui, url } = usePage().props;
    const { toast, dismiss } = useFlash();
    const user = auth?.user;
    const role = user?.role ?? 'guest';
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const [userMenuOpen, setUserMenuOpen] = useState(false);
    const [dark, setDark] = useState(() => {
        if (typeof window !== 'undefined') {
            return localStorage.getItem('medrecords_theme') === 'dark';
        }
        return false;
    });
    const userMenuRef = useRef(null);
    const currentPath = typeof window !== 'undefined' ? window.location.pathname : '';

    useEffect(() => {
        document.documentElement.setAttribute('data-theme', dark ? 'dark' : 'light');
        localStorage.setItem('medrecords_theme', dark ? 'dark' : 'light');
    }, [dark]);

    useEffect(() => {
        const saved = localStorage.getItem('medrecords_theme');
        if (saved === 'dark') setDark(true);
    }, []);

    useEffect(() => {
        function handler(e) {
            if (userMenuRef.current && !userMenuRef.current.contains(e.target)) {
                setUserMenuOpen(false);
            }
        }
        document.addEventListener('mousedown', handler);
        return () => document.removeEventListener('mousedown', handler);
    }, []);

    const navItems = role === 'facility_admin' || role === 'super_admin'
        ? NAV.admin
        : NAV[role] ?? [];

    const initials = getInitials(user?.name);

    function logout(e) {
        e.preventDefault();
        router.post('/logout');
    }

    return (
        <div style={{ display: 'flex', minHeight: '100vh' }}>
            {/* Sidebar overlay (mobile) */}
            {sidebarOpen && (
                <div
                    onClick={() => setSidebarOpen(false)}
                    style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.5)', zIndex: 190 }}
                />
            )}

            {/* Sidebar */}
            <aside style={{
                width: 260,
                background: 'var(--color-sb-bg, #0D1F35)',
                display: 'flex',
                flexDirection: 'column',
                position: 'fixed',
                inset: '0 auto 0 0',
                zIndex: 200,
                overflow: 'hidden',
                transition: 'transform 0.25s ease',
                transform: sidebarOpen ? 'translateX(0)' : undefined,
            }} className="sb-aside">
                {/* Logo */}
                <div style={{ display: 'flex', alignItems: 'center', gap: 10, padding: '18px 20px 16px', borderBottom: '1px solid rgba(255,255,255,0.08)', flexShrink: 0 }}>
                    {site?.logo
                        ? <img src={site.logo} alt={site.name} style={{ width: 30, height: 30, borderRadius: 8, objectFit: 'contain' }} />
                        : <div style={{ width: 30, height: 30, borderRadius: 8, background: 'linear-gradient(135deg,#1E3A5F,#2C5F2D)', display: 'flex', alignItems: 'center', justifyContent: 'center', fontWeight: 800, color: '#fff', fontSize: 14, flexShrink: 0 }}>M</div>
                    }
                    <div>
                        <div style={{ fontSize: 15, fontWeight: 800, color: '#fff', letterSpacing: '-0.02em', lineHeight: 1 }}>{site?.name ?? 'MedRecords'}</div>
                        <div style={{ fontSize: 10, color: 'rgba(255,255,255,0.45)', textTransform: 'uppercase', letterSpacing: '0.06em', marginTop: 2 }}>{getPortalLabel(role)}</div>
                    </div>
                </div>

                {/* User info */}
                <div style={{ display: 'flex', alignItems: 'center', gap: 10, padding: '14px 20px', borderBottom: '1px solid rgba(255,255,255,0.08)', flexShrink: 0 }}>
                    {user?.avatar
                        ? <img src={`/storage/${user.avatar}`} alt="" style={{ width: 36, height: 36, borderRadius: '50%', objectFit: 'cover', flexShrink: 0 }} />
                        : <div style={{ width: 36, height: 36, borderRadius: '50%', background: 'linear-gradient(135deg,#2C5F2D,#1E3A5F)', color: '#fff', fontSize: 13, fontWeight: 700, display: 'flex', alignItems: 'center', justifyContent: 'center', flexShrink: 0 }}>{initials}</div>
                    }
                    <div>
                        <div style={{ fontSize: 13, fontWeight: 600, color: '#fff', lineHeight: 1.2 }}>{(user?.name ?? '').substring(0, 22)}</div>
                        <div style={{ fontSize: 11, color: 'rgba(255,255,255,0.45)', marginTop: 1, textTransform: 'capitalize' }}>{getRoleLabel(role)}</div>
                    </div>
                </div>

                {/* Nav */}
                <nav style={{ flex: 1, padding: '8px 12px', overflowY: 'auto' }}>
                    {navItems.map((item, i) => {
                        if (item.section !== undefined) {
                            if (item.superOnly && role !== 'super_admin') return null;
                            return (
                                <div key={i} style={{ padding: '14px 8px 5px', fontSize: 10, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.08em', color: 'rgba(255,255,255,0.3)' }}>
                                    {item.section}
                                </div>
                            );
                        }
                        if (item.superOnly && role !== 'super_admin') return null;
                        const active = isActive(item.match, currentPath);
                        const badge = item.badgeKey === 'pendingConsents' ? (ui?.pendingConsents ? '!' : null)
                            : item.badgeKey === 'unreadCount' ? (ui?.unreadCount > 0 ? ui.unreadCount : null)
                            : item.badgeKey === 'activeBreakGlass' ? (ui?.activeBreakGlass > 0 ? ui.activeBreakGlass : null)
                            : null;
                        return (
                            <Link key={i} href={item.href} style={{
                                display: 'flex', alignItems: 'center', gap: 10,
                                padding: '9px 12px', borderRadius: 8,
                                fontSize: 13, fontWeight: active ? 600 : 500,
                                color: active ? '#fff' : 'rgba(255,255,255,0.65)',
                                background: active ? 'rgba(255,255,255,0.12)' : 'transparent',
                                marginBottom: 1,
                                position: 'relative',
                                textDecoration: 'none',
                                transition: 'background 0.15s, color 0.15s',
                                borderLeft: active ? '3px solid #4A9CF8' : '3px solid transparent',
                            }}
                            onMouseEnter={e => { if (!active) { e.currentTarget.style.background = 'rgba(255,255,255,0.06)'; e.currentTarget.style.color = '#fff'; }}}
                            onMouseLeave={e => { if (!active) { e.currentTarget.style.background = 'transparent'; e.currentTarget.style.color = 'rgba(255,255,255,0.65)'; }}}
                            >
                                <span style={{ fontSize: 15, width: 20, textAlign: 'center', flexShrink: 0 }}>{item.icon}</span>
                                {item.label}
                                {badge && (
                                    <span style={{ marginLeft: 'auto', background: '#B23A48', color: '#fff', fontSize: 10, fontWeight: 700, padding: '1px 6px', borderRadius: 10, minWidth: 18, textAlign: 'center' }}>
                                        {badge}
                                    </span>
                                )}
                            </Link>
                        );
                    })}
                </nav>

                {/* Sign out */}
                <div style={{ padding: '10px 12px 14px', borderTop: '1px solid rgba(255,255,255,0.08)', flexShrink: 0 }}>
                    <form onSubmit={logout}>
                        <button type="submit" style={{ width: '100%', display: 'flex', alignItems: 'center', gap: 10, padding: '9px 12px', borderRadius: 8, fontSize: 13, fontWeight: 500, color: 'rgba(255,255,255,0.65)', background: 'none', border: 'none', cursor: 'pointer', textAlign: 'left' }}
                            onMouseEnter={e => { e.currentTarget.style.background = 'rgba(178,58,72,0.12)'; e.currentTarget.style.color = '#FBEAEC'; }}
                            onMouseLeave={e => { e.currentTarget.style.background = 'none'; e.currentTarget.style.color = 'rgba(255,255,255,0.65)'; }}
                        >
                            <span style={{ fontSize: 15, width: 20, textAlign: 'center' }}>🚪</span> Sign out
                        </button>
                    </form>
                </div>
            </aside>

            {/* Main */}
            <div style={{ marginLeft: 260, flex: 1, minHeight: '100vh', display: 'flex', flexDirection: 'column' }} className="sb-main">
                {/* Topbar */}
                <div style={{ height: 62, background: '#fff', borderBottom: '1px solid var(--color-border)', display: 'flex', alignItems: 'center', padding: '0 24px', gap: 14, position: 'sticky', top: 0, zIndex: 100, boxShadow: '0 1px 2px rgba(15,23,42,0.05)' }}>
                    <button onClick={() => setSidebarOpen(s => !s)} style={{ display: 'none', background: 'none', border: 'none', cursor: 'pointer', fontSize: 20, padding: 4, color: 'var(--color-text-2)' }} className="sb-toggle">☰</button>
                    <div style={{ fontSize: 16, fontWeight: 700, color: 'var(--color-navy)', flex: 1 }}>{title}</div>
                    <div style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
                        <button onClick={() => setDark(d => !d)} title="Toggle dark mode" style={{ background: 'none', border: 'none', cursor: 'pointer', width: 36, height: 36, borderRadius: 8, display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 17, color: 'var(--color-text-2)' }}>
                            {dark ? '🌙' : '☀️'}
                        </button>
                        <Link href={role === 'patient' ? '/patient/notifications' : '#'}
                            style={{ position: 'relative', background: 'none', border: 'none', cursor: 'pointer', width: 36, height: 36, borderRadius: 8, display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 17, color: 'var(--color-text-2)', textDecoration: 'none' }}
                        >
                            🔔
                            {ui?.unreadCount > 0 && <span style={{ position: 'absolute', top: 5, right: 5, width: 8, height: 8, borderRadius: '50%', background: '#B23A48', border: '2px solid white' }} />}
                        </Link>

                        {/* User menu */}
                        <div ref={userMenuRef} style={{ position: 'relative' }}>
                            <button onClick={() => setUserMenuOpen(o => !o)} style={{ display: 'flex', alignItems: 'center', gap: 8, background: 'none', border: 'none', cursor: 'pointer', padding: '6px 10px', borderRadius: 8 }}>
                                <div style={{ width: 32, height: 32, borderRadius: '50%', background: 'linear-gradient(135deg,#2C5F2D,#1E3A5F)', color: '#fff', fontSize: 12, fontWeight: 700, display: 'flex', alignItems: 'center', justifyContent: 'center', overflow: 'hidden' }}>
                                    {user?.avatar ? <img src={`/storage/${user.avatar}`} alt="" style={{ width: '100%', height: '100%', objectFit: 'cover', borderRadius: '50%' }} /> : initials}
                                </div>
                                <span style={{ fontSize: 13, fontWeight: 600, color: 'var(--color-text)' }}>{(user?.name ?? '').substring(0, 16)}</span>
                                <span style={{ fontSize: 10, color: 'var(--color-text-3)' }}>▾</span>
                            </button>
                            {userMenuOpen && (
                                <div style={{ position: 'absolute', right: 0, top: 'calc(100% + 6px)', background: '#fff', border: '1px solid var(--color-border)', borderRadius: 10, boxShadow: '0 8px 30px rgba(15,23,42,0.12)', minWidth: 200, zIndex: 300, overflow: 'hidden' }}>
                                    <div style={{ padding: '14px 16px', borderBottom: '1px solid var(--color-border)' }}>
                                        <div style={{ fontSize: 14, fontWeight: 700 }}>{user?.name}</div>
                                        <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 1 }}>{user?.email}</div>
                                    </div>
                                    {role === 'patient' && <>
                                        <Link href="/patient/profile" onClick={() => setUserMenuOpen(false)} style={{ display: 'flex', alignItems: 'center', gap: 8, padding: '10px 16px', fontSize: 13, color: 'var(--color-text-2)', textDecoration: 'none' }}>👤 Edit profile</Link>
                                        <Link href="/patient/access-code" onClick={() => setUserMenuOpen(false)} style={{ display: 'flex', alignItems: 'center', gap: 8, padding: '10px 16px', fontSize: 13, color: 'var(--color-text-2)', textDecoration: 'none' }}>🔑 Access code</Link>
                                    </>}
                                    <hr style={{ border: 'none', borderTop: '1px solid var(--color-border)', margin: '4px 0' }} />
                                    <Link href="/" style={{ display: 'flex', alignItems: 'center', gap: 8, padding: '10px 16px', fontSize: 13, color: 'var(--color-text-2)', textDecoration: 'none' }}>🏠 Homepage</Link>
                                    <hr style={{ border: 'none', borderTop: '1px solid var(--color-border)', margin: '4px 0' }} />
                                    <form onSubmit={logout}>
                                        <button type="submit" style={{ width: '100%', display: 'flex', alignItems: 'center', gap: 8, padding: '10px 16px', fontSize: 13, color: '#B23A48', background: 'none', border: 'none', cursor: 'pointer', textAlign: 'left' }}>🚪 Sign out</button>
                                    </form>
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                {/* Flash */}
                {toast && (
                    <div style={{ margin: '20px 28px 0', display: 'flex', alignItems: 'flex-start', gap: 10, padding: '12px 16px', borderRadius: 10, fontSize: 13, borderLeft: `3px solid ${toast.type === 'success' ? '#2C5F2D' : '#B23A48'}`, background: toast.type === 'success' ? '#EDF7ED' : '#FBEAEC', color: toast.type === 'success' ? '#1A4D1B' : '#7A1F2C' }}>
                        <span>{toast.type === 'success' ? '✓' : '✗'}</span>
                        <span style={{ flex: 1 }}>{toast.message}</span>
                        <button onClick={dismiss} style={{ background: 'none', border: 'none', cursor: 'pointer', fontSize: 16, opacity: 0.5 }}>×</button>
                    </div>
                )}

                {/* Content */}
                <div style={{ padding: 28, flex: 1 }}>
                    {children}
                </div>
            </div>

            <style>{`
                @media (max-width: 900px) {
                    .sb-aside { transform: translateX(-100%); }
                    .sb-main { margin-left: 0 !important; }
                    .sb-toggle { display: flex !important; }
                }
            `}</style>
        </div>
    );
}
