import { useState } from 'react';
import { usePage, useForm, router } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

const STATES = ['Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno','Cross River','Delta','Ebonyi','Edo','Ekiti','Enugu','FCT','Gombe','Imo','Jigawa','Kaduna','Kano','Katsina','Kebbi','Kogi','Kwara','Lagos','Nasarawa','Niger','Ogun','Ondo','Osun','Oyo','Plateau','Rivers','Sokoto','Taraba','Yobe','Zamfara'];

function FormField({ label, children, error, span2 = false }) {
    return (
        <div style={{ gridColumn: span2 ? 'span 2' : 'span 1' }}>
            <label style={{ display: 'block', fontSize: 12, fontWeight: 600, color: 'var(--color-text-2)', marginBottom: 6 }}>{label}</label>
            {children}
            {error && <div style={{ fontSize: 11, color: 'var(--color-red)', marginTop: 4 }}>{error}</div>}
        </div>
    );
}

function inputStyle() {
    return { width: '100%', padding: '9px 12px', borderRadius: 8, border: '1.5px solid var(--color-border)', fontSize: 13, background: 'var(--color-card)', color: 'var(--color-text)', boxSizing: 'border-box' };
}

function selectStyle() {
    return { ...inputStyle(), cursor: 'pointer' };
}

export default function Profile({ patient, user }) {
    const { flash } = usePage().props;
    const [activeSection, setActiveSection] = useState('personal');

    const parts = (user.name || '').trim().split(' ');
    const initials = ((parts[0]?.[0] ?? '') + (parts[1]?.[0] ?? '')).toUpperCase() || '?';

    const fields = {
        'Date of birth': patient.dob,
        'Gender': patient.gender,
        'Blood type': patient.blood_type,
        'State': patient.state,
        'Emergency contact': patient.emergency_contact_name,
    };
    const filled = Object.values(fields).filter(Boolean).length;
    const pct = Math.round((filled / Object.keys(fields).length) * 100);

    const personal = useForm({
        name: user.name || '',
        username: user.username || '',
        email: user.email || '',
        phone: user.phone || '',
        dob: patient.dob || '',
        gender: patient.gender || '',
        state: patient.state || '',
        city: patient.city || '',
        address: patient.address || '',
        // keep medical/emergency fields as-is
        blood_type: patient.blood_type || '',
        genotype: patient.genotype || '',
        emergency_contact_name: patient.emergency_contact_name || '',
        emergency_contact_phone: patient.emergency_contact_phone || '',
    });

    const medical = useForm({
        name: user.name || '',
        email: user.email || '',
        phone: user.phone || '',
        dob: patient.dob || '',
        gender: patient.gender || '',
        state: patient.state || '',
        city: patient.city || '',
        address: patient.address || '',
        blood_type: patient.blood_type || '',
        genotype: patient.genotype || '',
        emergency_contact_name: patient.emergency_contact_name || '',
        emergency_contact_phone: patient.emergency_contact_phone || '',
    });

    const emergency = useForm({
        name: user.name || '',
        email: user.email || '',
        phone: user.phone || '',
        dob: patient.dob || '',
        gender: patient.gender || '',
        blood_type: patient.blood_type || '',
        genotype: patient.genotype || '',
        state: patient.state || '',
        city: patient.city || '',
        address: patient.address || '',
        emergency_contact_name: patient.emergency_contact_name || '',
        emergency_contact_phone: patient.emergency_contact_phone || '',
    });

    const password = useForm({ current_password: '', password: '', password_confirmation: '' });

    const navSections = [
        { id: 'personal', icon: '👤', label: 'Personal Info' },
        { id: 'medical',  icon: '🩺', label: 'Medical Profile' },
        { id: 'emergency', icon: '🚨', label: 'Emergency Contact' },
        { id: 'security', icon: '🔒', label: 'Password & Security' },
        { id: 'two-factor', icon: '🛡', label: 'Two-Factor Auth' },
    ];

    function submitPersonal(e) {
        e.preventDefault();
        personal.put('/patient/profile');
    }
    function submitMedical(e) {
        e.preventDefault();
        medical.put('/patient/profile');
    }
    function submitEmergency(e) {
        e.preventDefault();
        emergency.put('/patient/profile');
    }
    function submitPassword(e) {
        e.preventDefault();
        password.put('/patient/profile/password', { onSuccess: () => password.reset() });
    }
    function toggle2fa(action) {
        router.post('/patient/profile/2fa', { action });
    }

    const cardStyle = { background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12, marginBottom: 20 };
    const cardHeaderStyle = { padding: '16px 20px', borderBottom: '1px solid var(--color-border)' };
    const gridStyle = { display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16 };

    return (
        <AppLayout title="My Profile">
            <div style={{ marginBottom: 20 }}>
                <h1 style={{ fontSize: 20, fontWeight: 800, color: 'var(--color-navy)', margin: 0 }}>Edit Profile</h1>
                <p style={{ fontSize: 13, color: 'var(--color-text-2)', marginTop: 4 }}>Keep your personal and medical information up to date</p>
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: '260px 1fr', gap: 20, alignItems: 'start' }}>
                {/* Sidebar */}
                <div style={cardStyle}>
                    <div style={{ padding: 20 }}>
                        {/* Avatar */}
                        <div style={{ textAlign: 'center', marginBottom: 18 }}>
                            {user.avatar
                                ? <img src={`/storage/${user.avatar}`} alt="" style={{ width: 72, height: 72, borderRadius: '50%', objectFit: 'cover', margin: '0 auto 10px', display: 'block', border: '3px solid var(--color-border)' }} />
                                : <div style={{ width: 72, height: 72, borderRadius: '50%', background: 'linear-gradient(135deg,#2C5F2D,#1E3A5F)', color: '#fff', fontSize: 26, fontWeight: 800, display: 'flex', alignItems: 'center', justifyContent: 'center', margin: '0 auto 10px' }}>{initials}</div>
                            }
                            <div style={{ fontSize: 15, fontWeight: 700, color: 'var(--color-navy)' }}>{user.name}</div>
                            {user.username && <div style={{ fontSize: 12, color: 'var(--color-navy)', fontWeight: 600, marginTop: 1 }}>@{user.username}</div>}
                            <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>{user.email}</div>
                            <div style={{ marginTop: 8 }}>
                                <span style={{ display: 'inline-flex', alignItems: 'center', fontSize: 11, fontWeight: 600, padding: '2px 8px', borderRadius: 20, ...(patient.nin_verified ? { background: 'var(--color-green-lt)', color: 'var(--color-green)' } : { background: 'var(--color-border)', color: 'var(--color-text-2)' }) }}>
                                    {patient.nin_verified ? '✓ NIN Verified' : 'NIN unverified'}
                                </span>
                            </div>
                            <form method="POST" action="/profile/avatar" encType="multipart/form-data" style={{ marginTop: 12 }}>
                                <input type="hidden" name="_token" value={document.querySelector('meta[name=csrf-token]')?.content} />
                                <label style={{ cursor: 'pointer', display: 'inline-block', fontSize: 12, color: 'var(--color-navy)', fontWeight: 600, background: 'var(--color-navy-lt)', padding: '6px 14px', borderRadius: 20, border: '1.5px solid var(--color-navy)' }}>
                                    📷 Change photo
                                    <input type="file" name="avatar" accept="image/*" style={{ display: 'none' }} onChange={e => e.target.form.submit()} />
                                </label>
                            </form>
                        </div>

                        {/* Profile completeness */}
                        <div style={{ background: 'var(--color-border)', borderRadius: 10, padding: 14, marginBottom: 14 }}>
                            <div style={{ display: 'flex', justifyContent: 'space-between', fontSize: 12, fontWeight: 600, marginBottom: 7 }}>
                                <span style={{ color: 'var(--color-text-2)' }}>Profile completeness</span>
                                <span style={{ color: 'var(--color-navy)' }}>{pct}%</span>
                            </div>
                            <div style={{ height: 6, background: 'rgba(0,0,0,0.1)', borderRadius: 6, overflow: 'hidden' }}>
                                <div style={{ height: '100%', width: `${pct}%`, background: pct >= 80 ? 'var(--color-green)' : pct >= 50 ? 'var(--color-amber)' : 'var(--color-red)', borderRadius: 6, transition: 'width 0.5s' }} />
                            </div>
                            <div style={{ marginTop: 10, display: 'flex', flexDirection: 'column', gap: 4 }}>
                                {Object.entries(fields).map(([label, val]) => (
                                    <div key={label} style={{ fontSize: 11, color: val ? 'var(--color-green)' : 'var(--color-text-3)' }}>
                                        {val ? '✓' : '○'} {label}
                                    </div>
                                ))}
                            </div>
                        </div>

                        <nav style={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
                            {navSections.map(s => (
                                <a key={s.id} href={`#${s.id}`} onClick={() => setActiveSection(s.id)} style={{ display: 'flex', alignItems: 'center', gap: 8, padding: '9px 12px', borderRadius: 8, fontSize: 13, fontWeight: activeSection === s.id ? 700 : 500, color: activeSection === s.id ? 'var(--color-navy)' : 'var(--color-text-2)', background: activeSection === s.id ? 'var(--color-navy-lt)' : 'transparent', textDecoration: 'none' }}>
                                    {s.icon} {s.label}
                                </a>
                            ))}
                        </nav>
                    </div>
                </div>

                {/* Forms */}
                <div>
                    {/* Personal */}
                    <div id="personal" style={cardStyle}>
                        <div style={cardHeaderStyle}>
                            <div style={{ fontWeight: 700, fontSize: 14 }}>Personal Information</div>
                            <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>Your account details and contact information</div>
                        </div>
                        <div style={{ padding: 20 }}>
                            <form onSubmit={submitPersonal}>
                                <div style={gridStyle}>
                                    <FormField label="Full name *" error={personal.errors.name} span2>
                                        <input style={inputStyle()} type="text" value={personal.data.name} onChange={e => personal.setData('name', e.target.value)} required placeholder="Adaeze Okonkwo" />
                                    </FormField>
                                    <FormField label="Username" error={personal.errors.username}>
                                        <div style={{ position: 'relative', display: 'flex', alignItems: 'center' }}>
                                            <span style={{ position: 'absolute', left: 10, color: 'var(--color-text-3)', fontSize: 13, pointerEvents: 'none' }}>@</span>
                                            <input style={{ ...inputStyle(), paddingLeft: 24 }} type="text" value={personal.data.username} onChange={e => personal.setData('username', e.target.value.toLowerCase().replace(/[^a-z0-9_]/g, ''))} placeholder="adaeze_okonkwo" maxLength={30} autoComplete="username" />
                                        </div>
                                        <div style={{ fontSize: 11, color: 'var(--color-text-3)', marginTop: 4 }}>Used for login — 3–30 chars, letters, numbers and underscores only</div>
                                    </FormField>
                                    <FormField label="Email address *" error={personal.errors.email}>
                                        <input style={inputStyle()} type="email" value={personal.data.email} onChange={e => personal.setData('email', e.target.value)} required />
                                    </FormField>
                                    <FormField label="Phone number *" error={personal.errors.phone}>
                                        <input style={inputStyle()} type="tel" value={personal.data.phone} onChange={e => personal.setData('phone', e.target.value)} required placeholder="+2348012345678" />
                                    </FormField>
                                    <FormField label="Date of birth" error={personal.errors.dob}>
                                        <input style={inputStyle()} type="date" value={personal.data.dob} onChange={e => personal.setData('dob', e.target.value)} max={new Date(Date.now() - 86400000).toISOString().split('T')[0]} />
                                    </FormField>
                                    <FormField label="Gender" error={personal.errors.gender}>
                                        <select style={selectStyle()} value={personal.data.gender} onChange={e => personal.setData('gender', e.target.value)}>
                                            <option value="">Select…</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                            <option value="other">Other / prefer not to say</option>
                                        </select>
                                    </FormField>
                                    <FormField label="State of residence" error={personal.errors.state}>
                                        <select style={selectStyle()} value={personal.data.state} onChange={e => personal.setData('state', e.target.value)}>
                                            <option value="">Select state…</option>
                                            {STATES.map(s => <option key={s} value={s}>{s}</option>)}
                                        </select>
                                    </FormField>
                                    <FormField label="City" error={personal.errors.city}>
                                        <input style={inputStyle()} type="text" value={personal.data.city} onChange={e => personal.setData('city', e.target.value)} placeholder="Lagos Island" />
                                    </FormField>
                                    <FormField label="Address" span2>
                                        <input style={inputStyle()} type="text" value={personal.data.address} onChange={e => personal.setData('address', e.target.value)} placeholder="14 Broad Street, Lagos Island" />
                                    </FormField>
                                </div>
                                <div style={{ marginTop: 20, paddingTop: 16, borderTop: '1px solid var(--color-border)' }}>
                                    <button type="submit" disabled={personal.processing} style={{ background: 'var(--color-navy)', color: '#fff', border: 'none', borderRadius: 8, padding: '9px 20px', fontSize: 13, fontWeight: 600, cursor: 'pointer' }}>
                                        {personal.processing ? 'Saving…' : 'Save personal info'}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {/* Medical */}
                    <div id="medical" style={cardStyle}>
                        <div style={cardHeaderStyle}>
                            <div style={{ fontWeight: 700, fontSize: 14 }}>Medical Profile</div>
                            <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>Clinical information shown to providers during consultations</div>
                        </div>
                        <div style={{ padding: 20 }}>
                            <form onSubmit={submitMedical}>
                                <div style={gridStyle}>
                                    <FormField label="Blood type" error={medical.errors.blood_type}>
                                        <select style={selectStyle()} value={medical.data.blood_type} onChange={e => medical.setData('blood_type', e.target.value)}>
                                            <option value="">Unknown</option>
                                            {['A+','A-','B+','B-','AB+','AB-','O+','O-'].map(bt => <option key={bt} value={bt}>{bt}</option>)}
                                        </select>
                                    </FormField>
                                    <FormField label="Genotype" error={medical.errors.genotype}>
                                        <select style={selectStyle()} value={medical.data.genotype} onChange={e => medical.setData('genotype', e.target.value)}>
                                            <option value="">Unknown</option>
                                            {['AA','AS','SS','AC','SC'].map(g => <option key={g} value={g}>{g}</option>)}
                                        </select>
                                    </FormField>
                                </div>
                                <div style={{ marginTop: 16, display: 'flex', gap: 8, padding: '10px 14px', background: 'var(--color-navy-lt)', borderRadius: 8, fontSize: 12, color: 'var(--color-navy)', alignItems: 'center' }}>
                                    <span>ℹ</span>
                                    <span>Blood type and genotype are displayed to providers and pharmacists when they access your record with your consent.</span>
                                </div>
                                <div style={{ marginTop: 16 }}>
                                    <button type="submit" disabled={medical.processing} style={{ background: 'var(--color-navy)', color: '#fff', border: 'none', borderRadius: 8, padding: '9px 20px', fontSize: 13, fontWeight: 600, cursor: 'pointer' }}>
                                        {medical.processing ? 'Saving…' : 'Save medical profile'}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {/* Emergency Contact */}
                    <div id="emergency" style={cardStyle}>
                        <div style={cardHeaderStyle}>
                            <div style={{ fontWeight: 700, fontSize: 14 }}>Emergency Contact</div>
                            <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>Person to notify in a medical emergency</div>
                        </div>
                        <div style={{ padding: 20 }}>
                            <form onSubmit={submitEmergency}>
                                <div style={gridStyle}>
                                    <FormField label="Contact name" error={emergency.errors.emergency_contact_name}>
                                        <input style={inputStyle()} type="text" value={emergency.data.emergency_contact_name} onChange={e => emergency.setData('emergency_contact_name', e.target.value)} placeholder="Chidi Okonkwo" />
                                    </FormField>
                                    <FormField label="Contact phone" error={emergency.errors.emergency_contact_phone}>
                                        <input style={inputStyle()} type="text" value={emergency.data.emergency_contact_phone} onChange={e => emergency.setData('emergency_contact_phone', e.target.value)} placeholder="08012345678" />
                                    </FormField>
                                </div>
                                <div style={{ marginTop: 16 }}>
                                    <button type="submit" disabled={emergency.processing} style={{ background: 'var(--color-navy)', color: '#fff', border: 'none', borderRadius: 8, padding: '9px 20px', fontSize: 13, fontWeight: 600, cursor: 'pointer' }}>
                                        {emergency.processing ? 'Saving…' : 'Save emergency contact'}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {/* Password */}
                    <div id="security" style={cardStyle}>
                        <div style={cardHeaderStyle}>
                            <div style={{ fontWeight: 700, fontSize: 14 }}>Password & Security</div>
                            <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>Change your account password</div>
                        </div>
                        <div style={{ padding: 20 }}>
                            <form onSubmit={submitPassword}>
                                <div style={gridStyle}>
                                    <FormField label="Current password *" error={password.errors.current_password} span2>
                                        <input style={inputStyle()} type="password" value={password.data.current_password} onChange={e => password.setData('current_password', e.target.value)} required autoComplete="current-password" />
                                    </FormField>
                                    <FormField label="New password *" error={password.errors.password}>
                                        <input style={inputStyle()} type="password" value={password.data.password} onChange={e => password.setData('password', e.target.value)} required minLength={8} autoComplete="new-password" placeholder="Min 8 characters" />
                                    </FormField>
                                    <FormField label="Confirm new password *">
                                        <input style={inputStyle()} type="password" value={password.data.password_confirmation} onChange={e => password.setData('password_confirmation', e.target.value)} required autoComplete="new-password" placeholder="Repeat new password" />
                                    </FormField>
                                </div>
                                <div style={{ marginTop: 16, display: 'flex', gap: 8, padding: '10px 14px', background: 'var(--color-amber-lt)', borderRadius: 8, fontSize: 12, color: 'var(--color-amber)', alignItems: 'center' }}>
                                    <span>⚠</span>
                                    <span>Use at least 8 characters. Choose a mix of letters, numbers, and symbols for a strong password.</span>
                                </div>
                                <div style={{ marginTop: 16 }}>
                                    <button type="submit" disabled={password.processing} style={{ background: 'var(--color-navy)', color: '#fff', border: 'none', borderRadius: 8, padding: '9px 20px', fontSize: 13, fontWeight: 600, cursor: 'pointer' }}>
                                        {password.processing ? 'Changing…' : 'Change password'}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {/* Two-Factor */}
                    <div id="two-factor" style={cardStyle}>
                        <div style={{ ...cardHeaderStyle, display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                            <div>
                                <div style={{ fontWeight: 700, fontSize: 14 }}>Two-Factor Authentication</div>
                                <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>Add an extra layer of security with SMS OTP</div>
                            </div>
                            <span style={{ display: 'inline-flex', alignItems: 'center', fontSize: 11, fontWeight: 600, padding: '2px 8px', borderRadius: 20, ...(user.two_factor_enabled ? { background: 'var(--color-green-lt)', color: 'var(--color-green)' } : { background: 'var(--color-border)', color: 'var(--color-text-2)' }) }}>
                                {user.two_factor_enabled ? '✓ Enabled' : 'Disabled'}
                            </span>
                        </div>
                        <div style={{ padding: 20 }}>
                            {user.two_factor_enabled ? (
                                <>
                                    <div style={{ display: 'flex', gap: 8, padding: '10px 14px', background: 'var(--color-green-lt)', borderRadius: 8, fontSize: 12, color: 'var(--color-green)', alignItems: 'center', marginBottom: 16 }}>
                                        <span>✓</span>
                                        <span>Two-factor authentication is active. You will receive a 6-digit OTP via SMS each time you log in.</span>
                                    </div>
                                    <button onClick={() => { if (confirm('Disable two-factor authentication?')) toggle2fa('disable'); }} style={{ background: 'var(--color-red)', color: '#fff', border: 'none', borderRadius: 8, padding: '9px 20px', fontSize: 13, fontWeight: 600, cursor: 'pointer' }}>
                                        Disable 2FA
                                    </button>
                                </>
                            ) : (
                                <>
                                    <div style={{ display: 'flex', gap: 8, padding: '10px 14px', background: 'var(--color-amber-lt)', borderRadius: 8, fontSize: 12, color: 'var(--color-amber)', alignItems: 'center', marginBottom: 16 }}>
                                        <span>⚠</span>
                                        <span>2FA is currently disabled. Enable it to receive an OTP on your phone number each time you sign in.</span>
                                    </div>
                                    {user.phone ? (
                                        <>
                                            <p style={{ fontSize: 13, color: 'var(--color-text-2)', marginBottom: 16 }}>
                                                OTPs will be sent to <strong>{user.phone}</strong>.{' '}
                                                <a href="#personal" style={{ color: 'var(--color-navy)' }}>Change phone number</a>
                                            </p>
                                            <button onClick={() => toggle2fa('enable')} style={{ background: 'var(--color-navy)', color: '#fff', border: 'none', borderRadius: 8, padding: '9px 20px', fontSize: 13, fontWeight: 600, cursor: 'pointer' }}>
                                                Enable 2FA
                                            </button>
                                        </>
                                    ) : (
                                        <div style={{ display: 'flex', gap: 8, padding: '10px 14px', background: 'var(--color-red-lt)', borderRadius: 8, fontSize: 12, color: 'var(--color-red)', alignItems: 'center' }}>
                                            <span>✗</span>
                                            <span>You need to add a phone number before enabling 2FA. Update your personal info above.</span>
                                        </div>
                                    )}
                                </>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
