import { useForm, Link } from '@inertiajs/react';
import AuthLayout from '@/layouts/AuthLayout';

export default function ResetPassword({ token, email }) {
    const { data, setData, post, processing, errors } = useForm({
        token,
        email: email ?? '',
        password: '',
        password_confirmation: '',
    });

    function submit(e) {
        e.preventDefault();
        post('/reset-password');
    }

    const inp = { width: '100%', background: '#fff', border: '1.5px solid #E2E8F0', borderRadius: 9, padding: '10px 14px', fontSize: 14, color: '#0F172A', fontFamily: 'inherit', outline: 'none', boxSizing: 'border-box' };

    return (
        <AuthLayout title="Set New Password">
            <div style={{ marginBottom: 28 }}>
                <h2 style={{ fontSize: 24, fontWeight: 800, color: '#0F172A', letterSpacing: '-0.02em' }}>Set new password</h2>
                <p style={{ fontSize: 14, color: '#64748B', marginTop: 5 }}>Choose a strong password for your account.</p>
            </div>

            {(errors.email || errors.password || errors.token) && (
                <div style={{ background: '#FBEAEC', color: '#7A1F2C', borderLeft: '3px solid #B23A48', borderRadius: 9, padding: '11px 14px', fontSize: 13, marginBottom: 18 }}>
                    {errors.email || errors.password || errors.token}
                </div>
            )}

            <form onSubmit={submit}>
                <div style={{ marginBottom: 16 }}>
                    <label style={{ display: 'block', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.06em', color: '#475569', marginBottom: 6 }}>Email address</label>
                    <input type="email" value={data.email} disabled style={{ ...inp, background: '#F1F5F9', color: '#64748B' }} />
                </div>
                <div style={{ marginBottom: 16 }}>
                    <label style={{ display: 'block', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.06em', color: '#475569', marginBottom: 6 }}>New password</label>
                    <input type="password" value={data.password} onChange={e => setData('password', e.target.value)} placeholder="Min. 8 characters" autoComplete="new-password" required style={inp} />
                </div>
                <div style={{ marginBottom: 16 }}>
                    <label style={{ display: 'block', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.06em', color: '#475569', marginBottom: 6 }}>Confirm new password</label>
                    <input type="password" value={data.password_confirmation} onChange={e => setData('password_confirmation', e.target.value)} placeholder="Repeat password" autoComplete="new-password" required style={inp} />
                </div>
                <button type="submit" disabled={processing} style={{ width: '100%', background: processing ? '#a0aec0' : '#1E3A5F', color: '#fff', border: 'none', borderRadius: 10, padding: '13px 16px', fontSize: 15, fontWeight: 700, cursor: processing ? 'not-allowed' : 'pointer', marginTop: 6, fontFamily: 'inherit' }}>
                    {processing ? 'Resetting…' : 'Reset password'}
                </button>
            </form>

            <div style={{ textAlign: 'center', marginTop: 22, fontSize: 13, color: '#64748B' }}>
                <Link href="/login" style={{ color: '#1E3A5F', fontWeight: 700, textDecoration: 'none' }}>← Back to sign in</Link>
            </div>
        </AuthLayout>
    );
}
