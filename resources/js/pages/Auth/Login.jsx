import { useState } from 'react';
import { useForm, Link } from '@inertiajs/react';
import AuthLayout from '@/layouts/AuthLayout';

export default function Login() {
    const { data, setData, post, processing, errors } = useForm({
        login: '',
        password: '',
        remember: false,
    });
    const [showPwd, setShowPwd] = useState(false);

    function submit(e) {
        e.preventDefault();
        post('/login');
    }

    return (
        <AuthLayout title="Sign In">
            <div style={{ marginBottom: 28 }}>
                <h2 style={{ fontSize: 24, fontWeight: 800, color: '#0F172A', letterSpacing: '-0.02em' }}>Welcome back</h2>
                <p style={{ fontSize: 14, color: '#64748B', marginTop: 5 }}>Sign in with your email address or phone number</p>
            </div>

            {errors.login && (
                <div style={{ background: '#FBEAEC', color: '#7A1F2C', borderLeft: '3px solid #B23A48', borderRadius: 9, padding: '11px 14px', fontSize: 13, marginBottom: 18 }}>
                    {errors.login}
                </div>
            )}

            <form onSubmit={submit}>
                <div style={{ marginBottom: 16 }}>
                    <label style={{ display: 'block', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.06em', color: '#475569', marginBottom: 6 }}>Email or phone</label>
                    <input
                        type="text"
                        value={data.login}
                        onChange={e => setData('login', e.target.value)}
                        placeholder="Email address or phone number"
                        autoComplete="username"
                        autoFocus
                        required
                        style={inputStyle}
                    />
                </div>

                <div style={{ marginBottom: 16 }}>
                    <label style={{ display: 'block', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.06em', color: '#475569', marginBottom: 6 }}>Password</label>
                    <div style={{ position: 'relative', display: 'flex', alignItems: 'center' }}>
                        <input
                            type={showPwd ? 'text' : 'password'}
                            value={data.password}
                            onChange={e => setData('password', e.target.value)}
                            placeholder="••••••••"
                            autoComplete="current-password"
                            required
                            style={{ ...inputStyle, paddingRight: 42 }}
                        />
                        <button type="button" onClick={() => setShowPwd(p => !p)} style={{ position: 'absolute', right: 12, background: 'none', border: 'none', cursor: 'pointer', fontSize: 16, color: '#94A3B8' }}>
                            {showPwd ? '🙈' : '👁'}
                        </button>
                    </div>
                </div>

                <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginBottom: 8 }}>
                    <label style={{ display: 'flex', alignItems: 'center', gap: 8, fontSize: 13, color: '#64748B', cursor: 'pointer' }}>
                        <input type="checkbox" checked={data.remember} onChange={e => setData('remember', e.target.checked)} style={{ accentColor: '#1E3A5F' }} />
                        Keep me signed in
                    </label>
                    <Link href="/forgot-password" style={{ fontSize: 13, color: '#1E3A5F', fontWeight: 500, textDecoration: 'none' }}>Forgot password?</Link>
                </div>

                <button type="submit" disabled={processing} style={submitStyle(processing)}>
                    {processing ? 'Signing in…' : 'Sign in'}
                </button>
            </form>

            <div style={{ textAlign: 'center', marginTop: 22, fontSize: 13, color: '#64748B' }}>
                No account? <Link href="/register" style={{ color: '#1E3A5F', fontWeight: 700 }}>Register as patient</Link>
            </div>
        </AuthLayout>
    );
}

const inputStyle = {
    width: '100%', background: '#fff', border: '1.5px solid #E2E8F0',
    borderRadius: 9, padding: '10px 14px', fontSize: 14, color: '#0F172A',
    fontFamily: 'inherit', outline: 'none', boxSizing: 'border-box',
    transition: 'border-color 0.15s',
};

const submitStyle = (disabled) => ({
    width: '100%', background: disabled ? '#a0aec0' : '#1E3A5F', color: '#fff',
    border: 'none', borderRadius: 10, padding: '13px 16px',
    fontSize: 15, fontWeight: 700, cursor: disabled ? 'not-allowed' : 'pointer',
    marginTop: 6, transition: 'background 0.15s', fontFamily: 'inherit',
});
