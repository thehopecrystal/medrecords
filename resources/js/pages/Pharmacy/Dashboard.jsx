import { Link } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

export default function Dashboard({ recentDispenses }) {
    const now = new Date();

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const weekStart = new Date(today);
    weekStart.setDate(today.getDate() - today.getDay() + 1);

    const dispensedToday = recentDispenses.filter(d => new Date(d.created_at) >= today).length;
    const dispensedWeek  = recentDispenses.filter(d => new Date(d.created_at) >= weekStart).length;

    function diffForHumans(dateStr) {
        const diff = Date.now() - new Date(dateStr).getTime();
        const mins = Math.floor(diff / 60000);
        if (mins < 1)  return 'just now';
        if (mins < 60) return `${mins} min ago`;
        const hrs = Math.floor(mins / 60);
        if (hrs < 24)  return `${hrs} hr ago`;
        return `${Math.floor(hrs / 24)} day(s) ago`;
    }

    const statCard = { background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12, padding: '20px 22px' };

    return (
        <AppLayout title="Pharmacy Workstation">
            {/* Header row */}
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 24 }}>
                <div>
                    <h1 style={{ fontSize: 22, fontWeight: 800, color: 'var(--color-navy)', margin: 0 }}>Pharmacy Workstation</h1>
                    <p style={{ color: 'var(--color-text-3)', fontSize: 13, marginTop: 4 }}>
                        {now.toLocaleDateString('en-NG', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' })}
                    </p>
                </div>
                <Link href="/pharmacy/lookup" style={{ background: 'var(--color-navy)', color: '#fff', padding: '9px 18px', borderRadius: 8, fontWeight: 600, fontSize: 13, textDecoration: 'none' }}>
                    Verify prescription
                </Link>
            </div>

            {/* Stats */}
            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(2, 1fr)', gap: 16, marginBottom: 24 }}>
                <div style={statCard}>
                    <div style={{ fontSize: 12, fontWeight: 600, color: 'var(--color-text-3)', textTransform: 'uppercase', letterSpacing: '0.05em', marginBottom: 6 }}>Dispensed today</div>
                    <div style={{ fontSize: 32, fontWeight: 800, color: 'var(--color-navy)' }}>{dispensedToday}</div>
                </div>
                <div style={{ ...statCard, borderLeft: '3px solid var(--color-green)' }}>
                    <div style={{ fontSize: 12, fontWeight: 600, color: 'var(--color-text-3)', textTransform: 'uppercase', letterSpacing: '0.05em', marginBottom: 6 }}>This week</div>
                    <div style={{ fontSize: 32, fontWeight: 800, color: 'var(--color-green)' }}>{dispensedWeek}</div>
                </div>
            </div>

            {/* Recent dispenses */}
            <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12, overflow: 'hidden' }}>
                <div style={{ padding: '14px 20px', borderBottom: '1px solid var(--color-border)' }}>
                    <div style={{ fontSize: 15, fontWeight: 700, color: 'var(--color-navy)' }}>Recent dispenses</div>
                </div>
                <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                    <thead>
                        <tr style={{ borderBottom: '1px solid var(--color-border)' }}>
                            {['Patient', 'Drug', 'Prescriber', 'Qty', 'Price', 'When'].map(h => (
                                <th key={h} style={{ padding: '10px 18px', textAlign: 'left', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.05em', color: 'var(--color-text-3)' }}>{h}</th>
                            ))}
                        </tr>
                    </thead>
                    <tbody>
                        {recentDispenses.length === 0 ? (
                            <tr>
                                <td colSpan={6} style={{ textAlign: 'center', color: 'var(--color-text-3)', padding: 24, fontSize: 13 }}>No dispenses yet.</td>
                            </tr>
                        ) : (
                            recentDispenses.map(d => (
                                <tr key={d.id} style={{ borderBottom: '1px solid var(--color-border)' }}>
                                    <td style={{ padding: '12px 18px', fontWeight: 600, fontSize: 13 }}>{d.prescription?.patient?.user?.name}</td>
                                    <td style={{ padding: '12px 18px', fontSize: 13 }}>{d.prescription?.drug_name} {d.prescription?.dose}</td>
                                    <td style={{ padding: '12px 18px', fontSize: 13 }}>{d.prescription?.prescribed_by?.name ?? d.prescription?.prescribedBy?.name}</td>
                                    <td style={{ padding: '12px 18px', fontSize: 13 }}>{d.quantity}</td>
                                    <td style={{ padding: '12px 18px', fontSize: 13 }}>{d.price ? 'NGN ' + Number(d.price).toLocaleString() : '—'}</td>
                                    <td style={{ padding: '12px 18px', fontSize: 12, color: 'var(--color-text-3)' }}>{diffForHumans(d.created_at)}</td>
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            </div>
        </AppLayout>
    );
}
