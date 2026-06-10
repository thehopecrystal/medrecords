import { Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

export default function Index({ plans }) {
    function deletePlan(plan) {
        if (!window.confirm(`Delete plan ${plan.name}?`)) return;
        router.delete(`/admin/plans/${plan.id}`, { preserveScroll: true });
    }

    return (
        <AppLayout title="Plans & Pricing">
            <div style={{ display: 'flex', alignItems: 'flex-start', justifyContent: 'space-between', marginBottom: 24, gap: 12 }}>
                <div>
                    <h1 style={{ fontSize: 22, fontWeight: 800, color: 'var(--color-navy)', margin: 0 }}>Plans &amp; Pricing</h1>
                    <p style={{ fontSize: 13, color: 'var(--color-text-2)', marginTop: 4 }}>Manage subscription plans shown on the public pricing page.</p>
                </div>
                <Link href="/admin/plans/create" style={{ padding: '9px 18px', borderRadius: 9, background: 'var(--color-navy)', color: '#fff', fontSize: 13, fontWeight: 700, textDecoration: 'none', whiteSpace: 'nowrap' }}>+ New Plan</Link>
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill,minmax(280px,1fr))', gap: 20 }}>
                {plans.length === 0 ? (
                    <div style={{ gridColumn: '1/-1', background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 14, padding: 40, textAlign: 'center', color: 'var(--color-text-2)' }}>
                        <div style={{ fontSize: 36, marginBottom: 10 }}>💳</div>
                        <div style={{ fontWeight: 700, fontSize: 15, marginBottom: 6 }}>No plans yet</div>
                        <p style={{ fontSize: 13 }}><Link href="/admin/plans/create" style={{ color: 'var(--color-navy)' }}>Create your first plan</Link> to show pricing on the landing page.</p>
                    </div>
                ) : plans.map(plan => (
                    <div key={plan.id} style={{ background: 'var(--color-card)', border: plan.is_featured ? '2px solid var(--color-navy)' : '1px solid var(--color-border)', borderRadius: 14, position: 'relative', overflow: 'hidden' }}>
                        {plan.badge && (
                            <div style={{ position: 'absolute', top: 14, right: -22, background: 'var(--color-navy)', color: '#fff', fontSize: 10, fontWeight: 700, padding: '4px 30px', transform: 'rotate(35deg)', letterSpacing: '0.04em', zIndex: 1 }}>
                                {plan.badge}
                            </div>
                        )}
                        <div style={{ padding: '20px 20px 16px' }}>
                            <div style={{ display: 'flex', alignItems: 'flex-start', justifyContent: 'space-between', marginBottom: 8 }}>
                                <div>
                                    <div style={{ fontSize: 16, fontWeight: 800, color: 'var(--color-navy)' }}>{plan.name}</div>
                                    {!plan.is_active && <span style={{ background: '#F1F5F9', color: '#64748B', fontSize: 10, fontWeight: 700, padding: '1px 7px', borderRadius: 10 }}>Inactive</span>}
                                </div>
                                <div style={{ textAlign: 'right' }}>
                                    <div style={{ fontSize: 22, fontWeight: 800, color: plan.price === 0 ? 'var(--color-green)' : 'var(--color-navy)' }}>{plan.formatted_price}</div>
                                    <div style={{ fontSize: 11, color: 'var(--color-text-3)' }}>Sort: {plan.sort_order}</div>
                                </div>
                            </div>
                            <p style={{ fontSize: 12, color: 'var(--color-text-2)', marginBottom: 14, minHeight: 36 }}>{plan.description}</p>
                            <ul style={{ listStyle: 'none', margin: '0 0 16px', padding: 0, fontSize: 12, color: 'var(--color-text-2)' }}>
                                {(plan.features ?? []).slice(0, 5).map((f, i) => (
                                    <li key={i} style={{ padding: '3px 0', display: 'flex', gap: 6 }}>
                                        <span style={{ color: 'var(--color-green)' }}>✓</span> {f}
                                    </li>
                                ))}
                                {(plan.features ?? []).length > 5 && (
                                    <li style={{ color: 'var(--color-text-3)', padding: '3px 0' }}>+ {plan.features.length - 5} more…</li>
                                )}
                            </ul>
                            <div style={{ display: 'flex', gap: 8 }}>
                                <Link href={`/admin/plans/${plan.id}/edit`} style={{ flex: 1, padding: '8px', borderRadius: 8, border: '1.5px solid var(--color-border)', background: 'var(--color-card)', color: 'var(--color-text-2)', fontSize: 12, fontWeight: 600, textDecoration: 'none', textAlign: 'center' }}>Edit</Link>
                                <button onClick={() => deletePlan(plan)} style={{ flex: 1, padding: '8px', borderRadius: 8, border: 'none', background: 'var(--color-red-lt)', color: 'var(--color-red)', fontSize: 12, fontWeight: 600, cursor: 'pointer', fontFamily: 'inherit' }}>Delete</button>
                            </div>
                        </div>
                    </div>
                ))}
            </div>
        </AppLayout>
    );
}
