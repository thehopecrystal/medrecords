import { Link, usePage } from '@inertiajs/react';
import { useEffect } from 'react';

export default function Landing({ heroHead, heroSub, heroCta1, heroCta2, plans, contact, social, footerAbout }) {
    const { site } = usePage().props;

    useEffect(() => {
        if (site?.favicon) {
            let link = document.querySelector("link[rel~='icon']");
            if (!link) { link = document.createElement('link'); link.rel = 'icon'; document.head.appendChild(link); }
            link.href = site.favicon;
        }
    }, [site?.favicon]);

    const headParts = (heroHead || 'Your health record,<br>always with you').split('<br>');

    return (
        <div style={{ fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", "Inter", Roboto, Arial, sans-serif', color: '#0F172A', lineHeight: 1.6 }}>
            {/* Header */}
            <header style={{ position: 'sticky', top: 0, zIndex: 100, background: 'rgba(255,255,255,0.92)', backdropFilter: 'blur(12px)', borderBottom: '1px solid #E2E8F0' }}>
                <div style={{ maxWidth: 1160, margin: '0 auto', padding: '0 24px', height: 64, display: 'flex', alignItems: 'center', gap: 32 }}>
                    <a href="/" style={{ display: 'flex', alignItems: 'center', gap: 10, fontSize: 20, fontWeight: 800, color: '#1E3A5F', letterSpacing: '-0.03em', textDecoration: 'none', flexShrink: 0 }}>
                        {site?.logo
                            ? <img src={site.logo} alt={site.name} style={{ width: 36, height: 36, borderRadius: 10, objectFit: 'contain' }} />
                            : <div style={{ width: 36, height: 36, borderRadius: 10, background: 'linear-gradient(135deg,#1E3A5F,#2C5F2D)', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 18, fontWeight: 900, color: '#fff' }}>M</div>
                        }
                        {site?.name ?? 'MedRecords'}
                    </a>
                    <nav style={{ display: 'flex', gap: 4, flex: 1 }} className="hdr-nav">
                        {[['#features','Features'],['#how-it-works','How it works'],['#why-us','Why MedRecords'],['#pricing','Pricing'],['#contact','Contact']].map(([h,l]) => (
                            <a key={h} href={h} style={{ padding: '8px 14px', borderRadius: 8, fontSize: 14, fontWeight: 500, color: '#475569', textDecoration: 'none' }}>{l}</a>
                        ))}
                    </nav>
                    <div style={{ display: 'flex', gap: 8, flexShrink: 0 }}>
                        <Link href="/login" style={{ padding: '9px 18px', borderRadius: 8, fontSize: 13, fontWeight: 600, border: '1.5px solid #E2E8F0', color: '#0F172A', background: '#fff', textDecoration: 'none' }}>Sign in</Link>
                        <Link href="/register" style={{ padding: '9px 18px', borderRadius: 8, fontSize: 13, fontWeight: 700, background: '#1E3A5F', color: '#fff', textDecoration: 'none' }}>Get started free →</Link>
                    </div>
                </div>
            </header>

            {/* Hero */}
            <section style={{ background: 'linear-gradient(160deg,#0D1F35 0%,#1E3A5F 45%,#1A4A2E 100%)', color: '#fff', padding: '100px 24px 90px', textAlign: 'center', position: 'relative', overflow: 'hidden' }}>
                <div style={{ maxWidth: 800, margin: '0 auto', position: 'relative' }}>
                    <div style={{ display: 'inline-flex', alignItems: 'center', gap: 6, background: 'rgba(255,255,255,0.1)', border: '1px solid rgba(255,255,255,0.2)', padding: '6px 14px', borderRadius: 20, fontSize: 12, fontWeight: 600, color: 'rgba(255,255,255,0.85)', marginBottom: 28 }}>
                        🇳🇬 Built for Nigeria's healthcare system
                    </div>
                    <h1 style={{ fontSize: 54, fontWeight: 900, lineHeight: 1.1, letterSpacing: '-0.03em', marginBottom: 22 }}>
                        {headParts.map((p, i) => <span key={i}>{p}{i < headParts.length - 1 && <br />}</span>)}
                    </h1>
                    <p style={{ fontSize: 18, color: 'rgba(255,255,255,0.75)', lineHeight: 1.65, maxWidth: 600, margin: '0 auto 36px' }}>{heroSub}</p>
                    <div style={{ display: 'flex', gap: 12, justifyContent: 'center', flexWrap: 'wrap' }}>
                        <Link href="/register" style={{ padding: '14px 32px', borderRadius: 10, fontSize: 16, fontWeight: 700, background: '#fff', color: '#1E3A5F', textDecoration: 'none', boxShadow: '0 4px 20px rgba(0,0,0,0.2)' }}>{heroCta1} →</Link>
                        <a href="#features" style={{ padding: '14px 32px', borderRadius: 10, fontSize: 16, fontWeight: 600, background: 'rgba(255,255,255,0.12)', border: '1.5px solid rgba(255,255,255,0.3)', color: '#fff', textDecoration: 'none' }}>{heroCta2}</a>
                    </div>
                    <div style={{ display: 'flex', gap: 14, justifyContent: 'center', marginTop: 56, flexWrap: 'wrap' }}>
                        {[['🏥','4+','Partner facilities'],['🔐','100%','Consent-controlled'],['📋','FHIR','R4-aligned records'],['📲','SMS','Instant access codes']].map(([icon,val,lbl]) => (
                            <div key={lbl} style={{ background: 'rgba(255,255,255,0.08)', border: '1px solid rgba(255,255,255,0.15)', backdropFilter: 'blur(10px)', borderRadius: 14, padding: '16px 20px', textAlign: 'left', minWidth: 160 }}>
                                <div style={{ fontSize: 24, marginBottom: 8 }}>{icon}</div>
                                <div style={{ fontSize: 22, fontWeight: 800, color: '#6EE4A8' }}>{val}</div>
                                <div style={{ fontSize: 12, color: 'rgba(255,255,255,0.6)', marginTop: 2 }}>{lbl}</div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Features */}
            <section style={{ padding: '90px 24px' }} id="features">
                <div style={{ maxWidth: 1160, margin: '0 auto' }}>
                    <SectionLabel>What we offer</SectionLabel>
                    <SectionTitle>Everything you need in one place</SectionTitle>
                    <p style={{ fontSize: 16, color: '#475569', maxWidth: 560, lineHeight: 1.6 }}>From GP visits to lab results, prescriptions to dental records — your complete health story, always accessible.</p>
                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3,1fr)', gap: 24, marginTop: 52 }} className="feat-grid">
                        {[
                            ['📋','navy','Complete Health Records','Store encounters, prescriptions, lab results, vaccinations, dental and eye care records — all in a single FHIR R4-aligned record.'],
                            ['🔐','green','Patient-Controlled Access','You decide who sees your records. Grant consent to specific providers for a limited time. Revoke instantly, anytime.'],
                            ['💊','amber','Pharmacy Verification','Pharmacists verify prescriptions against your MedRecords before dispensing — preventing duplication and fraud.'],
                            ['📲','red','SMS Access Codes','Share one-time access codes via SMS. Providers verify your identity at reception without requiring a smartphone.'],
                            ['👨‍👩‍👧','navy','Family Dependents','Manage health records for your children and elderly relatives. One account, your whole family covered.'],
                            ['👁','green','Full Audit Trail','Every access to your record is logged with time, actor, and reason. Know exactly who saw what and when.'],
                        ].map(([icon,color,title,desc]) => (
                            <div key={title} style={{ background: '#F8FAFC', borderRadius: 16, padding: '28px 24px', border: '1px solid #E2E8F0', transition: 'transform 0.2s, box-shadow 0.2s' }}>
                                <div style={{ width: 52, height: 52, borderRadius: 14, background: color === 'navy' ? '#EEF3FA' : color === 'green' ? '#EDF7ED' : color === 'amber' ? '#FEF9E7' : '#FBEAEC', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 26, marginBottom: 18 }}>{icon}</div>
                                <div style={{ fontSize: 16, fontWeight: 700, color: '#1E3A5F', marginBottom: 8 }}>{title}</div>
                                <div style={{ fontSize: 13, color: '#475569', lineHeight: 1.6 }}>{desc}</div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* How it works */}
            <section style={{ padding: '90px 24px', background: '#F8FAFC' }} id="how-it-works">
                <div style={{ maxWidth: 1160, margin: '0 auto', textAlign: 'center' }}>
                    <SectionLabel>Simple steps</SectionLabel>
                    <SectionTitle>How MedRecords works</SectionTitle>
                    <p style={{ fontSize: 16, color: '#475569', maxWidth: 560, lineHeight: 1.6, margin: '0 auto' }}>Getting started takes under two minutes. No paperwork, no queues.</p>
                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3,1fr)', gap: 32, marginTop: 52 }} className="steps-grid">
                        {[
                            ['1','Create your account','Register with your name, email, phone number and a few health details. You get a unique MedID instantly — it\'s free, always.'],
                            ['2','Visit any partner facility','Share your 6-digit access code with the doctor or pharmacist. They can view your records with your permission only.'],
                            ['3','Stay in control','Review who accessed your records, revoke any consent, and get your complete health history on demand — anywhere, anytime.'],
                        ].map(([n,title,desc]) => (
                            <div key={n} style={{ textAlign: 'center' }}>
                                <div style={{ width: 56, height: 56, borderRadius: '50%', background: '#1E3A5F', color: '#fff', fontSize: 22, fontWeight: 800, display: 'flex', alignItems: 'center', justifyContent: 'center', margin: '0 auto 20px' }}>{n}</div>
                                <div style={{ fontSize: 17, fontWeight: 700, color: '#1E3A5F', marginBottom: 8 }}>{title}</div>
                                <div style={{ fontSize: 13, color: '#475569', lineHeight: 1.6 }}>{desc}</div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Stats */}
            <section style={{ padding: '90px 24px', background: '#0D1F35', color: '#fff' }} id="why-us">
                <div style={{ maxWidth: 1160, margin: '0 auto', textAlign: 'center' }}>
                    <div style={{ fontSize: 12, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.1em', color: '#6EE4A8', marginBottom: 10, display: 'inline-block' }}>By the numbers</div>
                    <h2 style={{ fontSize: 38, fontWeight: 900, color: '#fff', lineHeight: 1.15, letterSpacing: '-0.02em', marginBottom: 14 }}>Trusted by patients across Nigeria</h2>
                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4,1fr)', marginTop: 52 }} className="stats-bar">
                        {[['5+','Registered patients'],['3+','Partner facilities'],['100%','Consent-driven access'],['FHIR','International standard']].map(([v,l],i) => (
                            <div key={l} style={{ textAlign: 'center', padding: '32px 16px', borderRight: i < 3 ? '1px solid rgba(255,255,255,0.1)' : 'none' }}>
                                <div style={{ fontSize: 42, fontWeight: 900, color: '#6EE4A8', lineHeight: 1 }}>{v}</div>
                                <div style={{ fontSize: 13, color: 'rgba(255,255,255,0.65)', marginTop: 6 }}>{l}</div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Testimonials */}
            <section style={{ padding: '90px 24px' }}>
                <div style={{ maxWidth: 1160, margin: '0 auto' }}>
                    <SectionLabel>What people say</SectionLabel>
                    <SectionTitle>Trusted by patients & providers</SectionTitle>
                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3,1fr)', gap: 24, marginTop: 52 }} className="testi-grid">
                        {[
                            ['"I no longer carry a folder of lab results to every hospital visit. My doctor accesses my records the moment I share my code. This is the future of healthcare in Nigeria."','Adaeze Okonkwo','Patient, Lagos','linear-gradient(135deg,#1E3A5F,#2C5F2D)','AO'],
                            ['"Seeing a patient\'s allergy history before prescribing has prevented serious incidents at our clinic. The consent system is excellent — patients feel safe."','Dr. Tunde Kamara','General Practitioner, Reddington Hospital','linear-gradient(135deg,#2C5F2D,#1E3A5F)','TK'],
                            ['"Prescription verification through MedRecords has reduced fraudulent prescriptions at our pharmacy to almost zero. Every pharmacist in Nigeria should be using this."','Bola Okafor','Pharmacist, HealthPlus Pharmacy','linear-gradient(135deg,#B8860B,#1E3A5F)','BO'],
                        ].map(([quote,name,role,bg,initials]) => (
                            <div key={name} style={{ background: '#fff', borderRadius: 16, padding: '28px 24px', border: '1px solid #E2E8F0', boxShadow: '0 2px 8px rgba(15,23,42,0.04)' }}>
                                <div style={{ color: '#F59E0B', fontSize: 13, marginBottom: 12 }}>★★★★★</div>
                                <p style={{ fontSize: 14, color: '#475569', lineHeight: 1.7, marginBottom: 20, fontStyle: 'italic' }}>{quote}</p>
                                <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
                                    <div style={{ width: 40, height: 40, borderRadius: '50%', background: bg, display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 14, fontWeight: 700, color: '#fff' }}>{initials}</div>
                                    <div>
                                        <div style={{ fontSize: 14, fontWeight: 700 }}>{name}</div>
                                        <div style={{ fontSize: 12, color: '#94A3B8' }}>{role}</div>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Pricing */}
            {plans?.length > 0 && (
                <section style={{ padding: '90px 24px', background: '#F8FAFC' }} id="pricing">
                    <div style={{ maxWidth: 1160, margin: '0 auto', textAlign: 'center' }}>
                        <SectionLabel>Simple, transparent pricing</SectionLabel>
                        <SectionTitle>Choose the plan that fits you</SectionTitle>
                        <p style={{ fontSize: 16, color: '#475569', maxWidth: 560, margin: '0 auto', lineHeight: 1.6 }}>Start free. Upgrade anytime. No hidden fees, no contracts.</p>
                        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit,minmax(240px,1fr))', gap: 24, marginTop: 52, alignItems: 'start' }}>
                            {plans.map(plan => (
                                <div key={plan.id} style={{ background: '#fff', borderRadius: 20, border: `1.5px solid ${plan.is_featured ? '#1E3A5F' : '#E2E8F0'}`, padding: '32px 28px', position: 'relative', overflow: 'hidden', boxShadow: plan.is_featured ? '0 8px 32px rgba(30,58,95,0.15)' : undefined }}>
                                    {plan.badge && <div style={{ position: 'absolute', top: 16, right: -24, background: '#1E3A5F', color: '#fff', fontSize: 10, fontWeight: 700, letterSpacing: '0.04em', padding: '4px 32px', transform: 'rotate(35deg)' }}>{plan.badge}</div>}
                                    <div style={{ fontSize: 18, fontWeight: 800, color: '#1E3A5F', marginBottom: 6 }}>{plan.name}</div>
                                    <div style={{ fontSize: 36, fontWeight: 900, color: '#1E3A5F', lineHeight: 1, margin: '16px 0 6px' }}>
                                        {plan.price === 0 ? 'Free' : plan.billing_period === 'one-time' ? <span style={{ fontSize: 20 }}>Contact us</span> : <>₦{Number(plan.price).toLocaleString()}<span style={{ fontSize: 14, fontWeight: 500, color: '#475569' }}>/{plan.billing_period === 'monthly' ? 'mo' : 'yr'}</span></>}
                                    </div>
                                    <div style={{ fontSize: 13, color: '#475569', marginBottom: 22, minHeight: 36, lineHeight: 1.5 }}>{plan.description}</div>
                                    <ul style={{ listStyle: 'none', margin: '0 0 28px', padding: 0 }}>
                                        {(plan.features ?? []).map(f => (
                                            <li key={f} style={{ display: 'flex', alignItems: 'flex-start', gap: 8, fontSize: 13, color: '#475569', padding: '5px 0', borderBottom: '1px solid #F8FAFC' }}>
                                                <span style={{ color: '#2C5F2D', fontWeight: 700, flexShrink: 0 }}>✓</span> {f}
                                            </li>
                                        ))}
                                    </ul>
                                    <Link href="/register" style={{ display: 'block', textAlign: 'center', padding: '12px 24px', borderRadius: 10, fontSize: 14, fontWeight: 700, background: plan.price === 0 ? '#2C5F2D' : plan.billing_period === 'one-time' ? '#475569' : '#1E3A5F', color: '#fff', textDecoration: 'none' }}>
                                        {plan.billing_period === 'one-time' ? 'Contact Sales' : plan.price === 0 ? 'Get started free →' : `Start ${plan.name} →`}
                                    </Link>
                                </div>
                            ))}
                        </div>
                    </div>
                </section>
            )}

            {/* CTA */}
            <section style={{ background: 'linear-gradient(135deg,#1E3A5F,#2C5F2D)', padding: '80px 24px', textAlign: 'center' }}>
                <div style={{ maxWidth: 640, margin: '0 auto' }}>
                    <h2 style={{ fontSize: 38, fontWeight: 900, color: '#fff', marginBottom: 16 }}>Your health records deserve better</h2>
                    <p style={{ fontSize: 16, color: 'rgba(255,255,255,0.8)', marginBottom: 36 }}>Join thousands of Nigerians taking control of their health data. Registration is free and takes less than 2 minutes.</p>
                    <div style={{ display: 'flex', gap: 12, justifyContent: 'center', flexWrap: 'wrap' }}>
                        <Link href="/register" style={{ padding: '14px 32px', borderRadius: 10, fontSize: 16, fontWeight: 700, background: '#fff', color: '#1E3A5F', textDecoration: 'none', boxShadow: '0 4px 20px rgba(0,0,0,0.2)' }}>Create your free account →</Link>
                        <Link href="/login" style={{ padding: '14px 32px', borderRadius: 10, fontSize: 16, fontWeight: 600, background: 'rgba(255,255,255,0.12)', border: '1.5px solid rgba(255,255,255,0.3)', color: '#fff', textDecoration: 'none' }}>Sign in</Link>
                    </div>
                </div>
            </section>

            {/* Footer */}
            <footer style={{ background: '#0D1F35', color: 'rgba(255,255,255,0.7)', padding: '64px 24px 32px' }} id="contact">
                <div style={{ maxWidth: 1160, margin: '0 auto' }}>
                    <div style={{ display: 'grid', gridTemplateColumns: '2fr 1fr 1fr 1fr', gap: 48, marginBottom: 48 }} className="footer-grid">
                        <div>
                            <div style={{ display: 'flex', alignItems: 'center', gap: 10, fontSize: 18, fontWeight: 800, color: '#fff', letterSpacing: '-0.02em', marginBottom: 14 }}>
                                {site?.logo ? <img src={site.logo} alt={site?.name} style={{ width: 30, height: 30, borderRadius: 8, objectFit: 'contain' }} /> : <div style={{ width: 30, height: 30, borderRadius: 8, background: 'linear-gradient(135deg,#1E3A5F,#2C5F2D)', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 14, fontWeight: 900, color: '#fff' }}>M</div>}
                                {site?.name ?? 'MedRecords'}
                            </div>
                            <p style={{ fontSize: 13, lineHeight: 1.7 }}>{footerAbout}</p>
                            {(social?.facebook || social?.twitter || social?.instagram || social?.linkedin) && (
                                <div style={{ display: 'flex', gap: 10, marginTop: 18 }}>
                                    {social.facebook && <a href={social.facebook} style={socialBtn}>📘</a>}
                                    {social.twitter && <a href={social.twitter} style={socialBtn}>𝕏</a>}
                                    {social.instagram && <a href={social.instagram} style={socialBtn}>📷</a>}
                                    {social.linkedin && <a href={social.linkedin} style={socialBtn}>🔗</a>}
                                </div>
                            )}
                        </div>
                        <div>
                            <div style={footerColTitle}>Platform</div>
                            {[['Register as patient','/register'],['Provider login','/login'],['Pharmacy login','/login'],['Admin portal','/login']].map(([l,h]) => <Link key={l} href={h} style={footerLink}>{l}</Link>)}
                        </div>
                        <div>
                            <div style={footerColTitle}>Resources</div>
                            {[['Features','#features'],['How it works','#how-it-works'],['Privacy policy','#'],['Terms of service','#']].map(([l,h]) => <a key={l} href={h} style={footerLink}>{l}</a>)}
                        </div>
                        <div>
                            <div style={footerColTitle}>Contact</div>
                            {contact?.phone1 && <div style={contactItem}><span>📞</span><span>{contact.phone1}</span></div>}
                            {contact?.phone2 && <div style={contactItem}><span>📞</span><span>{contact.phone2}</span></div>}
                            {contact?.email && <div style={contactItem}><span>✉️</span><span>{contact.email}</span></div>}
                            {contact?.address && <div style={contactItem}><span>📍</span><span>{[contact.address, contact.city, contact.state].filter(Boolean).join(', ')}</span></div>}
                        </div>
                    </div>
                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', paddingTop: 24, borderTop: '1px solid rgba(255,255,255,0.1)', fontSize: 12 }}>
                        <span>© {new Date().getFullYear()} {site?.name ?? 'MedRecords'}. All rights reserved.</span>
                        <span>Built for Nigeria's healthcare future 🇳🇬</span>
                    </div>
                </div>
            </footer>

            <style>{`
                @media (max-width: 900px) {
                    .hdr-nav { display: none !important; }
                    .feat-grid { grid-template-columns: 1fr 1fr !important; }
                    .steps-grid { grid-template-columns: 1fr !important; }
                    .stats-bar { grid-template-columns: 1fr 1fr !important; }
                    .testi-grid { grid-template-columns: 1fr !important; }
                    .footer-grid { grid-template-columns: 1fr 1fr !important; gap: 32px !important; }
                }
                @media (max-width: 600px) {
                    .feat-grid { grid-template-columns: 1fr !important; }
                    .footer-grid { grid-template-columns: 1fr !important; }
                }
            `}</style>
        </div>
    );
}

const socialBtn = { width: 34, height: 34, borderRadius: 8, background: 'rgba(255,255,255,0.08)', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 14, textDecoration: 'none', color: '#fff' };
const footerColTitle = { fontSize: 12, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.08em', color: '#fff', marginBottom: 16 };
const footerLink = { display: 'block', fontSize: 13, color: 'rgba(255,255,255,0.6)', marginBottom: 10, textDecoration: 'none' };
const contactItem = { display: 'flex', alignItems: 'center', gap: 8, marginBottom: 10, fontSize: 13 };

function SectionLabel({ children }) {
    return <div style={{ fontSize: 12, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.1em', color: '#2C5F2D', marginBottom: 10 }}>{children}</div>;
}
function SectionTitle({ children }) {
    return <h2 style={{ fontSize: 38, fontWeight: 900, color: '#1E3A5F', lineHeight: 1.15, letterSpacing: '-0.02em', marginBottom: 14 }}>{children}</h2>;
}
