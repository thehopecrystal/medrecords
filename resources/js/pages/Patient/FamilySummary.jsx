import { Link } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import { fmtDate } from '@/utils/fmt';

function calcAge(dob) {
    if (!dob) return null;
    return Math.floor((Date.now() - new Date(dob).getTime()) / (365.25 * 86400000));
}

function MemberCard({ member }) {
    const { record, name, relation, dob } = member;
    const age = calcAge(dob);

    const activeConditions = (record?.conditions || []).filter(c => c.status === 'active' || c.status === 'chronic');
    const activePrescriptions = (record?.prescriptions || []).filter(p => p.status === 'active');
    const vaccinationsCount = record?.vaccinations?.length || 0;
    const lastEncounter = record?.encounters?.[0];
    const initial = (name || '').charAt(0).toUpperCase();

    return (
        <div style={{ background: 'var(--color-card)', border: '1px solid var(--color-border)', borderRadius: 12 }}>
            <div style={{ padding: 20 }}>
                {/* Header */}
                <div style={{ display: 'flex', alignItems: 'center', gap: 12, marginBottom: 16 }}>
                    <div style={{ width: 44, height: 44, borderRadius: '50%', background: 'linear-gradient(135deg,#1E3A5F,#2C5F2D)', color: '#fff', fontSize: 16, fontWeight: 700, display: 'flex', alignItems: 'center', justifyContent: 'center', flexShrink: 0 }}>
                        {initial}
                    </div>
                    <div>
                        <div style={{ fontSize: 15, fontWeight: 700, color: 'var(--color-navy)' }}>{name}</div>
                        <div style={{ fontSize: 12, color: 'var(--color-text-2)' }}>
                            {relation}{age != null ? ` · Age ${age}` : ''}
                        </div>
                    </div>
                </div>

                {/* Active conditions */}
                <div style={{ marginBottom: 12 }}>
                    <div style={{ fontSize: 11, fontWeight: 700, textTransform: 'uppercase', color: 'var(--color-text-2)', marginBottom: 6 }}>Active Conditions</div>
                    {activeConditions.length > 0
                        ? activeConditions.slice(0, 3).map(c => (
                            <span key={c.id} style={{ display: 'inline-flex', alignItems: 'center', fontSize: 11, fontWeight: 600, padding: '2px 8px', borderRadius: 20, background: 'var(--color-red-lt)', color: 'var(--color-red)', margin: 2 }}>{c.name}</span>
                        ))
                        : <span style={{ fontSize: 12, color: 'var(--color-text-3)' }}>None recorded</span>
                    }
                </div>

                {/* Active prescriptions */}
                <div style={{ marginBottom: 12 }}>
                    <div style={{ fontSize: 11, fontWeight: 700, textTransform: 'uppercase', color: 'var(--color-text-2)', marginBottom: 6 }}>Active Prescriptions</div>
                    {activePrescriptions.length > 0
                        ? activePrescriptions.slice(0, 3).map(rx => (
                            <span key={rx.id} style={{ display: 'inline-flex', alignItems: 'center', fontSize: 11, fontWeight: 600, padding: '2px 8px', borderRadius: 20, background: 'var(--color-amber-lt)', color: 'var(--color-amber)', margin: 2 }}>{rx.drug_name}</span>
                        ))
                        : <span style={{ fontSize: 12, color: 'var(--color-text-3)' }}>None</span>
                    }
                </div>

                {/* Vaccinations */}
                <div style={{ marginBottom: 12 }}>
                    <div style={{ fontSize: 11, fontWeight: 700, textTransform: 'uppercase', color: 'var(--color-text-2)', marginBottom: 6 }}>Vaccinations</div>
                    <span style={{ fontSize: 13, fontWeight: 600, color: 'var(--color-green)' }}>{vaccinationsCount} recorded</span>
                </div>

                {/* Last visit */}
                {lastEncounter && (
                    <div style={{ marginBottom: 12 }}>
                        <div style={{ fontSize: 11, fontWeight: 700, textTransform: 'uppercase', color: 'var(--color-text-2)', marginBottom: 4 }}>Last Visit</div>
                        <div style={{ fontSize: 12, color: 'var(--color-text-2)' }}>
                            {fmtDate(lastEncounter.created_at)} — {lastEncounter.type}
                        </div>
                    </div>
                )}

                <Link href={relation === 'You' ? '/patient/record' : '/patient/family'} style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', background: 'var(--color-card)', color: 'var(--color-text-2)', border: '1.5px solid var(--color-border)', borderRadius: 8, padding: '8px 16px', fontSize: 12, fontWeight: 600, textDecoration: 'none', marginTop: 4 }}>
                    {relation === 'You' ? 'View Full Records' : 'Manage Dependents'}
                </Link>
            </div>
        </div>
    );
}

export default function FamilySummary({ self, dependents }) {
    const members = [
        {
            record:   self,
            name:     self?.user?.name || '',
            relation: 'You',
            dob:      self?.dob,
        },
        ...(dependents || []).map(d => ({
            record:   d.dependent,
            name:     d.dependent?.user?.name || 'Unknown',
            relation: d.relationship ? d.relationship.charAt(0).toUpperCase() + d.relationship.slice(1) : 'Dependent',
            dob:      d.dependent?.dob,
        })),
    ];

    return (
        <AppLayout title="Family Health Summary">
            <div style={{ marginBottom: 24 }}>
                <h1 style={{ fontSize: 22, fontWeight: 800, color: 'var(--color-navy)', margin: 0 }}>Family Health Summary</h1>
                <p style={{ fontSize: 13, color: 'var(--color-text-2)', marginTop: 4 }}>A consolidated overview of health records for you and your dependents.</p>
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(320px, 1fr))', gap: 20 }}>
                {members.map((m, i) => (
                    <MemberCard key={i} member={m} />
                ))}
            </div>
        </AppLayout>
    );
}
