import { useForm, Link, usePage } from '@inertiajs/react';
import AuthLayout from '@/layouts/AuthLayout';

export default function ForgotPassword() {
    const { flash } = usePage().props;
    const { data, setData, post, processing, errors } = useForm({ email: '' });

    function submit(e) {
        e.preventDefault();
        post('/forgot-password');
    }

    return (
        <AuthLayout title="Forgot Password">
            <div style={{ marginBottom: 28 }}>
                <h2 style={{ fontSize: 24, fontWeight: 800, color: '#0F172A', letterSpacing: '-0.02em' }}>Reset your password</h2>
                <p style={{ fontSize: 14, color: '#64748B', marginTop: 5 }}>Enter your email and we'll send you a reset link.</p>
            </div>

            {flash?.success && (
                <div style={{ background: '#EDF7ED', color: '#1A4D1B', borderLeft: '3px solid #2C5F2D', borderRadius: 9, padding: '11px 14px', fontSize: 13, marginBottom: 18 }}>
                    {flash.success}
                </div>
            )}
            {errors.email && (
                <div style={{ background: '#FBEAEC', color: '#7A1F2C', borderLeft: '3px solid #B23A48', borderRadius: 9, padding: '11px 14px', fontSize: 13, marginBottom: 18 }}>
                    {errors.email}
                </div>
            )}

            <form onSubmit={submit}>
                <div style={{ marginBottom: 16 }}>
                    <label style={{ display: 'block', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.06em', color: '#475569', marginBottom: 6 }}>Email address</label>
                    <input
                        type="email"
                        value={data.email}
                        onChange={e => setData('email', e.target.value)}
                        placeholder="you@example.com"
                        autoComplete="email"
                        autoFocus
                        required
                        style={{ width: '100%', background: '#fff', border: '1.5px solid #E2E8F0', borderRadius: 9, padding: '10px 14px', fontSize: 14, color: '#0F172A', fontFamily: 'inherit', outline: 'none', boxSizing: 'border-box' }}
                    />
                </div>
                <button type="submit" disabled={processing} style={{ width: '100%', background: processing ? '#a0aec0' : '#1E3A5F', color: '#fff', border: 'none', borderRadius: 10, padding: '13px 16px', fontSize: 15, fontWeight: 700, cursor: processing ? 'not-allowed' : 'pointer', marginTop: 6, fontFamily: 'inherit' }}>
                    {processing ? 'Sending…' : 'Send reset link'}
                </button>
            </form>

            <div style={{ textAlign: 'center', marginTop: 22, fontSize: 13, color: '#64748B' }}>
                <Link href="/login" style={{ color: '#1E3A5F', fontWeight: 700, textDecoration: 'none' }}>← Back to sign in</Link>
            </div>
        </AuthLayout>
    );
}
