import { useState } from 'react';
import { Link, useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

const inputStyle = { width: '100%', background: 'var(--color-card)', border: '1.5px solid var(--color-border)', borderRadius: 8, padding: '9px 12px', fontSize: 13, color: 'var(--color-text)', fontFamily: 'inherit', outline: 'none', boxSizing: 'border-box' };

export default function Create({ facilities }) {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        email: '',
        phone: '',
        role: '',
        password: '',
        password_confirmation: '',
        facility_id: '',
        specialty: '',
        license_no: '',
    });

    const showProviderFields = data.role === 'provider' || data.role === 'pharmacist';

    function submit(e) {
        e.preventDefault();
        post('/admin/users');
    }

    function Field({ label, error, children, span2 }) {
        return (
            <div style={{ gridColumn: span2 ? 'span 2' : undefined }}>
                <label style={{ display: 'block', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.06em', color: 'var(--color-text-2)', marginBottom: 5 }}>{label}</label>
                {children}
                {error && <div style={{ color: 'var(--color-red)', fontSize: 12, marginTop: 4 }}>{error}</div>}
            </div>
        );
    }

    return (
        <AppLayout title="Create User">
            <div style={{ display: 'flex', alignItems: 'flex-start', justifyContent: 'space-between', marginBottom: 24, gap: 12 }}>
                <div>
                    <h1 style={{ fontSize: 22, fontWeight: 800, color: 'var(--color-navy)', margin: 0 }}>Create New User</h1>
                    <p style={{ fontSize: 13, color: 'var(--color-text-2)', marginTop: 4 }}>Add a new account to the platform.</p>
                </div>
                <Link href="/admin/users" style={{ padding: '8px 16px', borderRadius: 8, border: '1.5px solid var(--color-border)', background: 'var(--color-card)', color: 'var(--color-text-2)', fontSize: 13, fontWeight: 600, textDecoration: 'none' }}>← Back to Users</Link>
            </div>

            <div style={{ maxWidth: 680 }}>
                <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 14, overflow: 'hidden' }}>
                    <div style={{ padding: '16px 20px 12px', borderBottom: '1px solid var(--color-border)' }}>
                        <div style={{ fontWeight: 700, fontSize: 14, color: 'var(--color-navy)' }}>Account Details</div>
                        <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>Fill in the information below to create the account.</div>
                    </div>
                    <div style={{ padding: '20px' }}>
                        <form onSubmit={submit}>
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16, marginBottom: 16 }}>
                                <Field label="Full Name *" error={errors.name} span2>
                                    <input type="text" value={data.name} onChange={e => setData('name', e.target.value)} required placeholder="e.g. Amaka Okonkwo" style={inputStyle} />
                                </Field>
                                <Field label="Email Address *" error={errors.email}>
                                    <input type="email" value={data.email} onChange={e => setData('email', e.target.value)} required placeholder="user@example.com" style={inputStyle} />
                                </Field>
                                <Field label="Phone Number *" error={errors.phone}>
                                    <input type="tel" value={data.phone} onChange={e => setData('phone', e.target.value)} required placeholder="08012345678" style={inputStyle} />
                                </Field>
                                <Field label="Role *" error={errors.role}>
                                    <select value={data.role} onChange={e => setData('role', e.target.value)} required style={inputStyle}>
                                        <option value="">— Select role —</option>
                                        <option value="patient">Patient</option>
                                        <option value="provider">Provider (Doctor/Nurse)</option>
                                        <option value="pharmacist">Pharmacist</option>
                                        <option value="facility_admin">Facility Admin</option>
                                        <option value="super_admin">Super Admin</option>
                                    </select>
                                </Field>
                                <Field label="Password *" error={errors.password}>
                                    <input type="password" value={data.password} onChange={e => setData('password', e.target.value)} required placeholder="Min. 8 characters" style={inputStyle} />
                                </Field>
                                <Field label="Confirm Password *" error={errors.password_confirmation} span2>
                                    <input type="password" value={data.password_confirmation} onChange={e => setData('password_confirmation', e.target.value)} required placeholder="Repeat password" style={inputStyle} />
                                </Field>
                            </div>

                            {/* Provider / Pharmacist fields */}
                            {showProviderFields && (
                                <div style={{ marginBottom: 16 }}>
                                    <div style={{ borderTop: '1px solid var(--color-border)', margin: '16px 0 14px' }} />
                                    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16 }}>
                                        <div style={{ gridColumn: 'span 2' }}>
                                            <label style={{ display: 'block', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.06em', color: 'var(--color-text-2)', marginBottom: 5 }}>Facility *</label>
                                            <select value={data.facility_id} onChange={e => setData('facility_id', e.target.value)} style={inputStyle}>
                                                <option value="">— Select facility —</option>
                                                {facilities.map(f => <option key={f.id} value={f.id}>{f.name}</option>)}
                                            </select>
                                            {errors.facility_id && <div style={{ color: 'var(--color-red)', fontSize: 12, marginTop: 4 }}>{errors.facility_id}</div>}
                                        </div>
                                        <div>
                                            <label style={{ display: 'block', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.06em', color: 'var(--color-text-2)', marginBottom: 5 }}>Specialty *</label>
                                            <input type="text" value={data.specialty} onChange={e => setData('specialty', e.target.value)} placeholder="e.g. General Practice, Pharmacy" style={inputStyle} />
                                            {errors.specialty && <div style={{ color: 'var(--color-red)', fontSize: 12, marginTop: 4 }}>{errors.specialty}</div>}
                                        </div>
                                        <div>
                                            <label style={{ display: 'block', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.06em', color: 'var(--color-text-2)', marginBottom: 5 }}>Licence / MDCN No.</label>
                                            <input type="text" value={data.license_no} onChange={e => setData('license_no', e.target.value)} placeholder="Optional" style={inputStyle} />
                                        </div>
                                    </div>
                                </div>
                            )}

                            <div style={{ background: 'var(--color-navy-lt)', color: 'var(--color-navy)', borderLeft: '3px solid var(--color-navy)', borderRadius: 8, padding: '10px 14px', fontSize: 12, marginBottom: 20 }}>
                                ℹ️ <strong>Patient</strong>: MedID auto-generated. &nbsp;|&nbsp; <strong>Provider / Pharmacist</strong>: facility and specialty required.
                            </div>

                            <div style={{ display: 'flex', gap: 10 }}>
                                <button type="submit" disabled={processing} style={{ padding: '10px 22px', borderRadius: 9, background: processing ? '#a0aec0' : 'var(--color-navy)', color: '#fff', border: 'none', fontSize: 13, fontWeight: 700, cursor: processing ? 'not-allowed' : 'pointer', fontFamily: 'inherit' }}>
                                    {processing ? 'Creating…' : 'Create User'}
                                </button>
                                <Link href="/admin/users" style={{ padding: '10px 22px', borderRadius: 9, border: '1.5px solid var(--color-border)', background: 'var(--color-card)', color: 'var(--color-text-2)', fontSize: 13, fontWeight: 600, textDecoration: 'none' }}>Cancel</Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
