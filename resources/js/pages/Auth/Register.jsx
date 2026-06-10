import { useState } from 'react';
import { useForm, Link } from '@inertiajs/react';
import AuthLayout from '@/layouts/AuthLayout';

const STEPS = ['Account', 'Health profile', 'Review'];

const inp = {
    width: '100%', background: '#fff', border: '1.5px solid #E2E8F0',
    borderRadius: 9, padding: '10px 14px', fontSize: 14, color: '#0F172A',
    fontFamily: 'inherit', outline: 'none', boxSizing: 'border-box',
};
const sel = { ...inp };
const lbl = { display: 'block', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.06em', color: '#475569', marginBottom: 6 };
const field = { marginBottom: 16 };
const hint = { fontSize: 11, color: '#94A3B8', marginTop: 4 };

function pwdStrength(p) {
    let s = 0;
    if (p.length >= 8) s++;
    if (/[A-Z]/.test(p)) s++;
    if (/[0-9]/.test(p)) s++;
    if (/[^A-Za-z0-9]/.test(p)) s++;
    return s;
}

export default function Register() {
    const [step, setStep] = useState(1);
    const [showPwd, setShowPwd] = useState(false);
    const [agreed, setAgreed] = useState(false);

    const { data, setData, post, processing, errors } = useForm({
        name: '', username: '', email: '', phone: '', password: '', password_confirmation: '',
        dob: '', gender: '', blood_type: '', genotype: '', state: '', nin: '',
    });

    const strength = pwdStrength(data.password);
    const strengthColor = ['#E2E8F0', '#B23A48', '#B8860B', '#2C5F2D', '#1E3A5F'][strength];
    const strengthLabel = ['Enter a password', 'Weak', 'Fair', 'Good', 'Strong'][strength];

    function submit(e) {
        e.preventDefault();
        post('/register');
    }

    const anyErrors = Object.keys(errors).length > 0;

    return (
        <AuthLayout title="Create Account">
            {/* Progress bar */}
            <div style={{ display: 'flex', alignItems: 'center', marginBottom: 28 }}>
                {STEPS.map((s, i) => (
                    <>
                        <div key={s} style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', gap: 4 }}>
                            <div style={{ width: 30, height: 30, borderRadius: '50%', background: i + 1 < step ? '#2C5F2D' : i + 1 === step ? '#1E3A5F' : '#E2E8F0', color: i + 1 <= step ? '#fff' : '#94A3B8', fontSize: 12, fontWeight: 700, display: 'flex', alignItems: 'center', justifyContent: 'center', transition: 'all 0.2s' }}>
                                {i + 1 < step ? '✓' : i + 1}
                            </div>
                            <div style={{ fontSize: 10, fontWeight: 600, color: i + 1 === step ? '#1E3A5F' : '#94A3B8', textTransform: 'uppercase', letterSpacing: '0.04em', whiteSpace: 'nowrap' }}>{s}</div>
                        </div>
                        {i < STEPS.length - 1 && (
                            <div key={`l${i}`} style={{ flex: 1, height: 2, background: i + 1 < step ? '#2C5F2D' : '#E2E8F0', minWidth: 24, margin: '0 6px', marginBottom: 20, transition: 'background 0.2s' }} />
                        )}
                    </>
                ))}
            </div>

            {anyErrors && (
                <div style={{ background: '#FBEAEC', color: '#7A1F2C', borderLeft: '3px solid #B23A48', borderRadius: 9, padding: '11px 14px', fontSize: 13, marginBottom: 18 }}>
                    {Object.values(errors).map((e, i) => <div key={i}>{e}</div>)}
                </div>
            )}

            <form onSubmit={submit}>
                {/* Step 1 */}
                {step === 1 && (
                    <div>
                        <h2 style={{ fontSize: 22, fontWeight: 800, color: '#0F172A', marginBottom: 4 }}>Create your account</h2>
                        <p style={{ fontSize: 13, color: '#64748B', marginBottom: 20 }}>Your secure health record, free forever</p>

                        <div style={field}>
                            <label style={lbl}>Full name <span style={{ color: '#B23A48' }}>*</span></label>
                            <input type="text" value={data.name} onChange={e => setData('name', e.target.value)} placeholder="Adaeze Okonkwo" autoComplete="name" required style={inp} />
                            <div style={hint}>As it appears on government-issued ID</div>
                        </div>
                        <div style={field}>
                            <label style={lbl}>Username</label>
                            <div style={{ position: 'relative', display: 'flex', alignItems: 'center' }}>
                                <span style={{ position: 'absolute', left: 12, color: '#94A3B8', fontSize: 14, pointerEvents: 'none' }}>@</span>
                                <input type="text" value={data.username} onChange={e => setData('username', e.target.value.toLowerCase().replace(/[^a-z0-9_]/g, ''))} placeholder="adaeze_okonkwo" autoComplete="username" maxLength={30} style={{ ...inp, paddingLeft: 26 }} />
                            </div>
                            <div style={hint}>Optional — 3–30 chars, letters, numbers and underscores. Auto-generated if blank.</div>
                            {errors.username && <div style={{ ...hint, color: '#B23A48' }}>{errors.username}</div>}
                        </div>
                        <div style={field}>
                            <label style={lbl}>Email address <span style={{ color: '#B23A48' }}>*</span></label>
                            <input type="email" value={data.email} onChange={e => setData('email', e.target.value)} placeholder="adaeze@gmail.com" autoComplete="email" required style={inp} />
                        </div>
                        <div style={field}>
                            <label style={lbl}>Phone number <span style={{ color: '#B23A48' }}>*</span></label>
                            <input type="tel" value={data.phone} onChange={e => setData('phone', e.target.value)} placeholder="+2348012345678" autoComplete="tel" required style={inp} />
                            <div style={hint}>Include country code, e.g. +2348012345678</div>
                        </div>
                        <div style={field}>
                            <label style={lbl}>Password <span style={{ color: '#B23A48' }}>*</span></label>
                            <div style={{ position: 'relative', display: 'flex', alignItems: 'center' }}>
                                <input type={showPwd ? 'text' : 'password'} value={data.password} onChange={e => setData('password', e.target.value)} placeholder="Minimum 8 characters" autoComplete="new-password" minLength={8} required style={{ ...inp, paddingRight: 42 }} />
                                <button type="button" onClick={() => setShowPwd(p => !p)} style={{ position: 'absolute', right: 12, background: 'none', border: 'none', cursor: 'pointer', fontSize: 16, color: '#94A3B8' }}>{showPwd ? '🙈' : '👁'}</button>
                            </div>
                            <div style={{ height: 3, background: '#E2E8F0', borderRadius: 3, marginTop: 6, overflow: 'hidden' }}>
                                <div style={{ height: '100%', width: `${strength * 25}%`, background: strengthColor, transition: 'width 0.3s, background 0.3s', borderRadius: 3 }} />
                            </div>
                            <div style={hint}>{strengthLabel}</div>
                        </div>
                        <div style={field}>
                            <label style={lbl}>Confirm password <span style={{ color: '#B23A48' }}>*</span></label>
                            <input type="password" value={data.password_confirmation} onChange={e => setData('password_confirmation', e.target.value)} placeholder="Repeat your password" autoComplete="new-password" required style={inp} />
                            {data.password_confirmation && (
                                <div style={{ ...hint, color: data.password === data.password_confirmation ? '#2C5F2D' : '#B23A48' }}>
                                    {data.password === data.password_confirmation ? '✓ Passwords match' : '✗ Passwords do not match'}
                                </div>
                            )}
                        </div>
                        <button type="button" onClick={() => setStep(2)} style={{ width: '100%', background: '#1E3A5F', color: '#fff', border: 'none', borderRadius: 10, padding: '13px 16px', fontSize: 15, fontWeight: 700, cursor: 'pointer', fontFamily: 'inherit' }}>
                            Continue →
                        </button>
                    </div>
                )}

                {/* Step 2 */}
                {step === 2 && (
                    <div>
                        <h2 style={{ fontSize: 22, fontWeight: 800, color: '#0F172A', marginBottom: 4 }}>Health profile</h2>
                        <p style={{ fontSize: 13, color: '#64748B', marginBottom: 20 }}>Optional details help providers give better care</p>

                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12, marginBottom: 16 }}>
                            <div>
                                <label style={lbl}>Date of birth</label>
                                <input type="date" value={data.dob} onChange={e => setData('dob', e.target.value)} style={inp} />
                            </div>
                            <div>
                                <label style={lbl}>Gender</label>
                                <select value={data.gender} onChange={e => setData('gender', e.target.value)} style={sel}>
                                    <option value="">— Select —</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label style={lbl}>Blood type</label>
                                <select value={data.blood_type} onChange={e => setData('blood_type', e.target.value)} style={sel}>
                                    <option value="">— Select —</option>
                                    {['A+','A-','B+','B-','AB+','AB-','O+','O-'].map(t => <option key={t} value={t}>{t}</option>)}
                                </select>
                            </div>
                            <div>
                                <label style={lbl}>Genotype</label>
                                <select value={data.genotype} onChange={e => setData('genotype', e.target.value)} style={sel}>
                                    <option value="">— Select —</option>
                                    {['AA','AS','SS','AC','SC'].map(g => <option key={g} value={g}>{g}</option>)}
                                </select>
                            </div>
                        </div>
                        <div style={field}>
                            <label style={lbl}>State of residence</label>
                            <input type="text" value={data.state} onChange={e => setData('state', e.target.value)} placeholder="e.g. Lagos" style={inp} />
                        </div>
                        <div style={field}>
                            <label style={lbl}>NIN (National Identification Number)</label>
                            <input type="text" value={data.nin} onChange={e => setData('nin', e.target.value)} placeholder="11-digit NIN" maxLength={11} style={inp} />
                            <div style={hint}>Optional — used for identity verification only</div>
                        </div>
                        <div style={{ display: 'flex', gap: 10 }}>
                            <button type="button" onClick={() => setStep(1)} style={{ flex: '0 0 auto', background: '#F1F5F9', color: '#334155', border: '1.5px solid #E2E8F0', borderRadius: 10, padding: '12px 18px', fontSize: 14, fontWeight: 600, cursor: 'pointer', fontFamily: 'inherit' }}>← Back</button>
                            <button type="button" onClick={() => setStep(3)} style={{ flex: 1, background: '#1E3A5F', color: '#fff', border: 'none', borderRadius: 10, padding: '13px 16px', fontSize: 15, fontWeight: 700, cursor: 'pointer', fontFamily: 'inherit' }}>Review →</button>
                        </div>
                    </div>
                )}

                {/* Step 3 */}
                {step === 3 && (
                    <div>
                        <h2 style={{ fontSize: 22, fontWeight: 800, color: '#0F172A', marginBottom: 4 }}>Review</h2>
                        <p style={{ fontSize: 13, color: '#64748B', marginBottom: 20 }}>Confirm your details before creating your account</p>

                        <div style={{ border: '1.5px solid #E2E8F0', borderRadius: 12, overflow: 'hidden', marginBottom: 16 }}>
                            <div style={{ background: '#F8FAFC', padding: '8px 16px', fontSize: 10, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.06em', color: '#64748B' }}>Account Details</div>
                            {[['Name', data.name],['Username', data.username ? '@' + data.username : '(auto-generated)'],['Email', data.email],['Phone', data.phone]].map(([k,v]) => (
                                <div key={k} style={{ display: 'flex', justifyContent: 'space-between', padding: '8px 16px', fontSize: 13, borderTop: '1px solid #F1F5F9' }}>
                                    <span style={{ color: '#64748B' }}>{k}</span><strong>{v}</strong>
                                </div>
                            ))}
                        </div>
                        {(data.dob || data.gender || data.blood_type || data.genotype || data.state) && (
                            <div style={{ border: '1.5px solid #E2E8F0', borderRadius: 12, overflow: 'hidden', marginBottom: 16 }}>
                                <div style={{ background: '#F8FAFC', padding: '8px 16px', fontSize: 10, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.06em', color: '#64748B' }}>Health Profile</div>
                                {[['DOB', data.dob],['Gender', data.gender],['Blood type', data.blood_type],['Genotype', data.genotype],['State', data.state]].filter(([,v]) => v).map(([k,v]) => (
                                    <div key={k} style={{ display: 'flex', justifyContent: 'space-between', padding: '8px 16px', fontSize: 13, borderTop: '1px solid #F1F5F9' }}>
                                        <span style={{ color: '#64748B' }}>{k}</span><strong>{v}</strong>
                                    </div>
                                ))}
                            </div>
                        )}

                        <label style={{ display: 'flex', alignItems: 'flex-start', gap: 8, fontSize: 12, color: '#64748B', lineHeight: 1.55, cursor: 'pointer', marginBottom: 20 }}>
                            <input type="checkbox" checked={agreed} onChange={e => setAgreed(e.target.checked)} style={{ marginTop: 2, accentColor: '#1E3A5F', flexShrink: 0 }} />
                            I agree to the Terms of Service and Privacy Policy. I consent to MedRecords storing my health information securely.
                        </label>

                        <div style={{ display: 'flex', gap: 10 }}>
                            <button type="button" onClick={() => setStep(2)} style={{ flex: '0 0 auto', background: '#F1F5F9', color: '#334155', border: '1.5px solid #E2E8F0', borderRadius: 10, padding: '12px 18px', fontSize: 14, fontWeight: 600, cursor: 'pointer', fontFamily: 'inherit' }}>← Back</button>
                            <button type="submit" disabled={!agreed || processing} style={{ flex: 1, background: (!agreed || processing) ? '#a0aec0' : '#1E3A5F', color: '#fff', border: 'none', borderRadius: 10, padding: '13px 16px', fontSize: 15, fontWeight: 700, cursor: (!agreed || processing) ? 'not-allowed' : 'pointer', fontFamily: 'inherit' }}>
                                {processing ? 'Creating account…' : 'Create account'}
                            </button>
                        </div>
                    </div>
                )}
            </form>

            <div style={{ textAlign: 'center', marginTop: 22, fontSize: 13, color: '#64748B' }}>
                Already have an account? <Link href="/login" style={{ color: '#1E3A5F', fontWeight: 700, textDecoration: 'none' }}>Sign in</Link>
            </div>
        </AuthLayout>
    );
}
