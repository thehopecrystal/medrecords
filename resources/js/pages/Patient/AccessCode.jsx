import { useState, useEffect } from 'react';
import { usePage, router } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

export default function AccessCode({ accessCode, patient }) {
    const [timeLeft, setTimeLeft] = useState(null);
    const [expired, setExpired] = useState(false);

    useEffect(() => {
        if (!accessCode || !accessCode.expires_at) return;

        const expiry = new Date(accessCode.expires_at);

        function tick() {
            const diff = Math.max(0, Math.floor((expiry - Date.now()) / 1000));
            setTimeLeft(diff);
            if (diff === 0) setExpired(true);
        }

        tick();
        const interval = setInterval(tick, 1000);
        return () => clearInterval(interval);
    }, [accessCode?.expires_at]);

    function formatTime(seconds) {
        if (seconds === null) return '10:00';
        const m = Math.floor(seconds / 60);
        const s = seconds % 60;
        return `${m}:${String(s).padStart(2, '0')}`;
    }

    function generateCode() {
        router.post('/patient/access-code/generate');
    }

    function sendSms() {
        router.post(`/patient/access-code/${accessCode.id}/sms`);
    }

    const isValid = accessCode && !expired;
    const code = accessCode?.decrypted_code || '';
    const codePart1 = code.substring(0, 3);
    const codePart2 = code.substring(3);

    return (
        <AppLayout title="Share Record">
            <div style={{ textAlign: 'center', maxWidth: 420, margin: '0 auto' }}>
                <h1 style={{ fontSize: 22, fontWeight: 800, color: 'var(--color-navy)', marginBottom: 4 }}>Share at Reception</h1>
                <p style={{ fontSize: 14, color: 'var(--color-text-2)', marginBottom: 24 }}>Show this code to your provider for in-person access</p>

                {isValid ? (
                    <>
                        {/* Code display */}
                        <div style={{ opacity: expired ? 0.4 : 1, background: 'linear-gradient(135deg, #1E3A5F 0%, #2C5178 100%)', borderRadius: 20, padding: '32px 24px', color: '#fff', marginBottom: 16 }}>
                            <div style={{ fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.12em', opacity: 0.7, marginBottom: 14 }}>One-time access code</div>
                            <div style={{ fontSize: 52, fontWeight: 800, fontFamily: 'monospace', letterSpacing: '0.18em', textShadow: '0 2px 10px rgba(0,0,0,0.4)' }}>
                                {codePart1}&nbsp;{codePart2}
                            </div>
                            <div style={{ marginTop: 16, fontSize: 14, opacity: 0.75 }}>
                                Valid for {formatTime(timeLeft)}
                            </div>
                        </div>

                        <p style={{ fontSize: 13, color: 'var(--color-text-2)', marginBottom: 18 }}>
                            Show this to your provider or read it out loud. Works without internet.
                        </p>

                        <div style={{ display: 'flex', flexDirection: 'column', gap: 10 }}>
                            <button onClick={sendSms} style={{ width: '100%', padding: '11px 20px', borderRadius: 8, fontSize: 13, fontWeight: 600, cursor: 'pointer', background: 'var(--color-card)', color: 'var(--color-navy)', border: '1.5px solid var(--color-navy)' }}>
                                📲 Send code to my phone (SMS){accessCode.sms_sent ? ' ✓ Sent' : ''}
                            </button>
                            <button onClick={generateCode} style={{ width: '100%', padding: '11px 20px', borderRadius: 8, fontSize: 13, fontWeight: 600, cursor: 'pointer', background: 'var(--color-card)', color: 'var(--color-text-2)', border: '1.5px solid var(--color-border)' }}>
                                Generate new code
                            </button>
                        </div>
                    </>
                ) : (
                    <>
                        <div style={{ padding: '32px 0' }}>
                            <button onClick={generateCode} style={{ background: 'var(--color-navy)', color: '#fff', border: 'none', borderRadius: 8, padding: '12px 28px', fontSize: 15, fontWeight: 600, cursor: 'pointer', marginBottom: 12 }}>
                                Generate access code
                            </button>
                            <p style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 4 }}>Code expires after 10 minutes. Works offline.</p>
                        </div>

                        <div style={{ background: 'var(--color-navy-lt)', borderRadius: 10, padding: '12px 14px', fontSize: 12, color: 'var(--color-text-2)', textAlign: 'left' }}>
                            <strong style={{ color: 'var(--color-navy)' }}>Tip:</strong> Show the code on screen or read it to your provider. You can also give them your MedID:{' '}
                            <strong>{patient?.med_id || ''}</strong>
                        </div>
                    </>
                )}
            </div>
        </AppLayout>
    );
}
