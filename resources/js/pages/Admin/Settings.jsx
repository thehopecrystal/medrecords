import { useState, useRef } from 'react';
import { usePage, router } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

const NIGERIAN_STATES = ['Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno','Cross River','Delta','Ebonyi','Edo','Ekiti','Enugu','FCT','Gombe','Imo','Jigawa','Kaduna','Kano','Katsina','Kebbi','Kogi','Kwara','Lagos','Nasarawa','Niger','Ogun','Ondo','Osun','Oyo','Plateau','Rivers','Sokoto','Taraba','Yobe','Zamfara'];

const TABS = [
    { id: 'general',  label: '⚙️ General' },
    { id: 'contact',  label: '📞 Contact' },
    { id: 'social',   label: '🔗 Social Media' },
    { id: 'smtp',     label: '✉️ Email / SMTP' },
    { id: 'sms',      label: '📱 SMS / Twilio' },
    { id: 'payments', label: '💳 Payments' },
    { id: 'landing',  label: '🌐 Landing Page' },
];

function Field({ label, hint, children, span2 }) {
    return (
        <div style={{ gridColumn: span2 ? 'span 2' : undefined }}>
            <label style={{ display: 'block', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.06em', color: 'var(--color-text-2)', marginBottom: 5 }}>{label}</label>
            {children}
            {hint && <div style={{ fontSize: 11, color: 'var(--color-text-3)', marginTop: 4 }}>{hint}</div>}
        </div>
    );
}

const inputStyle = { width: '100%', background: 'var(--color-card)', border: '1.5px solid var(--color-border)', borderRadius: 8, padding: '9px 12px', fontSize: 13, color: 'var(--color-text)', fontFamily: 'inherit', outline: 'none', boxSizing: 'border-box' };
const textareaStyle = { ...inputStyle, resize: 'vertical', minHeight: 80 };

function SectionTitle({ children }) {
    return <div style={{ fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.06em', color: 'var(--color-text-2)', marginBottom: 12, paddingBottom: 8, borderBottom: '1px solid var(--color-border)' }}>{children}</div>;
}

function Alert({ variant = 'navy', children }) {
    const colors = {
        navy:  { background: 'var(--color-navy-lt)',  color: 'var(--color-navy)',  borderColor: 'var(--color-navy)' },
        amber: { background: 'var(--color-amber-lt)', color: 'var(--color-amber)', borderColor: 'var(--color-amber)' },
    };
    const s = colors[variant] ?? colors.navy;
    return (
        <div style={{ ...s, borderLeft: `3px solid ${s.borderColor}`, borderRadius: 8, padding: '10px 14px', fontSize: 12, display: 'flex', gap: 8, alignItems: 'flex-start' }}>
            {children}
        </div>
    );
}

export default function Settings({ settings }) {
    const { auth } = usePage().props;
    const [activeTab, setActiveTab] = useState('general');
    const [form, setForm] = useState(settings);
    const [smsEnabled, setSmsEnabled] = useState(settings.sms_enabled === '1');
    const [gateway, setGateway] = useState(settings.payment_gateway ?? 'none');
    const formRef = useRef(null);

    function setField(key, value) {
        setForm(prev => ({ ...prev, [key]: value }));
    }

    function handleSubmit(e) {
        e.preventDefault();
        const fd = new FormData(formRef.current);
        // Ensure sms_enabled is sent as 0 when unchecked
        if (!smsEnabled) fd.set('sms_enabled', '0');
        router.post('/admin/settings', fd, { forceFormData: true });
    }

    function handleSmtpTest(e) {
        e.preventDefault();
        const email = e.target.test_email.value;
        router.post('/admin/settings/smtp-test', { test_email: email }, { preserveScroll: true });
    }

    function handleSmsTest(e) {
        e.preventDefault();
        const phone = e.target.test_phone.value;
        router.post('/admin/settings/sms-test', { test_phone: phone }, { preserveScroll: true });
    }

    return (
        <AppLayout title="Site Settings">
            <div style={{ marginBottom: 24 }}>
                <h1 style={{ fontSize: 22, fontWeight: 800, color: 'var(--color-navy)', margin: 0 }}>Site Settings</h1>
                <p style={{ fontSize: 13, color: 'var(--color-text-2)', marginTop: 4 }}>Configure your website, contact details, social media and email settings</p>
            </div>

            {/* Tab nav */}
            <div style={{ display: 'flex', gap: 4, background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12, padding: 6, marginBottom: 20, flexWrap: 'wrap' }}>
                {TABS.map(tab => (
                    <button
                        key={tab.id}
                        type="button"
                        onClick={() => setActiveTab(tab.id)}
                        style={{
                            flex: 1, minWidth: 100, padding: '9px 14px', borderRadius: 8,
                            fontSize: 13, fontWeight: 600, border: 'none', cursor: 'pointer',
                            background: activeTab === tab.id ? 'var(--color-navy)' : 'transparent',
                            color: activeTab === tab.id ? '#fff' : 'var(--color-text-2)',
                            fontFamily: 'inherit', transition: 'all 0.15s', whiteSpace: 'nowrap',
                        }}
                    >
                        {tab.label}
                    </button>
                ))}
            </div>

            <form ref={formRef} onSubmit={handleSubmit} encType="multipart/form-data">
                {/* Hidden _method not needed — POST to settings update */}

                {/* ── General ── */}
                {activeTab === 'general' && (
                    <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 14, overflow: 'hidden' }}>
                        <div style={{ padding: '16px 20px 12px', borderBottom: '1px solid var(--color-border)' }}>
                            <div style={{ fontWeight: 700, fontSize: 14, color: 'var(--color-navy)' }}>General Settings</div>
                            <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>Core identity and branding for your platform</div>
                        </div>
                        <div style={{ padding: '20px' }}>
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16 }}>
                                <Field label="Site Name *">
                                    <input name="site_name" type="text" required placeholder="MedRecords" value={form.site_name ?? ''} onChange={e => setField('site_name', e.target.value)} style={inputStyle} />
                                </Field>
                                <Field label="Tagline">
                                    <input name="site_tagline" type="text" placeholder="Your health record, always with you" value={form.site_tagline ?? ''} onChange={e => setField('site_tagline', e.target.value)} style={inputStyle} />
                                </Field>
                                <Field label="Upload Logo" hint="PNG or SVG recommended. Max 2 MB. Uploading a file overrides the URL below." span2>
                                    <input name="site_logo_file" type="file" accept="image/*" style={{ ...inputStyle, padding: '8px', cursor: 'pointer' }} />
                                </Field>
                                {form.site_logo_url && (
                                    <div style={{ gridColumn: 'span 2' }}>
                                        <div style={{ padding: 16, background: '#F8FAFC', border: '1.5px solid var(--color-border)', borderRadius: 8, display: 'inline-flex', gap: 20, alignItems: 'center' }}>
                                            <img src={form.site_logo_url} alt="Logo" style={{ height: 48, objectFit: 'contain' }} />
                                            <span style={{ fontSize: 12, color: 'var(--color-text-2)' }}>{form.site_logo_url}</span>
                                        </div>
                                    </div>
                                )}
                                <Field label="Logo URL" hint="Or paste a direct URL (upload above takes priority)" span2>
                                    <input name="site_logo_url" type="url" placeholder="https://example.com/logo.png" value={form.site_logo_url ?? ''} onChange={e => setField('site_logo_url', e.target.value)} style={inputStyle} />
                                </Field>
                                <Field label="Upload Favicon" hint="ICO, PNG, or SVG. 32×32 px recommended. Max 512 KB." span2>
                                    <input name="site_favicon_file" type="file" accept="image/*,.ico" style={{ ...inputStyle, padding: '8px', cursor: 'pointer' }} />
                                </Field>
                                {form.site_favicon_url && (
                                    <div style={{ gridColumn: 'span 2' }}>
                                        <div style={{ padding: '12px 16px', background: '#F8FAFC', border: '1.5px solid var(--color-border)', borderRadius: 8, display: 'inline-flex', gap: 16, alignItems: 'center' }}>
                                            <img src={form.site_favicon_url} alt="Favicon" style={{ height: 32, width: 32, objectFit: 'contain' }} />
                                            <span style={{ fontSize: 12, color: 'var(--color-text-2)' }}>{form.site_favicon_url}</span>
                                        </div>
                                    </div>
                                )}
                                <Field label="Footer About Text" span2>
                                    <textarea name="footer_about" placeholder="Short description for the website footer…" value={form.footer_about ?? ''} onChange={e => setField('footer_about', e.target.value)} style={textareaStyle} />
                                </Field>
                            </div>
                        </div>
                    </div>
                )}

                {/* ── Contact ── */}
                {activeTab === 'contact' && (
                    <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 14, overflow: 'hidden' }}>
                        <div style={{ padding: '16px 20px 12px', borderBottom: '1px solid var(--color-border)' }}>
                            <div style={{ fontWeight: 700, fontSize: 14, color: 'var(--color-navy)' }}>Contact Information</div>
                            <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>Displayed on the landing page and in emails</div>
                        </div>
                        <div style={{ padding: '20px' }}>
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16 }}>
                                <Field label="Primary Phone Number">
                                    <input name="contact_phone_1" type="text" placeholder="+234 (0) 800 000 0000" value={form.contact_phone_1 ?? ''} onChange={e => setField('contact_phone_1', e.target.value)} style={inputStyle} />
                                </Field>
                                <Field label="Secondary Phone Number">
                                    <input name="contact_phone_2" type="text" placeholder="+234 (0) 700 000 0000" value={form.contact_phone_2 ?? ''} onChange={e => setField('contact_phone_2', e.target.value)} style={inputStyle} />
                                </Field>
                                <Field label="Email Address" span2>
                                    <input name="contact_email" type="email" placeholder="hello@yoursite.com" value={form.contact_email ?? ''} onChange={e => setField('contact_email', e.target.value)} style={inputStyle} />
                                </Field>
                                <Field label="Street Address" span2>
                                    <input name="contact_address" type="text" placeholder="14 Adeola Odeku Street" value={form.contact_address ?? ''} onChange={e => setField('contact_address', e.target.value)} style={inputStyle} />
                                </Field>
                                <Field label="City">
                                    <input name="contact_city" type="text" placeholder="Victoria Island" value={form.contact_city ?? ''} onChange={e => setField('contact_city', e.target.value)} style={inputStyle} />
                                </Field>
                                <Field label="State">
                                    <select name="contact_state" value={form.contact_state ?? ''} onChange={e => setField('contact_state', e.target.value)} style={inputStyle}>
                                        <option value="">Select state…</option>
                                        {NIGERIAN_STATES.map(st => <option key={st} value={st}>{st}</option>)}
                                    </select>
                                </Field>
                            </div>
                        </div>
                    </div>
                )}

                {/* ── Social ── */}
                {activeTab === 'social' && (
                    <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 14, overflow: 'hidden' }}>
                        <div style={{ padding: '16px 20px 12px', borderBottom: '1px solid var(--color-border)' }}>
                            <div style={{ fontWeight: 700, fontSize: 14, color: 'var(--color-navy)' }}>Social Media Links</div>
                            <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>Leave blank to hide the icon on the website footer</div>
                        </div>
                        <div style={{ padding: '20px' }}>
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16 }}>
                                <Field label="📘 Facebook">
                                    <input name="social_facebook" type="url" placeholder="https://facebook.com/yourpage" value={form.social_facebook ?? ''} onChange={e => setField('social_facebook', e.target.value)} style={inputStyle} />
                                </Field>
                                <Field label="𝕏 Twitter / X">
                                    <input name="social_twitter" type="url" placeholder="https://twitter.com/yourhandle" value={form.social_twitter ?? ''} onChange={e => setField('social_twitter', e.target.value)} style={inputStyle} />
                                </Field>
                                <Field label="📷 Instagram">
                                    <input name="social_instagram" type="url" placeholder="https://instagram.com/yourhandle" value={form.social_instagram ?? ''} onChange={e => setField('social_instagram', e.target.value)} style={inputStyle} />
                                </Field>
                                <Field label="🔗 LinkedIn">
                                    <input name="social_linkedin" type="url" placeholder="https://linkedin.com/company/yourname" value={form.social_linkedin ?? ''} onChange={e => setField('social_linkedin', e.target.value)} style={inputStyle} />
                                </Field>
                                <Field label="▶ YouTube">
                                    <input name="social_youtube" type="url" placeholder="https://youtube.com/@yourchannel" value={form.social_youtube ?? ''} onChange={e => setField('social_youtube', e.target.value)} style={inputStyle} />
                                </Field>
                            </div>
                        </div>
                    </div>
                )}

                {/* ── SMTP ── */}
                {activeTab === 'smtp' && (
                    <div style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
                        <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 14, overflow: 'hidden' }}>
                            <div style={{ padding: '16px 20px 12px', borderBottom: '1px solid var(--color-border)' }}>
                                <div style={{ fontWeight: 700, fontSize: 14, color: 'var(--color-navy)' }}>Email / SMTP Settings</div>
                                <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>Configure outgoing email delivery. Used for notifications and alerts.</div>
                            </div>
                            <div style={{ padding: '20px', display: 'flex', flexDirection: 'column', gap: 20 }}>
                                <div>
                                    <SectionTitle>Server Configuration</SectionTitle>
                                    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16 }}>
                                        <Field label="SMTP Host">
                                            <input name="smtp_host" type="text" placeholder="smtp.gmail.com" value={form.smtp_host ?? ''} onChange={e => setField('smtp_host', e.target.value)} style={inputStyle} />
                                        </Field>
                                        <Field label="Port">
                                            <input name="smtp_port" type="number" placeholder="587" value={form.smtp_port ?? ''} onChange={e => setField('smtp_port', e.target.value)} style={inputStyle} />
                                        </Field>
                                        <Field label="Encryption">
                                            <select name="smtp_encryption" value={form.smtp_encryption ?? 'tls'} onChange={e => setField('smtp_encryption', e.target.value)} style={inputStyle}>
                                                <option value="tls">TLS (recommended)</option>
                                                <option value="ssl">SSL</option>
                                                <option value="none">None</option>
                                            </select>
                                        </Field>
                                    </div>
                                </div>
                                <div>
                                    <SectionTitle>Authentication</SectionTitle>
                                    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16 }}>
                                        <Field label="Username">
                                            <input name="smtp_username" type="text" placeholder="your@email.com" autoComplete="off" value={form.smtp_username ?? ''} onChange={e => setField('smtp_username', e.target.value)} style={inputStyle} />
                                        </Field>
                                        <Field label="Password" hint="Leave blank to keep the existing password">
                                            <input name="smtp_password" type="password" placeholder="Leave blank to keep current" autoComplete="new-password" style={inputStyle} />
                                        </Field>
                                    </div>
                                </div>
                                <div>
                                    <SectionTitle>Sender Identity</SectionTitle>
                                    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16 }}>
                                        <Field label="From Name">
                                            <input name="smtp_from_name" type="text" placeholder="MedRecords" value={form.smtp_from_name ?? ''} onChange={e => setField('smtp_from_name', e.target.value)} style={inputStyle} />
                                        </Field>
                                        <Field label="From Email">
                                            <input name="smtp_from_email" type="email" placeholder="noreply@medrecords.ng" value={form.smtp_from_email ?? ''} onChange={e => setField('smtp_from_email', e.target.value)} style={inputStyle} />
                                        </Field>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* SMTP test — separate form so it doesn't include file fields */}
                        <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 14, overflow: 'hidden' }}>
                            <div style={{ padding: '16px 20px 12px', borderBottom: '1px solid var(--color-border)' }}>
                                <div style={{ fontWeight: 700, fontSize: 14, color: 'var(--color-navy)' }}>Test Email</div>
                                <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>Send a test email to verify your SMTP settings are working</div>
                            </div>
                            <div style={{ padding: '20px' }}>
                                <form onSubmit={handleSmtpTest} style={{ display: 'flex', gap: 10, alignItems: 'flex-end' }}>
                                    <Field label="Send test to" children={
                                        <input name="test_email" type="email" defaultValue={auth?.user?.email ?? ''} placeholder="test@example.com" style={inputStyle} />
                                    } />
                                    <button type="submit" style={{ padding: '9px 18px', borderRadius: 8, border: '1.5px solid var(--color-border)', background: 'var(--color-card)', color: 'var(--color-text-2)', fontSize: 13, fontWeight: 600, cursor: 'pointer', whiteSpace: 'nowrap', fontFamily: 'inherit' }}>Send test email</button>
                                </form>
                                <div style={{ marginTop: 14 }}>
                                    <Alert variant="navy"><span>ℹ</span><span>Save your SMTP settings first before sending a test email.</span></Alert>
                                </div>
                            </div>
                        </div>
                    </div>
                )}

                {/* ── SMS ── */}
                {activeTab === 'sms' && (
                    <div style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
                        <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 14, overflow: 'hidden' }}>
                            <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', padding: '16px 20px' }}>
                                <div>
                                    <div style={{ fontWeight: 700, fontSize: 14, color: 'var(--color-navy)' }}>SMS Notifications</div>
                                    <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>Controls whether OTP codes and alerts are sent via SMS.</div>
                                </div>
                                <label style={{ display: 'flex', alignItems: 'center', gap: 10, cursor: 'pointer', userSelect: 'none' }}>
                                    <div
                                        onClick={() => setSmsEnabled(p => !p)}
                                        style={{ width: 44, height: 24, borderRadius: 12, background: smsEnabled ? 'var(--color-navy)' : '#CBD5E1', position: 'relative', cursor: 'pointer', transition: 'background 0.2s', flexShrink: 0 }}
                                    >
                                        <div style={{ position: 'absolute', top: 3, left: smsEnabled ? 23 : 3, width: 18, height: 18, borderRadius: '50%', background: '#fff', transition: 'left 0.2s', boxShadow: '0 1px 3px rgba(0,0,0,.2)' }} />
                                    </div>
                                    <input type="hidden" name="sms_enabled" value={smsEnabled ? '1' : '0'} />
                                    <span style={{ fontSize: 13, fontWeight: 600, color: 'var(--color-text-2)' }}>{smsEnabled ? 'Enabled' : 'Disabled'}</span>
                                </label>
                            </div>
                        </div>

                        <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 14, overflow: 'hidden' }}>
                            <div style={{ padding: '16px 20px 12px', borderBottom: '1px solid var(--color-border)' }}>
                                <div style={{ fontWeight: 700, fontSize: 14, color: 'var(--color-navy)' }}>Twilio Credentials</div>
                                <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>Used to send SMS messages. Get these from your Twilio console.</div>
                            </div>
                            <div style={{ padding: '20px' }}>
                                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16 }}>
                                    <Field label="Account SID" hint="Found on your Twilio Console dashboard">
                                        <input name="twilio_account_sid" type="text" placeholder="ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" autoComplete="off" value={form.twilio_account_sid ?? ''} onChange={e => setField('twilio_account_sid', e.target.value)} style={inputStyle} />
                                    </Field>
                                    <Field label="Auth Token" hint="Leave blank to keep the existing token">
                                        <input name="twilio_auth_token" type="password" placeholder="Leave blank to keep current" autoComplete="new-password" style={inputStyle} />
                                    </Field>
                                    <Field label="From Number" hint="Your Twilio phone number in E.164 format (e.g. +12345678901)." span2>
                                        <input name="twilio_from_number" type="text" placeholder="+12345678901" value={form.twilio_from_number ?? ''} onChange={e => setField('twilio_from_number', e.target.value)} style={inputStyle} />
                                    </Field>
                                </div>
                            </div>
                        </div>

                        <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 14, overflow: 'hidden' }}>
                            <div style={{ padding: '16px 20px 12px', borderBottom: '1px solid var(--color-border)' }}>
                                <div style={{ fontWeight: 700, fontSize: 14, color: 'var(--color-navy)' }}>Test SMS</div>
                                <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>Send a test message to verify your Twilio settings</div>
                            </div>
                            <div style={{ padding: '20px' }}>
                                <form onSubmit={handleSmsTest} style={{ display: 'flex', gap: 10, alignItems: 'flex-end' }}>
                                    <Field label="Send test to phone number" children={
                                        <input name="test_phone" type="text" placeholder="08012345678" style={inputStyle} />
                                    } />
                                    <button type="submit" style={{ padding: '9px 18px', borderRadius: 8, border: '1.5px solid var(--color-border)', background: 'var(--color-card)', color: 'var(--color-text-2)', fontSize: 13, fontWeight: 600, cursor: 'pointer', whiteSpace: 'nowrap', fontFamily: 'inherit' }}>Send test SMS</button>
                                </form>
                                <div style={{ marginTop: 14 }}>
                                    <Alert variant="navy"><span>ℹ</span><span>Save your Twilio credentials first, then use this to confirm messages are delivered.</span></Alert>
                                </div>
                            </div>
                        </div>
                    </div>
                )}

                {/* ── Payments ── */}
                {activeTab === 'payments' && (
                    <div style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
                        <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 14, overflow: 'hidden' }}>
                            <div style={{ padding: '16px 20px 12px', borderBottom: '1px solid var(--color-border)' }}>
                                <div style={{ fontWeight: 700, fontSize: 14, color: 'var(--color-navy)' }}>Active Payment Gateway</div>
                                <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>Select which gateway to use for subscription and payment collection</div>
                            </div>
                            <div style={{ padding: '20px' }}>
                                <input type="hidden" name="payment_gateway" value={gateway} />
                                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: 12 }}>
                                    {[
                                        { val: 'none',        label: 'None / Disabled', icon: '🚫', desc: 'Payments are not collected' },
                                        { val: 'paystack',    label: 'Paystack',         icon: '💚', desc: 'Leading Nigerian gateway' },
                                        { val: 'flutterwave', label: 'Flutterwave',      icon: '🦋', desc: 'Pan-African payments' },
                                    ].map(gw => (
                                        <div
                                            key={gw.val}
                                            onClick={() => setGateway(gw.val)}
                                            style={{
                                                display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center',
                                                padding: '18px 10px', borderRadius: 12, cursor: 'pointer', textAlign: 'center', gap: 4,
                                                border: gateway === gw.val ? '2px solid var(--color-navy)' : '2px solid var(--color-border)',
                                                background: gateway === gw.val ? 'var(--color-navy)' : 'var(--color-card)',
                                                color: gateway === gw.val ? '#fff' : 'var(--color-text)',
                                                transition: 'all 0.15s',
                                            }}
                                        >
                                            <span style={{ fontSize: 28, display: 'block', marginBottom: 6 }}>{gw.icon}</span>
                                            <span style={{ fontWeight: 700, fontSize: 14 }}>{gw.label}</span>
                                            <span style={{ fontSize: 12, opacity: 0.75 }}>{gw.desc}</span>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>

                        {gateway === 'paystack' && (
                            <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 14, overflow: 'hidden' }}>
                                <div style={{ padding: '16px 20px 12px', borderBottom: '1px solid var(--color-border)' }}>
                                    <div style={{ fontWeight: 700, fontSize: 14, color: 'var(--color-navy)' }}>💚 Paystack Keys</div>
                                    <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>Available from your Paystack Dashboard → Settings → API Keys</div>
                                </div>
                                <div style={{ padding: '20px' }}>
                                    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16 }}>
                                        <Field label="Public Key" hint="Safe to expose in frontend JavaScript">
                                            <input name="paystack_public_key" type="text" placeholder="pk_live_xxxxxxxxxxxxxxxxxxxxxxxx" autoComplete="off" value={form.paystack_public_key ?? ''} onChange={e => setField('paystack_public_key', e.target.value)} style={inputStyle} />
                                        </Field>
                                        <Field label="Secret Key" hint="Keep secret — used server-side only. Leave blank to keep existing.">
                                            <input name="paystack_secret_key" type="password" placeholder="Leave blank to keep current" autoComplete="new-password" style={inputStyle} />
                                        </Field>
                                    </div>
                                    <div style={{ marginTop: 8 }}>
                                        <Alert variant="amber"><span>⚠️</span><span>Never use test keys in production. Switch to live keys before going live.</span></Alert>
                                    </div>
                                </div>
                            </div>
                        )}

                        {gateway === 'flutterwave' && (
                            <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 14, overflow: 'hidden' }}>
                                <div style={{ padding: '16px 20px 12px', borderBottom: '1px solid var(--color-border)' }}>
                                    <div style={{ fontWeight: 700, fontSize: 14, color: 'var(--color-navy)' }}>🦋 Flutterwave Keys</div>
                                    <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>Available from your Flutterwave Dashboard → Settings → API Keys</div>
                                </div>
                                <div style={{ padding: '20px' }}>
                                    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16 }}>
                                        <Field label="Public Key">
                                            <input name="flutterwave_public_key" type="text" placeholder="FLWPUBK-xxxxxxxxxxxxxxxxxxxxxxxx-X" autoComplete="off" value={form.flutterwave_public_key ?? ''} onChange={e => setField('flutterwave_public_key', e.target.value)} style={inputStyle} />
                                        </Field>
                                        <Field label="Secret Key" hint="Leave blank to keep the existing key.">
                                            <input name="flutterwave_secret_key" type="password" placeholder="Leave blank to keep current" autoComplete="new-password" style={inputStyle} />
                                        </Field>
                                    </div>
                                    <div style={{ marginTop: 8 }}>
                                        <Alert variant="amber"><span>⚠️</span><span>Never use test keys in production. Switch to live keys before going live.</span></Alert>
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>
                )}

                {/* ── Landing ── */}
                {activeTab === 'landing' && (
                    <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 14, overflow: 'hidden' }}>
                        <div style={{ padding: '16px 20px 12px', borderBottom: '1px solid var(--color-border)' }}>
                            <div style={{ fontWeight: 700, fontSize: 14, color: 'var(--color-navy)' }}>Landing Page Content</div>
                            <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>Customise the public-facing homepage</div>
                        </div>
                        <div style={{ padding: '20px' }}>
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16 }}>
                                <Field label="Hero Headline" hint="Use <br> for a line break." span2>
                                    <input name="hero_headline" type="text" placeholder="Your health record, always with you" value={form.hero_headline ?? ''} onChange={e => setField('hero_headline', e.target.value)} style={inputStyle} />
                                </Field>
                                <Field label="Hero Subtext" span2>
                                    <textarea name="hero_subtext" placeholder="Brief description shown below the headline…" value={form.hero_subtext ?? ''} onChange={e => setField('hero_subtext', e.target.value)} style={textareaStyle} />
                                </Field>
                                <Field label="Primary CTA Button Text">
                                    <input name="hero_cta_primary" type="text" placeholder="Get started free" value={form.hero_cta_primary ?? ''} onChange={e => setField('hero_cta_primary', e.target.value)} style={inputStyle} />
                                </Field>
                                <Field label="Secondary CTA Button Text">
                                    <input name="hero_cta_secondary" type="text" placeholder="Learn more" value={form.hero_cta_secondary ?? ''} onChange={e => setField('hero_cta_secondary', e.target.value)} style={inputStyle} />
                                </Field>
                            </div>
                            <div style={{ marginTop: 16 }}>
                                <Alert variant="navy">
                                    <span>👀</span>
                                    <span>Preview your landing page at <a href="/" target="_blank" style={{ color: 'var(--color-navy)', fontWeight: 600 }}>/</a></span>
                                </Alert>
                            </div>
                        </div>
                    </div>
                )}

                {/* Save button */}
                <div style={{ display: 'flex', justifyContent: 'flex-end', marginTop: 20 }}>
                    <button type="submit" style={{ padding: '12px 28px', borderRadius: 10, border: 'none', background: 'var(--color-navy)', color: '#fff', fontSize: 14, fontWeight: 700, cursor: 'pointer', fontFamily: 'inherit' }}>
                        💾 Save all settings
                    </button>
                </div>
            </form>
        </AppLayout>
    );
}
