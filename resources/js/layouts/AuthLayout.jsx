import { usePage } from '@inertiajs/react';

export default function AuthLayout({ children, title }) {
    const { site } = usePage().props;

    return (
        <div style={{ minHeight: '100vh', display: 'grid', gridTemplateColumns: '1fr 1fr', color: '#0F172A', lineHeight: 1.55, fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", "Inter", Roboto, Arial, sans-serif' }}>
            {/* Left branding panel */}
            <div style={{
                background: 'linear-gradient(160deg, #0D1F35 0%, #1E3A5F 50%, #1A4A2E 100%)',
                display: 'flex', flexDirection: 'column', justifyContent: 'space-between',
                padding: '40px 48px', position: 'relative', overflow: 'hidden',
            }} className="auth-left">
                <div style={{ position: 'relative', zIndex: 1 }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: 12, marginBottom: 64 }}>
                        {site?.logo
                            ? <img src={site.logo} alt={site?.name} style={{ width: 40, height: 40, borderRadius: 12, objectFit: 'contain' }} />
                            : <div style={{ width: 40, height: 40, borderRadius: 12, background: 'rgba(255,255,255,0.15)', border: '1px solid rgba(255,255,255,0.2)', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 20, fontWeight: 900, color: '#fff' }}>M</div>
                        }
                        <span style={{ fontSize: 22, fontWeight: 800, color: '#fff', letterSpacing: '-0.02em' }}>{site?.name ?? 'MedRecords'}</span>
                    </div>

                    <h1 style={{ fontSize: 36, fontWeight: 900, color: '#fff', lineHeight: 1.15, letterSpacing: '-0.02em', marginBottom: 18 }}>
                        Your health<br />record, <span style={{ color: '#6EE4A8' }}>always<br />with you</span>
                    </h1>
                    <p style={{ fontSize: 15, color: 'rgba(255,255,255,0.7)', lineHeight: 1.65, maxWidth: 340 }}>
                        Secure, patient-owned electronic health records for every Nigerian — shared only with your consent.
                    </p>

                    <div style={{ marginTop: 48, display: 'flex', flexDirection: 'column', gap: 16 }}>
                        {[
                            ['🔐', 'Consent-gated access — you control who sees your data'],
                            ['📋', 'Complete health history across all facilities'],
                            ['📲', 'Share access codes instantly via SMS'],
                            ['🏥', 'NHIA eligibility checks and prescription verification'],
                        ].map(([icon, text]) => (
                            <div key={text} style={{ display: 'flex', alignItems: 'center', gap: 12, fontSize: 13, color: 'rgba(255,255,255,0.8)' }}>
                                <div style={{ width: 28, height: 28, borderRadius: 8, background: 'rgba(255,255,255,0.1)', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 14, flexShrink: 0 }}>{icon}</div>
                                <span>{text}</span>
                            </div>
                        ))}
                    </div>
                </div>
                <div style={{ position: 'relative', zIndex: 1, fontSize: 12, color: 'rgba(255,255,255,0.4)' }}>
                    © {new Date().getFullYear()} {site?.name ?? 'MedRecords'} · Built for Nigeria 🇳🇬
                </div>
            </div>

            {/* Right form panel */}
            <div style={{ background: '#F8FAFC', display: 'flex', alignItems: 'center', justifyContent: 'center', padding: '40px 32px', overflowY: 'auto' }} className="auth-right">
                <div style={{ width: '100%', maxWidth: 440 }}>
                    <div style={{ textAlign: 'center', marginBottom: 28, display: 'none' }} className="auth-mobile-logo">
                        {site?.logo
                            ? <img src={site.logo} alt={site?.name} style={{ height: 36 }} />
                            : <div style={{ fontSize: 22, fontWeight: 900, color: '#1E3A5F' }}>{site?.name ?? 'MedRecords'}</div>
                        }
                    </div>
                    {children}
                </div>
            </div>

            <style>{`
                @media (max-width: 768px) {
                    .auth-left { display: none !important; }
                    .auth-right { grid-column: 1 / -1; padding: 24px 20px !important; background: #fff !important; }
                    .auth-mobile-logo { display: block !important; }
                }
            `}</style>
        </div>
    );
}
