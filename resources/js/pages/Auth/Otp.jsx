import { useForm, Link } from '@inertiajs/react';
import AuthLayout from '@/layouts/AuthLayout';

export default function Otp() {
    const { data, setData, post, processing, errors } = useForm({ otp: '' });

    function submit(e) {
        e.preventDefault();
        post('/verify-otp');
    }

    return (
        <AuthLayout title="Two-Factor Verification">
            <div style={{ maxWidth: 380, margin: '0 auto' }}>
                <div style={{ textAlign: 'center', marginBottom: 32 }}>
                    <div style={{ width: 56, height: 56, borderRadius: 16, background: 'linear-gradient(135deg,#1E3A5F,#2C5F2D)', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 26, margin: '0 auto 16px' }}>🔐</div>
                    <h1 style={{ fontSize: 24, fontWeight: 800, color: '#0F172A', marginBottom: 8 }}>Check your phone</h1>
                    <p style={{ color: '#64748B', fontSize: 14, lineHeight: 1.6 }}>We sent a 6-digit code to your registered phone number. Enter it below to continue.</p>
                </div>

                {errors.otp && (
                    <div style={{ background: '#FBEAEC', borderLeft: '3px solid #B23A48', padding: '12px 16px', borderRadius: 8, color: '#7A1F2C', fontSize: 13, marginBottom: 20 }}>
                        {errors.otp}
                    </div>
                )}

                <form onSubmit={submit}>
                    <div style={{ marginBottom: 20 }}>
                        <label style={{ display: 'block', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.05em', color: '#475569', marginBottom: 6 }}>Verification Code</label>
                        <input
                            type="text"
                            name="otp"
                            inputMode="numeric"
                            pattern="[0-9]{6}"
                            maxLength={6}
                            autoFocus
                            autoComplete="one-time-code"
                            value={data.otp}
                            onChange={e => setData('otp', e.target.value)}
                            placeholder="000000"
                            style={{ width: '100%', padding: 14, border: '1.5px solid #E2E8F0', borderRadius: 10, fontSize: 28, fontWeight: 800, letterSpacing: '0.25em', textAlign: 'center', background: '#FAFBFC', color: '#0F172A', fontFamily: 'inherit', boxSizing: 'border-box' }}
                        />
                        <div style={{ fontSize: 12, color: '#94A3B8', marginTop: 6, textAlign: 'center' }}>Code is valid for 10 minutes</div>
                    </div>
                    <button type="submit" disabled={processing} style={{ width: '100%', padding: 13, background: processing ? '#a0aec0' : '#1E3A5F', color: '#fff', border: 'none', borderRadius: 10, fontSize: 15, fontWeight: 700, cursor: processing ? 'not-allowed' : 'pointer', fontFamily: 'inherit' }}>
                        {processing ? 'Verifying…' : 'Verify & Sign In'}
                    </button>
                </form>

                <div style={{ marginTop: 24, textAlign: 'center' }}>
                    <Link href="/login" style={{ fontSize: 13, color: '#64748B', textDecoration: 'none' }}>← Use a different account</Link>
                </div>
            </div>
        </AuthLayout>
    );
}
