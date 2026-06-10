import { useState } from 'react';
import { usePage, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

export default function Welcome({ patient }) {
    const { auth } = usePage().props;
    const firstName = (auth.user.name || '').split(' ')[0];
    const [copied, setCopied] = useState(false);

    const profile = {
        'Date of birth': patient.dob,
        'Gender': patient.gender,
        'Blood type': patient.blood_type,
        'State': patient.state,
    };
    const filled = Object.values(profile).filter(Boolean).length;
    const total = Object.keys(profile).length;
    const pct = Math.round((filled / total) * 100);

    function copyMedId() {
        navigator.clipboard.writeText(patient.med_id).then(() => {
            setCopied(true);
            setTimeout(() => setCopied(false), 3000);
        });
    }

    const features = [
        { icon: '📁', title: 'Your full health history', desc: 'Encounters, prescriptions, lab results, vaccinations, dental and eye care — all in one place.' },
        { icon: '🔐', title: 'Consent-gated access', desc: 'Providers can only view your records when you approve. Revoke any time, instantly.' },
        { icon: '👨‍👩‍👧', title: 'Manage family dependents', desc: 'Add children or elderly relatives and oversee their health records from your account.' },
        { icon: '📋', title: 'Full audit trail', desc: 'Every access to your records is logged. You always know who looked at what and when.' },
        { icon: '💊', title: 'Pharmacy verification', desc: 'Pharmacists verify your prescription via MedRecords before dispensing — preventing fraud.' },
        { icon: '📲', title: 'Works everywhere', desc: 'Access codes work via SMS — share a one-time code with your provider at reception.' },
    ];

    return (
        <AppLayout title="Welcome to MedRecords">
            {/* Hero */}
            <div style={{ textAlign: 'center', padding: '32px 0 20px' }}>
                <div style={{ fontSize: 40, marginBottom: 10 }}>🎉</div>
                <h1 style={{ fontSize: 26, fontWeight: 800, color: 'var(--color-navy)', marginBottom: 6 }}>
                    Welcome to MedRecords, {firstName}!
                </h1>
                <p style={{ fontSize: 15, color: 'var(--color-text-2)' }}>Your secure, patient-owned health record is ready.</p>
            </div>

            {/* MedID reveal */}
            <div style={{ background: 'linear-gradient(135deg, #1E3A5F 0%, #2C5178 100%)', color: '#fff', borderRadius: 16, padding: '28px 32px', textAlign: 'center', margin: '20px 0 28px' }}>
                <div style={{ fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.1em', opacity: 0.7, marginBottom: 10 }}>Your unique Medical ID</div>
                <div style={{ fontSize: 32, fontWeight: 800, fontFamily: 'monospace', letterSpacing: '0.12em', color: '#fff', textShadow: '0 2px 8px rgba(0,0,0,0.3)' }}>{patient.med_id}</div>
                <div style={{ fontSize: 13, opacity: 0.8, marginTop: 12, maxWidth: 420, marginLeft: 'auto', marginRight: 'auto', lineHeight: 1.5 }}>
                    Share this with any healthcare provider or pharmacist in Nigeria — they can look up your records with your consent.
                </div>
                <div style={{ display: 'flex', gap: 10, justifyContent: 'center', marginTop: 16, flexWrap: 'wrap' }}>
                    <button onClick={copyMedId} style={{ background: 'var(--color-navy)', color: '#fff', border: 'none', borderRadius: 8, padding: '8px 16px', fontSize: 13, fontWeight: 600, cursor: 'pointer' }}>
                        📋 Copy MedID
                    </button>
                    <Link href="/patient/access-code" style={{ background: 'rgba(255,255,255,0.15)', color: '#fff', border: '1px solid rgba(255,255,255,0.4)', borderRadius: 8, padding: '8px 16px', fontSize: 13, fontWeight: 600, textDecoration: 'none', display: 'inline-flex', alignItems: 'center' }}>
                        🔑 Generate access code
                    </Link>
                </div>
                {copied && <div style={{ marginTop: 10, fontSize: 13, color: '#86efac', fontWeight: 600 }}>✓ Copied to clipboard!</div>}
            </div>

            {/* Profile completeness */}
            {pct < 100 && (
                <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12, padding: 20, marginBottom: 28 }}>
                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 12 }}>
                        <div>
                            <div style={{ fontWeight: 700, fontSize: 14, color: 'var(--color-text)' }}>Complete your health profile</div>
                            <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>{filled}/{total} fields filled — providers see this at a glance</div>
                        </div>
                        <div style={{ fontSize: 22, fontWeight: 800, color: 'var(--color-navy)' }}>{pct}%</div>
                    </div>
                    <div style={{ height: 6, background: 'var(--color-border)', borderRadius: 6, overflow: 'hidden', marginBottom: 14 }}>
                        <div style={{ height: '100%', width: `${pct}%`, background: 'var(--color-navy)', borderRadius: 6, transition: 'width 0.5s ease' }} />
                    </div>
                    <div style={{ display: 'flex', flexWrap: 'wrap', gap: 8 }}>
                        {Object.entries(profile).map(([label, val]) => (
                            <div key={label} style={{ fontSize: 12, padding: '4px 10px', borderRadius: 20, fontWeight: 500, background: val ? '#EDF7ED' : 'var(--color-border)', color: val ? '#2C5F2D' : 'var(--color-text-2)' }}>
                                {val ? '✓' : '○'} {label}
                            </div>
                        ))}
                    </div>
                </div>
            )}

            {/* Quick start */}
            <h2 style={{ fontSize: 15, fontWeight: 700, color: 'var(--color-text)', margin: '0 0 14px' }}>Get started in 3 steps</h2>
            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 14, marginBottom: 32 }}>
                <div style={{ border: '2px solid #2C5F2D44', borderRadius: 12, padding: 18, background: '#EDF7ED' }}>
                    <div style={{ fontSize: 18, fontWeight: 800, color: '#2C5F2D', marginBottom: 8 }}>✓</div>
                    <div style={{ fontSize: 14, fontWeight: 700, marginBottom: 6 }}>Create account</div>
                    <div style={{ fontSize: 12, color: 'var(--color-text-2)', lineHeight: 1.5 }}>Your account and MedID are active and ready to use.</div>
                </div>
                <Link href="/patient/access-code" style={{ border: '2px solid var(--color-border)', borderRadius: 12, padding: 18, textDecoration: 'none', color: 'inherit', display: 'block', transition: 'border-color 0.2s, box-shadow 0.2s' }}>
                    <div style={{ fontSize: 18, fontWeight: 800, color: 'var(--color-navy)', marginBottom: 8 }}>2</div>
                    <div style={{ fontSize: 14, fontWeight: 700, marginBottom: 6 }}>Generate an access code</div>
                    <div style={{ fontSize: 12, color: 'var(--color-text-2)', lineHeight: 1.5 }}>Give a 6-digit code to your provider so they can look up your record. Codes expire in 10 minutes.</div>
                    <div style={{ fontSize: 12, fontWeight: 600, color: 'var(--color-navy)', marginTop: 10 }}>Generate now →</div>
                </Link>
                <Link href="/patient/consents" style={{ border: '2px solid var(--color-border)', borderRadius: 12, padding: 18, textDecoration: 'none', color: 'inherit', display: 'block' }}>
                    <div style={{ fontSize: 18, fontWeight: 800, color: 'var(--color-navy)', marginBottom: 8 }}>3</div>
                    <div style={{ fontSize: 14, fontWeight: 700, marginBottom: 6 }}>Manage consent</div>
                    <div style={{ fontSize: 12, color: 'var(--color-text-2)', lineHeight: 1.5 }}>Approve or revoke provider access to your records. You are always in control.</div>
                    <div style={{ fontSize: 12, fontWeight: 600, color: 'var(--color-navy)', marginTop: 10 }}>View consents →</div>
                </Link>
            </div>

            {/* Feature overview */}
            <h2 style={{ fontSize: 15, fontWeight: 700, color: 'var(--color-text)', margin: '0 0 14px' }}>What you can do on MedRecords</h2>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 14, marginBottom: 8 }}>
                {features.map((f) => (
                    <div key={f.title} style={{ display: 'flex', gap: 14, padding: 16, border: '1px solid var(--color-border)', borderRadius: 10 }}>
                        <div style={{ fontSize: 22, flexShrink: 0 }}>{f.icon}</div>
                        <div>
                            <div style={{ fontSize: 13, fontWeight: 700, marginBottom: 4 }}>{f.title}</div>
                            <div style={{ fontSize: 12, color: 'var(--color-text-2)', lineHeight: 1.5 }}>{f.desc}</div>
                        </div>
                    </div>
                ))}
            </div>

            <div style={{ textAlign: 'center', margin: '32px 0 8px' }}>
                <Link href="/patient/dashboard" style={{ display: 'inline-block', background: 'var(--color-navy)', color: '#fff', borderRadius: 8, padding: '12px 32px', fontSize: 15, fontWeight: 600, textDecoration: 'none' }}>
                    Go to my dashboard →
                </Link>
            </div>
        </AppLayout>
    );
}
