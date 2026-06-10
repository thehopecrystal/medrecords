import { Link } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';

export default function Family({ dependents }) {
    return (
        <AppLayout title="My Family">
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 24 }}>
                <div>
                    <h1 style={{ fontSize: 22, fontWeight: 800, color: 'var(--color-navy)', margin: 0 }}>My Family</h1>
                    <p style={{ fontSize: 13, color: 'var(--color-text-2)', marginTop: 4 }}>Dependents under your guardianship</p>
                </div>
                <Link href="/patient/family/add" style={{ background: 'var(--color-navy)', color: '#fff', borderRadius: 8, padding: '9px 18px', fontSize: 13, fontWeight: 600, textDecoration: 'none' }}>
                    + Add dependent
                </Link>
            </div>

            {dependents.length === 0 ? (
                <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12, textAlign: 'center', padding: 40 }}>
                    <div style={{ fontSize: 32, marginBottom: 12 }}>👨‍👩‍👧‍👦</div>
                    <div style={{ fontSize: 15, fontWeight: 600, color: 'var(--color-navy)', marginBottom: 6 }}>No dependents added yet</div>
                    <div style={{ fontSize: 13, color: 'var(--color-text-2)', marginBottom: 16 }}>Add children, elderly parents, or anyone under your care.</div>
                    <Link href="/patient/family/add" style={{ display: 'inline-block', background: 'var(--color-navy)', color: '#fff', borderRadius: 8, padding: '9px 20px', fontSize: 13, fontWeight: 600, textDecoration: 'none' }}>
                        + Add first dependent
                    </Link>
                </div>
            ) : (
                dependents.map(dep => {
                    const d = dep.dependent;
                    const name = d?.user?.name || 'Unknown';
                    const age = d?.age;
                    const gender = d?.gender;
                    const vaxDue = d?.vaccinations?.filter(v => v.next_due && (new Date(v.next_due) < new Date() || (new Date(v.next_due) - new Date()) < 30 * 86400000)).length || 0;
                    const activeRx = d?.active_prescriptions?.length || 0;

                    return (
                        <div key={dep.id} style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12, padding: 20, marginBottom: 14, display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start' }}>
                            <div>
                                <div style={{ fontSize: 16, fontWeight: 700, color: 'var(--color-navy)' }}>
                                    {name}
                                    {age && <span style={{ fontWeight: 400, fontSize: 13, color: 'var(--color-text-2)' }}>, {age} yrs</span>}
                                </div>
                                <div style={{ fontSize: 12, color: 'var(--color-text-2)', marginTop: 2 }}>
                                    {dep.relationship ? dep.relationship.charAt(0).toUpperCase() + dep.relationship.slice(1) : 'Dependent'} · MedID: {d?.med_id}
                                </div>
                                <div style={{ marginTop: 8, display: 'flex', gap: 6, flexWrap: 'wrap' }}>
                                    {vaxDue > 0
                                        ? <span style={{ display: 'inline-flex', alignItems: 'center', fontSize: 11, fontWeight: 600, padding: '2px 8px', borderRadius: 20, background: 'var(--color-amber-lt)', color: 'var(--color-amber)' }}>{vaxDue} vaccine{vaxDue > 1 ? 's' : ''} due soon</span>
                                        : <span style={{ display: 'inline-flex', alignItems: 'center', fontSize: 11, fontWeight: 600, padding: '2px 8px', borderRadius: 20, background: 'var(--color-green-lt)', color: 'var(--color-green)' }}>Vaccines up to date</span>
                                    }
                                    {activeRx > 0 && <span style={{ display: 'inline-flex', alignItems: 'center', fontSize: 11, fontWeight: 600, padding: '2px 8px', borderRadius: 20, background: 'var(--color-navy-lt)', color: 'var(--color-navy)' }}>{activeRx} active Rx</span>}
                                </div>
                            </div>
                            <div style={{ fontSize: 22 }}>{gender === 'female' ? '👧' : '👦'}</div>
                        </div>
                    );
                })
            )}
        </AppLayout>
    );
}
