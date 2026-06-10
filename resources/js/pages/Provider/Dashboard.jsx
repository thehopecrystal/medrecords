import { Link, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

export default function Dashboard({ recentEncounters }) {
    const { auth } = usePage().props;
    const user = auth?.user;

    const nameParts = (user?.name ?? '').trim().split(' ');
    const lastName = nameParts[nameParts.length - 1];

    const now = new Date();
    const hour = now.getHours();
    const greeting = hour < 12 ? 'morning' : 'afternoon';

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const weekStart = new Date(today);
    weekStart.setDate(today.getDate() - today.getDay() + 1);

    const encountersToday = recentEncounters.filter(e => new Date(e.created_at) >= today).length;
    const encountersWeek  = recentEncounters.filter(e => new Date(e.created_at) >= weekStart).length;
    const encountersTotal = recentEncounters.length;

    const statCard = { background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12, padding: '20px 22px' };

    return (
        <AppLayout title="Provider Workstation">
            {/* Header row */}
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 24 }}>
                <div>
                    <h1 style={{ fontSize: 22, fontWeight: 800, color: 'var(--color-navy)', margin: 0 }}>
                        Good {greeting}, Dr {lastName}
                    </h1>
                    <p style={{ color: 'var(--color-text-3)', fontSize: 13, marginTop: 4 }}>
                        {now.toLocaleDateString('en-NG', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' })}
                    </p>
                </div>
                <Link href="/provider/search" style={{ background: 'var(--color-navy)', color: '#fff', padding: '9px 18px', borderRadius: 8, fontWeight: 600, fontSize: 13, textDecoration: 'none' }}>
                    + New encounter
                </Link>
            </div>

            {/* Stats */}
            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 16, marginBottom: 24 }}>
                <div style={statCard}>
                    <div style={{ fontSize: 12, fontWeight: 600, color: 'var(--color-text-3)', textTransform: 'uppercase', letterSpacing: '0.05em', marginBottom: 6 }}>Encounters today</div>
                    <div style={{ fontSize: 32, fontWeight: 800, color: 'var(--color-navy)' }}>{encountersToday}</div>
                </div>
                <div style={{ ...statCard, borderLeft: '3px solid var(--color-green)' }}>
                    <div style={{ fontSize: 12, fontWeight: 600, color: 'var(--color-text-3)', textTransform: 'uppercase', letterSpacing: '0.05em', marginBottom: 6 }}>This week</div>
                    <div style={{ fontSize: 32, fontWeight: 800, color: 'var(--color-green)' }}>{encountersWeek}</div>
                </div>
                <div style={{ ...statCard, borderLeft: '3px solid var(--color-navy)' }}>
                    <div style={{ fontSize: 12, fontWeight: 600, color: 'var(--color-text-3)', textTransform: 'uppercase', letterSpacing: '0.05em', marginBottom: 6 }}>Total encounters</div>
                    <div style={{ fontSize: 32, fontWeight: 800, color: 'var(--color-navy)' }}>{encountersTotal}</div>
                </div>
            </div>

            {/* Recent encounters */}
            <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12 }}>
                <div style={{ padding: '18px 22px', borderBottom: '1px solid var(--color-border)' }}>
                    <div style={{ fontSize: 15, fontWeight: 700, color: 'var(--color-navy)' }}>Recent encounters</div>
                    <div style={{ fontSize: 12, color: 'var(--color-text-3)', marginTop: 2 }}>Your most recent patient interactions</div>
                </div>
                {recentEncounters.length === 0 ? (
                    <div style={{ padding: 24, textAlign: 'center', color: 'var(--color-text-3)', fontSize: 13 }}>
                        No encounters yet.{' '}
                        <Link href="/provider/search" style={{ color: 'var(--color-navy)' }}>Search for a patient</Link> to begin.
                    </div>
                ) : (
                    recentEncounters.map(enc => (
                        <div key={enc.id} style={{ padding: '14px 22px', borderBottom: '1px solid var(--color-border)', display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start' }}>
                            <div>
                                <div style={{ fontSize: 14, fontWeight: 600, color: 'var(--color-text)' }}>
                                    <Link href={`/provider/patients/${enc.patient_id}`} style={{ color: 'inherit', textDecoration: 'none' }}>
                                        {enc.patient?.user?.name}
                                    </Link>
                                    {enc.chief_complaint ? ` — ${enc.chief_complaint}` : ''}
                                </div>
                                <div style={{ fontSize: 12, color: 'var(--color-text-3)', marginTop: 2 }}>
                                    {enc.facility?.name} · {enc.type ? enc.type.charAt(0).toUpperCase() + enc.type.slice(1) : ''}
                                </div>
                            </div>
                            <div style={{ textAlign: 'right', flexShrink: 0 }}>
                                <div style={{ fontSize: 12, color: 'var(--color-text-3)', marginBottom: 4 }}>
                                    {new Date(enc.created_at).toLocaleDateString('en-NG', { day: '2-digit', month: 'short', year: 'numeric' })}
                                </div>
                                <span style={{
                                    display: 'inline-block', padding: '2px 10px', borderRadius: 20, fontSize: 11, fontWeight: 600,
                                    background: enc.status === 'signed' ? 'var(--color-green-lt)' : 'var(--color-amber-lt)',
                                    color: enc.status === 'signed' ? 'var(--color-green)' : 'var(--color-amber)',
                                }}>
                                    {enc.status}
                                </span>
                            </div>
                        </div>
                    ))
                )}
            </div>
        </AppLayout>
    );
}
